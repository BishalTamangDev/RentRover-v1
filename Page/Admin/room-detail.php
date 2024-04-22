<?php
// session
if (!session_start())
    session_start();

// redirecting to login page is session variable is not set
if (!isset($_SESSION['adminId']))
    header("Location: login.php");

// including external files
include '../../class/user_class.php';
include '../../class/house_class.php';
include '../../Class/room_review_class.php';
include '../../class/functions.php';

// creating objects
$user = new User();
$reviewer = new User();
$house = new House();
$room = new Room();
$roomReview = new RoomReview();

if (isset($_GET['roomId'])) {
    $roomId = $_GET['roomId'];

    // url tampering check
    if (!$room->isValidRoom($roomId, 0))
        header("location: rooms.php");

    $room->fetchRoom($roomId);
    $room->roomId = $roomId;
} else
    header("Location: rooms.php");

$roomReview->setFinalRating($room->roomId);

// page url
$url = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/house-detail.css">
    <link rel="stylesheet" href="../../CSS/admin/room-detail.css">
    <link rel="stylesheet" href="../../CSS/Common/lightbox.min.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- title -->
    <title> Room Detail :
        <?php echo $room->roomId; ?>
    </title>

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

        <div class="content-article flex-column">
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
            <div class="content-container flex-row house-photo-container">
                <div class="left flex-column">
                    <img src="../../Assests/Uploads/Room/<?php echo $room->roomPhotoArray[0]['room_photo']; ?>" alt="">
                </div>

                <div class="right">
                    <?php
                    foreach ($room->roomPhotoArray as $roomPhoto) {
                        ?>
                        <div class="photo-div">
                            <a href="../../Assests/Uploads/Room/<?php echo $roomPhoto['room_photo']; ?>"
                                data-lightbox="room-photo">
                                <img src="../../Assests/Uploads/Room/<?php echo $roomPhoto['room_photo']; ?>" id="photo-4"
                                    alt="">
                            </a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <!-- detail -->
            <div class="content-container flex-row house-detail-container">
                <div class="flex-column house-detail-left-div">
                    <!-- requirement -->
                    <div class="requirement-container flex-column">
                        <p class="p-large f-bold"> Room Requirements </p>
                        <div class="requirement-div">
                            <p class="p-normal">
                                <?php echo ucfirst($room->requirement); ?>
                            </p>
                        </div>
                    </div>

                    <!-- house requirement -->
                    <div class="requirement-container flex-column">
                        <p class="p-large f-bold"> House Requirements </p>
                        <div class="requirement-div">
                            <p class="p-normal">
                                <?php echo ucfirst($room->getGeneralRequirement($room->houseId)); ?>
                            </p>
                        </div>
                    </div>

                    <!-- amenities container -->
                    <div class="amenities-container content-container flex-column">
                        <!-- heading -->
                        <p class="p-large f-bold"> Amenities </p>
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
                            <td class="detail-data">
                                <?php echo '-'; ?>
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
                        <?php $link = "operation/room-op.php?roomId=$roomId&task=verify&url=$url"; ?>
                        <button class="positive-button <?php if($room->roomState == 1) echo "hidden"; ?>" onclick="window.location.href='<?php echo $link; ?>'"> Verify Room </button>

                        <?php $link = "operation/room-op.php?roomId=$roomId&task=suspend&url=$url"; ?>
                        <button class="negative-button <?php if($room->roomState == 0) echo "hidden"; ?>" onclick="window.location.href='<?php echo $link; ?>'"> Suspend Room </button>
                        
                        <?php $link = ""; ?>
                        <button class="inverse-button <?php if($room->isAcquired == 0) echo "hidden"; ?>" onclick="window.location.href='<?php echo $link; ?>'">
                            Show Tenant Detail </button>
                    </div>
                </div>
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
                                            if (is_float($rating)) {
                                                $rating = intval($rating);
                                                for ($i = 0; $i < $rating; $i++) {
                                                    ?>
                                                    <img src="../../Assests/Icons/full-rating.png" class="icon class" alt="">
                                                    <?php
                                                }
                                                ?>
                                                <img src="../../Assests/Icons/half-rating.png" alt="">
                                                <?php
                                            } else {
                                                for ($i = 0; $i < $rating; $i++) {
                                                    ?>
                                                    <img src="../../Assests/Icons/full-rating.png" class="icon class" alt="">
                                                    <?php
                                                }
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
                    <div class="flex-row empty-room-review">
                        <p class="p-normal negative"> No Reviews has been made for this room. </p>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <!-- script -->
    <script>
        const activeMenu = $('#room-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
        });
    </script>
</body>

</html>