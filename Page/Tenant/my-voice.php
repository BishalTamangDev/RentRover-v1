<?php
// staring session
if (!session_start())
    session_start();

// including files
include '../../class/functions.php';
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/wishlist_class.php';
include '../../Class/notification_class.php';
include '../../Class/tenant_voice_class.php';

// creating the object
$user = new User();
$room = new Room();
$roomObj = new Room();
$wishlist = new Wishlist();
$notification = new Notification();

$tenantVoice = new TenantVoice();
$selectedTenantVoice = new TenantVoice();

$tenantVoiceResponse = new TenantVoiceResponse();

if (!isset($_SESSION['tenantUserId']))
    header("Location: ../../index.php");
else {
    // setting the values
    $user->userId = $_SESSION['tenantUserId'];
    $user->fetchSpecificRow($_SESSION['tenantUserId']);
}

$voiceId = 0;

if (isset($_GET['voiceId'])) {
    if ($_GET['voiceId'] == null)
        header('location: my-voice.php');
    else {
        $voiceId = $_GET['voiceId'];
        $selectedTenantVoice->tenantVoiceId = $voiceId;

        $selectedTenantVoice->fetchTenantVoice($selectedTenantVoice->tenantVoiceId);
        // fetching responses
        $tenantVoiceResponseSets = $tenantVoiceResponse->fetchAllTenantVoiceResponse($selectedTenantVoice->roomId, $selectedTenantVoice->tenantVoiceId);
    }
}

// getting notification count
$notificationCount = $notification->countNotification("tenant", $user->userId, "unseen");
$wishlistCount = $wishlist->countWishes($_SESSION['tenantUserId']);


// response form submission
if (isset($_POST['response-btn'])) {
    $response = $_POST['response-data'];
    $tenantVoiceResponseDate = date('Y-m-d H:i:s');

    $room->fetchRoom($selectedTenantVoice->roomId);

    $landlordId = $room->getOwnerId($room->houseId);

    $tenantVoiceResponse->whose = "tenant";

    $tenantVoiceResponse->setTenantVoiceResponse($selectedTenantVoice->roomId, $selectedTenantVoice->tenantVoiceId, "tenant", $user->userId, $landlordId, $response, $tenantVoiceResponseDate);
    $immediateTenantVoiceResponseId = $tenantVoiceResponse->registerTenantVoiceResponse();

    if ($immediateTenantVoiceResponseId != 0) {
        // notify tenant
        $roomObj->fetchRoom($selectedTenantVoice->roomId);
        $landlordId = $roomObj->getOwnerId($roomObj->houseId);

        $notification->resetObject();
        $notification->setTenantVoiceResponseNotification("tenant-voice-response", $selectedTenantVoice->roomId, $selectedTenantVoice->tenantVoiceId, $landlordId, $selectedTenantVoice->tenantId);
        $notification->whose = "landlord";
        $notification->register();
    }

    $url = $_SERVER['REQUEST_URI'];

    header("location: $url");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> My Voice </title>

    <!-- css -->
    <link rel="stylesheet" href="../../CSS/Common/style.css">
    <link rel="stylesheet" href="../../CSS/Admin/admin.css">
    <link rel="stylesheet" href="../../CSS/tenant/navbar.css">
    <link rel="stylesheet" href="../../CSS/tenant/tenant.css">
    <link rel="stylesheet" href="../../CSS/Tenant/my-voice-detail.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- script section -->
    <script>
        // prevent resubmission of the form
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <!-- navigation section -->
    <?php include 'navbar.php'; ?>

    <div class="container content-container flex-column my-voice-main-container">
        <div class="div flex-column my-voice-main-div">

            <!-- selected my voice container -->
            <?php if ($voiceId != 0) {
                ?>
                <div class="flex-column selected-my-voice-container">
                    <div class="flex-column selected-my-voice-div">
                        <div class="flex-row selected-my-voice-top">
                            <div class="left negative-bg">
                                <p class="p-form negative-bg">
                                    <?php echo ($selectedTenantVoice->issueState) ? "Solved" : "Unsolved"; ?>
                                </p>
                            </div>

                            <div class="right pointer" id="my-voice-close">
                                <?php $link = "my-voice.php"; ?>
                                <img src="../../Assests/Icons/Cancel-filled.png" alt=""
                                    onclick="window.location.href='<?php echo $link; ?>'">
                            </div>
                        </div>

                        <!-- voice detail -->
                        <div class="flex-column selected-my-voice-div">
                            <p class="selected-my-voice p-normal">
                                <?php echo '"' . ucfirst($selectedTenantVoice->voice) . '"'; ?>
                            </p>
                            <p class="p-form">
                                <?php echo $selectedTenantVoice->date; ?>
                            </p>
                        </div>

                        <!-- redirection section -->
                        <div class="flex-row selected-my-voice-redirection-section">
                            <?php
                            $roomId = $selectedTenantVoice->roomId;
                            $link = "room-details.php?roomId=$roomId";
                            ?>

                            <div class="flex-row selected-my-voice-redirection"
                                onclick="window.location.href='<?php echo $link; ?>'">
                                <img src="../../Assests/Icons/room.png" alt="">
                                <p class="p-form"> See Room Detail </p>
                            </div>
                        </div>
                    </div>

                    <!-- response form section -->
                    <div class="flex-column selected-my-voice-response-container">
                        <p class="p-form n-light">
                            <?php echo sizeof($tenantVoiceResponseSets) . " Responses" ?>
                        </p>
                        <?php
                        if (sizeof($tenantVoiceResponseSets) > 0) {
                            $userObj = new User();
                            foreach ($tenantVoiceResponseSets as $tenantVoiceResponseSet) {

                                if ($tenantVoiceResponseSet['whose'] == 'landlord') {
                                    $landlordId = $tenantVoiceResponseSet['landlord_id'];
                                    $photo = $userObj->getUserPhoto($landlordId);
                                } else {
                                    $tenantId = $tenantVoiceResponseSet['tenant_id'];
                                    $photo = $userObj->getUserPhoto($tenantId);
                                }

                                ?>
                                <div class="flex-row selected-my-voice-response-div">
                                    <div class="flex-row left">
                                        <div class="flex-row profile-div">
                                            <img src="../../Assests/Uploads/User/<?php echo $photo; ?>" alt="" class="icon-class">
                                        </div>
                                    </div>

                                    <!-- response detail -->
                                    <div class="middle">
                                        <div class="top">
                                            <p class="p-normal">
                                                <?php echo ucfirst($tenantVoiceResponseSet['response']); ?>
                                            </p>

                                            <p class="p-small">
                                                <?php echo $tenantVoiceResponseSet['response_date']; ?>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- operation section -->
                                    <?php
                                    $responseId = $tenantVoiceResponseSet['tenant_voice_id'];
                                    $task = "delete";
                                    $url = $_SERVER['REQUEST_URI'];
                                    $link = "operation/tvr-operation.php?id=$responseId&task=$task&url=$url";
                                    ?>
                                    <div
                                        class="right <?php if (ucfirst($tenantVoiceResponseSet['whose']) != $user->role)
                                            echo "hidden"; ?>">
                                        <img src="../../Assests/Icons/delete.png" alt="" class="pointer"
                                            onclick="window.location.href='<?php echo $link; ?>'">
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>

                    <!-- responding form section -->
                    <div class="flex-row response-form-container">
                        <!-- photo section -->
                        <?php $photo = $user->getUserPhoto($user->userId); ?>
                        <div class="left">
                            <div class="photo-container">
                                <img src="../../Assests/Uploads/User/<?php echo $photo; ?>" alt="">
                            </div>
                        </div>

                        <!-- form section -->
                        <form action="" method="POST" class="flex-column response-form">
                            <textarea name="response-data" required></textarea>
                            <button name="response-btn"> Reply </button>
                        </form>
                    </div>
                </div>
                <?php
            }
            ?>


            <p class="p-large f-bold n-light"> <?php echo ($voiceId == 0)?"My Voice":"Other Voices" ;?> </p>

            <!-- my voice cards -->
            <div class="flex-row card-container my-voice-card-section">
                <div class="flex-column pointer card my-voice-card" id="my-voice-card-all">
                    <p class="p-normal"> All </p>
                </div>

                <div class="flex-column pointer card my-voice-card" id="my-voice-card-pending">
                    <p class="p-normal"> Unsolved </p>
                </div>

                <div class="flex-column pointer card my-voice-card" id="my-voice-card-solved">
                    <p class="p-normal"> Solved </p>
                </div>
            </div>

            <!-- voices -->
            <div class="my-voice-container">
                <?php
                    $myTenantVoiceSets = $tenantVoice->fetchMyTenantVoice($user->userId);
                    if (sizeof($myTenantVoiceSets) > 0) {
                        foreach ($myTenantVoiceSets as $myTenantVoiceSet) {
                            $voiceId = $myTenantVoiceSet['tenant_voice_id'];
                            $link = "my-voice.php?voiceId=$voiceId";
                            ?>
                            <div class="my-voice-div shadow flex-column my-voice-element <?php echo ($myTenantVoiceSet['issue_state'] == 0) ? 'my-voice-unsolved-element' : 'my-voice-solved-element'; ?>">
                                <!-- top section -->
                                <div class="top positive-bg">
                                    <p class="p-normal">
                                        <?php echo ($myTenantVoiceSet['issue_state'] == 0) ? "Unsolved" : "Solved"; ?>
                                    </p>
                                </div>

                                <!-- voice detail -->
                                <div class="flex-column my-voice">
                                    <p class="p-normal">
                                        <?php echo ucfirst($myTenantVoiceSet['voice']); ?>
                                    </p>

                                    <p class="p-form">
                                        <?php echo ($myTenantVoiceSet['issue_state'] == 0) ? " - " : $myTenantVoiceSet['issue_solved_date']; ?>
                                    </p>
                                </div>

                                <!-- redirection section -->
                                <div class="flex-row my-voice-redirection-section">
                                    <?php
                                    $id = $myTenantVoiceSet['room_id'];
                                    $link = "room-details.php?roomId=$id"; 
                                    ?>
                                    <div class="flex-row my-voice-redirection" onclick="window.location.href='<?php echo $link; ?>'">
                                        <img src="../../Assests/Icons/room.png" alt="">
                                        <p class="p-form"> See Room Detail </p>
                                    </div>

                                    <?php
                                    $id = $myTenantVoiceSet['tenant_voice_id'];
                                    $link = "my-voice.php?voiceId=$id";
                                    ?>
                                    <div class="flex-row my-voice-redirection" onclick="window.location.href='<?php echo $link; ?>'">
                                        <img src="../../Assests/Icons/redirect.png" alt="">
                                        <p class="p-form"> Show More </p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                
                ?>
            </div>

            <!-- empty context -->
            <div class="container empty-data-container" id="empty-data-container">
                <div class="flex-column div empty-data-div" id="empty-data-div">
                    <img src="../../Assests/Icons/empty.png" alt="">
                    <p class="p-normal negative"> Empty! </p>
                </div>
            </div>
        </div>
    </div>

    <!-- js section -->
    <script>
        var userMenuState = false;
        var notificationMenuState = false;

        const userMenu = document.getElementById('menu-container');
        const notificationMenu = document.getElementById('notification-container');
        const logoutDialog = document.getElementById('logout-dialog-container');

        onload = () => {
            userMenu.style = "display:none";
            notificationMenu.style = "display:none";
        }

        showReviewForm = () => {
            console.log("Hello!");
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

        logout = () => {
            logoutDialog.style = "display:flex";
        }

        hideLogoutDialog = () => {
            logoutDialog.style = "display:none";
            userMenuState = false;
            userMenu.style = "display:none";
        }
    </script>

    <script>
        // cards
        const myVoiceAllCard = $('#my-voice-card-all');
        const myVoiceUnolvedCard = $('#my-voice-card-unsolved');
        const myVoiceSolvedCard = $('#my-voice-card-solved');

        const emptyDataContainer = $('#empty-data-container');

        var myVoiceElement = $('.my-voice-element');
        var myVoiceUnsolvedElement = $('.my-voice-unsolved-element');
        var myVoiceSolvedElement = $('.my-voice-solved-element');

        emptyDataContainer.hide();
        emptyDataContainer.show();

        var myVoiceType = 0;

        myVoiceAllCard.click(function () {
            myVoiceType = 0;
            filterMyVoice();
        });

        myVoiceUnolvedCard.click(function () {
            myVoiceType = 1;
            filterMyVoice();
        });

        myVoiceSolvedCard.click(function () {
            myVoiceType = 2;
            filterMyVoice();
        });

        filterMyVoice = () => {
            myVoiceElement.hide();

            if (myVoiceType == 0)
                myVoiceElement.show();
            else if (myVoiceType == 1)
                myVoiceUnsolvedElement.show();
            else
                myVoiceSolvedElement.show();

            if (countVisible() == 0)
                emptyDataContainer.show();
            else
                emptyDataContainer.hide();
        }

        countVisible = () => {
            var count = $('.my-voice-element:visible').length;
            return count;
        }

        filterMyVoice();
    </script>
</body>

</html>