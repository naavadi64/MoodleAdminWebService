<?php
/*
This script contains all Moodle API related functions.
The flow of data should be authoritave to Moodle,
meaning data should be updated to Moodle first, then the Web Service's
Database.
General flow of CRUD-ing data connected to Moodle is:
User requests on WS -> call and update Moodle API -> update WS database

List of tables dependant on Moodle:
- Users (userid, username, userfname, userlname, email, last access)
- Courses (courseid)
- Course Enrolments
- Assignments
- Event Log (No columns dependant, entries to keep track of database events)

List of tables not dependant on Moodle (Moodle API calls not needed)
- Role
- Unit

uses APIs from these external plugins: 
- Webservice manage sections (https://moodle.org/plugins/local_wsmanagesections)

Required Moodle API Functions:
- core_user_create_users
- core_user_get_users
- core_user_delete_users
- core_course_create_courses
- core_course_get_courses
- core_course_delete_courses
- core_completion_get_activities_completion_status
- enrol_manual_enrol_users
- enrol_manual_unenrol_users
The following is from an external plugin:
- local_wsmanagesections_create_sections
- local_wsmanagesections_delete_sections
- local_wsmanagesections_update_sections
- local_wsmanagesections_move_sections
Make sure these functions are enabled on Moodle for the token
authentication. Admin Users can check this on Site administration >
Server > Web services > External service > Functions.

TODOs:
-   Enforce data with Moodle (no dangling or leftover entries can occur- after 
    deletion API calls)
-   Continually update docs and comments
*/

require_once "MoodleRest.php";
require_once "config.php";

function init_moodlerest($debug = false) {
    /*  This function is used to initialize the MoodleRest object which
        is used for Moodle API Requests.
    */
    $moodle_ip = MOODLE_IP;
    $moodle_folder = MOODLE_FOLDER;
    $moodle_token = TOKEN;
    $MoodleRest = new MoodleRest();
    $MoodleRest->setServerAddress("http://$moodle_ip/$moodle_folder/webservice/rest/server.php");
    $MoodleRest->setToken($moodle_token);
    $MoodleRest->setReturnFormat(MoodleRest::RETURN_ARRAY);
    $MoodleRest->setDebug($debug);

    return $MoodleRest;
}

function add_moodle_user($username, $password, $userfname, $userlname, $email, $debug = false) {
    $MoodleRest = init_moodlerest($debug);

    $param_array = array("users" => array(0 => array("username" => "$username", "auth" => "manual", "password" => "$password", "firstname" => "$userfname", "lastname" => "$userlname", "email" => "$email")));
    $request = $MoodleRest->request('core_user_create_users', $param_array);

    return $request;
}

function remove_moodle_user($user_id, $debug = false) {
    $MoodleRest = init_moodlerest($debug);

    $param_array = array("userids" => array(0 => $user_id));
    $request = $MoodleRest->request('core_user_delete_users', $param_array);

    return $request;
}

function update_moodle_users($debug = false) {
    $MoodleRest = init_moodlerest($debug);

    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

    $param_array = array("criteria" => array(0 => array("key" => "email", "value" => "%%"))); // get all users with email wildcard
    $user_request = $MoodleRest->request('core_user_get_users', $param_array);
    $user_count = 1;
    foreach ($user_request["users"] as $user) {
        // Set data from response to variables
        $user_id = $user['id'];
        $user_f_name = $user["firstname"];
        $user_l_name = $user["lastname"];
        $email = $user["email"];
        $username = $user["username"];
        $last_access = gmdate("Y-m-d g:i", $user["lastaccess"]); //  Note: Moodle's API returns Unix Epoch time/timestamp, remember to format to a readable format. Database is also expecting DateTime. gmdate should return time in GMT +0, handle epoch as never accessed.
            
        $query = "SELECT * FROM t_user WHERE userid = $user_id";  
        if ($result = $mysqli -> query($query)) {

            if ($result -> num_rows > 0) {
                echo "User data already in table, updating...<br>";
                $result -> free_result();
                $query = "UPDATE t_user SET userfname = '$user_f_name', userlname = '$user_l_name', email = '$email', username = '$username', lastaccess = '$last_access' WHERE userid = $user_id;";
                if ($result = $mysqli -> query($query)) {
                    echo "Query result: " . $result;
                }    
            } else {
                echo "User data not yet in table, inserting...<br>";
                $result -> free_result();
                $query = "INSERT INTO t_user (userid, userfname, userlname, email, username, lastaccess) VALUES ('$user_id', '$user_f_name', '$user_l_name', '$email', '$username', '$last_access')";   
                if ($result = $mysqli -> query($query)) {
                    echo "Query result: " . $result;
                }
            }
        
        }  
            
        $user_count++;
        echo "<br>";
    }

    /*$query = "INSERT INTO t_metadata (database_update_contents) VALUES ('update t_user');";
    if ($result = $mysqli -> query($query)) {
        echo "Metadata updated, result: " . $result;
        //$result -> free_result();
    }*/
        
}

function get_moodle_user_course_activity_status($user_id, $course_id, $debug = false) {
    $MoodleRest = init_moodlerest($debug);

    $param_array = array("courseid" => $course_id, "userid" => $user_id);
    // core_completion_get_course_completion_status
    //$request = $MoodleRest->request('core_completion_get_course_completion_status', $param_array);
    // core_completion_get_activities_completion_status
    $request = $MoodleRest->request('core_completion_get_activities_completion_status', $param_array);


    return $request;
}

function create_moodle_course($fullname, $shortname, $category_id, $debug = false) {
    $MoodleRest = init_moodlerest($debug);

    $param_array = array("courses" => array(0 => array("fullname" => $fullname, "shortname" => $shortname, "categoryid" => $category_id)));
    $request = $MoodleRest->request('core_course_create_courses', $param_array);

    return $request;
}

function update_moodle_courses($debug = false) {
    /*  Request an array of courses from Moodle via API and then
        updates the Web Service's database.
        TODO: Handle enties that are no longer on Moodle but remain
        on WS's database.
        Handle data concurrency on category id (moodle database and t_categories)
    */
    $MoodleRest = init_moodlerest($debug);

    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

    $param_array = array();
    $request = $MoodleRest->request('core_course_get_courses', $param_array);

    foreach ($request as $course) {
        $course_id = $course["id"];
        $course_name = $course["fullname"];
        $course_desc = $course["summary"];
        $category_id = $course["categoryid"];
        
        // Update t_course
        $query = "SELECT * FROM t_course WHERE courseid = $course_id";
        if ($result = $mysqli -> query($query)) {

            if ($result -> num_rows > 0) {
                echo "Course data already in table, updating...<br>";
                $result -> free_result();
                $query = "UPDATE t_course SET coursename = '$course_name', coursedesc = '$course_desc', categoryid = '$category_id' WHERE courseid = $course_id;";

                if ($result = $mysqli -> query($query)) {
                    echo "Query result: " . $result;
                }              
                   
            } else {
                echo "Course data not yet in table, inserting...<br>";
                $result -> free_result();
                $query = "INSERT INTO t_course (courseid, coursename, coursedesc, categoryid) VALUES ('$course_id', '$course_name', '$course_desc', '$category_id')";
                //echo $query;
                    
                if ($result = $mysqli -> query($query)) {
                    echo "Query result: " . $result;
                    //$result -> free_result();
                }
            }
        
        }

    }

}

function get_moodle_categories($debug = false) {
    $MoodleRest = init_moodlerest($debug);

    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

    $param_array = array();
    $request = $MoodleRest->request('core_course_get_categories', $param_array);

    foreach ($request as $category) {
        $category_id = $category['id'];
        $category_name = $category['name'];
        $category_desc = $category['description'];

        $query = "SELECT * FROM t_categories WHERE categoryid = $category_id";
        if ($result = $mysqli -> query($query)) {
            
            if ($result -> num_rows > 0) {
                echo "Category found, updating";
                $result -> free_result();
                $query = "UPDATE t_categories SET categoryname = '$category_name', categorydesc = '$category_desc', WHERE categoryid = $category_id;";
                if ($result = $mysqli -> query($query)) {
                    echo "Query result: " . $result;
                }    
            } else {
                echo "Inserting new category";
                $result -> free_result();
                $query = "INSERT INTO t_categories (categoryid, categoryname, categorydesc) VALUES ('$category_id', '$category_name', '$category_desc')";   
                if ($result = $mysqli -> query($query)) {
                    echo "Query result: " . $result;
                }
            }
        
        }
    }

    return $request;
}

function show_moodle_course_sections($course_id, $exclude_modules = false, $exclude_module_contents = true, $debug = false) {
    $MoodleRest = init_moodlerest($debug);

    $param_array = array("courseid" => $course_id, "options" => array(0 => array ("name" => "excludemodules", "value" => "$exclude_modules"), 1 => array ("name" => "excludecontents", "value" => "$exclude_module_contents")));
    //"0" => array("excludemodules" => true)
    $request = $MoodleRest->request('core_course_get_contents', $param_array);

    return $request;
}

function add_moodle_course_section($course_id, $num_sections = 1, $debug = false) {
    /* Adds blank section to a given $course_id.
        To add information to the section, use edit_moodle_course_section while passing this function's returned section_id.
    */
    $MoodleRest = init_moodlerest($debug);

    $param_array = array("courseid" => $course_id, "position" => 0, "number" => $num_sections);
    $request = $MoodleRest->request('local_wsmanagesections_create_sections', $param_array);

    return $request;
}

function set_moodle_course_section_visibility($course_id, $section_id, $visibility, $debug = false) {
    /* Sets $visibility only from given $section_id in $course_id
        Simpified version of similar edit_moodle_course_section() function
        "1" = visible, "0" = not visible
    */
    $MoodleRest = init_moodlerest($debug);

    $param_array = array("courseid" => $course_id, "sections" => array(0 => array("type" => "id", "section" => $section_id, "visible" => $visibility)));
    $request = $MoodleRest->request('local_wsmanagesections_update_sections', $param_array);

    return $request;
}

function edit_moodle_course_section($course_id, $section_id, $section_name = "", $section_summary = "", $summary_format = "1", $visibility, $highlight, $debug = false) {
    /* Edits various section fields from given $section_id in $course_id
        $visibility and $highlight are alwas required to be given in function call, defaults are 1 and 0 respectively.
        TODO: currently only handles single sections, update to handle lists of sections and their details.

    */
    $MoodleRest = init_moodlerest($debug);

    $section_name == "" ? null : $section_name;
    $section_summary == "" ? null : $section_summary;

    $param_array = array("courseid" => $course_id, "sections" => array(0 => array("type" => "id", "section" => $section_id, "name" => $section_name, "summary" => $section_summary, "summaryformat" => $summary_format, "visible" => $visibility, "highlight" => $highlight)));
    $request = $MoodleRest->request('local_wsmanagesections_update_sections', $param_array);

    return $request;
}

function move_moodle_course_section($course_id, $section_number, $target_position, $debug = false) {
    /* TODO test and implement
    */
    $MoodleRest = init_moodlerest($debug);
    $param_array = array("courseid" => $course_id, "sectionnumber" => $section_number, $position => $target_position);
    $request = $MoodleRest->request('local_wsmanagesections_update_sections', $param_array);
}

function remove_moodle_course_section($course_id, $section_id, $debug = false) {
    /* removes given $section_id in given $course_id
        TODO: currently only handles single ids, expanmd to hgandle a list of int.
    */
    $MoodleRest = init_moodlerest($debug);

    $param_array = array("courseid" => $course_id, "sectionids" => array($section_id));
    $request = $MoodleRest->request('local_wsmanagesections_delete_sections', $param_array);

    return $request;
}

function remove_moodle_course($course_id, $debug = false) {
    /*  Removes a Moodle Course corrosponding to the given $course_id
        NOTE: does not update WS's database
    */
    $MoodleRest = init_moodlerest($debug);

    $param_array = array("courseids" => array(0 => $course_id));
    $request = $MoodleRest->request('core_course_delete_courses', $param_array);
}

function enrol_moodle_user($user_id, $course_id, $debug = false) {
    $MoodleRest = init_moodlerest($debug);
    
    $param_array = array("enrolments" => array(0 => array("roleid" => "5", "userid" => "$user_id", "courseid" => "$course_id")));
    $request = $MoodleRest->request('enrol_manual_enrol_users', $param_array);
    
    return $request;
}

function update_users_enrol($debug = false) {
    $MoodleRest = init_moodlerest($debug);
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

    $param_array = array("criteria" => array(0 => array("key" => "email", "value" => "%%")));
    $user_request = $MoodleRest->request('core_user_get_users', $param_array);
    $user_array = array();

    foreach ($user_request["users"] as $user) {
        $userid = $user['id'];
        $param_array = array("userid" => "$userid", "returnusercount" => "0");
        $enrol_request = $MoodleRest->request('core_enrol_get_users_courses', $param_array);

        echo "user = " . $user["username"] . ":<br>";

        foreach ($enrol_request as $course) {
            $courseid = $course["id"];
            $course_progress = is_null($course["progress"]) ? 0 : $course["progress"]; // this value can be null from moodle api, so handle that
            $course_last_access = gmdate("Y-m-d g:i", $course["lastaccess"]);
            $course_name = $course["fullname"];
            $query = "SELECT * FROM t_course_enrollment WHERE userid = $userid AND courseid = $courseid";

            if ($result = $mysqli -> query($query)) {

                if ($result -> num_rows > 0) {  // entry exists

                    $query = "UPDATE t_course_enrollment SET progress = '$course_progress', lastaccess = '$course_last_access' WHERE userid = $userid AND courseid = $courseid";
                    echo $query . "<br><br>";
                    if ($result = $mysqli -> query($query)) {
                        echo "- Query result: " . $result . " Updated.<br>";
                    }
                } else {
                    $query = "INSERT INTO t_course_enrollment (userid, courseid, lastaccess, progress) VALUES ('$userid', '$courseid','$course_last_access', $course_progress)";
                    echo $query;
                    if ($result = $mysqli -> query($query)) {
                        echo "Query result: " . $result . " Added.<br>";
                    }
                }

            }

        }

        echo "<br>";
            
    }

    /*$query = "INSERT INTO t_metadata (database_update_contents) VALUES ('update t_courses');";
    if ($result = $mysqli -> query($query)) {
        echo "Metadata updated, result: " . $result;
         //$result -> free_result();
        }*/

}

?>