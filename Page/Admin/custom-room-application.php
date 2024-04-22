<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['adminId']))
    header("Location: login.php");

// including files
include '../../Class/admin_class.php';
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/custom_room_class.php';
include '../../Class/announcement_class.php';
include '../../class/functions.php';

// creating the object
$admin = new Admin();
$user = new User();
$customRoomApplicationObj = new CustomRoomApplication();
$customRoomApplicationSelected = new CustomRoomApplication();

$selected = isset($_GET['customRoomApplicationId']) ? true : false;

if ($selected) {
    $customRoomApplicationSelected->setKeyValue('id', $_GET['announcementId']);
    $customRoomApplicationSelected->fetchCustomRoomApplication($_GET['announcementId']);
}

// setting the values
$admin->adminId = $_SESSION['adminId'];
$admin->fetchAdmin($admin->adminId);

// fetching all applications
$sets = $customRoomApplicationObj->fetchAllCustomRoomApplication();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> Custom Room Application </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/common/table.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/announcement.css">

    <!-- script section -->
    <script src="../../Js/main.js"> </script>

    <!-- jquery -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <?php
    include 'aside.php';
    ?>

    <div class="body-container flex-row">
        <aside class="empty-section"> </aside>

        <article class="content-article">
            <!-- heading -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold negative"> Custom Room Application </p>
            </div>

            <!-- card -->
            <div class="card-container flex-row">
                <!-- all applications -->
                <div class="card flex-column shadow pointer" id="all-custom-room-application-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo sizeof($sets); ?>
                    </p>
                    <p class="p-form"> All Applications </p>
                </div>

                <!-- unserved applications -->
                <div class="card flex-column shadow pointer" id="unserved-custom-room-application-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $customRoomApplicationObj->countCustomRoomApplication("unserved"); ?>
                    </p>
                    <p class="p-form"> Unserved </p>
                </div>

                <!-- served applications -->
                <div class="card flex-column shadow pointer" id="served-custom-room-application-filter-card">
                    <p class="p-large f-bold n-light">
                        <?php echo $customRoomApplicationObj->countCustomRoomApplication("served"); ?>
                    </p>
                    <p class="p-form"> Served </p>
                </div>
            </div>

            <!-- filter & search -->
            <div class="container content-container flex-row filter-search-container">
                <div class="flex-row filter-div">
                    <div class="flex-row filter-icon-div ">
                        <img src="../../Assests/Icons/filter.png" alt="">
                    </div>

                    <!-- house state select -->
                    <div class="flex-row filter-select-div announcement-type-div">
                        <label for="announcement-type-select"> Application Type </label>
                        <select name="custom-room-application-type-select" id="custom-room-application-type-select">
                            <option value="0"> All </option>
                            <option value="1"> Served </option>
                            <option value="2"> Unserved </option>
                        </select>
                    </div>

                    <div class="flex-row pointer clear-filter-div" id="clear-sort">
                        <p class="p-form"> Clear Sort </p>
                        <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                    </div>
                </div>
            </div>


            <p class="p-normal f-bold content-container negative <?php if (!$selected)
                echo "hidden"; ?>"> Other
                announcements </p>

            <!-- custom room application -->
            <div class="custom-room-table-container table-container content-container flex-column">
                <!-- top section : heading -->
                <div class="table-top-section flex-row">
                    <div class="table-name-clear-sort-div flex-row">
                        <div class="table-name flex-row">
                            <p class="p-normal f-bold" id="room-table-heading"> All Application </p>
                        </div>
                    </div>
                </div>

                <!-- custom room application table -->
                <table class="rooms-table table-class" id="rooms-table">
                    <thead>
                        <th class="t-id first-td"> ID </th>
                        <th class="t-tenant-id"> Tenant </th>
                        <th class="t-location"> Location </th>
                        <th class="t-room-type"> Room Type </th>
                        <th class="t-min-rent-range"> Min Rent </th>
                        <th class="t-max-rent-range"> Max Rent </th>
                        <th class="t-furnishing"> Furnishing </th>
                        <th class="t-state"> State </th>
                        <th class="t-date"> Applied Date </th>
                    </thead>

                    <tbody>
                        <?php
                        if (sizeof($sets) > 0) {
                            foreach ($sets as $set) {
                                ?>
                                <tr
                                    class="<?php echo "custom-room-application-element ";
                                    echo ($set['state'] == 0) ? 'unserved-custom-room-application-element' : 'served-custom-room-application-element'; ?>">
                                    <td class="t-id first-td">
                                        <?php echo $set['custom_room_id']; ?>
                                    </td>

                                    <td class="t-tenant-id">
                                        <?php echo $user->getUserName($set['tenant_id']); ?>
                                    </td>

                                    <td class="t-location">
                                        <?php echo returnArrayValue("district", $set['district']) . ", " . ucfirst($set['area_name']); ?>
                                    </td>

                                    <td class="t-room-type">
                                        <?php echo ($set['room_type'] == 1) ? "BHK" : "Non-BHK"; ?>
                                    </td>

                                    <td class="t-min-rent-range">
                                        <?php echo returnFormattedPrice($set['min_rent']); ?>
                                    </td>

                                    <td class="t-max-rent-range">
                                        <?php echo returnFormattedPrice($set['max_rent']); ?>
                                    </td>

                                    <td class="t-furnishing">
                                        <?php
                                        if ($set['furnishing'] == 0)
                                            echo "Unfurnished";
                                        elseif ($set['furnishing'] == 1)
                                            echo "Semi-Furnished";
                                        else
                                            echo "Fully-Furnished";
                                        ?>
                                    </td>

                                    <td class="t-state">
                                        <?php echo ($set['state'] == 0) ? "Unserved" : "Served"; ?>
                                    </td>

                                    <td class="t-date">
                                        <?php echo $set['date']; ?>
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
                <p class="p-normal negative"> No custom room application found! </p>
            </div>
        </article>
    </div>

    <script>
        const activeMenu = $('#custom-room-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
        });
    </script>

    <!-- script section -->
    <script>
        $(document).ready(function () {
            const emptyContextDiv = $('#empty-data-div');

            // elements
            const customRoomApplication = $('.custom-room-application-element');
            var servedCustomRoomApplication = $('.served-custom-room-application-element');
            var unservedCustomRoomApplication = $('.unserved-custom-room-application-element');

            $('#clear-sort').hide();

            // filter cards
            var filter = 0;
            $('#all-custom-room-application-filter-card').click(function () {
                filter = 0;
                filteCustomRoomApplicationElement();
            });

            $('#served-custom-room-application-filter-card').click(function () {
                filter = 1;
                filteCustomRoomApplicationElement();
            });

            $('#unserved-custom-room-application-filter-card').click(function () {
                filter = 2;
                filteCustomRoomApplicationElement();
            });

            // filterinf function
            filteCustomRoomApplicationElement = () => {
                $('#custom-room-application-type-select')[0].value = filter;

                customRoomApplication.hide();
                if (filter == 0) {
                    $('#clear-sort').hide();
                    customRoomApplication.show();
                    $('#announcement-heading').text("All Announcements");
                } else if (filter == 1) {
                    $('#clear-sort').show();
                    servedCustomRoomApplication.show();
                    $('#announcement-heading').text("Both Targeted Announcements");
                } else {
                    $('#clear-sort').show();
                    unservedCustomRoomApplication.show();
                    $('#announcement-heading').text("Landlord Targeted Announcements");
                }

                if ($('.custom-room-application-element:visible').length == 0)
                    emptyContextDiv.show();
                else
                    emptyContextDiv.hide();
            }

            filteCustomRoomApplicationElement();

            // sort >> select
            $('#custom-room-application-type-select').change(function () {
                filter = $('#custom-room-application-type-select')[0].value;
                filteCustomRoomApplicationElement();
            });

            $('#clear-sort').click(function () {
                filter = 0;
                $('#custom-room-application-type-select')[0].value = 0;
                $('#clear-sort').hide();
                filteCustomRoomApplicationElement();
            });
        });
    </script>
</body>

</html>