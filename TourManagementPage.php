<?php
//Making sure that the current user is an admin by using session
session_start();
if (!isset($_SESSION["User"])) {
    header('location:login.php');
    exit;
}
if ($_SESSION["User"] != "Admin") {
    header('location:login.php');
    exit;
}

//declare some variable to connect to the db
$host = "localhost";
$user = "root";
$password = "";
$dbName = "id8150395_cpan204";

//make the variable empty when the page first run
$TourName = '';
$Date = '';

//Connect to database server
$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");

//When admin use insert function to open more tour trip for the user
if (isset($_POST['INSERT'])) {
    $TourName = mysqli_real_escape_string($con, $_POST['tourName']);
    $Date = mysqli_real_escape_string($con, $_POST['date']);


    $query = "SELECT * FROM interestedvacationplan WHERE tourName = '$TourName' and date = '$Date'";
    $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
    if (($row = mysqli_fetch_row($result) == false)) {
        $queryi = "Insert Into interestedvacationplan (tourName,date) Values('$TourName','$Date')";
        $resulti = mysqli_query($con, $queryi) or die ("query is failed" . mysqli_error($con));
        if (mysqli_affected_rows($con) > 0) {
            echo "<script> alert ('Insert successful ');</script>";
        } else {
            echo "<script> alert ('Insert failed ');</script>";
        }

    } else {
        echo "<script> alert ('The tour you add already exist!!! ');</script>";
    }

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Information Admin Page</title>
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
    <h4><b>Tour Management Page</b></h4>
    <ul class="nav nav-pills card-header-tabs">
        <li class="nav-item">
            <a class="nav-link " href="AdminPage.php">Admin Page </a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="UserInfoPage.php">User Information Management Page </a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="FormGroupPage.php">Group Management Page </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active " href="TourManagementPage.php">Tour Management Page</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="login.php">Log out</a>
        </li>
    </ul>
</div>
<!--  Some select query to display all the tour information  into a table -->
<?php
$query = "Select * from interestedvacationplan";
$result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
echo "<table class=\"table table-bordered\">";
echo "<thead class=\"thead-dark\"><tr><th scope='col'>Tour ID </th><th scope='col'>Tour Name</th><th scope='col'>Date</th></tr></thead>";
while (($row = mysqli_fetch_row($result)) == true) {
    echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td></tr>";
}
echo "</table><br><br>";
?>
<form method="post" align="center">
    <label>Tour Name: </label>
    <input type="text" placeholder="Tour Name" name="tourName"><br><br>
    <label>Date: </label>
    <input type="date" placeholder="Date" name="date"><br><br>
    <input type="submit" class="btn btn-success" value="Insert" name="INSERT">
</form>

</body>
</html>
