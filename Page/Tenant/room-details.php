<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['tenantUserId']))
    header("Location: ../../index.php");

// including files
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/notification_class.php';
include '../../Class/room_review_class.php';
include '../../Class/application_class.php';
include '../../class/functions.php';
include_once '../../class/wishlist_class.php';

// creating the object
$user = new User();
$reviewer = new User();
$room = new Room();
$roomObj = new Room();
$house = new House();
$houseObj = new House();
$application = new Application();
$roomReview = new RoomReview();
$notification = new Notification();

// setting the values
$user->userId = $_SESSION['tenantUserId'];

if (!isset($_SESSION['tenantUserId']))
    header("Location: ../../index.php");
else
    $user->fetchSpecificRow($_SESSION['tenantUserId']);

// getting notification count
$notificationCount = $notification->countNotification("tenant", $user->userId, "unseen");

// widhlist
$wishlist = new Wishlist();
$wishlistCount = $wishlist->countWishes($_SESSION['tenantUserId']);

// setting up room details
$room->fetchRoom($_GET['roomId']);
$room->setKeyValue("id", $_GET['roomId']);
$roomId = $room->roomId;

// url tampering check
// check if the room is valid
if (!$room->isValidRoom($roomId, 0))
    header("location: home.php");

$room->fetchHouse($room->houseId);

$roomReview->setFinalRating($room->roomId);

$url = $_SERVER['REQUEST_URI'];   
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/tenant/navbar.css">
    <link rel="stylesheet" href="../../CSS/tenant/room.css">
    <link rel="stylesheet" href="../../CSS/tenant/room-detail.css">
    <link rel="stylesheet" href="../../CSS/tenant/footer.css">
    <link rel="stylesheet" href="../../CSS/tenant/tenant.css">
    <link rel="stylesheet" href="../../CSS/tenant/room_apply.css">

    <!-- lightbox css -->
    <link rel="stylesheet" href="../../CSS/Common/lightbox.min.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- title -->
    <title> Room Detail </title>

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <!-- jquery -->
    <script src="../../Js/lightbox-plus-jquery.min.js"></script>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <!-- selected room detail -->
    <div class="room-detail-container container flex-row">
        <div class="room-detail-div div flex-column">
            <!-- house name and rating section -->
            <div class="house-name-rating-button-container flex-row">
                <div class="flex-column house-name-rating-container">
                    <div class="name-container">
                        <p class="p-large f-bold">
                            <?php echo ucfirst($house->getHouseIdentity($room->houseId) . " >> Room No : " . $room->roomNumber); ?>
                        </p>
                    </div>

                    <div class="flex-row rating-container">
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

                <div class="flex-row apply-button-div">
                    <?php
                    if ($wishlist->isWish($_SESSION['tenantUserId'], $room->roomId)) {
                        ?>
                        <a
                            href="operation/wishlist-op.php?userId=<?php echo $_SESSION['tenantUserId']; ?>&task=remove&roomId=<?php echo $room->roomId; ?>&url=<?php echo $_SERVER['REQUEST_URI']; ?>">
                            <img src="../../Assests/Icons/saved.png" alt="">
                        </a>
                        <?php
                    } else {
                        ?>
                        <a
                            href="operation/wishlist-op.php?userId=<?php echo $_SESSION['tenantUserId']; ?>&task=add&roomId=<?php echo $room->roomId; ?>&url=<?php echo $_SERVER['REQUEST_URI']; ?>">
                            <img src="../../Assests/Icons/unsaved.png" alt="">
                        </a>
                        <?php
                    }
                    ?>

                    <!-- check for redundant room apply -->
                    <?php
                    include_once '../../Class/application_class.php';
                    $applciation = new Application();
                    $applyState = $applciation->getApplicationState($room->roomId, $user->userId);
                    $roomId = $room->roomId;
                    $tenantId = $_SESSION['tenantUserId'];
                    $url = $_SERVER['REQUEST_URI'];
                    $link = "operation/room-apply-cancel.php?roomId=$roomId&tenantId=$tenantId&url=$url";

                    if ($applyState == -1) {
                        ?>
                        <button id="room-apply-form-show"> Apply Now </button>
                        <?php
                    } elseif ($applyState == 0) {
                        ?>
                        <?php
                        $task = 'cancel';
                        $link = "operation/room-apply-cancel.php?roomId=$roomId&tenantId=$tenantId&task=$task&url=$url";
                        ?>
                        <button class="negative-button" onclick="window.location.href='<?php echo $link; ?>'"> Application
                            Pending - Cancel </button>
                        <?php
                    } elseif ($applyState == 1 || $applyState == 4) {
                        $task = ($applyState == 1) ? 'cancel' : 'leave-room';
                        $link = $link . '&task=' . $task;

                        if ($applyState == 1) {
                            $link = "operation/room-apply-cancel.php?roomId=$roomId&tenantId=$tenantId&task=$task&url=$url";
                            ?>
                            <button class="positive-button" onclick="window.location.href='<?php echo $link; ?>'"> Application
                                Accepted - Cancel </button>
                            <?php
                        } else {
                            $link = 'account.php?task=my-room';
                            ?>
                            <button class="negative-button" onclick="window.location.href='<?php echo $link; ?>'"> Leave Room
                            </button>
                            <?php
                        }
                    } elseif ($applyState == 2 || $applyState == 3 || $applyState == 5) {
                        $task = 're-apply';
                        $link = "operation/room-apply-re-apply.php?roomId=$roomId&tenantId=$tenantId&task=$task&url=$url";
                        ?>
                        <button class="warning-button" onclick="window.location.href='<?php echo $link; ?>'">
                            <?php
                            if ($applyState == 2)
                                echo "Rejected/ Re-Apply";
                            elseif ($applyState == 3)
                                echo "Cancelled/ Re-Apply";
                            elseif ($applyState == 5)
                                echo "Previously Rented/ Re-Apply";
                            ?>
                        </button>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <div id="dark-background">
                <p> . </p>
            </div>

            <!-- room image section -->
            <div class="room-photo-container content-container flex-row">
                <div class="left flex-column">
                    <img src="../../Assests/Uploads/Room/<?php echo $room->roomPhotoArray[0]['room_photo']; ?>" alt="">
                </div>

                <div class="right">
                    <div class="photo-div">
                        <a href="../../Assests/Uploads/Room/<?php echo $room->roomPhotoArray[0]['room_photo']; ?>"
                            data-lightbox="room-photo">
                            <img src="../../Assests/Uploads/Room/<?php echo $room->roomPhotoArray[0]['room_photo']; ?>"
                                id="photo-4" alt="">
                        </a>
                    </div>

                    <div class="photo-div">
                        <a href="../../Assests/Uploads/Room/<?php echo $room->roomPhotoArray[1]['room_photo']; ?>"
                            data-lightbox="room-photo">
                            <img src="../../Assests/Uploads/Room/<?php echo $room->roomPhotoArray[1]['room_photo']; ?>"
                                id="photo-4" alt="">
                        </a>
                    </div>

                    <div class="photo-div">
                        <a href="../../Assests/Uploads/Room/<?php echo $room->roomPhotoArray[2]['room_photo']; ?>"
                            data-lightbox="room-photo">
                            <img src="../../Assests/Uploads/Room/<?php echo $room->roomPhotoArray[2]['room_photo']; ?>"
                                id="photo-4" alt="">
                        </a>
                    </div>

                    <div class="photo-div">
                        <a href="../../Assests/Uploads/Room/<?php echo $room->roomPhotoArray[3]['room_photo']; ?>"
                            data-lightbox="room-photo">
                            <img src="../../Assests/Uploads/Room/<?php echo $room->roomPhotoArray[3]['room_photo']; ?>"
                                id="photo-4" alt="">
                        </a>
                    </div>
                </div>
            </div>

            <!-- detail -->
            <div class="room-detail-container content-container flex-row">
                <div class="room-detail-left-div flex-column">
                    <!-- contact -->
                    <div class="shadow flex-row contact-container <?php echo ($applyState == 1) ? "" : "hidden"; ?>">
                        <div class="left">
                            <p class="p-normal f-bold"> Contact the owner for further details. </p>
                        </div>

                        <div class="flex-row right">
                            <img src="../../Assests/Icons/call.png" alt="" class="icon-class">
                            <p class="p-normal f-bold"> 9823645014 </p>
                        </div>

                    </div>
                    <!-- requirement -->
                    <div class="requirement-container flex-column">
                        <p class="p-large f-bold n-light"> Room Requirements </p>
                        <div class="requirement-div">
                            <p class="p-normal">
                                <?php echo (ucfirst($room->requirement) == ' ') ? ucfirst($room->requirement) : "No requirment"; ?>
                            </p>
                        </div>
                    </div>

                    <!-- house requirement -->
                    <div class="requirement-container content-container flex-column">
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
                            $amenities = unserialize(base64_decode($room->amenities));
                            foreach ($amenities as $amenity) {
                                ?>
                                <div class="amenity flex-column card">
                                    <?php
                                    $amenityName = returnArrayValue("amenity", $amenity);
                                    $amenityIconName = returnIconName($amenityName);
                                    ?>
                                    <img src="../../Assests/Icons/amenities/<?php echo $amenityIconName; ?>"
                                        alt="Amenity icon">
                                    <p class="p-normal">
                                        <?php echo returnArrayValue("amenity", $amenity); ?>
                                    </p>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    <!-- advertisement -->
                    <div class="content-container ad-container">
                        <img src="../../Assests/Images/ad-1.png" alt="">
                    </div>
                </div>

                <!-- right section -->
                <div class="room-detail-right-div flex-column">
                    <div class="verified-date-div flex-row">
                        <div class="verified-div flex-row">
                            <?php
                            if ($room->roomState == 0)
                                $icon = "report.png";
                            elseif ($room->roomState == 1)
                                $icon = "verified.png";
                            else
                                $icon = "report.png";
                            ?>
                            <img src="../../Assests/Icons/<?php echo $icon; ?>" class="icon-class" alt="">
                            <p class="p-form">
                                <?php
                                if ($room->roomState == 0)
                                    echo "Unverified";
                                elseif ($room->roomState == 1)
                                    echo "Verified";
                                else
                                    echo "Suspended";
                                ?>
                            </p>
                        </div>

                        <!-- report -->
                        <div class="date-div flex-row">
                            <abbr title="Report this room">
                                <img src="../../Assests/Icons/report.png" class="icon-class" alt="">
                            </abbr>
                        </div>
                    </div>

                    <!-- table detail -->
                    <table class="room-detail-table">
                        <tr>
                            <td class="detail-title"> Room Number </td>
                            <td class="detail-data">
                                <?php echo $room->roomNumber; ?>
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

                        <!-- number of room -->
                        <tr class="<?php if ($room->roomType == 1)
                            echo "hidden"; ?>">
                            <td class="detail-title"> No. of Room </td>
                            <td class="detail-data">
                                <?php echo ($room->roomType == 1) ? ($room->bhk + 2) : $room->numberOfRoom; ?>
                            </td>
                        </tr>

                        <!-- floor -->
                        <tr>
                            <td class="detail-title"> Floor </td>
                            <td class="detail-data">
                                <?php echo ($room->floor); ?>
                            </td>
                        </tr>

                        <!-- rent -->
                        <tr>
                            <td class="detail-title"> Rent </td>
                            <td class="detail-data">
                                <?php echo returnFormattedPrice($room->rentAmount); ?>
                            </td>
                        </tr>

                        <!-- room acquired state -->
                        <tr>
                            <td class="detail-title"> Room State </td>
                            <td class="detail-data">
                                <?php echo ($room->isAcquired) ? "Acquired" : "Unacquired"; ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> Location </td>
                            <td class="detail-data">
                                <?php echo ucfirst($room->getLocation($room->houseId)); ?>
                            </td>
                        </tr>
                    </table>

                    <?php
                    $house->fetchHouse($room->houseId);
                    ?>

                    <!-- house details -->
                    <div class="house-detail-container flex-column">
                        <div class="heading-div f-bold p-large">
                            <p class="p-normal" style="line-height:32px;"> House Photos </p>
                        </div>

                        <div class="top-photo-container">
                            <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[0]['house_photo']; ?>"
                                alt="">
                        </div>

                        <div class="bottom-photo-container">
                            <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[1]['house_photo']; ?>"
                                alt="">
                            <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[2]['house_photo']; ?>"
                                alt="">
                            <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[3]['house_photo']; ?>"
                                alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- reviews -->
    <div class="section-heading-container container content-container">
        <div class="div">
            <p class="p-large f-bold n-light"> Reviews and Ratings </p>
        </div>
    </div>

    <div class="content-container container review-container">
        <?php
        $roomReviewSets = $roomReview->fetchAllRoomReview($room->roomId);
        if (sizeof($roomReviewSets) > 0) {
            ?>
            <div class="div review-div flex-column">
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
                    <div class="flex-row review">
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
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        } else {
            ?>
            <div class="div flex-row empty-room-review">
                <p class="p-normal negative"> No Reviews has been made for this room. </p>
            </div>
            <?php
        }
        ?>
    </div>

    <!-- room apply form -->
    <div class="container flex-column room-apply-container" id="room-apply-container">
        <div class="div flex-column room-apply-div">
            <?php $link = "operation/room-apply-form.php?roomId=$roomId"; ?>

            <form action="<?php echo $link; ?>" method="POST" class="flex-column room-apply-form" id="room-apply-form">
                <div class="flex-row top-section">
                    <h3> Room Apply Form </h3>
                    <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class"
                        id="room-apply-form-close">
                </div>

                <!-- error message section -->
                <div class="flex-row room-apply-error-message-section" id="room-apply-error-message">
                    <p class="p-normal negative" id="room-apply-error-message"> This is an error message section. </p>
                </div>

                <!-- phase 1 -->
                <div class="flex-column room-apply-phase-1" id="room-apply-phase-1">
                    <p class="p-normal"> Select the renting type. </p>
                    <select name="room-apply-rent-type-select" id="room-apply-rent-type-select">
                        <option value="0" selected> Not-fixed </option>
                        <option value="1"> Fixed </option>
                    </select>

                    <div class="pointer flex-row room-apply-next-phase-div" id="room-apply-next-phase-div">
                        <p class="p-normal"> Next </p>
                    </div>
                </div>

                <!-- phase 2 -->
                <div class="flex-column room-apply-phase-2" id="room-apply-phase-2">
                    <!-- fixed time -->
                    <div class="flex-column room-apply-move-in-date-container" id="room-apply-move-in-date-container">
                        <label for="room-apply-move-in-date"> Move in date </label>
                        <input type="date" name="room-apply-move-in-date" id="room-apply-move-in-date">
                    </div>

                    <!-- not fixed time -->
                    <div class="flex-column room-apply-move-out-date-container" id="room-apply-move-out-date-container">
                        <label for="room-apply-move-out-date"> Move out date </label>
                        <input type="date" name="room-apply-move-out-date" id="room-apply-move-out-date">
                    </div>

                    <label for="room-apply-note"> Note </label>
                    <textarea name="room-apply-note" id="room-apply-note" cols="30" rows="10"
                        placeholder="Here..."></textarea>

                    <input type="submit" name="room-apply-submit-btn" value="Apply Now">

                    <div class="pointer flex-row room-apply-previous-phase-div" id="room-apply-previous-phase-div">
                        <p class="p-normal"> Previous </p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Heading : Other rooms in this house  -->
    <div class="heading-container container">
        <div class="div">
            <p class="p-large f-bold"> Other rooms in this house </p>
        </div>
    </div>

    <!-- room container -->
    <div class="room-main-container content-container container flex-column">
        <div class="room-container div flex-row">
            <?php
            $sets = $roomObj->fetchRoomsOfThisHouseForTenant($room->houseId, false, $room->roomId);
            if (count($sets) > 0) {
                foreach ($sets as $set) {
                    // processing data
                    $photo = $roomObj->getRoomPhoto($set['room_id']);
                    $amenities = unserialize(base64_decode($set['amenities']));
                    ?>
                    <div class="room flex-row">
                        <div class="room-photo-div flex-column">
                            <img src="../../Assests/Uploads/Room/<?php echo $photo; ?>" alt="">
                        </div>

                        <!-- spec -->
                        <div class="room-spec-div flex-column">

                            <div class="verified-wishlist-div flex-row">
                                <div class="verified-div flex-row">
                                    <img class="icon-class" src="../../Assests/Icons/Verified.png" alt="">
                                    <p class="positive p-form f-bold"> Verified </p>
                                </div>

                                <div class="wishlist-div">
                                    <?php
                                    if ($wishlist->isWish($_SESSION['tenantUserId'], $set['room_id'])) {
                                        $userId = $_SESSION['tenantUserId'];
                                        $roomId = $set['room_id'];
                                        $link = "operation/wishlist-op.php?userId=$userId&task=remove&roomId=$roomId&url=$url";
                                        ?>
                                        <a
                                            href="<?php echo $link; ?>">
                                            <img src="../../Assests/Icons/saved.png" alt="">
                                        </a>
                                        <?php
                                    } else {
                                        $userId = $_SESSION['tenantUserId'];
                                        $roomId = $set['room_id'];
                                        $link = "operation/wishlist-op.php?userId=$userId&task=add&roomId=$roomId&url=$url";
                                        ?>
                                        <a
                                            href="<?php echo $link; ?>">
                                            <img src="../../Assests/Icons/unsaved.png" alt="">
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- location -->
                            <div class="location-div">
                                <p class="p-normal f-bold">
                                    <?php echo ucfirst($houseObj->getLocation($set['house_id'])); ?>
                                </p>
                            </div>

                            <!-- bhk & floor -->
                            <div class="bhk-floor-div">
                                <p class="p-normal n-normal">
                                    <?php
                                    if ($set['room_type'] == 1)
                                        echo $set['bhk'] . ' BHK, ';
                                    else {
                                        echo $set['number_of_room'];

                                        if ($set['number_of_room'] > 1)
                                            echo " Rooms, ";
                                        else
                                            echo " Room, ";
                                    }
                                    ?>

                                    <?php
                                    if ($set['floor'] == 1)
                                        echo $set['floor'] . 'st Floor';
                                    elseif ($set['floor'] == 2)
                                        echo $set['floor'] . 'nd Floor';
                                    elseif ($set['floor'] % 10 == 3)
                                        echo $set['floor'] . 'rd Floor';
                                    else {
                                        echo $set['floor'] . 'th Floor';
                                    }
                                    ?>
                                </p>
                            </div>

                            <!-- amenities -->
                            <abbr title="<?php foreach ($amenities as $amenity)
                                echo returnArrayValue('amenity', $amenity) . ', '; ?>">
                                <div class="amenity-div">
                                    <?php foreach ($amenities as $amenity)
                                        echo returnArrayValue('amenity', $amenity) . ', '; ?>
                                </div>
                            </abbr>

                            <!-- price -->
                            <div class="price-div">
                                <p class="p-form f-bold">
                                    <?php echo returnFormattedPrice($set['rent_amount']); ?>/ <span class="n-normal f-normal">
                                        Month </span>
                                </p>
                            </div>

                            <!-- rating & show more section -->
                            <div class="price-show-more-div flex-row">
                                <!-- rating -->
                                <div class="rating-div flex-row">
                                    <?php $roomReview->setFinalRating($set['room_id']); ?>
                                    <img class="icon-class" src="../../Assests/Icons/full-rating.png" alt="">
                                    <p class="p-form f-bold">
                                        <?php echo $roomReview->cumulativeRating; ?>
                                    </p>
                                </div>

                                <div class="show-more-div flex-row">
                                    <a href="room-details.php?roomId=<?php echo $set['room_id']; ?>">
                                        <button class="normal-button"> Show More </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p class="p-normal negative"> This is only the room in this house. </p>';
            }
            ?>
        </div>
    </div>

    <!-- footer -->
    <?php include 'footer.php'; ?>

    <!-- js section -->
    <script>
        var userMenuState = false;
        var notificationMenuState = false;

        const userMenu = document.getElementById('menu-container');
        const logoutDialog = document.getElementById('logout-dialog-container');
        const notificationMenu = document.getElementById('notification-container');

        onload = () => {
            // logoutDialog.style = "display:none";
            userMenu.style = "display:none";
            notificationMenu.style = "display:none";
        }

        showReviewForm = () => {
            console.log("Hello!");
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

        logout = () => {
            logoutDialog.style = "display:flex";
        }

        hideLogoutDialog = () => {
            logoutDialog.style = "display:none";
            userMenuState = false;
            userMenu.style = "display:none";
        }
    </script>

    <!-- script for room apply -->
    <script>
        const darkBackground = $('#dark-background');
        const roomApplyContainer = $('#room-apply-container');
        const roomApplyPhaseController = $('#room-apply-phase-controller');
        const roomApplyClose = $('#room-apply-form-close');
        const roomApplyOpen = $('#room-apply-form-show');
        const roomApplyNextPhase = $('#room-apply-next-phase-div');
        const roomApplyPreviousPhase = $('#room-apply-previous-phase-div');
        const roomApplyPhase1 = $('#room-apply-phase-1');
        const roomApplyPhase2 = $('#room-apply-phase-2');

        // form values
        var roomApplyRentTypeElement = $('#room-apply-rent-type-select');
        var roomApplyMoveInDateElement = $('#room-apply-move-in-date');
        var roomApplyMoveOutDateElement = $('#room-apply-move-out-date');

        $('#room-apply-error-message').hide();

        $(document).ready(function () {
            // Add event listener for form submission
            $("#room-apply-form").submit(function (event) {
                // Prevent the default form submission
                event.preventDefault();

                // Get form values
                var rentType = roomApplyRentTypeElement.val();
                var moveInDate = roomApplyMoveInDateElement.val();
                var moveOutDate = roomApplyMoveOutDateElement.val();

                // check form values correctness
                if (moveInDate === null || moveInDate.trim() === '') {
                    $('#room-apply-error-message').show();
                    $('#room-apply-error-message').text("Please set the move in date.");
                } else {
                    if (rentType == 1) {
                        if (moveOutDate === null || moveOutDate.trim() === '') {
                            $('#room-apply-error-message').show();
                            $('#room-apply-error-message').text("Please set the move out date.");
                        } else {
                            // move out date should be greater than move in date
                            if (moveInDate >= moveOutDate) {
                                $('#room-apply-error-message').show();
                                $('#room-apply-error-message').text("Make sure you chose move in and move out date correctly.");
                            } else {
                                // proceed to form submission
                                this.submit();
                            }
                        }
                    } else {
                        // proceed to form submission
                        this.submit();
                    }
                }
            });
        });

        roomApplyContainer.hide();
        darkBackground.hide();

        roomApplyOpen.click(function () {
            roomApplyContainer.show();
            darkBackground.show();
            roomApplyPhase1.show();
            roomApplyPhase2.hide();
        });

        roomApplyClose.click(function () {
            roomApplyContainer.hide();
            darkBackground.hide();
        });

        roomApplyNextPhase.click(function () {
            roomApplyPhase1.hide();
            roomApplyPhase2.show();
        });

        roomApplyPreviousPhase.click(function () {
            roomApplyPhase1.show();
            roomApplyPhase2.hide();
        });

        // form values : select
        const rentTypeSelect = $('#room-apply-rent-type-select');
        const moveInDate = $('#room-apply-move-in-date-container');
        const moveOutDate = $('#room-apply-move-out-date-container');

        moveOutDate.hide();

        rentTypeSelect.change(function () {
            if (rentTypeSelect[0].value == 0)
                moveOutDate.hide();
            else
                moveOutDate.show();
        });

        console.log();
    </script>
</body>

</html>