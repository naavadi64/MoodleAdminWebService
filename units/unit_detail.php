<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Unit Detail</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Units > Unit Details
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
   <h1 style="margin-top:100px">Unit Details</h1>

   <?php
   include('../lib_database.php');
   $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
   if(isset($_GET['unit_id'])) {
      $unit_id = $_GET['unit_id'];
        
      $unit = get_single_unit($unit_id);
      if ($unit['parent'] == "None") {
         $parent_link = "None";
      } else {
         $parent_id = $unit['parent'];
         $parent_name = get_single_result("t_unit", "unitname", "unitid", $parent_id);
         $parent_link = "<a href='unit_detail.php?unit_id=$parent_id'>$parent_name</a>";
      };
      
      echo 
         "Unit Name: " . $unit['unitname'] . "<br>" . 
         "Unit ID: " . $unit['unitid'] . "<br>" . 
         "Hierarchy Level: " . $unit['hierarchy'] . "<br>" . 
         "Hierarchy Parent: " . $parent_link . "<br><br>" . 
         "Unit Description:<br>" . $unit['unitdesc'] . "<br>";

      echo "<h2>Child Units:</h2>";
   
      $child_units = get_child_from_unit($unit_id);
      if ($child_units[0]["unitname"] == "No child units.<br>") {
         echo $child_units[0]["unitname"];
      } else {
         foreach ($child_units as $index => $child_unit) {
            $child_unitname = $child_unit['unitname'];
            $child_unit_id = $child_unit['unitid'];
            echo ++$index . ". <a href='../units/unit_detail.php?unit_id=$child_unit_id'>$child_unitname  (ID: $child_unit_id)</a><br>";
         }
      }
      
      echo "<h2>Users assigned to this unit:</h2>";

      $users = get_users_in_unit($unit_id);
      if ($users[0]["username"] == "No users on this unit.<br>") {
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