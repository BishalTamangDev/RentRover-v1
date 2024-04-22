<?php
if (!isset($_SESSION['landlordUserId']))
    header('location: ../../index.php');

include_once '../../Class/connection_class.php';
include_once '../../Class/notification_class.php';

$dbObj = new DatabaseConnection();
$notification = new Notification();

$landlordUserId = $_SESSION['landlordUserId'];
$notificationCount = $notification->countNotification('user', $landlordUserId, "all");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/aside.css">

    <!-- title -->
    <title> Aside </title>

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">
</head>

<body>
    <div class="flex-row body-container">
        <!-- side menu -->
        <aside class="aside-menu-container flex-column" id="menu-container">
            <div class="logo-section">
                <a href="dashboard.php">
                    <img src="../../Assests/Images/rentrover-logo-rectangle.png" alt="">
                </a>
            </div>

            <div class="aside-menu-section flex-column">
                <!-- dashboard -->
                <abbr title="Dashboard">
                    <div class="section flex-row pointer" id="dashboard-menu-id"
                        onclick="window.location.href='dashboard.php'">
                        <div class="left">
                            <img src="../../Assests/Icons/menu.png" class="icon-class" alt="">
                        </div>

                        <div class="right">
                            <p class="p-normal"> Dashboard </p>
                        </div>
                    </div>
                </abbr>

                <!-- house -->
                <abbr title="House">
                    <div class="section flex-row pointer" id="house-menu-id"
                        onclick="window.location.href='myhouse.php'">
                        <div class="left">
                            <img src="../../Assests/Icons/home.svg" class="icon-class" alt="">
                        </div>

                        <div class="right">
                            <p class="p-normal"> Houses </p>
                        </div>
                    </div>
                </abbr>

                <!-- room -->
                <abbr title="Room">
                    <div class="section flex-row pointer" id="room-menu-id" onclick="window.location.href='myroom.php'">
                        <div class="left">
                            <img src="../../Assests/Icons/room.png" class="icon-class" alt="" id="room-icon">
                        </div>

                        <div class="right">
                            <p class="p-normal"> Rooms </p>
                        </div>
                    </div>
                </abbr>

                <!-- tenants -->
                <abbr title="Tenants">
                    <div class="section flex-row pointer" id="tenant-menu-id"
                        onclick="window.location.href='tenants.php'">
                        <div class="left">
                            <img src="../../Assests/Icons/user-square.svg" class="icon-class" alt="">
                        </div>

                        <div class="right">
                            <p class="p-normal"> Tenants </p>
                        </div>
                    </div>
                </abbr>

                <!-- tenant voice -->
                <abbr title="Tenant Voice">
                    <div class="section flex-row pointer" id="tenant-voice-menu-id"
                        onclick="window.location.href='tenant-voice.php'">
                        <div class="left">
                            <img src="../../Assests/Icons/speaking.png" class="icon-class" alt="">
                        </div>

                        <div class="right">
                            <p class="p-normal"> Tenants Voice </p>
                        </div>
                    </div>
                </abbr>

                <!-- application -->
                <abbr title="Applications">
                    <div class="section flex-row pointer" id="application-menu-id"
                        onclick="window.location.href='application.php'">
                        <div class="left">
                            <img src="../../Assests/Icons/application.png" class="icon-class" alt="">
                        </div>

                        <div class="right">
                            <p class="p-normal"> Applications </p>
                        </div>
                    </div>
                </abbr>

                <!-- leave application -->
                <abbr title="Leave Applications">
                    <div class="section flex-row pointer" id="leave-application-menu-id"
                        onclick="window.location.href='leave-application.php'">
                        <div class="left">
                            <img src="../../Assests/Icons/leave-room.png" class="icon-class" alt="">
                        </div>

                        <div class="right">
                            <p class="p-normal"> Leave Applications </p>
                        </div>
                    </div>
                </abbr>

                <!-- Tenancy History -->
                <abbr title="Tenancy History">
                    <div class="section flex-row pointer" id="tenancy-history-menu-id"
                        onclick="window.location.href='tenancy-history.php'">
                        <div class="left">
                            <img src="../../Assests/Icons/make-tenant.png" class="icon-class" alt="">
                        </div>

                        <div class="right">
                            <p class="p-normal"> Tenancy History </p>
                        </div>
                    </div>
                </abbr>

                <!-- my anouncement -->
                <abbr title="My Announcements">
                    <div class="section flex-row pointer" id="announcement-menu-id"
                        onclick="window.location.href='my-announcement.php'">
                        <div class="left">
                            <img src="../../Assests/Icons/announcement.png" class="icon-class" alt="">
                        </div>

                        <div class="right">
                            <p class="p-normal"> Announcements </p>
                        </div>
                    </div>
                </abbr>
            </div>
        </aside>

        <!-- top nav & menus container -->
        <nav class="nav-menus-container flex-column">
            <!-- nav section -->
            <div class="nav-container flex-row">
                <div class="flex-row notification-profile-container">
                    <!-- notification section -->
                    <div class="nav-notification-container flex-row pointer" onclick="toggleNotificationMenu()">
                        <div class="notification-icon-div flex-row">
                            <img src="../../Assests/Icons/notification.svg" alt="">
                        </div>

                        <!-- notification count -->
                        <div class="notification-count-div flex-row">
                            <p class="p-form">
                                <?php
                                echo $notificationCount <= 9 ? $notificationCount : '9+';
                                ?>
                            </p>
                        </div>
                    </div>

                    <!-- profile section -->
                    <div class="profile-container flex-row pointer" onclick="toggleUserMenu()">
                        <img src="../../Assests/Uploads/user/<?php echo $user->userPhoto; ?>" alt="User photo"
                            class="user-photo">
                    </div>
                </div>
            </div>

            <!-- content section -->
            <div class="menus-container">
                <!-- user menu  -->
                <div class="user-menu-container" id="user-menu-container-id">
                    <div class="user-menu-div flex-row">
                        <div class="user-menu shadow flex-column" id="menu-container">
                            <!-- my profile -->
                            <div class="section flex-row pointer" onclick="window.location.href='profile-view.php'">
                                <div class="left">
                                    <img src="../../Assests/Icons/user-square.svg" alt="">
                                </div>

                                <div class="right">
                                    <p class="p-normal"> My Profile </p>
                                </div>
                            </div>

                            <!-- Password & Security -->
                            <div class="section flex-row pointer"
                                onclick="window.location.href='password-security.php'">
                                <div class="left">
                                    <img src="../../assests/Icons/shield.png" alt="">
                                </div>

                                <div class="right">
                                    <p class="p-normal"> Password & Security </p>
                                </div>
                            </div>

                            <!-- Notification Setting -->
                            <div class="section flex-row pointer"
                                onclick="window.location.href='notification-setting.php'">
                                <div class="left">
                                    <img src="../../Assests/Icons/notification.svg" class="icon-class" alt="">
                                </div>

                                <div class="right">
                                    <p class="p-normal"> Notification Setting </p>
                                </div>
                            </div>

                            <!-- system announcement -->
                            <div class="section flex-row pointer"
                                onclick="window.location.href='system-announcement.php'">
                                <div class="left">
                                    <img src="../../Assests/Icons/announcement.png" class="icon-class" alt="">
                                </div>

                                <div class="right">
                                    <p class="p-normal"> System Announcement </p>
                                </div>
                            </div>

                            <!-- Subscription -->
                            <div class="section flex-row pointer" onclick="window.location.href='subscription.php'">
                                <div class="left">
                                    <img src="../../Assests/Icons/setting.svg" alt="">
                                </div>

                                <div class="right">
                                    <p class="p-normal"> Subscription </p>
                                </div>
                            </div>

                            <hr>

                            <div class="section flex-row pointer" onclick="window.location.href='logout.php'">
                                <div class="left">
                                    <img src="../../Assests/Icons/logout.svg" alt="">
                                </div>

                                <div class="right">
                                    <p class="p-normal"> Log Out </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- notification -->
                <div class="notification-menu-container" id="notification-menu-container-id">
                    <div class="notification-menu-div flex-row">
                        <div class="notification-container flex-column shadow" id="notification-container">
                            <div class="flex-column notification-top-section">
                                <div class="top flex-row">
                                    <p class="p-large f-bold"> Notification </p>
                                    <p class="p-normal pointer info" onclick="window.location.href='notification.php'">
                                        See all</p>
                                </div>

                                <div class="flex-row bottom">
                                    <div class="pointer card" id="all-notification-trigger">
                                        <p class="p-form"> All </p>
                                    </div>

                                    <div class="pointer card" id="unseen-notification-trigger">
                                        <p class="p-form"> Unseen </p>
                                    </div>

                                    <div class="pointer card" id="seen-notification-trigger">
                                        <p class="p-form"> Seen </p>
                                    </div>
                                </div>
                            </div>

                            <hr style="background-color:lightgray;">

                            <?php
                            $notificationSets = $notification->fetchNotification("landlord", $_SESSION['landlordUserId']);

                            foreach ($notificationSets as $set) {
                                if ($set['type'] == 'user-registration') { // user registration
                                    ?>
                                    <div class="pointer flex-row section notification-element <?php echo ($set['seen'] == 1) ? "notification-seen-element" : "notification-unseen-element"; ?>"
                                        onclick="window.location.href='profile-view.php'">
                                        <div class="left flex-row">
                                            <div class="dot seen-dot unseen-dot"> </div>
                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/notification_icon_user_registration.png"
                                                    alt="icon">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-normal"> You joined RentRover. </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'user-verify') { // user verify
                                    ?>
                                    <div class="pointer flex-row section notification-element <?php echo ($set['seen'] == 1) ? "notification-seen-element" : "notification-unseen-element"; ?>"
                                        onclick="window.location.href='profile-view.php'">
                                        <div class="left flex-row">
                                            <div class="dot seen-dot unseen-dot"> </div>
                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/verified.png" alt="icon">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-normal"> You account has been verified. </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'user-suspend') { // user suspend
                                    ?>
                                    <div class="pointer flex-row section notification-element <?php echo ($set['seen'] == 1) ? "notification-seen-element" : "notification-unseen-element"; ?>"
                                        onclick="window.location.href='profile-view.php'">
                                        <div class="left flex-row">
                                            <div class="dot seen-dot unseen-dot"> </div>
                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/report.png" alt="icon">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-normal"> You account has been suspended. </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'house-registration') { // house registration
                                    $houseId = $set['house_id'];
                                    $link = "myhouse-detail.php?houseId=$houseId";
                                    ?>
                                    <!-- new user joined notification -->
                                    <div class="pointer flex-row section notification-element <?php echo ($set['seen'] == 1) ? "notification-seen-element" : "notification-unseen-element"; ?>"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot"> </div>

                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/notification_icon_house_registration.png" alt=""
                                                    class="notify-image">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-form"> You registered a new house. </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'house-verify') { // house verify
                                    $houseId = $set['house_id'];
                                    $link = "myhouse-detail.php?houseId=$houseId";
                                    ?>
                                    <!-- new user joined notification -->
                                    <div class="pointer flex-row section notification-element <?php echo ($set['seen'] == 1) ? "notification-seen-element" : "notification-unseen-element"; ?>"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot"> </div>

                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/verified.png" alt="" class="notify-image">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-form"> Your house has been verified. </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'house-suspend') { // house registration
                                    $houseId = $set['house_id'];
                                    $link = "myhouse-detail.php?houseId=$houseId";
                                    ?>
                                    <!-- new user joined notification -->
                                    <div class="pointer flex-row section notification-element <?php echo ($set['seen'] == 1) ? "notification-seen-element" : "notification-unseen-element"; ?>"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot"> </div>

                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/report.png" alt="" class="notify-image">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-form"> You house has been suspended. </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'room-registration') { // room registration
                                    $roomId = $set['room_id'];
                                    $link = "myroom-detail.php?roomId=$roomId";
                                    ?>
                                    <!-- room registration notification -->
                                    <div class="pointer flex-row section notification-element <?php echo ($set['seen'] == 1) ? "notification-seen-element" : "notification-unseen-element"; ?>"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot"> </div>

                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/notification_icon_room_registration.png" alt=""
                                                    class="notify-image">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-form"> You registered a new room. </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'room-verify') { // room verify
                                    $roomId = $set['room_id'];
                                    $link = "myroom-detail.php?roomId=$roomId";
                                    ?>
                                    <!-- room registration notification -->
                                    <div class="pointer flex-row section notification-element <?php echo ($set['seen'] == 1) ? "notification-seen-element" : "notification-unseen-element"; ?>"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot"> </div>

                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/verified.png" alt="" class="notify-image">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-form"> You room has ben verified. </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'room-suspend') { // room registration
                                    $roomId = $set['room_id'];
                                    $link = "myroom-detail.php?roomId=$roomId";
                                    ?>
                                    <!-- room registration notification -->
                                    <div class="pointer flex-row section notification-element <?php echo ($set['seen'] == 1) ? "notification-seen-element" : "notification-unseen-element"; ?>"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot"> </div>

                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/report.png" alt="" class="notify-image">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-form"> You room has been suspended. </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'room-application-submit') { // room application submit
                                    $tenantId = $set['tenant_id'];
                                    $roomId = $set['room_id'];
                                    $application = $set['application_id'];
                                    $link = "application-detail.php?applicationId=$application&tenantId=$tenantId&roomId=$roomId";
                                    ?>
                                    <div class="pointer flex-row section read-notification"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot seen-dot unseen-dot"> </div>
                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/room-apply.png" alt="icon">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-normal">
                                                <?php echo $user->getUserName($set['tenant_id']); ?> applied for your room.
                                            </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'room-application-cancel') { // room application cancel
                                    $tenantId = $set['tenant_id'];
                                    $roomId = $set['room_id'];
                                    $application = $set['application_id'];
                                    $link = "application-detail.php?applicationId=$application&tenantId=$tenantId&roomId=$roomId";
                                    ?>
                                    <div class="pointer flex-row section read-notification"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot seen-dot unseen-dot"> </div>
                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/cancelled.png" alt="icon">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-normal">
                                                <?php echo $user->getUserName($set['tenant_id']); ?> cancelled the application.
                                            </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'room-application-re-apply') { // room application re-apply
                                    $tenantId = $set['tenant_id'];
                                    $roomId = $set['room_id'];
                                    $application = $set['application_id'];
                                    $link = "application-detail.php?applicationId=$application&tenantId=$tenantId&roomId=$roomId";
                                    ?>
                                    <div class="pointer flex-row section read-notification"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot seen-dot unseen-dot"> </div>
                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/room-apply.png" alt="icon">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-normal">
                                                <?php echo $user->getUserName($set['tenant_id']); ?> re-applied the application.
                                            </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'room-application-make-tenant') { // make tenant
                                    $tenantId = $set['tenant_id'];
                                    // $roomId = $set['room_id'];
                                    // $application = $set['application_id'];
                                    $link = "tenants-detail.php?tenantId=$tenantId";
                                    ?>
                                    <div class="pointer flex-row section read-notification"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot seen-dot unseen-dot"> </div>
                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/make-tenant.png" alt="icon">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-normal">
                                                <?php echo $user->getUserName($set['tenant_id']); ?> got registered as tenant.
                                            </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'room-leave-application-submit') { // leave room application
                                    // $tenantId = $set['tenant_id'];
                                    // $roomId = $set['room_id'];
                                    $leaveApplicationId = $set['leave_application_id'];
                                    $link = "leave-application-detail.php?leaveApplicationId=$leaveApplicationId";
                                    ?>
                                    <div class="pointer flex-row section read-notification"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot seen-dot unseen-dot"> </div>
                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/leave-room.png" alt="icon">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-normal">
                                                <?php echo $user->getUserName($set['tenant_id']); ?> wants to leave the room.
                                            </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'tenant-voice-submit') { // tenant voice
                                    $tenantVoiceId = $set['tenant_voice_id'];
                                    $link = "tenant-voice.php?voiceId=$tenantVoiceId";
                                    ?>
                                    <div class="pointer flex-row section read-notification"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot seen-dot unseen-dot"> </div>
                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/speaking.png" alt="icon">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-normal">
                                                <?php echo $user->getUserName($set['tenant_id']); ?> submitted a tenant voice.
                                            </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($set['type'] == 'tenant-voice-response') { // teannt voice response
                                    $tenantVoiceId = $set['tenant_voice_id'];
                                    $link = "tenant-voice.php?voiceId=$tenantVoiceId";
                                    ?>
                                    <div class="pointer flex-row section read-notification"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot seen-dot unseen-dot"> </div>
                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/speaking.png" alt="icon">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-normal">
                                                <?php echo $user->getUserName($set['tenant_id']); ?> replied to a tenant voice.
                                            </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                }elseif ($set['type'] == 'room-review') { // room review
                                    $roomId = $set['room_id'];
                                    $link = "myroom-detail.php?roomId=$roomId";
                                    ?>
                                    <div class="pointer flex-row section read-notification"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot seen-dot unseen-dot"> </div>
                                            <div class="icon-box">
                                                <img src="../../Assests/Icons/review.png" alt="icon">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-normal">
                                                <?php echo $user->getUserName($set['tenant_id']); ?> submitted the room review.
                                            </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    echo $set['type']."<br>";
                                }
                            }
                            ?>

                            <div class="flex-column empty-data-div" id="empty-notification-div">
                                <img src="../../Assests/Icons/empty.png" alt="">
                                <p class="p-normal negative" id="empty-notification-msg"> Notification is empty! </p>
                            </div>

                            <!-- dummy notification -->
                            <div class="pointer flex-row section read-notification hidden"
                                onclick="window.location.href=''">
                                <div class="left flex-row">
                                    <div class="dot seen-dot unseen-dot"> </div>
                                    <div class="icon-box">
                                        <img src="../../Assests/Icons/blank.jpg" alt="icon">
                                    </div>
                                </div>

                                <div class="right flex-column">
                                    <p class="p-normal"> Notification detail 1 </p>
                                    <p class="p-small n-light"> Date </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- script section -->
    <script>
        var userMenuState = false;
        var notificationMenuState = false;

        const userMenu = document.getElementById('user-menu-container-id');
        const notificationMenu = document.getElementById('notification-menu-container-id');

        onload = () => {
            userMenu.style.display = "none";
            notificationMenu.style.display = "none";
        }

        toggleUserMenu = () => {
            if (userMenuState == false) {
                userMenuState = true;
                notificationMenu.style.display = "none";
                userMenu.style.display = "flex";
            } else {
                userMenuState = false;
                userMenu.style.display = "none";
            }
        }

        toggleNotificationMenu = () => {
            if (notificationMenuState == false) {
                notificationMenuState = true;
                userMenuState = false;
                userMenu.style.display = "none";
                notificationMenu.style.display = "flex";
            } else {
                notificationMenuState = false;
                notificationMenu.style.display = "none";
            }
        }
    </script>

    <script src="../../Js/jquery-3.7.1.min.js"> </script>

    <script>
        // $(document).ready(function () {
        var notificationTrigger = 0;
        var notificationElements = $('.notification-element');
        var notificationUnseenElements = $('.notification-unseen-element');
        var notificationSeenElements = $('.notification-seen-element');

        $('#all-notification-trigger').css('background-color', 'whitesmoke');

        $('#all-notification-trigger').click(function () {
            notificationTrigger = 0;
            toggleNotification();
        });

        $('#unseen-notification-trigger').click(function () {
            notificationTrigger = 1;
            toggleNotification();
        });

        $('#seen-notification-trigger').click(function () {
            notificationTrigger = 2;
            toggleNotification();
        });

        toggleNotification = () => {
            notificationElements.hide();

            if (notificationTrigger == 0) {
                unsetTriggerBackground();
                $('#all-notification-trigger').css('background-color', 'lightgray');
                $('#empty-notification-msg')[0].innerHTML = "Notification is empty";
                notificationElements.show();
            } else if (notificationTrigger == 1) {
                unsetTriggerBackground();
                $('#unseen-notification-trigger').css('background-color', 'lightgray');
                $('#empty-notification-msg')[0].innerHTML = "No unseen notification!";
                notificationUnseenElements.show();
            } else {
                unsetTriggerBackground();
                $('#seen-notification-trigger').css('background-color', 'lightgray');
                $('#empty-notification-msg')[0].innerHTML = "No seen notification!";
                notificationSeenElements.show();
            }

            var notificationVisibleElements = $('.notification-element:visible');

            $('#empty-notification-div').hide();
            if (notificationVisibleElements.length == 0)
                $('#empty-notification-div').show();

            function unsetTriggerBackground() {
                $('#all-notification-trigger').css('background-color', 'unset');
                $('#unseen-notification-trigger').css('background-color', 'unset');
                $('#seen-notification-trigger').css('background-color', 'unset');
            }
        }

        toggleNotification();
        // });
    </script>
</body>

</html>