<?php
// staring session
if (!session_start())
    session_start();

// getting values from the url
if(!isset($_GET['roomId']) || !isset($_GET['tenantId'])){
    header("location: tenants.php");
}else{
    $roomId = $_GET['roomId'];
    $tenantId = $_GET['tenantId'];
    if($roomId == null || $tenantId == null)
        header("location: tenants.php");
}

// including files
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../class/functions.php';
include '../../class/tenancy_history_class.php';

// creating the object
$user = new User();
$tenant = new User();
$room = new Room();
$house = new House();
$tenancyHistory = new TenancyHistory();

if (!isset($_SESSION['landlordUserId']))
    header("Location: login.php");
else {
    $user->userId = $_SESSION['landlordUserId'];
    $user->fetchSpecificRow($user->userId);
}

$tenancyHistorySets = [];

// for tenant detail
if (isset($_GET['tenantId'])) {
    $tenant->userId = $tenantId;
    $tenant->fetchUser($tenant->userId);
} else {
    header("location: tenants.php");
}
$tenancyHistorySets = $tenancyHistory->fetchTenancyHistory($tenantId, $roomId);

$roomIdArray = [];
$roomIdArray = getRoomIdArray($user->userId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title> Tenant Detail </title>

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/Common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/Admin/user-detail.css">
    <link rel="stylesheet" href="../../CSS/Common/lightbox.min.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- script section -->
    <script src="../../Js/main.js"> </script>
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
    <script src="../../Js/lightbox-plus-jquery.min.js"></script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="body-container flex-row">
        <!-- empty section -->
        <aside class="empty-section"> </aside>

        <article class="flex-row content-article">
            <div class="flex-row user-detail-div">
                <!-- photo & username -->
                <div class="photo-container">
                    <div class="photo-div">
                        <img src="../../Assests/Uploads/user/<?php echo $tenant->getUserPhoto($tenant->userId); ?>" alt="">
                    </div>
                </div>

                <div class="flex-column user-detail-section">
                    <!-- full name -->
                    <div class="name-container flex-row">
                        <div class="left flex-row">
                            <p class="p-normal"> Name </p>
                        </div>

                        <div class="right flex-row">
                            <p class="p-normal">
                                <?php echo $tenant->getUserName($tenant->userId); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="flex-row">
                        <div class="left flex-row">
                            <p class="p-normal"> Gender </p>
                        </div>

                        <div class="right flex-row">
                            <p class="p-normal">
                                <?php echo ucfirst($tenant->gender); ?>
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
                                <?php echo $tenant->dob; ?>
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
                                <?php echo $tenant->getUserAddress($tenant->userId); ?>
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
                                <?php echo $tenant->contact; ?>
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
                                <?php echo $tenant->citizenshipNumber; ?>
                            </p>
                        </div>
                    </div>

                    <!-- move in and move out date date -->
                    <?php
                    
                    foreach($tenancyHistorySets as $tenancyHistorySet){
                        ?>
                        <div class="register-container flex-row">
                            <div class="left flex-row">
                                <p class="p-normal"> Tenancy History </p>
                            </div>
    
                            <div class="register-div flex-row right">
                                <p class="p-normal"> <?php echo '['.$tenancyHistorySet['move_in_date'].'] - ['.$tenancyHistorySet['move_out_date'].']'; ?> </p>
                            </div>
                        </div>
                        <?php
                    }
                    
                    ?>


                    <!-- tenancy state -->
                    <div class="account-state-container flex-row">
                        <div class="left flex-row">
                            <p class="p-normal"> State </p>
                        </div>
                        
                        <div class="account-state-div flex-row">
                            <p class="p-normal">
                                <?php echo ($room->isTenant($roomIdArray, $tenant->userId))?"Current Tenant":"Ex-Tenant"; ?>
                            </p>

                        </div>
                    </div>

                    <div class="flex-column operation-div">
                        <div class="left">
                            <?php 
                            $url = $_SERVER['REQUEST_URI'];
                            $link = "../operation/room-operation.php?task=remove-tenant&roomId=$roomId&url=$url"; ?>
                            <button class="negative-button <?php if(!$room->isTenant($roomIdArray, $tenant->userId)) echo "hidden"; ?>" hidden> Remove Tenant </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-column user-detail-section-2">
                <!-- front side -->
                <a href="../../Assests/Uploads/Citizenship/<?php echo $tenant->citizenshipFrontPhoto; ?>"
                    data-lightbox="citizenship-photo" class="flex-row">
                    <img src="../../Assests/Images/image.png" alt="" class="icon-class">
                    <p class="p-normal"> Citizenship Front Photo </p>
                </a>

                <!-- back side -->
                <a href="../../Assests/Uploads/Citizenship/<?php echo $tenant->citizenshipBackPhoto; ?>"
                    data-lightbox="citizenship-photo" class="flex-row">
                    <img src="../../Assests/Images/image.png" alt="" class="icon-class">
                    <p class="p-normal"> Citizenship Back Photo </p>
                </a>
            </div>
        </article>
    </div>

    <!-- js section -->
    <script>
        const activeMenu = $('#tenant-menu-id');
        activeMenu.css({
            "background-color" : "#DFDFDF"
        });
    </script>
</body>

</html>