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


$host = "localhost";
$user = "root";
$password = "";
$dbName = "travel_project";
$GroupId = '';
$GroupSize = '';
$Interested_vacation_plan = '';
$Date = '';

$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");


if (isset($_POST['FIND'])) {
    $GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
    $GroupSize = mysqli_real_escape_string($con, $_POST['groupSize']);
    $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
    $Date = mysqli_real_escape_string($con, $_POST['date']);


    $query_select_groupId = "SELECT DISTINCT groupId FROM useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan' AND date = '$Date'";
    $result_select_groupId = mysqli_query($con, $query_select_groupId) or die ("query is failed" . mysqli_error($con));

}

if (isset($_POST['UPDATE'])) {

    $GroupSize = mysqli_real_escape_string($con, $_POST['groupSize']);
    $GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
    $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
    $Date = mysqli_real_escape_string($con, $_POST['date']);
    $OldGroupId = $GroupId;


    $query_count = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$GroupId'";
    $result_count = mysqli_query($con, $query_count) or die ("query is failed" . mysqli_error($con));

    if ($rowcount = mysqli_fetch_row($result_count)) {
        if ($GroupSize != NULL) {
            if ($GroupSize < $rowcount[0]) {
                $MemberMove = $rowcount[0] - $GroupSize;
                $count = $MemberMove;
                $query_select_groupId = "SELECT DISTINCT groupId FROM useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan' AND date = '$Date' AND groupId != '$GroupId'";
                $result_select_groupId = mysqli_query($con, $query_select_groupId) or die ("query is failed" . mysqli_error($con));
                while ($rowid = mysqli_fetch_row($result_select_groupId)) {
                    $query_select_groupSize = "SELECT groupSize FROM groupinfo WHERE groupId = '$rowid[0]'";
                    $result_select_groupSize = mysqli_query($con, $query_select_groupSize) or die ("query is failed" . mysqli_error($con));
                    if ($rowsize = mysqli_fetch_row($result_select_groupSize)) {
                        if ($rowsize[0] == NULL) {
                            continue;
                        }
                        $query_count_available = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$rowid[0]'";
                        $result_count_available = mysqli_query($con, $query_count_available) or die ("query is failed" . mysqli_error($con));
                        if ($rowcountavailable = mysqli_fetch_row($result_count_available)) {
                            $availablespace = $rowsize[0] - $rowcountavailable[0];
                            if ($availablespace == 0) {
                                continue;
                            }
                            if (($MemberMove < $availablespace) && ($availablespace >= 0)) {
                                if ($rowcount[0] == $GroupSize) break;
                                $query_select_move = "SELECT registrationId FROM useraccount WHERE groupId = '$GroupId' LIMIT $MemberMove";
                                $result_select_move = mysqli_query($con, $query_select_move) or die ("query is failed" . mysqli_error($con));
                                while ($rowmoveid = mysqli_fetch_row($result_select_move)) {

                                    $query_update = "UPDATE useraccount SET groupId = '$rowid[0]' WHERE registrationId = '$rowmoveid[0]'";
                                    $result_update = mysqli_query($con, $query_update) or die ("query is failed" . mysqli_error($con));
                                    if (mysqli_affected_rows($con) > 0) {
                                        $rowcount[0] = $rowcount[0] - 1;
                                        $count = $count - 1;
                                        $alertupdated = "You have updated tt " . mysqli_affected_rows($con) . " row";
                                        echo "<script> alert ('$alertupdated');</script>";
                                    } else {
                                        echo "<script> alert('You have not updated any rows tt');</script>";
                                    }
                                }
                            } else {
                                if ($rowcount[0] == $GroupSize) break;
                                $query_select_move = "SELECT registrationId FROM useraccount WHERE groupId = '$GroupId' LIMIT $availablespace";
                                $result_select_move = mysqli_query($con, $query_select_move) or die ("query is failed" . mysqli_error($con));
                                while ($rowmoveid = mysqli_fetch_row($result_select_move)) {
                                    $query_update = "UPDATE useraccount SET groupId = '$rowid[0]' WHERE registrationId = '$rowmoveid[0]'";
                                    $result_update = mysqli_query($con, $query_update) or die ("query is failed" . mysqli_error($con));
                                    if (mysqli_affected_rows($con) > 0) {
                                        echo "before2 $rowcount[0] <br><br>";
                                        $rowcount[0] = $rowcount[0] - 1;
                                        $alertupdated = "You have updated  " . mysqli_affected_rows($con) . " row";
                                        echo "<script> alert ('$alertupdated');</script>";
                                    } else {
                                        echo "<script> alert('You have not updated any rows ');</script>";
                                    }
                                }
                            }
                        }
                    }
                }
                $query_select_groupId_null = "SELECT DISTINCT groupId FROM useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan' AND date = '$Date' AND groupId != '$GroupId'";
                $result_select_groupId_null = mysqli_query($con, $query_select_groupId_null) or die ("query is failed" . mysqli_error($con));
                while ($row_id_null = mysqli_fetch_row($result_select_groupId_null)) {
                    $query_select_groupSize_null = "SELECT groupSize FROM groupinfo WHERE groupId = '$row_id_null[0]'";
                    $result_select_groupSize_null = mysqli_query($con, $query_select_groupSize_null) or die ("query is failed" . mysqli_error($con));
                    if ($rowsizenull = mysqli_fetch_row($result_select_groupSize_null)) {
                            if (!empty($rowsizenull[0])) {
                                continue;
                            }
                            $query_id = "SELECT groupId FROM groupinfo WHERE groupId != '$GroupId' AND groupSize IS NULL ";
                            $result_id = mysqli_query($con, $query_id) or die ("query is failed" . mysqli_error($con));
                            if ($row_idnull = mysqli_fetch_row($result_id)) {
                                $query_select_move_null = "SELECT registrationId FROM useraccount WHERE groupId = '$GroupId' LIMIT $count";
                                $result_select_move_null = mysqli_query($con, $query_select_move_null) or die ("query is failed" . mysqli_error($con));
                                while ($rowmoveidnull = mysqli_fetch_row($result_select_move_null)) {
                                    $query_update_null = "UPDATE useraccount SET groupId = '$row_idnull[0]' WHERE registrationId = '$rowmoveidnull[0]'";
                                    $result_update_null = mysqli_query($con, $query_update_null) or die ("query is failed" . mysqli_error($con));
                                    if (mysqli_affected_rows($con) > 0) {
                                        $count = $count - 1;
                                        $alertupdated = "You have updated tt " . mysqli_affected_rows($con) . " row";
                                        echo "<script> alert ('$alertupdated');</script>";
                                    } else {
                                        echo "<script> alert('You have not updated any rows ');</script>";
                                    }
                                }
                        }
                    }
                }
                $queryselectall = "SELECT groupId FROM useraccount";
                $resultselectall = mysqli_query($con, $queryselectall) or die ("query is failed" . mysqli_error($con));
                while ($rows = mysqli_fetch_row($resultselectall)) {
                    if ($rows[0] == $GroupId) {
                        $GroupId = $GroupId + 1;
                    }
                }
                while ($count != 0) {
                    $query_insert = "Insert Into groupinfo Values('$GroupId', '$GroupSize')";
                    $result_insert = mysqli_query($con, $query_insert) or die ("query is failed" . mysqli_error($con));
                    if (mysqli_affected_rows($con) > 0) {
                        $query_select_move1 = "SELECT registrationId FROM useraccount WHERE groupId = '$OldGroupId' LIMIT $GroupSize";
                        $result_select_move1 = mysqli_query($con, $query_select_move1) or die ("query is failed" . mysqli_error($con));
                        while (($rowmoveid1 = mysqli_fetch_row($result_select_move1)) && ($count != 0)) {
                            if ($rowmoveid1[0] == NULL) {
                                echo "NULL";
                            }
                            $query_update1 = "UPDATE useraccount SET groupId = '$GroupId' WHERE registrationId = '$rowmoveid1[0]'";
                            $result_update1 = mysqli_query($con, $query_update1) or die ("query is failed" . mysqli_error($con));
                            if (mysqli_affected_rows($con) > 0) {
                                $rowcount[0] = $rowcount[0] - 1;
                                $count = $count - 1;
                                $query_count_available1 = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$GroupId'";
                                $result_count_available1 = mysqli_query($con, $query_count_available1) or die ("query is failed" . mysqli_error($con));
                                if ($rowavailable1 = mysqli_fetch_row($result_count_available1)) {
                                    if ($rowavailable1[0] == $GroupSize) {
                                        $GroupId = $GroupId + 1;
                                    }
                                }
                                $alertupdated = "You have updated last " . mysqli_affected_rows($con) . " row";
                                echo "<script> alert ('$alertupdated');</script>";
                            } else {
                                echo "<script> alert('You have not updated any rows last');</script>";
                            }
                        }
                        echo "<script> alert ('Success inserted');</script>";
                    } else {
                        echo "<script> alert ('You have not inserted any rows');</script>";
                    }
                }
            } $GroupSize = !empty($GroupSize) ? "'$GroupSize'" : "NULL";
            $query_update_group = "Update groupinfo Set groupSize = $GroupSize where groupId = '$OldGroupId'";
            $result_update_group = mysqli_query($con, $query_update_group) or die ("query is failed" . mysqli_error($con));
            if (mysqli_affected_rows($con) > 0) {
                $query_status_id =  "SELECT DISTINCT groupId FROM useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan' AND date = '$Date' ";
                $result_status_id = mysqli_query($con, $query_status_id) or die ("query is failed" . mysqli_error($con));
                while ($row_status_id = mysqli_fetch_row($result_status_id)){
                    $query_status_size = "SELECT groupSize FROM groupinfo WHERE groupId = '$row_status_id[0]'";
                    $result_status_size = mysqli_query($con, $query_status_size) or die ("query is failed" .  mysqli_error($con));
                    if ($row_status_size = mysqli_fetch_row($result_status_size)) {
                        $query_status_count = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$row_status_id[0]'";
                        $result_status_count = mysqli_query($con, $query_status_count) or die ("query is failed" . mysqli_error($con));
                        if ($row_status_count = mysqli_fetch_row($result_status_count)) {
                            $ava = $row_status_size[0] - $row_status_count[0];
                            if ($ava > 0){
                                $query_status_update_no = "UPDATE useraccount SET status = 'Not Confirmed' WHERE groupId = '$row_status_id[0]'";
                                $result_status_update_no = mysqli_query($con, $query_status_update_no) or die ("query is failed" . mysqli_error($con));
                            }
                            else {
                                $query_status_update_yes = "UPDATE useraccount SET status = 'Confirmed' WHERE groupId = '$row_status_id[0]'";
                                $result_status_update_yes = mysqli_query($con, $query_status_update_yes) or die ("query is failed" . mysqli_error($con));
                            }
                        }
                    }
                }

                $query_selectall_samepd = "Select * from useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan' AND date = '$Date' ";
                $result_selectall_samepd = mysqli_query($con, $query_selectall_samepd) or die ("query is failed" . mysqli_error($con));

                $query_location = "Select tourName from interestedvacationplan where tourId = '$Interested_vacation_plan'";
                $result_location = mysqli_query($con, $query_location) or die ("query is failed" . mysqli_error($con));
                if (($row_location = mysqli_fetch_row($result_location)) == true) {
                    echo "<H2> $row_location[0] ------- $Date </H2>";
                }

                echo "<table class=\"table table-bordered\">";
                echo "<thead class=\"thead-dark\"><tr><th scope='col'>Registration ID </th><th scope='col'>Email</th><th scope='col'>Tour Id</th><th scope='col'>Date</th><th scope='col'>GroupId</th><th scope='col'>Status</th></tr></thead>";
                while (($row = mysqli_fetch_row($result_selectall_samepd)) == true) {
                    echo "<tr><td>$row[0]</td><td>$row[2]</td><td>$row[4]</td><td>$row[5]</td><td>$row[6]</td><td>$row[7]</td></tr>";
                }
                echo "</table><br><br>";

                $alertupdated = "You have updated " . mysqli_affected_rows($con) . " row";
                echo "<script> alert ('$alertupdated');</script>";
            } else {
                echo "<script> alert('You have not updated any rows');</script>";
            }
        }
    }
}

if (isset($_POST['GROUPUP'])) {
    $GroupSize = mysqli_real_escape_string($con, $_POST['groupSize']);
    $GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
    $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
    $Date = mysqli_real_escape_string($con, $_POST['date']);



    $query = "SELECT registrationId from useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan' AND date = '$Date'";
    $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
    while ($row = mysqli_fetch_row($result)) {
        $query_gid = "SELECT groupId from useraccount WHERE registrationId = '$row[0]'";
        $result_gid = mysqli_query($con, $query_gid) or die ("query is failed" . mysqli_error($con));
        if ($row_gid = mysqli_fetch_row($result_gid)) {
            $queryuc = "Update groupinfo set groupSize = 100 WHERE groupid = '$GroupId'";
            $resultuc = mysqli_query($con, $queryuc) or die ("query is failed" . mysqli_error($con));
            if (mysqli_affected_rows($con) > 0) {
                $queryug = "Update useraccount set groupId ='$GroupId' WHERE registrationId = '$row[0]'";
                $resultug = mysqli_query($con, $queryug) or die ("query is failed" . mysqli_error($con));
                if (mysqli_affected_rows($con) > 0) {
                    $alertupdated = "You have updated " . mysqli_affected_rows($con) . "groupid row";
                    echo "<script> alert ('$alertupdated');</script>";
                    $querydel = "Delete from groupinfo where groupId = '$row_gid[0]'";
                    $resultdel = mysqli_query($con, $querydel) or die ("query is failed" . mysqli_error($con));
                    if (mysqli_affected_rows($con) > 0) {
                        $alert = "You have deleted " . mysqli_affected_rows($con) . " row";
                        echo "<script> alert ('$alert');</script>";
                    } else {
                        echo "<script> alert ('Record not found !! Delete failed');</script>";
                    }

                } else {
                    echo "<script> alert('You have not updated any rows tt');</script>";
                }

                $alertupdated = "You have updated " . mysqli_affected_rows($con) . "size row";
                echo "<script> alert ('$alertupdated');</script>";
            } else {
                echo "<script> alert('You have not updated any rows tt');</script>";
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
    <h4><b>Group Management</b></h4>
    <ul class="nav nav-pills card-header-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="FormGroupPage.php">Group Management </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="UserInfoPageAdmin.php">User Information Management </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="AdminPage.php">Admin Page </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="Login.php">Log out</a>
        </li>
    </ul>
</div>
<?php
// Retrieved all user account
$query_selectall_useraccount = "Select * from useraccount ";
$result_selectall_useraccount = mysqli_query($con, $query_selectall_useraccount) or die ("query is failed" . mysqli_error($con));
echo "<table class=\"table table-bordered\">";
echo "<thead class=\"thead-dark\"><tr><th scope='col'>Registration ID </th><th scope='col'>Email</th><th scope='col'>Tour Id</th><th scope='col'>Date</th><th scope='col'>GroupId</th><th scope='col'>Status</th></tr></thead>";
while (($row = mysqli_fetch_row($result_selectall_useraccount)) == true) {
    echo "<tr><td>$row[0]</td><td>$row[2]</td><td>$row[4]</td><td>$row[5]</td><td>$row[6]</td><td>$row[7]</td></tr>";
}
echo "</table><br><br>";
?>
<form method="post" align="center">
    <label>Interested Vacation Plan</label>
    <select name="interested_vacation_plan" required>
        <option value="">None</option>
        <?php
        $query_select_tourId = "SELECT * FROM interestedvacationplan";
        $result_select_tourId = mysqli_query($con, $query_select_tourId) or die ("query is failed" . mysqli_error($con));
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
    <select name="date" required>
        <option value="">None</option>
        <?php
        $query_select_date = "SELECT DISTINCT date FROM useraccount";
        $result_select_date = mysqli_query($con, $query_select_date) or die ("query is failed" . mysqli_error($con));
        while ($row = mysqli_fetch_row($result_select_date)) {
            if ($Date == $row[0]) {
                echo "<option selected value=$row[0]> " . $row[0] . "</option>";
            } else {
                echo "<option value=$row[0]> " . $row[0] . "</option>";
            }
        }
        ?>
    </select><br><br>
    <label>Group ID:</label>
    <select name="groupId" >
        <option value="">None</option>
        <?php
        if (isset($_POST['FIND'])) {
            $GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
            $GroupSize = mysqli_real_escape_string($con, $_POST['groupSize']);
            $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
            $Date = mysqli_real_escape_string($con, $_POST['date']);


            $query_select_groupId = "SELECT DISTINCT groupId FROM useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan' AND date = '$Date'";
            $result_select_groupId = mysqli_query($con, $query_select_groupId) or die ("query is failed" . mysqli_error($con));

            while ($rowid = mysqli_fetch_row($result_select_groupId)) {
                $query_select_groupSize = "SELECT groupSize FROM groupinfo WHERE groupId = '$rowid[0]'";
                $result_select_groupSize = mysqli_query($con, $query_select_groupSize) or die ("query is failed" . mysqli_error($con));
                if ($rowsize = mysqli_fetch_row($result_select_groupSize)) {
                    echo "<option value=$rowid[0]> " . $rowid[0] . " Size " . $rowsize[0] . "</option>";
                }

            }
        }
        ?>
    </select><br><br>
    <label>Group Size: </label>
    <input type="text" placeholder="Group Size" name="groupSize" value=""><br><br>


    <input type="submit" class="btn btn-warning" value="Find" name="FIND"/>
    <input type="submit" class="btn btn-success" value="Update" name="UPDATE"/>
    <input type="submit" class="btn btn-danger" value="GroupUP" name="GROUPUP"/>
</form>

</body>
</html>
<?php
//Close connection
mysqli_close($con);
?>
