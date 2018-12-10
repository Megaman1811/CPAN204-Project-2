<?php
//Making sure that the current user is an admin by using session
session_start();
if (!isset($_SESSION["User"])) {
    header('location:login.php');
    exit;
}
if ($_SESSION["User"] != "Guest") {
    header('location:login.php');
    exit;
}

$host = "localhost";
$user = "root";
$password = "";
$dbName = "travel_project";
$RegistrationId = $_SESSION['registrationId'];
$Email = $_SESSION['email'];
$Name = '';
$Address = '';
$Interested_vacation_plan = '';
$GroupId = '';
$Date = '' ;
$Status = '';
$MembersName = '';

$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");

if(isset($_POST['name']) && isset($_POST['address'])&& isset($_POST['interested_vacation_plan'])&& isset($_POST['groupId'])&& isset($_POST['date'])&& isset($_POST['status'])&& isset($_POST['address'])&& isset($_POST['membersName'])) {
    $Name = mysqli_real_escape_string($con, $_POST['name']);
    $Address = mysqli_real_escape_string($con, $_POST['address']);
    $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
    $GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
    $Date = mysqli_real_escape_string($con, $_POST['date']);
    $Status = mysqli_real_escape_string($con, $_POST['status']);
    $MembersName = mysqli_real_escape_string($con, $_POST['membersName']);
}

$query = "SELECT * FROM useraccount WHERE email = '$Email' AND registrationId = '$RegistrationId'";
$result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
        if (($row = mysqli_fetch_row($result)) == true) {
            $query_tour = "SELECT tourName FROM interestedvacationplan WHERE tourId = '$row[4]'";
            $result_tour = mysqli_query($con, $query_tour) or die ("query is failed" . mysqli_error($con));
            if (($row_tour = mysqli_fetch_row($result_tour)) == true) {
                $RegistrationId = $row[0];
                $Name = $row[1];
                $Email = $row[2];
                $Address = $row[3];
                $Interested_vacation_plan = $row_tour[0];
                $Date = $row[5];
                $GroupId = $row[6];
                $Status = $row[7];
            }
        }
        else echo "<script> alert ('Record not found !! Find failed');</script>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incident Reports Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO"
          crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
            integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
            integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
            crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.full.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.2.1/moment.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/jquery.datetimepicker.css"
          rel="stylesheet"/>
</head>
<body>
<div class="card-header">
    <h4><b>User Information Page</b></h4>
    <ul class="nav nav-pills card-header-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="UserInfoPage.php">User Information Page </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="Login.php">Log out</a>
        </li>
    </ul>
</div>

<?php
$query = "Select * from useraccount where email = '$Email' ";
$result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
echo "<table class=\"table table-bordered\">";
echo "<thead class=\"thead-dark\"><tr><th scope='col'>Registration ID </th><th scope='col'>Name</th><th scope='col'>Email</th><th scope='col'>Address</th><th scope='col'>Tour Id</th><th scope='col'>Date</th><th scope='col'>GroupId</th><th scope='col'>Status</th></tr></thead>";
while (($row = mysqli_fetch_row($result)) == true) {
echo "<tr><td>$row[0]</td><td>$row[2]</td><td>$row[2]</td><td>$row[3]</td><td>$row[4]</td><td>$row[5]</td><td>$row[6]</td><td>$row[7]</td></tr>";
}
echo "</table><br><br>";
?>
<form method="post" align="center">
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
    <label>Status: </label>
    <input type="text" placeholder="Status" name="status" value="<?php echo $Status; ?>"><br><br>
    <label>Group Member Name: </label><br>
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
<?php
//Close connection
mysqli_close($con);
?>
