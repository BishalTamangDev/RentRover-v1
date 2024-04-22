<?php

// session
if (!session_start())
    session_start();

// redirecting to login page is session variable is not set
if (!isset($_SESSION['adminId']))
    header("Location: login.php");

// including external files
include '../../class/user_class.php';
include '../../class/house_class.php';
include '../../class/tenancy_history_class.php';
include '../../class/functions.php';

// creating objects
$user = new User();
$roomObj = new Room();
$houseObj = new House();
$tenancyHistory = new TenancyHistory();

$url = $_SERVER['REQUEST_URI'];

if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];

    // url tampering check
    if (!$user->isValidUser($userId))
        header("location: users.php");

    $user->fetchSpecificRow($userId);
    $user->setKeyValue("id", $userId);
} else {
    header("Location: users.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> User Detail </title>

    <!-- main css import -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/common/table.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/user-detail.css">
    <link rel="stylesheet" href="../../CSS/Common/lightbox.min.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- script section -->
    <script src="../../Js/lightbox-plus-jquery.min.js"> </script>

    <!-- jquery -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="body-container flex-row">
        <aside class="empty-section"> </aside>

        <article class="flex-column content-article">
            <div class="flex-row user-detail-div">
                <!-- photo & username -->
                <div class="photo-container">
                    <div class="photo-div">
                        <img src="../../Assests/Uploads/user/<?php echo $user->userPhoto; ?>" alt="">
                    </div>
                </div>

                <div class="flex-column user-detail-section">
                    <!-- Role -->
                    <div class="role-div flex-row">
                        <div class="left flex-row">
                            <p class="p-normal"> Role </p>
                        </div>

                        <div class="right flex-row">
                            <p class="p-normal">
                                <?php echo ucfirst($user->role); ?>
                            </p>
                        </div>
                    </div>

                    <!-- full name -->
                    <div class="name-container flex-row">
                        <div class="left flex-row">
                            <p class="p-normal"> Name </p>
                        </div>

                        <div class="right flex-row">
                            <?php echo returnFormattedName($user->firstName, $user->middleName, $user->lastName); ?>
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="flex-row">
                        <div class="left flex-row">
                            <p class="p-normal"> Gender </p>
                        </div>

                        <div class="right flex-row">
                            <p class="p-normal">
                                <?php echo ucfirst($user->gender); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Date of Birth -->
                    <div class="flex-row">
                        <div class="left flex-row">
                            <p class="p-normal"> Date of Birth </p>
                        </div>

                        <div class="right flex-row">
                            <p class="p-normal">
                                <?php echo $user->dob; ?>
                            </p>
                        </div>
                    </div>

                    <!-- address -->
                    <div class="address-container flex-row">
                        <div class="left flex-row">
                            <p class="p-normal"> Address </p>
                        </div>

                        <div class="right flex-row">
                            <p class="p-normal">
                                <?php echo returnFormattedAddress($user->province, $user->district, $user->areaName, $user->wardNumber); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Email address -->
                    <div class="flex-row">
                        <div class="left flex-row">
                            <p class="p-normal"> Email Address </p>
                        </div>

                        <div class="right flex-row">
                            <p class="p-normal">
                                <?php echo $user->email; ?>
                            </p>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="contact-div flex-row">
                        <div class="left flex-row">
                            <p class="p-normal"> Contact </p>
                        </div>

                        <div class="right flex-row">
                            <p class="p-normal">
                                <?php echo $user->contact; ?>
                            </p>
                        </div>
                    </div>

                    <!-- citizenship detail -->
                    <div class="citizenship-div flex-row">
                        <div class="left flex-row">
                            <p class="p-normal"> Citizenship Number </p>
                        </div>

                        <div class="right flex-row">
                            <p class="p-normal">
                                <?php echo $user->citizenshipNumber; ?>
                            </p>
                        </div>
                    </div>

                    <!-- register date -->
                    <div class="register-container flex-row">
                        <div class="left flex-row">
                            <p class="p-normal"> Register Date </p>
                        </div>

                        <div class="register-div flex-row right">
                            <p class="p-normal">
                                <?php echo $user->registerDate; ?>
                            </p>
                        </div>
                    </div>

                    <!-- Account state -->
                    <div class="account-state-container flex-row">
                        <div class="left flex-row">
                            <p class="p-normal"> Account State </p>
                        </div>

                        <div class="account-state-div flex-row">
                            <p class="p-normal">
                                <?php
                                if ($user->accountState == 0)
                                    echo "Verification pending";
                                else if ($user->accountState == 1)
                                    echo "Verified";
                                else
                                    echo "Suspended";
                                ?>
                            </p>
                        </div>
                    </div>

                    <!-- operation div -->
                    <div class="button-div flex-row">
                        <?php
                        $userId = $_GET['userId'];
                        if ($user->accountState == 0) {
                            ?>
                            <a
                                href="operation/user-op.php?task=verify&userId=<?php echo $userId . "&url=$url"; ?>">
                                <button class="positive-button"> Verify User </button>
                            </a>
                            <?php
                        } else if ($user->accountState == 1) {
                            ?>
                                <a
                                    href="operation/user-op.php?task=suspend&userId=<?php echo $userId . "&url=$url"; ?>">
                                    <button class="negative-button notification-button"> Suspend User </button>
                                </a>
                            <?php
                        } else {
                            ?>
                                <a
                                    href="operation/user-op.php?task=verify&userId=<?php echo $userId . "&url=$url"; ?>">
                                    <button class="warning-button notification-button"> Re-verify User </button>
                                </a>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <div class="flex-column user-detail-section-2">
                    <!-- front side -->
                    <a href="../../Assests/Uploads/Citizenship/<?php echo $user->citizenshipFrontPhoto; ?>"
                        data-lightbox="citizenship-photo" class="flex-row">
                        <img src="../../Assests/Images/image.png" alt="" class="icon-class">
                        <p class="p-normal"> Citizenship Front Photo </p>
                    </a>

                    <!-- back side -->
                    <a href="../../Assests/Uploads/Citizenship/<?php echo $user->citizenshipBackPhoto; ?>"
                        data-lightbox="citizenship-photo" class="flex-row">
                        <img src="../../Assests/Images/image.png" alt="" class="icon-class">
                        <p class="p-normal"> Citizenship Back Photo </p>
                    </a>
                </div>
            </div>

            <!-- if tenant: show residing room detail -->
            <?php
            if ($user->role == "Tenant") {
                ?>
                <div class="residing-room-detail-container  flex-column">
                    <div class="panel-heading-container">
                        <p class="heading f-bold negative"> User's Residing Room Detail </p>
                    </div>

                    <!-- user tenancy history table -->
                    <table class="table-class residing-room-table">
                        <thead>
                            <th class="t- first-td"> S.N. </th>
                            <th class="t-"> Room </th>
                            <th class="t-"> Move in date </th>
                            <th class="t-"> Room out date </th>
                        </thead>

                        <?php
                        $tenantId = $_GET['userId'];
                        $tenancyHistorySets = $tenancyHistory->fetchTenancyHistoryOfTenant($tenantId);
                        if (sizeof($tenancyHistorySets) > 0) {
                            ?>
                            <tbody>
                                <?php
                                $serial = 1;
                                foreach ($tenancyHistorySets as $tenancyHistorySet) {
                                    ?>
                                    <tr>
                                        <td class="t- first-td">
                                            <?php echo $serial++; ?>
                                        </td>
                                        <td class="t-">
                                            <?php echo $tenancyHistorySet['room_id']; ?>
                                        </td>
                                        <td class="t-">
                                            <?php echo $tenancyHistorySet['move_in_date']; ?>
                                        </td>
                                        <td class="t-">
                                            <?php echo ($tenancyHistorySet['move_out_date'] != NULL) ? $tenancyHistorySet['move_out_date'] : "Still residing"; ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                            <?php
                        } else {
                            ?>
                            <tbody>
                                <tr>
                                    <td colspan="4" style="text-align:center;"> This tenant has not been registered in any of
                                        the room yet. </td>
                                </tr>
                            </tbody>
                            <?php
                        }
                        ?>
                    </table>
                </div>
                <?php
            }
            ?>

            <!-- if landlord: show house details detail -->
            <?php
            if ($user->role == "Landlord") {
                ?>
                <div class="landlord-detail-container flex-column">
                    <div class="landlord-houses table-container">
                        <div class="panel-heading-container">
                            <p class="heading f-bold negative"> User's Houses </p>
                        </div>

                        <div class="house-detail flex-column">
                            <table class="house-detail-table table-class">
                                <thead>
                                    <tr>
                                        <th class="t-id first-td"> S.N. </th>
                                        <th class="t-house-id"> House ID </th>
                                        <th class="t-location"> Location </th>
                                        <th class="t-number-of-room"> No. of rooms </th>
                                    </tr>
                                </thead>

                                <?php
                                $landlordId = $_GET['userId'];
                                $houseSets = $houseObj->fetchAllHouses($landlordId);
                                if (sizeof($houseSets) > 0) {
                                    ?>
                                    <tbody>
                                        <?php
                                        $serial = 1;
                                        foreach ($houseSets as $houseSet) {
                                            $houseId = $houseSet['house_id'];
                                            $houseLink = "house-detail.php?houseId=" . $houseId;
                                            ?>
                                            <tr onclick="window.location.href='<?php echo $houseLink; ?>'">
                                                <td class="t-id first-td">
                                                    <?php echo $serial++; ?>
                                                </td>
                                                <td class="t-house-id">
                                                    <?php echo $houseSet['house_id']; ?>
                                                </td>
                                                <td class="t-location">
                                                    <?php echo $houseObj->getLocation($houseSet['house_id']); ?>
                                                </td>
                                                <td class="t-number-of-room">
                                                    <?php echo $roomObj->countRoomOfThisHouse($houseSet['house_id']); ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                    <?php
                                } else {
                                    ?>
                                    <tbody>
                                        <tr>
                                            <td colspan="4" style="text-align:center;" class="negative"> This user has not registered any houses. </td>
                                        </tr>
                                    </tbody>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>

                    <!-- room detail : container -->
                    <div class="landlord-houses table-container">
                        <div class="panel-heading-container">
                            <p class="heading f-bold negative"> User's Rooms </p>
                        </div>

                        <div class="room-detail flex-column">
                            <table class="room-detail-table table-class">
                                <thead>
                                    <tr>
                                        <th class="t-id first-td"> S.N. </th>
                                        <th class="t-room-id"> Room ID </th>
                                        <th class="t-type"> Type </th>
                                        <th class="t-location"> Location </th>
                                        <th class="t-specs"> Specs </th>
                                        <th class="t-rent"> Rent </th>
                                        <th class="t-room-state"> Acquired </th>
                                        <th class="t-tenant"> Tenant </th>
                                        <th class="t-state"> Verification </th>
                                    </tr>
                                </thead>

                                <?php
                                $roomSets = $roomObj->fetchAllRoom($landlordId);
                                if(sizeof($roomSets) > 0){
                                    ?>
                                    <tbody>
                                    <?php
                                    $serial = 1;
                                    foreach ($roomSets as $roomSet) {
                                        $roomId = $roomSet['room_id'];
                                        $roomLink = "room-detail.php?roomId=$roomId";
                                        ?>
                                        <tr class="" onclick="window.location.href='<?php echo $roomLink; ?>'">
                                            <td class="t-id first-td">
                                                <?php echo $serial++; ?>
                                            </td>

                                            <td class="t-room-id">
                                                <?php echo $roomSet['room_id']; ?>
                                            </td>

                                            <td class="t-type">
                                                <?php echo ($roomSet['room_type'] == 1) ? "BHK, " : "Non-BHK, "; ?>
                                                <?php if ($roomSet['furnishing'] == 0)
                                                    echo "Unfurnished";
                                                else if ($roomSet['furnishing'] == 1)
                                                    echo "Semi-Furnished";
                                                else
                                                    echo "Fully-Furnished"; ?>
                                            </td>

                                            <td class="t-location">
                                                <?php echo ucfirst($houseObj->getLocation($roomSet['house_id'])); ?>
                                            </td>

                                            <td class="t-specs">
                                                <?php echo $roomSet['bhk'] . " BHK, "; ?>
                                                <?php echo $roomSet['floor'] . " Floor, "; ?>
                                            </td>

                                            <td class="t-rent">
                                                <?php echo returnFormattedPrice($roomSet['rent_amount']); ?>
                                            </td>

                                            <td class="t-acquired">
                                                <?php echo ($roomSet['is_acquired'] == 0) ? "Unacquired" : "Acquired"; ?>
                                            </td>

                                            <td class="t-tenant">
                                                <?php echo ($roomSet['tenant_id'] != 0) ? $roomSet['tenant_id'] : "-"; ?>
                                            </td>

                                            <td class="t-room-state">
                                                <?php
                                                if ($roomSet['room_state'] == 0)
                                                    echo "Unverified";
                                                elseif ($roomSet['room_state'] == 1)
                                                    echo "Verified";
                                                else
                                                    echo "Suspended";
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                                    <?php
                                }else{
                                    ?>
                                    <tbody>
                                        <tr>
                                            <td colspan="9" class="negative" style="text-align:center;"> This user has not registered any room. </td>
                                        </tr>
                                    </tbody>
                                    <?php
                                }
                                ?>
                                
                            </table>
                        </div>
                    </div>
                </div>

                <?php
            }
            ?>
        </article>
    </div>

    <!-- script -->
    <script>
        const activeMenu = $('#user-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
        });
    </script>
</body>

</html>