<?php
// session
if (!session_start())
    session_start();

// redirecting to login page is session variable is not set
if (!isset($_SESSION['landlordUserId']))
    header("Location: landlord-login.php");

// including external files
include '../../class/user_class.php';
include '../../class/house_class.php';
include '../../class/room_review_class.php';
include '../../class/functions.php';

// creating objects
$user = new User();
$house = new House();
$houseObj = new House();
$roomObj = new Room();
$roomReview = new RoomReview();

$user->userId = $_SESSION['landlordUserId'];

if (isset($_GET['houseId'])) {
    $houseId = $_GET['houseId'];

    // url tampering check
    if (!$houseObj->isValidHouse($houseId, $_SESSION['landlordUserId']))
        header("location: myhouse.php");

    $houseObj->fetchHouse($houseId);
    $houseObj->houseId = $houseId;
    $user->fetchSpecificRow($_SESSION['landlordUserId']);
} else
    header("Location: landlord-houses.php");

$houseId = $_GET['houseId'];

$url = $_SERVER['REQUEST_URI'];
?>

<?php
$i = 0;

$housePhoto1 = $houseObj->housePhoto1;
$housePhoto2 = $houseObj->housePhoto2;
$housePhoto3 = $houseObj->housePhoto3;
$housePhoto4 = $houseObj->housePhoto4;
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
    <link rel="stylesheet" href="../../CSS/landlord/myhouse-detail.css">
    <link rel="stylesheet" href="../../CSS/Common/lightbox.min.css">

    <!-- title -->
    <title> House Detail :
        <?php echo ucfirst($houseObj->getHouseIdentity($houseObj->houseId)); ?>
    </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- script section -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <script src="../../Js/lightbox-plus-jquery.min.js"></script>

</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="body-container flex-row">
        <div class="empty-section"></div>

        <article class="content-article flex-column">
            <!-- house name and rating section -->
            <div class="house-name-rating-container content-container flex-column">
                <div class="name-container">
                    <p class="p-large f-bold">
                        <?php echo ucfirst($houseObj->getHouseIdentity($houseObj->houseId)); ?>
                    </p>
                </div>
            </div>

            <!-- house image section -->
            <div class="house-photo-container content-container flex-row">
                <div class="left flex-column">
                    <img src="../../Assests/Uploads/House/<?php echo $houseObj->housePhotoArray[0]['house_photo']; ?>"
                        alt="">
                </div>

                <div class="right">
                    <?php
                    foreach ($houseObj->housePhotoArray as $housePhoto) {
                        ?>
                        <div class="photo-div">
                            <a href="../../Assests/Uploads/House/<?php echo $housePhoto['house_photo']; ?>"
                                data-lightbox="house-photo">
                                <img src="../../Assests/Uploads/House/<?php  echo ($housePhoto['house_photo'] == NULL)?"blank.jpg" :$housePhoto['house_photo']; ?>" alt="">
                            </a>
                        </div>
                        <?php
                    }
                    ?>
                </div>

            </div>

            <!-- detail -->
            <div class="content-container flex-row house-detail-container">
                <div class="house-detail-left-div flex-column">
                    <!-- requirement -->
                    <div class="requirement-container flex-column">
                        <p class="p-large f-bold"> Requirements </p>
                        <div class="requirement-div">
                            <p class="p-normal">
                                <?php echo ucfirst($houseObj->generalRequirement); ?>
                            </p>
                        </div>
                    </div>

                    <!-- amenities container -->
                    <div class="amenities-container content-container flex-column">
                        <!-- heading -->
                        <p class="p-large f-bold"> Amenities </p>
                        <div class="amenities-div">
                            <?php
                            $amenities = unserialize(base64_decode($houseObj->allAmenities));
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
                <div class="flex-column house-detail-right-div">
                    <!-- top section -->
                    <div class="verified-date-div flex-row">
                        <div class="verified-div flex-row">
                            <?php
                            if ($houseObj->houseState == 0)
                                $icon = "report.png";
                            else
                                $icon = "verified.png";
                            ?>
                            <img src="../../Assests/Icons/<?php echo $icon; ?>" class="icon-class" alt="">
                            <p class="p-form">
                                <?php
                                if ($houseObj->houseState == 0)
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
                                <?php echo $houseObj->houseId; ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> Location </td>
                            <td class="detail-data">
                                <?php echo ucfirst($houseObj->getLocation($houseObj->houseId)); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> Total Rooms </td>
                            <td class="detail-data">
                                <?php echo $roomObj->countRoomOfThisHouse($houseObj->houseId); ?>
                            </td>
                        </tr>
                    </table>

                    <div class="flex-column operation-container">
                        <!-- edit house detail -->
                        <?php
                        $houseId = $_GET['houseId'];
                        $link = "edit-house.php?houseId=$houseId";
                        ?>

                        <div class="flex-row operation-div" onclick="window.location.href='<?php echo $link; ?>'">
                            <img src="../../Assests/Icons/edit.svg" alt="">
                            <p class="p normal"> Edit House Detail </p>
                        </div>

                        <!-- add room -->
                        <?php $link = "add-room.php?houseId=$houseId"; ?>
                        <div class="flex-row operation-div" onclick="window.location.href='<?php echo $link; ?>'">
                            <img src="../../Assests/Icons/add-black-filled.png" alt="" class="icon-class">
                            <p class="p normal"> Add Room </p>
                        </div>

                        <!-- remove house -->
                        <?php
                        $houseId = $_GET['houseId'];
                        $link = "operation/house-op.php?task=remove&houseId=$houseId&url=$url";
                        ?>

                        <button class="negative-button" id="remove-house"
                            onclick="window.location.href='<?php echo $link; ?>'"> Remove House </button>
                    </div>
                </div>
            </div>

            <!-- rooms in this house -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold negative"> Rooms in this house </p>
            </div>

            <div class="room-container content-container">
                <?php
                $sets = $roomObj->fetchRoomsOfThisHouse($houseObj->houseId, true, '0');
                if (sizeof($sets) > 0) {
                    foreach ($sets as $set) {
                        $id = $set['room_id'];
                        $rating = '(-, -)';
                        $photo = $roomObj->getRoomPhoto($set['room_id']);
                        $rentAmount = returnFormattedPrice($set['rent_amount']);
                        $location = ucfirst($houseObj->getLocation($set['house_id']));

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
                            <div class="room flex-row pointer">
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

                                    <div class="price-div">
                                        <p class="p-normal f-bold">
                                            <?php echo $rentAmount; ?>/ <span class="n-normal f-normal"> Month</span>
                                        </p>
                                    </div>

                                    <!-- rating & show more section -->
                                    <div class="price-show-more-div flex-row">
                                        <!-- rating div -->
                                        <div class="rating-div flex-row">
                                            <?php $roomReview->setFinalRating($set['room_id']); ?>
                                            <img class="icon-class" src="../../Assests/Icons/full-rating.png" alt="">
                                            <p class="p-form f-bold">
                                                <?php echo $roomReview->cumulativeRating; ?>
                                            </p>
                                        </div>

                                        <div class="show-more-div flex-row">
                                            <a href="myroom-detail.php?photo=1&roomId=<?php echo $set['room_id']; ?>">
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

    <!-- js section -->
    <script>
        const activeMenu = $('#house-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
        });
    </script>
</body>

</html>