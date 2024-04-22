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

$newUser->userId = $user->userId;
$newUser->setKeyValue('password', $user->password);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> Accounts </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- linking external css -->
    <link rel="stylesheet" href="../../CSS/Common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/landlord/notification-setting.css">

    <!-- script -->
    <script src="../../Js/main.js"></script>
</head>

<body>
    <?php
    include 'aside.php';
    ?>

    <div class="body-container flex-row">
        <!-- empty section -->
        <aside class="empty-section"></aside>

        <!-- main area -->
        <article class="content-article flex-column">
            <div class="flex-column notification-setting-container">
            <!-- heading -->
            <p class="p-normal f-bold" style="margin-top: 20px; padding-left:2px;"> Notification Setting </p>

            <p class="p-normal n-light"> Email : someone@gmail.com </p>

            <p class="p-normal"> Get important notification in your email address? </p>

            <!-- Toggle Slider Container -->
            <label class="toggle-container">
                <input type="checkbox">
                <div class="slider"> </div>
            </label>
            </div>
        </article>
    </div>

    <!-- js script -->
    <script> </script>
</body>

</html>