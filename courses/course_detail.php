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
   include('../function_call.php');

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

    if(array_key_exists('show_contents_trigger', $_POST)) {
      echo "<h2>Course Structure:</h2>";
      $contents = show_moodle_course_sections($course_id);
      $section_count = 0;
      foreach ($contents as $section) {
         $section_id = $section["id"];
         $section_name = $section["name"];
         $section_summary = $section["summary"];
         $visibility = $section["visible"] == 1 ? "Visible" : "Not Visible";
         $section_modules = count($section["modules"]);
         echo 
            ++$section_count . ". $section_name (ID:  $section_id)<br>" . 
            "Summary: " . "$section_summary<br>" . 
            "Visibility: " . "$visibility<br>" . 
            "Number of modules: $section_modules<br>" . 
            "
            <form method='post'>
            <input type='hidden' id='section_removal' name='section_removal' value='$section_id'>
            <input type='submit' name='section_removal_trigger' class='button' value='Remove Section ID: $section_id' />
            </form>
            " . 
            "
            <form method='post'>
               <input type='hidden' id='section_edit' name='section_edit' value='$section_id'>
               <label for='section_addition'>Section Edit</label>
               <label for='section_name'><br>Section Name:</label>
               <input type='text' id='section_name' name='section_name'>
               <label for='section_summary'><br>Section Summary (Optional):</label>
               <input type='text' id='section_summary' name='section_summary'>
               <br>
               <input type='checkbox' id='visibility' name='visibility' value='1'>
               <label for='visibility'>Enable visibility to students</label>

               <br><br>
               <input type='submit' name='section_edit_trigger' class='button' value='Edit Section ID: $section_id' />
            </form><br><br>
            ";
      }
      
      //echo '<pre>'; print_r($contents); echo '</pre>';

    }

   ?>
   <form method="post">
      <input type="submit" name="show_contents_trigger" class="button" value="Show Course Contents" />
   </form>

   <h2>Modify Sections<h2>
   <form method="post">
      <label for="batch_section_additions">Number of sections to add: </label>
      <input type="number" id="batch_section_additions" name="batch_section_additions" min="1">
      <input type="submit" name="batch_section_addition_trigger" class="button" value="Add Blank Section(s)"/>
   </form>
      
   <form method="post">
      <label for="section_addition">Or<br><br> Add section with details:</label>
      <label for="section_name"><br>Section Name:</label>
      <input type="text" id="section_name" name="section_name">
      <label for="section_summary"><br>Section Summary (Optional):</label>
      <input type="text" id="section_summary" name="section_summary">
      <br>
      <input type="checkbox" id="visibility" name="visibility" value="1">
      <label for="visibility">Enable visibility to students</label>

      <br><br>
      <input type="submit" name="section_addition_trigger" class="button" value="Add Section" />
      
   </form>
   

   <h2>Enrol Users</h2>
   <form method="post">
      <label for="user_id">User ID: </label>
      <input type="number" id="user_id" name="user_id" min="0">
      <br><br>
      <input type="submit" name="enrol_user_trigger" class="button" value="Enrol User to Course" />
   </form>
   
   <?php

   if(array_key_exists('batch_section_addition_trigger', $_POST)) { 
      $num_sections = $_POST['batch_section_additions'];
      $added_sections = add_moodle_course_section($course_id, $num_sections);
      // New blank sections are set to not visible
      foreach ($added_sections as $new_section) {
         $section_id = $new_section["sectionid"];
         $section_num = $new_section["sectionnumber"];
         echo $section_id . " | " . $section_num;
         set_moodle_course_section_visibility($course_id, $section_id, 0);
      }
   }

   if(array_key_exists('section_addition_trigger', $_POST)) {
      $new_section = add_moodle_course_section($course_id, 1);
      $section_id = $new_section[0]["sectionid"];
      $section_name = $_POST['section_name'];
      $section_summary = $_POST['section_summary'];
      $visibility = array_key_exists('visibility', $_POST) ? $visibility : $visibility = 0;
      echo $visibility;
      edit_moodle_course_section($course_id, $section_id, $section_name, $section_summary, 1, $visibility, 0);
      
   }

   if(array_key_exists('section_edit_trigger', $_POST)) {
      $section_id = $_POST['section_edit'];
      $section_name = $_POST['section_name'];
      $section_summary = $_POST['section_summary'];
      $visibility = array_key_exists('visibility', $_POST) ? $visibility : $visibility = 0;
      echo $section_id . " | " . $section_name . " | " . $section_summary . " | " . $visibility;
      edit_moodle_course_section($course_id, $section_id, $section_name, $section_summary, 1, $visibility, 0);
      
   }

   if(array_key_exists('section_removal_trigger', $_POST)) {
      $section_id = $_POST['section_removal'];
      remove_moodle_course_section($course_id, $section_id);
   }

   if(array_key_exists('enrol_user_trigger', $_POST)) {
      $user_id = $_POST['user_id'];
      enrol_user($user_id, $course_id);

      $result = enrol_moodle_user($user_id, $course_id);
   }

   ?>

   </div>
    
   </body>
   
</html>