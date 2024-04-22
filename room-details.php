<?php
// staring session
if (!session_start())
    session_start();

// including files
include 'Class/user_class.php';
include 'Class/house_class.php';
include 'Class/notification_class.php';
include 'Class/room_review_class.php';
include 'class/functions.php';
include_once 'class/wishlist_class.php';

// creating the object
$user = new User();

$room = new Room();
$roomObj = new Room();
$house = new House();
$houseObj = new House();
$roomReview = new RoomReview();

// widhlist
$wishlist = new Wishlist();
$wishlistCount = $wishlist->countWishes(0);

// setting up room details
$room->fetchRoom($_GET['roomId']);
$room->setKeyValue("id", $_GET['roomId']);
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
    <link rel="stylesheet" href="CSS/common/style.css">
    <!-- <link rel="stylesheet" href="CSS/admin/admin.css"> -->
    <link rel="stylesheet" href="CSS/tenant/navbar.css">
    <link rel="stylesheet" href="CSS/tenant/room.css">
    <link rel="stylesheet" href="CSS/tenant/room-detail.css">
    <link rel="stylesheet" href="CSS/tenant/footer.css">
    <link rel="stylesheet" href="CSS/Common/lightbox.min.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="Assests/Images/RentRover-Logo.png">

    <style>
        body {
            padding-bottom: 100px;
        }

        .room-detail-container{
            margin-top: 90px;
        }
    </style>

    <!-- title -->
    <title> Room Detail </title>

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <script src="Js/lightbox-plus-jquery.min.js"></script>
</head>

<body>
    <!-- navbar -->
    <?php include 'navbar.php';?>

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
                                        <img src="Assests/Icons/full-rating.png" class="icon class" alt="">
                                        <?php
                                    }
                                    ?>
                                    <img src="Assests/Icons/half-rating.png" class="icon class" alt="">
                                    <?php
                                } else {
                                    for ($i = 0; $i < $roomReview->cumulativeRating; $i++) {
                                        ?>
                                        <img src="Assests/Icons/full-rating.png" class="icon class" alt="">
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
                    if ($wishlist->isWish(0, $room->roomId)) {
                        ?>
                        <a
                            href="Operation/wishlist-op.php?userId=<?php echo 0; ?>&task=remove&roomId=<?php echo $room->roomId; ?>&url=<?php echo $url; ?>">
                            <img src="Assests/Icons/saved.png" alt="">
                        </a>
                        <?php
                    } else {
                        ?>
                        <a
                            href="Operation/wishlist-op.php?userId=<?php echo 0; ?>&task=add&roomId=<?php echo $room->roomId; ?>&url=<?php echo $url; ?>">
                            <img src="Assests/Icons/unsaved.png" alt="">
                        </a>
                        <?php
                    }
                    ?>

                    <button> Apply Now </button>
                </div>
            </div>

            <!-- house image section -->
            <div class="room-photo-container content-container flex-row">
                <div class="left flex-column">
                    <img src="Assests/Uploads/Room/<?php echo $room->roomPhotoArray[0]['room_photo']; ?>" alt="">
                </div>

                <div class="right">
                    <div class="photo-div">
                        <a href="Assests/Uploads/Room/<?php echo $room->roomPhotoArray[0]['room_photo']; ?>"
                            data-lightbox="room-photo">
                            <img src="Assests/Uploads/Room/<?php echo $room->roomPhotoArray[0]['room_photo']; ?>"
                                id="photo-4" alt="">
                        </a>
                    </div>

                    <div class="photo-div">
                        <a href="Assests/Uploads/Room/<?php echo $room->roomPhotoArray[1]['room_photo']; ?>"
                            data-lightbox="room-photo">
                            <img src="Assests/Uploads/Room/<?php echo $room->roomPhotoArray[1]['room_photo']; ?>"
                                id="photo-4" alt="">
                        </a>
                    </div>

                    <div class="photo-div">
                        <a href="Assests/Uploads/Room/<?php echo $room->roomPhotoArray[2]['room_photo']; ?>"
                            data-lightbox="room-photo">
                            <img src="Assests/Uploads/Room/<?php echo $room->roomPhotoArray[2]['room_photo']; ?>"
                                id="photo-4" alt="">
                        </a>
                    </div>

                    <div class="photo-div">
                        <a href="Assests/Uploads/Room/<?php echo $room->roomPhotoArray[3]['room_photo']; ?>"
                            data-lightbox="room-photo">
                            <img src="Assests/Uploads/Room/<?php echo $room->roomPhotoArray[3]['room_photo']; ?>"
                                id="photo-4" alt="">
                        </a>
                    </div>
                </div>
            </div>

            <!-- detail -->
            <div class="room-detail-container content-container flex-row">
                <div class="room-detail-left-div flex-column">
                    <!-- requirement -->
                    <div class="requirement-container flex-column">
                        <p class="p-large f-bold"> Room Requirements </p>
                        <div class="requirement-div">
                            <p class="p-normal">
                                <?php echo (ucfirst($room->requirement) == ' ') ? ucfirst($room->requirement) : "No requirment"; ?>
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
                                    <img src="Assests/Icons/amenities/<?php echo $amenityIconName; ?>" alt="Amenity icon">
                                    <p class="p-normal">
                                        <?php echo returnArrayValue("amenity", $amenity); ?>
                                    </p>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    <!-- reviews -->
                    <div class="section-heading-container content-container">
                        <p class="heading f-bold"> Reviews and Ratings </p>
                    </div>

                    <div class="review-container">
                        <div class="review-div flex-column">
                            <!-- review 1 -->
                            <div class="review flex-row">
                                <div class="left flex-column">
                                    <img src="Assests/Uploads/user/<?php echo "blank.jpg"; ?>" alt="">
                                </div>

                                <div class="right flex-column">
                                    <div class="flex-row right-top">
                                        <div class="flex-column tenant-detail">
                                            <p class="p-form"> Tenant Name </p>
                                            <p class="p-normal"> Room ID </p>
                                        </div>
                                    </div>

                                    <div class="flex-column right-bottom">
                                        <p class="p-normal">
                                            "Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestias totam
                                            numquam facilis exercitationem sunt culpa labore laudantium, eum beatae
                                            quos.
                                            Eum, ab beatae impedit officia est asperiores in quisquam. Modi corrupti,
                                            sed ab
                                            dignissimos optio eum eaque minima dicta cum corporis inventore saepe quam
                                            veniam, fugit officiis ducimus deleniti tempore."
                                        </p>

                                        <div class="flex-row rating-div">
                                            <img src="Assests/Icons/star.png" alt="">
                                            <img src="Assests/Icons/star.png" alt="">
                                            <img src="Assests/Icons/star.png" alt="">
                                            <img src="Assests/Icons/star.png" alt="">
                                            <img src="Assests/Icons/star.png" alt="">

                                            <p class="p-normal n-light"> (4.5) </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                            <img src="Assests/Icons/<?php echo $icon; ?>" class="icon-class" alt="">
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
                            <td class="detail-title"> Room Specs </td>
                            <td class="detail-data">
                                <?php echo ($room->roomType == 1) ? $room->bhk . " BHK, " : "Non BHK, "; ?>
                                <?php
                                if ($room->furnishing == 0)
                                    echo "Non-Furnished";
                                elseif ($room->furnishing == 1)
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
                            <p class="p-normal"> House Photos </p>
                        </div>

                        <div class="top-photo-container">
                            <img src="Assests/Uploads/House/<?php echo $house->housePhotoArray[0]['house_photo']; ?>"
                                alt="">
                        </div>

                        <div class="bottom-photo-container">
                            <img src="Assests/Uploads/House/<?php echo $house->housePhotoArray[1]['house_photo']; ?>"
                                alt="">
                            <img src="Assests/Uploads/House/<?php echo $house->housePhotoArray[2]['house_photo']; ?>"
                                alt="">
                            <img src="Assests/Uploads/House/<?php echo $house->housePhotoArray[3]['house_photo']; ?>"
                                alt="">
                        </div>
                    </div>
                </div>
            </div>
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
                    <div class="room flex-row pointer">
                        <div class="room-photo-div flex-column">
                            <img src="Assests/Uploads/Room/<?php echo $photo; ?>" alt="">
                        </div>

                        <!-- spec -->
                        <div class="room-spec-div flex-column">

                            <div class="verified-wishlist-div flex-row">
                                <div class="verified-div flex-row">
                                    <img class="icon-class" src="Assests/Icons/Verified.png" alt="">
                                    <p class="positive p-form f-bold"> Verified </p>
                                </div>

                                <div class="wishlist-div">
                                    <?php
                                    if ($wishlist->isWish(0, $set['room_id'])) {
                                        ?>
                                        <a
                                            href="page/operation/wishlist-operation.php?userId=<?php echo 0; ?>&task=remove&roomId=<?php echo $set['room_id']; ?>&url=<?php echo $_SERVER['REQUEST_URI']; ?>">
                                            <img src="Assests/Icons/saved.png" alt="">
                                        </a>
                                        <?php
                                    } else {
                                        ?>
                                        <a
                                            href="page/operation/wishlist-operation.php?userId=<?php echo 0; ?>&task=add&roomId=<?php echo $set['room_id']; ?>&url=<?php echo $_SERVER['REQUEST_URI']; ?>">
                                            <img src="Assests/Icons/unsaved.png" alt="">
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- location -->
                            <div class="location-div">
                                <p class="p-normal f-bold">
                                    <?php
                                    // echo "Location name";
                                    echo $houseObj->getLocation($set['house_id']);
                                    ?>
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
                                <p class="p-normal f-bold">
                                    <?php echo returnFormattedPrice($set['rent_amount']); ?>/ <span class="n-normal f-normal">
                                        Month </span>
                                </p>
                            </div>

                            <!-- rating & show more section -->
                            <div class="price-show-more-div flex-row">
                                <!-- rating -->
                                <div class="rating-div flex-row">
                                    <?php $roomReview->setFinalRating($set['room_id']); ?>
                                    <img class="icon-class" src="Assests/Icons/full-rating.png" alt="">
                                    <p class="p-form f-bold"> <?php echo $roomReview->cumulativeRating; ?> </p>
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
    <?php 
    include 'footer.php';
    ?>

    <!-- js section -->
    <script>
        var userMenuState = false;
        var notificationMenuState = false;

        const userMenu = document.getElementById('menu-container');
        const logoutDialog = document.getElementById('logout-dialog-container');
        const notificationMenu = document.getElementById('notification-container');

        onload = () => {
            logoutDialog.style = "display:none";
            userMenu.style = "display:none";
            notificationMenu.style = "display:none";
        }

        onUnload = () => {
            userMenuState = false;
            notificationMenuState = false;
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
</body>

</html>