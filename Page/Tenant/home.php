<?php
// staring session
if (!session_start())
    session_start();

// including files
include '../../Class/functions.php';
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/wishlist_class.php';
include '../../Class/feedback_class.php';
include '../../Class/custom_room_class.php';
include '../../Class/room_review_class.php';
include '../../Class/notification_class.php';

// creating the object
$user = new User();
$roomObj = new Room();
$houseObj = new House();
$wishlist = new Wishlist();
$feedback = new Feedback();
$feedbackObj = new Feedback();
$roomReview = new RoomReview();
$notification = new Notification();
$customRoomApplication = new CustomRoomApplication();

// setting the values
$user->userId = $_SESSION['tenantUserId'];

if (!isset($_SESSION['tenantUserId']))
    header("Location: ../../index.php");
else{
    $response = $user->fetchSpecificRow($_SESSION['tenantUserId']);
    if(!$response){
        header("Location: logout.php");
    }
}

// getting notification count
$notificationCount = $notification->countNotification("tenant", $user->userId, "unseen");

// wishlist
$wishlistCount = $wishlist->countWishes($_SESSION['tenantUserId']);

// user review
$userReviewMessageState = false;

// review submission check
$submissionState = "Unknown";
if (isset($_GET['submission'])) {
    if ($_GET['submission'] != '')
        $submissionState = $_GET['submission'];
}

// on review submission
if (isset($_POST['submit-feedback'])) {
    $userId = $_SESSION['tenantUserId'];
    $feedbackData = $_POST['feedback-data'];

    if ($_POST['rating-select'] != 0) {
        $rating = $_POST['rating-select'];

        $feedback->setFeedback($userId, $feedbackData, $rating);

        $immediateId = $feedback->registerFeedback();

        // notification
        $notification->setUserVoiceNotification("user-voice", "admin", $_SESSION['tenantUserId'], "tenant", $immediateId);
        $adminNotification = $notification->register();

        if ($adminNotification)
            header("location: home.php?submission=success");
        else
            header("location: home.php?submission=failure");
    } else {
        $userReviewMessageState = true;
    }
}

// search result section
$searchState = false;

$location = "";
$roomType = 1;

// retrieving the search parameter from ther url
if (isset($_GET['location']))
    $location = $_GET['location'];

$maxRent = isset($_GET['max-rent']) ? $_GET['max-rent'] : 0;

// on triggering search button
if (isset($_GET["search-btn"])) {
    $location = $_GET['location'];

    // min rent amount
    if (isset($_GET['min-rent'])) {
        if ($_GET['min-rent'] != '')
            $minRent = $_GET['min-rent'];
        else
            $minRent = 0;
    }

    // max rent amount
    if (isset($_GET['max-rent'])) {
        if ($_GET['max-rent'] != '')
            $maxRent = $_GET['max-rent'];
        else
            $maxRent = 0;
    }

    $searchState = true;
}

// custom room application
$customErrorMessageState = false;
$customErrorMessage = "This is a custom error message.";

if (isset($_POST['custom-room-application-submit'])) {
    $customErrorMessageState = false;

    // retrieving form values
    $customCoordinate = "0,0";
    $customDistrict = $_POST['custom-district'];
    $customAreaName = $_POST['custom-area-name'];
    $customRoomType = $_POST['custom-room-type'];
    $customMinRent = $_POST['custom-min-rent'];
    $customMaxRent = $_POST['custom-max-rent'];
    $customFurnishing = $_POST['custom-furnishing'];

    // min rent should not be greater than max rent
    if ($customMinRent > $customMaxRent) {
        $customErrorMessageState = true;
        $customErrorMessage = "Min rent amount cannot be greater than Max rent amount.";
    } else {
        // select input check
        if ($customDistrict == 0 || $customRoomType == 0 || $customFurnishing == 0) {
            $customErrorMessageState = true;

            if ($customDistrict == 0)
                $customErrorMessage = "Please select the district.";
            elseif ($customRoomType == 0)
                $customErrorMessage = "Please select the room type.";
            elseif ($customFurnishing == 0)
                $customErrorMessage = "Please select the furnishing type.";
        } else {
            $tenantId = $_SESSION['tenantUserId'];
            $state = 0;
            $date = date('Y-m-d H:i:s');
            $customRoomApplication->setCustomRoomApplication($tenantId, $customDistrict, $customAreaName, $customRoomType, $customMinRent, $customMaxRent, $customFurnishing, $state, $date);
            $customRoomApplication->registerCustomRoomApplication();

            // create notification
            $customErrorMessageState = true;
            $customErrorMessage = "Your application has been submitted.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/tenant/index.css">
    <link rel="stylesheet" href="../../CSS/tenant/navbar.css">
    <link rel="stylesheet" href="../../CSS/tenant/room.css">
    <link rel="stylesheet" href="../../CSS/tenant/footer.css">

    <link rel="stylesheet" href="../../CSS/tenant/tenant.css">
    <link rel="stylesheet" href="../../CSS/tenant/home.css">

    <link rel="stylesheet" href="../../CSS/Common/services.css">
    <link rel="stylesheet" href="../../CSS/tenant/custom.css">

    <!-- title -->
    <title> Home </title>

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- jquery import -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <!-- navigation section -->
    <?php include 'navbar.php'; ?>

    <!-- search section -->
    <div class="search-container container flex-column <?php if ($searchState) echo "hidden"; ?>">
        <div class="search-div div flex-column">
            <div class="text-div flex-row">
                <p class="p-largest f-bold">
                    <?php echo ("Discover the Best Rooms For You For Free"); ?>
                </p>
            </div>

            <div class="search-form-div flex-row">
                <form method="GET" class="search-form flex-row" action="">
                    <!-- room spec -->
                    <div class="location-div flex-column">
                        <div class="top flex-row">
                            <img src="../../Assests/Icons/map-pin.png" class="icon-class" alt="">
                            <p class="p-normal n-normal" required> Area name </p>
                        </div>

                        <input type="text" name="location" id="" placeholder="Location" value="<?php
                        if (isset($_GET['location']))
                            echo $_GET['location'];
                        ?>" required>
                    </div>

                    <div class="price-div flex-column">
                        <div class="top flex-row">
                            <img src="../../Assests/Icons/dollar.png" class="icon-class" alt="">
                            <p class="p-normal n-normal"> Rent Range </p>
                        </div>

                        <div class="bottom flex-row">
                            <input type="text" name="min-rent" id="" placeholder="Min" value="<?php
                            if (isset($_GET['min-rent']))
                                echo $_GET['min-rent'];
                            ?>" onkeypress="avoidMistake('number')">

                            <p> - </p>
                            <input type="text" name="max-rent" id="" placeholder="Max" value="<?php
                            if (isset($_GET['max-rent']))
                                echo $_GET['max-rent'];
                            ?>" onkeypress="avoidMistake('number')">
                        </div>
                    </div>

                    <div class="search-button-div flex-row">
                        <button type="submit" name="search-btn">
                            <img src="../../Assests/Icons/search-white.png" alt="">
                        </button>
                        <!-- <input name="search-btn" type="submit" value="Search"> -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    // $sets = ($searchState) ? $sets = $roomObj->searchRoomForTenant($location, $minRent, $maxRent) : $roomObj->fetchRoomsForTenant();
    $residingRoomId = $roomObj->getResidingRoomId($user->userId);
    $sets = ($searchState) ? $sets = $roomObj->searchRoomForTenant($location, $minRent, $maxRent, $residingRoomId) : $roomObj->fetchRoomsForTenant();
    ?>

    <!-- search result heading -->
    <?php
    if ($searchState) {
        // $sets = $roomObj->searchRoom($location, $roomType, $maxRent);
        ?>
        <div class="container content-container result-container">
            <div class="flex-row div result-div">
                <div class="heading-div">
                    <p class="p-large f-bold"> Search Result </p>
                </div>

                <div class="reset-div">

                    <a href="home.php">
                        <button class="negative-button" id="clear-search-btn"> Clear Search </button>
                    </a>
                </div>
            </div>

            <div class="div flex-row attribute-div">
                <div class="flex-row attribute">
                    <img src="../../Assests/Icons/map-pin.png" alt="">
                    <p class="p-normal">
                        <?php echo ucfirst($location); ?>
                    </p>
                </div>

                <!-- rent range -->
                <div class="flex-row attribute">
                    <img src="../../Assests/Icons/money.png" alt="">
                    <p class="p-normal">
                        <?php
                        echo "Rent Range : ";
                        if ($minRent == 0 && $maxRent == 0)
                            echo "All";
                        elseif ($minRent != 0 && $maxRent == 0)
                            echo "Minimum " . returnFormattedPrice($minRent);
                        elseif ($minRent == 0 && $maxRent != 0)
                            echo "Maximun " . returnFormattedPrice($maxRent);
                        else
                            echo "Minimum " . returnFormattedPrice($minRent) . " - Maximun " . returnFormattedPrice($maxRent);
                        ?>
                    </p>
                </div>
            </div>

            <?php
            if ($searchState && sizeof($sets) > 0) {
                ?>
                <div class="container flex-row content-container search-result-count-container">
                    <div class="div search-result-count-div">
                        <p class="p-normal negative">
                            <?php 
                            echo sizeof($sets) == 1 ? sizeof($sets)." result found." :  sizeof($sets)." results found.";
                            ?>
                        </p>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }
    ?>

    <!-- heading : all rooms -->
    <div class="container content-container all-room-heading-container">
        <div class="div flex-row">
            <div class="flex-row left <?php if ($searchState)
                echo "hidden"; ?>">
                <p class="p-large n-light f-bold"> All Rooms </p>
            </div>

            <div class="flex-row right <?php if ($searchState && sizeof($sets) == 0)
                echo "hidden"; ?>">
                <div class="filter-trigger-div flex-row pointer" id="filter-trigger-div" onclick="showFilter()">
                    <p class="p-form n-normal f-bold"> Filter </p>
                    <img src="../../Assests/Icons/filter.png" class="icon-class" alt="">
                </div>
            </div>
        </div>
    </div>

    <div class="filter-container container" id="filter-div">
        <div class="div flex-row filter-div">
            <div class="flex-row select-div district-select-div">
                <label for="district-select"> District </label>
                <select name="district-select" id="district-select">
                    <option value="0" selected> All </option>
                    <?php
                    for ($i = 0; $i < 77; $i++)
                        echo '<option value="' . ($i + 1) . '">' . returnArrayValue('district', $i + 1) . '</option>';
                    ?>
                </select>
            </div>

            <div class="flex-row select-div type-select-div">
                <label for="room-type-select"> Room Type </label>
                <select name="room-type-select" id="room-type-select">
                    <option value="0" selected> All </option>
                    <option value="1"> BHK </option>
                    <option value="2"> Non BHK </option>
                </select>
            </div>

            <div class="flex-row select-div furnishing-select-div">
                <label for="furnishing-select"> Furnishing </label>
                <select name="furnishing-select" id="furnishing-select">
                    <option value="0" selected> All </option>
                    <option value="1"> Unfurnished </option>
                    <option value="2"> Semi-Furnished </option>
                    <option value="3"> Fully-Furnished </option>
                </select>
            </div>

            <div class="flex-row pointer filter-close-div" id="filter-close-div" onclick="hideFilter()">
                <p class="p-form n-normal f-bold pointer"> Clear Filter </p>
                <img src="../../Assests/Icons/Cancel-filled.png" alt="">
            </div>
        </div>
    </div>

    <!-- searched room container -->
    <div class="room-main-container content-container container flex-column">
        <div class="room-container div flex-row">
            <?php
            // $sets = $roomObj->fetchRoomsForTenant();
            $sets = ($searchState) ? $sets = $roomObj->searchRoomForTenant($location, $minRent, $maxRent, $residingRoomId) : $roomObj->fetchRoomsForTenant();

            foreach ($sets as $set) {
                $photo = $roomObj->getRoomPhoto($set['room_id']);
                $amenities = unserialize(base64_decode($set['amenities']));
                $districtNumber = $roomObj->getDistrict($set['house_id']);
                ?>
                <div class="room flex-row elements <?php echo "district-element-$districtNumber"; ?> <?php echo ($set['room_type'] == 1) ? "bhk-element" : "non-bhk-element"; ?> <?php if ($set['furnishing'] == 1)
                                    echo "unfurnished-element";
                                else if ($set['furnishing'] == 2)
                                    echo "semi-furnished-element";
                                else
                                    echo "fully-furnished-element"; ?>">
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
                                if ($searchState) {
                                    $location = $_GET['location'];
                                    $minRent = $_GET['min-rent'];
                                    $max = $_GET['max-rent'];
                                    $url = $_SERVER['REQUEST_URI']."?location=$location&min-rent=$minRent&maxRent=$maxRent";
                                }

                                if ($wishlist->isWish($_SESSION['tenantUserId'], $set['room_id'])) {
                                    ?>
                                    <a
                                        href="operation/wishlist-op.php?userId=<?php echo $_SESSION['tenantUserId']; ?>&task=remove&roomId=<?php echo $set['room_id']; ?>&url=<?php echo $_SERVER['REQUEST_URI']; ?>">
                                        <img src="../../Assests/Icons/saved.png" alt="">
                                    </a>
                                    <?php
                                } else {
                                    ?>
                                    <a
                                        href="operation/wishlist-op.php?userId=<?php echo $_SESSION['tenantUserId']; ?>&task=add&roomId=<?php echo $set['room_id']; ?>&url=<?php echo $_SERVER['REQUEST_URI']; ?>">
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
                                <p class="p-normal n-normal f-form">
                                    <?php foreach ($amenities as $amenity)
                                        echo returnArrayValue('amenity', $amenity) . ', '; ?>
                                </p>
                            </div>
                        </abbr>

                        <!-- price -->
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
                                <?php
                                $roomReview->setFinalRating($set['room_id']);
                                ?>
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
            ?>
        </div>

        <!-- view all -->
        <div class="show-all-container div content-container flex-row pointer hidden"
            onclick="window.location.href='home.php'">
            <p class="p-normal pointer" onclick="window.location.href='home.php'"> Show all </p>
            <img src="../../Assests/Icons/right-arrow-black.png" alt="" class="icon-class"
                onclick="window.location.href='home.php'">
        </div>
    </div>


    <!-- empty context -->
    <div class="container empty-data-container <?php if (sizeof($sets) > 0)
        echo "hidden"; ?>" id="empty-data-container">
        <div class="flex-column div empty-data-div" id="empty-data-div">
            <img src="../../Assests/Icons/empty.png" alt="">
            <p class="p-normal negative"> No rooms found! </p>
        </div>
    </div>

    <!-- banner -->
    <div class="banner-container section-container container <?php if ($searchState)
        echo "hidden"; ?>" id="find-room">
        <div class="banner-div div flex-column">
            <div class="top">
                <p class="p-larger"> Didn't Find What You Were Looking For? </p>
            </div>

            <div class="bottom pointer" id="custom-application-open">
                <p class="p-large"> LET US KNOW </p>
            </div>
        </div>
    </div>

    <!-- custom room application form -->
    <form method="POST">
        <div class="div flex-column custom-room-application-div" id="custom-room-application-div">
            <div class="flex-row custom-top-section">
                <h3> Custom Room Application </h3>
                <abbr title="Close">
                    <img src="../../Assests/Icons/Cancel-filled.png" alt="" id="custom-application-close">
                </abbr>
            </div>

            <?php
            if ($customErrorMessageState || $customErrorMessageState) {
                ?>
                <p class="p-normal negative" style="margin-top: -10px;">
                    <?php echo $customErrorMessage; ?>
                </p>
                <?php
            }
            ?>

            <div class="flex-column custom-map-container">
                <div class="custom-map-div">
                    <img src="../../Assests/Images/map.png" alt="">
                </div>

                <p class="p-form negative"> Point the location around which you want room. </p>
            </div>

            <!-- spec section -->
            <!-- district -->
            <div class="flex-column custom-location-container">
                <p class="p-normal"> Location </p>
                <div class="flex-row custom-location-div">
                    <select name="custom-district" id="custom-district">
                        <?php
                        if ($_POST['custom-district'] != 0)
                            echo '<option value="', $_POST['custom-district'], '" selected hidden>', returnArrayValue("district", $_POST['custom-district']), '</option>';
                        else
                            echo '<option value="0" selected hidden> District </option>';

                        for ($count = 1; $count <= 77; $count++) {
                            echo '<option value="' . $count . '">' . $districtArray[$count] . '</option>';
                        }
                        ?>
                    </select>

                    <input type="text" name="custom-area-name" id="custom-area-name" placeholder="Area name" value="<?php if (isset($_POST['custom-area-name']))
                        echo $_POST['custom-area-name']; ?>" required>
                </div>
            </div>

            <!-- room type -->
            <div class="flex-column custom-room-type-container">
                <div class="flex-column custom-room-type-div">
                    <p class="p-normal"> Room Type </p>
                    <select name="custom-room-type" id="">
                        <?php
                        if (isset($_POST['custom-room-type']) && $_POST['custom-room-type'] != 0) {
                            ?>
                            <option value="<?php echo $_POST['custom-room-type']; ?>" selected hidden>
                                <?php echo ($_POST['custom-room-type'] == 1) ? "BHK" : "Non-BHK"; ?>
                            </option>
                            <?php
                        } else {
                            ?>
                            <option value="0" selected hidden> Select Room Type </option>
                            <?php
                        }
                        ?>
                        <option value="1"> BHK </option>
                        <option value="2"> Non-BHK </option>
                    </select>
                </div>
            </div>

            <!-- price range -->
            <div class="custom-rent-range-container">
                <p class="p-normal"> Rent Amount </p>
                <div class="flex-row custom-rent-range-div">
                    <input name="custom-min-rent" type="text" placeholder="Min Rent" onkeypress='avoidMistake("number")'
                        value="<?php if (isset($_POST['custom-min-rent']))
                            echo $_POST['custom-min-rent']; ?>" required>
                    <p class="p-large"> - </p>
                    <input name="custom-max-rent" type="text" placeholder="Max Rent" onkeypress='avoidMistake("number")'
                        value="<?php if (isset($_POST['custom-max-rent']))
                            echo $_POST['custom-max-rent']; ?>" required>
                </div>
            </div>

            <!-- furnishing -->
            <div class="custom-furnishing-container">
                <p class="p-normal"> Furnishing </p>
                <div class="flex-row custom-furnishing-div">
                    <select name="custom-furnishing" id="custom-furnishing">
                        <?php
                        if (isset($_POST['custom-furnishing']) && $_POST['custom-furnishing'] != 0) {
                            ?>
                            <option value="<?php echo $_POST['custom-furnishing']; ?>" selected hidden>
                                <?php
                                if ($_POST['custom-furnishing'] == 1)
                                    echo "Unfurnished";
                                elseif ($_POST['custom-furnishing'] == 2)
                                    echo "Semi-Furnished";
                                else
                                    echo "Fully-Furnished";
                                ?>
                            </option>
                            <?php
                        } else {
                            ?>
                            <option value="0" selected hidden> Select Furnishing Type </option>
                            <?php
                        }
                        ?>
                        <option value="1"> Unfurnished </option>
                        <option value="2"> Semi-Furnished </option>
                        <option value="3"> Fully-Furnished </option>
                    </select>
                </div>
            </div>

            <p class="p-small"> You will be notified after a new room with above specification meets. </p>

            <input type="submit" name="custom-room-application-submit" value="Submit Application">
        </div>
    </form>

    <div id="dark-background"> </div>

    <!-- customer reviews -->
    <div class="heading-container container <?php if ($searchState)
        echo "hidden"; ?>">
        <div class="heading-div div">
            <p class="p-larger f-bold"> What our happy customer saying about us? </p>
        </div>
    </div>

    <div class="customer-review-container content-container container <?php if ($searchState)
        echo "hidden"; ?>">
        <div class="customer-review-div div flex-row">
            <?php
            $feedbackSets = $feedbackObj->fetchFeedbackSet();

            if (sizeof($feedbackSets) > 0) {
                foreach ($feedbackSets as $feedbackSet) {
                    $user->fetchSpecificRow($feedbackSet['user_id']);
                    $reviewerImage = $user->userPhoto;
                    $firstname = $user->firstName;
                    $middlename = $user->middleName;
                    $lastName = $user->lastName;
                    $username = returnFormattedName($firstname, $middlename, $lastName);
                    $role = $user->role;
                    ?>

                    <div class="customer-review flex-column">
                        <div class="top flex-row">
                            <div class="left">
                                <img src="../../Assests/Uploads/user/<?php echo $reviewerImage; ?>" alt="">
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

                        <div class="bottom flex-column">
                            <div class="top">
                                <p class="p-normal">
                                    <?php echo '"' . ucfirst($feedbackSet['feedback_data']) . '"'; ?>
                                </p>
                            </div>

                            <div class="bottom">
                                <div class="flex-row rating">
                                    <?php
                                    for ($count = 0; $count < $feedbackSet['rating']; $count++)
                                        echo '<img src="../../Assests/Icons/star.png" alt="">';
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>

            <!-- dummy review 1 -->
            <div class="customer-review flex-column hidden">
                <div class="top flex-row">
                    <div class="left">
                        <img src="../../Assests/Uploads/user/blank.jpg" alt="">
                    </div>

                    <div class="right">
                        <p class="p-normal f-bold"> Maeve Maeve </p>
                        <p class="p-normal"> Landlord </p>
                    </div>
                </div>

                <div class="bottom flex-column">
                    <div class="top">
                        <p class="p-normal">
                            "Lorem ipsum dolor sit, amet consectetur adipisicing elit. Mollitia velit laboriosam,
                            voluptatem ducimus eum impedit."
                        </p>
                    </div>

                    <div class="bottom">
                        <div class="flex-row rating">
                            <img src="../../Assests/Icons/star.png" alt="">
                            <img src="../../Assests/Icons/star.png" alt="">
                            <img src="../../Assests/Icons/star.png" alt="">
                            <img src="../../Assests/Icons/star.png" alt="">
                            <img src="../../Assests/Icons/star.png" alt="">
                            <p class="p-form"> (4.5) </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- submit review -->
            <div class="submit-customer-review flex-column">
                <div class="top flex-row pointer" id="submit-review" onclick="showReviewForm()">
                    <img src="../../Assests/Icons/add-black-filled.png" alt="">
                </div>

                <div class="bottom">
                    <p class="p-normal"> Click here to submit your review. </p>
                </div>
            </div>
        </div>
    </div>

    <!-- customer review form -->
    <div class="container content-container customer-review-form-container">
        <div class="customer-review-form-div div flex-row" id="customer-review-form-div">
            <form action="" method="POST" class="customer-review-form flex-column">
                <p class="p-larger f-bold"> Feel free to express your thoughts! </p>

                <p class="p-form negative">
                    <?php
                    if ($userReviewMessageState)
                        echo '<p class="p-form negative"> Select the rating first. </p>';
                    ?>
                </p>

                <textarea name="feedback-data" id="" placeholder="Write your thought here..." required><?php
                if (isset($_POST['feedback-data']))
                    echo $_POST['feedback-data'];
                ?></textarea>

                <select name="rating-select" id="rating-select">
                    <option value="0" selected hidden> Select rating </option>
                    <option value="1"> 1 </option>
                    <option value="2"> 2 </option>
                    <option value="3"> 3 </option>
                    <option value="4"> 4 </option>
                    <option value="5"> 5 </option>
                </select>

                <button type="submit" name="submit-feedback"> Submit <img src="../../Assests/Icons/send-white.png"
                        alt=""> </button>
                <!-- <input type="submit" name="submit-review" value="Submit Review"> -->
            </form>
        </div>
    </div>

    <!-- about us -->
    <div class="about-us-container section-container container flex-row <?php if ($searchState)
        echo "hidden"; ?>">
        <div class="about-us-div div flex-row">
            <div class="left">
                <p class="p-large f-bold" id="heading"> About Us </p>
                <p class="p-normal" id="about">
                    Lorem ipsuma repecorpotaquas. Npit exercitationem accusamus, ut fugit in! Ullam perspiciatis autem
                    quaerat doloremque mollitia esse consectetur, tempore sequi totam ipsa rem laboriosam, aut, harum
                    molestias nisi provident obcaecati explicabo facere laudantium porro? Ratione suscipit corrupti
                    voluptas! Ut labore pariatur ea, odio quod quaerat quis, explicabo tenetur incidunt dolore
                    consequuntur
                    inventore? Quasi reprehenderit nostrum, possimus animi, hic perferendis laudantium reiciendis
                    voluptates
                    expedita a natus inventore magni ex soluta libero provident fuga? Corrupti, tempora modi corporis
                    deserunt repudiandae sequi animi aspernatur quibusdam. A eius corrupti aliquid voluptates hic odit
                    mollitia fugit doloribus quidem sequi animi maiores, omnis eaque quae nulla laboriosam vitae. Facere
                    atque explicabo nihil dolore enim officia? Ea explicabo ipsam quasi.
                </p>
                <button> View More </button>
            </div>

            <div class="right">
                <img src="../../Assests/Images/room-1.jpg" alt="">
            </div>
        </div>
    </div>

    <!-- what we offer -->
    <div class="container content-container our-services-container flex-column <?php if ($searchState)
        echo 'hidden'; ?>">
        <div class="heading">
            <p class="p-larger f-bold"> What we offer? </p>
        </div>

        <div class="services-div">
            <!-- service 1 -->
            <div class="service-card flex-column">
                <div class="flex-row top">
                    <img src="../../Assests/Icons/custom.png" alt="">
                </div>

                <div class="middle">
                    <p class="f-bold"> Customized Room Application </p>
                </div>

                <div class="bottom">
                    <p class="p-normal">
                        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Provident aliquam beatae labore. Sit
                    </p>
                </div>
            </div>

            <!-- service 2 -->
            <div class="service-card flex-column">
                <div class="flex-row top" id="service-card-id">
                    <img src="../../Assests/Icons/building.png" alt="">
                    <p class="p-larger"> > </p>
                    <img src="../../Assests/Icons/room.png" alt="">
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
                    <img src="../../Assests/Icons/announcement.png" alt="">
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

    <!-- Supported Tenant Activites -->
    <div class="top-heading-container content-container container <?php if ($searchState)
        echo 'hidden'; ?>" hidden>
        <div class="div">
            <p class="p-larger f-bold"> Supported Tenant Activites </p>
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

    <!-- dialog -->
    <?php
    if ($submissionState == 'success' || $submissionState == 'failure') {
        ?>
        <div class="dialog-container flex-column">
            <div class="dialog-div flex-column">
                <div class="top-div flex-row">
                    <div class="message-div flex-column">
                        <?php
                        if ($submissionState == 'success') {
                            ?>
                            <p class="p-large f-bold positive"> Your feedback has been successfully submitted successfully. </p>
                            <?php
                        } else if ($submissionState == 'failure') {
                            ?>
                                <p class="p-large f-bold negative"> Your house could not be submitted. </p>
                                <p class="p-normal"> Please try again. </p>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <div class="operation-div flex-row">
                    <?php
                    if ($submissionState == 'success') {
                        ?>
                        <button onclick="window.location.href='home.php';"> Close </button>
                        <?php
                    } else if ($submissionState == 'failure') {
                        ?>
                            <button onclick="window.location.href='home.php';"> Close </button>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    } ?>

    <!-- footer -->
    <?php include 'footer.php'; ?>

    <!-- js section -->
    <script>
        var userMenuState = false;
        var notificationMenuState = false;
        var filterState = false;

        const userMenu = document.getElementById('menu-container');
        const notificationMenu = document.getElementById('notification-container');

        const filterTrigger = document.getElementById('filter-trigger-div');
        const filterClose = document.getElementById('filter-close-div');
        const filterContainer = document.getElementById('filter-div');
        const reviewForm = document.getElementById('customer-review-form-div');

        onload = () => {
            reviewForm.style = "display:none";
            userMenu.style = "display:none";
            filterContainer.style = "display:none";
            notificationMenu.style = "display:none";
        }

        showFilter = () => {
            filterContainer.style = "display:flex";
            filterClose.style = "visibility:visible";
        }

        hideFilter = () => {
            filterContainer.style = "display:none";
            filterClose.style = "visibility:hidden";
        }

        showReviewForm = () => {
            reviewForm.style = "display:block";
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

        // required during custom room application form fill up
        avoidMistake = (inputType) => {
            var ascii = event.keyCode;
            // avoiding space
            if (ascii == 32) {
                event.preventDefault();
            } else {
                // type: text
                if (inputType == 'name') {
                    if ((ascii >= 97 && ascii <= 122) || (ascii >= 65 && ascii <= 90)) {
                    } else
                        event.preventDefault();
                }

                // type: number
                if (inputType == "number") {
                    if (ascii >= 48 && ascii <= 57) {
                    } else
                        event.preventDefault();
                }
            }
        }
    </script>

    <script>
        var locationFilter = 0;
        var typeFilter = 0;
        var furnishingFilter = 0;

        var elements = $('.elements');
        var bhkElement = $('.bhk-element');
        var nonBhkElement = $('.non-bhk-element');
        var unfurnishedElement = $('.unfurnished-element');
        var semiFurnishedElement = $('.semi-furnished-element');
        var fullyFurnishedElement = $('.fully-furnished-element');

        $('#district-select').change(function () {
            locationFilter = Number($('#district-select')[0].value);
            filterElement();

            console.log(locationFilter);
        });

        $('#room-type-select').change(function () {
            typeFilter = Number($('#room-type-select')[0].value);
            filterElement();
        });

        $('#furnishing-select').change(function () {
            furnishingFilter = Number($('#furnishing-select')[0].value);
            filterElement();
        });

        $('#filter-close-div').click(function () {
            $('#filter-close-div').hide();
            locationFilter = 0;
            typeFilter = 0;
            furnishingFilter = 0;

            $('#district-select')[0].value = 0;
            $('#room-type-select')[0].value = 0;
            $('#furnishing-select')[0].value = 0;
            filterElement();
        });

        filterElement = () => {
            var districtElementClass = '.district-element-' + String(locationFilter);
            districtElement = $(districtElementClass);

            // console.log(locationFilter, typeFilter, furnishingFilter);

            elements.hide();

            // filtering by district
            districtElement.show();
            if (districtElementClass == '.district-element-0')
                elements.show();


            // filtering by room type
            if (typeFilter == 1)
                nonBhkElement.hide();
            else if (typeFilter == 2)
                bhkElement.hide();

            // filtering by furnishing
            if (furnishingFilter == 1) {
                semiFurnishedElement.hide();
                fullyFurnishedElement.hide();
            } else if (furnishingFilter == 2) {
                unfurnishedElement.hide();
                fullyFurnishedElement.hide();
            } else if (furnishingFilter == 3) {
                unfurnishedElement.hide();
                semiFurnishedElement.hide();
            }

            // empty context
            visibleElementCount = $('.elements:visible').length;
            if (visibleElementCount == 0)
                $('#empty-data-container').show();
            else
                $('#empty-data-container').hide();
        }

        // custom room application form
        const darkBackground = $('#dark-background');
        const customApplicationOpen = $('#custom-application-open');
        const customApplication = $('#custom-room-application-div');
        const customApplicationClose = $('#custom-application-close');

        darkBackground.hide();
        customApplication.hide();

        customApplicationClose.click(function () {
            customApplication.hide();
            darkBackground.hide();
        });

        customApplicationOpen.click(function () {
            darkBackground.show();
            customApplication.show();
        });
    </script>
</body>

</html>