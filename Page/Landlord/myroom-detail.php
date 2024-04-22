<?php
// session
if (!session_start())
    session_start();

// redirecting to login page is session variable is not set
if (!isset($_SESSION['landlordUserId']))
    header("Location: ../../");

// including external files
include '../../class/functions.php';
include '../../class/user_class.php';
include '../../class/house_class.php';
include '../../Class/room_review_class.php';
include '../../Class/tenancy_history_class.php';

// creating objects
$user = new User();
$room = new Room();
$house = new House();
$reviewer = new User();
$roomReview = new RoomReview();
$tenancyHistory = new TenancyHistory();

$user->userId = $_SESSION['landlordUserId'];

if (isset($_GET['roomId'])) {
    $roomId = $_GET['roomId'];

    // url tampering check
    if (!$room->isValidRoom($roomId, $_SESSION['landlordUserId']))
        header("location: myroom.php");

    $room->fetchRoom($roomId);
    $room->roomId = $roomId;
    $user->fetchSpecificRow($_SESSION['landlordUserId']);
} else
    header("Location: myroom.php");

$roomId = $_GET['roomId'];

$roomReview->setFinalRating($room->roomId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> RentRover - Room Detail </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/common/table.css">
    <link rel="stylesheet" href="../../CSS/tenant/room.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/house-detail.css">
    <link rel="stylesheet" href="../../CSS/admin/room-detail.css">
    <link rel="stylesheet" href="../../CSS/landlord/myroom-detail.css">
    <link rel="stylesheet" href="../../CSS/Common/lightbox.min.css">

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <!-- jquery -->
    <script src="../../Js/lightbox-plus-jquery.min.js"> </script>
</head>

<body>
    <?php
    include 'aside.php';
    ?>

    <div class="body-container flex-row">
        <div class="empty-section"></div>

        <article class="content-article flex-column">
            <!-- house name and rating section -->
            <div class="house-name-rating-container flex-column">
                <div class="name-container">
                    <p class="p-large f-bold">
                        <?php echo ucfirst($house->getHouseIdentity($room->houseId) . " >> Room No : " . $room->roomNumber); ?>
                    </p>
                </div>

                <div class="rating-container flex-row">
                    <div class="star-div flex-row">
                        <?php
                        if ($roomReview->numberOfReviews == 0) {
                            echo "No Reviews";
                        } else {
                            if (is_float($roomReview->cumulativeRating)) {
                                $myRoomRating = intval($roomReview->cumulativeRating);
                                for ($i = 0; $i < $myRoomRating; $i++) {
                                    ?>
                                    <img src="../../Assests/Icons/full-rating.png" class="icon class" alt="">
                                    <?php
                                }
                                ?>
                                <img src="../../Assests/Icons/half-rating.png" class="icon class" alt="">
                                <?php
                            } else {
                                for ($i = 0; $i < $roomReview->cumulativeRating; $i++) {
                                    ?>
                                    <img src="../../Assests/Icons/full-rating.png" class="icon class" alt="">
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>

                    <div class="rating-div">
                        <p class="p-form n-light">
                            <?php if ($roomReview->numberOfReviews != 0)
                                echo '(' . $roomReview->numberOfReviews . ' Reviews)'; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- house image section -->
            <div class="house-photo-container content-container flex-row">
                <div class="left flex-column">
                    <img src="../../Assests/Uploads/Room/<?php echo $room->roomPhotoArray[0]['room_photo']; ?>" alt="room photo">
                </div>

                <div class="right">
                    <?php
                    foreach ($room->roomPhotoArray as $roomPhoto) {
                        ?>
                        <div class="photo-div">
                            <a href="../../Assests/Uploads/Room/<?php echo $roomPhoto['room_photo']; ?>"
                                data-lightbox="room-photo">
                                <img src="../../Assests/Uploads/Room/<?php echo ($roomPhoto['room_photo'] == NULL)?"blank.jpg" :$roomPhoto['room_photo']; ?>" alt="room photo">
                            </a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <!-- detail -->
            <div class="house-detail-container content-container flex-row">
                <div class="house-detail-left-div flex-column">
                    <!-- requirement -->
                    <div class="requirement-container flex-column">
                        <p class="p-large f-bold n-light"> Room Requirements </p>
                        <div class="requirement-div">
                            <p class="p-normal">
                                <?php echo ($room->requirement != '') ? ucfirst($room->requirement) : "No requirement."; ?>
                            </p>
                        </div>
                    </div>

                    <!-- house requirement -->
                    <div class="requirement-container flex-column">
                        <p class="p-large f-bold n-light"> House Requirements </p>
                        <div class="requirement-div">
                            <p class="p-normal">
                                <?php echo ucfirst($room->getGeneralRequirement($room->houseId)); ?>
                            </p>
                        </div>
                    </div>

                    <!-- amenities container -->
                    <div class="amenities-container content-container flex-column">
                        <!-- heading -->
                        <p class="p-large f-bold n-light"> Amenities </p>
                        <div class="amenities-div">
                            <?php
                            $services = unserialize(base64_decode($room->amenities));
                            foreach ($services as $service) {
                                ?>
                                <div class="amenity flex-column card">
                                    <?php
                                    $amenityName = returnArrayValue("amenity", $service);
                                    $amenityIconName = returnIconName($amenityName);
                                    ?>
                                    <img src="../../Assests/Icons/amenities/<?php echo $amenityIconName; ?>"
                                        alt="Amenity icon">
                                    <p class="p-normal">
                                        <?php echo returnArrayValue("amenity", $service); ?>
                                    </p>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- right section -->
                <div class="house-detail-right-div flex-column">
                    <div class="verified-date-div flex-row">
                        <div class="verified-div flex-row">
                            <?php
                            if ($room->roomState == 0)
                                $icon = "report.png";
                            else
                                $icon = "verified.png";
                            ?>
                            <img src="../../Assests/Icons/<?php echo $icon; ?>" class="icon-class" alt="">
                            <p class="p-form">
                                <?php
                                if ($room->roomState == 0)
                                    echo "Unverified";
                                else
                                    echo "Verified";
                                ?>
                            </p>
                        </div>

                        <div class="date-div flex-row">
                            <img src="../../Assests/Icons/calendar.png" class="icon-class" alt="">
                            <p class="p-normal">
                                <?php
                                $registerDate = new DateTime($room->registerDate);
                                echo $registerDate->format('Y-m-d');
                                ?>
                            </p>
                        </div>
                    </div>

                    <!-- table detail -->
                    <table class="house-detail-table">
                        <tr>
                            <td class="detail-title"> Room ID </td>
                            <td class="detail-data">
                                <?php echo $_GET['roomId']; ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> Room Number </td>
                            <td class="detail-data">
                                <?php echo $room->roomNumber; ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> House ID </td>
                            <td class="detail-data">
                                <?php echo $room->houseId; ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> Room Type </td>
                            <td class="detail-data">
                                <?php echo ($room->roomType == 1) ? $room->bhk . " BHK, " : "Non BHK, "; ?>
                                <?php
                                if ($room->furnishing == 1)
                                    echo "Unfurnished";
                                elseif ($room->furnishing == 2)
                                    echo "Semi-Furnished";
                                else
                                    echo "Full-Furnished";
                                ?>
                            </td>
                        </tr>

                        <tr class="<?php if ($room->roomType == 1)
                            echo "hidden"; ?>">
                            <td class="detail-title"> No. of Room </td>
                            <td class="detail-data">
                                <?php echo ($room->roomType == 1) ? ($room->bhk + 2) : $room->numberOfRoom; ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> Floor </td>
                            <td class="detail-data">
                                <?php echo ($room->floor); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> Room State </td>
                            <td class="detail-data">
                                <?php echo ($room->isAcquired) ? "Acquired" : "Unacquired"; ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> Tenant </td>

                            <?php 
                            $tenantId = $room->tenantId;
                            $link = "tenants-detail.php?tenantId=$tenantId";
                            ?>
                            <td class="detail-data" onclick="window.location.href='<?php echo $link;?>'">
                                <?php echo ($room->isAcquired) ? $user->getUserName($room->tenantId):"-"; ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> Location </td>
                            <td class="detail-data">
                                <?php echo ucfirst($room->getLocation($room->houseId)); ?>
                            </td>
                        </tr>
                    </table>

                    <div class="flex-column operation-container">
                        <!-- edit room detail -->
                        <?php
                        $url = $_SERVER['REQUEST_URI'];
                        $roomId = $_GET['roomId'];
                        $link = "edit-room.php?roomId=$roomId";
                        ?>

                        <div class="flex-row operation-div" onclick="window.location.href='<?php echo $link; ?>'">
                            <img src="../../Assests/Icons/edit.svg" alt="">
                            <p class="p normal"> Edit Room Detail </p>
                        </div>

                        <!-- remove tenant -->
                        <?php $link = "operation/room-op.php?task=remove-tenant&roomId=$roomId&url=$url"; ?>
                        <div class="flex-row operation-div <?php if ($room->isAcquired == 0)
                            echo "hidden"; ?>" onclick="window.location.href='<?php echo $link; ?>'">
                            <img src="../../Assests/Icons/delete.png" alt="" class="icon-class">
                            <p class="p normal"> Remove Tenant </p>
                        </div>

                        <!-- remove room -->
                        <?php $link = "operation/room-op.php?task=remove&roomId=$roomId&url=$url"; ?>
                        <button class="negative-button" id="remove-house"
                            onclick="window.location.href='<?php echo $link; ?>'"> Remove Room </button>
                    </div>
                </div>
            </div>

            <!-- application -->
            <?php
            include_once '../../Class/application_class.php';
            $applicationObj = new Application();
            $applicationSets = $applicationObj->fetchApplicationsOfRoom($roomId);
            ?>

            <div class="section-heading-container content-container">
                <p class="p-large f-bold n-light"> Applications </p>
            </div>

            <!-- application cards -->
            <div class="card-container flex-row application-card-container">
                <!-- all application card -->
                <div class="flex-row pointer card" id="applied-room-card-all">
                    <p class="p-form"> Total Applications - </p>
                    <p class="p-form f-bold">
                        <?php echo $applicationObj->countRoomApplication($roomId, "all"); ?>
                    </p>
                </div>

                <!-- Pending -->
                <div class="flex-row pointer card" id="applied-room-card-pending">
                    <p class="p-form"> Pending - </p>
                    <p class="p-form f-bold">
                        <?php echo $applicationObj->countRoomApplication($roomId, "pending"); ?>
                    </p>
                </div>

                <!-- accepted -->
                <div class="flex-row pointer card" id="applied-room-card-accepted">
                    <p class="p-form"> Accepted - </p>
                    <p class="p-form f-bold">
                        <?php echo $applicationObj->countRoomApplication($roomId, "accepted"); ?>
                    </p>
                </div>

                <!-- rejected -->
                <div class="flex-row pointer card" id="applied-room-card-rejected">
                    <p class="p-form"> Rejected - </p>
                    <p class="p-form f-bold">
                        <?php echo $applicationObj->countRoomApplication($roomId, "rejected"); ?>
                    </p>
                </div>
            </div>

            <!-- filter & search -->
            <div class="container flex-row filter-search-container">
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

            <div class="flex-column application-container">
                <div class="flex-column application-div">
                    <div class="flex-row application-card">

                    </div>
                </div>

                <!-- applied room table -->
                <table id="applied-room-table">

                    <thead>
                        <th class="first-td"> S.N. </th>
                        <th class="t-tenant"> Tenant </th>
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
                        foreach ($applicationSets as $applicationSet) {
                            $room->fetchRoom($applicationSet['room_id']);
                            $location = $room->getLocation($room->houseId);

                            $applicantId = $applicationSet['tenant_id'];
                            $roomId = $applicationSet['room_id'];
                            $applicationId = $applicationSet['application_id'];
                            $link = "application-detail.php?applicationId=$applicationId&tenantId=$applicantId&roomId=$roomId";
                            ?>
                            <tr class="<?php echo "applied-room-element ";
                            if ($applicationSet['state'] == 0)
                                echo "applied-room-pending-element";
                            elseif ($applicationSet['state'] == 1)
                                echo "applied-room-accepted-element";
                            else
                                echo "applied-room-rejected-element";

                            echo ($applicationSet['rent_type'] == "fixed") ? " applied-room-fixed-type-element" : " applied-room-not-fixed-type-element";
                            ?>" onclick="window.location.href='<?php echo $link; ?>'" ;>
                                <td class="t-room-id first-td">
                                    <?php echo $serial++; ?>
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
                                    elseif ($applicationSet['state'] == 5)
                                        echo "Accepted/ Ex-Tenant";
                                    elseif ($applicationSet['state'] == 6)
                                        echo "Accepted/ Current Tenant";
                                    ?>
                                </td>

                                <td class="t-date">
                                    <?php echo $applicationSet['application_date']; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr class="" id="empty-applied-room-data-message">
                            <td colspan="8">
                                <p class="p-normal negative note"> No application found! </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- reviews -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold"> Reviews and Ratings </p>
            </div>

            <div class="review-container">
                <?php
                $roomReviewSets = $roomReview->fetchAllRoomReview($room->roomId);
                if (sizeof($roomReviewSets) > 0) {
                    ?>
                    <div class="review-div flex-column">
                        <?php
                        foreach ($roomReviewSets as $roomReviewSet) {
                            $reviewerId = $roomReviewSet['tenant_id'];
                            $reviewer->fetchUser($reviewerId);
                            $reviewerName = $reviewer->getUserName($reviewerId);
                            $reviewerPhoto = $reviewer->getUserPhoto($reviewerId);
                            $review = '"' . ucfirst($roomReviewSet['review_data']) . '"';
                            $rating = $roomReviewSet['rating'];
                            ?>

                            <!-- reviews -->
                            <div class="review flex-row">
                                <div class="left flex-column">
                                    <img src="../../Assests/Uploads/user/<?php echo $reviewerPhoto; ?>" alt="">
                                </div>

                                <div class="right flex-column">
                                    <div class="flex-row right-top">
                                        <div class="flex-column tenant-detail">
                                            <p class="p-form">
                                                <?php echo $reviewerName; ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex-column right-bottom">
                                        <p class="p-normal">
                                            <?php echo $review; ?>
                                        </p>

                                        <div class="flex-row rating-div">
                                            <?php
                                            for ($i = 0; $i < $rating; $i++) {
                                            ?>
                                                <img src="../../Assests/Icons/full-rating.png" class="icon class" alt="">
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <abbr title="Report this review">
                                    <img src="../../Assests/Icons/report.png" alt="" class="icon-class pointer">
                                </abbr>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="flex-row empty-room-review">
                        <p class="p-normal negative"> No Reviews has been made for this room. </p>
                    </div>
                    <?php
                }
                ?>
            </div>

            <!-- tenancy history table -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold"> Tenancy History </p>
            </div>

            <table class="table-class resided-room-table">
                        <thead>
                            <th class="t- first-td"> S.N. </th>
                            <th class="t-"> Tenant </th>
                            <th class="t-"> Move In Date </th>
                            <th class="t-"> Move Out Date </th>
                        </thead>

                        <tbody>
                            <?php
                            $tenancyHistorySets = $tenancyHistory->fetchTenancyHistoryOfRoom($_GET['roomId']);
                            if(sizeof($tenancyHistorySets) > 0){
                                $serial = 0;
                                foreach($tenancyHistorySets as $tenancyHistorySet){
                                    ?>
                                    <tr>
                                        <td class="t-serial first-td">
                                            <?php echo ++$serial; ?>
                                        </td>
                                            
                                        <td class="t-">
                                            <?php echo $user->getUserName($tenancyHistorySet['tenant_id']); ?> 
                                        </td>

                                        <td class="t-">
                                            <?php echo $tenancyHistorySet['move_in_date']; ?>
                                        </td>

                                        <td class="t-">
                                            <?php 
                                            echo ($tenancyHistorySet['move_out_date'] == "0000-00-00")?"Still residing":$tenancyHistorySet['move_out_date'];
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
        </article>
    </div>

    <!-- script section -->
    <script src="../../Js/lightbox-plus-jquery.min.js"> </script>

    <!-- js section -->
    <script>
        const activeMenu = $('#room-menu-id');
        activeMenu.css({
            "background-color" : "#DFDFDF"
        });
    </script>

    <!-- applied room js -->
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