<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Add or Update Units</title>
   </head>
   
   <body>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Units > Add or Update Units
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
   <h1 style="margin-top:100px">Add or Update Unit:</h1>

   <form method="post">
        <label for="unitid">Unit ID: </label>
        <input type="number" id="unitid" name="unitid" min="0">
        <label for="unitname"><br>Unit Name: </label>
        <input type="text" id="unitname" name="unitname">
        <label for="unitdesc"><br>Unit Description: </label>
        <input type="text" id="unitdesc" name="unitdesc">
        <br><br>
        <input type="submit" name="mod_unit_trigger" class="button" value="Modify or add new unit" />
    </form>

   <?php

   if(array_key_exists('mod_unit_trigger', $_POST)) { // button trigger
      include('../function_call.php');
      $unit_id = $_POST["unitid"];
      $unit_name = $_POST["unitname"];
      $unit_desc = $_POST["unitdesc"];
      update_unit($unit_id, $unit_name, $unit_desc) ; 
   }

   ?>

   </div>
    
   </body>
   
</html>