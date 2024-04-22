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

$submissionState = false;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- linking external css -->
    <link rel="stylesheet" href="../../CSS/Common/style.css">
    <link rel="stylesheet" href="../../CSS/Common/table.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/landlord/subscription.css">

    <!-- title -->
    <title> Accounts </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- script -->
    <script src="../../Js/main.js"></script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="body-container flex-row">
        <!-- empty section -->
        <aside class="empty-section"></aside>

        <article class="content-article flex-column">
            <!-- heading -->
            <p class="p-normal f-bold" style="margin-top: 20px; padding-left:2px;"> Subscription </p>

            <div class="flex-row subscription-div">
                <div class="subscription-left-div flex-column">
                    <p class="p-normal"> Your subscription has been expired. Renew now!</p>
                    <p class="p-normal hidden"> 12 days remainign for you subscription.</p>
                    <button class="negative-button"> Subscribe Now </button>
                    <button class="negative-button hidden"> Pre Subscribe Now </button>
                </div>

                <div class="subscription-right-div flex-column">
                    <p class="p-normal"> Use the following QR to renew. </p>
                    <img src="../../Assests/Icons/qr.png" alt="">
                </div>
            </div>

            <!-- Subscription History Table -->
            <div class="subscription-history-container content-container flex-column">
                <p class="p-large heading f-bold n-light"> Subscription History </p>

                <table class="content-container table-class subscription-table">
                    <thead>
                        <th class="t-serial"> S.N. </th>
                        <th class="t-subscription-month"> Month </th>
                        <th class="t-type"> Type </th>
                        <th class="t-note"> Note</th>
                        <th class="t-subscription-date"> Subscription Date </th>
                    </thead>

                    <tr>
                        <td class="t-serial"> 1. </td>
                        <td class="t-subscription-month"> April, 1999 </td>
                        <td class="t-type"> Type 1 </td>
                        <td class="t-note"> Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore,
                            ipsum. </td>
                        <td class="t-subscription-date"> 4th April, 1999 </td>
                    </tr>

                    <tr>
                        <td colspan="5" class="negative" style="text-align: center; padding-bottom: 10px;"> No
                            subscription history found. </td>
                    </tr>
                </table>
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

    <!-- js section -->
    <script> </script>
</body>

</html>