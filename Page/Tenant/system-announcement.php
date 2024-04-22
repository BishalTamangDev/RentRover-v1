<?php
// staring session
if (!session_start())
    session_start();

// including files
include '../../class/functions.php';
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/wishlist_class.php';
include '../../Class/feedback_class.php';
include '../../Class/announcement_class.php';
include '../../Class/notification_class.php';

// creating the object
$user = new User();
$userObj = new User();
$roomObj = new Room();
$houseObj = new House();
$wishlist = new Wishlist();
$feedback = new Feedback();
$feedbackObj = new Feedback();
$notification = new Notification();
$announcementObj = new Announcement();
$announcementSelected = new Announcement();
$announcementResponse = new AnnouncementResponse();

$selected = isset($_GET['announcementId']) ? true : false;

if ($selected) {
    // URL tampering test

    $announcementSelected->announcementId = $_GET['announcementId'];
    $announcementSelected->fetchAnnouncement($_GET['announcementId']);
    $announcementResponseSet = $announcementResponse->fetchAllAnnouncementResponse($announcementSelected->announcementId);
}

// user id
$user->userId = $_SESSION['tenantUserId'];

if (!isset($_SESSION['tenantUserId'])) {
    // divert to the login page
    header("Location: ../index.php");
} else
    $user->fetchSpecificRow($_SESSION['tenantUserId']);

// getting notification count
$notificationCount = $notification->countNotification("tenant", $user->userId, "unseen");

// wishlist
$wishlistCount = $wishlist->countWishes($_SESSION['tenantUserId']);

// fetching announcement set
$announcementSet = $announcementObj->fetchAllSystemAnnouncementForUser('tenant');

// on response submittion
if (isset($_POST['announcement-respond-btn'])) {
    $response = $_POST['response'];
    $announcementResponseDate = date('Y-m-d H:i:s');
    $announcementResponse->setAnnouncementResponse($announcementSelected->announcementId, $user->userId, strtolower($user->role), $response, 0, $announcementResponseDate);
    $announcementResponse->registerAnnouncementResponse($_SERVER['REQUEST_URI']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> RentRover: System Announcement </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/tenant/navbar.css">

    <link rel="stylesheet" href="../../CSS/Tenant/system-announcement.css">
    <link rel="stylesheet" href="../../CSS/Admin/announcement.css">

    <!-- script section -->
    <script>
        // prevent resubmission of the form
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <!-- jquery -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <!-- system announcement container -->
    <div class="flex-column container content-container system-announcement-container">
        <!-- heading -->
        <div class="div section-heading-container">
            <p class="p-large f-bold"> System Announcements </p>
        </div>

        <!-- selected announcement -->
        <div class="div flex-column announcement-div selected-announcement-div <?php if (!$selected)
            echo 'hidden'; ?>">
            <div class="announcement flex-column">
                <!-- top -->
                <div class="announcement-basic flex-row">
                    <div class="announcement-basic-left flex-column">
                        <p class="p-form f-bold"> Title :
                            <?php echo ucfirst($announcementSelected->title); ?>
                        </p>
                        <p class="p-small n-light"> Announced date :
                            <?php echo $announcementSelected->announcementDate; ?>
                        </p>
                    </div>

                    <div class="flex-row announcement-basic-right">
                        <abbr title="Close">
                            <a href="system-announcement.php">
                                <img src="../../assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                            </a>
                        </abbr>
                    </div>
                </div>

                <!-- mid -->
                <div class="announcement-detail">
                    <p class="p-normal">
                        <?php echo ucfirst($announcementSelected->announcement); ?>
                    </p>
                </div>

                <!-- bottom -->
                <div class="announcement-operation-div flex-row">
                    <div class="flex-row left-div">
                        <div class="flex-row like-div">
                            <a href="../operation/like-announcement.php?id=">
                                <img src="../../assests/Icons/thumbs-up.png" alt="">
                            </a>
                            <p class="p-form">
                                <?php echo '0'; ?>
                            </p>
                        </div>

                        <div class="flex-row dislike-div">
                            <a href="../operation/dislike-announcement.php?id=">
                                <img src="../../assests/Icons/thumbs-down.png" alt="">
                            </a>
                            <p class="p-form">
                                <?php echo '0'; ?>
                            </p>
                        </div>

                        <div class="comment-div flex-row">
                            <img src="../../assests/Icons/comment.png" alt="">
                            <p class="p-form">
                                <?php echo '0'; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <p class="p-normal negative" style="margin-top:6px;"> Responses </p>

                <!-- announcement replies -->
                <div class="flex-column announement-responses-div">
                    <?php
                    if ($selected) {
                        foreach ($announcementResponseSet as $set) {
                            if ($set['role'] != 'admin') {
                                $userObj->fetchSpecificRow($set['user_id']);
                                $userObj->userId = $set['user_id'];
                                $profilePic = $userObj->userPhoto;
                                $userName = $userObj->getUserName($userObj->userId);
                            }
                            ?>

                            <div class="flex-row announement-response hiddens">
                                <div class="left">
                                    <?php
                                    if ($set['role'] == 'admin')
                                        echo '<img src="../../Assests/Images/RentRover-Logo.png" alt="">';
                                    elseif ($set['role'] == 'Landlord')
                                        echo '<img src="../../Assests/uploads/user/' . $profilePic . '" alt="">';
                                    else
                                        echo '<img src="../../Assests/uploads/user/' . $profilePic . '" alt="">';
                                    ?>
                                </div>

                                <div class="flex-column middle">
                                    <p class="p-form">
                                        <?php
                                        if ($set['role'] == 'admin')
                                            echo "Admin";
                                        else {
                                            echo $userName;
                                        }
                                        ?>
                                    </p>

                                    <p class="p-small">
                                        <?php echo $set['announcement_response_date']; ?>
                                    </p>

                                    <p class="p-normal">
                                        <?php echo ucfirst($set['response']); ?>
                                    </p>
                                </div>

                                <div class="right <?php if ($set['user_id'] != $user->userId)
                                    echo "hidden"; ?>">
                                    <abbr title="Delete this reply">
                                        <?php
                                        $task = "delete";
                                        $id = $set['announcement_id'];
                                        $userId = $_SESSION['tenantUserId'];
                                        $url = $_SERVER['REQUEST_URI'];
                                        $link = "../operation/announcement-response-operation.php?announcementId=$id&task=$task&userId=$userId&url=$url";
                                        ?>
                                        <a href="<?php echo $link; ?>">
                                            <img class="pointer" src="../../Assests/Icons/delete.png" alt="">
                                        </a>
                                    </abbr>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>

                <!-- response form > announcement -->
                <div class="flex-row respond-announcement-div">
                    <div class="left">
                        <img src="../../Assests/Uploads/user/<?php echo $user->userPhoto; ?>" alt="">
                    </div>

                    <div class="right">
                        <form action="" method="POST" class="flex-column respond-announcement-form">
                            <textarea name="response" id="" placeholder="your response" required></textarea>
                            <button type="submit" name="announcement-respond-btn" class="positive-button"> Submit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="div content-container <?php if (!$selected)
            echo 'hidden'; ?>">
            <p class="p-normal f-bold"> Other Announcements </p>
        </div>

        <!-- announcements -->
        <div class="div announcement-div content-container flex-column">
            <!-- announcement -->
            <?php
            if (sizeof($announcementSet) > 0) {
                foreach ($announcementSet as $set) {
                    $id = $set['announcement_id'];
                    ?>
                    <div
                        class="announcement flex-column system-announcement-element system-unseen-announcement-element system-seen-announcement-element <?php if (isset($_GET['announcementId']) && $_GET['announcementId'] == $id)
                            echo "hidden"; ?>">
                        <!-- top -->
                        <div class="announcement-basic flex-row">
                            <div class="announcement-basic-left flex-column">
                                <p class="p-form f-bold"> Title :
                                    <?php echo ucfirst($set['title']); ?>
                                </p>
                                <p class="p-small n-light"> Announced date :
                                    <?php echo $set['announcement_date']; ?>
                                </p>
                            </div>
                        </div>

                        <!-- mid -->
                        <div class="announcement-detail">
                            <p class="p-normal">
                                <?php echo ucfirst($set['announcement']); ?>
                            </p>
                        </div>

                        <!-- bottom -->
                        <div class="announcement-operation-div flex-row">
                            <div class="left-div flex-row">
                                <div class="like-div flex-row">
                                    <img src="../../assests/Icons/thumbs-up.png" alt="">
                                    <p class="p-form">
                                        <?php echo '0'; ?>
                                    </p>
                                </div>

                                <div class="dislike-div flex-row">
                                    <img src="../../assests/Icons/thumbs-down.png" alt="">
                                    <p class="p-form">
                                        <?php echo '0'; ?>
                                    </p>
                                </div>

                                <div class="comment-div flex-row">
                                    <img src="../../assests/Icons/comment.png" alt="">
                                    <p class="p-form">
                                        <?php echo '0'; ?>
                                    </p>
                                </div>
                            </div>

                            <div class="right-div">
                                <a href="system-announcement.php?announcementId=<?php echo $id; ?>"> View Detail </a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="div flex-column empty-data-div" id="empty-data-div">
                    <img src="../../Assests/Icons/empty.png" alt="">
                    <p class="p-normal negative"> Empty! </p>
                </div>
                <?php
            }
            ?>

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
</body>

</html>