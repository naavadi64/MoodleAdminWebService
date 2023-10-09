<html>
   
   <head>
      <link rel="stylesheet" href="styles/styles.css">
      <title>Dashboard</title>
   </head>
   
   <body>

   <?php
   include('session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      > Dashboard
      <div class="header-right">
         <a href="" class="logo">Logo</a>
         
      </div>
   </div>

   <!-- Navigation Bar -->
   <ul>
      <li><a href="units/units_overview.php">Units</a></li>
      <li><a href="roles/roles_overview.php">Roles</a></li>
      <li><a href="assignments/assignments_overview.php">Assignments</a></li>
      <li><a href="users/users_overview.php">Users</a></li>
      <li><a href="courses/courses_overview.php">Courses</a></li>
   </ul> 

   <!-- Page Body --->
   <div style="margin-left:25%;padding:1px 16px;height:1000px;">
   <h1>Welcome <?php echo $login_session; ?></h1>
   <br>

   <h2>Web Service Status Log:</h2>
   No. | Time Stamp (UTC+0) | Event <br>
   <?php

   $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME); // Note two different methods of connecting is used, check that
   $event_count = 0;
   $query = "SELECT * FROM t_event_log";
   if ($result = $mysqli -> query($query)) {
      //echo "Submitted query: " . $query . '<br>';
      if ($result -> num_rows > 0) // shows query result if num of rows > 0
      {
         while($row = $result->fetch_assoc()) {
            //cho implode(" ",$row) . '<br><br>';
            echo ++$event_count. " | " . $row['time'] . " | " . $row["event"] . "<br>";
         }
      }
   //echo "Query result: " . $result -> num_rows . " rows.";
   $result -> free_result(); // Free result set
   }

   ?>

   <h2>Service Overview:</h2>

   <?php

   $query = "SELECT COUNT(*) AS 'count' FROM t_user";
   if ($result = $mysqli -> query($query)) {
      if ($result -> num_rows > 0) // Should always return 1 row
      {
         while($row = $result->fetch_assoc()) {
            echo "Total users (including guest account): " . $row["count"] . '<br>';
         }
      }   
   $result -> free_result(); // Free result set
   }

   $query = "SELECT COUNT(*) AS 'count' FROM t_course; ";
   if ($result = $mysqli -> query($query)) {
      if ($result -> num_rows > 0) 
      {
         while($row = $result->fetch_assoc()) {
            echo "Total courses (including Moodle Site): " . $row["count"] . '<br>';
         }
      }
   $result -> free_result(); // Free result set
   }

   $query = "SELECT COUNT(*) AS 'count' FROM t_unit; ";
   if ($result = $mysqli -> query($query)) {
      if ($result -> num_rows > 0) 
      {
         while($row = $result->fetch_assoc()) {
            echo "Total units: " . $row["count"] . '<br>';
         }
      }
   $result -> free_result(); // Free result set
   }

   $query = "SELECT COUNT(*) AS 'count' FROM t_role; ";
   if ($result = $mysqli -> query($query)) {
      if ($result -> num_rows > 0) 
      {
         while($row = $result->fetch_assoc()) {
            echo "Total roles: " . $row["count"] . '<br>';
         }
      }
   $result -> free_result(); // Free result set
   }

   ?>

   <h2><a href = "logout.php">Sign Out</a></h2>
   </div>
    
   </body>
   
</html>