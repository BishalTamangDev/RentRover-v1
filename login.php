<?php
// staring session
if (!session_start())
    session_start();

if (isset($_SESSION['adminId']) || isset($_SESSION['tenantUserId']) || isset($_SESSION['tenantUserId'])) {
    if (isset($_SESSION['adminId']))
        header("location: page/admin/dashboard.php");
    elseif (isset($_SESSION['landlordId']))
        header("location: page/landlord/dashboard.php");
    else
        header("location: page/tenant/home.php");
}

// including files
include 'Class/user_class.php';
include 'Class/functions.php';

// creating objects
$userObj = new User();

// on form submission
$errorMessageState = false;
$errorMessage = "This is error message.";

if (isset($_POST['login'])) {
    // retriving the form values
    $email = $_POST['email-address'];
    $password = $_POST['password'];

    // check for value correctness
    $email = mysqli_real_escape_string($userObj->conn, $_POST['email-address']);
    $password = mysqli_real_escape_string($userObj->conn, $_POST['password']);

    $errorMessageState = false;

    // email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessageState = true;
        $errorMessage = "Invalid email format.";
    } else {
        // password security : length and regular expression
        if (strlen($password) < 8) {
            $errorMessageState = true;
            $errorMessage = "Password length must be greater than or equal to 8.";
        } else {
            // encrypt password
            $encPassword = password_hash($password, PASSWORD_BCRYPT);

            // if email exists
            if ($userObj->validateEmail($email)) {
                // retrieving db email & password
                $query = "select email, password, role from `user` where email = '$email'";
                
                $response = mysqli_query($userObj->conn, $query);
                $count = mysqli_num_rows($response);

                if ($count) {
                    // email address found >> verifying password
                    $dbData = mysqli_fetch_assoc($response);
                    $db_password = $dbData['password'];
                    $password_decrypt = password_verify($password, $db_password);

                    if ($password_decrypt) {
                            $role = $dbData['role'];
                            
                            if($role=="Tenant"){
                                $_SESSION['tenantUserId'] = $userObj->getUserId($email);
                                header("location: page/tenant/home.php");
                            }else{
                                $_SESSION['landlordUserId'] = $userObj->getUserId($email);
                                header("location: page/landlord/dashboard.php");
                            }
                    } else {
                        $errorMessageState = true;
                        $errorMessage = "Invalid password!";
                    }
                } else {
                    $errorMessageState = true;
                    $errorMessage = "Make sure you entered the credentials correctly.";
                }
            } else {
                $errorMessageState = true;
                $errorMessage = "Sorry! this email address has not been registered yet.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- external css -->
    <link rel="stylesheet" href="CSS/common/style.css">
    <link rel="stylesheet" href="CSS/common/login.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="Assests/Images/RentRover-Logo.png">

    <!-- title -->
    <title> Login </title>

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>
</head>

<body>
    <div class="login-container flex-column">
        <div class="login-top-section flex-row">
            <abbr title="Home">
                <a href="index.php">
                    <img src="assests/Icons/home.svg" alt="">
                </a>
            </abbr>
            
            <p class="p-large f-bold n-light"> Login </p>
        </div>

        <?php
        if ($errorMessageState) {
            echo '<p class="p-normal negative" style="margin-top: 12px;">', $errorMessage, '</p>';
        }
        ?>

        <br>
        <hr>

        <div class="login-bottom-section">
            <form action="" method="POST" class="login-form flex-column" autocomplete="on">
                <!-- email -->
                <div class="email-sec form-section">
                    <p class="form-title"> Email Address </p>
                    <input type="text" name="email-address" id="email-address" placeholder="email@gmail.com"
                        value="<?php if (isset($_POST['email-address']))
                            echo $_POST['email-address']; ?>"
                        onkeypress="avoidMistake('email-address')" required>
                </div>

                <!-- password -->
                <div class="password-sec form-section flex-column">
                    <div class="top flex-row">
                        <p class="form-title"> Password </p>
                        <input type="checkbox" id="password-toggler" onclick="togglePassword()">
                    </div>

                    <div class="bottom flex-row">
                        <input type="password" id="password" name="password" placeholder="Password"
                            value="<?php if (isset($_POST['password']))
                                echo $_POST['password']; ?>"
                            onkeypress="avoidMistake('email-address')" required>
                    </div>
                </div>

                <input type="submit" name="login" value="Login">

                <a href="registration.php" class="p-small already-have-account-link"> Donot have an account? </a>
            </form>
        </div>
    </div>

    <!-- script section -->
    <script>
        var passwordToggle = document.getElementById("password-toggler");
        var passwordBox = document.getElementById("password");

        window.onload = () => {
            passwordBox.type = "password";
        }

        function togglePassword() {
            if (passwordToggle.checked == true)
                passwordBox.type = "text";
            else
                passwordBox.type = "password";
        }

        // avoiding space press
        avoidMistake = (inputId) => {
            var ascii = event.keyCode;
            // avoiding space
            if (ascii == 32) {
                event.preventDefault();
            } else {
                if (inputId == "email-address") {
                    if (ascii == 32)
                        event.preventDefault();
                }
            }
        } 
    </script>
</body>

</html>