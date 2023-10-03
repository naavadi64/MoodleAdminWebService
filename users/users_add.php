<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Add New User</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Users > Add New User
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
   <h1 style="margin-top:100px">Add New User:</h1>

   <form method="post">
        <label for="username">Username: </label>
        <input type="text" id="username" name="username">
        <label for="password"><br>Password: </label>
        <input type="text" id="password" name="password">
        <label for="userfname"><br>First Name: </label>
        <input type="text" id="userfname" name="userfname">
        <label for="userlname"><br>Last Name: </label>
        <input type="text" id="userlname" name="userlname">
        <label for="email"><br>Email: </label>
        <input type="text" id="email" name="email">
        <br><br>
        <input type="submit" name="add_user_trigger" class="button" value="Add New User" />
    </form>

   <?php

    if(array_key_exists('add_user_trigger', $_POST)) { // button trigger
        // TODO add error handling when user exists and form values invalid
        $username = $_POST["username"];
        $password = $_POST["password"];
        $userfname = $_POST["userfname"];
        $userlname = $_POST["userlname"];
        $email = $_POST["email"];

        include('../function_call.php');
        // Call Moodle API to create user
        $result = add_moodle_user($username, $password, $userfname, $userlname, $email);

        // Add Result to Web Service database
        if (array_key_exists(0, $result))
        $user_id = $result[0]["id"];
        $username = $result[0]["username"];
        update_user($user_id, $username, $password, $userfname, $userlname, $email, 0);
    }

    if(array_key_exists('sync_user_trigger', $_POST)) {
        
    }

   ?>

   </div>
    
   </body>
   
</html>