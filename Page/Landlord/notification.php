<?php
// staring session
if (!session_start())
    session_start();

// setting the values
if (!isset($_SESSION['landlordUserId']))
    header('location: ../../index.php');

// including files
include '../../Class/user_class.php';
include '../../Class/notification_class.php';
include '../../Class/house_class.php';
include '../../class/functions.php';

// creating the object
$user = new User();
$houseObj = new House();
$notificationObj = new Notification();

$user->userId = $_SESSION['landlordUserId'];
$user->fetchSpecificRow($user->userId);

$url = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/common/notification.css">

    <!-- title -->
    <title> Notification </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="flex-row body-container">
        <!-- empty aside -->
        <aside class="empty-section"> </aside>

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

            <!-- notification container -->
            <div class="notification-container flex-container flex-column" id="notification-container">
                <?php
                $notificationSet = $notificationObj->fetchNotification("landlord", $user->userId);
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
                                    <a href="operation/notification-op.php?task=remove&notificationId=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
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
                                <p class="p-normal n-normal"> You account has been suspended. </p>

                                <p class="p-small notification-date">
                                    <?php echo $set['date_time'] ?>
                                </p>

                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a href="operation/notification-op.php?task=remove&notificationId=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
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
                                <p class="p-normal n-normal"> You account has been verified. </p>

                                <p class="p-small notification-date">
                                    <?php echo $set['date_time'] ?>
                                </p>

                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a href="operation/notification-op.php?task=remove&notificationId=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
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
                                    <a href="operation/notification-op.php?task=remove&notificationId=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
                                    </a>
                                </abbr>
                            </div>
                        </div>
                        <?php
                    } elseif ($set['type'] == 'house-verify') { // new house notification
                        $link = "myhouse-detail.php?houseId=" . $set['house_id'];
                        ?>
                        <div
                            class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                            <div class="left">
                                <div class="notify-image-container flex-row">
                                    <img src="../../Assests/Icons/notification_icon_house_registration.png" alt=""
                                        class="notify-image">
                                    <img src="../../Assests/Icons/verified.png" alt="" class="notify-image">
                                </div>
                            </div>

                            <div class="flex-column middle">
                                <p class="p-normal n-normal"> You house has been verified. </p>

                                <p class="p-small notification-date">
                                    <?php echo $set['date_time'] ?>
                                </p>

                                <div class="flex-row button-section">
                                    <button onclick="window.location.href='<?php echo $link ?>'"> See House Detail </button>
                                </div>
                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a href="operation/notification-op.php?task=remove&notificationId=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
                                    </a>
                                </abbr>
                            </div>
                        </div>
                        <?php
                    } elseif ($set['type'] == 'house-suspend') { // new house notification
                        $link = "myhouse-detail.php?houseId=" . $set['house_id'];
                        ?>
                        <div
                            class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                            <div class="left">
                                <div class="notify-image-container flex-row">
                                    <img src="../../Assests/Icons/report.png" alt="" class="notify-image">
                                </div>
                            </div>

                            <div class="flex-column middle">
                                <p class="p-normal n-normal"> You house has been suspended. </p>

                                <p class="p-small notification-date">
                                    <?php echo $set['date_time'] ?>
                                </p>

                                <div class="flex-row button-section">
                                    <button onclick="window.location.href='<?php echo $link ?>'"> See House Detail </button>
                                </div>
                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a href="operation/notification-op.php?task=remove&notificationId=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
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
                                    <a href="operation/notification-op.php?task=remove&notificationId=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
                                    </a>
                                </abbr>
                            </div>
                        </div>
                        <?php
                    } elseif ($set['type'] == 'room-verify') { // room verify
                        $link = "myroom-detail.php?roomId=" . $set['room_id'];
                        ?>
                        <div
                            class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                            <div class="left">
                                <div class="notify-image-container flex-row">
                                    <img src="../../Assests/Icons/verified.png" alt="" class="notify-image">
                                </div>
                            </div>

                            <div class="flex-column middle">
                                <p class="p-normal n-normal"> You room has been verified. </p>

                                <p class="p-small notification-date">
                                    <?php echo $set['date_time'] ?>
                                </p>

                                <div class="flex-row button-section">
                                    <button onclick="window.location.href='<?php echo $link ?>'"> See Room Detail </button>
                                </div>
                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a href="operation/notification-op.php?task=remove&notificationId=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
                                    </a>
                                </abbr>
                            </div>
                        </div>
                        <?php
                    } elseif ($set['type'] == 'room-suspend') { // room suspend
                        $link = "myroom-detail.php?roomId=" . $set['room_id'];
                        ?>
                        <div
                            class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                            <div class="left">
                                <div class="notify-image-container flex-row">
                                    <img src="../../Assests/Icons/report.png" alt="" class="notify-image">
                                </div>
                            </div>

                            <div class="flex-column middle">
                                <p class="p-normal n-normal"> You room has been suspended. </p>

                                <p class="p-small notification-date">
                                    <?php echo $set['date_time'] ?>
                                </p>

                                <div class="flex-row button-section">
                                    <button onclick="window.location.href='<?php echo $link ?>'"> See Room Detail </button>
                                </div>
                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a href="operation/notification-op.php?task=remove&notificationId=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
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
                                    <button onclick="window.location.href='<?php echo $link ?>'"> See Application Detail
                                    </button>
                                </div>
                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a href="operation/notification-op.php?task=remove&notificationId=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
                                    </a>
                                </abbr>
                            </div>
                        </div>
                        <?php
                    } elseif ($set['type'] == 'tenant-voice-submit') { // tenant voice submission notification
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
                                    <img src="../../Assests/Icons/speaking.png" alt="" class="notify-image">
                                </div>
                            </div>

                            <div class="flex-column middle">
                                <p class="p-normal n-normal">
                                    <?php echo $applicantName; ?> has a tenant voice.
                                </p>

                                <p class="p-small notification-date">
                                    <?php echo $set['date_time'] ?>
                                </p>

                                <div class="flex-row button-section">
                                    <button onclick="window.location.href='<?php echo $link ?>'"> See Application Detail
                                    </button>
                                </div>
                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a href="operation/notification-op.php?task=remove&notificationId=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
                                    </a>
                                </abbr>
                            </div>
                        </div>
                        <?php
                    } elseif ($set['type'] == 'room-application-make-tenant') { // make tenant notification
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
                                <p class="p-normal n-normal">
                                    <?php echo $applicantName; ?> is registered as a tenant.
                                </p>

                                <p class="p-small notification-date">
                                    <?php echo $set['date_time'] ?>
                                </p>

                                <div class="flex-row button-section">
                                    <button onclick="window.location.href='<?php echo $link ?>'"> See Application Detail
                                    </button>
                                </div>
                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a href="operation/notification-op.php?task=remove&notificationId=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
                                    </a>
                                </abbr>
                            </div>
                        </div>
                        <?php
                    } elseif ($set['type'] == 'room-review') { // room review
                        $roomId = $set['room_id'];
                        $tenantId = $set['tenant_id'];
                        $applicantName = $user->getUserName($tenantId);
                        $link = "myroom-detail.php?roomId=$roomId";
                        ?>
                        <div
                            class="notification-card flex-container flex-row page-notification-element <?php echo ($set['seen'] == 1) ? "page-seen-notification" : "page-unseen-notification"; ?>">
                            <div class="left">
                                <div class="notify-image-container flex-row">
                                    <img src="../../Assests/Icons/review.png" alt="" class="notify-image">
                                </div>
                            </div>

                            <div class="flex-column middle">
                                <p class="p-normal n-normal">
                                    <?php echo $applicantName; ?> submitted the room review.
                                </p>

                                <p class="p-small notification-date">
                                    <?php echo $set['date_time'] ?>
                                </p>

                                <div class="flex-row button-section">
                                    <button onclick="window.location.href='<?php echo $link ?>'"> See Review
                                    </button>
                                </div>
                            </div>

                            <div class="right">
                                <abbr title="Delete notification">
                                    <a href="operation/notification-op.php?task=remove&notificationId=<?php echo $set['notification_id']; ?>&url=<?php echo $url; ?>">
                                        <img src="../../Assests/Images/Delete-black.png" alt="">
                                    </a>
                                </abbr>
                            </div>
                        </div>
                        <?php
                    } else {
                        echo $set['type'] . "<br>";
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

    <script src="../../Js/jquery-3.7.1.min.js"> </script>

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