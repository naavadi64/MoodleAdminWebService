<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Add or Update Roles</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Units > Add or Update Roles
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
   <h1 style="margin-top:100px">Add or Update Roles:</h1>

   <form method="post">
        <label for="roleid">Role ID: </label>
        <input type="number" id="roleid" name="roleid" min="0">
        <label for="rolename"><br>Role Name: </label>
        <input type="text" id="rolename" name="rolename">
        <label for="roledesc"><br>Role Description: </label>
        <input type="text" id="roledesc" name="roledesc">
        <br><br>
        <input type="submit" name="mod_role_trigger" class="button" value="Modify or add new role" />
    </form>

   <?php

   

   if(array_key_exists('mod_role_trigger', $_POST)) { // button trigger
      $role_id = $_POST["roleid"];
      $role_name = $_POST["rolename"];
      $role_desc = $_POST["roledesc"];
      include('../lib_database.php');
      update_role($role_id, $role_name, $role_desc);
   }

   ?>

   </div>
    
   </body>
   
</html>