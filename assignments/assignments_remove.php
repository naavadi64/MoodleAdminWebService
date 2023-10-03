<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Remove Assignments</title>
   </head>
   
   <body>

   <?php
   
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Assignments > Remove Assignments
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
   <h1 style="margin-top:100px">Remove Assignment:</h1>

   <form method="post">
        <label for="assign_id">Assign ID: </label>
        <input type="number" id="assign_id" name="assign_id" min="0">
        <br><br>
        <input type="submit" name="rm_assign_trigger" class="button" value="Delete Specified Assignment" />
    </form>

   <?php

   if(array_key_exists('rm_assign_trigger', $_POST)) { // button trigger
      $assign_id = $_POST["assign_id"];
      include('../function_call.php');
      remove_assignment($assign_id);
   }

   ?>

   </div>
    
   </body>
   
</html>