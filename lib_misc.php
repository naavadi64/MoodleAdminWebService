<?php 
/*
This script contains misc functions used within the webservice.
The flow of data should be authoritave to Moodle,
meaning data should be updated to Moodle first, then the Web Service's
Database.
General flow of CRUD-ing data connected to Moodle is:
User requests on WS -> call and update Moodle API -> update WS database

List of tables dependant on Moodle:
- Users (userid, username, userfname, userlname, email, last access)
- Courses (courseid)
- Course Enrolments
- Assignments
- Event Log (No columns dependant, entries to keep track of database events)

List of tables not dependant on Moodle (Moodle API calls not needed)
- Role
- Unit

TODOs:
-   Enforce data with Moodle (no dangling or leftover entries can occur- after 
    deletion API calls)
-   Continually update docs and comments
*/

function generate_table($data_array) {
    /* 
    Generates and returns a table formatted in html. 
    Expecting data_array in the form of array( array( {key} => {value} ) ).
    Make sure data is formatted as and array of array of key-value pairs.
    Any other input will cause the script to panic.
    */
    $html_result = "<table>";

    // Set header:
    $html_result .= "<tr>";
    foreach($data_array[0] as $key=>$value) { // takes the first row and uses key (header) only
        $html_result .= "<th>" . htmlspecialchars($key) . "</th>";
    };
    $html_result .= "</tr>";

    // Set table body
    foreach($data_array as $key=>$value) {
        $html_result .= "<tr>";
        foreach($value as $key2=>$value2) {
            $html_result .= "<td>" . $value2 . "</td>";
        }
        $html_result .= "</tr>";
    }

    $html_result .= "</table>";
    return $html_result;
}


?>