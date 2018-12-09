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
$Status = '';

$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");


if (isset($_POST['FIND'])) {
    if (!empty(mysqli_real_escape_string($con, $_POST['registrationId']))) {
        $RegistrationId = mysqli_real_escape_string($con, $_POST['registrationId']);
        $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
        $Date = mysqli_real_escape_string($con, $_POST['date']);
        $query = "Select * from useraccount where registrationId = '$RegistrationId'";
        $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
        if (($row = mysqli_fetch_row($result)) == true) {
            $RegistrationId = $row[0];
            $Name = $row[1];
            $Email = $row[2];
            $Address = $row[3];
            $Interested_vacation_plan = $row[4];
            $Date = $row[5];
            $GroupId = $row[6];
            $Status = $row[7];
        } else echo "<script> alert ('Record not found !! Find failed');</script>";
    } else echo "<script> alert ('Please fill up Registration ID field to search');</script>";
}











?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Information Page</title>
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
<h1>User Information Page</h1>
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
    <input type="text" placeholder="Registration Id" name="registrationId"
           value="<?php echo $RegistrationId; ?>"><br><br>
    <label>Email: </label>
    <input type="text" placeholder="Email" name="email" value="<?php echo $Email; ?>"><br><br>
    <label>Name: </label>
    <input type="text" placeholder="Name" name="name" value="<?php echo $Name; ?>"><br><br>
    <label>Address: </label>
    <input type="text" placeholder="Address" name="address" value="<?php echo $Address; ?>"><br><br>
    <label>Interested Vacation Plan</label>
    <input type="text" placeholder="Interested Vacation Plan" name="interested_vacation_plan"
           value="<?php echo $Interested_vacation_plan; ?>"><br><br>
    <label>Date: </label>
    <input type="date" placeholder="Date" name="date" value="<?php echo $Date; ?>"><br><br>
    <label>Group Id: </label>
    <select name="groupId">
        <option value="">None</option>
        <?php
        if (isset($_POST['FIND'])) {
            $queryid = "Select interestedvacationplanId,date from useraccount where registrationId = '$RegistrationId'";
            $resultid = mysqli_query($con, $queryid) or die ("query is failed" . mysqli_error($con));
            if (($rowid = mysqli_fetch_row($resultid)) == true) {
                $queryreturn = "SELECT DISTINCT groupId FROM useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan' AND date = '$Date'";
                $resultreturn = mysqli_query($con, $queryreturn) or die ("query is failed" . mysqli_error($con));
                while ($rowreturn = mysqli_fetch_row($resultreturn)) {
                    if ($GroupId == $rowreturn[0]) {
                        echo "<option selected value=$rowreturn[0]> " . $rowreturn[0] . "</option>";
                    } else {
                        echo "<option value=$rowreturn[0]> " . $rowreturn[0] . "</option>";
                    }
                }
            }
        }
        ?>
    </select><br><br>
    <label>Status: </label>
    <input type="text" placeholder="Status" name="status" value="<?php echo $Status; ?>"><br><br>
    <input type="submit" class="btn btn-warning" value="Find" name="FIND"/>
    <input type="submit" class="btn btn-success" value="Update" name="UPDATE"/>

</form>
</body>
</html>
