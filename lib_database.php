<?php 
/*
This script contains all Web Service Database related functions.
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

TODOs:
-   Enforce data with Moodle (no dangling or leftover entries can occur- after 
    deletion API calls)
-   Continually update docs and comments
*/

require_once "config.php";

function get_database_status() {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
    $events = Array();
    $event_count = 0;

    $query = "SELECT * FROM t_event_log";
    if ($result = $mysqli -> query($query)) {
            while($rows = $result->fetch_assoc()) {
                $events[$event_count++] = $rows;
            }
    }

    return $result;
}

function get_single_result($table_name, $target_column, $filter_column, $where_clause) {
    // function to get a single column, row result from a database entry
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

    $query = "SELECT $target_column FROM $table_name WHERE $filter_column = $where_clause";
    if ($result = $mysqli -> query($query)) {
        if ($result -> num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                return $row[$target_column];
            }
        }
    }
}

function get_all_units() {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
    $units = Array();

    $query = "SELECT * FROM t_unit ORDER BY unitid";
    if ($result = $mysqli -> query($query)) {
        if ($result -> num_rows > 0) {
            while($row = $result->fetch_assoc()) {
            $units[] = $row;
        }
      }
    //echo "Query result: " . $result -> num_rows . " rows.";
    $result -> free_result(); // Free result set
    }
    return $units;
}

function get_single_unit($unit_id) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
    $unit = Array();

    $query = "SELECT * FROM t_unit WHERE unitid = $unit_id";
        if ($result = $mysqli -> query($query)) {
            while($row = $result->fetch_assoc()) {
                $unit = $row;
                if ($row['hierarchy'] == "0") {
                    $unit["parent"] = "None";
                } else {
                    $unit["parent"] = $row['parent_unit'];
                }
            }
         $result -> free_result(); 
        }
    return $unit;
}

function get_child_from_unit($unit_id) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
    $child_units = Array();

    $query = "SELECT * FROM t_unit WHERE parent_unit = $unit_id";
    if ($result = $mysqli -> query($query)) {
        if ($result -> num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $child_units[] = $row; 
            }
        } else {
            $child_units[] = Array("unitname" => "No child units.<br>");
        }
        $result -> free_result(); 
    }

    return $child_units;
}

function get_users_in_unit($unit_id) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
    $users = Array();

    $query = "SELECT * FROM t_user, t_assign WHERE t_assign.unitid = $unit_id AND t_user.userid = t_assign.userid";
    if ($result = $mysqli -> query($query)) {
        if ($result -> num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               $users[] = $row;
            }
        } else {
            $users[] = Array("username" => "No users on this unit.<br>");
        }
        $result -> free_result(); 
    }

    return $users;
}

function update_unit($unit_id, $unit_name, $unit_desc){
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

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
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

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

function get_all_roles() {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
    $roles = Array();

    $query = "SELECT * FROM t_role ORDER BY roleid";
    if ($result = $mysqli -> query($query)) {
        if ($result -> num_rows > 0) // shows query result if num of rows > 0
        {
            while($row = $result->fetch_assoc()) {
                $roles[] = $row;
            }
        }
    $result -> free_result(); // Free result set
    }
    return $roles;
}

function get_single_role($role_id) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
    $role = Array();

    $query = "SELECT * FROM t_role WHERE roleid = $role_id";
    if ($result = $mysqli -> query($query)) {
        while($row = $result->fetch_assoc()) {
            $role = $row;
        }
        $result -> free_result(); 
    }
    return $role;
}

function get_users_in_role($role_id) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
    $users = Array();

    $query = "SELECT * FROM t_user, t_assign WHERE t_assign.roleid = $role_id AND t_user.userid = t_assign.userid";
    if ($result = $mysqli -> query($query)) {
        if ($result -> num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               $users[] = $row;
            }
        } else {
            $users[] = Array("username" => "No users with this role.<br>");
        }
        $result -> free_result(); 
    }

    return $users;
}

function update_role($role_id, $role_name, $role_desc) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

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
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

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

function get_enrolled_users_from_course($course_id) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
    $users = Array();

    $query = "SELECT * FROM t_user, t_course_enrollment WHERE t_course_enrollment.courseid ='$course_id' AND t_course_enrollment.userid = t_user.userid";
    if ($result = $mysqli -> query($query)) {
        if ($result -> num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $users[] = $row;
            }  
      $result -> free_result();
        }
    }
   return $users;
}

function update_user($user_id, $username, $password, $userfname, $userlname, $email, $wsadmin) {
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

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
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

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
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

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
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

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
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

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
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

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
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

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
    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

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

?>
