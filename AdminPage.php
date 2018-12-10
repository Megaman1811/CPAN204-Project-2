<?php
//Making sure that the current user is an admin by using session
session_start();
if (!isset($_SESSION["User"])) {
    header('location:Login.php');
    exit;
}
if ($_SESSION["User"] != "Admin") {
    header('location:Login.php');
    exit;
}
//Declaring some variables to connection into db
$host = "localhost";
$user = "root";
$password = "";
$dbName = "travel_project";

// Get session Email from Login page
$Email = $_SESSION['email'];

//Connect to server + select DB
$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");

if (isset($_POST['email']))
    $Email = mysqli_real_escape_string($con, $_POST['email']);

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
<div class="card-header">

    <h5>Admin Page</h5>
    <ul class="nav nav-pills card-header-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="Admin_Page.php">Admin</a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="UserInfoPageAdmin.php">User Information Management</a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="FormGroupPage.php">Group Management</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="Login.php">Log out</a>
        </li>
    </ul>
</div>
<div align="center">
    <div class="card-body">
        <br><br>
        <h1 class="card-title">Admin Page</h1><br>
        <form>
            <div class="form-inline">
            </div>
        </form>
        <a href="UserInfoPageAdmin.php" class="btn btn-primary">User Information Management</a>
        <a href="FormGroupPage.php" class="btn btn-warning">Group Management</a>
    </div>
</div>
</body>
</html>

<?php
mysqli_close($con);
?>
