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
$house = new House();
$room = new Room();
$roomReview = new RoomReview();

if (isset($_GET['houseId'])) {
    $houseId = $_GET['houseId'];

    // url tampering check
    if (!$house->isValidHouse($houseId, 0))
        header("location: houses.php");

    $house->fetchHouse($houseId);
    $house->houseId = $houseId;
} else
    header("Location: houses.php");

$url = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/tenant/room.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/house-detail.css">
    <link rel="stylesheet" href="../../CSS/Common/lightbox.min.css">

    <!-- title -->
    <title> House Detail :
        <?php echo ucfirst($house->getHouseIdentity($house->houseId)); ?>
    </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- script section -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <!-- lightbox -->
    <script src="../../Js/lightbox-plus-jquery.min.js"></script>
</head>

<body>
    <?php
    include 'aside.php';
    ?>

    <div class="body-container flex-row">
        <div class="empty-section"> </div>

        <article class="content-article flex-column">
            <!-- house name and rating section -->
            <div class="house-name-rating-container flex-column">
                <div class="name-container">
                    <p class="p-large f-bold">
                        <?php echo ucfirst($house->getHouseIdentity($house->houseId)); ?>
                    </p>
                </div>
            </div>

            <!-- house image section -->
            <div class="house-photo-container content-container flex-row">
                <div class="left flex-column">
                    <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[0]['house_photo']; ?>"
                        alt="">
                </div>

                <div class="right">
                    <div class="photo-div">
                        <a href="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[0]['house_photo']; ?>"
                            data-lightbox="house-photo">
                            <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[0]['house_photo']; ?>"
                                id="photo-1" alt="">
                        </a>
                    </div>

                    <div class="photo-div">
                        <a href="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[1]['house_photo']; ?>"
                            data-lightbox="house-photo">
                            <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[1]['house_photo']; ?>"
                                id="photo-2" alt="">
                        </a>
                    </div>

                    <div class="photo-div">
                        <a href="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[2]['house_photo']; ?>"
                            data-lightbox="house-photo">
                            <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[2]['house_photo']; ?>"
                                id="photo-3" alt="">
                        </a>
                    </div>

                    <div class="photo-div">
                        <a href="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[3]['house_photo']; ?>"
                            data-lightbox="house-photo">
                            <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[3]['house_photo']; ?>"
                                id="photo-4" alt="">
                        </a>
                    </div>
                </div>
            </div>

            <!-- detail -->
            <div class="house-detail-container content-container flex-row">
                <div class="house-detail-left-div flex-column">
                    <!-- requirement -->
                    <div class="requirement-container flex-column">
                        <p class="p-large f-bold"> Requirements </p>
                        <div class="requirement-div">
                            <p class="p-normal">
                                <?php echo ucfirst($house->generalRequirement); ?>
                            </p>
                        </div>
                    </div>

                    <!-- amenities container -->
                    <div class="amenities-container content-container flex-column">
                        <!-- heading -->
                        <p class="p-large f-bold"> Amenities </p>
                        <div class="amenities-div">
                            <?php
                            $amenities = unserialize(base64_decode($house->allAmenities));
                            foreach ($amenities as $amenity) {
                                // echo returnServiceName($service).', ';
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
                    <!-- top section -->
                    <div class="verified-date-div flex-row">
                        <div class="verified-div flex-row">
                            <?php
                            if ($house->houseState == 0)
                                $icon = "report.png";
                            else
                                $icon = "verified.png";
                            ?>
                            <img src="../../Assests/Icons/<?php echo $icon; ?>" class="icon-class" alt="">
                            <p class="p-form">
                                <?php
                                if ($house->houseState == 0)
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
                                $registerDate = new DateTime($house->registerDate);
                                echo $registerDate->format('Y-m-d');
                                ?>
                            </p>
                        </div>
                    </div>


                    <!-- table section -->
                    <table class="house-detail-table">
                        <tr>
                            <td class="detail-title"> House ID </td>
                            <td class="detail-data">
                                <?php echo $house->houseId; ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> Location </td>
                            <td class="detail-data">
                                <?php echo ucfirst($house->getLocation($house->houseId)); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> Total Rooms </td>
                            <td class="detail-data">
                                <?php echo $room->countRoomOfThisHouse($house->houseId); ?>
                            </td>
                        </tr>
                    </table>

                    <div class="flex-column operation-container">
                        <?php $houseId = $_GET['houseId']; ?>

                        <!-- verify house button -->
                        <?php $link = "operation/house-op.php?houseId=$houseId&task=verify&url=$url";?>
                        <button class="positive-button <?php if ($house->houseState == 1) echo "hidden"; ?>" id="verify-house" onclick="window.location.href='<?php echo $link; ?>'">
                            Verify House
                        </button>
                        
                        <!-- suspend house button -->
                        <?php $link = "operation/house-op.php?houseId=$houseId&task=suspend&url=$url";?>
                        <button class="negative-button <?php if ($house->houseState == 0) echo "hidden"; ?>" id="remove-house" onclick="window.location.href='<?php echo $link; ?>'"> Suspend House
                        </button>
                    </div>
                </div>
            </div>

            <!-- rooms in this house -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold negative"> Rooms in this house </p>
            </div>

            <div class="room-container content-container">
                <?php
                $sets = $room->fetchRoomsOfThisHouse($house->houseId, true, '0');
                if (sizeof($sets) > 0) {
                    foreach ($sets as $set) {
                        $id = $set['room_id'];
                        $photo = $room->getRoomPhoto($set['room_id']);
                        $amenities = unserialize(base64_decode($set['amenities']));
                        $rentAmount = returnFormattedPrice($set['rent_amount']);
                        $location = ucfirst($house->getLocation($set['house_id']));

                        if ($set['floor'] % 10 == 1)
                            $floor = $set['floor'] . 'st Floor';
                        elseif ($set['floor'] == 2)
                            $floor = $set['floor'] . 'nd Floor';
                        elseif ($set['floor'] % 10 == 3)
                            $floor = $set['floor'] . 'rd Floor';
                        else
                            $floor = $set['floor'] . 'th Floor';

                        if ($set['room_type'] == 1)
                            $numberOfRoom = $set['bhk'] . ' BHK, ';
                        else
                            $numberOfRoom = ($set['number_of_room'] > 1) ? $set['number_of_room'] . ' Rooms, ' : $set['number_of_room'] . ' Room, ';
                        ?>

                        <div class="room-div flex-row">
                            <div class="room flex-row">
                                <div class="room-photo-div flex-column">
                                    <img src="../../Assests/Uploads/Room/<?php echo $photo; ?>" alt="" class="room-image">
                                </div>

                                <!-- spec -->
                                <div class="room-spec-div flex-column">
                                    <div class="verified-wishlist-div flex-row">
                                        <div class="verified-div flex-row">
                                            <img class="icon-class" src="../../Assests/Icons/Verified.png" alt="">
                                            <p class="positive p-form f-bold"> Verified </p>
                                        </div>
                                    </div>

                                    <!-- location -->
                                    <div class="location-div">
                                        <p class="p-normal f-bold">
                                            <?php echo $location; ?>
                                        </p>
                                    </div>

                                    <!-- bhk & floor -->
                                    <div class="bhk-floor-div">
                                        <p class="p-normal n-normal">
                                            <?php echo $numberOfRoom, $floor; ?>
                                        </p>
                                    </div>

                                    <!-- amenities -->
                                    <div class="amenity-div">
                                        <p class="p-normal n-normal f-form">
                                            <?php foreach ($amenities as $amenity)
                                                echo returnArrayValue('amenity', $amenity) . ', '; ?>
                                        </p>
                                    </div>

                                    <!-- price -->
                                    <div class="price-div">
                                        <p class="p-normal f-bold">
                                            <?php echo $rentAmount; ?>/ <span class="n-normal f-normal"> Month</span>
                                        </p>
                                    </div>

                                    <!-- price & show more section -->
                                    <div class="price-show-more-div flex-row">
                                        <!-- rating div -->
                                        <?php $roomReview->setFinalRating($set['room_id']); ?>
                                        <div class="rating-div flex-row">
                                            <img class="icon-class" src="../../Assests/Icons/full-rating.png" alt="">
                                            <p class="p-form f-bold">
                                                <?php echo $roomReview->cumulativeRating; ?>
                                            </p>
                                        </div>

                                        <div class="show-more-div flex-row">
                                            <a href="room-detail.php?roomId=<?php echo $set['room_id']; ?>">
                                                <button class="normal-button"> Show More </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p class="p-normal negative"> No Room Has Been Added In This House! </p>';
                }
                ?>
            </div>
        </article>
    </div>

    <!-- script -->
    <script>
        const activeMenu = $('#house-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
        });
    </script>
</body>

</html>