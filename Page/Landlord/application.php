<?php
// staring session
if (!session_start()) {
    session_start();
}

// including files
include '../../class/functions.php';
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/application_class.php';

// creating the object
$user = new User();
$room = new Room();
$houseObj = new House();
$applicationObj = new Application();

$user->userId = $_SESSION['landlordUserId'];

if (!isset($_SESSION['landlordUserId'])) {
    // divert to the login page
    header("Location: landlord-login.php");
} else
    $user->fetchSpecificRow($_SESSION['landlordUserId']);

$myRoomIdArray = [];
$myRoomIdArray = getRoomIdArray($user->userId);

// application sets
$applicationSets = $applicationObj->fetchApplicationsForLandlord($myRoomIdArray);

$totalAnnouncementCount = sizeof($applicationSets);
$pendingAnnouncementCount = $applicationObj->countApplicationType($applicationSets, 'pending');
$acceptedAnnouncementCount = $applicationObj->countApplicationType($applicationSets, 'accepted');
$rejectedAnnouncementCount = $applicationObj->countApplicationType($applicationSets, 'rejected');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> Application </title>

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
                <p class="heading f-bold negative"> Application </p>
            </div>

            <!-- application cards -->
            <div class="card-container flex-row content-container application-card-container">
                <!-- all application card -->
                <div class="flex-column pointer card" id="applied-room-card-all">
                    <p class="p-form f-bold"> &nbsp; <?php echo $totalAnnouncementCount; ?>
                </p>
                <p class="p-form"> Total Applications </p>
                </div>
            
                <!-- Pending -->
                <div class="flex-column pointer card" id="applied-room-card-pending">
                    <p class="p-form f-bold"> &nbsp;
                        <?php echo $pendingAnnouncementCount; ?>
                    </p>
                    <p class="p-form"> Pending </p>
                </div>
            
                <!-- accepted -->
                <div class="flex-column pointer card" id="applied-room-card-accepted">
                    <p class="p-form f-bold"> &nbsp;
                        <?php echo $acceptedAnnouncementCount; ?>
                    </p>
                    <p class="p-form"> Accepted </p>
                </div>
            
                <!-- rejected -->
                <div class="flex-column pointer card" id="applied-room-card-rejected">
                    <p class="p-form f-bold"> &nbsp;
                        <?php echo $rejectedAnnouncementCount; ?>
                    </p>
                    <p class="p-form"> Rejected </p>
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
                            <option value="3"> Rejected </option>
                        </select>

                        <label for="applied-room-rent-type-select"> Rent Type </label>
                        <select name="applied-room-rent-type-select" id="applied-room-rent-type-select">
                            <option value="0"> All </option>
                            <option value="1"> Fixed </option>
                            <option value="2"> Not-Fixed </option>
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
                        <th class="t-rent-type"> Rent Type </th>
                        <th class="t-move-in-date"> Move In Date </th>
                        <th class="t-move-out-date"> Move Out Date </th>
                        <th class="t-note"> Note </th>
                        <th class="t-state"> State </th>
                        <th class="t-date"> Date </th>
                    </thead>

                    <tbody>
                        <?php
                        $serial = 1;
                        if (sizeof($applicationSets) > 0) {
                            foreach ($applicationSets as $applicationSet) {
                                $room->fetchRoom($applicationSet['room_id']);
                                $location = $room->getLocation($room->houseId);
                                $roomId = $applicationSet['room_id'];
                                $applicantId = $applicationSet['tenant_id'];
                                $roomId = $applicationSet['room_id'];
                                $applicationId = $applicationSet['application_id'];
                                $link = "application-detail.php?applicationId=$applicationId&tenantId=$applicantId&roomId=$roomId";
                                ?>
                                <tr class="<?php echo "applied-room-element ";
                                if ($applicationSet['state'] == 0)
                                    echo "applied-room-pending-element";
                                elseif ($applicationSet['state'] == 1 || $applicationSet['state'] == 6)
                                    echo "applied-room-accepted-element";
                                elseif ($applicationSet['state'] == 2)
                                    echo "applied-room-rejected-element";

                                echo ($applicationSet['rent_type'] == "fixed") ? " applied-room-fixed-type-element" : " applied-room-not-fixed-type-element";
                                ?>" onclick="window.location.href='<?php echo $link; ?>'" ;>
                                    <td class="t-serial first-td">
                                        <?php echo $serial++; ?>
                                    </td>

                                    <?php $link = "myroom-detail.php?roomId=$roomId"; ?>

                                    <td class="t-room-id first-td">
                                        <a href="<?php echo $link; ?>">
                                            <?php echo $roomId; ?>
                                        </a>    
                                    </td>

                                    <td class="t-tenant">
                                        <?php echo $user->getUserName($applicationSet['tenant_id']); ?>
                                    </td>

                                    <td class="t-rent-type">
                                        <?php echo ucfirst($applicationSet['rent_type']); ?>
                                    </td>

                                    <td class="t-move-in-date">
                                        <?php echo $applicationSet['move_in_date']; ?>
                                    </td>

                                    <td class="t-move-out-date">
                                        <?php echo ($applicationSet['move_out_date'] != "0000-00-00") ? $applicationSet['move_out_date'] : "-"; ?>
                                    </td>

                                    <td class="t-note">
                                        <?php echo ucfirst($applicationSet['note']); ?>
                                    </td>

                                    <td class="t-state">
                                        <?php
                                        if ($applicationSet['state'] == 0)
                                            echo "Pending";
                                        elseif ($applicationSet['state'] == 1)
                                            echo "Accepted";
                                        elseif ($applicationSet['state'] == 2)
                                            echo "Rejected";
                                        elseif ($applicationSet['state'] == 6)
                                            echo "Current Tenant";
                                        else
                                            echo "Left Out";
                                        ?>
                                    </td>

                                    <td class="t-date">
                                        <?php echo $applicationSet['application_date']; ?>
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
        const activeMenu = $('#application-menu-id');
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