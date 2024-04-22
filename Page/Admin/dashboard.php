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
include '../../class/feedback_class.php';

// creating the object
$admin = new Admin();
$user = new User();
$room = new Room();
$house = new House();
$feedback = new Feedback();
$announcementObj = new Announcement();

$announcementSet = $announcementObj->fetchLatestAnnouncement('admin');

$url = $_SERVER['REQUEST_URI'];

// setting the values
$admin->adminId = $_SESSION['adminId'];
$admin->fetchAdmin($admin->adminId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/dashboard.css">
    <link rel="stylesheet" href="../../CSS/admin/announcement.css">
    <link rel="stylesheet" href="../../CSS/admin/user-voice.css">

    <!-- title -->
    <title> Dashboard (Admin) </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- script section -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <!-- chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"> </script>

    <!-- jquery -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="flex-row body-container">
        <!-- empty aside -->
        <aside class="empty-section"> </aside>

        <article class="content-article">
            <p class="p-normal f-bold" style="margin-top:20px; color: gray;"> Hello, Admin ! </p>

            <!-- dashboard cards -->
            <div class="content-container flex-row card-container-v2">

                <!-- house card -->
                <div class="flex-row card" id="card-1">
                    <div class="left flex-row">
                        <img src="../../Assests/Icons/building.png" class="icon-class" alt="">
                    </div>

                    <div class="right">
                        <p class="p-normal"> Users </p>
                        <p class="p-normal f-bold">
                            <?php echo $user->countUsers("all", "all"); ?>
                        </p>
                    </div>
                </div>

                <!-- house card -->
                <div class="flex-row card" id="card-2">
                    <div class="left flex-row">
                        <img src="../../Assests/Icons/user-square.svg" class="icon-class" alt="">
                    </div>

                    <div class="right">
                        <p class="p-normal"> Houses </p>
                        <p class="p-normal f-bold">
                            <?php echo $house->countHouse("all"); ?>
                        </p>
                    </div>
                </div>

                <!-- Rooms card -->
                <div class="card flex-row" id="card-3">
                    <div class="left flex-row">
                        <img src="../../Assests/Icons/room.png" class="icon-class" alt="">
                    </div>

                    <div class="right">
                        <p class="p-normal"> Rooms </p>
                        <p class="p-normal f-bold">
                            <?php echo $room->countRoom("admin", "allHouses", "allTypes"); ?>
                        </p>
                    </div>
                </div>

                <!-- Tenants Problems card -->
                <div class="card flex-row" id="card-4">
                    <div class="left flex-row">
                        <img src="../../Assests/Icons/speaking.png" class="icon-class" alt="">
                    </div>

                    <div class="right">
                        <p class="p-normal"> User Voice </p>
                        <p class="p-normal f-bold counter">
                            <?php echo $feedback->countFeedback("all"); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- chart container -->
            <div class="flex-row content-container chart-main-container">
                <!-- user pie chart -->
                <div class="shadow flex-column chart-container">
                    <p class="title p-form n-light"> Number of users based on role </p>
                    <hr>
                    <canvas id="user-pie-chart"> </canvas>
                </div>
            </div>

            <!-- announcement -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold negative"> Latest Announcement </p>
            </div>

            <!-- announcements -->
            <div class="announcement-div content-container flex-column <?php if (sizeof($announcementSet) == 0)
                echo 'hidden'; ?>">
                <?php
                foreach ($announcementSet as $set) {
                    $id = $set['announcement_id'];
                    ?>

                    <!-- announcement -->
                    <div class="announcement flex-column">
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

                            <div class="announcement-basic-right"
                                onclick="window.location.href='operation/announcement-op.php?id=<?php echo $id; ?>&url=<?php echo $url; ?>'">
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
                                <a href="announcement.php?announcementId=<?php echo $set['announcement_id']; ?>"> View Detail </a>
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
                            Lorem ipsum, dolor sit amet consectetur adipisicing elit. Dignissimos consequatur itaque
                            accusamus aperiam necessitatibus possimus hic dolorum praesentium, inventore mollitia alias
                            voluptas,Lorem ipsum dolor sit amet consectetur, adipisicing elit. Delectus inventore alias
                            iste tenetur aut sapiente, ratione voluptatem nobis totam laboriosam corrupti,
                            reprehenderit, sequi voluptatum eius. Enim, ducimus nesciunt. Velit sapiente alias
                            recusandae, expedita cupiditate inventore quidem quam quaerat consequatur ab.
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

                <div class="flex-column empty-data-div <?php if (sizeof($announcementSet) > 0)
                    echo "hidden"; ?>" id="empty-announcement-div">
                    <p class="p-normal negative"> No announcements! </p>
                </div>
            </div>

            <div class="flex-column empty-data-div <?php if (sizeof($announcementSet) != 0)
                echo "hidden"; ?>">
                <p class="p-normal negative"> No system announcement has been made. </p>
            </div>

            <!-- dummy announcement -->
            <div class="announcement-div content-container flex-column hidden">
                <!-- announcement -->
                <div class="announcement flex-column">
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

                <div class="flex-row show-all-container">
                    <p class="p-normal pointer" onclick="window.location.href='announcement.php'"> Show all user voices
                    </p>
                    <img src="../../Assests/Icons/right-arrow-black.png" alt=""
                        onclick="window.location.href='announcement.php'">
                </div>
            </div>

            <!-- user voice -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold negative"> Most Recent User Voice </p>
            </div>

            <div class="user-voice-div flex-column">
                <?php
                $feedbackCount = 0;
                $feedbackSet = $feedback->fetchFeedback("latest");

                if (sizeof($feedbackSet) > 0) {
                    $feedbackData = $feedbackSet[0]['feedback_data'];
                    $userId = $feedbackSet[0]['user_id'];
                    $userName = $user->getUserName($feedbackSet[0]['user_id']);
                    $feedbackData = ucfirst($feedbackSet[0]['feedback_data']);
                    $feedbackDate = $feedbackSet[0]['feedback_date'];
                    $role = ucfirst($user->getRole($feedbackSet[0]['user_id']));
                    $userLink = "user-detail.php?userId=$userId";
                    $email = $user->getUserEmail($feedbackSet[0]['user_id']);
                    $feedbackState = (isset($feedbackSet[0]['response_data'])) ? "Replied" : "Unreplied";
                    $userPhoto = $user->getUserPhoto($feedbackSet[0]['user_id']);
                    $feedbackCount++;
                    $responseFormId = "user-voice-response-form-" . $feedbackCount;
                    ?>

                    <div
                        class="user-voice shadow flex-column user-voice-element <?php echo ($feedbackState == "Replied") ? "user-voice-replied-element" : "user-voice-unreplied-element" ?>">
                        <!-- top -->
                        <div class="user-detail-div flex-row">
                            <div class="image-div">
                                <img src="../../Assests/Uploads/user/<?php echo $userPhoto; ?>" alt="">
                            </div>

                            <div class="username-div flex-column">
                                <p class="p-form">
                                    <?php echo $userName; ?> /
                                    <?php echo $role; ?>
                                </p>

                                <p class="p-small n-light">
                                    <?php echo $email; ?>
                                </p>
                            </div>
                        </div>

                        <!-- middle -->
                        <div class="flex-column user-voice-box">
                            <p class="p-normal problem">
                                "<?php echo $feedbackData; ?>"
                            </p>

                            <p class="p-small n-light">
                                <?php echo $feedbackDate; ?>
                            </p>

                            <p class="p-small negative">
                                <?php echo $feedbackState; ?>
                            </p>
                        </div>

                        <!-- bottom -->
                        <div class="bottom flex-row">
                            <div class="section flex-row pointer">
                                <img src="../../Assests/Icons/user.png" alt="">
                                <a href="<?php echo $userLink ?>">
                                    <p class="p-form"> See User Profile </p>
                                </a>
                            </div>

                            <div class="section flex-row pointer">
                                <img src="../../Assests/Icons/comment.png" alt="">
                                <p class="p-form"> Reply </p>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>

            <!-- empty context -->
            <div class="container empty-data-container <?php if (sizeof($feedbackSet) > 0)
                echo "hidden"; ?>" id="empty-data-container">
                <div class="flex-column div empty-data-div" id="empty-data-div">
                    <p class="p-normal negative"> No user voice has ben submitted yet. </p>
                </div>
            </div>
        </article>
    </div>

    <!-- chart -->
    <script>
        const userTypeChart = document.getElementById('user-pie-chart');

        // user pie chart
        new Chart(userTypeChart, {
            type: 'pie',
            data: {
                labels: ['Landlord', 'Tenant'],
                datasets: [{
                    backgroundColor: ['#5CBEDB', '#E37E7E'],
                    data: [<?php echo $user->countUsers("landlord", "all"); ?>, <?php echo $user->countUsers("tenant", "all"); ?>],
                }]
            },
            options: {
                borderColor: 'white',
                responsize: true,
            }
        });
    </script>

    <script>
        const activeMenu = $('#dashboard-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
        });
    </script>
</body>

</html>