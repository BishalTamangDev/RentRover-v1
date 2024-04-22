<?php
// staring session
if (!session_start())
    session_start();

// including files
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../class/functions.php';

// creating the object
$user = new User();
$houseObj = new House();
$roomObj = new Room();

$user->userId = $_SESSION['landlordUserId'];

if (!isset($_SESSION['landlordUserId']))
    header("Location: login.php");
else
    $user->fetchSpecificRow($_SESSION['landlordUserId']);

$search = false;
$content = "";

// searching
if (isset($_GET['content']) && $_GET['content'] != '') {
    $search = true;
    $content = $_GET['content'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/common/table.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/houses.css">

    <!-- title -->
    <title> My Rooms </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="flex-row body-container">
        <!-- empty section -->
        <aside class="empty-section"> </aside>

        <article class="flex-column content-article">
            <!-- heading -->
            <div class="section-heading-container content-container">
                <p class="f-bold negative"> My Rooms </p>
            </div>

            <div class="add-new-house-container content-container hidden">
                <a href="add-room.php">
                    <button> Add New Room </button>
                </a>
            </div>

            <!-- cards -->
            <div class="flex-row content-container card-container" id="my-room-card-container">
                <!-- all room card -->
                <div class="card flex-column pointer" id="all-room-filter-card">
                    <p class="p-normal f-bold counter">
                        <?php echo $roomObj->countRoom($_SESSION['landlordUserId'], "allHouses", "allTypes"); ?>
                    </p>
                    <p class="p-normal"> All Rooms </p>
                </div>

                <!-- unverified room card -->
                <div class="card flex-column pointer" id="unverified-room-filter-card">
                    <p class="p-normal f-bold counter">
                        <?php echo $roomObj->countRoom($_SESSION['landlordUserId'], "allHouses", "unverified"); ?>
                    </p>
                    <p class="p-normal"> Unverified </p>
                </div>
                
                <!-- verified room card -->
                <div class="card flex-column pointer" id="verified-room-filter-card">
                    <p class="p-normal f-bold counter">
                        <?php echo $roomObj->countRoom($_SESSION['landlordUserId'], "allHouses", "verified"); ?>
                    </p>
                    <p class="p-normal"> Verified </p>
                </div>                

                <!-- acquired room card -->
                <div class="card flex-column pointer" id="acquired-room-filter-card">
                    <p class="p-normal f-bold counter">
                        <?php echo $roomObj->countRoom($_SESSION['landlordUserId'], "allHouses", "acquired"); ?>
                    </p>
                    <p class="p-normal"> Acquired </p>
                </div>

                <!-- unacquired room card -->
                <div class="card flex-column pointer" id="unacquired-room-filter-card">
                    <p class="p-normal f-bold counter">
                        <?php echo $roomObj->countRoom($_SESSION['landlordUserId'], "allHouses", "unacquired"); ?>
                    </p>
                    <p class="p-normal"> Unacquired </p>
                </div>
            </div>

            <!-- filter & search -->
            <div class="container content-container flex-row filter-search-container">
                <div class="flex-row filter-div">
                    <div class="flex-row filter-icon-div ">
                        <img src="../../Assests/Icons/filter.png" alt="">
                    </div>

                    <!-- room type select -->
                    <div class="flex-row filter-select-div room-type-select-div">
                        <label for="room-type-select"> Type </label>
                        <select name="room-type-select" id="room-type-select">
                            <option value="0"> All </option>
                            <option value="1"> BHK </option>
                            <option value="2"> Non BHK </option>
                        </select>
                    </div>

                    <div class="flex-row filter-select-div room-furnishing-select-div">
                        <label for="room-furnishing-select"> Furnishing </label>
                        <select name="room-furnishing" id="room-furnishing-select">
                            <option value="0"> All </option>
                            <option value="1"> Unfurnished </option>
                            <option value="2"> Semi-Furnished </option>
                            <option value="3"> Fully Furnished </option>
                        </select>
                    </div>

                    <div class="flex-row filter-select-div room-state-select-div">
                        <label for="room-acquired-select"> Acquired </label>
                        <select name="room-acquired" id="room-acquired-select">
                            <option value="0"> All </option>
                            <option value="1"> Unacquired </option>
                            <option value="2"> Acquired </option>
                        </select>
                    </div>

                    <div class="flex-row filter-select-div room-state-select-div">
                        <label for="room-state-select"> Verification </label>
                        <select name="room-state" id="room-state-select">
                            <option value="0"> All </option>
                            <option value="1"> Unverified </option>
                            <option value="2"> Verified </option>
                        </select>
                    </div>

                    <div class="flex-row pointer clear-filter-div" id="clear-sort">
                        <p class="p-form"> Clear Sort </p>
                        <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                    </div>
                </div>

                <div class="flex-row search-div">
                    <?php
                    if ($search) {
                        ?>
                        <div class="clear-search-div flex-row pointer" onclick="window.location.href='myroom.php'">
                            <p class="p-form"> Clear Search </p>
                            <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                        </div>
                        <?php
                    }
                    ?>

                    <form action="" method="GET" class="flex-row search-form">
                        <input type="text" name="content" id="" placeholder="Search here..." value="<?php if ($search)
                            echo $content; ?>" required>
                        <button class="search-button" type="submit" name="search-btn"> <img
                                src="../../Assests/Icons/search-normal.svg" alt=""> </button>
                    </form>
                </div>
            </div>

            <!-- table section -->
            <?php $sets = ($search) ? $roomObj->searchRoom($_GET['content']) : $roomObj->fetchAllRoom($user->userId); ?>

            <div class="myroom-table-container table-container content-container flex-column">
                <!-- top section -->
                <div class="table-top-section flex-row">
                    <!-- top section : heading -->
                    <div class="table-top-section flex-row">
                        <div class="table-name-clear-sort-div flex-row">
                            <div class="table-name flex-row">
                                <p class="p-normal f-bold" id="room-table-heading">
                                    <?php echo (!$search) ? 'Rooms' : 'Search Results '; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- table section -->
                <table class="my-room-table table-class" id="rooms-table">
                    <thead>
                        <th class="t-id first-td"> ID </th>
                        <th class="t-house-id"> House ID </th>
                        <th class="t-room-number"> Room No. </th>
                        <th class="t-room-type"> Type </th>
                        <th class="t-furnished"> Furnishing </th>
                        <th class="t-number-of-room"> No. of rooms </th>
                        <th class="t-rent"> Rent </th>
                        <th class="t-location-name"> Location </th>
                        <!-- <th class="t-rating"> Rating </th> -->
                        <th class="t-tenant"> Tenant </th>
                        <th class="t-room-state"> State </th>
                    </thead>

                    <tbody>
                        <?php
                        if (sizeof($sets) > 0) {
                            foreach ($sets as $set) {
                                ?>
                                <tr onclick="window.location.href='myroom-detail.php?roomId=<?php echo $set['room_id']; ?>'"
                                class="element <?php echo ($set['room_type'] == 1)? "bhk-element":"non-bhk-element"; ?> <?php if($set['furnishing'] == 1) echo "unfurnished-element"; else if($set['furnishing'] == 2) echo "semi-furnished-element"; else echo "fully-furnished-element";?> <?php echo ($set['is_acquired'] == 0)?"unacquired-element":"acquired-element"; ?> <?php if($set['room_state'] == 0) echo "unverified-element"; else if($set['room_state'] == 1) echo "verified-element"; else echo "suspended-element";?>">

                                    <td class="t-id first-td">
                                        <?php echo $set['room_id']; ?>
                                    </td>

                                    <td class="t-house-id">
                                        <?php echo $set['house_id']; ?>
                                    </td>

                                    <td class="t-room-number">
                                        <?php echo $set['room_number']; ?>
                                    </td>

                                    <td class="t-room-type">
                                        <?php echo ($set['room_type'] == 1) ? "BHK" : "Non BHK"; ?>
                                    </td>

                                    <td class="t-furnishing">
                                        <?php
                                        if ($set['furnishing'] == 1)
                                            echo "Unfurnished";
                                        elseif ($set['furnishing'] == 2)
                                            echo "Semi-Furnished";
                                        else
                                            echo "Full-Furnished";
                                        ?>
                                    </td>

                                    <td class="t-number-of-room">
                                        <?php echo ($set['room_type'] == 1) ? $set['bhk'] . " BHK" : $set['number_of_room']; ?>
                                    </td>

                                    <td class="t-rent">
                                        <?php echo returnFormattedPrice($set['rent_amount']); ?>
                                    </td>

                                    <td class="t-location-name">
                                        <?php echo ucfirst($roomObj->getLocation($set['house_id'])); ?>
                                    </td>
                                    <!-- 
                                    <td class="t-rating">
                                        <?php 
                                        // echo "-"; 
                                        ?>
                                    </td> -->

                                    <td class="t-tenant">
                                        <?php
                                        if($set['tenant_id'] != 0){
                                            echo $user->getUserName($set['tenant_id']);
                                        }else{
                                            echo '-';
                                        }
                                        ?>
                                    </td>

                                    <td class="t-room-state">
                                        <?php
                                        if ($set['room_state'] == 0)
                                            echo "Unverified";
                                        elseif ($set['room_state'] == 1)
                                            echo "Verified";
                                        else
                                            echo "Suspended";
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="flex-column empty-data-div" id="empty-data-div">
                <p class="p-normal negative"> Empty! </p>
            </div>
        </article>
    </div>

    <!-- script section -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>

    <!-- js section -->
    <script>
        const activeMenu = $('#room-menu-id');
        activeMenu.css({
            "background-color" : "#DFDFDF"
        });
    </script>

    <!-- custom script section -->
    <script>
        $(document).ready(function () {
            const roomElement = $('.element');

            // const roomTypeElement = $('.room-type-element');
            const bhkElement = $('.bhk-element');
            const nonBhkElement = $('.non-bhk-element');

            // const roomFurnishingElement = $('.room-furnishing-element');
            const unfurnishedElement = $('.unfurnished-element');
            const semiFurnishedElement = $('.semi-furnished-element');
            const fullyFurnishedElement = $('.fully-furnished-element');

            // const roomAcquiredElement = $('.room-acquired-element');
            const acquiredElement = $('.acquired-element');
            const unacquiredElement = $('.unacquired-element');

            // const roomStateElement = $('.room-state-element');
            const unverifiedElement = $('.unverified-element');
            const verifiedElement = $('.verified-element');
            const suspendedElement = $('.suspended-element');

            const roomTableHeading = $('#room-table-heading');
            const clearSortDiv = $('#clear-sort');

            // values
            var roomType = 0;
            var roomFurnishing = 0;
            var roomAcquired = 0;
            var roomState = 0;

            // hiding filter
            clearSortDiv.hide();

            // individual sorting task
            $('#all-room-filter-card').click(function () {
                roomState = 0;
                $('#room-state-select')[0].value = roomState;
                filterElement();
            });

            $('#unverified-room-filter-card').click(function () {
                roomState = 1;
                $('#room-state-select')[0].value = roomState;
                filterElement();
            });

            $('#verified-room-filter-card').click(function () {
                roomState = 2;
                $('#room-state-select')[0].value = roomState;
                filterElement();
            });

            $('#suspended-room-filter-card').click(function () {
                roomState = 3;
                $('#room-state-select')[0].value = roomState;
                filterElement();
            });
            
            // acquired && unacquired
            $('#unacquired-room-filter-card').click(function () {
                roomAcquired = 1;
                $('#room-acquired-select')[0].value = roomAcquired;
                filterElement();
            });

            $('#acquired-room-filter-card').click(function () {
                roomAcquired = 2;
                $('#room-acquired-select')[0].value = roomAcquired;
                filterElement();
            });

            // select
            // room type
            $('#room-type-select').change(function () {
                roomType = Number($('#room-type-select')[0].value);
                filterElement();
            });

            // furnishing
            $('#room-furnishing-select').change(function () {
                roomFurnishing = Number($('#room-furnishing-select')[0].value);
                filterElement();
            });

            // room acquired
            $('#room-acquired-select').change(function () {
                roomAcquired = Number($('#room-acquired-select')[0].value);
                filterElement();
            });

            // room state
            $('#room-state-select').change(function () {
                roomState = Number($('#room-state-select')[0].value);
                filterElement();
            });

            filterElement = () => {
                roomElement.hide();

                var elementCount = 0;

                // room type
                if (roomType == 0) {
                    bhkElement.show();
                    nonBhkElement.show();
                } else if (roomType == 1)
                    bhkElement.show();
                else if (roomType == 2)
                    nonBhkElement.show();

                // furnishing
                if (roomFurnishing == 1) { // unfurnished
                    semiFurnishedElement.hide();
                    fullyFurnishedElement.hide();
                } else if (roomFurnishing == 2) { //semi-furnished
                    unfurnishedElement.hide();
                    fullyFurnishedElement.hide();
                } else if (roomFurnishing == 3) { //fully-furnished
                    unfurnishedElement.hide();
                    semiFurnishedElement.hide();
                }

                // acquired
                if (roomAcquired == 1)
                    acquiredElement.hide();
                else if (roomAcquired == 2)
                    unacquiredElement.hide();

                // Verification
                if (roomState == 1) {
                    verifiedElement.hide();
                    suspendedElement.hide();
                } else if (roomState == 2) {
                    unverifiedElement.hide();
                    suspendedElement.hide();
                } else if (roomState == 3) {
                    unverifiedElement.hide();
                    verifiedElement.hide();
                }

                elementCount = countVisibleRows();

                if (elementCount == 0)
                    $("#empty-data-div").show();
                else
                    $("#empty-data-div").hide();

                // show clear sort div
                $('#clear-sort').show();

                if (roomType == 0 && roomFurnishing == 0 && roomAcquired == 0 && roomState == 0)
                    $('#clear-sort').hide();
            }

            filterElement();

            function countVisibleRows() {
                var visibleRows = $("#rooms-table tbody tr:visible");
                var visibleRowCount = visibleRows.length;
                return visibleRowCount;
            }

            $('#clear-sort').click(function () {
                roomType = 0;
                roomFurnishing = 0;
                roomAcquired = 0;
                roomState = 0;

                $('#room-type-select')[0].value = roomType;
                $('#room-furnishing-select')[0].value = roomFurnishing;
                $('#room-acquired-select')[0].value = roomAcquired;
                $('#room-state-select')[0].value = roomState;

                filterElement();

                $('#clear-sort').hide();
            });
        });
    </script>
</body>

</html>