<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Dashboard</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Units
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
   <h1 style="margin-top:100px">Units Overview</h1>

   <h2>List of Units:</h2>
   <?php

   require_once("../lib_database.php");

   $units = get_all_units();
   $unit_count = 0;
   foreach ($units as $unit) {
      $unit_id = $unit["unitid"];
      $unit_name = $unit["unitname"];
      $unit_link = "<a href='unit_detail.php?unit_id=$unit_id'>$unit_name - $unit_id</a>";
      echo ++$unit_count . ". " . $unit_link . "<br>";
   } 

   ?>

   <h2>Manage Units:</h2>
   <a href="units_update.php">Add or Update Unit</a> <br>
   <a href="units_remove.php">Remove Unit</a>

   </div>
    
   </body>
   
</html>