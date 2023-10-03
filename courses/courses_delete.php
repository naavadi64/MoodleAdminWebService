<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Remove Course</title>
   </head>
   
   <body>

   <?php
   
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
        > <a href="../dashboard.php">Dashboard</a> > Courses > Remove Courses
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
   <h1 style="margin-top:100px">Remove Course:</h1>

   <form method="post">
        <label for="course_id">Course ID: </label>
        <input type="number" id="course_id" name="course_id" min="0">
        <br><br>
        <input type="submit" name="rm_course_trigger" class="button" value="Delete Specified Course" />
    </form>

   <?php

   if(array_key_exists('rm_course_trigger', $_POST)) { // button trigger
      $course_id = $_POST["course_id"];
      include('../function_call.php');
      remove_moodle_course($course_id);
      remove_course($course_id);
   }

   ?>

   </div>
    
   </body>
   
</html>