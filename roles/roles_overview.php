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

   $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);
   $role_count = 0;

   $query = "SELECT * FROM t_role ORDER BY roleid";
   if ($result = $mysqli -> query($query)) {
      //echo "Submitted query: " . $query . '<br>';
      if ($result -> num_rows > 0) // shows query result if num of rows > 0
      {
         while($row = $result->fetch_assoc()) {
            //echo implode(" ",$row) . '<br><br>';
            $role_id = $row["roleid"];
            $role_name = $row["rolename"];
            $role_link = "<a href='role_detail.php?role_id=$role_id'>$role_name - $role_id</a><br>";
            echo ++$role_count . ". " . $role_link;
         }
      }
   //echo "Query result: " . $result -> num_rows . " rows.";
   $result -> free_result(); // Free result set
   }

   ?>

   <h2>Manage Roles:</h2>
   <a href="roles_update.php">Add or Update Roles</a> <br>
   <a href="roles_remove.php">Remove Roles</a>

   </div>
    
   </body>
   
</html>