<?php
// staring session
if (!session_start())
    session_start();

// including files
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/tenant_voice_class.php';
include '../../Class/announcement_class.php';
include '../../class/functions.php';

// creating the object
$user = new User();
$house = new House();
$room = new Room();
$tenantVoice = new TenantVoice();
$announcementObj = new Announcement();

if (!isset ($_SESSION['landlordUserId']))
    header("Location: ../../index.php");
else
    $user->fetchSpecificRow($_SESSION['landlordUserId']);

$user->userId = $_SESSION['landlordUserId'];
$announcementSet = $announcementObj->fetchLatestAnnouncement('admin');

$myRoomArray = getRoomIdArray($_SESSION['landlordUserId']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/aside.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/announcement.css">
    <link rel="stylesheet" href="../../CSS/admin/user-voice.css">
    <link rel="stylesheet" href="../../CSS/landlord/dashboard.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- title -->
    <title> Dashboard </title>

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"> </script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="flex-row body-container">
        <aside class="empty-section"> </aside>

        <article class="content-article">
            <!-- subscription section -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold negative"> Your subscription has expired. </p>
                <button class="negative-button" onclick="window.location.href='subscription.php'"> Subscribe Now
                </button>
            </div>

            <p class="p-normal f-bold n-light" style="margin-top: 20px; padding-left:2px;"> Hello,
                <?php echo ucfirst($user->firstName); ?>!
            </p>

            <!-- dashboard cards -->
            <div class="content-container flex-row card-container-v2">
                <!-- house card -->
                <div class="flex-row card" id="card-1">
                    <div class="left flex-row">
                        <img src="../../Assests/Icons/home.svg" class="icon-class" alt="">
                    </div>

                    <div class="right">
                        <p class="p-normal"> Houses </p>
                        <p class="p-normal f-bold counter">
                            <?php echo $house->countUserHouse($user->userId, "all"); ?>
                        </p>
                    </div>
                </div>

                <!-- Rooms card -->
                <div class="flex-row card" id="card-2">
                    <div class="left flex-row">
                        <img src="../../Assests/Icons/room.png" class="icon-class" alt="">
                    </div>

                    <div class="right">
                        <p class="p-normal"> Rooms </p>
                        <p class="p-normal f-bold counter">
                            <?php echo $room->countRoom($_SESSION['landlordUserId'], "allHouses", "allTypes"); ?>
                        </p>
                    </div>
                </div>

                <!-- Acquired Rooms card -->
                <div class="flex-row card" id="card-3">
                    <div class="left flex-row">
                        <img src="../../Assests/Icons/room.png" class="icon-class" alt="">
                    </div>

                    <div class="right">
                        <p class="p-normal"> Acquired Rooms </p>
                        <p class="p-normal f-bold counter">
                            <?php echo $room->countRoom($_SESSION['landlordUserId'], "allHouses", "acquired"); ?>
                        </p>
                    </div>
                </div>


                <!-- Tenants voice card -->
                <div class="flex-row card" id="card-5">
                    <div class="left flex-row">
                        <img src="../../Assests/Icons/announcement.png" class="icon-class" alt="">
                    </div>

                    <div class="right">
                        <p class="p-normal"> Tenant Voices </p>
                        <p class="p-normal f-bold">
                            <?php echo $tenantVoice->countTenantVoiceForLandlord($myRoomArray); ?>
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
                <p class="heading f-bold negative"> Latest Announcement From System </p>
            </div>

            <div class="announcement-div content-container flex-column">
                <!-- latest announcement -->
                <?php
                $latestSystemAnnouncement = $announcementObj->fetchLatestSystemAnnouncement('landlord');
                if ($latestSystemAnnouncement != 0) {
                    ?>
                    <!-- announcement -->
                    <div class="announcement flex-column">
                        <!-- top -->
                        <div class="announcement-basic flex-row">
                            <div class="announcement-basic-left flex-column">
                                <p class="p-form f-bold"> Title :
                                    <?php echo ucfirst($latestSystemAnnouncement['title']); ?>
                                </p>
                                <p class="p-small n-light">
                                    <?php echo $latestSystemAnnouncement['announcement_date']; ?>
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
                                <?php echo ucfirst($latestSystemAnnouncement['announcement']); ?>
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
                                <?php
                                $announcementId = $latestSystemAnnouncement['announcement_id'];
                                $link = "system-announcement.php?announcementId=$announcementId";
                                ?>
                                <a href="<?php echo $link; ?>"> View Detail </a>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <!-- empty system announcement voice -->
                    <div class="flex-column empty-data-div" id="empty-data-div">
                        <p class="p-normal negative"> No system announcement has been made! </p>
                    </div>
                    <?php
                }
                ?>

                <div class="flex-row show-all-container <?php if ($latestSystemAnnouncement == 0)
                    echo "hidden"; ?>">
                    <p class="p-normal pointer" onclick="window.location.href='system-announcement.php'"> Show all
                        system announcements
                    </p>
                    <img src="../../Assests/Icons/right-arrow-black.png" alt=""
                        onclick="window.location.href='announcement.php'">
                </div>

                <!-- dummy anouncement -->
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

            <!-- user voice -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold negative"> Most Recent Tenant Voice </p>
            </div>

            <?php
            $myRoomIdArray = getRoomIdArray($user->userId);

            if (sizeof($myRoomIdArray) != 0) {

                $tenantVoice->fetchLatestTenantVoiceForLandlord($myRoomIdArray);

                if ($tenantVoice->tenantId != NULL) {
                    ?>
                    <div class="user-voice-div flex-column">
                        <!-- user voice 1 -->
                        <div class="user-voice shadow flex-column">
                            <div class="user-detail-div flex-row">
                                <div class="image-div">
                                    <img src="../../Assests/Uploads/user/<?php echo $user->getUserPhoto($tenantVoice->tenantId); ?>"
                                        alt="">
                                </div>

                                <div class="username-div flex-column">
                                    <p class="p-form">
                                        <?php echo $user->getUserName($tenantVoice->tenantId); ?>
                                    </p>
                                    <p class="p-small n-light">
                                        <?php echo $tenantVoice->date; ?>
                                    </p>
                                </div>
                            </div>

                            <div class="user-voice-box">
                                <p class="p-normal problem">
                                    <?php echo '"' . ucfirst($tenantVoice->voice) . '"'; ?>
                                </p>

                                <p class="p-small negative">
                                    <?php echo ($tenantVoice->issueState == 0) ? "Unsolved" : "Solved"; ?>
                                </p>
                            </div>

                            <div class="bottom flex-row">
                                <?php $link = "tenants-detail.php?tenantId=$tenantVoice->tenantId"; ?>
                                <div class="section flex-row pointer" onclick="window.location.href='<?php echo $link; ?>'">
                                    <img src="../../Assests/Icons/user.png" alt="">
                                    <p class="p-form"> Show user detail </p>
                                </div>

                                <?php
                                $link = "tenant-voice.php?voiceId=$tenantVoice->tenantId"; ?>
                                <div class="section flex-row pointer" onclick="window.location.href='<?php echo $link; ?>'">
                                    <img src="../../Assests/Icons/comment.png" alt="">
                                    <p class="p-form"> Show More </p>
                                </div>
                            </div>
                        </div>

                        <?php
                } else {
                    ?>
                        <!-- empty system announcement voice -->
                        <div class="flex-column empty-data-div">
                            <p class="p-normal negative"> No tenant voice has been raised. </p>
                        </div>

                        <?php
                }
                ?>

                    <div class="flex-row show-all-container <?php if ($tenantVoice->tenantId == NULL)
                        echo 'hidden'; ?>">
                        <p class="p-normal pointer" onclick="window.location.href='tenant-voice.php'"> Show all tenant
                            voices
                        </p>
                        <img src="../../Assests/Icons/right-arrow-black.png" alt=""
                            onclick="window.location.href='user-voice.php'">
                    </div>
                </div>
                <?php
            }else{
                ?>
                <!-- empty system announcement voice -->
                <div class="flex-column empty-data-div">
                            <p class="p-normal negative"> No tenant voice has been raised. </p>
                        </div>
                <?php
            }
            ?>
        </article>
    </div>

    <!-- js section -->
    <script>
        const activeMenu = $('#dashboard-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
        });
    </script>

    <!-- chart -->
    <script>
        const userTypeChart = document.getElementById('user-pie-chart');

        // user pie chart
        new Chart(userTypeChart, {
            type: 'pie',
            data: {
                labels: ['Acquirec', 'Unacquired'],
                datasets: [{
                    backgroundColor: ['#5CBEDB', '#E37E7E'],

                    // 'rgb(255, 99, 132)',
                    // 'rgb(54, 162, 235)',

                    data: [<?php echo $room->countRoom($_SESSION['landlordUserId'], "allHouses", "acquired"); ?>, <?php echo $room->countRoom($_SESSION['landlordUserId'], "allHouses", "unacquired"); ?>],
                }]
            },
            options: {
                borderColor: 'white',
                responsize: true,
            }
        });
    </script>
</body>

</html>