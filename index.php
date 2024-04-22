<?php
// starting the session
if (!session_start())
    session_start();

// redirecting to the home page
if (isset($_SESSION['adminId']) || isset($_SESSION['landlordUserId']) || isset($_SESSION['tenantUserId'])) {
    if (isset($_SESSION['tenantUserId']))
        header("location: page/tenant/home.php");
    elseif (isset($_SESSION['landlordUserId']))
        header("location: page/landlord/dashboard.php");
    else
        header("location: page/admin/dashboard.php");
}

// including files
include 'Class/user_class.php';
include 'Class/house_class.php';
include 'Class/feedback_class.php';
include 'Class/room_review_class.php';
include 'class/functions.php';
include 'class/wishlist_class.php';

// creating the object
$userObj = new User();
$roomObj = new Room();
$roomReview = new RoomReview();
$houseObj = new House();
$wishlist = new Wishlist();
$feedback = new Feedback();

$wishlistCount = $wishlist->countWishes(0);

$url = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="CSS/common/style.css">
    <link rel="stylesheet" href="CSS/navbar.css">
    <link rel="stylesheet" href="CSS/common/services.css">
    <link rel="stylesheet" href="CSS/admin/admin.css">
    <link rel="stylesheet" href="CSS/tenant/footer.css">
    <link rel="stylesheet" href="CSS/tenant/home.css">
    <link rel="stylesheet" href="CSS/tenant/tenant.css">
    <link rel="stylesheet" href="CSS/tenant/room.css">
    <link rel="stylesheet" href="CSS/tenant/index.css">

    <style>
        body {
            overflow-x: hidden;
        }
    </style>

    <!-- title -->
    <title> RentRover </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="Assests/Images/RentRover-Logo.png">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <!-- landing content -->
    <div class="landing-container container flex-column">
        <div class="landing-div div flex-column">

            <div class="content-div flex-column">
                <p class="p-large">
                    Unlock the Door to Seamless Room Renting and Finding: Your Space, Digitized for Easy, Hassle-Free Accommodation Solutions.
                </p>

                <div class="flex-row bottom">
                    <button class="negative-button" id="register-button"
                        onclick="window.location.href='registration.php'"> Register Now </button>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-column unsigned-user-saved-container" id="unsigned-user-saved-container">
        <div class="flex-row top-section">
            <p class="p-large f-bold"> Your Saved Rooms </p>
        </div>

        <?php
        $savedIdSet = [];
        $savedIdSet = $wishlist->fetchWishlistRoomId(0);
        ?>

        <?php
        if (sizeof($savedIdSet) > 0) {
            ?>
            <div class="flex-column saved-room-container">
                <?php
                foreach ($savedIdSet as $savedId) {
                    $roomObj->fetchRoom($savedId);
                    $roomObj->roomId = $savedId;

                    $amenities = unserialize(base64_decode($roomObj->amenities));
                    ?>
                    <div class="room flex-row">
                        <div class="room-photo-div flex-column">
                            <img src="Assests/Uploads/Room/<?php echo $roomObj->roomPhotoArray[0]['room_photo']; ?>" alt=""
                                class="room-image">
                        </div>

                        <!-- spec -->
                        <div class="room-spec-div flex-column">

                            <div class="verified-wishlist-div flex-row">
                                <div class="verified-div flex-row">
                                    <img class="icon-class" src="Assests/Icons/Verified.png" alt="">
                                    <p class="positive p-form f-bold"> Verified </p>
                                </div>

                                <div class="wishlist-div">
                                    <a
                                        href="Operation/wishlist-op.php?userId=<?php echo 0; ?>&task=remove&roomId=<?php echo $roomObj->roomId; ?>&url=<?php echo $url; ?>">
                                        <img src="Assests/Icons/saved.png" alt="">
                                    </a>
                                </div>
                            </div>

                            <!-- location -->
                            <div class="location-div">
                                <p class="p-normal f-bold">
                                    <?php echo $houseObj->getLocation($roomObj->houseId); ?>
                                </p>
                            </div>

                            <!-- bhk & floor -->
                            <div class="bhk-floor-div">
                                <p class="p-normal n-normal">
                                    <?php
                                    if ($roomObj->roomType == 1)
                                        echo $roomObj->bhk . ' BHK, ';
                                    else {
                                        echo $roomObj->numberOfRoom;

                                        if ($roomObj->numberOfRoom > 1)
                                            echo " Rooms, ";
                                        else
                                            echo " Room, ";
                                    }
                                    ?>

                                    <?php
                                    if ($roomObj->floor == 1)
                                        echo $roomObj->floor . 'st Floor';
                                    elseif ($roomObj->floor == 2)
                                        echo $roomObj->floor . 'nd Floor';
                                    elseif ($roomObj->floor % 10 == 3)
                                        echo $roomObj->floor . 'rd Floor';
                                    else {
                                        echo $roomObj->floor . 'th Floor';
                                    }
                                    ?>
                                </p>
                            </div>

                            <!-- amenities -->
                            <abbr title="<?php foreach ($amenities as $amenity)
                                echo returnArrayValue('amenity', $amenity) . ', '; ?>">

                                <div class="amenity-div">
                                    <p class="p-form n-normal f-form">
                                        <?php foreach ($amenities as $amenity)
                                            echo returnArrayValue('amenity', $amenity) . ', '; ?>
                                    </p>
                                </div>

                            </abbr>

                            <!-- price & show more section -->
                            <div class="price-div">
                                <p class="p-normal f-bold">
                                    <?php echo returnFormattedPrice($roomObj->rentAmount); ?>/ <span class="n-normal f-normal">
                                        Month</span>
                                </p>
                            </div>

                            <div class="price-show-more-div flex-row">
                                <!-- rating div -->
                                <?php $roomReview->setFinalRating($roomObj->roomId); ?>
                                <div class="rating-div flex-row">
                                    <img class="icon-class" src="Assests/Icons/star.png" alt="">
                                    <p class="p-form f-bold">
                                        <?php echo $roomReview->cumulativeRating; ?>
                                    </p>
                                </div>

                                <div class="show-more-div flex-row">
                                    <a href="room-details.php?roomId=<?php echo $roomObj->roomId; ?>">
                                        <button class="normal-button"> Show More </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>

        <?php
        if (sizeof($savedIdSet) == 0) {
            ?>
            <div class="flex-column empty-data-div" id="empty-data-div">
                <img src="Assests/Icons/empty.png" alt="">
                <p class="p-normal negative"> Your saved list is empty! </p>
            </div>
            <?php
        }
        ?>
    </div>

    <!-- heading -->
    <div class="top-heading-container container">
        <div class="div">
            <p class="p-large f-bold n-light"> Rooms </p>
        </div>
    </div>

    <!-- room container -->
    <div class="room-main-container content-container container flex-column">
        <div class="room-container div flex-row">
            <?php
            $sets = $roomObj->fetchAllRoom("admin");

            if (sizeof($sets) > 0) {
                foreach ($sets as $set) {
                    // processing data
                    $photo = $roomObj->getRoomPhoto($set['room_id']);

                    $amenities = unserialize(base64_decode($set['amenities']));
                    ?>
                    <div class="room flex-row">
                        <div class="room-photo-div flex-column">
                            <img src="Assests/Uploads/Room/<?php echo $photo; ?>" alt="" class="room-image">
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
                                            href="Operation/wishlist-op.php?userId=<?php echo 0; ?>&task=remove&roomId=<?php echo $set['room_id']; ?>&url=<?php echo $url; ?>">
                                            <img src="Assests/Icons/saved.png" alt="">
                                        </a>
                                        <?php
                                    } else {
                                        ?>
                                        <a
                                            href="Operation/wishlist-op.php?userId=<?php echo 0; ?>&task=add&roomId=<?php echo $set['room_id']; ?>&url=<?php echo $url; ?>">
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
                            <abbr title="<?php foreach ($amenities as $amenity)
                                echo returnArrayValue('amenity', $amenity) . ', '; ?>">
                                <div class="amenity-div">
                                    <p class="p-normal n-normal f-form">
                                        <?php foreach ($amenities as $amenity)
                                            echo returnArrayValue('amenity', $amenity) . ', '; ?>
                                    </p>
                                </div>
                            </abbr>

                            <!-- price & show more section -->
                            <div class="price-div">
                                <p class="p-normal f-bold">
                                    <?php echo returnFormattedPrice($set['rent_amount']); ?>/ <span class="n-normal f-normal">
                                        Month</span>
                                </p>
                            </div>

                            <div class="price-show-more-div flex-row">
                                <!-- rating div -->
                                <div class="flex-row rating-div">
                                    <?php $roomReview->setFinalRating($set['room_id']); ?>
                                    <img class="icon-class" src="Assests/Icons/full-rating.png" alt="">
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
                ?>
                <p class="p-normal negative"> No room has been added. </p>
                <?php
            }
            ?>



            <div class="room flex-row hidden">
                <div class="top flex-column">
                    <div class="room-photo-div flex-column"> </div>
                </div>
            </div>
        </div>
    </div>

    <!-- banner -->
    <div class="banner-container container section-container" id="find-room">
        <div class="banner-div div flex-column">
            <div class="top">
                <p class="p-larger"> Didn't Find What You Were Looking For? </p>
            </div>

            <div class="bottom pointer" id="custom-room-trigger">
                <p class="p-large"> LET US KNOW </p>
            </div>
        </div>
    </div>

    <!-- about us -->
    <div class="about-us-container section-container container flex-row">
        <div class="about-us-div div flex-row">
            <div class="left">
                <p class="p-large f-bold" id="heading"> About Us </p>
                <p class="p-normal" id="about">
                    Lorem ipsuma repecorpotaquas. Npit exercitationem accusamus, ut fugit in! Ullam perspiciatis autem
                    quaerat doloremque mollitia esse consectetur, tempore sequi totam ipsa rem laboriosam, aut, harum
                    molestias nisi provident obcaecati explicabo facere laudantium porro? Ratione suscipit corrupti
                    voluptas! Ut labore pariatur ea, odio quod quaerat quis, explicabo tenetur incidunt dolore
                    consequuntur inventore? Quasi reprehenderit nostrum, possimus animi.
                </p>
                <button> View More </button>
            </div>

            <div class="right">
                <img src="Assests/Images/room-1.jpg" alt="">
            </div>
        </div>
    </div>

    <!-- Supported Landlord Activites -->
    <div class="top-heading-container container hidden">
        <div class="div">
            <p class="p-large f-bold"> Supported Landlord Activites </p>
            <div class="list-div">
                <ul>
                    <li class="p-normal"> Activity 1 </li>
                    <li class="p-normal"> Activity 2 </li>
                    <li class="p-normal"> Activity 3 </li>
                    <li class="p-normal"> Activity 4 </li>
                    <li class="p-normal"> Activity 5 </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Supported Tenant Activites -->
    <div class="top-heading-container container hidden">
        <div class="div">
            <p class="p-large f-bold"> Supported Tenant Activites </p>
            <div class="list-div">
                <ul>
                    <li class="p-normal"> Activity 1 </li>
                    <li class="p-normal"> Activity 2 </li>
                    <li class="p-normal"> Activity 3 </li>
                    <li class="p-normal"> Activity 4 </li>
                    <li class="p-normal"> Activity 5 </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- user feedback -->
    <div class="container heading-container">
        <div class="div">
            <p class="p-larger f-bold"> What our happy customer saying about us. </p>
        </div>
    </div>

    <div class="customer-review-container content-container container">
        <div class="customer-review-div div flex-row">
            <?php
            $feedbackSets = $feedback->fetchFeedbackSet();
            if (sizeof($feedbackSets) > 0) {
                // $feedbackSets = $feedback->fetchAllFeedbacks();
            
                foreach ($feedbackSets as $feedbackSet) {
                    $userObj->fetchSpecificRow($feedbackSet['user_id']);
                    $reviewerImage = $userObj->userPhoto;
                    $firstname = $userObj->firstName;
                    $middlename = $userObj->middleName;
                    $lastName = $userObj->lastName;
                    $username = returnFormattedName($firstname, $middlename, $lastName);
                    $role = $userObj->role;
                    ?>

                    <div class="customer-review flex-column">
                        <div class="top flex-row">
                            <div class="left">
                                <img src="Assests/Uploads/user/<?php echo $reviewerImage; ?>" alt="">
                            </div>

                            <div class="right">
                                <p class="p-normal f-bold">
                                    <?php echo $username; ?>
                                </p>
                                <p class="p-normal">
                                    <?php echo ucfirst($role); ?>
                                </p>
                            </div>
                        </div>

                        <div class="bottom">
                            <div class="top">
                                <p class="p-normal">
                                    "
                                    <?php echo ucfirst($feedbackSet['feedback_data']); ?>"
                                </p>
                            </div>

                            <div class="bottom">
                                <div class="flex-row rating">
                                    <?php
                                    for ($count = 0; $count < $feedbackSet['rating']; $count++)
                                        echo '<img src="Assests/Icons/star.png" alt="error">';
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <p class="p-normal negative"> No user has reviewed us. </p>
                <?php
            }
            ?>
        </div>
    </div>

    <!-- what we offer -->
    <div class="flex-column content-container container our-services-container">
        <div class="div our-services-div content-container">

            <div class="heading">
                <p class="p-larger f-bold"> What we offer? </p>
            </div>

            <div class="services-div">
                <!-- service 1 -->
                <div class="service-card flex-column">
                    <div class="flex-row top">
                        <img src="Assests/Icons/custom.png" alt="">
                    </div>

                    <div class="middle">
                        <p class="f-bold"> Customized Room Application </p>
                    </div>

                    <div class="bottom">
                        <p class="p-normal">
                            Lorem ipsum dolor sit, amet consectetur adipisicing elit. Provident aliquam beatae labore.
                            Sit
                        </p>
                    </div>
                </div>

                <!-- service 2 -->
                <div class="service-card flex-column">
                    <div class="flex-row top" id="service-card-id">
                        <img src="Assests/Icons/building.png" alt="">
                        <p class="p-larger"> > </p>
                        <img src="Assests/Icons/room.png" alt="">
                    </div>

                    <div class="middle">
                        <p class="f-bold"> Add House Then Add Room </p>
                    </div>

                    <div class="bottom">
                        <p class="p-normal">
                            Lorem ipsum dolor sitrum ea necessitatibus animi fugit qui.
                        </p>
                    </div>
                </div>

                <!-- service 3 -->
                <div class="service-card flex-column">
                    <div class="flex-row top">
                        <img src="Assests/Icons/announcement.png" alt="">
                    </div>

                    <div class="middle">
                        <p class="f-bold"> Convey Issues to Landlord Directly </p>
                    </div>

                    <div class="bottom">
                        <p class="p-normal">
                            Lorem ipsum dolor sit, amet consectetur. Lorem ipsum dolor sit amet consectetur adipisicing
                            elit. Architecto, enim?
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- dialog box -->
    <div class="container dialog-container" id="dialog-container-id">
        <div class="div dialog-div">
            <div class="flex-row top">
                <p class="p-large n-light" id="dialog-message"> Be member first to access this feature. </p>
                <img src="assests/Icons/Cancel-filled.png" alt="" class="icon-class" id="dialog-close">
            </div>

            <div class="bottom">
                <button class="" onclick="window.location.href='registration.php'"> Register Now </button>
            </div>
        </div>
    </div>

    <!-- footer -->
    <div class="footer-container container flex-column">
        <div class="footer-div div flex-row">
            <!-- website -->
            <div class="first flex-column">
                <div class="top">
                    <p class="p-large f-bold"> RentRover </p>
                </div>

                <div class="bottom">
                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/right-arrow-white.png" alt="">
                        </div>

                        <div class="right">
                            <a href="">
                                <p class="p-normal"> About Us </p>
                            </a>
                        </div>
                    </div>

                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/right-arrow-white.png" alt="">

                        </div>

                        <div class="right">
                            <a href="">
                                <p class="p-normal"> Send Us a Message </p>
                            </a>
                        </div>
                    </div>

                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/right-arrow-white.png" alt="">

                        </div>

                        <div class="right">
                            <a href="">
                                <p class="p-normal"> FAQ </p>
                            </a>
                        </div>
                    </div>

                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/right-arrow-white.png" alt="">

                        </div>

                        <div class="right">
                            <a href="">
                                <p class="p-normal"> Blog </p>
                            </a>
                        </div>
                    </div>

                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/right-arrow-white.png" alt="">

                        </div>

                        <div class="right">
                            <a href="">
                                <p class="p-normal"> Privacy Policy </p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- quick links -->
            <div class="second">
                <div class="top">
                    <p class="p-large f-bold"> Quick Links </p>
                </div>

                <div class="bottom flex-column">
                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/right-arrow-white.png" alt="">

                        </div>

                        <div class="right">
                            <a href="">
                                <p class="p-normal"> Pre Booking </p>
                            </a>
                        </div>
                    </div>

                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/right-arrow-white.png" alt="">

                        </div>

                        <div class="right">
                            <a href="">
                                <p class="p-normal"> Popular Property </p>
                            </a>
                        </div>
                    </div>

                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/right-arrow-white.png" alt="">

                        </div>

                        <div class="right">
                            <a href="">
                                <p class="p-normal"> Newly Added Rooms </p>
                            </a>
                        </div>
                    </div>

                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/right-arrow-white.png" alt="">

                        </div>

                        <div class="right">
                            <a href="">
                                <p class="p-normal"> Be a Landlord </p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- related links -->
            <div class="third">
                <div class="top">
                    <p class="p-large f-bold"> Related Links </p>
                </div>

                <div class="bottom flex-column">
                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/right-arrow-white.png" alt="">

                        </div>

                        <div class="right">
                            <a href="">
                                <p class="p-normal"> Pre Booking </p>
                            </a>
                        </div>
                    </div>

                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/right-arrow-white.png" alt="">

                        </div>

                        <div class="right">
                            <a href="">
                                <p class="p-normal"> Popular Property </p>
                            </a>
                        </div>
                    </div>

                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/right-arrow-white.png" alt="">

                        </div>

                        <div class="right">
                            <a href="">
                                <p class="p-normal"> Newly Added Rooms </p>
                            </a>
                        </div>
                    </div>

                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/right-arrow-white.png" alt="">

                        </div>

                        <div class="right">
                            <a href="">
                                <p class="p-normal"> Be a Landlord </p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- contact section -->
            <div class="fourth flex-column">
                <div class="top">
                    <p class="p-large f-bold"> Contact Us </p>
                </div>

                <div class="bottom flex-column">
                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/map.png" alt="">
                        </div>

                        <div class="right">
                            <p class="p-normal"> Bansbari, Kathmandu </p>
                        </div>
                    </div>

                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/call.png" alt="">
                        </div>

                        <div class="right">
                            <p class="p-normal"> +977 9823645014 </p>
                        </div>
                    </div>

                    <div class="section flex-row">
                        <div class="left">
                            <img src="Assests/Icons/gmail.png" alt="">
                        </div>

                        <div class="right">
                            <p class="p-normal"> bishaltamang117@gmail.com </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- copyright section -->
        <div class="copyright-container container">
            <div class="copyright-div div">
                <p class="p-normal"> Copyright &copy 2024 RentRover.com - All rights reserved. </p>
            </div>
        </div>
    </div>

    <!-- js section -->
    <script src="Js/jquery-3.7.1.min.js"> </script>

    <script>
        const dialogContainer = $('#dialog-container-id');

        dialogContainer.hide();

        var savedContainerState = false;
        const savedContainer = $('#unsigned-user-saved-container');
        savedContainer.hide();

        $('#saved-icon').click(function () {
            if (savedContainerState) {
                savedContainer.hide();
                savedContainerState = false;
            }
            else {
                savedContainerState = true;
                savedContainer.show();
            }
        });

        $('#dialog-close').click(function () {
            dialogContainer.hide();
        });

        $('#custom-room-trigger').click(function () {
            dialogContainer.show();
        });
    </script>
</body>

</html>