<!-- starting session -->
<?php

// starting session
if (!session_start())
    session_start();

// redirecting to login page is session variable is not set
if (!isset($_SESSION['adminId']))
    header("Location: login.php");

// including external files
include '../../class/user_class.php';
include '../../class/notification_class.php';
include '../../class/functions.php';
include '../../class/house_class.php';

// updating notification table
// updateNotificationTable();
// updateNotificationTableEmail();

// creating objects
$user = new User();
$houseObj = new House();
$notification = new Notification();
$notificationObj = new Notification();
$url = $_SERVER['REQUEST_URI'];

// global variables
$userPhoto = "NULL";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> Notification</title>

    <!-- main css import -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/common/notification.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- jquery -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <?php
    include 'aside.php';
    ?>

    <div class="flex-row body-container">
        <!-- empty aside -->
        <aside class="empty-section"> </aside>

        <!-- content section -->
        <article class="content-article">
            <!-- heading -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold negative"> Notification </p>
            </div>

            <!-- filter buttons -->
            <div class="flex-row container content-container notification-filter-container">
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

            <div class="flex-column notification-container" id="notification-container">
                <?php
                $notificationSet = $notificationObj->fetchNotification("admin", $_SESSION['adminId']);
                foreach ($notificationSet as $set) {
                    // new user joined notification
                    if ($set['type'] == "user-registration") {
                        $link = ($set['landlord_id'] == 0) ? "user-detail.php?userId=" . $set['tenant_id'] : "user-detail.php?userId=" . $set['landlord_id'];
                        ?>
                        <div
                            class="flex-row notification-card page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                            <div class="left">
                                <div class="notify-image-container flex-row">
                                    <img src="../../Assests/Icons/notification_icon_user_registration.png" alt=""
                                        class="notify-image">
                                </div>
                            </div>

                            <div class="flex-column middle">
                                <p class="p-normal n-normal">
                                    A new user has joined as a
                                    <?php echo ($set['landlord_id'] == 0) ? "tenant" : "landlord"; ?>.
                                </p>

                                <p class="p-small notification-date">
                                    <?php echo $set['date_time'] ?>
                                </p>

                                <div class="button-section">
                                    <button class="button" onclick="window.location.href='<?php echo $link; ?>'"> See profile
                                    </button>
                                </div>
                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a
                                        href="../operation/notification-operation.php?task=remove&id=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
                                    </a>
                                </abbr>
                            </div>
                        </div>
                        <?php
                    } elseif ($set['type'] == "house-registration") { // new house notification
                        $houselink = "house-detail.php?houseId=" . $set['house_id'];
                        $landlordlink = "user-detail.php?userId=" . $set['landlord_id'];
                        ?>
                        <div
                            class="flex-row notification-card page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                            <div class="left">
                                <div class="notify-image-container flex-row">
                                    <img src="../../Assests/Icons/notification_icon_house_registration.png" alt=""
                                        class="notify-image">
                                </div>
                            </div>

                            <div class="flex-column middle">
                                <p class="p-normal n-normal">
                                    <?php echo $user->getUserName($set['landlord_id']); ?> registered a new house.
                                </p>

                                <p class="p-small notification-date">
                                    <?php echo $set['date_time'] ?>
                                </p>

                                <div class="flex-row button-section">
                                    <button class="button button-1" onclick="window.location.href='<?php echo $houselink; ?>'">
                                        House Info </button>
                                    <button class="button button-2"
                                        onclick="window.location.href='<?php echo $landlordlink; ?>'"> Landlord Info </button>
                                </div>
                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a
                                        href="../operation/notification-operation.php?task=remove&id=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
                                    </a>
                                </abbr>
                            </div>
                        </div>
                        <?php
                    } elseif ($set['type'] == "room-registration") { // room registration notification
                        $roomLink = "room-detail.php?roomId=" . $set['room_id'];
                        $landlordlink = "user-detail.php?userId=" . $set['landlord_id'];
                        ?>
                        <div
                            class="flex-row notification-card page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                            <div class="left">
                                <div class="notify-image-container flex-row">
                                    <img src="../../Assests/Icons/notification_icon_room_registration.png" alt=""
                                        class="notify-image">
                                </div>
                            </div>

                            <div class="flex-column middle">
                                <p class="p-normal n-normal">
                                    <?php echo $user->getUserName($set['landlord_id']); ?> registered a new house.
                                </p>

                                <p class="p-small notification-date">
                                    <?php echo $set['date_time'] ?>
                                </p>

                                <div class="flex-row button-section">
                                    <button class="button button-1" onclick="window.location.href='<?php echo $roomLink; ?>'">
                                        Room Info </button>
                                    <button class="button button-2"
                                        onclick="window.location.href='<?php echo $landlordlink; ?>'"> Landlord Info </button>
                                </div>
                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a
                                        href="../operation/notification-operation.php?task=remove&id=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
                                    </a>
                                </abbr>
                            </div>
                        </div>
                        <?php
                    } elseif ($set['type'] == "user-voice") {
                        $userId = ($set['tenant_id'] == 0) ? $set['landlord_id'] : $set['tenant_id'];
                        $feedbackId = $set['feedback_id'];
                        $voiceLink = "user-voice.php?voiceId=$feedbackId";
                        $userLink = "user-detail.php?userId=$userId";
                        ?>
                        <div
                            class="flex-row notification-card page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                            <div class="left">
                                <div class="notify-image-container flex-row">
                                    <img src="../../Assests/Icons/notification_icon_user_voice.png" alt="" class="notify-image">
                                </div>
                            </div>

                            <div class="flex-column middle">
                                <p class="p-normal n-normal">
                                    <?php echo $user->getUserName($userId) . " (" . ucfirst($user->getRole($userId)) . ")"; ?>
                                    submissted a voice.
                                </p>

                                <p class="p-small notification-date">
                                    <?php echo $set['date_time'] ?>
                                </p>

                                <div class="flex-row button-section">
                                    <button class="button button-1" onclick="window.location.href='<?php echo $voiceLink; ?>'">
                                        Voice Info </button>
                                    <button class="button button-2" onclick="window.location.href='<?php echo $userLink; ?>'">
                                        User Info </button>
                                </div>
                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a
                                        href="../operation/notification-operation.php?task=remove&id=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
                                    </a>
                                </abbr>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <div class="flex-column empty-data-div" id="page-empty-notification-div">
                <img src="../../Assests/Icons/empty.png" alt="">
                <p class="p-normal negative" id="page-empty-notification-msg"> Found nothing! </p>
            </div>
        </article>
    </div>

    <!-- script -->
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