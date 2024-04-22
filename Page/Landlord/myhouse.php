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
    header("Location: ../login.php");
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
    <link rel="stylesheet" href="../../CSS/landlord/myhouse.css">

    <!-- title -->
    <title> My Houses </title>

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

    <div class="body-container flex-row">
        <!-- empty section -->
        <aside class="empty-section"> </aside>

        <article class="flex-column content-article">
            <!-- heading -->
            <div class="section-heading-container content-container">
                <p class="f-bold negative"> Houses </p>
            </div>

            <div class="add-new-house-container content-container">
                <a href="add-house.php">
                    <button> Add New House </button>
                </a>
            </div>

            <!-- myhouse cards -->
            <div class="content-container flex-row card-container">
                <!-- total house card -->
                <div class="card flex-column pointer" id="all-house-filter-card">
                    <p class="p-normal"> Total </p>
                    <p class="p-normal f-bold counter">
                        <?php echo $houseObj->countUserHouse($user->userId, "all"); ?>
                    </p>
                </div>

                <!-- unverified house card -->
                <div class="card flex-column pointer" id="unverified-house-filter-card">
                    <p class="p-normal"> Unverified </p>
                    <p class="p-normal f-bold counter">
                        <?php echo $houseObj->countUserHouse($user->userId, "0"); ?>
                    </p>
                </div>

                <!-- verified house card -->
                <div class="card flex-column pointer" id="verified-house-filter-card">
                    <p class="p-normal"> Verified </p>
                    <p class="p-normal f-bold counter">
                        <?php echo $houseObj->countUserHouse($user->userId, "1"); ?>
                    </p>
                </div>
            </div>

            <!-- filter & search -->
            <div class="container content-container flex-row filter-search-container">
                <div class="flex-row filter-div">
                    <div class="flex-row filter-icon-div ">
                        <img src="../../Assests/Icons/filter.png" alt="">
                    </div>

                    <!-- house state select -->
                    <div class="flex-row filter-select-div user-role-div">
                        <label for="house-state-select"> House State </label>
                        <select name="house-state-select" id="house-state-select">
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
                        <div class="clear-search-div flex-row pointer" onclick="window.location.href='myhouse.php'">
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

            <div class="myhouse-table-container table-container content-container flex-column">
                <!-- top section -->
                <div class="table-top-section flex-row">
                    <div class="table-name-clear-sort-div flex-row">
                        <div class="table-name flex-row">
                            <p class="p-normal f-bold" id="house-table-heading">
                                <?php echo (!$search) ? 'Houses' : 'Search Results '; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- table section -->
                <?php $sets = ($search) ?$houseObj->searchHouse($_GET['content']) : $houseObj->fetchAllHouses($user->userId); ?>

                <table class="my-house-table table-class" id="houses-table">
                    <thead>
                        <th class="t-house-id first-td"> ID </th>
                        <th class="t-house-identity"> House Identity Name </th>
                        <th class="t-location-name"> Location </th>
                        <th class="t-number-of-room"> No. of rooms </th>
                        <th class="t-house-state"> House State </th>
                    </thead>

                    <tbody>
                        <?php
                        if (sizeof($sets) > 0) {
                            foreach ($sets as $set){
                                ?>
                                <tr onclick="window.location.href='myhouse-detail.php?houseId=<?php echo $set['house_id']; ?>'" class="element 
                                <?php
                                if ($set['house_state'] == 0)
                                    echo "unverified-element";
                                else if ($set['house_state'] == 1)
                                    echo "verified-element";
                                else if ($set['house_state'] == 1)
                                    echo "suspended-element";
                                ?>">
                                   
                                    <td class="t-house-id first-td">
                                        <?php echo $set['house_id']; ?>
                                    </td>

                                    <td class="t-house-identity">
                                        <?php echo ucfirst($set['house_identity']); ?>
                                    </td>

                                    <td class="t-location-name">
                                        <?php echo returnFormattedString($set['area_name']) . ', ' . returnArrayValue("district", $set['district']); ?>
                                    </td>

                                    <!-- task left -->
                                    <td class="t-number-of-room">
                                        <?php echo $roomObj->countRoomOfThisHouse($set['house_id']); ?>
                                    </td>

                                    <td class="t-house-state">
                                        <?php
                                        if ($set['house_state'] == 0)
                                            echo "Unverified";
                                        elseif ($set['house_state'] == 1)
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
        const activeMenu = $('#house-menu-id');
        activeMenu.css({
            "background-color" : "#DFDFDF"
        });
    </script>

    <!-- custom script section -->
    <script>
        $(document).ready(function () {
            const elements = $('.element');
            const verifiedElements = $('.verified-element');
            const unverifiedElements = $('.unverified-element');
            const suspendedElements = $('.suspended-element');

            const clearSortDiv = $('#clear-sort');

            // values
            var state = 0;

            // hiding filter
            clearSortDiv.hide();

            // individual sorting task
            $('#all-house-filter-card').click(function () {
                state = 0;
                $('#house-state-select')[0].value = state;
                filterElement();
            });

            $('#unverified-house-filter-card').click(function () {
                state = 1;
                $('#house-state-select')[0].value = state;
                filterElement();
            });

            $('#verified-house-filter-card').click(function () {
                state = 2;
                $('#house-state-select')[0].value = state;
                filterElement();
            });

            $('#suspended-house-filter-card').click(function () {
                state = 3;
                $('#house-state-select')[0].value = state;
                filterElement();
            });

            filterElement = () => {
                elements.hide();

                var elementCount = 0;

                // house state
                if (state == 0) {
                    elements.show();
                    $('#house-table-heading')[0].innerText = "All Houses";
                } else if (state == 1) {
                    unverifiedElements.show();
                    $('#house-table-heading')[0].innerText = "Unverified Houses";
                } else if (state == 2) {
                    verifiedElements.show();
                    $('#house-table-heading')[0].innerText = "Verified Houses";
                }

                elementCount = countVisibleRows();

                if (elementCount == 0)
                    $("#empty-data-div").show();
                else
                    $("#empty-data-div").hide();

                $('#clear-sort').show();

                if (state == 0)
                    $('#clear-sort').hide();
            }

            $('#house-state-select').change(function () {
                state = $('#house-state-select')[0].value;
                filterElement();
            });

            $('#clear-sort').click(function () {
                state = 0;
                $('#house-state-select')[0].value = state;
                $('#clear-sort').hide();
                filterElement();
            });

            function countVisibleRows() {
                var visibleRows = $("#houses-table tbody tr:visible");
                var visibleRowCount = visibleRows.length;
                return visibleRowCount;
            }

            filterElement();
        });
    </script>
</body>

</html>