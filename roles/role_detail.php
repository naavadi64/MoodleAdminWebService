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
    if(isset($_GET['role_id'])) {
        $role_id = $_GET['role_id'];
        $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_USER, WS_DB_NAME);
        $query = "SELECT * FROM t_role WHERE roleid = $role_id";
        if ($result = $mysqli -> query($query)) {
            if ($result -> num_rows > 0)
            {
                while($row = $result->fetch_assoc()) {
                    $role_name = $row['rolename'];
                    $role_desc = $row['roledesc'];
                    echo 
                        "Role Name: " . $role_name . "<br>" . 
                        "Role ID: " . $role_id . "<br><br>" . 
                        "Role Description:<br>" . $role_desc . "<br>";
                }
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