<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Users Overview</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Users
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
   <h1 style="margin-top:100px">Users Overview</h1>
   <br>

   <h2>List of Users:</h2>
   <?php

   $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);
   $user_count = 0;

   $query = "";

   if(array_key_exists("filter_user_trigger", $_POST)) { 
      $query = "SELECT * FROM t_user ORDER BY userid";
   } else {
      $query = "SELECT * FROM t_user ORDER BY userid";
   }
   
   if ($result = $mysqli -> query($query)) {
      //echo "Submitted query: " . $query . '<br>';
      if ($result -> num_rows > 0) // shows query result if num of rows > 0
      {
         while($row = $result->fetch_assoc()) {
            //echo implode(" ",$row) . '<br><br>';
            $user_id = $row["userid"];
            $username = $row["username"];
            $user_link = "<a href='user_detail.php?user_id=$user_id'>$username - $user_id</a><br>";
            echo ++$user_count . ". " . $user_link;
         }
      }
   echo "Total Users (Including Guest): " . $result -> num_rows . " users.<br>";
   $result -> free_result(); // Free result set
   }

   ?>
   <br>
   <form method="post">
      <input type="submit" name="sync_user_trigger" class="button" value="Sync Database with Moodle" /><br>  
      <input type="submit" name="filter_user_trigger" class="button" value="Filter Users" />
   </form>
   
   <?php

   if(array_key_exists('sync_user_trigger', $_POST)) { 
      include('../function_call.php');
      update_moodle_users();
   }

   ?>

   <h2>Manage Users:</h2>
   <a href="users_add.php">Add a New User</a> <br>
   <a href="users_remove.php">Remove Users</a> <br>

   </div>
    
   </body>
   
</html>