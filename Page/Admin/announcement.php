<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['adminId']))
    header("Location: login.php");

// including files
include '../../Class/admin_class.php';
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/announcement_class.php';
include '../../class/functions.php';

// creating the object
$admin = new Admin();
$user = new User();
$room = new Room();
$house = new House();

$announcementObj = new Announcement();
$announcementSelected = new Announcement();
$announcementResponse = new AnnouncementResponse();

$selected = isset($_GET['announcementId']) ? true : false;

$url = $_SERVER['REQUEST_URI'];

if ($selected) {
    // check for the validity of the announcement id
    $valid = $announcementObj->isValid($_GET['announcementId']);

    // if(!$valid)
    // header("location: dashboard.php");

    $announcementSelected->announcementId = $_GET['announcementId'];
    $announcementSelected->fetchAnnouncement($_GET['announcementId']);
    $announcementResponseSet = $announcementResponse->fetchAllAnnouncementResponse($announcementSelected->announcementId);
}

$submissionState = "unknown";
if (isset($_GET['submission']))
    if ($_GET['submission'] != '')
        $submissionState = $_GET['submission'];


// setting the values
$admin->adminId = $_SESSION['adminId'];
$admin->fetchAdmin($admin->adminId);

if (isset($_POST['announcement-btn'])) {
    $title = $_POST['title'];
    $announcement = $_POST['announcement'];
    $announcementDate = date('Y-m-d H:i:s');
    $target = $_POST['target-select'];

    $announcementObj->setAnnouncement('-', 'admin', $target, '', '', '', '', $title, $announcement, $announcementDate);

    $immediateAnnouncementId = $announcementObj->registerAnnouncement();

    // create notification
    if ($immediateAnnouncementId != 0) {
        header('location: announcement.php?submission=success');
    } else
        header('location: announcement.php?submission=failure');
}

$announcementSet = $announcementObj->fetchAllAnnouncement('admin', '-');

// on response submittion
if (isset($_POST['announcement-respond-btn'])) {
    $response = $_POST['response'];
    $announcementResponseDate = date('Y-m-d H:i:s');
    $announcementResponse->setAnnouncementResponse($announcementSelected->announcementId, $admin->adminId, 'admin', $response, 0, $announcementResponseDate);
    $announcementResponse->registerAnnouncementResponse($_SERVER['REQUEST_URI']);
}

$url = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> Announcement </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/announcement.css">

    <!-- script section -->
    <script src="../../Js/main.js"> </script>

    <!-- jquery -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="body-container flex-row">
        <!-- menu -->
        <aside class="empty-section"> </aside>

        <article class="content-article">
            <!-- heading -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold negative">
                    <?php echo $selected ? "Announcement Detail" : "Announcement"; ?>
                </p>
            </div>

            <!-- selected announcement -->
            <div class="content-container flex-column announcement-div selected-announcement-div <?php if (!$selected)
                echo 'hidden'; ?>">
                <div class="announcement flex-column">
                    <!-- top -->
                    <div class="announcement-basic flex-row">
                        <div class="announcement-basic-left flex-column">
                            <p class="p-form f-bold"> Title :
                                <?php if ($selected)
                                    echo ucfirst($announcementSelected->title); ?>
                            </p>
                            <p class="p-small n-light"> Announced date :
                                <?php if ($selected)
                                    echo $announcementSelected->announcementDate; ?>
                            </p>
                        </div>

                        <div class="flex-row announcement-basic-right">
                            <abbr title="Close">
                                <a href="announcement.php">
                                    <img src="../../assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                                </a>
                            </abbr>
                        </div>
                    </div>

                    <!-- mid -->
                    <div class="announcement-detail">
                        <p class="p-normal">
                            <?php if ($selected)
                                echo ucfirst($announcementSelected->announcement); ?>
                        </p>
                    </div>

                    <!-- bottom -->
                    <div class="flex-row announcement-operation-div">
                        <div class="flex-row left-div">
                            <div class="flex-row like-div">
                                <img class="pointer" src="../../assests/Icons/thumbs-up.png" alt="">
                                <p class="p-form">
                                    <?php if ($selected)
                                        echo '0'; ?>
                                </p>
                            </div>

                            <div class="flex-row dislike-div">
                                <img class="pointer" src="../../assests/Icons/thumbs-down.png" alt="">
                                <p class="p-form">
                                    <?php if ($selected)
                                        echo '0'; ?>
                                </p>
                            </div>

                            <div class="flex-row comment-div">
                                <img class="pointer" src="../../assests/Icons/comment.png" alt="">
                                <p class="p-form">
                                    <?php if ($selected)
                                        echo '0'; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <p class="p-normal negative" style="margin-top:6px;"> Responses </p>
                    <?php
                    if ($selected) {
                        if (sizeof($announcementResponseSet) == 0)
                            echo '<p class="p-form"> No response yet. </p>';
                        else {
                            echo '<p class="p-form">' . sizeof($announcementResponseSet);
                            echo (sizeof($announcementResponseSet) == 1) ? " Response" : " Responses" . '</p>';
                        }
                    }
                    ?>

                    <!-- responses -->
                    <div class="flex-column announement-responses-div">
                        <?php
                        if ($selected) {
                            foreach ($announcementResponseSet as $set) {
                                if ($set['role'] != 'admin') {
                                    $user->fetchSpecificRow($set['user_id']);
                                    $user->setKeyValue('id', $set['user_id']);
                                    $profilePic = $user->userPhoto;
                                    $userName = $user->getUserName($user->userId);
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

                                    <div class="right">
                                        <abbr title="Delete this reply">
                                            <?php
                                            $announcementId = $set['announcement_id'];
                                            $link = "../operation/delete-announcement-response.php?id=$announcementId&url=$url; ?>"; ?>
                                            <a
                                                href="<?php echo $link; ?>">
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

                    <!-- respond form > announcement -->
                    <div class="flex-row respond-announcement-div">
                        <div class="left">
                            <img src="../../Assests/Images/RentRover-Logo.png" alt="">
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

            <!-- card -->
            <div class="card-container flex-row">
                <!-- all announcement -->
                <div class="card flex-column shadow pointer" id="all-announcement-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $announcementObj->countAnnouncement("admin", 0, "all"); ?>
                    </p>
                    <p class="p-form"> All Announcements </p>
                </div>

                <div class="card flex-column shadow pointer" id="both-announcement-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $announcementObj->countAnnouncement("admin", 0, "both"); ?>
                    </p>
                    <p class="p-form"> Both Targeted </p>
                </div>

                <!-- landlord targeted -->
                <div class="card flex-column shadow pointer" id="landlord-targeted-announcement-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $announcementObj->countAnnouncement("admin", 0, "landlord"); ?>
                    </p>
                    <p class="p-form"> Landlord Targeted </p>
                </div>

                <!-- tenant targeted -->
                <div class="card flex-column shadow pointer" id="tenant-targeted-announcement-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $announcementObj->countAnnouncement("admin", 0, "tenant"); ?>
                    </p>
                    <p class="p-form"> Tenant Targeted </p>
                </div>
            </div>

            <button id="announcement-btn" onclick="toggleAnnouncementForm()"> Make an announcements </button>

            <form action="" method="POST" class="announcement-form shadows flex-column" id="announcement-form">
                <div class="top-div flex-row">
                    <p class="p-large"> Announcment Form </p>
                    <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="pointer"
                        onclick="toggleAnnouncementForm()">
                </div>

                <p class="p-form"> Title </p>
                <input type="text" name="title" id="" required>

                <!-- select target -->
                <p class="p-form"> Audience </p>
                <select name="target-select" id="">
                    <option value="0" selected> Lanlord & Tenant </option>
                    <option value="1"> Lanlord </option>
                    <option value="2"> Tenant </option>
                </select>

                <p class="p-form"> Announcement </p>
                <textarea name="announcement" id="" required></textarea>

                <button type="submit" name="announcement-btn" class="positive-button flex-row"> <img
                        src="../../Assests/Icons/announcement.png" alt=""> Announce Now </button>
            </form>

            <!-- filter & search -->
            <div class="container content-container flex-row filter-search-container">
                <div class="flex-row filter-div">
                    <div class="flex-row filter-icon-div ">
                        <img src="../../Assests/Icons/filter.png" alt="">
                    </div>

                    <!-- house state select -->
                    <div class="flex-row filter-select-div announcement-type-div">
                        <label for="announcement-type-select"> Announcement Type </label>
                        <select name="announcement-type-select" id="announcement-type-select">
                            <option value="0"> All </option>
                            <option value="1"> Both Targeted </option>
                            <option value="2"> Landlord Targeted </option>
                            <option value="3"> Tenant </option>
                        </select>
                    </div>

                    <div class="flex-row pointer clear-filter-div" id="clear-sort">
                        <p class="p-form"> Clear Sort </p>
                        <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                    </div>
                </div>
            </div>


            <p class="p-normal f-bold content-container negative <?php if (!$selected)
                echo "hidden"; ?>"> Other announcements </p>

            <p class="p-normal f-bold" style="margin-top:30px; padding-left:4px;" id="announcement-heading"> All
                Announcements </p>

            <!-- announcements -->
            <div class="announcement-div content-container flex-column <?php if (sizeof($announcementSet) == 0)
                echo 'hidden'; ?>">
                <?php
                foreach ($announcementSet as $set) {
                    $id = $set['announcement_id'];
                    ?>
                    <!-- announcement -->
                    <div class="announcement flex-column system-announcement-element <?php if ($set['target'] == 0)
                        echo "both-targeted-announcement";
                    else if ($set['target'] == 1)
                        echo "landlord-targeted-announcement";
                    else if ($set['target'] == 2)
                        echo "tenant-targeted-announcement"; ?> <?php if ($announcementSelected->announcementId == $id)
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

                            <?php $link = "operation/announcement-op.php?id=$id&url=$url"; ?>
                            <div class="announcement-basic-right" onclick="window.location.href='<?php echo $link; ?>'">
                                <abbr title="Delete this announcement">
                                    <img src="../../assests/Icons/delete.png" alt="" class="icon-class <?php if ($set['announcement_id'] == $announcementSelected->announcementId)
                                        echo "hidden"; ?>">
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
                                <a href="announcement.php?announcementId=<?php echo $id; ?>"> View Detail </a>
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
                            <abbr title="Delete this announcement">
                                <img src="../../assests/Icons/delete.png" alt="" class="icon-class">
                            </abbr>
                        </div>
                    </div>

                    <!-- mid -->
                    <div class="announcement-detail">
                        <p class="p-normal">
                            This is an announcement.
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

        <!-- dialog box -->
        <?php
        if ($submissionState == 'success' || $submissionState == 'failure') {
            ?>
            <div class="dialog-container flex-column">
                <div class="dialog-div flex-column">
                    <div class="top-div flex-row">
                        <div class="message-div flex-column">
                            <?php
                            if ($submissionState == 'success') {
                                ?>
                                <p class="p-large f-bold positive"> Announcement has been made. </p>
                                <?php
                            } else if ($submissionState == 'failure') {
                                ?>
                                    <p class="p-large f-bold negative"> Announcement could not be made. </p>
                                    <p class="p-normal"> Please try again. </p>
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    <div class="bottom-div flex-row">
                        <?php
                        if ($submissionState == 'success') {
                            ?>
                            <button onclick="window.location.href='announcement.php';" class="inverse-button"> See Announcment
                                Detail </button>
                            <?php
                        } else if ($submissionState == 'failure') {
                            ?>
                                <button onclick="window.location.href='announcement.php'" class="inverse-button"> Try Again
                                </button>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
        } ?>
    </div>

    <!-- js section -->
    <script>
        const activeMenu = $('#announcement-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
        });
    </script>

    <script>
        $(document).ready(function () {
            const emptyContextDiv = $('#empty-data-div');

            // elements
            const systemAnnouncementElement = $('.system-announcement-element');
            var bothTargetedAnnouncement = $('.both-targeted-announcement');
            var landlordTargetedAnnouncement = $('.landlord-targeted-announcement');
            var tenantTargetedAnnouncement = $('.tenant-targeted-announcement');

            var announcementFormState = false;
            const announcementForm = $('#announcement-form');

            announcementForm.hide();
            $('#clear-sort').hide();


            // filter cards
            var filter = 0;
            $('#all-announcement-filter-card').click(function () {
                filter = 0;
                filterAnnouncementElement();
            });

            $('#both-announcement-filter-card').click(function () {
                filter = 1;
                filterAnnouncementElement();
            });

            $('#landlord-targeted-announcement-filter-card').click(function () {
                filter = 2;
                filterAnnouncementElement();
            });

            $('#tenant-targeted-announcement-filter-card').click(function () {
                filter = 3;
                filterAnnouncementElement();
            });

            toggleAnnouncementForm = () => {
                if (!announcementFormState) {
                    announcementForm.show();
                    announcementFormState = true;
                }
                else {
                    announcementForm.hide();
                    announcementFormState = false;
                }
            }

            filterAnnouncementElement = () => {
                $('#announcement-type-select')[0].value = filter;

                systemAnnouncementElement.hide();
                if (filter == 0) {
                    $('#clear-sort').hide();
                    systemAnnouncementElement.show();
                    $('#announcement-heading').text("All Announcements");
                } else if (filter == 1) {
                    $('#clear-sort').show();
                    bothTargetedAnnouncement.show();
                    $('#announcement-heading').text("Both Targeted Announcements");
                } else if (filter == 2) {
                    $('#clear-sort').show();
                    landlordTargetedAnnouncement.show();
                    $('#announcement-heading').text("Landlord Targeted Announcements");
                } else {
                    $('#clear-sort').show();
                    tenantTargetedAnnouncement.show();
                    $('#announcement-heading').text("Tenant Targeted Announcements");
                }

                if ($('.system-announcement-element:visible').length == 0)
                    emptyContextDiv.show();
                else
                    emptyContextDiv.hide();
            }

            filterAnnouncementElement();

            // sort >> select
            $('#announcement-type-select').change(function () {
                filter = $('#announcement-type-select')[0].value;
                filterAnnouncementElement();
            });

            $('#clear-sort').click(function () {
                filter = 0;
                $('#announcement-type-select')[0].value = 0;
                $('#clear-sort').hide();
                filterAnnouncementElement();
            });
        });
    </script>
</body>

</html>