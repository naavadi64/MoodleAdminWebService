<?php
// Browse to config.php only to test settings
// Otherwise use require_once "config.php" and run the class on webpages;

    // Set connection to Moodle site and credentials below
    define('MOODLE_IP', "192.168.56.101"); // IP to Moodle
    define('MOODLE_FOLDER', "moodle");
    define('TOKEN', "b31c9fea7655cdcbdd94ca7fc02b9983"); // This should be from a login instead
    
    // Set connection and credentials to web service database below
    define('WS_DB_IP' , "192.168.56.101"); // IP to webservice database
    define('WS_DB_USER' , "admin"); // This should not be an admin account
    define('WS_DB_PASS' , "admin");
    define('WS_DB_NAME' , "moodleadminwebservice");
    $db = mysqli_connect(WS_DB_IP, WS_DB_USER, WS_DB_PASS, WS_DB_NAME);


//echo Configurations::IP;

?>