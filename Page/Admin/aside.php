<?php
if (!isset($_SESSION['adminId']))
    header('location: login.php');

include_once '../../Class/connection_class.php';
include_once '../../Class/notification_class.php';

$notification = new Notification();

$notificationCount = $notification->countNotification('admin', $_SESSION['adminId'], "all");
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

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <!-- script section -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <div class="body-container flex-row">
        <!-- side menu -->
        <aside class="aside-menu-container flex-column" id="menu-container">
            <div class="logo-section">
                <a href="dashboard.php">
                    <img src="../../Assests/Images/rentrover-logo-rectangle.png" alt="">
                </a>
            </div>

            <div class="aside-menu-section flex-column">
                <!-- dashboard -->
                <div class="section flex-row pointer" id="dashboard-menu-id"
                    onclick="window.location.href='dashboard.php'">
                    <div class="left">
                        <img src="../../Assests/Icons/menu.png" class="icon-class" alt="">
                    </div>
                    <div class="right">
                        <p class="p-normal"> Dashboard </p>
                    </div>
                </div>

                <!-- users -->
                <div class="section flex-row pointer" id="user-menu-id" onclick="window.location.href='users.php'">
                    <div class="left">
                        <img src="../../Assests/Icons/user-square.svg" class="icon-class" alt="">
                    </div>
                    <div class="right">
                        <p class="p-normal"> Users </p>
                    </div>
                </div>

                <!-- house -->
                <div class="section flex-row pointer" id="house-menu-id" onclick="window.location.href='houses.php'">
                    <div class="left">
                        <img src="../../Assests/Icons/building.png" class="icon-class" alt="">
                    </div>
                    <div class="right">
                        <p class="p-normal"> Houses </p>
                    </div>
                </div>

                <!-- room -->
                <div class="section flex-row pointer" id="room-menu-id" onclick="window.location.href='rooms.php'">
                    <div class="left">
                        <img src="../../Assests/Icons/room.png" class="icon-class" alt="">
                    </div>
                    <div class="right">
                        <p class="p-normal"> Rooms </p>
                    </div>
                </div>

                <!-- user voice -->
                <div class="section flex-row pointer" id="user-voice-menu-id"
                    onclick="window.location.href='user-voice.php'">
                    <div class="left">
                        <img src="../../Assests/Icons/speaking.png" class="icon-class" alt="">
                    </div>
                    <div class="right">
                        <p class="p-normal"> User Voice </p>
                    </div>
                </div>

                <!-- custom room application -->
                <div class="section flex-row pointer" id="custom-room-menu-id"
                    onclick="window.location.href='custom-room-application.php'">
                    <div class="left">
                        <img src="../../Assests/Icons/setting.svg" class="icon-class" alt="">
                    </div>
                    <div class="right">
                        <p class="p-normal"> Custom Room </p>
                    </div>
                </div>

                <!-- anouncement -->
                <div class="section flex-row pointer" id="announcement-menu-id"
                    onclick="window.location.href='announcement.php'">
                    <div class="left">
                        <img src="../../Assests/Icons/announcement.png" class="icon-class" alt="">
                    </div>
                    <div class="right">
                        <p class="p-normal"> Announcements </p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- top nav & menus container -->
        <nav class="nav-menus-container flex-column">
            <!-- nav section -->
            <div class="nav-container flex-row">
                <div class="flex-row notification-profile-container">
                    <!-- notification section -->
                    <div class="nav-notification-container flex-column pointer" onclick="toggleNotificationMenu()">
                        <div class="notification-icon-div flex-row">
                            <img src="../../Assests/Icons/notification.svg" alt="">
                        </div>

                        <!-- notification count -->
                        <div class="notification-count-div flex-row">
                            <p class="p-form">
                                <?php
                                echo $notificationCount < 9 ? $notificationCount : '9<sup>+</sup>';
                                ?>
                                <!-- 9+ -->
                            </p>
                        </div>
                    </div>

                    <!-- profile section -->
                    <div class="profile-container flex-row pointer" onclick="toggleUserMenu()">
                        <img src="../../Assests/Icons/user-square.svg" alt="Admin photo" class="user-photo">
                    </div>
                </div>
            </div>

            <!-- user menu & notification -->
            <div class="menus-container">
                <div class="user-menu-container" id="user-menu-container-id">
                    <div class="user-menu-div flex-row">
                        <div class="user-menu shadow flex-column" id="menu-container">
                            <div class="section flex-row pointer" onclick="window.location.href='logout.php'">
                                <img src="../../Assests/Icons/logout.svg" alt="">
                                <p class="p-normal"> Log Out </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- notification -->
                <div class="notification-menu-container" id="notification-menu-container-id">
                    <div class="notification-menu-div flex-row">
                        <div class="notification-container flex-column shadow" id="notification-container">
                            <!-- top section -->
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

                            <hr style="background-color: lightgray;">

                            <?php
                            $notificationSet = $notification->fetchNotification("admin", $_SESSION['adminId']);
                            foreach ($notificationSet as $set) {
                                if ($set['type'] == 'user-registration') {
                                    $userId = ($set['tenant_id'] == 0) ? $set['landlord_id'] : $set['tenant_id'];
                                    $link = "user-detail.php?userId=$userId";
                                    ?>
                                    <!-- new user joined notification -->
                                    <div class="pointer flex-row section notification-element <?php echo ($set['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                                        onclick="window.location.href='<?php echo $link; ?>'">
                                        <div class="left flex-row">
                                            <div class="dot"> </div>

                                            <div class="icon-box">
                                                <img src="../../Assests/icons/notification_icon_user_registration.png" alt=""
                                                    class="notify-image">
                                            </div>
                                        </div>

                                        <div class="right flex-column">
                                            <p class="p-form">
                                                A new user has joined as a
                                                <?php echo $user->getRole($userId); ?>.
                                            </p>
                                            <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php
                                } else if ($set['type'] == 'house-registration') {
                                    $landlordId = $set['landlord_id'];
                                    $houseId = $set['house_id'];
                                    $link = "house-detail.php?houseId=$houseId";
                                    ?>
                                        <div class="pointer flex-row section notification-element <?php echo ($set['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                                            onclick="window.location.href='<?php echo $link; ?>'">
                                            <div class="left flex-row">
                                                <div class="dot"> </div>

                                                <div class="icon-box">
                                                    <img src="../../Assests/icons/notification_icon_house_registration.png" alt=""
                                                        class="notify-image">
                                                </div>
                                            </div>

                                            <div class="right flex-column">
                                                <p class="p-form">
                                                <?php echo $user->getUserName($landlordId); ?> registered a new house.
                                                </p>
                                                <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php
                                } else if ($set['type'] == 'room-registration') {
                                    $landlordId = $set['landlord_id'];
                                    $roomId = $set['room_id'];
                                    $link = "room-detail.php?roomId=$roomId";
                                    ?>
                                            <div class="pointer flex-row section notification-element <?php echo ($set['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                                                onclick="window.location.href='<?php echo $link; ?>'">
                                                <div class="left flex-row">
                                                    <div class="dot"> </div>

                                                    <div class="icon-box">
                                                        <img src="../../Assests/icons/notification_icon_room_registration.png" alt=""
                                                            class="notify-image">
                                                    </div>
                                                </div>

                                                <div class="right flex-column">
                                                    <p class="p-form">
                                                <?php echo $user->getUserName($landlordId); ?> registered a new room.
                                                    </p>
                                                    <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                                    </p>
                                                </div>
                                            </div>
                                    <?php
                                } else if ($set['type'] == 'user-voice') {
                                    $userId = ($set['tenant_id'] == 0) ? $set['landlord_id'] : $set['tenant_id'];
                                    $feedbackId = $set['feedback_id'];
                                    $link = "user-voice.php";
                                    ?>
                                                <div class="pointer flex-row section notification-element <?php echo ($set['seen'] == 1) ? "seen-notification" : "unseen-notification"; ?>"
                                                    onclick="window.location.href='<?php echo $link; ?>'">
                                                    <div class="left flex-row">
                                                        <div class="dot"> </div>

                                                        <div class="icon-box">
                                                            <img src="../../Assests/icons/notification_icon_user_voice.png" alt=""
                                                                class="notify-image">
                                                        </div>
                                                    </div>

                                                    <div class="right flex-column">
                                                        <p class="p-form">
                                                <?php echo $user->getUserName($userId) . " (" . ucfirst($user->getRole($userId)) . ")"; ?>
                                                            submitted a voice.
                                                        </p>
                                                        <p class="p-small n-light">
                                                <?php echo $set['date_time']; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                    <?php
                                }
                            }
                            ?>


                            <div class="flex-column empty-data-div" id="empty-notification-div">
                                <img src="../../Assests/Icons/empty.png" alt="">
                                <p class="p-normal negative" id="empty-notification-msg"> Notification is empty! </p>
                            </div>

                            <!-- dummy notification -->
                            <div class="pointer flex-row section unread-noticication hidden"
                                onclick="window.location.href=''">
                                <div class="left flex-row">
                                    <div class="dot"> </div>

                                    <div class="icon-box">
                                        <img src="../../Assests/Icons/blank.jpg" alt="icon">
                                    </div>
                                </div>

                                <div class="right flex-column">
                                    <p class="p-normal"> Notification detail 2 </p>
                                    <p class="p-small n-light"> Date </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>


    <!-- script -->
    <script>
        $(document).ready(function () {

            var userMenuState = false;
            var notificationTrigger = 0;
            var notificationMenuState = false;

            const userMenu = $('#user-menu-container-id');
            const notificationMenu = $('#notification-menu-container-id');
            const logoutDialog = $('#logout-dialog-container');

            const allNotificationTrigger = $('#all-notification-trigger');
            const unseenNotificationTrigger = $('#unseen-notification-trigger');
            const seenNotificationTrigger = $('#seen-notification-trigger');

            var notificationElement = $('.notification-element');
            var notificationUnseenElement = $('.unseen-notification');
            var notificationSeenElement = $('.seen-notification');

            // on window load
            userMenu.hide();
            logoutDialog.hide();
            notificationMenu.hide();

            toggleUserMenu = () => {
                if (userMenuState == false) {
                    userMenuState = true;
                    userMenu.show();
                    notificationMenu.hide();
                } else {
                    userMenuState = false;
                    userMenu.hide();
                }
            }

            toggleNotificationMenu = () => {
                if (notificationMenuState == false) {
                    notificationMenuState = true;
                    userMenuState = false;
                    userMenu.hide();
                    notificationMenu.show();
                    toggleNotification();
                } else {
                    notificationMenuState = false;
                    notificationMenu.hide();
                }
            }

            logout = () => {
                logoutDialog.show();
            }

            hideLogoutDialog = () => {
                userMenu.hide();
                logoutDialog.hide();
                userMenuState = false;
            }

            // notification triggers
            allNotificationTrigger.click(function () {
                notificationTrigger = 0;
                toggleNotification();
            });

            unseenNotificationTrigger.click(function () {
                notificationTrigger = 1;
                toggleNotification();
            });

            seenNotificationTrigger.click(function () {
                notificationTrigger = 2;
                toggleNotification();
            });

            toggleNotification = () => {
                notificationElement.hide();

                if (notificationTrigger == 1) {
                    unsetTriggerBackground();
                    $('#unseen-notification-trigger').css('background-color', 'lightgray');
                    $('#empty-notification-msg')[0].innerHTML = "No unseen notification!";
                    notificationUnseenElement.show();
                } else if (notificationTrigger == 2) {
                    unsetTriggerBackground();
                    $('#seen-notification-trigger').css('background-color', 'lightgray');
                    $('#empty-notification-msg')[0].innerHTML = "No seen notification!";
                    notificationSeenElement.show();
                } else {
                    notificationTrigger = 0;
                    unsetTriggerBackground();
                    $('#all-notification-trigger').css('background-color', 'lightgray');
                    $('#empty-notification-msg')[0].innerHTML = "Notification is empty";
                    notificationElement.show();
                }

                var notificationElementCount = $('.notification-element:visible');

                if (notificationElementCount.length == 0)
                    $('#empty-notification-div').show();
                else
                    $('#empty-notification-div').hide();

                function unsetTriggerBackground() {
                    $('#all-notification-trigger').css('background-color', 'unset');
                    $('#unseen-notification-trigger').css('background-color', 'unset');
                    $('#seen-notification-trigger').css('background-color', 'unset');
                }
            }

            toggleNotification();
        });

    </script>
</body>

</html>