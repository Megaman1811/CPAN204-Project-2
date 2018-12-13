<?php
//declare some variable to connect to the db
$host = "localhost";
$user = "root";
$password = "";
$dbName = "id8150395_cpan204";

//make the variable empty when the page first run
$RegistrationId = '';
$Email = '';
$Name = '';
$Address = '';
$Interested_vacation_plan = '';
$GroupId = '';
$Date = '';
$status = '';

//Connect to database server
$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");

// When user used insert function
if (isset($_POST['INSERT'])) {
    $Email = mysqli_real_escape_string($con, $_POST['email']);
    $Name = mysqli_real_escape_string($con, $_POST['name']);
    $Address = mysqli_real_escape_string($con, $_POST['address']);
    $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
    $Date = mysqli_real_escape_string($con, $_POST['date']);

    $query = "SELECT groupId FROM groupinfo";
    $result =  mysqli_query($con, $query) or die ("query is failed " . mysqli_error($con));

//Check if the date in the interestedvacationplan table is null or not if null then insert the user input into it.
        $querynull = "Select tourId from interestedvacationplan where tourName = '$Interested_vacation_plan' && date IS NULL";
        $resultnull = mysqli_query($con, $querynull) or die ("query is failed " . mysqli_error($con));
        if ($rownull = mysqli_fetch_row($resultnull)) {
            $Tourid = $rownull[0];
            $query_status_update = "UPDATE interestedvacationplan SET date = '$Date' WHERE tourId = '$rownull[0]'";
            $result_status_update = mysqli_query($con, $query_status_update) or die ("query is failed" . mysqli_error($con));
        } else {
            $querycheck = "Select tourId from interestedvacationplan where tourName = '$Interested_vacation_plan' && date = '$Date'";
            $resultcheck = mysqli_query($con, $querycheck) or die ("query is failed" . mysqli_error($con));
            if ($rowcheck = mysqli_fetch_row($resultcheck)) {
                $Tourid = $rowcheck[0];
//If not then create a new tourid different with the rest and insert new tourName and date into it.
            } else {
                $Tourid = 1;
                $query_id = "SELECT tourId FROM interestedvacationplan";
                $result_id = mysqli_query($con, $query_id) or die ("query is failed1" . mysqli_error($con));
                while ($row_id = mysqli_fetch_row($result_id)) {
                    if ($row_id[0] == $Tourid) {
                        $Tourid = $Tourid + 1;
                    }
                }
                $queryi = "Insert Into interestedvacationplan Values('$Tourid','$Interested_vacation_plan', '$Date')";
                $resulti = mysqli_query($con, $queryi) or die ("query is failed" . mysqli_error($con));
            }

        }

// Check if the user with the specific email is already register for a tour with same location and same date on that specific date or not, if yes then show notification, no then can complete register.
        $querycheck = "SELECT tourId FROM interestedvacationplan WHERE tourName = '$Interested_vacation_plan' AND date = '$Date'";
        $resultcheck = mysqli_query($con, $querycheck) or die ("query is failed" . mysqli_error($con));
        if ($rowcheck = mysqli_fetch_row($resultcheck)) {
            $query_tid = "SELECT interestedvacationplanId from useraccount where email = '$Email' AND interestedvacationplanId = '$rowcheck[0]'";
            $result_tid = mysqli_query($con, $query_tid) or die ("query is failed" . mysqli_error($con));
            if ($row_tid = mysqli_fetch_row($result_tid)) {
                echo "<script> alert ('You already registered for this trip on this day');</script>";
            } else {

// Select all the groupId that contain specific Tourid from useraccount to insert the new register id into it.
                $queryselect = "SELECT DISTINCT groupId FROM useraccount WHERE interestedvacationplanId = '$Tourid'";
                $resultselect = mysqli_query($con, $queryselect) or die ("query is failed" . mysqli_error($con));
                if (($row = mysqli_fetch_row($resultselect)) == false) {
                    $GroupId = 1;
                    $queryselectall = "SELECT groupId FROM useraccount";
                    $resultselectall = mysqli_query($con, $queryselectall) or die ("query is failed" . mysqli_error($con));
                    while ($rows = mysqli_fetch_row($resultselectall)) {
                        if ($rows[0] == $GroupId) {
                            $GroupId = $GroupId + 1;
                        }
                    }

                } else {
                    do {

// Counting the member that specific group contain inside
                        $querya = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$row[0]'";
                        $resulta = mysqli_query($con, $querya) or die ("query is failed" . mysqli_error($con));

// Check the group size for that specific groupId
                        $queryb = "SELECT groupSize FROM groupinfo WHERE groupId = '$row[0]'";
                        $resultb = mysqli_query($con, $queryb) or die ("query is failed" . mysqli_error($con));
                        if ($rowa = mysqli_fetch_row($resulta)) {

                            if ($rowb = mysqli_fetch_row($resultb)) {
                                if ((($rowa[0] < $rowb[0]) && ($rowb[0] != NULL))) {

                                    $GroupId = $row[0];
                                    break;
                                } else if ((empty($rowb[0]))) {
                                    $GroupId = $row[0];
                                } else {
                                }
                            }
                        }
// return the final groupId here
                    } while ($row = mysqli_fetch_row($resultselect));
// null case for groupId
                    if ($GroupId == null) {
                        $GroupId = 1;
                        $queryselectall = "SELECT groupId FROM useraccount";
                        $resultselectall = mysqli_query($con, $queryselectall) or die ("query is failed" . mysqli_error($con));
                        while ($rows = mysqli_fetch_row($resultselectall)) {
                            if ($rows[0] == $GroupId) {
                                $GroupId = $GroupId + 1;
                            }
                        }
                    }

                }
//Select all group size and counting the number inside that group to set the specific status for each member

                $query_status_size = "SELECT groupSize FROM groupinfo WHERE groupId = '$GroupId'";
                $result_status_size = mysqli_query($con, $query_status_size) or die ("query is failed1" . mysqli_error($con));
                if ($row_status_size = mysqli_fetch_row($result_status_size)) {
                    $query_status_count = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$GroupId'";
                    $result_status_count = mysqli_query($con, $query_status_count) or die ("query is failed2" . mysqli_error($con));
                    if ($row_status_count = mysqli_fetch_row($result_status_count)) {
                        $ava = $row_status_size[0] - $row_status_count[0];
                        if (($ava > 1) || (empty($row_status_size[0]))) {
                            $status = "Not Confirmed";
                        } else {
                            $status = "Confirmed";
                            $query_status_id1 = "SELECT registrationId FROM useraccount WHERE groupId = '$GroupId'";
                            $result_status_id1 = mysqli_query($con, $query_status_id1) or die ("query is failed" . mysqli_error($con));
                            while ($row_status_id1 = mysqli_fetch_row($result_status_id1)) {
                                $query_status_update_no1 = "UPDATE useraccount SET status = 'Confirmed' WHERE registrationId = '$row_status_id1[0]'";
                                $result_status_update_no1 = mysqli_query($con, $query_status_update_no1) or die ("query is failed" . mysqli_error($con));
                                if (mysqli_affected_rows($con) > 0) {
                                    echo "update successful status";
                                } else {
                                    echo "update failed";
                                }
                            }
                        }
                    }
                } else {
                    $status = "Not Confirmed";
                }

// If there is a group Id with that location and date but already have a full member then create a new group Id for that specific location with null size
                $querys = "Select groupId FROM groupinfo WHERE groupId = '$GroupId'";
                $results = mysqli_query($con, $querys) or die ("query is failed" . mysqli_error($con));
                if ($rows = mysqli_fetch_row($results) == false) {
                    $queryi = "Insert Into groupinfo Values('$GroupId', NULL)";
                    $resulti = mysqli_query($con, $queryi) or die ("query is failed" . mysqli_error($con));
                }

// Generate the registration id using simple math and php uniqid function
                $Id = mt_rand(10, 1000);
                $rand = rand(1, 10);
                $RegistrationId = uniqid($Id, true);
                $queryid = "Select registrationId from useraccount";
                $resultid = mysqli_query($con, $queryid) or die ("query is failed" . mysqli_error($con));
                while ($rowid = mysqli_fetch_row($resultid)) {
                    if ($rowid[0] == $RegistrationId) {
                        $Id = $Id + $rand;
                        $RegistrationId = uniqid($Id, true);
                    }
                }


//Insert the new registration Id with into useraccount also send a notification with registration Id for user use later for login with email
                $query = "Insert Into useraccount(registrationId,email, name, address, interestedvacationplanId, groupId, status) Values('$RegistrationId','$Email','$Name','$Address','$Tourid','$GroupId','$status')";
                $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
                if (mysqli_affected_rows($con) > 0) {
                    echo "<script> alert ('Your registration id is: $RegistrationId ');</script>";
                } else {
                    echo "<script> alert ('You have not inserted any rows');</script>";
                }
            }
        }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Page</title>
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
    <h4><b>Registration Page</b></h4>
    <ul class="nav nav-pills card-header-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="RegistrationPage.php">Registration Page </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="login.php">Log out</a>
        </li>
    </ul>
</div>

<!-- Some select query to display all the user info into a table -->
<?php
$query = "Select * from useraccount";
$result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
echo "<table class=\"table table-bordered\">";
echo "<thead class=\"thead-dark\"><tr><th scope='col'>Registration ID </th><th scope='col'>Name</th><th scope='col'>Email</th><th scope='col'>Address</th><th scope='col'>Tour Id</th><th scope='col'>GroupId</th><th scope='col'>Status</th></tr></thead>";
while (($row = mysqli_fetch_row($result)) == true) {
    echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[2]</td><td>$row[3]</td><td>$row[4]</td><td>$row[5]</td><td>$row[6]</tr>";
}
echo "</table><br><br>";
?>

<?php
$queryg = "Select * from groupinfo";
$resultg = mysqli_query($con, $queryg) or die ("query is failed" . mysqli_error($con));
echo "<table class=\"table table-bordered\">";
echo "<thead class=\"thead-dark\"><tr><th scope='col'>Group ID </th><th scope='col'>Group Size</th></tr></thead>";
while (($rowg = mysqli_fetch_row($resultg)) == true) {
    echo "<tr><td>$rowg[0]</td><td>$rowg[1]</td></tr>";
}
echo "</table><br><br>";
?>

<!-- Some select query to display all the tour into a table -->
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
    <h1>Registration Page</h1>
    <label>Email: </label>
    <input type="email" placeholder="Email" name="email"><br><br>
    <label>Name: </label>
    <input type="text" placeholder="Name" name="name"><br><br>
    <label>Address: </label>
    <input type="text" placeholder="Address" name="address"><br><br>
    <label>Interested Vacation Plan</label>
    <!-- passing value from interestedvacationplan table to registration page -->
    <select name="interested_vacation_plan" required>
        <option value="">None</option>
        <?php
        $query_select_tourId = "SELECT DISTINCT tourName FROM interestedvacationplan";
        $result_select_tourId = mysqli_query($con, $query_select_tourId);
        while ($row = mysqli_fetch_row($result_select_tourId)) {
            echo "<option value=$row[0]> " . $row[0] . "</option>";
        }
        ?>
    </select><br><br>
    <label>Date: </label>
    <input required type="date" name="date" min="<?php echo date('Y-m-d'); ?>"/>
    <input type="submit" class="btn btn-success" value="Insert" name="INSERT">
</form>
</body>
</html>
<?php
//Close connection
mysqli_close($con);

?>

