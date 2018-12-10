<?php
//Create 2 variable to display the value of user email and password in the form input, they are empty by default
$Email = "";
$RegistrationId = "";
//Check if the cookies for userEmail and userPassword exist. If they do, assign their values to the 2 variable above for display
if(isset($_COOKIE['email']) & isset($_COOKIE['registrationId'])){
    $Email = $_COOKIE['email'];
    $RegistrationId = $_COOKIE['registrationId'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
            integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
            integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
            crossorigin="anonymous"></script>
</head>
<body>
<div class="card" style="margin: 200px auto; width: 500px;">
    <form method="post">
        <h5 class="card-header">Log In Page</h5>
        <div class="card-body">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">@</span>
                </div>
                <input type="email" name="email" class="form-control" placeholder="Username(Email)"
                       aria-label="Username" aria-describedby="basic-addon1" value="<?php echo $Email; ?>" required>
            </div>
            <div class="input-group mb-3">
                <input type="number" name="registrationId" class="form-control" placeholder="Registration ID"
                       aria-label="Id" value="<?php echo $RegistrationId; ?>" required>
            </div>
            <div class="custom-control custom-checkbox" style="margin-bottom: 10px;">
                <input type="checkbox" class="custom-control-input" id="customCheck1" name="remember">
                <label class="custom-control-label" for="customCheck1">Remember Me</label>
            </div>
            <input type="submit" class="btn btn-primary" value="Log In">
            <a href="RegistrationPage.php" style="color: green; margin-left: 20px;">Would you like to travel with us ?</a>
        </div>
    </form>
</div>
</body>
</html>

<?php
//Check if there is at least 1 cookie exist. If there is, check the checkbox
if(isset($_COOKIE['email'])) echo "<script>document.getElementById('customCheck1').checked = true;</script>";
//Clear any variable from the previous session
session_unset();

//Start session
session_start();




if (isset($_POST['email']) & isset($_POST['registrationId'])) {



$host = "localhost";
$user = "root";
$password = "";
$dbName = "travel_project";


$con = mysqli_connect($host, $user, $password, $dbName)
or die("Connection is failed");

$Email = mysqli_real_escape_string($con, $_POST['email']);
$RegistrationId = mysqli_real_escape_string($con, $_POST['registrationId']);


    $query = "SELECT * FROM useraccount
          WHERE email = '$Email'
          AND   registrationId = '$RegistrationId'";
$result = mysqli_query($con, $query) or die("Query is failed! " . mysqli_error($con));

if (mysqli_num_rows($result) == 0) {
    if (($Email == "admin@gmail.com") &&($RegistrationId == 2410))  {
        $_SESSION['User'] = 'Admin';
        $_SESSION['email'] = $Email;
        header('location:AdminPage.php');
    }
        else {

            echo "<h2 style='color: red;'>Login Failed! Wrong Username or Password!</h2>";
        }
}
else {

        //If checkbox "Remember Me" is checked, assign 2 cookies to store user email and user password. If not, destroy those 2 cookies.
        if(isset($_POST['remember'])){
            setcookie("email",$_POST['email'],time()+(10 * 365 * 24 * 60 * 60));
            setcookie("registrationId",$_POST['registrationId'],time()+(10 * 365 * 24 * 60 * 60));
        } else {
            setcookie("email",$_POST['email'],time()-3600);
            setcookie("registrationId",$_POST['registrationId'],time()-3600);
        }

        //Redirect to admin page if the account type is admin and redirect to guest incident report page for the rest
            $_SESSION['User'] = 'Guest';
            $_SESSION['email'] = $Email;
            $_SESSION['registrationId'] = $RegistrationId;
            header('location:UserInfoPage.php');
        }
    //Close the connection
    mysqli_close($con);
}
?>

