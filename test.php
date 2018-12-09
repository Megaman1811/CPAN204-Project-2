<?php
$host = "localhost";
$user = "root";
$password = "";
$dbName = "travel_project";

$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");

$query_select_groupSize_null = "SELECT groupSize FROM groupinfo WHERE groupId = 1";
$result_select_groupSize_null = mysqli_query($con, $query_select_groupSize_null) or die ("query is failed" . mysqli_error($con));
if ($rowsizenull = mysqli_fetch_row($result_select_groupSize_null)) {
    if ($rowsizenull[0] === NULL){
        echo "fjdklsjlfkdsklfjkdlsjklfds";
    }

}
