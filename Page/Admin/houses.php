<?php

// starting session
if (!session_start())
    session_start();

// redirecting page to login page if not logged in
if (!isset($_SESSION['adminId']))
    header("Location: login.php");

// including external files
include '../../class/user_class.php';
include '../../class/house_class.php';
include '../../class/functions.php';

// creating objects
$user = new User();
$roomObj = new Room();
$houseObj = new House();

$search = false;

if (isset($_GET['content'])) {
    $search = true;
    $content = $_GET['content'];
} else
    $search = false;

// on search
if (isset($_GET['$search-btn'])) {
    $content = $_GET['content'];
    header("Location: houses.php?content='$content'");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> Houses </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- main css import -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/common/table.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/houses.css">

    <!-- jquery -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <?php
    include 'aside.php';
    ?>

    <div class="body-container flex-row">
        <!-- empty section -->
        <aside class="empty-section"> </aside>

        <article class="content-article">
            <!-- heading -->
            <div class="section-heading-container content-container">
                <p class="f-bold negative"> Houses </p>
            </div>

            <!-- card -->
            <div class="card-container flex-row">
                <!-- all houses -->
                <div class="card flex-column pointer" id="all-house-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $houseObj->countHouse("all"); ?>
                    </p>
                    <p class="p-form"> All Houses </p>
                </div>

                <!-- unverified -->
                <div class="card flex-column pointer" id="unverified-house-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $houseObj->countHouse("unapproved"); ?>
                    </p>
                    <p class="p-form"> Unverified </p>
                </div>

                <!-- verified -->
                <div class="card flex-column pointer" id="verified-house-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $houseObj->countHouse("1"); ?>
                    </p>
                    <p class="p-form"> Verified </p>
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
                        <div class="clear-search-div flex-row pointer" onclick="window.location.href='houses.php'">
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


            <!-- house table -->
            <div class="housestable-container table-container content-container flex-column">
                <!-- top section : heading, sor & search -->
                <div class="table-top-section flex-row">
                    <div class="table-name-clear-sort-div flex-row">
                        <div class="table-name flex-row">
                            <p class="p-normal f-bold" id="house-table-heading">
                                <?php echo (!$search) ? 'All Houses' : 'Search Results '; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- houses table -->
                <?php $sets = ($search) ? $houseObj->searchHouse($_GET['content']) : $houseObj->fetchAllHouses("all"); ?>
                <table class="houses-table table-class" id="houses-table">
                    <thead>
                        <th class="t-id first-td"> ID </th>
                        <th class="t-identity-name"> Identity Name </th>
                        <th class="t-owner-name"> Owner Name </th>
                        <th class="t-location-name"> Location </th>
                        <th class="t-number-of-room"> No. of Room </th>
                        <th class="t-house-state"> House State </th>
                    </thead>

                    <tbody>
                        <?php
                        if (sizeof($sets) > 0) {
                            foreach ($sets as $set) {
                                ?>
                                <tr onclick="window.location.href='house-detail.php?houseId=<?php echo $set['house_id']; ?>'"
                                    class="element 
                                <?php
                                if ($set['house_state'] == 0)
                                    echo "unverified-element";
                                else if ($set['house_state'] == 1)
                                    echo "verified-element";
                                else if ($set['house_state'] == 1)
                                    echo "suspended-element";
                                ?>">

                                    <td class="t-id first-td">
                                        <?php echo $set['house_id']; ?>
                                    </td>

                                    <td class="t-identity-name">
                                        <?php echo ucfirst($set['house_identity']); ?>
                                    </td>

                                    <td class="t-owner-name">
                                        <?php
                                        $userId = $set['owner_id'];
                                        echo '<abbr title="See owner details"><a href="user-detail.php?userId=', $userId, '">' . $user->getUserName(($set['owner_id']) . '</a> </abbr>');
                                        ?>
                                    </td>

                                    <td class="t-location-name">
                                        <?php echo ucfirst($set['area_name']) . ' ,' . returnArrayValue("district", $set['district']); ?>
                                    </td>

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
                <img src="../../Assests/Icons/empty.png" alt="">
                <p class="p-normal negative"> Empty! </p>
            </div>
        </article>
    </div>

    <!-- script section -->
    <script>
        const activeMenu = $('#house-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
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
                } else if (state == 3) {
                    suspendedElements.show();
                    $('#house-table-heading')[0].innerText = "Suspended Houses";
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
                $('#house-state-select')[0].value = 0;
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

</html