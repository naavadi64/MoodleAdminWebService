<?php
/*
This script handles all Web Service Database and Moodle API Requests.
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

Required Moodle API Functions:
- core_user_create_users
- core_user_get_users
- core_user_delete_users
- core_course_create_courses
- core_course_get_courses
- core_course_delete_courses
- enrol_manual_enrol_users
- enrol_manual_unenrol_users
Make sure these functions are enabled on Moodle for the token
authentication. Admin Users can check this on Site administration >
Server > Web services > External service > Functions.

TODOs:
-   Enforce data with Moodle (no dangling or leftover entries after 
    deletion API calls)
-   Continually update docs and comments
*/

require_once "MoodleRest.php";
require_once "config.php";

// Database function calls

function get_single_result($table_name, $target_column, $filter_column, $where_clause) {
    // function to get a single column, row result from a database entry
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $query = "SELECT $target_column FROM $table_name WHERE $filter_column = $where_clause";
    if ($result = $mysqli -> query($query)) {
        if ($result -> num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                return $row[$target_column];
            }
        }
    }
}

function update_unit($unit_id, $unit_name, $unit_desc){
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $query = "SELECT * FROM t_unit WHERE unitid = $unit_id";
    if ($result = $mysqli -> query($query)) {

        if ($result -> num_rows > 0) {
            $query = "UPDATE t_unit SET unitname = '$unit_name', unitdesc = '$unit_desc' WHERE unitid = '$unit_id'";
            $result = $mysqli -> query($query);
            echo $result;
        } else {
            $query = "INSERT INTO t_unit (unitid, unitname, unitdesc) VALUES ('$unit_id', '$unit_name', '$unit_desc')";
            $result = $mysqli -> query($query);
            echo $result;
        }

    }
}

function remove_unit($unit_id) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $query = "SELECT * FROM t_unit WHERE unitid = $unit_id";
        if ($result = $mysqli -> query($query)) {

            if ($result -> num_rows > 0) {
                $query = "DELETE FROM t_unit WHERE unitid = '$unit_id'";
                $result = $mysqli -> query($query);
                echo $result;
            } else {
                echo "Error: Unit ID does not exist. <br><br>";
            }

      }
}

function update_role($role_id, $role_name, $role_desc) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $query = "SELECT * FROM t_role WHERE roleid = $role_id";
    if ($result = $mysqli -> query($query)) {

        if ($result -> num_rows > 0) {
            echo "Updating existing Role ID<br><br>";
            $query = "UPDATE t_role SET rolename = '$role_name', roledesc = '$role_desc' WHERE roleid = '$role_id'";
            $result = $mysqli -> query($query);
            echo $result;
        } else {
            echo "Inserting new Role ID<br><br>";
            $query = "INSERT INTO t_role (roleid, rolename, roledesc) VALUES ('$role_id', '$role_name', '$role_desc')";
            $result = $mysqli -> query($query);
            echo $result;
          }
      }
}

function remove_role($role_id) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $query = "SELECT * FROM t_role WHERE roleid = $role_id";
    if ($result = $mysqli -> query($query)) {

        if ($result -> num_rows > 0) {
            $query = "DELETE FROM t_role WHERE roleid = '$role_id'";
            $result = $mysqli -> query($query);
            echo $result;
        } else {
            echo "Error: Role ID does not exist. <br><br>";
        }
    }
}

function update_user($user_id, $username, $password, $userfname, $userlname, $email, $wsadmin) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $query = "SELECT * FROM t_user WHERE userid = $user_id";
    if ($result = $mysqli -> query($query)) {

        if ($result -> num_rows > 0) {
            echo "Updating existing User<br>";
            $query = "UPDATE t_user SET username = '$username', userpass = '$password', userfname = '$userfname', userlname = '$userlname', email = '$email', wsadmin = '$wsadmin' WHERE userid = '$user_id'";
            $result = $mysqli -> query($query);
            echo $result;
        } else {
            echo "Inserting new User<br>";
            $query = "INSERT INTO t_user (userid, username, userpass, userfname, userlname, email, wsadmin) VALUES ('$user_id', '$username', '$password', '$userfname', '$userlname', '$email', $wsadmin)";
            echo $query;
            $result = $mysqli -> query($query);
            echo $result;
        }
    }
}

function remove_user($user_id) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $query = "SELECT * FROM t_user WHERE userid = $user_id";
    if ($result = $mysqli -> query($query)) {

        if ($result -> num_rows > 0) {
            echo "Deleting entry with User ID: $user_id<br><br>";
            $query = "DELETE FROM t_user WHERE userid = '$user_id'";
            $result = $mysqli -> query($query);
            echo $result;
        } else {
            echo "Error: User ID does not exist. <br><br>";
        }
    }
}

function update_course($course_id, $course_name, $course_desc) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $query = "SELECT * FROM t_course WHERE courseid = $course_id";
    if ($result = $mysqli -> query($query)) {

        if ($result -> num_rows > 0) {
            $query = "UPDATE t_course SET coursename = '$course_name', coursedesc = '$course_desc' WHERE courseid = '$course_id'";
            $result = $mysqli -> query($query);
            echo $result;
        } else {
            $query = "INSERT INTO t_course (courseid, coursename, coursedesc) VALUES ('$course_id', '$course_name', '$course_desc')";
            $result = $mysqli -> query($query);
            echo $result;
        }
    }
}

function remove_course($course_id) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $query = "SELECT * FROM t_course WHERE courseid = $course_id";
    if ($result = $mysqli -> query($query)) {

        if ($result -> num_rows > 0) {
            $query = "DELETE FROM t_course WHERE courseid = '$course_id'";
            $result = $mysqli -> query($query);
            echo $result;
        } else {
            echo "Error: Course ID does not exist. <br><br>";
        }
    }
}

function update_assignment($assign_id, $user_id, $role_id, $unit_id, $start, $end, $contract, $status) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $query = "SELECT * FROM t_assign WHERE assignid = $assign_id";
    if ($result = $mysqli -> query($query)) {

        if ($result -> num_rows > 0) {
            $query = "UPDATE t_assign SET userid = '$user_id', roleid = $role_id, unitid = $unit_id, start = '$start', end = '$end', contract = '$contract', status = '$status' WHERE assignid = '$assign_id'";
            $result = $mysqli -> query($query);
            echo $result;
        } else {
            $query = "INSERT INTO t_assign (assignid, userid, roleid, unitid, start, end, contract, status) VALUES ('$assign_id', '$user_id', '$role_id', '$unit_id', '$start', '$end', '$contract', '$status')";
            $result = $mysqli -> query($query);
            echo $result;
        }

    }
}

function remove_assignment($assign_id) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $query = "SELECT * FROM t_assign WHERE assignid = $assign_id";
    if ($result = $mysqli -> query($query)) {

        if ($result -> num_rows > 0) {
            $query = "DELETE FROM t_assign WHERE assignid = '$assign_id'";
            $result = $mysqli -> query($query);
            echo $result;
        } else { // no entry
            echo "Error: Assign ID does not exist. <br><br>";
        }
    }
}

function enrol_user($user_id, $course_id) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $query = "SELECT * FROM t_course_enrollment WHERE userid = '$user_id' AND courseid = '$course_id'";
    if ($result = $mysqli -> query($query)) {

        if ($result -> num_rows > 0) {
            $query = "UPDATE t_course_enrollment SET courseid = '$course_id', WHERE userid = '$user_id'";
            $result = $mysqli -> query($query);
            echo $result;
        } else {
            $query = "INSERT INTO t_course_enrollment (userid, courseid) VALUES ('$user_id', '$course_id')";
            //echo $query;
            $result = $mysqli -> query($query);
            echo $result;
        }
    }
}

function unenrol_user($user_id, $course_id) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $query = "SELECT * FROM t_course_enrollment WHERE userid = '$user_id' AND courseid = '$course_id'";
    if ($result = $mysqli -> query($query)) {

        if ($result -> num_rows > 0) {
            echo "Deleting entry with User ID/Course ID: $user_id/$course_id<br><br>";
            $query = "DELETE FROM t_course_enrollment WHERE userid = '$user_id' AND courseid = '$course_id'";
            $result = $mysqli -> query($query);
            echo $result;
        } else { // no entry
            echo "Error: User ID and Course ID combo does not exist. <br><br>";
        }
    }
}

// Moodle API related calls

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

function add_moodle_user($username, $password, $userfname, $userlname, $email) {
    $MoodleRest = init_moodlerest(true);

    $param_array = array("users" => array(0 => array("username" => "$username", "auth" => "manual", "password" => "$password", "firstname" => "$userfname", "lastname" => "$userlname", "email" => "$email")));
    $request = $MoodleRest->request('core_user_create_users', $param_array);

    return $request;
}

function remove_moodle_user($user_id) {
    $MoodleRest = init_moodlerest(true);

    $param_array = array("userids" => array(0 => $user_id));
    $request = $MoodleRest->request('core_user_delete_users', $param_array);

    return $request;
}

function get_moodle_categories() {
    $MoodleRest = init_moodlerest(true);

    $param_array = array();
    $request = $MoodleRest->request('core_user_delete_users', $param_array);

    return $request;
}

function update_moodle_users() {
    $MoodleRest = init_moodlerest(true);

    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $param_array = array("criteria" => array(0 => array("key" => "email", "value" => "%%"))); // get all users with email wildcard
    $user_request = $MoodleRest->request('core_user_get_users', $param_array);
    $user_count = 1;
    foreach ($user_request["users"] as $user) {
        // Set data from response to variables
        $userid = $user['id'];
        $userfname = $user["firstname"];
        $userlname = $user["lastname"];
        $email = $user["email"];
        $username = $user["username"];
        $last_access = gmdate("Y-m-d g:i", $user["lastaccess"]); //  Note: Moodle's API returns Unix Epoch time/timestamp, remember to format to a readable format. Database is also expecting DateTime. gmdate should return time in GMT +0, handle epoch as never accessed.
            
        $query = "SELECT * FROM t_user WHERE userid = $userid";  
        if ($result = $mysqli -> query($query)) {

            if ($result -> num_rows > 0) {
                echo "User data already in table, updating...<br>";
                $result -> free_result();
                $query = "UPDATE t_user SET userfname = '$userfname', userlname = '$userlname', email = '$email', username = '$username', lastaccess = '$last_access' WHERE userid = $userid;";
                if ($result = $mysqli -> query($query)) {
                    echo "Query result: " . $result;
                }    
            } else {
                echo "User data not yet in table, inserting...<br>";
                $result -> free_result();
                $query = "INSERT INTO t_user (userid, userfname, userlname, email, username, lastaccess) VALUES ('$userid', '$userfname', '$userlname', '$email', '$username', '$last_access')";   
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

function create_moodle_course($fullname, $shortname, $category_id) {
    $MoodleRest = init_moodlerest(true);

    $param_array = array("courses" => array(0 => array("fullname" => $fullname, "shortname" => $shortname, "categoryid" => $category_id)));
    $request = $MoodleRest->request('core_course_create_courses', $param_array);

    return $request;
}

function update_moodle_courses() {
    /*  Request an array of courses from Moodle via API and then
        updates the Web Service's database.
        TODO: Handle enties that are no longer on Moodle but remain
        on WS's database.
    */
    $MoodleRest = init_moodlerest(true);

    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

    $param_array = array();
    $request = $MoodleRest->request('core_course_get_courses', $param_array);

    foreach ($request as $course) {
        $course_id = $course["id"];
        $course_name = $course["fullname"];
        $course_desc = $course["summary"];

        $query = "SELECT * FROM t_course WHERE courseid = $course_id";
        if ($result = $mysqli -> query($query)) {

            if ($result -> num_rows > 0) {
                echo "Course data already in table, updating...<br>";
                $result -> free_result();
                $query = "UPDATE t_course SET coursename = '$course_name', coursedesc = '$course_desc' WHERE courseid = $course_id;";

                if ($result = $mysqli -> query($query)) {
                    echo "Query result: " . $result;
                }
                   
            } else {
                echo "Course data not yet in table, inserting...<br>";
                $result -> free_result();
                $query = "INSERT INTO t_course (courseid, coursename, coursedesc) VALUES ('$course_id', '$course_name', '$course_desc')";
                //echo $query;
                    
                if ($result = $mysqli -> query($query)) {
                    echo "Query result: " . $result;
                    //$result -> free_result();
                }
            }
        
        }  

    }

}

function remove_moodle_course($course_id) {
    /*  Removes a Moodle Course corrosponding to the given $course_id
        NOTE: does not update WS's database
    */
    $MoodleRest = init_moodlerest(true);

    $param_array = array("courseids" => array(0 => $course_id));
    $request = $MoodleRest->request('core_course_delete_courses', $param_array);
}

function enrol_moodle_user($user_id, $course_id) {
    $MoodleRest = init_moodlerest();
    
    $param_array = array("enrolments" => array(0 => array("roleid" => "5", "userid" => "$user_id", "courseid" => "$course_id")));
    $request = $MoodleRest->request('enrol_manual_enrol_users', $param_array);
    
    return $request;
}

function update_users_enrol() {
    $moodle_ip = MOODLE_IP;
    $moodle_folder = MOODLE_FOLDER;
    $moodle_token = TOKEN;
    $MoodleRest = new MoodleRest();
    $MoodleRest->setServerAddress("http://$moodle_ip/$moodle_folder/webservice/rest/server.php");
    $MoodleRest->setToken($moodle_token);
    $MoodleRest->setReturnFormat(MoodleRest::RETURN_ARRAY);
    $MoodleRest->setDebug(false);

    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);

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