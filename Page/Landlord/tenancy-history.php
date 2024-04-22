<?php
// session
if (!session_start())
    session_start();

// redirecting to login page is session variable is not set
if (!isset($_SESSION['landlordUserId']))
    header("Location: login.php");

// including external files
include '../../class/functions.php';
include '../../class/user_class.php';
include '../../class/house_class.php';
include '../../Class/tenancy_history_class.php';

// creating objects
$user = new User();
$userObj = new User();
$room = new Room();
$roomObj = new Room();
$house = new House();
$tenancyHistory = new TenancyHistory();

$user->userId = $_SESSION['landlordUserId'];
$user->fetchSpecificRow($user->userId);

$roomIdArray = getRoomIdArray($user->userId);
$tenancyHistorySets = $tenancyHistory->fetchTenancyHistoryForLandlord($roomIdArray);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> RentRover - Room Detail </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/common/table.css">
    <link rel="stylesheet" href="../../CSS/tenant/room.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/house-detail.css">
    <link rel="stylesheet" href="../../CSS/admin/room-detail.css">
    <link rel="stylesheet" href="../../CSS/landlord/myroom-detail.css">
    <link rel="stylesheet" href="../../CSS/Common/lightbox.min.css">

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <script src="../../Js/lightbox-plus-jquery.min.js"> </script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="body-container flex-row">
        <div class="empty-section"></div>

        <article class="content-article flex-column">
            <!-- tenancy history table -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold"> Tenancy History </p>
            </div>

            <!-- tenancy history cards -->
            <div class="card-container flex-row tenancy-history-card-container">
                <?php
                $totalHistory = sizeof($tenancyHistorySets);
                $residingHistory = 0;
                $notResidingHistory = 0;

                foreach($tenancyHistorySets as $tenancyHistorySet){
                    if($tenancyHistorySet['move_out_date'] == "0000-00-00")
                        $residingHistory++;
                    else
                    $notResidingHistory++;
                }
                ?>
                <!-- all -->
                <div class="flex-row pointer card" id="tenancy-history-card-all">
                    <p class="p-form"> All - </p>
                    <p class="p-form f-bold"> &nbsp; <?php echo $totalHistory;?> </p>
                </div>

                <!-- Residing -->
                <div class="flex-row pointer card" id="tenancy-history-card-residing">
                    <p class="p-form"> Residing - </p>
                    <p class="p-form f-bold"> &nbsp; <?php echo $residingHistory;?> </p>
                </div>

                <!-- Non-Residing -->
                <div class="flex-row pointer card" id="tenancy-history-card-not-residing">
                    <p class="p-form"> Non-Residing - </p>
                    <p class="p-form f-bold"> &nbsp; <?php echo $notResidingHistory;?> </p>
                </div>
            </div>

            <!-- filter & search -->
            <div class="container flex-row filter-search-container">
                <div class="flex-row filter-div">
                    <div class="flex-row filter-icon-div ">
                        <img src="../../Assests/Icons/filter.png" alt="">
                    </div>

                    <!-- order select -->
                    <div class="flex-row filter-select-div order-div">
                        <label for="tenancy-history-type-select"> Tenancy State </label>
                        <select name="tenancy-history-type-select"
                            id="tenancy-history-type-select">
                            <option value="0"> All </option>
                            <option value="1"> Residing </option>
                            <option value="2"> Not-Residing </option>
                        </select>
                    </div>

                    <div class="flex-row pointer clear-filter-div" id="tenancy-history-clear-sort">
                        <p class="p-form"> Clear Sort </p>
                        <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                    </div>
                </div>
            </div>

            <table class="content-container table-class tenancy-history-table" id="tenancy-history-table">
                <thead>
                    <th class="t- first-td"> S.N. </th>
                    <th class="t-"> Room ID </th>
                    <th class="t-"> Room Location </th>
                    <th class="t-"> Floor </th>
                    <th class="t-"> Room Number </th>
                    <th class="t-"> Tenant </th>
                    <th class="t-"> Move In Date </th>
                    <th class="t-"> Move Out Date </th>
                </thead>

                <tbody>
                    <?php
                    if (sizeof($tenancyHistorySets) > 0) {
                        $serial = 0;
                        foreach ($tenancyHistorySets as $tenancyHistorySet) {

                            // user detail
                            $userObj->fetchUser($tenancyHistorySet['tenant_id']);
                            $userObj->userId = $tenancyHistorySet['tenant_id'];
                            $tenantName = $userObj->getUserName($userObj->userId);

                            // room detail
                            $roomObj->fetchRoom($tenancyHistorySet['room_id']);
                            $roomObj->roomId = $tenancyHistorySet['room_id'];

                            $location = $roomObj->getLocation($roomObj->houseId);
                            ?>
                            <tr class="tenancy-history-element <?php echo ($tenancyHistorySet['move_out_date'] == "0000-00-00")?"tenancy-history-residing-element":"tenancy-history-not-residing-element"; ?>">
                                <!-- serial -->
                                <td class="t-serial first-td">
                                    <?php echo ++$serial; ?>
                                </td>

                                <!-- room id -->
                                <?php $link = "myroom-detail.php?roomId=$roomObj->roomId";?>
                                <td class="t-" onclick="window.location.href='<?php echo $link; ?>'">
                                    <?php echo $tenancyHistorySet['room_id']; ?>
                                </td>

                                <!-- room location -->
                                <td class="t-">
                                    <?php echo $location; ?>
                                </td>

                                <!-- floor -->
                                <td class="t-">
                                    <?php echo $roomObj->floor; ?>
                                </td>

                                <!-- room number -->
                                <td class="t-">
                                    <?php echo $roomObj->roomNumber; ?>
                                </td>

                                <!-- tenant name -->
                                <?php $link = "tenants-detail.php?tenantId=$userObj->userId";?>
                                <td class="t-" onclick="window.location.href='<?php echo $link; ?>'">
                                    <?php echo $tenantName; ?>
                                </td>

                                <!-- move in date -->
                                <td class="t-">
                                    <?php echo $tenancyHistorySet['move_in_date']; ?>
                                </td>

                                <!-- move out date -->
                                <td class="t-">
                                    <?php
                                    echo ($tenancyHistorySet['move_out_date'] == "0000-00-00") ? "Still residing" : $tenancyHistorySet['move_out_date'];
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>

            <div class="flex-column empty-data-div" id="empty-data-div">
                <p class="p-normal negative"> Empty! </p>
            </div>
        </article>
    </div>

    <!-- script section -->
    <script src="../../Js/lightbox-plus-jquery.min.js"> </script>

    <!-- js section -->
    <script>
        const activeMenu = $('#tenancy-history-menu-id');
        activeMenu.css({
            "background-color" : "#DFDFDF"
        });
    </script>

    <!-- applied room js -->
    <script>
        // cards
        const tenancyHistoryCardAll = $('#tenancy-history-card-all');
        const tenancyHistoryCardResiding = $('#tenancy-history-card-residing');
        const tenancyHistoryCardNotResiding = $('#tenancy-history-card-not-residing');

        const tenancyHistoryTypeSelect = $('#tenancy-history-type-select');
        const tenancyHistoryClearSort = $('#tenancy-history-clear-sort');

        const emptyTenancyHistoryDataMessage = $('#empty-data-div');

        // tenancy-history state elements
        var tenancyHistoryElements = $('.tenancy-history-element');
        var tenancyHistoryResidingElements = $('.tenancy-history-residing-element');
        var tenancyHistoryNotResidingElements = $('.tenancy-history-not-residing-element');

        var tenancyHistoryType = 0;

        tenancyHistoryClearSort.hide();
        emptyTenancyHistoryDataMessage.hide();

        tenancyHistoryCardAll.click(function () {
            tenancyHistoryType = 0;
            tenancyHistoryTypeSelect[0].value = tenancyHistoryType;
            filterTenancyHistory();
        });

        tenancyHistoryCardResiding.click(function () {
            tenancyHistoryType = 1;
            tenancyHistoryTypeSelect[0].value = tenancyHistoryType;
            filterTenancyHistory();
        });

        tenancyHistoryCardNotResiding.click(function () {
            tenancyHistoryType = 2;
            tenancyHistoryTypeSelect[0].value = tenancyHistoryType;
            filterTenancyHistory();
        });

        tenancyHistoryTypeSelect.change(function () {
            tenancyHistoryType = tenancyHistoryTypeSelect.val();
            filterTenancyHistory();
        });


        filterTenancyHistory = () => {
            if (tenancyHistoryType != 0 || tenancyHistoryType != 0)
                tenancyHistoryClearSort.show();
            else
                tenancyHistoryClearSort.hide();

            tenancyHistoryElements.hide();

            if (tenancyHistoryType == 0)
                tenancyHistoryElements.show();
            else if (tenancyHistoryType == 1)
                tenancyHistoryResidingElements.show();
            else if (tenancyHistoryType == 2)
                tenancyHistoryNotResidingElements.show();

            emptyTenancyHistoryDataMessage.hide();
            if (countVisibleTenancyHistoryRows() == 0)
                emptyTenancyHistoryDataMessage.show();
        }

        countVisibleTenancyHistoryRows = () => {
            var visibleRows = $("#tenancy-history-table tbody tr:visible");
            var visibleRowCount = visibleRows.length;
            return visibleRowCount;
        }

        filterTenancyHistory();

        tenancyHistoryClearSort.click(function () {
            tenancyHistoryType = 0;
            tenancyHistoryTypeSelect[0].value = tenancyHistoryType;
            filterTenancyHistory();
        });
    </script>
</body>

</html>