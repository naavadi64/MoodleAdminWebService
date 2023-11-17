<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>User Details</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Users > User Detail
      </div>
      <div class="header-right">
         <a href="" class="logo">Logo</a>
         <br>
         <a href = "../logout.php">Sign Out</a>
      </div>
   </div>

   <!-- Navigation Bar -->
   <ul>
      <li><a href="../units/units_overview.php">Units</a></li>
      <li><a href="../roles/roles_overview.php">Roles</a></li>
      <li><a href="../assignments/assignments_overview.php">Assignments</a></li>
      <li><a href="../users/users_overview.php">Users</a></li>
      <li><a href="../courses/courses_overview.php">Courses</a></li>
   </ul> 

   <!-- Page Body --->
   <div style="margin-left:20%;padding:1px 16px;height:1000px;">
   <h1 style="margin-top:100px">User Details:</h1>

   <?php
   include('../function_call.php');
   $user_id = $_GET["user_id"];

    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

    $query = "SELECT * FROM t_user WHERE userid = $user_id";
    if ($result = $mysqli -> query($query)) {

        if ($result -> num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                //echo implode(" ", $row);
                $user_id = $row["userid"];
                $username = $row["username"];
                $user_pass = $row["userpass"];
                $userfname = $row["userfname"];
                $userlname = $row["userlname"];
                $email = $row["email"];
                $wsadmin = $row["wsadmin"];
                $last_access = $row["lastaccess"];
                echo
                    "User ID: " . $user_id . "<br>" . 
                    "Username: " . $username .  "<br>" . 
                    'Password: ' . $user_pass .  "<br>" . 
                    "First Name: " . $userfname .  "<br>" . 
                    "Last Name: " . $userlname .  "<br>" . 
                    "Email: " . $email .  "<br><br>" .
                    "Last Access to Moodle Site: " . $last_access .  "<br>" . 
                    "Web Service Admin Level: " . $wsadmin . "<br><br>";
                $query = "SELECT t_unit.unitname, t_unit.unitid FROM t_unit, t_assign WHERE t_assign.userid = $user_id AND t_assign.unitid = t_unit.unitid"; // TODO: Clean up and move up in html
                if ($result = $mysqli -> query($query)) {
                    while($row = $result->fetch_assoc()) {
                        $unit_id = $row["unitid"];
                        $unit_name = $row["unitname"];
                        echo "Unit: <a href='../units/unit_detail.php?unit_id=$unit_id'>$unit_name (ID: $unit_id)</a><br>";
                        
                    }
                }
                
                $query = "SELECT t_role.rolename, t_role.roleid FROM t_role, t_assign WHERE t_assign.userid = $user_id AND t_assign.roleid = t_role.roleid";
                if ($result = $mysqli -> query($query)) {
                    while($row = $result->fetch_assoc()) {
                        $role_id = $row["roleid"];
                        $role_name = $row["rolename"];
                        echo "Role: <a href='../roles/role_detail.php?role_id=$role_id'>$role_name (ID: $role_id)</a>";
                    }
                }
            }
        } else {
            echo "UserID not found!<br><br>";
            
        }
    }

   ?>

    <h2>Enrolled Courses:</h1>
    <?php
    $user_id = $_GET["user_id"];

    $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

    $query = "SELECT t_course_enrollment.courseid, t_course.coursename, t_course_enrollment.progress FROM t_course_enrollment, t_course WHERE userid = $user_id AND t_course.courseid = t_course_enrollment.courseid";
    $course_count = 0;
    if ($result = $mysqli -> query($query)) {

        if ($result -> num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                //echo implode(" - ", $row) . "%<br>";
                $course_id = $row["courseid"];
                $course_name = $row["coursename"];
                $progress = $row["progress"];
                echo ++$course_count . ". <a href='../courses/course_detail.php?course_id=$course_id'>$course_name (ID: $course_id)</a> - $progress% Complete<br>";

                // pull data of user's actvitiy in moodle and save for later
                $course_activity =  get_moodle_user_course_activity_status($user_id, $course_id);
                $course_activity_data = array(); // store as [key: cmid] => [value: state]
                foreach ($course_activity["statuses"] as $activity) {
                    $cmid = $activity["cmid"];
                    $cm_state = $activity["state"];
                    $course_activity_data[$cmid] = $cm_state;
                };

                //print_r($course_activity_data);
                //Array ( [9] => 1 [10] => 1 [11] => 1 [12] => 0 ) 

                $course_sections = show_moodle_course_sections($course_id);
                echo "<br>";
                foreach($course_sections as $section) {
                    echo $section["name"] . "<br>";

                    foreach($section["modules"] as $module) {
                        $module_name = $module["name"];
                        $module_id = $module["id"];
                        $module_completion_type = $module["completion"]; // Type of completion tracking: 0 means none, 1 manual, 2 automatic.
                        echo '&nbsp&nbsp&nbsp&nbsp - ' . $module["name"] . " (Module ID: " . $module["id"] . ")";
                        if (array_key_exists($module_id, $course_activity_data)) {
                            $completion = $course_activity_data[$module_id] == 0 ? "Not Completed." : "Done.";
                            echo " -> " . $completion;
                        };
                        echo "<br>";
                    }

                    echo "<br>";
                };
                echo "<br>";
            }
        } else {
            echo "No courses found! Please check and sync database<br>";
            
        }
    }

   ?>

   </div>
    
   </body>
   
</html>