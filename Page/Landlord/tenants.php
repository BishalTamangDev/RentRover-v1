<?php
// staring session
if (!session_start())
    session_start();

// including files
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../class/functions.php';
include '../../Class/tenancy_history_class.php';

// creating the object
$room = new Room();
$user = new User();
$tenant = new User();
$house = new House();
$tenancyHistory = new TenancyHistory();

$user->userId = $_SESSION['landlordUserId'];

if (!isset($_SESSION['landlordUserId']))
    header("Location: login.php");
else
    $user->fetchSpecificRow($_SESSION['landlordUserId']);

// get landlord's room id -> roomIdArray
$roomIdArray = getRoomIdArray($user->userId);
$tenantIdArray = $tenancyHistory->fetchTenancyHistoryForLandlord($roomIdArray);

// on search
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
    <title> Tenants </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <!-- jquery -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="body-container flex-row">
        <!-- empty section -->
        <aside class="empty-section"> </aside>

        <article class="flex-column content-article">
            <!-- heading -->
            <div class="section-heading-container content-container">
                <p class="f-bold negative"> Tenants </p>
            </div>

            <!-- myhouse cards -->
            <div class="content-container flex-row card-container">
                <!-- total house card -->
                <div class="card flex-row pointer" id="all-tenants-filter-card">
                    <p class="p-normal"> All Tenants </p>
                    <p class="p-normal f-bold counter" id="all-tenant-counter">
                        
                    </p>
                </div>

                <!-- approved house card -->
                <div class="card flex-row pointer" id="current-tenant-filter-card">
                    <p class="p-normal"> Current Tenants </p>
                    <p class="p-normal f-bold counter" id="current-tenant-counter">
                        <?php echo ""; ?>
                    </p>
                </div>

                <!-- unapproved house card -->
                <div class="card flex-row pointer" id="former-tenant-filter-card">
                    <p class="p-normal"> Former Tenants </p>
                    <p class="p-normal f-bold counter" id="former-tenant-counter">
                        <?php echo ""; ?>
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
                        <label for="tenant-type-select"> Type </label>
                        <select name="tenant-type-select" id="tenant-type-select">
                            <option value="0"> All </option>
                            <option value="1"> Current Tenants </option>
                            <option value="2"> Former Tenants </option>
                        </select>
                    </div>

                    <div class="flex-row pointer clear-filter-div" id="clear-sort">
                        <p class="p-form"> Clear Sort </p>
                        <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                    </div>
                </div>

                <div class="flex-row search-div hidden">
                    <?php
                    if ($search) {
                        ?>
                        <div class="clear-search-div flex-row pointer" onclick="window.location.href='tenants.php'">
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
                            <p class="p-normal f-bold" id="tenant-table-heading">
                                <?php echo (!$search) ? 'Tenants' : 'Search Results '; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- table section -->
                <?php $sets = ($search) ? $house->searchHouse($_GET['content']) : $house->fetchAllHouses($user->userId); ?>

                <table class="my-house-table table-class" id="tenant-table">
                    <thead>
                        <th class="t-first-td first-td"> S.N. </th>
                        <th class="t-tenant-name"> Tenant Name </th>
                        <th class="t-room-number"> Room ID </th>
                        <th class="t-address"> Address </th>
                        <th class="t-house-id"> Contact </th>
                        <th class="t-move-in-date"> Move In Date </th>
                        <th class="t-move-out-date"> Move Out Date </th>
                    </thead>

                    <tbody>
                        <?php
                        if (sizeof($tenantIdArray) > 0) {
                            $serial = 1;
                            foreach ($tenantIdArray as $tenantId) {
                                $tenant->userId = $tenantId['tenant_id'];
                                $tenant->fetchUser($tenantId['tenant_id']);

                                // getting room & house detail
                                $room->fetchRoom($tenantId['room_id']);

                                $roomId = $tenantId['room_id'];

                                $link = "tenants-detail.php?tenantId=$tenant->userId&roomId=$roomId";
                                ?>
                                <tr onclick="window.location.href='<?php echo $link; ?>'"
                                    class="tenant-element <?php echo ($tenantId['move_out_date'] == "0000-00-00") ? "current-tenant-element":"former-tenant-element"; ?>">
                                    <td class="t-serial first-td">
                                        <?php echo $serial++; ?>
                                    </td>
                                    <td class="t-tenant-name">
                                        <?php echo $tenant->getUserName($tenant->userId); ?>
                                    </td>
                                    <td class="t-room-id">
                                        <?php echo $tenantId['room_id']; ?>
                                    </td>
                                    <td class="t-room-id">
                                        <?php echo $tenant->getUserAddress($tenant->userId); ?>
                                    </td>
                                    <td class="t-contact">
                                        <?php echo $tenant->contact; ?>
                                    </td>
                                    <td class="t-move-in-date">
                                        <?php echo $tenantId['move_in_date']; ?>
                                    </td>
                                    <td class="t-move-out-date">
                                        <?php echo ($tenantId['move_out_date'] == "0000-00-00") ? "Still residing" : $tenantId['move_out_date']; ?>
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
    <script>
        const activeMenu = $('#tenant-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
        });
    </script>

    <!-- custom script section -->
    <script>
        $(document).ready(function () {
            const tenantElement = $('.tenant-element');
            const currentTenantElement = $('.current-tenant-element');
            const formerTenantElement = $('.former-tenant-element');

            const clearSortDiv = $('#clear-sort');

            // values
            var tenantState = 0;

            // hiding filter
            clearSortDiv.hide();

            // individual sorting task
            $('#all-tenants-filter-card').click(function () {
                tenantState = 0;
                $('#tenant-type-select')[0].value = tenantState;
                filterElement();
            });

            $('#current-tenant-filter-card').click(function () {
                tenantState = 1;
                $('#tenant-type-select')[0].value = tenantState;
                filterElement();
            });

            $('#former-tenant-filter-card').click(function () {
                tenantState = 2;
                $('#tenant-type-select')[0].value = tenantState;
                filterElement();
            });

            // count tenant
            tenantCounter = () =>{
                allTenantCounter = $('#all-tenant-counter')[0]; 
                currentTenantCounter = $('#current-tenant-counter')[0]; 
                formerTenantCounter = $('#former-tenant-counter')[0]; 

                currentTenantCount = $('.current-tenant-element').length;
                formerTenantCount = $('.former-tenant-element').length;
                allTenantCount = $('.tenant-element').length;

                allTenantCounter.innerText = ": " + allTenantCount;
                currentTenantCounter.innerText = ": " + currentTenantCount;
                formerTenantCounter.innerText = ": " + formerTenantCount;
            }
            
            filterElement = () => {
                tenantElement.hide();


                var elementCount = 0;

                // house tenantState
                if (tenantState == 0) {
                    tenantElement.show();
                    $('#tenant-table-heading')[0].innerText = "All Tenants";
                } else if (tenantState == 1) {
                    currentTenantElement.show();
                    $('#tenant-table-heading')[0].innerText = "Current Tenants";
                } else if (tenantState == 2) {
                    formerTenantElement.show();
                    $('#tenant-table-heading')[0].innerText = "Former Tenants";
                }


                if ($("#tenant-table tbody tr:visible").length == 0)
                    $("#empty-data-div").show();
                else
                    $("#empty-data-div").hide();

                $('#clear-sort').show();

                if (tenantState == 0)
                    $('#clear-sort').hide();
            }

            $('#tenant-type-select').change(function () {
                tenantState = $('#tenant-type-select')[0].value;
                filterElement();
            });

            $('#clear-sort').click(function () {
                tenantState = 0;
                filterElement();
                $('#tenant-type-select')[0].value = 0;
                $('#clear-sort').hide();
            });

            filterElement();

            tenantCounter();
        });
    </script>
</body>

</html>