<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['landlordUserId']))
    header("location: ../index.php");

if (!isset($_GET['leaveApplicationId']))
    header("location: dashboard.php");

// url values check
if ($_GET['leaveApplicationId'] == null)
    header("location: dashboard.php");
else
    $leaveApplicationId = $_GET['leaveApplicationId'];

// including files
include '../../Class/functions.php';
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/notification_class.php';
include '../../Class/leave_application_class.php';

// creating the object
$user = new User();
$applicant = new User();
$room = new Room();
$tenantNotification = new Notification();
$leaveApplication = new LeaveApplication();

$user->userId = $_SESSION['landlordUserId'];
$user->fetchUser($user->userId);
$user->fetchSpecificRow($_SESSION['landlordUserId']);

// get room array
$roomIdArray = getRoomIdArray($_SESSION['landlordUserId']);

$leaveApplication->fetchLeaveApplication($leaveApplicationId);

if (!in_array($leaveApplication->roomId, $roomIdArray))
    header("location: dashboard.php");


// room
$roomId = $leaveApplication->roomId;
$room->setKeyValue('id', $roomId);
$room->fetchRoom($roomId);

// applicant
$applicantId = $leaveApplication->tenantId;
$applicant->userId = $leaveApplication->tenantId;
$applicant->fetchSpecificRow($applicantId);

// application operation : accept button
if (isset($_POST['accept-btn'])) {
    $task = 'accept';
    $response = $leaveApplication->leaveApplicationOperation($task, $roomId, $applicantId);

    if ($response) {
        // notify applicant
        $tenantNotification->setApplicationNotification("room-leave-application-accept", $roomId, $_SESSION['landlordUserId'], $applicantId, $leaveApplicationId);
        $tenantNotification->whose = "tenant";

        $response = $tenantNotification->register();
        
        $url = $_SERVER['REQUEST_URI'];
        header("Location: " . $url);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title> Application Detail </title>

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/landlord/make-tenant.css">
    <link rel="stylesheet" href="../../CSS/landlord/application-detail.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="flex-row body-container">
        <aside class="empty-section"> </aside>

        <article class="flex-column content-article">
            <p class="top-heading p-larger f-bold"> Leave Application Detail </p>

            <div class="application-container">
                <!-- application detail -->
                <div class="flex-column application-detail-container">
                    <div class="flex-column application-detail-section room-photo-container">
                        <div class="flex-column top">
                            <img src="../../Assests/Uploads/Room/<?php echo $room->roomPhotoArray[0]['room_photo']; ?>"
                                alt="" class="pointer">
                        </div>

                        <div class="flex-row bottom">
                            <?php
                            foreach ($room->roomPhotoArray as $roomPhoto) {
                                ?>
                                <img src="../../Assests/Uploads/Room/<?php echo $roomPhoto['room_photo'] ?>" alt=""
                                    class="pointer">
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    <?php $link = "myroom-detail.php?roomId=$room->roomId"; ?>
                    <div class="pointer flex-row application-detail-section"
                        onclick="window.location.href='<?php echo $link; ?>'">
                        <img src="../../Assests/Icons/redirect.png" alt="" class="icon-class">
                        <p class="p-font n-light"> See Room Detail </p>
                    </div>


                    <div class="flex-row application-detail-section">
                        <p class="p-normal left"> Leave Date </p>
                        <p class="p-normal right">
                            <?php echo $leaveApplication->leaveDate; ?>
                        </p>

                    </div>

                    <div class="flex-row application-detail-section">
                        <p class="p-normal left"> Note </p>
                        <p class="p-normal right">
                            <?php echo ucfirst($leaveApplication->note); ?>
                        </p>
                    </div>

                    <div class="flex-row application-detail-section">
                        <p class="p-normal left"> Application Date </p>
                        <p class="p-normal right">
                            <?php echo $leaveApplication->leaveApplicationDate; ?>
                        </p>
                    </div>


                    <div class="flex-row application-detail-section">
                        <p class="p-normal left"> State </p>
                        <p class="p-normal right">
                            <?php echo ($leaveApplication->state == 0)? "Pending":"Accepted"; ?>
                        </p>
                    </div>
                </div>

                <!-- applicant detail -->
                <div class="flex-row applicant-detail-container">
                    <div class="flex-column applicant-detail-div">
                        <p class="p-large"> Applicant Detail </p>

                        <div class="flex-row applicant-photo-container">
                            <img src="../../Assests/Uploads/User/<?php echo $applicant->userPhoto; ?>" alt=""
                                class="icon-class">
                        </div>

                        <div class="flex-row applicant-detail-section">
                            <p class="p-normal left"> Name </p>
                            <p class="p-normal right">
                                <?php echo $applicant->getUserName($applicant->userId); ?>
                            </p>
                        </div>

                        <div class="flex-row applicant-detail-section">
                            <p class="p-normal left"> Gender </p>
                            <p class="p-normal right">
                                <?php echo ucfirst($applicant->gender); ?>
                            </p>
                        </div>

                        <div class="flex-row applicant-detail-section">
                            <p class="p-normal left"> DOB </p>
                            <p class="p-normal right">
                                <?php echo $applicant->dob; ?>
                            </p>
                        </div>

                        <div class="flex-row applicant-detail-section">
                            <p class="p-normal left"> Address </p>
                            <p class="p-normal right">
                                <?php echo $applicant->areaName . ', '; ?>
                                <?php echo returnArrayValue("district", $applicant->district) . ', '; ?>
                                <?php echo returnArrayValue("province", $applicant->province); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- operation section -->
            <div class="flex-row application-operation-section">
                <form method="POST">
                    <?php
                    if ($leaveApplication->state == 0) {
                        ?>
                        <button name="accept-btn" class="positive-button"> Accept </button>
                        <?php
                    }
                    ?>
                </form>
            </div>
        </article>
    </div>

    <!-- js section -->
    <script>
        const activeMenu = $('#leave-application-menu-id');
        activeMenu.css({
            "background-color" : "#DFDFDF"
        });
    </script>
</body>

</html>