<html>
   
   <head>
      <link rel="stylesheet" href="styles/styles.css">
      <title>API Test</title>
   </head>
   
   <body>

   <!-- Page Header -->
   <div class="header">
      API Test
      <div class="header-right">
         <a href="" class="logo">Logo</a>
         
      </div>
   </div>

   <!-- Navigation Bar -->
   <ul>
      <li><a href="units/units_overview.php">Units</a></li>
      <li><a href="roles/roles_overview.php">Roles</a></li>
      <li><a href="assignments/assignments_overview.php">Assignments</a></li>
      <li><a href="users/users_overview.php">Users</a></li>
      <li><a href="courses/courses_overview.php">Courses</a></li>
   </ul> 

   <!-- Page Body --->
   <div style="margin-left:25%;padding:1px 16px;height:1000px;">
   <h1 style="margin-top:200px">API Test</h1>
   <br>

   <h2>Web Service Status Log:</h2>
   No. | Time Stamp (UTC+0) | Event <br>
   <?php
   include('config.php');
   include('function_call.php');

   $event_count = 0;

   $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
   $query = "SELECT * FROM t_event_log";
   if ($result = $mysqli -> query($query)) {
      //echo "Submitted query: " . $query . '<br>';
      if ($result -> num_rows > 0) // shows query result if num of rows > 0
      {
         while($row = $result->fetch_assoc()) {
            //cho implode(" ",$row) . '<br><br>';
            echo ++$event_count. " | " . $row['time'] . " | " . $row["event"] . "<br>";
         }
      }
   //echo "Query result: " . $result -> num_rows . " rows.";
   $result -> free_result(); // Free result set
   }

   echo "<h2>API Test:</h2>";
   
   $user_id = 2;
   $course_id = 3;

   //echo "<h2>update_moodle_users</h2>";
   //update_moodle_users(true);

   // check [completion] to see if module is completable
   /*
   echo "<h2>[Direct Call] core_course_get_course_module</h2>";
   $MoodleRest = init_moodlerest(true);
   $param_array = array("cmid" => 9);
   $request = $MoodleRest->request('core_course_get_course_module', $param_array);
   $param_array = array("cmid" => 21);
   $request = $MoodleRest->request('core_course_get_course_module', $param_array);
   $param_array = array("cmid" => 12);
   $request = $MoodleRest->request('core_course_get_course_module', $param_array);
   */

   //echo "<h2>get_moodle_user_course_status</h2>";
   //$request = get_moodle_user_course_activity_status($user_id, $course_id, true);
   //print_r($request); // returns user module data in course_id that is completable

   //echo "<h2>show_moodle_course_sections</h2>";
   //$request = show_moodle_course_sections($course_id, false, true, true);
   //print_r($request);

   // Misal course x, maka kolom tabelnya
   //1. Participant id
   //2. Participant name
   //3. Activity 1 name dan statusnya, bisa saja hanya activity name lalu statusnya pakai warna. 
   //Warna hijau complete
   //Warna merah incomplete
   //4. Activity 2, dst
   $example_data = array( 
      array("User ID"=>1, "Name"=>"User1" , "Book"=>"N/A", "Quiz"=>"Done", "Assignment"=>"Done"),
      array("User ID"=>2, "Name"=>"User2" , "Book"=>"N/A", "Quiz"=>"Not Completed", "Assignment"=>"Done"),
      array("User ID"=>3, "Name"=>"User3" , "Book"=>"N/A", "Quiz"=>"Not Completed", "Assignment"=>"Not Completed"),  
   ); 
   echo "<h2>Example table</h2><br>".  generate_table($example_data) . "<br>";

   // Get activity module list w/ name
   $activity_list = Array();
   $course = show_moodle_course_sections($course_id, false);
   foreach ($course as $section) {
      foreach ($section["modules"] as $module) {
         if (array_key_exists("completion", $module)) { // handle empty sections in course
            if ($module["completion"] == 1)
            {
               $activity_list[$module["id"]] = $module["name"];
            };
         }
      }
   }

   // Get list of users enrolled in course
   $user_list = array();
   $query = "SELECT t_user.username, t_user.userid FROM t_user, t_course_enrollment WHERE t_course_enrollment.courseid ='$course_id' AND t_course_enrollment.userid = t_user.userid";
   if ($result = $mysqli -> query($query)) {
      if ($result -> num_rows > 0)
      {
         while($row = $result->fetch_assoc()) {
            $user_id = $row["userid"];
            $username = $row["username"];
            $user_list[$row["userid"]] = $row["username"];
         }
            
      $result -> free_result();
      }
   };

   // iterate over list of users, call api to check user activity completion
   $data = Array();
   foreach ($user_list as $user_id => $username) {
      $row = Array("User ID" => $user_id, "Name" => $username);
      $activities = get_moodle_user_course_activity_status($user_id, $course_id);
      foreach ($activities["statuses"] as $activity) {
         $activity_name = $activity_list[$activity["cmid"]];
         $activity["state"] == 1 ? $row[$activity_name] = "Done" : $row[$activity_name] = "Not Completed";
      }
      $data[] = $row;
   }
   //echo print_r($data) . "<br>";
   echo generate_table($data) . "<br>";
   ?>

   <h2><a href = "logout.php">Sign Out</a></h2>
   </div>
    
   </body>
   
</html>