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
$Date = '' ;
$MembersName = '';

$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");

$RegistrationId = 32;/*mysqli_real_escape_string($con, $_POST['registrationId']);*/
$Email = 'kennychen2410@gmail.com';/*mysqli_real_escape_string($con, $_POST['email']);*/
$Name = mysqli_real_escape_string($con, $_POST['name']);
$Address = mysqli_real_escape_string($con, $_POST['address']);
$Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
$GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
$Date = mysqli_real_escape_string($con, $_POST['date']);
$MembersName = mysqli_real_escape_string($con, $_POST['membersName']);

$query = "SELECT * FROM useraccount WHERE email = '$Email' AND registrationId = '$RegistrationId'";
$result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
        if (($row = mysqli_fetch_row($result)) == true) {
            $RegistrationId = $row[0];
            $Name = $row[1];
            $Email = $row[2];
            $Address= $row[3];
            $Interested_vacation_plan= $row[4];
            $Date= $row[5];
            $GroupId = $row[6];
        }
        else echo "<script> alert ('Record not found !! Find failed');</script>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Information Page</title>
</head>
<body>
<h1>User Information Page</h1>
<form method="post">
    <label>Registration Id: </label>
    <input type="text" placeholder="Registration Id" name="registrationId" value="<?php echo $RegistrationId ; ?>"><br><br>
    <label>Email: </label>
    <input type="text" placeholder="Email" name="email" value="<?php echo $Email; ?>"><br><br>
    <label>Name: </label>
    <input type="text" placeholder="Name" name="name" value="<?php echo $Name; ?>"><br><br>
    <label>Address: </label>
    <input type="text" placeholder="Address" name="address" value="<?php echo  $Address; ?>"><br><br>
    <label>Interested Vacation Plan</label>
    <input type="text" placeholder="Interested Vacation Plan" name="interested_vacation_plan" value="<?php echo $Interested_vacation_plan; ?>"><br><br>
    <label>Date: </label>
    <input type="date" placeholder="Date" name="date" value="<?php echo $Date; ?>"><br><br>
    <label>Group Id: </label>
    <input type="text" placeholder="GroupId" name="groupId" value="<?php echo $GroupId; ?>"><br><br>
    <label>Group Member Name: </label>
    <textarea rows="10" cols="50" placeholder="Group member name" name="membersName"><?php $query = "SELECT email FROM useraccount WHERE groupId = '$GroupId'";
        $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
        while ($row = mysqli_fetch_row($result))
        {
            $MembersName = $row[0];
            echo $MembersName;
            echo "\n";
        }?></textarea><br><br>

</form>
</body>
</html>
