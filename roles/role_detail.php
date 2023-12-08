<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Role Detail</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Roles > Role Details
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
   <h1 style="margin-top:100px">Role Details</h1>

   <?php
   require_once("../lib_database.php");
   if(isset($_GET['role_id'])) {
      $role_id = $_GET['role_id'];

      $role = get_single_role($role_id);
      echo 
         "Role Name: " . $role['rolename'] . "<br>" . 
         "Role ID: " . $role['roleid'] . "<br><br>" . 
         "Role Description:<br>" . $role['roledesc'] . "<br>";

      echo "<h2>Users with this role:</h2>";

      $users = get_users_in_role($role_id);
      if ($users[0]["username"] == "No users with this role.<br>") {
         echo $users[0]["username"];
      } else {
         foreach ($users as $index => $user) {
            $username = $user['username'];
            $user_id = $user['userid'];
            echo ++$index . ". <a href='../users/user_detail.php?user_id=$user_id'>$username  (ID: $user_id)</a><br>";
         }
      }

    } else {
        echo 
            "
            No Unit ID has been specified.
            ";
    }

    ?>

   </div>
    
   </body>
   
</html>