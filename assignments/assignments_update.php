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
      <li><a href="../courses/courses_overview.php">Courses</a></li>
   </ul> 

   <!-- Page Body --->
   <div style="margin-left:20%;padding:1px 16px;height:1000px;">
   <h1 style="margin-top:100px">Add New User:</h1>

   <form method="post">
        <label for="user_id"><br>User ID: </label>
        <input type="text" id="user_id" name="user_id">
        <label for="role_id"><br>Role ID: </label>
        <input type="text" id="role_id" name="role_id">
        <label for="unit_id"><br>Unit ID: </label>
        <input type="text" id="unit_id" name="unit_id">
        <label for="start"><br>Start Date: </label>
        <input type="date" id="start" name="start">
        <label for="end"><br>End Date: </label>
        <input type="date" id="end" name="end">
        <label for="contract"><br>Contract Link: </label>
        <input type="text" id="contract" name="contract">
        <label for="status"><br>Contract Status: </label>
        <input type="text" id="status" name="status">
        <br><br>
        <input type="submit" name="add_assign_trigger" class="button" value="Add New User" />
    </form>

   <?php

   if(array_key_exists('add_assign_trigger', $_POST)) { // button trigger
        // TODO add error handling when user exists and form values invalid
        $assign_id = $_POST["assign_id"];
        $user_id= $_POST["user_id"];
        $role_id = $_POST["role_id"];
        $unit_id = $_POST["unit_id"];
        $start = $_POST["start"];
        $end = $_POST["end"];
        $contract = $_POST["contract"];
        $status = $_POST["status"];

        include('../function_call.php');
        $result = update_assignment($assign_id, $user_id, $role_id, $unit_id, $start, $end, $contract, $status);
   }
   
   ?>

   </div>
    
   </body>
   
</html>