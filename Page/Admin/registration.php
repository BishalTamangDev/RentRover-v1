<?php

// starting session
if (!session_start())
    session_start();

// including files
include '../../Class/admin_class.php';
include '../../Class/functions.php';

// creating objects
$adminObj = new Admin();

// on form submission
$errorMessageState = false;
$errorMessage = "This is error message";

if (isset($_POST['register'])) {
    // retriving the form values
    $firstName = $_POST['first-name'];
    $middleName = $_POST['middle-name'];
    $lastName = $_POST['last-name'];

    if ($lastName == '')
        $lastName = "null";

    $contact = $_POST['contact'];
    $email = $_POST['email-address'];
    $registerDate = date('Y-m-d H:i:s');

    // check for value correctness
    $email = mysqli_real_escape_string($adminObj->conn, $_POST['email-address']);
    $password = mysqli_real_escape_string($adminObj->conn, $_POST['password']);
    $passwordConfirm = mysqli_real_escape_string($adminObj->conn, $_POST['password-confirmation']);

    // email validation
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
            $encPasswordConfirm = password_hash($passwordConfirm, PASSWORD_BCRYPT);

            $emailQuery = "select * from `admin` where email = '$email'";

            $query = mysqli_query($adminObj->conn, $emailQuery);

            $emailCount = mysqli_num_rows($query);

            if ($emailCount > 0) {
                $errorMessageState = true;
                $errorMessage = "Sorry! this email address is already registered.";
            } else {
                if ($password != $passwordConfirm) {
                    $errorMessageState = true;
                    $errorMessage = "Make sure you entered the same passwords.";
                } else {
                    // if all the details and files are valid
                    $adminObj->setAdmin($firstName, $middleName, $lastName, $email, $encPassword, $contact, $registerDate);
                    $adminObj->register();
                }
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

    <!-- css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/common/registration.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <title> Registration </title>

    <!-- internal css -->
    <style rel="stylesheet">
        .registration-container {
            width: 500px;
            /* display: none; */
        }
    </style>

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

    <!-- jquery -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <div class="registration-container flex-column">
        <div class="registration-top-section flex-row" style="align-items:center;">
            <abbr title="Home">
                <a href="../../index.php">
                    <img src="../../assests/Icons/home.svg" alt="">
                </a>
            </abbr>

            <p class="p-large f-bold n-light"> Admin Registration Form </p>
        </div>

        <?php
        if ($errorMessageState) {
            ?>
            <p class="p-negative negative" style="margin-top: 12px;">
                <?php echo $errorMessage; ?>
            </p>
            <?php
        }
        ?>
        <br>
        <hr>

        <div class="registration-bottom-section">
            <form action="" method="POST" enctype="multipart/form-data" class="registration-form flex-row"
                id="registration-form" autocomplete="on">
                <div class="bottom-left-section">
                    <!-- name -->
                    <div class="name-sec form-section">
                        <p class="form-title"> Full Name </p>
                        <div class="name-sec-right flex-row">
                            <input type="text" name="first-name" placeholder="First name" required value="<?php if (isset($_POST['first-name']))
                                echo $_POST['first-name']; ?>" onkeypress="avoidMistake('first-name')">
                            <input type="text" name="middle-name" placeholder="Middle name" value="<?php if (isset($_POST['middle-name']))
                                echo $_POST['middle-name']; ?>" onkeypress="avoidMistake('middle-name')">
                            <input type="text" name="last-name" placeholder="Last name" required value="<?php if (isset($_POST['last-name']))
                                echo $_POST['last-name']; ?>" onkeypress="avoidMistake('last-name')">
                        </div>
                    </div>

                    <!-- email -->
                    <div class="email-sec form-section">
                        <p class="form-title"> Email Address </p>
                        <input type="text" name="email-address" placeholder="someone@gmail.com" value="<?php if (isset($_POST['email-address']))
                            echo $_POST['email-address']; ?>" onkeypress="avoidMistake()" required>
                    </div>

                    <!-- password -->
                    <div class="password-sec form-section">
                        <div class="top flex-row">
                            <p class="form-title"> Password </p>
                            <input type="checkbox" id="password-toggler" onclick="togglePassword()">
                        </div>

                        <div class="password-sec-bottom flex-row">
                            <input type="password" name="password" id="password" placeholder="Password" value="<?php if (isset($_POST['password']))
                                echo $_POST['password']; ?>" onkeypress="avoidMistake()" required>
                            <input type="password" name="password-confirmation" id="password-confirmation"
                                placeholder="Password confirmation" value="<?php if (isset($_POST['password-confirmation']))
                                    echo $_POST['password-confirmation']; ?>" onkeypress="avoidMistake()" required>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="contact-sec form-section">
                        <p class="form-title"> Contact </p>
                        <input type="text" placeholder="Phone number" id="contact" name="contact" value="<?php if (isset($_POST['contact']))
                            echo $_POST['contact']; ?>" onkeypress="avoidMistake('contact')" maxlength="14" required>
                    </div>

                    <div class="bottom-right-section">
                        <!-- terms and conditions -->
                        <div class="terms-and-condition-sec form-section flex-row">
                            <input type="checkbox" name="terms-and-conditions" id="terms" required>
                            <label for="terms"> I accept all the <span class="negative"> Terms & Conditons.</span>
                            </label>
                        </div>

                        <!-- form submit section -->
                        <input type="submit" value="Register" name="register">

                        <a href="login.php" style="display: block; float: right; margin-top: 10px; cursor: pointer;">
                            Already have an account? </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- script section -->
    <script>
        var passwordToggle = document.getElementById("password-toggler");
        var passworconnectionox = document.getElementById("password");
        var passwordConfirmBox = document.getElementById("password-confirmation");

        window.onload = () => {
            passworconnectionox.type = "password";
            passwordConfirmBox.type = "password";
        }

        function togglePassword() {
            if (passwordToggle.checked == true) {
                passworconnectionox.type = "text";
                passwordConfirmBox.type = "text";
            } else {
                passworconnectionox.type = "password";
                passwordConfirmBox.type = "password";
            }
        }

        // avoiding space press
        avoidMistake = (inputId) => {
            var ascii = event.keyCode;
            // avoiding space
            if (ascii == 32) {
                event.preventDefault();
            } else {
                // type: text
                if (inputId == 'first-name' || inputId == 'middle-name' || inputId == 'last-name') {
                    if ((ascii >= 97 && ascii <= 122) || (ascii >= 65 && ascii <= 90)) {
                    } else {
                        event.preventDefault();
                    }
                }

                // type number
                if (inputId == 'contact') {
                    if (ascii >= 48 && ascii <= 57) {
                    } else {
                        event.preventDefault();
                    }
                }

                // citizensip
                if (inputId == 'citizenship-number') {
                    if (ascii == 45 || ascii >= 48 && ascii <= 57) {
                    } else {
                        event.preventDefault();
                    }
                }
            }
        } 
    </script>
</body>

</html>