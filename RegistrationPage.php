<?php
$host = "localhost";
$user = "root";
$password = "";
$dbName = "travel_project";
$RegistrationId = '';
$Email = '';
$Name = '';
$Address = '';
$Interested_vacation_plan = '';
$GroupId = '';
$Date = '';

//Connect to server + select DB
$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");
// When user used insert function
if (isset($_POST['INSERT'])) {
    $GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
    $Email = mysqli_real_escape_string($con, $_POST['email']);
    $Name = mysqli_real_escape_string($con, $_POST['name']);
    $Address = mysqli_real_escape_string($con, $_POST['address']);
    $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
    $Date = mysqli_real_escape_string($con, $_POST['date']);

    $queryselect = "SELECT groupId FROM useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan' AND date = '$Date'";
    $resultselect = mysqli_query($con, $queryselect) or die ("query is failed" . mysqli_error($con));

    if (($row = mysqli_fetch_row($resultselect)) == false){

        $GroupId = rand(1, 10);

        echo $GroupId;
        echo "check";
        $queryselectall = "SELECT groupId FROM useraccount";
        $resultselectall = mysqli_query($con, $queryselectall) or die ("query is failed" . mysqli_error($con));
        while (($row = mysqli_fetch_row($resultselectall)) == true){
            while($GroupId == $row[0]){
                $GroupId = rand(1, 10);
            }

        }
    }
    else {

            $GroupId = $row[0];
            echo $GroupId;
            echo "test";
        }






    $query = "Insert Into useraccount(email, name, address, interestedvacationplanId, groupId, date) Values('$Email','$Name','$Address','$Interested_vacation_plan','$GroupId','$Date')";
    $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
    if (mysqli_affected_rows($con) > 0) {
        $query = "SELECT registrationId FROM useraccount WHERE email = '$Email' ORDER BY registrationId DESC LIMIT 1";
        $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
        if (($row = mysqli_fetch_row($result)) == true) {
            $RegistrationId = $row[0];

            echo "<script> alert ('$RegistrationId');</script>";
        } else {
            echo "<script> alert ('You have not inserted any rows');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Page</title>
</head>
<body>
<h1>Registration Page</h1>
<form method="post">
    <label>Email: </label>
    <input type="text" placeholder="Email" name="email" required><br><br>
    <label>Name: </label>
    <input type="text" placeholder="Name" name="name" required><br><br>
    <label>Address: </label>
    <input type="text" placeholder="Address" name="address" required><br><br>
    <label>Interested Vacation Plan</label>
    <select name="interested_vacation_plan" required>
        <option value="">None</option>
        <?php
        $query_select_tourId = "SELECT * FROM interestedvacationplan";
        $result_select_tourId = mysqli_query($con, $query_select_tourId);
        while ($row = mysqli_fetch_row($result_select_tourId)) {

            if ($Interested_vacation_plan == $row[0]) {
                echo "<option selected value=$row[0]> " . $row[0] . " -- " . $row[1] . "</option>";
            } else {
                echo "<option value=$row[0]> " . $row[0] . " -- " . $row[1] . "</option>";
            }
        }
        ?>
    </select><br><br>
    <label>Date: </label>
    <input type="date" placeholder="Date" name="date" required><br><br>
    <input type="submit" value="Submit" name="INSERT">

</form>
</body>
</html>


