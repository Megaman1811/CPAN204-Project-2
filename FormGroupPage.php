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
$GroupSize = '';
$GroupId = '';
$Interested_vacation_plan = '';

//Connect to database server
$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");

//When admin use find function to get specific tour id and group id by date and location to be able to form group
if (isset($_POST['FIND'])) {
    $GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
    $GroupSize = mysqli_real_escape_string($con, $_POST['groupSize']);
    $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);

    $query_select_groupId = "SELECT DISTINCT  interestedvacationplanId, groupId FROM useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan'";
    $result_select_groupId = mysqli_query($con, $query_select_groupId) or die ("query is failed" . mysqli_error($con));


}
//When admin use update function to divide group into small group with limit size of people
if (isset($_POST['UPDATE'])) {

    $GroupSize = mysqli_real_escape_string($con, $_POST['groupSize']);
    $GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
    $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
    $OldGroupId = $GroupId;

// Select to see how many member in a specific groupid if the groupsize is smaller than member in that groupid then start divide that group id into many smalls group that have same size with the admin input
    $query_count = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$GroupId'";
    $result_count = mysqli_query($con, $query_count) or die ("query is failed" . mysqli_error($con));
// First we start with not null group id first to be able to fill up all the space available first
    if ($rowcount = mysqli_fetch_row($result_count)) {
        if ($GroupSize != NULL) {
            if ($GroupSize < $rowcount[0]) {
                $MemberMove = $rowcount[0] - $GroupSize;
                $count = $MemberMove;
 // Start selecting the all the group id that have same location and date without the current group because current group need to stay the same with smaller size only after divide
                $query_select_groupId = "SELECT DISTINCT groupId FROM useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan' AND groupId != '$GroupId'";
                $result_select_groupId = mysqli_query($con, $query_select_groupId) or die ("query is failed" . mysqli_error($con));
                while ($rowid = mysqli_fetch_row($result_select_groupId)) {
                    $query_select_groupSize = "SELECT groupSize FROM groupinfo WHERE groupId = '$rowid[0]'";
                    $result_select_groupSize = mysqli_query($con, $query_select_groupSize) or die ("query is failed" . mysqli_error($con));
// Looping to get all the group size that have in every group id if null found then continute checking method for the next group available.
                    if ($rowsize = mysqli_fetch_row($result_select_groupSize)) {
                        if (empty($rowsize[0])) {
                            continue;
                        }

// Counting the member in all the group too check available space by minus the group size with member found in that group we will have available space
                        $query_count_available = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$rowid[0]'";
                        $result_count_available = mysqli_query($con, $query_count_available) or die ("query is failed" . mysqli_error($con));
                        if ($rowcountavailable = mysqli_fetch_row($result_count_available)) {
                            $availablespace = $rowsize[0] - $rowcountavailable[0];
                            if ($availablespace == 0) {
                                continue;
                            }
//If the group is full then we continute with next group id
//This case is for member need to move smaller the availablespace
                            if (($MemberMove <= $availablespace) && ($availablespace >= 0)) {
                                if ($rowcount[0] == $GroupSize) break;
//We start moving member from the original group into a new group with limit member enough in that new group only
                                $query_select_move = "SELECT registrationId FROM useraccount WHERE groupId = '$GroupId' LIMIT $MemberMove";
                                $result_select_move = mysqli_query($con, $query_select_move) or die ("query is failed" . mysqli_error($con));
                                while ($rowmoveid = mysqli_fetch_row($result_select_move)) {
//Each time we move member into a new group we will minus the number of people we need to move feom the begining until the member have to move is equal 0 then break the loop
                                    $query_update = "UPDATE useraccount SET groupId = '$rowid[0]' WHERE registrationId = '$rowmoveid[0]'";
                                    $result_update = mysqli_query($con, $query_update) or die ("query is failed" . mysqli_error($con));
                                    if (mysqli_affected_rows($con) > 0) {
                                        $rowcount[0] = $rowcount[0] - 1;
                                        $count = $count - 1;
                                    }
                                }
                            } else {
// If the member have to move is larger than the available space of the group then we will move enough of member only
                                if ($rowcount[0] == $GroupSize) break;
                                $query_select_move = "SELECT registrationId FROM useraccount WHERE groupId = '$GroupId' LIMIT $availablespace";
                                $result_select_move = mysqli_query($con, $query_select_move) or die ("query is failed" . mysqli_error($con));
                                while ($rowmoveid = mysqli_fetch_row($result_select_move)) {
                                    $query_update = "UPDATE useraccount SET groupId = '$rowid[0]' WHERE registrationId = '$rowmoveid[0]'";
                                    $result_update = mysqli_query($con, $query_update) or die ("query is failed" . mysqli_error($con));
                                    if (mysqli_affected_rows($con) > 0) {
                                        $rowcount[0] = $rowcount[0] - 1;
                                        $count = $count - 1;
                                    }
                                }
                            }
                        }
                    }
                }
// At the end of the loop we are all fill up the available space but still have member left then we can check if there is a null size group with that specific location and date or not
//if we do have the null group then we will move all the member left into that group
                $query_select_groupId_null = "SELECT DISTINCT groupId FROM useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan'  AND groupId != '$GroupId'";
                $result_select_groupId_null = mysqli_query($con, $query_select_groupId_null) or die ("query is failed" . mysqli_error($con));
                while ($row_id_null = mysqli_fetch_row($result_select_groupId_null)) {
                    $query_select_groupSize_null = "SELECT groupSize FROM groupinfo WHERE groupId = '$row_id_null[0]'";
                    $result_select_groupSize_null = mysqli_query($con, $query_select_groupSize_null) or die ("query is failed" . mysqli_error($con));
                    if ($rowsizenull = mysqli_fetch_row($result_select_groupSize_null)) {
                        if (!empty($rowsizenull[0])) {
                            continue;
                        }
                        $query_select_move_null = "SELECT registrationId FROM useraccount WHERE groupId = '$GroupId' LIMIT $count";
                        $result_select_move_null = mysqli_query($con, $query_select_move_null) or die ("query is failed" . mysqli_error($con));
                        while ($rowmoveidnull = mysqli_fetch_row($result_select_move_null)) {
                            $query_update_null = "UPDATE useraccount SET groupId = '$row_id_null[0]' WHERE registrationId = '$rowmoveidnull[0]'";
                            $result_update_null = mysqli_query($con, $query_update_null) or die ("query is failed" . mysqli_error($con));
                            if (mysqli_affected_rows($con) > 0) {
                                $count = $count - 1;
                            }
                        }
                    }
                }

//if there is no null group and no enough space by all the original group id then we start creating a new group id with same size with admin input and insert member into it

                $queryselectall = "SELECT groupId FROM useraccount";
                $resultselectall = mysqli_query($con, $queryselectall) or die ("query is failed" . mysqli_error($con));
                while ($rows = mysqli_fetch_row($resultselectall)) {
                    if ($rows[0] == $GroupId) {
                        $GroupId = $GroupId + 1;
                    }
                }
// We start moving all the member left into those new group just create
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
                            }
                        }
                        echo "<script> alert ('Success inserted');</script>";
                    } else {
                        echo "<script> alert ('You have not inserted any rows');</script>";
                    }
                }
            }
        }
// After moving all the member into that group we start update the user status by check if group size is equal with member in that group id or not
// If it does then status confirmed will be updated into all member in that group, if it does not then not confirmed status will be updated
        $GroupSize = !empty($GroupSize) ? "'$GroupSize'" : "NULL";
        $query_update_group = "Update groupinfo Set groupSize = $GroupSize where groupId = '$OldGroupId'";
        $result_update_group = mysqli_query($con, $query_update_group) or die ("query is failed" . mysqli_error($con));
        if (mysqli_affected_rows($con) > 0) {
            $query_status_id = "SELECT DISTINCT groupId FROM useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan'";
            $result_status_id = mysqli_query($con, $query_status_id) or die ("query is failed" . mysqli_error($con));
            while ($row_status_id = mysqli_fetch_row($result_status_id)) {
                $query_status_size = "SELECT groupSize FROM groupinfo WHERE groupId = '$row_status_id[0]'";
                $result_status_size = mysqli_query($con, $query_status_size) or die ("query is failed" . mysqli_error($con));
                if ($row_status_size = mysqli_fetch_row($result_status_size)) {
                    $query_status_count = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$row_status_id[0]'";
                    $result_status_count = mysqli_query($con, $query_status_count) or die ("query is failed" . mysqli_error($con));
                    if ($row_status_count = mysqli_fetch_row($result_status_count)) {
                        $ava = $row_status_size[0] - $row_status_count[0];
                        if (($ava > 0) || empty($row_status_size[0])) {
                            $query_status_update_no = "UPDATE useraccount SET status = 'Not Confirmed' WHERE groupId = '$row_status_id[0]'";
                            $result_status_update_no = mysqli_query($con, $query_status_update_no) or die ("query is failed" . mysqli_error($con));
                        } else {
                            $query_status_update_yes = "UPDATE useraccount SET status = 'Confirmed' WHERE groupId = '$row_status_id[0]'";
                            $result_status_update_yes = mysqli_query($con, $query_status_update_yes) or die ("query is failed" . mysqli_error($con));
                        }
                    }
                }
            }

            $query_selectall_samepd = "Select * from useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan'";
            $result_selectall_samepd = mysqli_query($con, $query_selectall_samepd) or die ("query is failed" . mysqli_error($con));

//Select statement to display the tour name and date in interestedvationplan table with same tour id into a table
            $query_location = "Select tourName, date from interestedvacationplan where tourId = '$Interested_vacation_plan'";
            $result_location = mysqli_query($con, $query_location) or die ("query is failed" . mysqli_error($con));
            if (($row_location = mysqli_fetch_row($result_location)) == true) {
                echo "<H2> $row_location[0] ------- $row_location[1] </H2>";
            }

            echo "<table class=\"table table-bordered\">";
            echo "<thead class=\"thead-dark\"><tr><th scope='col'>Registration ID </th><th scope='col'>Email</th><th scope='col'>Tour Id</th><th scope='col'>GroupId</th><th scope='col'>Status</th></tr></thead>";
            while (($row = mysqli_fetch_row($result_selectall_samepd)) == true) {
                echo "<tr><td>$row[0]</td><td>$row[2]</td><td>$row[4]</td><td>$row[5]</td><td>$row[6]</td></tr>";
            }
            echo "</table><br><br>";
            $alertupdated = "You have updated " . mysqli_affected_rows($con) . " row";
            echo "<script> alert ('$alertupdated');</script>";
        } else {
            echo "<script> alert('You have not updated any rows');</script>";
        }

    }

$queryg = "Select * from useraccount where groupId = '$GroupId'";
$resultg = mysqli_query($con, $queryg) or die ("query is failed" . mysqli_error($con));
echo "<table class=\"table table-bordered\">";
echo "<thead class=\"thead-dark\"><tr><th scope='col'>Registration ID </th><th scope='col'>Email</th><th scope='col'>Tour Id</th><th scope='col'>GroupId</th><th scope='col'>Status</th></tr></thead>";
while (($rowg = mysqli_fetch_row($resultg)) == true) {
    echo "<tr><td>$rowg[0]</td><td>$rowg[2]</td><td>$rowg[4]</td><td>$rowg[5]</td><td>$rowg[6]</td></tr>";
}
echo "</table><br><br>";




 // Make the input box clear after update
    $GroupSize = '';
    $GroupId = '';
    $Interested_vacation_plan = '';

}
// When admin want to group up all the group id into 1 group id with same location and date
if (isset($_POST['GROUPUP'])) {
    $GroupSize = mysqli_real_escape_string($con, $_POST['groupSize']);
    $GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
    $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);

// First we have to set the current groupSize into null to be able to move all member from the other group into current group cause we dont know how many member are there
    $queryuc = "Update groupinfo set groupSize = NULL WHERE groupid = '$GroupId'";
    $resultuc = mysqli_query($con, $queryuc) or die ("query is failed" . mysqli_error($con));
// We start check all the group id that have same location and date and start to loop
    $query = "SELECT groupId from useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan' AND groupId != '$GroupId'";
    $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));
// Select all member in those group id and start moving 1 by 1
    while ($row = mysqli_fetch_row($result)) {
        $querymember = "SELECT registrationId from useraccount WHERE groupId = '$row[0]'";
        $resultmember = mysqli_query($con, $querymember) or die ("query is failed" . mysqli_error($con));
        while ($rowmember = mysqli_fetch_row($resultmember)) {
            $queryug = "Update useraccount set groupId ='$GroupId' WHERE registrationId = '$rowmember[0]'";
            $resultug = mysqli_query($con, $queryug) or die ("query is failed" . mysqli_error($con));
        }
//After we are moving all member in specific group into new group we will delete that groupid to have more space
        $querydel = "Delete from groupinfo where groupId = '$row[0]'";
        $resultdel = mysqli_query($con, $querydel) or die ("query is failed" . mysqli_error($con));

    }
//Setting the status of all member in new group because the group size is null then status should be Not Confirmed.
    $querymemberc = "SELECT registrationId from useraccount WHERE groupId = '$GroupId'";
    $resultmemberc = mysqli_query($con, $querymemberc) or die ("query is failed" . mysqli_error($con));
    while ($rowmemberc = mysqli_fetch_row($resultmemberc)) {
        $queryus = "Update useraccount set status ='Not Confirmed' WHERE registrationId = '$rowmemberc[0]'";
        $resultus = mysqli_query($con, $queryus) or die ("query is failed" . mysqli_error($con));
    }

    
    $queryg = "Select * from useraccount where groupId = '$GroupId'";
    $resultg = mysqli_query($con, $queryg) or die ("query is failed" . mysqli_error($con));
    echo "<table class=\"table table-bordered\">";
    echo "<thead class=\"thead-dark\"><tr><th scope='col'>Registration ID </th><th scope='col'>Email</th><th scope='col'>Tour Id</th><th scope='col'>GroupId</th><th scope='col'>Status</th></tr></thead>";
    while (($rowg = mysqli_fetch_row($resultg)) == true) {
        echo "<tr><td>$rowg[0]</td><td>$rowg[2]</td><td>$rowg[4]</td><td>$rowg[5]</td><td>$rowg[6]</td></tr>";
    }
    echo "</table><br><br>";


//Clear all the input box after group
    $GroupSize = '';
    $GroupId = '';
    $Interested_vacation_plan = '';

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Group Managerment</title>
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
            <a class="nav-link" href="AdminPage.php">Admin Page </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="UserInfoPageAdmin.php">User Information Management </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="FormGroupPage.php">Group Management </a>
        </li>
        <li class="nav-item">
            <a class="nav-link " href="TourManagementPage.php">Group Management</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="login.php">Log out</a>
        </li>
    </ul>
</div>
<?php
// Retrieved all user account
$query_selectall_useraccount = "Select * from useraccount ";
$result_selectall_useraccount = mysqli_query($con, $query_selectall_useraccount) or die ("query is failed" . mysqli_error($con));
echo "<table class=\"table table-bordered\">";
echo "<thead class=\"thead-dark\"><tr><th scope='col'>Registration ID </th><th scope='col'>Email</th><th scope='col'>Tour Id</th><th scope='col'>GroupId</th><th scope='col'>Status</th></tr></thead>";
while (($row = mysqli_fetch_row($result_selectall_useraccount)) == true) {
    echo "<tr><td>$row[0]</td><td>$row[2]</td><td>$row[4]</td><td>$row[5]</td><td>$row[6]</td></tr>";
}
echo "</table><br><br>";
?>
<form method="post" align="center">
    <label>Interested Vacation Plan</label>
    <select name="interested_vacation_plan" required>
<!-- drop down list to show all information of the specific tour -->
        <option value="">None</option>
        <?php

        $query_select_tourId = "SELECT * FROM interestedvacationplan";
        $result_select_tourId = mysqli_query($con, $query_select_tourId) or die ("query is failed" . mysqli_error($con));
        while ($row = mysqli_fetch_row($result_select_tourId)) {
            if ($Interested_vacation_plan == $row[0])
                echo "<option selected value=$row[0]> " . $row[0] . " | " . $row[1] . " -- " . $row[2] . "</option>";
            else {
                echo "<option value=$row[0]> " . $row[0] . " | " . $row[1] . " -- " . $row[2] . "</option>";
            }
        }
        ?>
    </select><br><br>
    <label>Group ID:</label>
    <select name="groupId">
        <option value="">None</option>
        <?php
// find function to find and display group id and group size in same column in a dropdownlist .
        if (isset($_POST['FIND'])) {
            $GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
            $GroupSize = mysqli_real_escape_string($con, $_POST['groupSize']);
            $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);


            while (($rowid = mysqli_fetch_row($result_select_groupId)) == true) {
                $query_select_groupSize = "SELECT groupSize FROM groupinfo WHERE groupId = '$rowid[1]'";
                $result_select_groupSize = mysqli_query($con, $query_select_groupSize) or die ("query is failed" . mysqli_error($con));
                if ($rowsize = mysqli_fetch_row($result_select_groupSize)) {
                    $Interested_vacation_plan = $rowid[0];
                    $GroupId = $rowid[1];
                    $GroupSize = $rowsize[0];
                    if ($GroupId == $rowid[1]) {
                        echo "<option selected value=$rowid[1]> " . $rowid[1] . "</option>";
                    } else {
                        echo "<option value=$rowid[1]> " . $rowid[1] . "</option>";
                    }
                }
            }

        }
        ?>
    </select><br><br>
    <label>Group Size: </label>
    <input type="text" placeholder="Group Size" name="groupSize" value="<?php echo $GroupSize; ?>"><br><br>
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
