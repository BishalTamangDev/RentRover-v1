<?php
// including files
include 'Class/user_class.php';
include 'Class/notification_class.php';
include 'Class/functions.php';

// global variables
$citizenshipFrontPhotoName = "";
$citizenshipBackPhotoName = "";
$userPhotoName = "";

$citizenshipFrontPhotoTmpName = "";
$citizenshipBackPhotoTmpName = "";
$userPhotoTmpName = "";

$citizenshipPhotoDestination = "assests/uploads/citizenship/";
$userPhotoDestination = "assests/uploads/user/";

// creating objects
$userObj = new User();

// on form submission
$errorMessageState = false;
$errorMessage = "This is error message";

// review submission check
$submissionState = "unknown";
if (isset($_GET['submission'])) {
    if ($_GET['submission'] != '')
        $submissionState = $_GET['submission'];
}

if (isset($_POST['register'])) {
    // retriving the form values

    $userRole = $_POST['user-role'];
    $firstName = $_POST['first-name'];
    $middleName = $_POST['middle-name'];
    $lastName = $_POST['last-name'];

    if ($lastName == '')
        $lastName = "null";

    $gender = $_POST['gender'];
    $contact = $_POST['contact'];
    $email = $_POST['email-address'];
    $areaName = $_POST['atom-address'];
    $citizenshipNumber = $_POST['citizenship-number'];

    $dob = $_POST['birth-date'];
    $registerDate = date('Y-m-d H:i:s');
    $accountState = 1;

    $citizenshipFrontPhotoFile = $_FILES['citizenship-front-photo'];
    $citizenshipBackPhotoFile = $_FILES['citizenship-back-photo'];
    $profilePhotoFile = $_FILES['profile-photo'];

    // select
    if ($userRole == "None" || $_POST['province'] == 0 || $_POST['district'] == 0 || $_POST['isVdc'] == "None" || $_POST['ward-number'] == 0) {
        $errorMessageState = true;
        if ($userRole == "None") {
            $errorMessage = "Please select the role.";
        } elseif ($_POST['province'] == 0) {
            $errorMessage = "Please select the province.";
        } elseif ($_POST['district'] == 0) {
            $errorMessage = "Please select the district.";
        } elseif ($_POST['isVdc'] == "None") {
            $errorMessage = "Please select VDC/ Municipality.";
        } else {
            $errorMessage = "Please select the ward number.";
        }
    } else {
        $province = $_POST['province'];
        $district = $_POST['district'];
        $isVdc = $_POST['isVdc'];
        $wardNumber = $_POST['ward-number'];

        // check for value correctness
        $email = mysqli_real_escape_string($userObj->conn, $_POST['email-address']);
        $password = mysqli_real_escape_string($userObj->conn, $_POST['password']);
        $passwordConfirm = mysqli_real_escape_string($userObj->conn, $_POST['password-confirmation']);

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

                $emailQuery = "select * from `user` where email = '$email'";

                $query = mysqli_query($userObj->conn, $emailQuery);

                $emailCount = mysqli_num_rows($query);

                if ($emailCount > 0) {
                    $errorMessage = "Sorry! this email address is already registered.";
                } else {
                    if ($password != $passwordConfirm) {
                        $errorMessageState = true;
                        $errorMessage = "Make sure you entered the same passwords.";
                    } else {
                        // citizenship photo upload
                        $citizenshipFrontPhotoValid = fileValidityCheck($_FILES['citizenship-front-photo']);
                        $citizenshipBackPhotoValid = fileValidityCheck($_FILES['citizenship-back-photo']);

                        // user profile picture upload
                        $userPhotoValid = fileValidityCheck($_FILES['profile-photo']);

                        // if all the details and files are valid
                        if ($citizenshipFrontPhotoValid && $citizenshipBackPhotoValid && $userPhotoValid) {
                            uploadFile("citizenshipFrontPhoto", $_FILES['citizenship-front-photo']);
                            uploadFile("citizenshipBackPhoto", $_FILES['citizenship-back-photo']);
                            uploadFile("userPhoto", $_FILES['profile-photo']);

                            $userObj->setUser($firstName, $middleName, $lastName, $gender, $dob, $email, $encPassword, $contact, $province, $district, $isVdc, $areaName, $wardNumber, $userRole, $userPhotoName, $citizenshipNumber, $citizenshipFrontPhotoName, $citizenshipBackPhotoName, $accountState, $registerDate);
                            $userId = $userObj->register();

                            if ($userRole != 0) {
                                // create notification
                                $userNotification = new Notification();
                                $adminNotification = new Notification();

                                if ($userRole == 'Tenant')
                                    $userNotification->setUserNotification(0, 'tenant', $userId, strtolower($userRole));
                                else
                                    $userNotification->setUserNotification(0, 'landlord', $userId, strtolower($userRole));

                                $adminNotification->setUserNotification(0, 'admin', $userId, strtolower($role));

                                $userNotificationState = $userNotification->register();
                                $adminNotificationState = $adminNotification->register();

                                header("location: registration.php?submission=success");
                            } else {
                                header("location: registration.php?submission=failure");
                            }
                        } else {
                            $errorMessageState = true;

                            if (!$citizenshipFrontPhotoValid)
                                $errorMessage = "Error in uploading citizenship front photo.";
                            elseif (!$citizenshipBackPhotoValid)
                                $errorMessage = "Error in uploading citizenship back photo.";
                            else
                                $errorMessage = "Error in uploading profile photos.";
                        }
                    }
                }
            }
        }
    }
}

function fileValidityCheck($formFile)
{
    global $errorMessage;
    global $errorMessageState;

    $fileValid = true;

    $fileName = $formFile['name'];
    $fileTmpName = $formFile['tmp_name'];
    $fileSize = $formFile['size'];
    $fileError = $formFile['error'];
    $fileType = $formFile['type'];

    // error check
    if ($fileError) {
        $fileValid = false;
        $errorMessageState = true;
        $errorMessage = "Error in uploading the file. Make sure you selected the image file that is less than or equal to 2MB.";
        // echo '<script> alert("Error in uploading the file. Make sure you selected the image file that is less than or equal to 2MB."); </script>';
    } else {
        // size check
        if ($fileSize >= 2087152) {
            $fileValid = false;
            $errorMessageState = true;
            $errorMessage = "File size is too big.";
            // echo '<script> alert("File too big :(") </script>';
        } else {
            // extension extraction
            $fileTempExtension = explode('.', $fileName);
            $fileExtension = strtolower(end($fileTempExtension));
            $allowedExtension = array('jpg', 'jpeg', 'png');

            if (in_array($fileExtension, $allowedExtension)) {
                $newFileName = uniqid('', true) . "." . $fileExtension;
            } else {
                $fileValid = false;
                $errorMessageState = true;
                $errorMessage = "Invalid file format.";
                // echo '<script> alert("Invalid file format :("); </script>';
            }
        }
    }
    return $fileValid;
}

function uploadFile($fileCategory, $formFile)
{
    global $citizenshipFrontPhotoName;
    global $citizenshipBackPhotoName;
    global $userPhotoName;

    global $citizenshipFrontPhotoTmpName;
    global $citizenshipBackPhotoTmpName;
    global $userPhotoTmpName;

    global $citizenshipPhotoDestination;
    global $userPhotoDestination;

    $fileValid = true;

    $fileName = $formFile['name'];
    $fileTmpName = $formFile['tmp_name'];
    $fileSize = $formFile['size'];
    $fileError = $formFile['error'];
    $fileType = $formFile['type'];

    // extension extraction
    $fileTempExtension = explode('.', $fileName);
    $fileExtension = strtolower(end($fileTempExtension));

    $newFileName = uniqid('', true) . "." . $fileExtension;

    // setting destination
    if ($fileCategory == "citizenshipFrontPhoto") {
        $citizenshipFrontPhotoName = $newFileName;
        $citizenshipFrontPhotoTmpName = $fileTmpName;
        $citizenshipPhotoDestinaltion = $citizenshipPhotoDestination . $citizenshipFrontPhotoName;
        move_uploaded_file($citizenshipFrontPhotoTmpName, $citizenshipPhotoDestinaltion);
    } elseif ($fileCategory == "citizenshipBackPhoto") {
        $citizenshipBackPhotoName = $newFileName;
        $citizenshipBackPhotoTmpName = $fileTmpName;
        $citizenshipPhotoDestinaltion = $citizenshipPhotoDestination . $citizenshipBackPhotoName;
        move_uploaded_file($citizenshipBackPhotoTmpName, $citizenshipPhotoDestinaltion);
    } elseif ($fileCategory == "userPhoto") {
        $userPhotoName = $newFileName;
        $userPhotoTmpName = $fileTmpName;
        $userPhotoDestinaltion = $userPhotoDestination . $userPhotoName;
        move_uploaded_file($userPhotoTmpName, $userPhotoDestinaltion);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- css -->
    <link rel="stylesheet" href="CSS/common/style.css">
    <link rel="stylesheet" href="CSS/common/registration.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="Assests/Images/RentRover-Logo.png">

    <!-- title -->
    <title> User Registration </title>

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>
</head>

<body>
    <div class="flex-column registration-container">
        <div class="registration-top-section flex-row">
            <abbr title="Home">
                <a href="index.php">
                    <img src="assests/icons/home.svg" alt="">
                </a>
            </abbr>
            <p class="p-large f-bold n-light"> User Registration </p>
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
                    <!-- role -->
                    <div class="role-sec form-section">
                        <p class="form-title"> Select Role </p>
                        <select name="user-role" id="user-role">
                            <?php
                            if (isset($_POST['user-role'])) {
                                ?>
                                <option value="<?php echo $_POST['user-role']; ?>" selected hidden>
                                    <?php echo $_POST['user-role']; ?>
                                </option>
                                <?php
                            } else {
                                ?>
                                <option value="None" selected hidden> None </option>
                                <?php
                            }
                            ?>
                            <option value="Tenant"> Tenant </option>
                            <option value="Landlord"> Landlord </option>
                        </select>
                    </div>

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

                    <!-- gender -->
                    <div class="gender-sec form-section flex-row">
                        <div class="left">
                            <p class="form-title"> Gender </p>
                        </div>

                        <div class="right flex-row">
                            <?php
                            if (isset($_POST['gender'])) {
                                if ($_POST['gender'] == "male") {
                                    echo '<input type="radio" name="gender" id="male" value="male" checked required>';
                                    echo '<label for="male"> Male </label>';

                                    echo '<input type="radio" name="gender" id="female" value="female" required>';
                                    echo '<label for="female"> Female </label>';

                                    echo '<input type="radio" name="gender" id="other" value="other" required>';
                                    echo '<label for="other"> Other </label>';
                                } elseif ($_POST['gender'] == "female") {
                                    echo '<input type="radio" name="gender" id="male" value="male" required>';
                                    echo '<label for="male"> Male </label>';

                                    echo '<input type="radio" name="gender" id="female" value="female" checked required>';
                                    echo '<label for="female"> Female </label>';

                                    echo '<input type="radio" name="gender" id="other" value="other" required>';
                                    echo '<label for="other"> Other </label>';
                                } else {
                                    echo '<input type="radio" name="gender" id="male" value="male" required>';
                                    echo '<label for="male"> Male </label>';

                                    echo '<input type="radio" name="gender" id="female" value="female" required>';
                                    echo '<label for="female"> Female </label>';

                                    echo '<input type="radio" name="gender" id="other" value="other" checked required>';
                                    echo '<label for="other"> Other </label>';
                                }
                            } else {
                                ?>
                                <input type="radio" name="gender" id="male" value="male" required>
                                <label for="male"> Male </label>

                                <input type="radio" name="gender" id="female" value="female">
                                <label for="female"> Female </label>

                                <input type="radio" name="gender" id="other" value="other">
                                <label for="other"> Other </label>
                                <?php
                            }
                            ?>
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

                    <!-- address -->
                    <div class="address-sec form-section">
                        <p class="form-title"> Address </p>
                        <div class="top flex-row">
                            <!-- province -->
                            <select name="province">
                                <?php
                                if ($_POST['province'] != 0) {
                                    echo '<option value="', $_POST['province'], '" selected hidden>', returnArrayValue("province", $_POST['province']), '</option>';
                                } else {
                                    echo '<option value="0" selected hidden> Province </option>';
                                }
                                ?>

                                <option value="1"> Koshi </option>
                                <option value="2"> Madhesh </option>
                                <option value="3"> Bagmati </option>
                                <option value="4"> Gandaki </option>
                                <option value="5"> Lumbini </option>
                                <option value="6"> Karnali </option>
                                <option value="7"> Sudurpaschim </option>
                            </select>

                            <!-- district -->
                            <select name="district">
                                <?php
                                if ($_POST['district'] != 0) {
                                    echo '<option value="', $_POST['district'], '" selected hidden>', returnArrayValue("district", $_POST['district']), '</option>';
                                } else {
                                    echo '<option value="0" selected hidden> District </option>';
                                }

                                for ($count = 1; $count <= 77; $count++) {
                                    echo '<option value="' . $count . '">' . $districtArray[$count] . '</option>';
                                }
                                ?>
                            </select>

                            <!-- municipality or ward? -->
                            <select name="isVdc">
                                <?php
                                if (isset($_POST['isVdc']) && $_POST['isVdc'] != "None") {
                                    if ($_POST['isVdc'] == "municipality") {
                                        echo '<option value="municipality" selected hidden> Municipality </option>';
                                    } else {
                                        echo '<option value="vdc" selected hidden> VDC </option>';
                                    }
                                } else {
                                    echo '<option value="None" selected hidden> Municipality/ VDC </option>';
                                }
                                ?>
                                <option value="municipality"> Municipality </option>
                                <option value="vdc"> VDC </option>
                            </select>
                        </div>

                        <div class="bottom flex-row">
                            <!-- municipality/ VDC name -->
                            <input type="text" name="atom-address" id="municipality-name"
                                placeholder="Municipality/ VDC name" value="<?php if (isset($_POST['atom-address']))
                                    echo $_POST['atom-address']; ?>" required>

                            <!-- ward number -->
                            <select name="ward-number">
                                <?php
                                if ($_POST['ward-number'] != 0) {
                                    echo '<option value="', $_POST['ward-number'], '" selected hidden>', $_POST['ward-number'], '</option>';
                                } else {
                                    echo '<option value="0" selected hidden> Ward Number </option>';
                                }
                                ?>
                                <option value="1"> 1 </option>
                                <option value="2"> 2 </option>
                                <option value="3"> 3 </option>
                                <option value="4"> 4 </option>
                                <option value="5"> 5 </option>
                                <option value="6"> 6 </option>
                                <option value="7"> 7 </option>
                                <option value="8"> 8 </option>
                                <option value="9"> 9 </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bottom-right-section">
                    <!-- citizenship number -->
                    <div class="citisenship-number-sec form-section">
                        <p class="form-title"> Citizenship Number </p>
                        <input type="text" name="citizenship-number" value="<?php if (isset($_POST['citizenship-number']))
                            echo $_POST['citizenship-number']; ?>" onkeypress="avoidMistake('citizenship-number')"
                            maxlength="15" required>
                    </div>

                    <!-- citizenship photo -->
                    <div class="citizenship-photo-sec form-section file-input flex-column">
                        <p class="form-title"> Citizenship Photo </p>

                        <div class="bottom flex-column">
                            <div class="bottom-top flex-row">
                                <label class="p-normal"> Front side </label>
                                <input type="file" name="citizenship-front-photo" accept=".jpeg, .jpg, .png" required>
                                <br>
                            </div>

                            <div class="bottom-bottom flex-row">
                                <label class="p-normal"> Back side </label>
                                <input type="file" name="citizenship-back-photo" accept=".jpeg, .jpg, .png" required>
                            </div>
                        </div>
                    </div>

                    <p class="p-form warning" style="margin-top: 10px;"> Note: Please attach front and back side of the
                        citizenship. </p>

                    <!-- Profile picture -->
                    <div class="profile-pic-sec form-section file-input flex-row flex-row">
                        <label for="" class="p-normal"> Profile picture </label>
                        <input type="file" name="profile-photo" accept=".jpeg, .jpg, .png" required>
                    </div>

                    <!-- Birth date -->
                    <div class="birth-date-sec form-section">
                        <p class="form-title"> Birth Date </p>
                        <input type="date" name="birth-date" class="p-form" value="<?php if (isset($_POST['birth-date']))
                            echo $_POST['birth-date']; ?>" required>
                    </div>

                    <!-- terms and conditions -->
                    <div class="terms-and-condition-sec form-section flex-row">
                        <input type="checkbox" name="terms-and-conditions" id="terms" required>
                        <label for="terms"> I accept all the <span class="negative"> Terms & Conditons.</span> </label>
                    </div>

                    <!-- form submit section -->
                    <input type="submit" value="Register" name="register">

                    <!-- if($role == 'tenant') -->
                    <!-- echo '<a href="login.php?role=tenant" class="already-have-account-link"> Already have an account? </a>'; -->
                    <!-- else -->
                    <!-- echo '<a href="login.php?role=landlord" class="already-have-account-link"> Already have an account? </a>'; -->
                    <a href="login.php" class="already-have-account-link"> Already have an account? </a>
                </div>
            </form>
        </div>
    </div>

    <!-- dialog -->
    <?php
    if ($submissionState == 'success' || $submissionState == 'failure') {
        ?>
        <div class="dialog-container flex-column">
            <div class="dialog-div flex-column">
                <div class="top-div flex-row">
                    <div class="message-div flex-column">
                        <?php
                        if ($submissionState == 'success') {
                            ?>
                            <p class="p-large f-bold "> Registration successful. </p>
                            <p class="p-normal"> Please wait for some time for your account verification. </p>
                            <?php
                        } else if ($submissionState == 'failure') {
                            ?>
                                <p class="p-large f-bold negative"> Registration failed. </p>
                                <p class="p-normal"> Please try again. </p>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <div class="operation-div flex-row">
                    <?php
                    if ($submissionState == 'success') {
                        ?>
                        <button onclick="window.location.href='login.php'"> Proceed to login </button>
                        <?php
                    } else if ($submissionState == 'failure') {
                        ?>
                            <button onclick="window.location.href='registration.php>'"> Try again </button>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    } ?>

    <!-- script section -->
    <script src="Js/jquery-3.7.1.min.js"> </script>

    <script>
        var passwordToggle = document.getElementById("password-toggler");
        var passwordBox = document.getElementById("password");
        var passwordConfirmBox = document.getElementById("password-confirmation");

        window.onload = () => {
            passwordBox.type = "password";
            passwordConfirmBox.type = "password";
        }

        function togglePassword() {
            if (passwordToggle.checked == true) {
                passwordBox.type = "text";
                passwordConfirmBox.type = "text";
            } else {
                passwordBox.type = "password";
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
                    } else
                        event.preventDefault();
                }

                // type number
                if (inputId == 'contact') {
                    if (ascii >= 48 && ascii <= 57) {
                    } else
                        event.preventDefault();
                }

                // citizensip
                if (inputId == 'citizenship-number') {
                    if (ascii == 45 || ascii >= 48 && ascii <= 57) {
                    } else
                        event.preventDefault();
                }
            }
        } 
    </script>

    <script>

    </script>
</body>

</html>