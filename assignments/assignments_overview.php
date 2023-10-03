<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Assignments Overview</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Assignments
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
   <h1 style="margin-top:100px">Assignments Overview</h1>
   <br>

   

   <?php
   if (array_key_exists('filter_reset_trigger', $_POST)) {
      echo "<h2>List of Assignments:</h2>";
      $query = "SELECT * FROM t_assign";

   } elseif(array_key_exists('filter_assign_trigger', $_POST)) {
      echo "<h2>Search results:</h2>";
      $filter_user_id = $_POST["toggle_user_id"] = "on" ? "%" : $_POST["user_id"];
      $filter_role_id = $_POST["toggle_role_id"] = "on" ? "%" : $_POST["role_id"];
      $filter_unit_id = $_POST["toggle_unit_id"] = "on" ? "%" : $_POST["unit_id"];
      $filter_status =$_POST["status"] = "any" ? "%" : $_POST["status"];
      //echo $filter_user_id . $filter_role_id . $filter_unit_id . $filter_status;
      // SELECT * FROM `t_assign` WHERE userid LIKE "%" AND unitid LIKE "%" AND roleid LIKE "%";
      $query = "SELECT * FROM `t_assign` WHERE userid LIKE '$filter_user_id' AND unitid LIKE '$filter_role_id' AND roleid LIKE '$filter_unit_id' AND status LIKE '$filter_status'";
    
   } else {
      echo "<h2>List of Assignments:</h2>";
      $query = "SELECT * FROM t_assign";
   }
   ?>

   Assign ID | User ID | Role ID | Unit ID | Start Date | End Date | Contract Link | Contract Status<br>
   <?php
   include('../function_call.php');

   $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);
   $course_count = 0;

   //$query = "SELECT * FROM t_assign";
   if ($result = $mysqli -> query($query)) {
      if ($result -> num_rows > 0)
      {
         while($row = $result->fetch_assoc()) {
            $user_id = $row['userid'];
            $username = get_single_result("t_user", "username", "userid", $user_id);
            $user_link = "<a href='../users/user_detail.php?user_id=$user_id'>$username</a>";
            $role_id = $row['roleid'];
            $rolename = get_single_result("t_role", "rolename", "roleid", $role_id);
            $role_link = "<a href='../roles/role_detail.php?role_id=$role_id'>$rolename</a>";
            $unit_id = $row['unitid'];
            $unitname = get_single_result("t_unit", "unitname", "unitid", $unit_id);
            $unit_link = "<a href='../units/unit_detail.php?unit_id=$unit_id'>$unitname</a>";
            echo
               $row["assignid"] . " | " . 
               $user_link . " | " . 
               $role_link . " | " . 
               $unit_link . " | " . 
               $row["start"] . " | " . 
               $row["end"] . " | " . 
               $row["contract"] . " | " . 
               $row["status"]. "<br>";
         }
      }
   echo "<br>Total number of assignments: " . $result -> num_rows . "<br><br>";
   $result -> free_result(); // Free result set
   }

   ?>

   <h2>Search and Filter:</h2>

   <form method="post">
      <label for="user_id">User ID: </label>
      <input type="checkbox" id="toggle_user_id" name="toggle_user_id">
      <input type="number" id="user_id" name="user_id">
      <label for="role_id"><br>Role ID: </label>
      <input type="checkbox" id="toggle_role_id" name="toggle_role_id">
      <input type="number" id="role_id" name="role_id">
      <label for="unit_id"><br>Unit ID: </label>
      <input type="checkbox" id="toggle_unit_id" name="toggle_unit_id">
      <input type="number" id="unit_id" name="unit_id">
      <label for="status"><br>Contract Status: </label>
      <select id="status" name="status">
         <option value="any">Any</option>
         <option value="active">Active</option>
         <option value="inactive">Inactive</option>
         <option value="suspended">Suspended</option>
         <option value="probation">Probation</option>
      </select>
      <br>
      <input type="submit" name="filter_assign_trigger" class="button" value="Filter Users" />
      <input type="submit" name="filter_reset_trigger" class="button" value="Reset Filters" />
   </form>

   <h2>Manage Assignments:</h2>
   <a href="assignments_update.php">Update Assignements</a> <br>
   <a href="assignments_remove.php">Remove Assignements</a>

   </div>
    
   </body>
   
</html>