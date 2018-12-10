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
$status = '';

//Connect to server + select DB
$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");
// When user used insert function
if (isset($_POST['INSERT'])) {
    $Email = mysqli_real_escape_string($con, $_POST['email']);
    $Name = mysqli_real_escape_string($con, $_POST['name']);
    $Address = mysqli_real_escape_string($con, $_POST['address']);
    $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
    $Date = mysqli_real_escape_string($con, $_POST['date']);


    $querycheck = "SELECT interestedvacationplanId, date FROM useraccount WHERE email= '$Email'";
    $resultcheck = mysqli_query($con, $querycheck) or die ("query is failed" . mysqli_error($con));
    if ($rowcheck = mysqli_fetch_row($resultcheck)) {
        if(($rowcheck[0] == $Interested_vacation_plan)&& ($rowcheck[1] == $Date))
        {
            echo "<script> alert ('You already registered for this trip on this day');</script>";
        }
    } else {


        $queryselect = "SELECT DISTINCT groupId FROM useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan' AND date = '$Date'";
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


                $querya = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$row[0]'";
                $resulta = mysqli_query($con, $querya) or die ("query is failed" . mysqli_error($con));


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


            } while ($row = mysqli_fetch_row($resultselect));

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


        $querys = "Select groupId FROM groupinfo WHERE groupId = '$GroupId'";
        $results = mysqli_query($con, $querys) or die ("query is failed" . mysqli_error($con));
        if ($rows = mysqli_fetch_row($results) == false) {
            $queryi = "Insert Into groupinfo Values('$GroupId', NULL)";
            $resulti = mysqli_query($con, $queryi) or die ("query is failed" . mysqli_error($con));
        }
        $query = "Insert Into useraccount(email, name, address, interestedvacationplanId, groupId, date,status) Values('$Email','$Name','$Address','$Interested_vacation_plan','$GroupId','$Date','$status')";
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

}


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
    <h4><b>Registration Page</b></h4>
    <ul class="nav nav-pills card-header-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="RegistrationPage.php">Registration Page </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="Login.php">Log out</a>
        </li>
    </ul>
</div>
<form method="post" align="center">
    <h1>Registration Page</h1>
    <label>Email: </label>
    <input type="text" placeholder="Email" name="email"><br><br>
    <label>Name: </label>
    <input type="text" placeholder="Name" name="name"><br><br>
    <label>Address: </label>
    <input type="text" placeholder="Address" name="address"><br><br>
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
    <input type="submit" class="btn btn-success" value="Insert" name="INSERT">

</form>
</body>
</html>
<?php
//Close connection
mysqli_close($con);
?>

