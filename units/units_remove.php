<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Remove Units</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Units > Remove Units
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
   <h1 style="margin-top:100px">Remove Unit:</h1>

   <form method="post">
        <label for="unitid">Unit ID: </label>
        <input type="number" id="unitid" name="unitid" min="0">
        <br><br>
        <input type="submit" name="rm_unit_trigger" class="button" value="Delete Specified Unit" />
    </form>

   <?php

   if(array_key_exists('rm_unit_trigger', $_POST)) { // button trigger
      $unit_id = $_POST["unitid"];
      include("../function_call.php");
      remove_unit($unit_id);
   }

   ?>

   </div>
    
   </body>
   
</html>