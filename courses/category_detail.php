<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Category Detail</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Courses > Category Details
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
   <h1 style="margin-top:100px">Category Details</h1>

   <?php
    if(isset($_GET['category_id'])) {
        $category_id = $_GET['category_id'];
        $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
        $query = "SELECT * FROM t_categories WHERE categoryid = $category_id";
        if ($result = $mysqli -> query($query)) {
            if ($result -> num_rows > 0)
            {
                while($row = $result->fetch_assoc()) {
                    $category_name = $row['categoryname'];
                    $category_desc = $row['categorydesc'];
                    echo 
                        "Category Name: " . $category_name . "<br>" . 
                        "Category ID: " . $category_id . "<br><br>" . 
                        "Category Description:<br>" . $category_desc . "<br>";
                }
            }
            $result -> free_result(); 
        }

        echo "Courses in category:<br>";
        $query = "SELECT coursename, courseid FROM t_course WHERE categoryid = $category_id";
        //echo $query . "<br>";
        if ($result = $mysqli -> query($query)) {
            if ($result -> num_rows > 0)
            {
               while($row = $result->fetch_assoc()) {
                  //echo implode(" ", $row) . "<br>";
                  $course_name = $row["coursename"];
                  $course_id = $row["courseid"];
                  echo "<a href='course_detail.php?user_id=$course_id'>$course_name</a><br>";
               }
            
            $result -> free_result(); 
            } else {
                echo "No courses under this category.";
            }
        }

    } else {
        echo 
            "
            No Category ID has been specified.
            ";
    }

   ?>

   </div>
    
   </body>
   
</html>