<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Roles Overview</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Roles
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
   <h1 style="margin-top:100px">Roles Overview</h1>
   <br>

   <h2>List of Roles:</h2>
   <?php
   require_once("../lib_database.php");

   $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
   $role_count = 0;

   $roles = get_all_roles();
   foreach ($roles as $index => $role) {
      $role_id = $role["roleid"];
      $role_name = $role["rolename"];
      $role_link = "<a href='role_detail.php?role_id=$role_id'>$role_name - $role_id</a><br>";
      echo ++$index . ". " . $role_link;
   }

   ?>

   <h2>Manage Roles:</h2>
   <a href="roles_update.php">Add or Update Roles</a> <br>
   <a href="roles_remove.php">Remove Roles</a>

   </div>
    
   </body>
   
</html>