<?php
$host = "localhost";
$user = "root";
$password = "";
$dbName = "travel_project";

$GroupId = '';
$GroupSize = '';
$MembersName = '';
$Interested_vacation_plan = '';
$Date = '';

$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");


if (isset($_POST['FIND'])) {
    $GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
    $GroupSize = mysqli_real_escape_string($con, $_POST['groupSize']);
    $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
    $Date = mysqli_real_escape_string($con, $_POST['date']);


    $query = "Select interestedvacationplanId, date, email from useraccount where groupId = '$GroupId'";
    $result = mysqli_query($con, $query) or die ("query is failed" . mysqli_error($con));

    $queryselect = "SELECT groupSize from groupinfo where groupId = '$GroupId'";
    $resultselect = mysqli_query($con, $queryselect) or die ("query is failed" . mysqli_error($con));


    if ($row = mysqli_fetch_row($result)) {
        $Interested_vacation_plan = $row[0];
        $Date = $row[1];
        $MembersName = $row[2];
    } else {
        echo "<script> alert('Please fill up Incident Group ID field to search');</script> ";
    }

    if ($row = mysqli_fetch_row($resultselect)) {

        $GroupSize = $row[0];
    } else {
        echo "<script> alert('Please fill up Incident Group ID field to search');</script> ";
    }


}

if (isset($_POST['UPDATE'])) {

    $GroupSize = mysqli_real_escape_string($con, $_POST['groupSize']);
    $GroupId = mysqli_real_escape_string($con, $_POST['groupId']);
    $Interested_vacation_plan = mysqli_real_escape_string($con, $_POST['interested_vacation_plan']);
    $Date = mysqli_real_escape_string($con, $_POST['date']);
    $OldGroupId = $GroupId;


    $querya = "SELECT COUNT(email) FROM useraccount WHERE groupId = '$GroupId'";
    $resulta = mysqli_query($con, $querya) or die ("query is failed" . mysqli_error($con));

    if ($rowa = mysqli_fetch_row($resulta)) {
        if (($GroupSize < $rowa[0]) && ($GroupSize != NULL)) {
            $queryselect = "SELECT DISTINCT groupId FROM useraccount WHERE interestedvacationplanId = '$Interested_vacation_plan' AND date = '$Date'";
            $resultselect = mysqli_query($con, $queryselect) or die ("query is failed" . mysqli_error($con));

            while (($rowexist = mysqli_fetch_row($resultselect)) == true) {
                echo "Group exist: $rowexist[0] <br><br>";


                $querysize = "SELECT groupSize FROM groupinfo WHERE groupId = '$rowexist[0]'";
                $resultsize = mysqli_query($con, $querysize) or die ("query is failed" . mysqli_error($con));

                $querycheckcount = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$rowexist[0]'";
                $resultcheckcount = mysqli_query($con, $querycheckcount) or die ("query is failed" . mysqli_error($con));

                if ($rowcount = mysqli_fetch_row($resultcheckcount)) {
                    echo "count numm: $rowcount[0] <br><br>";
                    if ($rowsize = mysqli_fetch_row($resultsize)) {
                        echo "size: $rowsize[0] <br><br>";
                        if (($rowcount[0] > $rowsize[0]) && ($rowsize[0] != NULL)) {

                            $GroupId = $rowexist[0];

                        } else if (($rowsize[0] == NULL)) {
                            $GroupId = $rowexist[0];


                        } else {
                            $querycheckcount2 = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$GroupId'";
                            $resultcheckcount2 = mysqli_query($con, $querycheckcount2) or die ("query is failed" . mysqli_error($con));
                            if ($rowcount2 = mysqli_fetch_row($resultcheckcount2)) {
                                if ($rowcount2[0] > $rowcount[0]) {
                                    $GroupId = $rowexist[0];
                                }
                            }

                        }


                    }


                }


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
            echo "Final try $GroupId";

            echo $rowa[0];
            echo $GroupSize;

            echo "count: $rowa[0] <br>";
            $MemberMove = $rowa[0] - $GroupSize;
            echo "Moving member $MemberMove <br>";

            $querys = "SELECT registrationId FROM useraccount WHERE groupId = '$OldGroupId' LIMIT $MemberMove";
            $selects = mysqli_query($con, $querys) or die ("query is failed blah blah" . mysqli_error($con));
            while ($row = mysqli_fetch_row($selects)) {
                $queryu = "UPDATE useraccount SET groupId = '$GroupId' WHERE registrationId = '$row[0]'";
                $resultu = mysqli_query($con, $queryu) or die ("query is failed" . mysqli_error($con));
                if (mysqli_affected_rows($con) > 0) {

                    echo "test Group Id: $GroupId";
                    echo "test Registration ID $row[0]";
                    $alertupdated = "You have updated tt " . mysqli_affected_rows($con) . " row";
                    echo "<script> alert ('$alertupdated');</script>";
                } else {
                    echo "<script> alert('You have not updated any rows tt');</script>";
                }


                $queryselects = "SELECT groupSize from groupinfo where groupId = '$GroupId'";
                $resultselects = mysqli_query($con, $queryselects) or die ("query is failed" . mysqli_error($con));

                if ($rowsl = mysqli_fetch_row($resultselects) == true) {
                    $querycheckcount3 = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$row[0]'";
                    $resultcheckcount3 = mysqli_query($con, $querycheckcount3) or die ("query is failed" . mysqli_error($con));
                    if ($rowcount3 = mysqli_fetch_row($resultcheckcount3)) {
                        if ($rowcount3[0] < $rowsl[0]) {

                        }

                    }


                }



            }







        }

    }

    /* echo "count: $rowa[0] <br>";
     $MemberMove = $rowa[0] - $GroupSize;
     echo "Moving member $MemberMove <br>";

     $querys = "SELECT registrationId FROM useraccount WHERE groupId = '$OldGroupId' LIMIT $MemberMove";
     $selects = mysqli_query($con, $querys) or die ("query is failed blah blah" . mysqli_error($con));
     while ($row = mysqli_fetch_row($selects)) {
         echo $row[0];
         $queryu = "UPDATE useraccount SET groupId = '$GroupId' WHERE registrationId = '$row[0]'";
         $resultu = mysqli_query($con, $queryu) or die ("query is failed" . mysqli_error($con));
         if (mysqli_affected_rows($con) > 0) {
             echo "test Group Id: $GroupId";
             echo "test Registration ID $row[0]";
             $alertupdated = "You have updated tt " . mysqli_affected_rows($con) . " row";
             echo "<script> alert ('$alertupdated');</script>";
         } else {
             echo "<script> alert('You have not updated any rows tt');</script>";
         }


     }*/


    /*
                                $queryselectall = "SELECT groupId FROM groupinfo";
                                $resultselectall = mysqli_query($con, $queryselectall) or die ("query is failed" . mysqli_error($con));
                                while ($rows = mysqli_fetch_row($resultselectall)) {

                                    echo "tatatatatatta $rows[0] <br><br>";
                                    if ($rows[0] == $GroupId) {
                                        $GroupId = $GroupId + 1;
                                        echo "lalaalallalalalalala $GroupId <br><br>";

                                    }
                                }
                                $queryi = "Insert Into groupinfo Values('$GroupId', NULL)";
                                $resulti = mysqli_query($con, $queryi) or die ("query is failed" . mysqli_error($con));

                                echo $OldGroupId;
                                echo $GroupSize;

                                echo "count: $rowa[0] <br>";
                                $MemberMove = $rowa[0] - $GroupSize;
                                echo "Moving member $MemberMove <br>";


                                $querys = "SELECT registrationId FROM useraccount WHERE groupId = '$OldGroupId' LIMIT $MemberMove";
                                $selects = mysqli_query($con, $querys) or die ("query is failed blah blah" . mysqli_error($con));
                                while ($row = mysqli_fetch_row($selects)) {
                                    echo $row[0];
                                    $queryu = "UPDATE useraccount SET groupId = '$GroupId' WHERE registrationId = '$row[0]'";
                                    $resultu = mysqli_query($con, $queryu) or die ("query is failed" . mysqli_error($con));
                                    if (mysqli_affected_rows($con) > 0) {
                                        echo "test Group Id: $GroupId";
                                        echo "test Registration ID $row[0]";
                                        $alertupdated = "You have updated tt " . mysqli_affected_rows($con) . " row";
                                        echo "<script> alert ('$alertupdated');</script>";
                                    } else {
                                        echo "<script> alert('You have not updated any rows tt');</script>";
                                    }


                                }*/


    /*else {
                do {

                    echo "same tour location $rowexist[0] <br>";


                    $querya = "SELECT COUNT(registrationId) FROM useraccount WHERE groupId = '$rowexist[0]'";
                    $resulta = mysqli_query($con, $querya) or die ("query is failed" . mysqli_error($con));


                    $queryb = "SELECT groupSize FROM groupinfo WHERE groupId = '$rowexist[0]'";
                    $resultb = mysqli_query($con, $queryb) or die ("query is failed" . mysqli_error($con));
                    if ($rowa = mysqli_fetch_row($resulta)) {

                        echo "member $rowa[0] <br>";
                        if ($rowb = mysqli_fetch_row($resultb)) {
                            echo "size $rowb[0] <br>";
                            if ((($rowa[0] < $rowb[0]) && ($rowb[0] != NULL))) {

                                $GroupId = $rowexist[0];
                                echo "tel: $GroupId";
                                break;
                            } else if (($rowb[0] == NULL)) {
                                $GroupId = $rowexist[0];
                            } else {
                                echo "else $GroupId <br>";
                            }
                        }


                    }


                } while ($rowexist = mysqli_fetch_row($resultselect));

                echo "Final: $GroupId <br><br><br>";


            }*/


    $GroupSize = !empty($GroupSize) ? "'$GroupSize'" : "NULL";

    $queryupdate = "Update groupinfo Set groupSize = $GroupSize where groupId = '$OldGroupId'";
    $resultupdate = mysqli_query($con, $queryupdate) or die ("query is failed" . mysqli_error($con));
    $GroupId = '';
    $GroupSize = '';
    if (mysqli_affected_rows($con) > 0) {
        $alertupdated = "You have updated " . mysqli_affected_rows($con) . " row";
        echo "<script> alert ('$alertupdated');</script>";
    } else {
        echo "<script> alert('You have not updated any rows');</script>";


    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Group Page</title>
</head>
<body>
<form method="post">
    <label>Group ID:</label>
    <input type="text" placeholder="Group Id" name="groupId" value="<?php echo $GroupId; ?>"><br><br>
    <label>Group Size: </label>
    <input type="text" placeholder="Group Size" name="groupSize" value="<?php echo $GroupSize; ?>"><br><br>
    <label>Interested Vacation Plan</label>
    <input type="text" placeholder="Interested Vacation Plan" name="interested_vacation_plan"
           value="<?php echo $Interested_vacation_plan; ?>"><br><br>
    <label>Date: </label>
    <input type="date" placeholder="Date" name="date" value="<?php echo $Date; ?>"><br><br>
    <textarea rows="10" cols="50" placeholder="Group member name"
              name="membersName"><?php echo $MembersName ?></textarea><br><br>
    <input type="submit" class="btn btn-warning" value="Find" name="FIND"/>
    <input type="submit" class="btn btn-success" value="Update" name="UPDATE"/>
</form>

</body>
</html>
