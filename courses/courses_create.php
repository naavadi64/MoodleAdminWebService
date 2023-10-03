<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Create Course Template</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Courses > Create Course Template
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
   <h1 style="margin-top:100px">Create Course Template:</h1>

   <form method="post">
        <label for="fullname">Course Full Name: </label>
        <input type="text" id="fullname" name="fullname">
        <label for="shortname"><br>Course Short Name: </label>
        <input type="text" id="shortname" name="shortname">
        <!-- <label for="category_id"><br>Course Category ID: </label> lock category for now
        <input type="text" id="category_id" name="category_id"> -->
        <br><br>
        <input type="submit" name="add_course_trigger" class="button" value="Add Template Course" />
    </form>

   <?php

   if(array_key_exists('add_course_trigger', $_POST)) { // button trigger
        $fullname = $_POST["fullname"];
        $shortname= $_POST["shortname"];
        $category_id = 1;

        include('../function_call.php');
        $result = create_moodle_course($fullname, $shortname, $category_id);

        $course_id = $result[0]["id"];
        $course_name = $fullname;
        $course_desc = ""; //TODO: add desc field
        update_course($course_id, $course_name, $course_desc);
   }

   ?>

   </div>
    
   </body>
   
</html>