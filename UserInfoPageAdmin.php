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

//making some variable clear when first login
$RegistrationId = '';
$Email = '';
$Name = '';
$Address = '';
$Interested_vacation_plan = '';
$GroupId = '';
$Status = '';

//Connect to database server
$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");

//When admin use find function it will find all the information and pass it by providing valid registrationId
if (isset($_POST['FIND'])) {
    if (!empty(mysqli_real_escape_string($con, $_POST['registrationId']))) {
        $RegistrationId = mysqli_real_escape_string($con, $_POST['registrationId']);
        $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
        $query = "Select * from useraccount where registrationId = '$RegistrationId'";
        $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
        if (($row = mysqli_fetch_row($result)) == true) {
            $RegistrationId = $row[0];
            $Name = $row[1];
            $Email = $row[2];
            $Address = $row[3];
            $Interested_vacation_plan = $row[4];
            $GroupId = $row[5];
            $Status = $row[6];


        } else echo "<script> alert ('Record not found !! Find failed');</script>";
    } else echo "<script> alert ('Please fill up Registration ID field to search');</script>";

 //Some select query to display specific the user info by valid email into a table
    $query = "Select * from useraccount where email = '$Email' ";
    $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
    echo "<table class=\"table table-bordered\">";
    echo "<thead class=\"thead-dark\"><tr><th scope='col'>Registration ID </th><th scope='col'>Name</th><th scope='col'>Email</th><th scope='col'>Address</th><th scope='col'>Tour Id</th><th scope='col'>GroupId</th><th scope='col'>Status</th></tr></thead>";
    while (($row = mysqli_fetch_row($result)) == true) {
        echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td><td>$row[4]</td><td>$row[5]</td><td>$row[6]</td></tr>";
    }
    echo "</table><br><br>";
}

//When admin use update function and make sure that the text box is not null
if (isset($_POST['UPDATE'])) {
    if (!empty($_POST['name']) && !empty($_POST['registrationId']) && !empty($_POST['email']) && !empty($_POST['address'])
        && !empty($_POST['interested_vacation_plan']) && !empty($_POST['groupId'])
        && !empty($_POST['status'])) {
        $RegistrationId = mysqli_real_escape_string($con, $_POST['registrationId']);
        $Name = mysqli_real_escape_string($con, $_POST['name']);
        $Email = mysqli_real_escape_string($con, $_POST['email']);
        $Address = mysqli_real_escape_string($con, $_POST['address']);
        $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
        $GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
        $Status = mysqli_real_escape_string($con, $_POST['status']);

//Select all group id with specific tour id and group id provided to check if the group admin want to apply more user in is enough space or not or admin move user into the correct groupid which is match with location tour id
        $query_group = "Select DISTINCT groupId from useraccount where interestedvacationplanId = '$Interested_vacation_plan' AND groupId = '$GroupId'";
        $result_group = mysqli_query($con, $query_group) or die ("query is failed" . mysqli_error($con));
        if ($row_group = mysqli_fetch_row($result_group) == true) {
            $query_size = "Select groupsize from groupinfo where groupId = '$GroupId'";
            $result_size = mysqli_query($con, $query_size) or die ("query is failed" . mysqli_error($con));
            if ($row_size = mysqli_fetch_row($result_size)) {
                echo $row_size[0];
                $querycount = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$GroupId'";
                $resultcount = mysqli_query($con, $querycount) or die ("query is failed" . mysqli_error($con));
                if ($rowcount = mysqli_fetch_row($resultcount)) {
                    $ava = $row_size[0] - $rowcount[0];
                    if (($ava > 0) || empty($row_size[0])) {
                        $query = "Update useraccount set name = '$Name', address = '$Address', interestedvacationplanId = '$Interested_vacation_plan', groupid = '$GroupId', status = '$Status' WHERE registrationId = '$RegistrationId'";
                        $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
                        if (mysqli_affected_rows($con) > 0) {
                            $alert = "You have updated " . mysqli_affected_rows($con) . " row";
                            echo "<script> alert ('$alert');</script>";

                        } else {
                            echo "<script> alert ('Update failed');</script>";
                        }
                    } else {
                        echo "<script> alert ('This group space is full, please choose another');</script>";
                    }
                }
            }

        } else {
            echo "<script> alert ('There is no group available with your input');</script>";
        }

    } else {
        echo "Please fill up all Field";
    }

//Some select query to display all the user with specific registrationId into a table
    $query = "Select * from useraccount where registrationId = '$RegistrationId' ";
    $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
    echo "<table class=\"table table-bordered\">";
    echo "<thead class=\"thead-dark\"><tr><th scope='col'>Registration ID </th><th scope='col'>Name</th><th scope='col'>Email</th><th scope='col'>Address</th><th scope='col'>Tour Id</th><th scope='col'>GroupId</th><th scope='col'>Status</th></tr></thead>";
    while (($row = mysqli_fetch_row($result)) == true) {
        echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td><td>$row[4]</td><td>$row[5]</td><td>$row[6]</td></tr>";
    }
    echo "</table><br><br>";
}

//When admin use delete function a specific user by registrationId
if (isset($_POST['DELETE'])) {
    if (!empty($RegistrationId = mysqli_real_escape_string($con, $_POST['registrationId']))) {
        $RegistrationId = mysqli_real_escape_string($con, $_POST['registrationId']);
        $query = "Delete from useraccount where registrationId = '$RegistrationId'";
        $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
        $RegistrationId = '';
        $Email = '';
        $Name = '';
        $Address = '';
        $Interested_vacation_plan = '';
        $GroupId = '';
        $Date = '';
        $status = '';
        if (mysqli_affected_rows($con) > 0) {
            $alert = "You have deleted " . mysqli_affected_rows($con) . " row";
            echo "<script> alert ('$alert');</script>";


        } else {
            echo "<script> alert ('Record not found !! Delete failed');</script>";
        }
    } else {
        echo "<script> alert ('Please fill up Report ID field to delete');</script>";
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
    <h4><b>User Information Management</b></h4>
    <ul class="nav nav-pills card-header-tabs">
        <li class="nav-item">
            <a class="nav-link " href="AdminPage.php">Admin Page </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="UserInfoPage.php">User Information Management Page </a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="FormGroupPage.php">Group Management Page </a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="TourManagementPage.php">Tour Management Page</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="login.php">Log out</a>
        </li>
    </ul>
</div>

<!-- Some select query to display all the user into a table -->
<?php
$query = "Select * from useraccount";
$result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
echo "<table class=\"table table-bordered\">";
echo "<thead class=\"thead-dark\"><tr><th scope='col'>Registration ID </th><th scope='col'>Name</th><th scope='col'>Email</th><th scope='col'>Address</th><th scope='col'>Tour Id</th><th scope='col'>GroupId</th><th scope='col'>Status</th></tr></thead>";
while (($row = mysqli_fetch_row($result)) == true) {
    echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td><td>$row[4]</td><td>$row[5]</td><td>$row[6]</td></tr>";
}
echo "</table><br><br>";
?>
<form method="post" align="center">
    <label>Registration Id: </label>
    <input type="text" placeholder="Registration Id" name="registrationId"
           value="<?php echo $RegistrationId; ?>"><br><br>
    <label>Email: </label>
    <input type="email" placeholder="Email" name="email" value="<?php echo $Email; ?>" readonly><br><br>
    <label>Name: </label>
    <input type="text" placeholder="Name" name="name" value="<?php echo $Name; ?>"><br><br>
    <label>Address: </label>
    <input type="text" placeholder="Address" name="address" value="<?php echo $Address; ?>"><br><br>
    <label>Interested Vacation Plan</label>
    <select name="interested_vacation_plan">
        <option value="">None</option>
        <?php
//Passing the Tour Id, Tour Name, date from interestedvacationplan table into a dropdownlist box
        $query = "SELECT * FROM interestedvacationplan";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_row($result)) {


            if ($Interested_vacation_plan == $row[0]) {
                echo "<option selected value=$row[0]> " . $row[0] . " | " . $row[1] . " -- " . $row[2] . "</option>";
            } else {
                echo "<option value=$row[0]> " . $row[0] . " | " . $row[1] . " -- " . $row[2] . "</option>";
            }
        }
        ?>
    </select><br><br>
    <label>Group Id</label>
    <select name="groupId">
        <option value="">None</option>
        <?php
        // Retrieved all groupinfo table information
        $query = "SELECT * FROM groupinfo";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_row($result)) {
            // the function will add selected to option that have value match with group id variable to display
            if ($GroupId == $row[0]) {
                echo "<option selected value=$row[0]> " . $row[0] . " Size " . $row[1] . "</option>";
            } else {
                echo "<option value=$row[0]> " . $row[0] . " Size " . $row[1] . "</option>";
            }
        }
        ?>
    </select><br><br>
    <label>Status: </label>
    <input type="text" placeholder="Status" name="status" value="<?php echo $Status; ?>" readonly><br><br>
    <input type="submit" class="btn btn-warning" value="Find" name="FIND"/>
    <input type="submit" class="btn btn-success" value="Update" name="UPDATE"/>
    <input type="submit" class="btn btn-danger" value="Delete" name="DELETE"/>

</form>
</body>
</html>
<?php
//Close connection
mysqli_close($con);
?>
