<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['landlordUserId']))
    header("location: ../index.php");

if (!isset($_GET['applicationId']) || !isset($_GET['tenantId']) || !isset($_GET['roomId']))
    header("location: dashboard.php");

// url values check
$applicationId = $_GET['applicationId'];
$applicantId = $_GET['tenantId'];
$roomId = $_GET['roomId'];

// including files
include '../../Class/functions.php';
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/notification_class.php';
include '../../Class/tenancy_history_class.php';
include '../../Class/application_class.php';

$applicationObj = new Application();
$state = $applicationObj->applicationValidityCheck($_SESSION['landlordUserId'], $applicantId, $roomId, $applicationId);

if (!$state)
    header("location: dashboard.php");


// creating the object
$user = new User();
$applicant = new User();
$house = new House();
$room = new Room();
$tenancyHistory = new TenancyHistory();
$tenantNotification = new Notification();
$landlordNotification = new Notification();

$user->userId = $_SESSION['landlordUserId'];

// applicant
$applicant->fetchSpecificRow($applicantId);
$applicant->userId = $applicantId;

$user->fetchSpecificRow($_SESSION['landlordUserId']);

// application
$applicationObj->fetchApplication($applicationId);
$applicationObj->applicationId = $applicantId;

// room
$room->fetchRoom($roomId);

// application operation buttons
// accept button
if (isset($_POST['accept-btn'])) {
    $task = 'accept';
    $response = $applicationObj->applicationOperation($task, $roomId, $applicantId);

    if ($response) {
        // notify accepted applicant
        $tenantNotification->setApplicationNotification("room-application-accept", $roomId, $_SESSION['landlordUserId'], $applicantId, $applicationId);
        $tenantNotification->whose = "tenant";
        $response = $tenantNotification->register();

        if ($response) {
            // get all pending application for this room
            $applicationIdArray = [];
            $applicationIdArray = $applicationObj->getPendingApplicationForThisRoom($roomId);

            $applicantIdArray = [];
            $applicantIdArray = $applicationObj->getPendingApplicantIdForThisRoom($roomId);

            if (sizeof($applicationIdArray) > 0) {
                // reject all remaining application 
                $response = $applicationObj->rejectRemainingApplication($roomId, $applicantId);

                if ($response) {
                    foreach ($applicationIdArray as $temp) {
                        // create notification for tenant
                        $tenantNotification->setApplicationNotification("room-application-reject", $roomId, $_SESSION['landlordUserId'], $applicantIdArray[0]++, $temp);
                        $tenantNotification->whose = "tenant";
                        $response = $tenantNotification->register();
                    }
                }

                $url = "application-detail.php?applicationId=$applicationId&tenantId=$applicantId&roomId=$roomId";
                header("location: $url");
            }
        }
    }
}

// make tenant button
if (isset($_POST['make-tenant-btn'])) {
    $roomId = $_GET['roomId'];

    // getting the form value
    $tenancyStartDate = $_POST['make-tenant-start-date'];

    // update the 'tenancy_history' table
    $tenancyHistory->setTenancyHistory($roomId, $applicantId, $tenancyStartDate, 0);
    $state = $tenancyHistory->register();

    if($state){
        // updating room table
        $query = "update `room` set tenant_id = '$applicantId', is_acquired = 1 where room_id = '$roomId'";
        $response = mysqli_query($room->conn, $query);
        
        if ($response) {
            $task = 'make-tenant';
            $response = $applicationObj->applicationOperation($task, $roomId, $applicantId);
            
            if ($response) {
                // create notification for tenant
                $tenantNotification->setApplicationNotification("room-application-make-tenant", $roomId, $_SESSION['landlordUserId'], $applicantId, $applicationId);
                $tenantNotification->whose = "tenant";
                $response = $tenantNotification->register();
                
                if ($response) {
                    // notify landlord about new tenant
                    $landlordNotification->setApplicationNotification("room-application-make-tenant", $roomId, $_SESSION['landlordUserId'], $applicantId, $applicationId);
                    $landlordNotification->whose = "landlord";
                    $response = $landlordNotification->register();
                    
                    $url = "application-detail.php?applicationId=$applicationId&tenantId=$applicantId&roomId=$roomId";
                    header("location: $url");
                }
            }
        }
    }
}

// reject button
if (isset($_POST['reject-btn'])) {
    $task = 'reject';
    $response = $applicationObj->applicationOperation($task, $roomId, $applicantId);

    if ($response) {
        // create notification for tenant
        $tenantNotification->setApplicationNotification("room-application-reject", $roomId, $_SESSION['landlordUserId'], $applicantId, $applicationId);
        $tenantNotification->whose = "tenant";
        $response = $tenantNotification->register();

        if ($response) {
            $url = "application-detail.php?applicationId=$applicationId&tenantId=$applicantId&roomId=$roomId";
            header("Location: " . $url);
        }
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
            <p class="top-heading p-larger f-bold"> Application Detail </p>

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

                    <?php
                        $roomId = $_GET['roomId'];
                        $link = "myroom-detail.php?roomId=$roomId";
                    ?>
                    <div class="pointer flex-row application-detail-section" onclick="window.location.href='<?php echo $link; ?>'">
                        <img src="../../Assests/Icons/redirect.png" alt="" class="icon-class">
                        <p class="p-font n-light"> See Room Detail </p>
                    </div>

                    <div class="flex-row application-detail-section">
                        <p class="p-normal left"> Renting Type </p>
                        <p class="p-normal right">
                            <?php echo ucfirst($applicationObj->rentType); ?>
                        </p>

                    </div>

                    <div class="flex-row application-detail-section">
                        <p class="p-normal left"> Move in date </p>
                        <p class="p-normal right">
                            <?php echo $applicationObj->moveInDate; ?>
                        </p>

                    </div>

                    <div class="flex-row application-detail-section">
                        <p class="p-normal left"> Move out date </p>
                        <p class="p-normal right">
                            <?php
                            echo ($applicationObj->rentType == 'not-fixed') ? "-" : $applicationObj->moveOutDate;
                            ?>
                        </p>

                    </div>

                    <div class="flex-row application-detail-section">
                        <p class="p-normal left"> Note </p>
                        <p class="p-normal right">
                            <?php echo ucfirst($applicationObj->note); ?>
                        </p>
                    </div>

                    <div class="flex-row application-detail-section">
                        <p class="p-normal left"> Application Date </p>
                        <p class="p-normal right">
                            <?php echo $applicationObj->applicationDate; ?>
                        </p>
                    </div>

                    <div class="flex-row application-detail-section <?php if($applicationObj->cancelCount == 0) echo "hidden";?>">
                        <p class="p-normal left"> Cancel Count </p>
                        <p class="p-normal right">
                            <?php echo $applicationObj->cancelCount; ?>
                        </p>
                    </div>

                    <div class="flex-row application-detail-section">
                        <p class="p-normal left"> State </p>
                        <p class="p-normal right">
                            <?php
                            if ($applicationObj->state == 0)
                                echo "Pending";
                            elseif ($applicationObj->state == 1)
                                echo "Accepted";
                            elseif ($applicationObj->state == 2)
                                echo "Rejected";
                            elseif ($applicationObj->state == 3)
                                echo "Tenant cancelled";
                            elseif ($applicationObj->state == 4)
                                echo "Re-applied";
                            elseif ($applicationObj->state == 5)
                                echo "Re-applied/ Ex-tenant";
                            elseif ($applicationObj->state == 6)
                                echo "Current Tenant";
                            ?>

                            <?php if($applicationObj->cancelCount > 0) echo " - Previously cancelled"; ?>
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
                    if ($applicationObj->state == 0 || $applicationObj->state == 4) { // 0: pending, 4: re-applied
                        ?>
                        <button name="accept-btn" class="positive-button"> Accept </button>
                        <button name="reject-btn" class="negative-button"> Reject </button>
                        <?php
                    }
                    ?>
                </form>

                <?php
                if ($applicationObj->state == 1) { // accepted
                        ?>
                        <button id="make-tenant-trigger"> Make Tenant </button>
                        <?php
                    }

                    ?>
            </div>

            <!-- dark background -->
    <div id="dark-background"> </div>

            <!-- make tenant container -->
            <div class="container flex-column make-tenant-dialog-container" id="make-tenant-dialog-container">
                <div class="flex-column make-tenant-dialog-div">
                    <div class="flex-row top">
                        <p class="p-large f-bold"> Make Tenant </p>
                        <img src="../../Assests/Icons/Cancel-filled.png" alt="" id="make-tenant-close">
                    </div>

                    <div class="flex-column bottom">
                        <form action="" method="POST" class="flex-column make-tenant-form">
                            <!-- tenancy start date -->
                            <div class="flex-column tenancy-date-section">
                                <p class="p-normal n-light"> Set the tenancy start date. </p>
                                <input type="date" name="make-tenant-start-date" id="make-tenant-move-in-date"
                                    required>
                            </div>

                            <p class="p-normal warning note"> Note: Applicant will be registered as the tenant and your
                                room wont appear to others. </p>

                            <div class="flex-row agreement-section">
                                <input type="checkbox" name="make-tenant-agree" id="make-tenant-agree" required>
                                <label for="make-tenant-agree" class="p-normal"> I accept the aggrement. </label>
                            </div>

                            <button name="make-tenant-btn" id="make-tenant-btn"> Make Tenant </button>
                        </form>
                    </div>
                </div>
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

    <!-- make tenant script -->
    <script>
        const makeTenantContainer = $('#make-tenant-dialog-container');
        const makeTenantTrigger = $('#make-tenant-trigger');
        const makeTenantClose = $('#make-tenant-close');

        const darkBackground = $('#dark-background');

        makeTenantContainer.hide();
        darkBackground.hide();
       
        makeTenantTrigger.click(function () {
            darkBackground.show();
            makeTenantContainer.show();
        });

        makeTenantClose.click(function () {
            darkBackground.hide();
            makeTenantContainer.hide();
        });
    </script>
</body>

</html>