<?php
// staring session
if (!session_start())
    session_start();

// including files
include '../../class/functions.php';
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/application_class.php';
include '../../Class/leave_application_class.php';

// creating the object
$user = new User();
$room = new Room();
$house = new House();
$application = new Application();
$leaveApplication = new LeaveApplication();

$user->userId = $_SESSION['landlordUserId'];

if (!isset($_SESSION['landlordUserId'])) {
    // divert to the login page
    header("Location: landlord-login.php");
} else
    $user->fetchSpecificRow($_SESSION['landlordUserId']);

$myRoomIdArray = [];
$myRoomIdArray = getRoomIdArray($user->userId);

// application sets
$leaveApplicationSets = $leaveApplication->fetchLeaveApplicationsForLandlord($myRoomIdArray);

$totalLeaveApplicationCount = sizeof($leaveApplicationSets);
$pendingLeaveApplicationCount = $leaveApplication->countLeaveApplicationType($myRoomIdArray, 'pending');
$acceptedLeaveApplciationCount = $leaveApplication->countLeaveApplicationType($myRoomIdArray, 'accepted');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> Leave Application </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/common/table.css">

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <!-- jquery import -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="body-container flex-row">
        <aside class="empty-section"> </aside>

        <article class="content-article">
            <!-- heading -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold negative"> Leave Application </p>
            </div>

            <!-- application cards -->
            <div class="card-container flex-row content-container application-card-container">
                <!-- all application card -->
                <div class="flex-column pointer card" id="applied-room-card-all">
                    <p class="p-form f-bold"> &nbsp; <?php echo $totalLeaveApplicationCount; ?>
                </p>
                <p class="p-form"> Total Leave Applications </p>
                </div>
            
                <!-- Pending -->
                <div class="flex-column pointer card" id="applied-room-card-pending">
                    <p class="p-form f-bold"> &nbsp;
                        <?php echo $pendingLeaveApplicationCount; ?>
                    </p>
                    <p class="p-form"> Pending </p>
                </div>
            
                <!-- accepted -->
                <div class="flex-column pointer card" id="applied-room-card-accepted">
                    <p class="p-form f-bold"> &nbsp;
                        <?php echo $acceptedLeaveApplciationCount; ?>
                    </p>
                    <p class="p-form"> Accepted </p>
                </div>
            </div>

            <!-- filter & search -->
            <div class="container flex-row content-container filter-search-container">
                <div class="flex-row filter-div">
                    <div class="flex-row filter-icon-div ">
                        <img src="../../Assests/Icons/filter.png" alt="">
                    </div>

                    <!-- order select -->
                    <div class="flex-row filter-select-div order-div">
                        <label for="applied-room-application-type-select"> Application State </label>
                        <select name="applied-room-application-type-select" id="applied-room-application-type-select">
                            <option value="0"> All </option>
                            <option value="1"> Pending </option>
                            <option value="2"> Accepted </option>
                        </select>
                    </div>

                    <div class="flex-row pointer clear-filter-div" id="applied-room-clear-sort">
                        <p class="p-form"> Clear Sort </p>
                        <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                    </div>
                </div>
            </div>

            <div class="flex-column content-container application-container">
                <table id="applied-room-table">
                    <thead>
                        <th class="t-serial first-td"> S.N. </th>
                        <th class="t-room"> Room ID </th>
                        <th class="t-tenant"> Applicant </th>
                        <th class="t-move-in-date"> Leave Date </th>
                        <th class="t-note"> Note </th>
                        <th class="t-state"> State </th>
                        <th class="t-date"> Date </th>
                    </thead>

                    <tbody>
                        <?php
                        $serial = 1;
                        
                        if (sizeof($leaveApplicationSets) > 0) {
                            foreach ($leaveApplicationSets as $leaveApplicationSet) {
                                $room->fetchRoom($leaveApplicationSet['room_id']);
                                $location = $room->getLocation($room->houseId);
                                $roomId = $leaveApplicationSet['room_id'];
                                $applicantId = $leaveApplicationSet['tenant_id'];
                                $roomId = $leaveApplicationSet['room_id'];
                                $leaveApplicationId = $leaveApplicationSet['leave_application_id'];
                                $link = "leave-application-detail.php?leaveApplicationId=$leaveApplicationId";
                                ?>
                                <tr class="<?php echo 'applied-room-element';
                                if ($leaveApplicationSet['state'] == 0)
                                    echo "applied-room-pending-element";
                                elseif ($leaveApplicationSet['state'] == 1 || $leaveApplicationSet['state'] == 6)
                                    echo "applied-room-accepted-element";
                                elseif ($leaveApplicationSet['state'] == 2)
                                    echo "applied-room-rejected-element";
                                ?>" onclick="window.location.href='<?php echo $link; ?>'" ;>
                                    <td class="t-serial first-td">
                                        <?php echo $serial++; ?>
                                    </td>

                                    <!-- room id -->
                                    <?php $link = "myroom-detail.php?roomId=$roomId"; ?>
                                    <td class="t-room-id first-td">
                                        <a href="<?php echo $link; ?>">
                                            <?php echo $roomId; ?>
                                        </a>    
                                    </td>

                                    <!-- applicant name -->
                                    <td class="t-tenant">
                                        <?php echo $user->getUserName($leaveApplicationSet['tenant_id']); ?>
                                    </td>

                                    <!-- leave date -->
                                    <td class="t-move-in-date">
                                        <?php echo $leaveApplicationSet['leave_date']; ?>
                                    </td>

                                    <!-- note -->
                                    <td class="t-note">
                                        <?php echo ucfirst($leaveApplicationSet['note']); ?>
                                    </td>

                                    <!-- application state -->
                                    <td class="t-state">
                                        <?php
                                        if ($leaveApplicationSet['state'] == 0)
                                            echo "Pending";
                                        elseif ($leaveApplicationSet['state'] == 1)
                                            echo "Accepted";
                                        else
                                            echo "Rejected";
                                        ?>
                                    </td>

                                    <!-- application registered date -->
                                    <td class="t-date">
                                        <?php echo $leaveApplicationSet['application_date']; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- empty context -->
            <div class="container empty-data-container" id="empty-applied-room-data-message">
                <div class="flex-column div empty-data-div" id="empty-data-div">
                    <p class="p-normal negative"> Empty! </p>
                </div>
            </div>
        </article>
    </div>

    <!-- js section -->
    <!-- js section -->
    <script>
        const activeMenu = $('#leave-application-menu-id');
        activeMenu.css({
            "background-color" : "#DFDFDF"
        });
    </script>

    <script>
        // cards
        const appliedRoomCardAll = $('#applied-room-card-all');
        const appliedRoomCardPending = $('#applied-room-card-pending');
        const appliedRoomCardAccepted = $('#applied-room-card-accepted');
        const appliedRoomCardRejected = $('#applied-room-card-rejected');

        const appliedRoomApplicationSelect = $('#applied-room-application-type-select');
        const appliedRoomRentTypeSelect = $('#applied-room-rent-type-select');
        const appliedRoomClearSort = $('#applied-room-clear-sort');

        const emptyAppliedRoomDataMessage = $('#empty-applied-room-data-message');

        // application state elements
        var appliedRoomElements = $('.applied-room-element');
        var appliedRoomPendingElements = $('.applied-room-pending-element');
        var appliedRoomAcceptedElements = $('.applied-room-accepted-element');
        var appliedRoomRejectedElements = $('.applied-room-rejected-element');

        // rent type elements elements
        var appliedRoomFixedTypeElement = $('.applied-room-fixed-type-element');
        var appliedRoomNotFixedTypeElement = $('.applied-room-not-fixed-type-element');

        var appliedRoomType = 0;
        var appliedRoomRentType = 0;

        appliedRoomClearSort.hide();
        emptyAppliedRoomDataMessage.hide();

        appliedRoomCardAll.click(function () {
            appliedRoomType = 0;
            appliedRoomApplicationSelect[0].value = appliedRoomType;
            filterAppliedRoomApplication();
        });

        appliedRoomCardPending.click(function () {
            appliedRoomType = 1;
            appliedRoomApplicationSelect[0].value = appliedRoomType;
            filterAppliedRoomApplication();
        });

        appliedRoomCardAccepted.click(function () {
            appliedRoomType = 2;
            appliedRoomApplicationSelect[0].value = appliedRoomType;
            filterAppliedRoomApplication();
        });

        appliedRoomCardRejected.click(function () {
            appliedRoomType = 3;
            appliedRoomApplicationSelect[0].value = appliedRoomType;
            filterAppliedRoomApplication();
        });

        appliedRoomApplicationSelect.change(function () {
            appliedRoomType = appliedRoomApplicationSelect.val();
            filterAppliedRoomApplication();
        });

        appliedRoomRentTypeSelect.change(function () {
            appliedRoomRentType = appliedRoomRentTypeSelect[0].value;
            filterAppliedRoomApplication();
        });

        filterAppliedRoomApplication = () => {
            if (appliedRoomType != 0 || appliedRoomRentType != 0)
                appliedRoomClearSort.show();
            else
                appliedRoomClearSort.hide();

            appliedRoomElements.hide();

            if (appliedRoomType == 0)
                appliedRoomElements.show();
            else if (appliedRoomType == 1)
                appliedRoomPendingElements.show();
            else if (appliedRoomType == 2)
                appliedRoomAcceptedElements.show();
            else
                appliedRoomRejectedElements.show();

            if (appliedRoomRentType != 0) {
                if (appliedRoomRentType == 1)
                    appliedRoomNotFixedTypeElement.hide();
                else
                    appliedRoomFixedTypeElement.hide();
            }

            emptyAppliedRoomDataMessage.hide();
            if (countVisibleAppliedRoomRows() == 0)
                emptyAppliedRoomDataMessage.show();
        }

        countVisibleAppliedRoomRows = () => {
            var visibleRows = $("#applied-room-table tbody tr:visible");
            var visibleRowCount = visibleRows.length;
            return visibleRowCount;
        }

        filterAppliedRoomApplication();

        appliedRoomClearSort.click(function () {
            appliedRoomType = 0;
            appliedRoomRentType = 0;
            appliedRoomApplicationSelect[0].value = appliedRoomType;
            appliedRoomRentTypeSelect[0].value = appliedRoomRentType;
            filterAppliedRoomApplication();
        });
    </script>
</body>

</html>