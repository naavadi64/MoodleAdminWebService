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
   include('../function_call.php');
   $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
   if(isset($_GET['unit_id'])) {
      $unit_id = $_GET['unit_id'];
        
      $query = "SELECT * FROM t_unit WHERE unitid = $unit_id";
      if ($result = $mysqli -> query($query)) {
         if ($result -> num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               $unit_name = $row['unitname'];
               $unit_desc = $row['unitdesc'];
               $hierarchy = $row['hierarchy'];
               if ($hierarchy == "0") {
                  $parent_link = "None";
               } else {
                  $parent_id = $row['parent_unit'];
                  $parent = get_single_result("t_unit", "unitname", "unitid", $parent_id);
                  $parent_link = "<a href='unit_detail.php?unit_id=$parent_id'>$parent</a>";
               }

               echo 
                  "Unit Name: " . $unit_name . "<br>" . 
                  "Unit ID: " . $unit_id . "<br>" . 
                  "Hierarchy Level: " . $hierarchy . "<br>" . 
                  "Hierarchy Parent: " . $parent_link . "<br><br>" . 
                  "Unit Description:<br>" . $unit_desc . "<br>";
            }
         }
         $result -> free_result(); 
      }

      echo "<h2>Child Units:</h2>";
      $query = "SELECT unitname, unitid FROM t_unit WHERE parent_unit = $unit_id";
      $unit_count = 0;
      if ($result = $mysqli -> query($query)) {
         if ($result -> num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               $child_unitname = $row['unitname'];
               $child_unit_id = $row['unitid'];
               echo ++$unit_count . ". <a href='../units/unit_detail.php?unit_id=$child_unit_id'>$child_unitname  (ID: $child_unit_id)</a><br>";
            }
         } else {
            echo "No child units.<br>";
         }
         $result -> free_result(); 
      }
      
      
      echo "<h2>Users assigned to this unit:</h2>";

      $query = "SELECT t_user.username, t_user.userid FROM t_user, t_assign WHERE t_assign.unitid = $unit_id AND t_user.userid = t_assign.userid";
      $user_count = 0;
      if ($result = $mysqli -> query($query)) {
         if ($result -> num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               $username = $row['username'];
               $user_id = $row['userid'];
               echo ++$user_count . ". <a href='../users/user_detail.php?user_id=$user_id'>$username  (ID: $user_id)</a><br>";
            }
         } else {
            echo "No users on this unit.<br>";
         }
         $result -> free_result(); 
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