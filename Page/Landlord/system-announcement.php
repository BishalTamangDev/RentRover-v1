<?php
// staring session
if (!session_start()) {
    session_start();
}

// including files
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/announcement_class.php';
include '../../class/functions.php';

// creating the object
$user = new User();
$userObj = new User();
$houseObj = new House();
$roomObj = new Room();
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
$user->userId = $_SESSION['landlordUserId'];

if (!isset($_SESSION['landlordUserId'])) {
    // divert to the login page
    header("Location: ../index.php");
} else {
    $user->fetchSpecificRow($_SESSION['landlordUserId']);
}

// sorting : all, approved & unapproved
if (isset($_GET['sortType'])) {
    $sortType = $_GET['sortType'];
    $sort = true;
}

// getting all the announcements for landlord
$announcementSet = $announcementObj->fetchAllSystemAnnouncementForUser('landlord');

// on response submittion
if (isset($_POST['announcement-respond-btn'])) {
    $response = $_POST['response'];
    $announcementResponseDate = date('Y-m-d H:i:s');
    $announcementResponse->setAnnouncementResponse($announcementSelected->announcementId, $user->userId, 'landlord', $response, 0, $announcementResponseDate);
    $announcementResponse->registerAnnouncementResponse($_SERVER['REQUEST_URI']);
}

// url
$url = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> System Announcements </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/announcement.css">

    <!-- script section -->
    <script>
        // prevent resubmission of the form
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="body-container flex-row">
        <aside class="empty-section"> </aside>

        <article class="content-article">
            <!-- heading -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold negative"> System Announcements </p>
            </div>

            <!-- selected announcement -->
            <div class="content-container flex-column announcement-div selected-announcement-div <?php if (!$selected)
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
                                <a href="">
                                    <img src="../../assests/Icons/thumbs-up.png" alt="">
                                </a>
                                <p class="p-form">
                                    <?php echo '0'; ?>
                                </p>
                            </div>

                            <div class="flex-row dislike-div">
                                <a href="">
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
                                        elseif ($set['role'] == 'landlord')
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

                                    <?php 
                                        $id = $set['announcement_response_id'];
                                        $link = "operation/system-announcement-op.php?announcementResponseId=$id&task=remove&url=$url";
                                    ?>

                                    <div class="right <?php if ($set['user_id'] != $user->userId)
                                        echo "hidden"; ?>">
                                        <abbr title="Delete this reply">
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

                        <!-- dummy reply -->
                        <div class="flex-row announement-response hidden">
                            <div class="left">
                                <img src="../../Assests/Uploads/user/blank.jpg" alt="">
                            </div>

                            <div class="flex-column middle">
                                <p class="p-form"> Username </p>
                                <p class="p-small"> 0000-00-00 </p>
                                <p class="p-normal"> This is the first announcement response. </p>
                            </div>

                            <div class="right hidden">
                                <abbr title="Delete this reply">
                                    <a href="">
                                        <img class="pointer" src="../../Assests/Icons/delete.png" alt="">
                                    </a>
                                </abbr>
                            </div>
                        </div>
                    </div>

                    <!-- respond form > announcement -->
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

            <p class="p-normal f-bold content-container negative <?php if (!$selected)
                echo "hidden"; ?>"> Other
                announcements </p>

            <!-- announcements -->
            <div class="announcement-div content-container flex-column">
                <?php
                foreach ($announcementSet as $set) {
                    $id = $set['announcement_id'];
                    ?>
                    <!-- announcement -->
                    <div class="announcement flex-column system-announcement-element system-unseen-announcement-element system-seen-announcement-element <?php if ($announcementSelected->announcementId == $id)
                        echo 'hidden'; ?>">
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

                            <div class="announcement-basic-right hidden"
                                onclick="window.location.href='operation/delete-announcement.php?id=<?php echo $id; ?>&link=<?php echo $_SERVER['REQUEST_URI']; ?>'">
                                <abbr title="Delete this announcement">
                                    <img src="../../assests/Icons/delete.png" alt="" class="icon-class">
                                </abbr>
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
                ?>

                <!-- dummy announcement -->
                <div class="announcement flex-column hidden">
                    <!-- top -->
                    <div class="announcement-basic flex-row">
                        <div class="announcement-basic-left flex-column">
                            <p class="p-form f-bold"> Title : Announcement Title </p>
                            <p class="p-small n-light"> Announced date : 2080/01/06</p>
                        </div>

                        <div class="announcement-basic-right">
                            <abbr title="Delete">
                                <a href="">
                                    <img src="../../assests/Icons/delete.png" alt="" class="icon-class">
                                </a>
                            </abbr>
                        </div>
                    </div>

                    <!-- mid -->
                    <div class="announcement-detail">
                        <p class="p-normal">
                            Lorem ipsum, dolor sit amet consectetur adipisicing elit. Dignissimos consequatur itaque
                            accusamus aperiam necessitatuidem quam quaerat consequatur ab.
                        </p>
                    </div>

                    <!-- bottom -->
                    <div class="announcement-operation-div flex-row">
                        <div class="left-div flex-row">
                            <div class="like-div flex-row">
                                <img src="../../assests/Icons/thumbs-up.png" alt="">
                                <p class="p-form"> 120 </p>
                            </div>

                            <div class="dislike-div flex-row">
                                <img src="../../assests/Icons/thumbs-down.png" alt="">
                                <p class="p-form"> 7 </p>
                            </div>

                            <div class="comment-div flex-row">
                                <img src="../../assests/Icons/comment.png" alt="">
                                <p class="p-form"> 7 </p>
                            </div>
                        </div>

                        <div class="right-div">
                            <a href=""> View Detail </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-column empty-data-div" id="empty-data-div">
                <img src="../../Assests/Icons/empty.png" alt="">
                <p class="p-normal negative"> Empty! </p>
            </div>
        </article>
    </div>

    <!-- script section -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>

    <script>
        $(document).ready(function () {
            const emptyContextDiv = $('#empty-data-div');
            const systemAnnouncementElement = $('.system-announcement-element');
            const systemUnseenAnnouncementElement = $('.system-unseen-announcement-element');
            const systemSeenAnnouncementElement = $('.system-seen-announcement-element');

            filterAnnouncement = () =>{
                if($('.system-announcement-element:visible').length == 0)
                    emptyContextDiv.show();
                else
                    emptyContextDiv.hide();
            }

            filterAnnouncement();
        });
    </script>
</body>

</html>