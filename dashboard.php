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
   <h1 style="margin-top:200px">Welcome <?php echo $login_session; ?></h1>
   <br>

   <h2>Web Service Status Log:</h2>
   No. | Time Stamp (UTC+0) | Event <br>
   <?php
   require_once('lib_database.php');

   $status = get_database_status();
   foreach ($status as $count => $event) {
   echo $count . ". | " . $event["time"] . " | " . $event["event"];
   }

   echo "<h2>Service Overview:</h2>";
   $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);

   $query = "SELECT COUNT(*) AS 'count' FROM t_user";
   if ($result = $mysqli -> query($query)) {
      while($row = $result->fetch_assoc()) {
         echo "Total users (including guest account): " . $row["count"] . '<br>';
      }
      $result -> free_result();
   }

   $query = "SELECT COUNT(*) AS 'count' FROM t_course; ";
   if ($result = $mysqli -> query($query)) {
      while($row = $result->fetch_assoc()) {
         echo "Total courses (including Moodle Site): " . $row["count"] . '<br>';
      }
      $result -> free_result();
   }

   $query = "SELECT COUNT(*) AS 'count' FROM t_unit; ";
   if ($result = $mysqli -> query($query)) {
      while($row = $result->fetch_assoc()) {
         echo "Total units: " . $row["count"] . '<br>';
      }
      $result -> free_result(); // Free result set
   }

   $query = "SELECT COUNT(*) AS 'count' FROM t_role; ";
   if ($result = $mysqli -> query($query)) {
         while($row = $result->fetch_assoc()) {
            echo "Total roles: " . $row["count"] . '<br>';
         }
      $result -> free_result(); // Free result set
   }

   ?>

   <h2><a href = "logout.php">Sign Out</a></h2>
   </div>
    
   </body>
   
</html>