<html>
   
   <head>
      <link rel="stylesheet" href="../styles/styles.css">
      <title>Course Detail</title>
   </head>
   
   <body>

   <?php
   include('../session.php');
   ?>

   <!-- Page Header -->
   <div class="header">
      <div class="header-left">
         > <a href="../dashboard.php">Dashboard</a> > Courses > Course Details
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
   <h1 style="margin-top:100px">Course Details</h1>

   <?php
    if(isset($_GET['course_id'])) {
        $course_id = $_GET['course_id'];
        $mysqli = new mysqli(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);
        $query = "SELECT * FROM t_course WHERE courseid = $course_id";
        if ($result = $mysqli -> query($query)) {
            if ($result -> num_rows > 0)
            {
                while($row = $result->fetch_assoc()) {
                    $course_name = $row['coursename'];
                    $course_desc = $row['coursedesc'];
                    echo 
                        "Course Name: " . $course_name . "<br>" . 
                        "Course ID: " . $course_id . "<br><br>" . 
                        "Course Description:<br>" . $course_desc . "<br>";
                }
            }
            $result -> free_result(); 
        }

        echo "Enroled Users:<br>";
        $query = "SELECT t_user.username, t_user.userid FROM t_user, t_course_enrollment WHERE t_course_enrollment.courseid ='$course_id' AND t_course_enrollment.userid = t_user.userid";
        //echo $query . "<br>";
        if ($result = $mysqli -> query($query)) {
            if ($result -> num_rows > 0)
            {
               while($row = $result->fetch_assoc()) {
                  //echo implode(" ", $row) . "<br>";
                  $username = $row["username"];
                  $user_id = $row["userid"];
                  echo "<a href='../users/user_detail.php?user_id=$user_id'>$username</a><br>";
               }
            
            $result -> free_result(); 
            }
        }

    } else {
        echo 
            "
            No Unit ID has been specified.
            ";
    }

   ?>

   <h2>Enrol Users</h2>
   <form method="post">
      <label for="user_id">User ID: </label>
      <input type="number" id="user_id" name="user_id" min="0">
      <br><br>
      <input type="submit" name="enrol_user_trigger" class="button" value="Enrol User to Course" />
   </form>

   <?php

   if(array_key_exists('enrol_user_trigger', $_POST)) { // button trigger
      $user_id = $_POST['user_id'];
      include('../function_call.php');
      enrol_user($user_id, $course_id);

      require_once "../MoodleRest.php";  //TODO move to function_call
      require_once "../config.php";

      $result = enrol_moodle_user($user_id, $course_id);
      echo $result;
   }

   ?>

   </div>
    
   </body>
   
</html>