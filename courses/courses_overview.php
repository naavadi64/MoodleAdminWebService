<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Course Overview</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Courses
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
   <h1 style="margin-top:100px">Course Overview</h1>
   <br>

   <h2>List of Courses:</h2>
   <?php

   $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
   $course_count = 0;

   $query = "SELECT courseid, coursename FROM t_course ORDER BY courseid";
   if ($result = $mysqli -> query($query)) {
      if ($result -> num_rows > 0)
      {
         while($row = $result->fetch_assoc()) {
            //echo implode(" ",$row) . '<br><br>';
            $course_id = $row["courseid"];
            $course_name = $row["coursename"];
            $course_link = "<a href='course_detail.php?course_id=$course_id'>$course_name - $course_id</a><br>";
            echo ++$course_count . ". " . $course_link;
         }
      }
   //echo "Query result: " . $result -> num_rows . " rows.";
   $result -> free_result(); // Free result set
   }

   ?>

   <br>
   <form method="post">
      <input type="submit" name="sync_course_trigger" class="button" value="Sync Database with Moodle" />
   </form>
   <?php

   if(array_key_exists('sync_course_trigger', $_POST)) { // button trigger
      include('../function_call.php');
      update_moodle_courses(); // Get courses from moodle API
      update_users_enrol(); // Update user course enrollment data
   }

   ?>

   <h2>Manage courses:</h2>
   <a href="courses_create.php">Create Course Template</a> <br>
   <a href="courses_delete.php">Delete Course</a>

   </div>
    
   </body>
   
</html>