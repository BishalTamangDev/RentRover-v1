<?php
// staring session
if (!session_start())
    session_start();

// setting the values
if (!isset($_SESSION['tenantUserId']))
    header('location: ../index.php');

// including files
include '../../Class/user_class.php';
include '../../Class/notification_class.php';
include '../../Class/house_class.php';
include '../../class/functions.php';
include '../../Class/wishlist_class.php';

// creating the object
$user = new User();
$houseObj = new House();
$wishlist = new Wishlist();
$notification = new Notification();
$notificationObj = new Notification();

$user->userId = $_SESSION['tenantUserId'];
$user->fetchSpecificRow($user->userId);

// wishlist
$wishlistCount = $wishlist->countWishes($_SESSION['tenantUserId']);

// getting notification count
$notificationCount = $notification->countNotification("tenant", $user->userId, "unseen");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/tenant/navbar.css">
    <link rel="stylesheet" href="../../CSS/common/notification.css">
    <link rel="stylesheet" href="../../CSS/tenant/notification.css">

    <!-- title -->
    <title> Notification </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- js section -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <!-- navigation section -->
    <?php include 'navbar.php'; ?>

    <!-- heading -->
    <div class="heading-container container">
        <div class="heading-div div">
            <p class="p-larger f-bold"> Notification </p>
        </div>
    </div>

    <div class="flex-column container notification-main-container">
        <!-- filter buttons -->
        <div class="div flex-row notification-filter-container">
            <div class="filter" id="page-all-notification-trigger">
                <p class="p-normal"> All </p>
            </div>

            <div class="filter" id="page-unseen-notification-trigger">
                <p class="p-normal"> Unseen </p>
            </div>

            <div class="filter" id="page-seen-notification-trigger">
                <p class="p-normal"> Seen </p>
            </div>
        </div>

        <!-- notification container -->
        <div class="flex-container flex-column notification-container" id="notification-container">
            <?php
            $notificationSet = $notificationObj->fetchNotification("tenant", $_SESSION['tenantUserId']);
            foreach ($notificationSet as $set) {
                // new user joined notification
                if ($set['type'] == 'user-registration') { // user registration
                    $link = "profile-view.php";
                    ?>
                    <div
                        class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                        <div class="left">
                            <div class="notify-image-container flex-row">
                                <img src="../../Assests/Icons/notification_icon_user_registration.png" alt=""
                                    class="notify-image">
                            </div>
                        </div>

                        <div class="flex-column middle">
                            <p class="p-normal n-normal"> You joined RentRover. </p>

                            <p class="p-small notification-date">
                                <?php echo $set['date_time'] ?>
                            </p>

                        </div>

                        <div class="right">
                            <abbr title="Delete notification">
                                <a href="#">
                                    <img src="../../Assests/Images/Delete-black.png" alt="" class="icon-class">
                                </a>
                            </abbr>
                        </div>
                    </div>

                    <?php
                } elseif ($set['type'] == 'user-verify') { // user verify
                    $link = "profile-view.php";
                    ?>
                    <div
                        class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                        <div class="left">
                            <div class="notify-image-container flex-row">
                                <img src="../../Assests/Icons/verified.png" alt="" class="notify-image">
                            </div>
                        </div>

                        <div class="flex-column middle">
                            <p class="p-normal n-normal"> Your account has been verified. </p>

                            <p class="p-small notification-date">
                                <?php echo $set['date_time'] ?>
                            </p>

                        </div>

                        <div class="right">
                            <abbr title="Delete notification">
                                <a href="#">
                                    <img src="../../Assests/Images/Delete-black.png" alt="" class="icon-class">
                                </a>
                            </abbr>
                        </div>
                    </div>

                    <?php
                } elseif ($set['type'] == 'user-suspend') { // user suspend
                    $link = "profile-view.php";
                    ?>
                    <div
                        class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                        <div class="left">
                            <div class="notify-image-container flex-row">
                                <img src="../../Assests/Icons/report.png" alt="" class="notify-image">
                            </div>
                        </div>

                        <div class="flex-column middle">
                            <p class="p-normal n-normal"> You accound has been suspended. </p>

                            <p class="p-small notification-date">
                                <?php echo $set['date_time'] ?>
                            </p>

                        </div>

                        <div class="right">
                            <abbr title="Delete notification">
                                <a href="#">
                                    <img src="../../Assests/Images/Delete-black.png" alt="" class="icon-class">
                                </a>
                            </abbr>
                        </div>
                    </div>

                    <?php
                } elseif ($set['type'] == 'house-registration') { // new house notification
                    $link = "myhouse-detail.php?houseId=" . $set['house_id'];
                    ?>
                    <div
                        class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                        <div class="left">
                            <div class="notify-image-container flex-row">
                                <img src="../../Assests/Icons/notification_icon_house_registration.png" alt=""
                                    class="notify-image">
                            </div>
                        </div>

                        <div class="flex-column middle">
                            <p class="p-normal n-normal"> You registered a new house. </p>

                            <p class="p-small notification-date">
                                <?php echo $set['date_time'] ?>
                            </p>

                            <div class="flex-row button-section">
                                <button onclick="window.location.href='<?php echo $link ?>'"> See House Detail </button>
                            </div>
                        </div>

                        <div class="right">
                            <abbr title="Delete notification">
                                <a href="delete-notification.php?notificationId=<?php echo $set['notification_id']; ?>">
                                    <img src="../../Assests/Images/Delete-black.png" alt="">
                                </a>
                            </abbr>
                        </div>
                    </div>
                    <?php
                } elseif ($set['type'] == 'room-registration') { // new house notification
                    $link = "myroom-detail.php?roomId=" . $set['room_id'];
                    ?>
                    <div
                        class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                        <div class="left">
                            <div class="notify-image-container flex-row">
                                <img src="../../Assests/Icons/notification_icon_room_registration.png" alt=""
                                    class="notify-image">
                            </div>
                        </div>

                        <div class="flex-column middle">
                            <p class="p-normal n-normal"> You registered a new room. </p>

                            <p class="p-small notification-date">
                                <?php echo $set['date_time'] ?>
                            </p>

                            <div class="flex-row button-section">
                                <button onclick="window.location.href='<?php echo $link ?>'"> See Room Detail </button>
                            </div>
                        </div>

                        <div class="right">
                            <abbr title="Delete notification">
                                <a href="delete-notification.php?notificationId=<?php echo $set['notification_id']; ?>">
                                    <img src="../../Assests/Images/Delete-black.png" alt="">
                                </a>
                            </abbr>
                        </div>
                    </div>
                    <?php
                } elseif ($set['type'] == 'room-application-submit') { // room application submission notification
                    $applicationId = $set['application_id'];
                    $tenantId = $set['tenant_id'];
                    $roomId = $set['room_id'];

                    $applicantName = $user->getUserName($tenantId);

                    $link = "application-detail.php?applicationId=$applicationId&tenantId=$tenantId&roomId=$roomId";
                    ?>
                    <div
                        class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                        <div class="left">
                            <div class="notify-image-container flex-row">
                                <img src="../../Assests/Icons/room-apply.png" alt="" class="notify-image">
                            </div>
                        </div>

                        <div class="flex-column middle">
                            <p class="p-normal n-normal">
                                <?php echo $applicantName; ?> applied for you room.
                            </p>

                            <p class="p-small notification-date">
                                <?php echo $set['date_time'] ?>
                            </p>

                            <div class="flex-row button-section">
                                <button onclick="window.location.href='<?php echo $link ?>'"> See Application Detail </button>
                            </div>
                        </div>

                        <div class="right">
                            <abbr title="Delete notification">
                                <a href="delete-notification.php?notificationId=<?php echo $set['notification_id']; ?>">
                                    <img src="../../Assests/Images/Delete-black.png" alt="">
                                </a>
                            </abbr>
                        </div>
                    </div>
                    <?php
                } elseif ($set['type'] == 'tenant-voice-response') { // announcement
                    $applicationId = $set['application_id'];
                    $tenantId = $set['tenant_id'];
                    $roomId = $set['room_id'];

                    $applicantName = $user->getUserName($tenantId);

                    $voiceId = $set['tenant_voice_id'];

                    $link = "my-voice.php?voiceId=$voiceId";
                    ?>
                    <div
                        class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                        <div class="left">
                            <div class="notify-image-container flex-row">
                                <img src="../../Assests/Icons/speaking.png" alt="" class="notify-image">
                            </div>
                        </div>

                        <div class="flex-column middle">
                            <p class="p-normal n-normal"> The landlord replied to you issue.. </p>

                            <p class="p-small notification-date">
                                <?php echo $set['date_time'] ?>
                            </p>

                            <div class="flex-row button-section">
                                <button onclick="window.location.href='<?php echo $link ?>'"> See reply </button>
                            </div>
                        </div>

                        <div class="right">
                            <abbr title="Delete notification">
                                <a href="delete-notification.php?notificationId=<?php echo $set['notification_id']; ?>">
                                    <img src="../../Assests/Images/Delete-black.png" alt="">
                                </a>
                            </abbr>
                        </div>
                    </div>
                    <?php
                } elseif ($set['type'] == 'room-application-accept') { // announcement
                    $applicationId = $set['application_id'];
                    $tenantId = $set['tenant_id'];
                    $roomId = $set['room_id'];

                    $applicantName = $user->getUserName($tenantId);

                    $link = "application-detail.php?applicationId=$applicationId&tenantId=$tenantId&roomId=$roomId";
                    ?>
                    <div
                        class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                        <div class="left">
                            <div class="notify-image-container flex-row">
                                <img src="../../Assests/Icons/application-accepted.png" alt="" class="notify-image">
                            </div>
                        </div>

                        <div class="flex-column middle">
                            <p class="p-normal n-normal"> Your application got accepted. </p>

                            <p class="p-small notification-date">
                                <?php echo $set['date_time'] ?>
                            </p>

                            <div class="flex-row button-section">
                                <button onclick="window.location.href='<?php echo $link ?>'"> See Announcement </button>
                            </div>
                        </div>

                        <div class="right">
                            <abbr title="Delete notification">
                                <a href="delete-notification.php?notificationId=<?php echo $set['notification_id']; ?>">
                                    <img src="../../Assests/Images/Delete-black.png" alt="">
                                </a>
                            </abbr>
                        </div>
                    </div>
                    <?php
                } elseif ($set['type'] == 'room-application-make-tenant') { // announcement
                    $applicationId = $set['application_id'];
                    $tenantId = $set['tenant_id'];
                    $roomId = $set['room_id'];

                    $applicantName = $user->getUserName($tenantId);

                    $link = "application-detail.php?applicationId=$applicationId&tenantId=$tenantId&roomId=$roomId";
                    ?>
                    <div
                        class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                        <div class="left">
                            <div class="notify-image-container flex-row">
                                <img src="../../Assests/Icons/make-tenant.png" alt="" class="notify-image">
                            </div>
                        </div>

                        <div class="flex-column middle">
                            <p class="p-normal n-normal"> You got registered as a tenant. </p>

                            <p class="p-small notification-date">
                                <?php echo $set['date_time'] ?>
                            </p>

                            <div class="flex-row button-section">
                                <button onclick="window.location.href='<?php echo $link ?>'"> See Announcement </button>
                            </div>
                        </div>

                        <div class="right">
                            <abbr title="Delete notification">
                                <a href="delete-notification.php?notificationId=<?php echo $set['notification_id']; ?>">
                                    <img src="../../Assests/Images/Delete-black.png" alt="">
                                </a>
                            </abbr>
                        </div>
                    </div>
                    <?php
                } elseif ($set['type'] == 'announcement') { // announcement
                    $applicationId = $set['application_id'];
                    $tenantId = $set['tenant_id'];
                    $roomId = $set['room_id'];

                    $applicantName = $user->getUserName($tenantId);

                    $link = "account.php?task=announcement";
                    ?>
                    <div
                        class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                        <div class="left">
                            <div class="notify-image-container flex-row">
                                <img src="../../Assests/Icons/room-apply.png" alt="" class="notify-image">
                            </div>
                        </div>

                        <div class="flex-column middle">
                            <p class="p-normal n-normal"> The landlord has an announcement. </p>

                            <p class="p-small notification-date">
                                <?php echo $set['date_time'] ?>
                            </p>

                            <div class="flex-row button-section">
                                <button onclick="window.location.href='<?php echo $link ?>'"> See Announcement </button>
                            </div>
                        </div>

                        <div class="right">
                            <abbr title="Delete notification">
                                <a href="delete-notification.php?notificationId=<?php echo $set['notification_id']; ?>">
                                    <img src="../../Assests/Images/Delete-black.png" alt="">
                                </a>
                            </abbr>
                        </div>
                    </div>
                    <?php
                } else {
                    echo $set['type'] . '<br>';
                }
            }
            ?>
        </div>

        <div class="div flex-column empty-data-div" id="page-empty-notification-div">
            <img src="../../Assests/Icons/empty.png" alt="">
            <p class="p-normal negative" id="page-empty-notification-msg"> Found nothing! </p>
        </div>
    </div>


    <!-- js section -->
    <script>
        var userMenuState = false;
        var notificationMenuState = false;

        const userMenu = document.getElementById('menu-container');
        const notificationMenu = document.getElementById('notification-container');

        onload = () => {
            userMenu.style = "display:none";
            notificationMenu.style = "display:none";
        }

        toggleUserMenu = () => {
            if (userMenuState == false) {
                userMenuState = true;
                notificationMenuState = false;
                notificationMenu.style = "display:none";
                userMenu.style = "display:flex";
            } else {
                userMenuState = false;
                userMenu.style = "display:none";
            }
        }

        toggleNotificationMenu = () => {
            if (notificationMenuState == false) {
                notificationMenuState = true;
                userMenuState = false;
                userMenu.style = "display:none";
                notificationMenu.style = "display:flex";
            } else {
                notificationMenuState = false;
                notificationMenu.style = "display:none";
            }
        }
    </script>

    <script>
        var pageNotificationTrigger = 0;

        $('#page-all-notification-trigger').click(function () {
            pageNotificationTrigger = 0;
            togglePageNotification();
        });

        $('#page-unseen-notification-trigger').click(function () {
            pageNotificationTrigger = 1;
            togglePageNotification();
        });

        $('#page-seen-notification-trigger').click(function () {
            pageNotificationTrigger = 2;
            togglePageNotification();
        });

        togglePageNotification = () => {
            pageNotificationElements = $('.page-notification-element');
            pageNotificationElements.hide();

            if (pageNotificationTrigger == 0) {
                $('#page-all-notification-trigger').css('background-color', 'rgb(233, 233, 233)');
                $('#page-unseen-notification-trigger').css('background-color', 'white');
                $('#page-seen-notification-trigger').css('background-color', 'white');
                $('#page-empty-notification-msg')[0].innerHTML = "Notification is empty";
                pageNotificationElements.show();
            } else if (pageNotificationTrigger == 1) {
                $('.page-unseen-notification').show();
                $('#page-all-notification-trigger').css('background-color', 'white');
                $('#page-unseen-notification-trigger').css('background-color', 'rgb(233, 233, 233)');
                $('#page-seen-notification-trigger').css('background-color', 'white');
                $('#page-empty-notification-msg')[0].innerHTML = "No unseen notification!";
            } else {
                $('.page-seen-notification').show();
                $('#page-all-notification-trigger').css('background-color', 'white');
                $('#page-unseen-notification-trigger').css('background-color', 'white');
                $('#page-seen-notification-trigger').css('background-color', 'rgb(233, 233, 233)');
                $('#page-empty-notification-msg')[0].innerHTML = "No seen notification!";
            }

            var pageNotificationElementsCount = $('.page-notification-element:visible').length;

            if (pageNotificationElementsCount == 0)
                $('#page-empty-notification-div').show();
            else
                $('#page-empty-notification-div').hide();
        }

        togglePageNotification();
    </script>
</body>

</html>