<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Remove Roles</title>
   </head>
   
   <body>

   <?php
   
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Users > Remove Users
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
      <li><a href="../courses/users_overview.php">Courses</a></li>
   </ul> 

   <!-- Page Body --->
   <div style="margin-left:20%;padding:1px 16px;height:1000px;">
   <h1 style="margin-top:100px">Remove User:</h1>

   <form method="post">
        <label for="user_id">User ID: </label>
        <input type="number" id="user_id" name="user_id" min="0">
        <br><br>
        <input type="submit" name="rm_user_trigger" class="button" value="Delete Specified User" />
    </form>

   <?php

   if(array_key_exists('rm_user_trigger', $_POST)) { // button trigger
      $user_id = $_POST["user_id"];
      include('../function_call.php');
      remove_moodle_user($user_id);
      remove_user($user_id);
   }

   ?>

   </div>
    
   </body>
   
</html>