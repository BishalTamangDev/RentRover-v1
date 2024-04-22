<?php
// staring session
if (!session_start())
    session_start();

// including files
include '../../Class/user_class.php';
include '../../class/functions.php';

// creating the object
$user = new User();
$newUser = new User();


if (!isset($_SESSION['landlordUserId']))
    header("Location: login.php");
else
    $user->fetchSpecificRow($_SESSION['landlordUserId']);

$user->userId = $_SESSION['landlordUserId'];
$user->fetchSpecificRow($user->userId);

// submission
$submissionState = "failure";
if (isset($_GET['submission']))
    $submissionState = $_GET['submission'];

$errorMessageState = false;
$errorMessage = "This is an error message.";

// updating password
if (isset($_POST['password-submit'])) {
    $oldPassword = $_POST['password-old'];
    $newPassword1 = $_POST['password-new-1'];
    $newPassword2 = $_POST['password-new-2'];
    if (strlen($oldPassword) < 8 || strlen($newPassword1) < 8 || strlen($newPassword2) < 8) {
        $errorMessageState = true;
        $errorMessage = 'Make sure the size of passwords are greater than or equal to 8.';
    } else {
        if ($oldPassword == $newPassword1) {
            $errorMessageState = true;
            $errorMessage = 'Please enter new password different than the old password.';
        } else {
            if ($newPassword1 != $newPassword2) {
                $errorMessageState = true;
                $errorMessage = 'Please enter the same password for the new passwords.';
            } else {
                $newPasswordUser = new User();
                $newPasswordUser->fetchSpecificRow($_SESSION['landlordUserId']);
                $newPasswordUser->setKeyValue('id', $_SESSION['landlordUserId']);

                $password_decrypt = password_verify($oldPassword, $newPasswordUser->password);
                if (!$password_decrypt) {
                    $errorMessageState = true;
                    $errorMessage = "The old password didn't match.";
                } else {
                    $encPassword = password_hash($newPassword1, PASSWORD_BCRYPT);
                    $result = $newPasswordUser->updatePassword($encPassword);
                    if ($result) {
                        header("location: accounts.php?task=view-password-and-security&submission=success");
                    } else {
                        $errorMessageState = true;
                        $errorMessage = "Error occured. Please try again later.";
                    }
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

    <!-- linking external css -->
    <link rel="stylesheet" href="../../CSS/Common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/landlord/password-security.css">

    <!-- title -->
    <title> Accounts </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- script -->
    <script src="../../Js/main.js"> </script>
</head>

<body>
    <?php
    include 'aside.php';
    ?>

    <div class="body-container flex-row">
        <!-- empty section -->
        <aside class="empty-section"> </aside>

        <!-- password & security -->
        <article class="content-article flex-column">
            <!-- heading -->
            <div class="container flex-column">
                <p class="p-normal f-bold" style="margin-top: 20px; padding-left:2px;"> Password & Security </p>

                <?php if ($errorMessageState)
                    echo '<p class="p-form negative">' . $errorMessage . '</p>';
                ?>

            </div>

            <div class="top-section flex-row">
                <img src="../../Assests/Icons/eye.svg" alt="" class="pointer" onclick="togglePassword()">
                <p class="p-form pointer" id="show-password-label" onclick="togglePassword()"> Show Password
                </p>
            </div>

            <!-- form -->
            <form action="" method="POST" class="password-update-form flex-column">
                <input type="password" name="password-old" id="password-old" placeholder="old password" value="<?php if (isset($_POST['password-old']))
                    echo $_POST['password-old']; ?>" required>
                <input type="password" name="password-new-1" id="password-new-1" placeholder="new password" value="<?php if (isset($_POST['password-new-1']))
                    echo $_POST['password-new-1']; ?>" required>
                <input type="password" name="password-new-2" id="password-new-2" placeholder="retype new password"
                    value="<?php if (isset($_POST['password-new-2']))
                        echo $_POST['password-new-2']; ?>" required>
                <input type="submit" name="password-submit" value="Update">
            </form>

            <div class="initial-div flex-column">
                <p class="p-form"> Note : Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas et
                    ipsum, unde, illo, rem nemo molestiae aliquid ullam obcaecati debitis nostrum rerum
                    distinctio delectus? </p>
            </div>

            <!-- dialog box -->
            <?php
            if ($submissionState == 'success') {
                ?>
                <div class="dialog-container flex-column">
                    <div class="dialog-div flex-column">
                        <div class="top-div flex-row">
                            <div class="message-div flex-column">
                                <?php
                                if ($task == 'view-password-and-security') {
                                    echo '<p class="p-large f-bold"> Password has been updated. </p>';
                                } elseif ($task == 'edit-profile') {
                                    echo '<p class="p-large f-bold"> Profile has been updated succesfully. </p>';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="operation-div flex-row">
                            <!-- <button onclick="window.location.href='add-house.php';" class="inverse-button"> Register Another House </button> -->
                            <!-- <button onclick="window.location.href='myhouse.php';"> See House Details </button> -->
                            <?php
                            if ($task == 'view-password-and-security' || $task == 'edit-profile') {
                                ?>
                                <button onclick="window.location.href='accounts.php?task=view-profile'" class="button"> View
                                    Profile </button>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            } ?>
        </article>
    </div>

    <script>
        var passwordVisibility = false;
        const passwordBox1 = document.getElementById("password-old");
        const passwordBox2 = document.getElementById("password-new-1");
        const passwordBox3 = document.getElementById("password-new-2");
        const passwordToggleLabel = document.getElementById("show-password-label");

        togglePassword = () => {
            passwordVisibility = !passwordVisibility;
            if (passwordVisibility) {
                passwordBox1.type = "text";
                passwordBox2.type = "text";
                passwordBox3.type = "text";
                passwordToggleLabel.innerText = "Hide password";
            } else {
                passwordBox1.type = "password";
                passwordBox2.type = "password";
                passwordBox3.type = "password";
                passwordToggleLabel.innerText = "Show password";
            }
        }

        // Trim removes leading and trailing whitespaces
        function isEmpty(inputElement) {
            return inputElement.value.trim() === '';
        }
    </script>
</body>

</html>