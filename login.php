<?php
$host = "localhost";
$user = "root";
$password = "";
$dbName = "travel_project";


$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");

$Email = mysqli_real_escape_string($con, $_POST['email']);
$RegistrationId = mysqli_real_escape_string($con, $_POST['registrationId']);

$query = "SELECT email FROM useraccount
          WHERE email = '$Email' AND registrationId = '$RegistrationId'";

$result = mysqli_query($con, $query) or die("Query is failed! " . mysqli_error($con));
if (mysqli_num_rows($result) == 0) {
    echo "<h2 style='color: red;'>Login Failed! Wrong Username or Password!</h2>";

} else {
    /*$Email = mysqli_fetch_row($result);*/
    //If checkbox "Remember Me" is checked, assign 2 cookies to store user email and user password. If not, destroy those 2 cookies.
    /*if(isset($_POST['remember'])){
        setcookie("userEmail",$_POST['userEmail'],time()+(10 * 365 * 24 * 60 * 60));
        setcookie("userPassword",$_POST['userPassword'],time()+(10 * 365 * 24 * 60 * 60));
    } else {
        setcookie("userEmail",$_POST['userEmail'],time()-3600);
        setcookie("userPassword",$_POST['userPassword'],time()-3600);
    }*/

    //Redirect to admin page if the account type is admin and redirect to guest incident report page for the rest
    /*if ($type[0] == "admin") {
        $_SESSION['User'] = 'Admin';
        $_SESSION['email'] = $userEmail;
        header('location:Admin_Page.php');
    } else {
        $_SESSION['User'] = 'Guest';
        $_SESSION['email'] = $userEmail;*/
        header('location:HomePage.html');

}
//Close the connection
mysqli_close($con);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
</head>
<body>
<h1>Login Page</h1>
<form method="post">
    <label>Email: </label>
    <input type="text" placeholder="Email" name="email"><br><br>
    <label>RegistrationID: </label>
    <input type="text" placeholder="RegistrationID" name="registrationId"><br><br>
    <input type="submit" value="Registration" name="Login">


</form>
</body>
</html>