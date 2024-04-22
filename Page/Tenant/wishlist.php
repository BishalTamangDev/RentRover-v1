<?php
// staring session
if (!session_start()) {
    session_start();
}

// including files
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/feedback_class.php';
include '../../Class/notification_class.php';
include '../../class/functions.php';
include_once '../../class/wishlist_class.php';

// creating the object
$user = new User();
$roomObj = new Room();
$houseObj = new House();
$notification = new Notification();

// setting the values
$user->userId = $_SESSION['tenantUserId'];

if (!isset($_SESSION['tenantUserId'])) {
    // divert to the login page
    header("Location: ../../index.php");
} else {
    $user->fetchSpecificRow($_SESSION['tenantUserId']);
}

// getting notification count
$notificationCount = $notification->countNotification("tenant", $user->userId, "unseen");

// widhlist
$wishlist = new Wishlist();
$wishlistCount = $wishlist->countWishes($_SESSION['tenantUserId']);

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
    <link rel="stylesheet" href="../../CSS/tenant/footer.css">
    <link rel="stylesheet" href="../../CSS/tenant/tenant.css">
    <link rel="stylesheet" href="../../CSS/tenant/home.css">

    <!-- title -->
    <title> Wishlist </title>

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">
</head>

<body>
    <!-- navigation section -->
    <?php include 'navbar.php'; ?>

    <!-- heading -->
    <div class="top-heading-container container">
        <div class="div">
            <p class="p-large f-bold"> My Wishlist </p>
        </div>
    </div>

    <!-- user wishlist -->
    <!-- room container -->
    <div class="room-main-container content-container container flex-column">
        <div class="room-container div flex-row">
            <?php
            $wishlistIdArray = [];
            $wishlistIdArray = $wishlist->fetchWishlistRoomId($user->userId);

            if (sizeof($wishlistIdArray) > 0) {
                $sets = $roomObj->fetchRoomsForTenant();

                foreach ($sets as $set) {
                    if (in_array($set['room_id'], $wishlistIdArray)) {
                        // processing data
                        $photo = $roomObj->getRoomPhoto($set['room_id']);

                        $amenities = unserialize(base64_decode($set['amenities']));
                        ?>
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

                                    <div class="wishlist-div">
                                        <?php
                                        if ($wishlist->isWish($_SESSION['tenantUserId'], $set['room_id'])) {
                                            ?>
                                            <a
                                                href="operation/wishlist-op.php?userId=<?php echo $_SESSION['tenantUserId']; ?>&task=remove&roomId=<?php echo $set['room_id']; ?>&url=<?php echo $url; ?>">
                                                <img src="../../Assests/Icons/saved.png" alt="">
                                            </a>
                                            <?php
                                        } else {
                                            ?>
                                            <a
                                                href="operation/wishlist-op.php?userId=<?php echo $_SESSION['tenantUserId']; ?>&task=add&roomId=<?php echo $set['room_id']; ?>&url=<?php echo $url; ?>">
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
                                        <?php echo $houseObj->getLocation($set['house_id']); ?>
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
                                <div class="amenity-div">
                                    <p class="p-normal n-normal f-form">
                                        <?php foreach ($amenities as $amenity)
                                            echo returnArrayValue('amenity', $amenity) . ', '; ?>
                                    </p>
                                </div>

                                <!-- price div -->
                                <div class="price-div">
                                    <p class="p-normal f-bold">
                                        <?php echo returnFormattedPrice($set['rent_amount']); ?>/ <span class="n-normal f-normal">
                                            Month</span>
                                    </p>
                                </div>

                                <!-- price & show more section -->
                                <div class="price-show-more-div flex-row">
                                    <!-- rating div -->
                                    <div class="rating-div flex-row">
                                        <img class="icon-class" src="../../Assests/Icons/star.png" alt="">
                                        <p class="p-form f-bold"> 4.5 (123) </p>
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
                }
            }
            ?>
        </div>
    </div>

    <!-- empty context -->
    <div class="container empty-data-container <?php if (sizeof($wishlistIdArray) > 0)
        echo "hidden"; ?>">
        <div class="flex-column div empty-data-div" id="empty-data-div">
            <img src="../../Assests/Icons/empty.png" alt="">
            <p class="p-normal negative"> Your saved list is empty! </p>
        </div>
    </div>

    <!-- footer -->
    <?php include 'footer.php'; ?>

    <!-- js section -->
    <script>
        var userMenuState = false;
        var notificationMenuState = false;

        const userMenu = document.getElementById('menu-container');
        const notificationMenu = document.getElementById('notification-container');
        const logoutDialog = document.getElementById('logout-dialog-container');

        onload = () => {
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
</body>

</html>