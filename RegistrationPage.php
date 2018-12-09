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
    $Type = "guest";

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


            echo "same tour location $row[0] <br>";

            $querya = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$row[0]'";
            $resulta = mysqli_query($con, $querya) or die ("query is failed" . mysqli_error($con));


            $queryb = "SELECT groupSize FROM groupinfo WHERE groupId = '$row[0]'";
            $resultb = mysqli_query($con, $queryb) or die ("query is failed" . mysqli_error($con));
            if ($rowa = mysqli_fetch_row($resulta)) {

                echo "member $rowa[0] <br>";
                if ($rowb = mysqli_fetch_row($resultb)) {
                    echo "size $rowb[0] <br>";
                    if ((($rowa[0] < $rowb[0]) && ($rowb[0] != NULL))) {

                        $GroupId = $row[0];
                        echo "tel: $GroupId";
                        break;
                    } else if ((empty($rowb[0]))) {
                        $GroupId = $row[0];
                    } else {
                        echo "else $GroupId <br>";
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
    echo "Final $GroupId <br>";


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
    }
    else {
        $status = "Not Confirmed";
    }
    echo $status;
    echo $GroupId;


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


