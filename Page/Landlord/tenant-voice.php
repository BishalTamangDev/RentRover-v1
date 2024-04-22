<?php
// staring session
if (!session_start())
    session_start();

// including files
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/notification_class.php';
include '../../Class/tenant_voice_class.php';
include '../../class/functions.php';

// creating the object
$user = new User();
$houseObj = new House();
$roomObj = new Room();
$notification = new Notification();
$tenantVoice = new TenantVoice();
$selectedTenantVoice = new TenantVoice();
$tenantVoiceResponse = new TenantVoiceResponse();

$user->userId = $_SESSION['landlordUserId'];

if (!isset($_SESSION['landlordUserId'])) {
    // divert to the login page
    header("Location: landlord-login.php");
} else
    $user->fetchUser($_SESSION['landlordUserId']);

$myRoomIdArray = [];

$tenantVoiceSets = [];

$myRoomIdArray = getRoomIdArray($user->userId);

// fetch tenant voices
if (sizeof($myRoomIdArray) > 0)
    $tenantVoiceSets = $tenantVoice->fetchTenantVoiceForLandlord($myRoomIdArray);

$myVoiceArray = [];

// checking if the selected voic is in the array
if (sizeof($tenantVoiceSets) > 0) {
    foreach ($tenantVoiceSets as $temp)
        $myVoiceArray[] = $temp['tenant_voice_id'];
}

// selected tenant voice
if (isset($_GET['voiceId'])) {
    $selectedTenantVoice->tenantVoiceId = $_GET['voiceId'];
    // url tampering check
    if (in_array($selectedTenantVoice->tenantVoiceId, $myVoiceArray))
        $selectedTenantVoice->fetchTenantVoice($selectedTenantVoice->tenantVoiceId);
    else
        header('location: tenant-voice.php');
} else {
    $selectedTenantVoice->tenantVoiceId = 0;
}

// solve issue btn
if (isset($_POST['tenant-voice-solve-btn'])) {
    // just solve 
    if ($tenantVoice->solveTenantVoice($selectedTenantVoice->tenantVoiceId)) {
        // notify tenant
        $notification->resetObject();
        $issueSolvedDate = date('Y-m-d H:i:s');
        $notification->setTenantVoiceNotification("tenant-voice-solved", $selectedTenantVoice->roomId, $selectedTenantVoice->tenantVoiceId, $user->userId, $selectedTenantVoice->tenantId);
        $notification->whose = "tenant";
        $notification->register();
    }

    $url = $_SERVER['REQUEST_URI'];
    header("location: $url");

}

// reply only
if (isset($_POST['tenant-voice-response-btn'])) {
    $tenantVoiceResponseResponse = $_POST['tenant-voice-response'];
    $tenantVoiceResponseDate = date('Y-m-d H:i:s');

    $tenantVoiceResponse->setTenantVoiceResponse($selectedTenantVoice->roomId, $selectedTenantVoice->tenantVoiceId, "landlord", $selectedTenantVoice->tenantId, $user->userId, $tenantVoiceResponseResponse, $tenantVoiceResponseDate);
    $immediateTenantVoiceResponseId = $tenantVoiceResponse->registerTenantVoiceResponse();

    if ($immediateTenantVoiceResponseId != 0) {
        // notify tenant
        $notification->resetObject();
        $notification->setTenantVoiceResponseNotification("tenant-voice-response", $selectedTenantVoice->roomId, $selectedTenantVoice->tenantVoiceId, $user->userId, $selectedTenantVoice->tenantId);
        $notification->whose = "tenant";
        $notification->register();
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
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/user-voice.css">

    <!-- title -->
    <title> Tenant Voice </title>

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">
</head>

<body>
    <?php
    include 'aside.php';
    ?>

    <div class="body-container flex-row">
        <aside class="empty-section"> </aside>

        <article class="content-article">
            <!-- selected tenant voice -->
            <?php
            if ($selectedTenantVoice->tenantVoiceId != 0 && in_array($selectedTenantVoice->tenantVoiceId, $myVoiceArray)) {
                $tenantName = $user->getUserName($selectedTenantVoice->tenantId);
                $tenantPhoto = $user->getUserPhoto($selectedTenantVoice->tenantId);
                $issueState = ($selectedTenantVoice->issueState == 0) ? "Unsolved" : "Solved";
                $voice = '"' . ucfirst($selectedTenantVoice->voice) . '"';
                $date = $selectedTenantVoice->date;
                $tenantState = ($roomObj->getTenantState($selectedTenantVoice->roomId, $selectedTenantVoice->tenantId)) ? "Current Tenant" : "Ex-Tenant";
                ?>
                <div class="user-voice-div flex-column selected-voice-container">
                    <div class="user-voice shadow flex-column">
                        <div class="user-detail-div flex-row">
                            <div class="image-div">
                                <img src="../../Assests/Uploads/user/<?php echo $tenantPhoto; ?>" alt="">
                            </div>

                            <div class="username-div flex-column">
                                <p class="p-form">
                                    <?php echo $tenantName; ?>
                                </p>
                                <p class="p-form n-light">
                                    <?php echo $tenantState; ?>
                                </p>
                            </div>

                            <div class="close-div" onclick="window.location.href='tenant-voice.php'">
                                <abbr title="Close">
                                    <img src="../../Assests/Icons/close.png" class="pointer icon-class" alt="">
                                </abbr>
                            </div>
                        </div>

                        <div class="user-voice-box">
                            <p class="p-normal problem">
                                <?php echo $voice; ?>
                            </p>

                            <p class="p-small">
                                <?php echo $date; ?>
                            </p>

                            <p class="p-small negative">
                                <?php echo $issueState; ?>
                            </p>
                        </div>

                        <!-- tenant voice reponses -->
                        <div class="flex-column tenant-voice-reponse-container">
                            <?php
                            $tenantVoiceResponseSets = $tenantVoiceResponse->fetchAllTenantVoiceResponse($selectedTenantVoice->roomId, $selectedTenantVoice->tenantVoiceId);
                            ?>

                            <p class="p-form n-light">
                                <?php echo sizeof($tenantVoiceResponseSets) . " Responses"; ?>
                            </p>

                            <?php
                            if (sizeof($tenantVoiceResponseSets) > 0) {
                                foreach ($tenantVoiceResponseSets as $tenantVoiceResponseSet) {
                                    if ($tenantVoiceResponseSet['whose'] == "landlord")
                                        $userPhoto = $user->getUserPhoto($tenantVoiceResponseSet['landlord_id']);
                                    else
                                        $userPhoto = $user->getUserPhoto($tenantVoiceResponseSet['tenant_id']);
                                    ?>
                                    <div class="flex-row tenant-voice-reponse-div">
                                        <div class="left">
                                            <img src="../../Assests/Uploads/User/<?php echo $userPhoto; ?>" alt="user image"
                                                class="icon-class">
                                        </div>

                                        <div class="flex-column middle response">
                                            <!-- response -->
                                            <p class="p-normal">
                                                <?php echo '"' . $tenantVoiceResponseSet['response'] . '"'; ?>
                                            </p>

                                            <!-- response date -->
                                            <p class="p-small n-light">
                                                <?php echo '"' . $tenantVoiceResponseSet['response_date'] . '"'; ?>
                                            </p>
                                        </div>

                                        <!-- operation section -->
                                        <?php 
                                        $responseId = $tenantVoiceResponseSet['tenant_voice_response_id'];
                                        $task = "delete";
                                        $url = $_SERVER['REQUEST_URI'];
                                        $link = "operation/tvr-operation.php?id=$responseId&task=$task&url=$url";
                                        ?>
                                        <div class="right <?php 
                                        if($user->role == "Landlord"){
                                            if(ucfirst($tenantVoiceResponseSet['whose']) != $user->role ){
                                                echo "hidden";
                                            }
                                        }
                                        ?>">
                                            <img src="../../Assests/Icons/delete.png" alt="" class="icon-class"  onclick="window.location.href='<?php echo $link; ?>'">
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>

                        <!-- operation section -->
                        <div class="voice-flex-column operation-section">
                            <form action="" method="POST" class="flex-column voice-operation-form">
                                <button type="submit" name="tenant-voice-solve-btn"
                                    class="primary-button <?php if ($issueState == 'Solved')
                                        echo "hidden"; ?>"> Mark issue
                                    as solved </button>
                            </form>

                            <form action="" method="POST" class="flex-column voice-operation-form">
                                <textarea name="tenant-voice-response" id="tenant-voice-response" required></textarea>
                                <button type="submit" name="tenant-voice-response-btn" class="positive-button"> Reply
                                </button>
                            </form>
                        </div>

                        <div class="bottom flex-row">
                            <div class="section flex-row pointer">
                                <img src="../../Assests/Icons/user.png" alt="">
                                <?php $link = "tenants-detail.php?tenantId=$selectedTenantVoice->tenantId&roomId=$selectedTenantVoice->roomId"; ?>
                                <p class="p-form" onclick="window.location.href='<?php echo $link; ?>'"> Show tenant detail
                                </p>
                            </div>

                            <div class="section flex-row pointer">
                                <img src="../../Assests/Icons/room.png" alt="">
                                <?php
                                $link = "myroom-detail.php?roomId=$selectedTenantVoice->roomId";
                                ?>
                                <p class="p-form" onclick="window.location.href='<?php echo $link; ?>'"> Show room detail
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="section-heading-container content-container">
                <p class="heading f-bold negative">
                    <?php echo ($selectedTenantVoice->tenantVoiceId != 0) ? "Other Tenant Voices" : "Tenant Voices"; ?>
                </p>
            </div>

            <!-- voice card -->
            <div class="card-container voice-card-container flex-row">
                <div class="voice-card flex-row shadow pointer card" id="all-voice-trigger">
                    <p class="p-form"> All Voices </p>
                    <p class="p-form f-bold n-light"> - </p>
                </div>

                <div class="voice-card flex-row shadow pointer card" id="unreplied-voice-trigger">
                    <p class="p-form"> Unsolved </p>
                    <p class="p-form f-bold n-light"> - </p>
                </div>

                <div class="voice-card flex-row shadow pointer card" id="replied-voice-trigger">
                    <p class="p-form"> Solved </p>
                    <p class="p-form f-bold n-light"> - </p>
                </div>
            </div>

            <div class="user-voice-div flex-column <?php if (sizeof($tenantVoiceSets) == 0)
                echo "hidden"; ?>">
                <!-- user voice  -->
                <?php
                if (sizeof($tenantVoiceSets) > 0) {
                    foreach ($tenantVoiceSets as $tenantVoiceSet) {
                        if ($tenantVoiceSet['tenant_voice_id'] != $selectedTenantVoice->tenantVoiceId) {
                            $tenantVoiceId = $tenantVoiceSet['tenant_voice_id'];
                            $tenantId = $tenantVoiceSet['tenant_id'];
                            $roomId = $tenantVoiceSet['room_id'];
                            $tenantName = $user->getUserName($tenantId);
                            $tenantPhoto = $user->getUserPhoto($tenantId);
                            $issueState = ($tenantVoiceSet['issue_state'] == 0) ? "Unsolved" : "Solved";
                            $voice = '"' . ucfirst($tenantVoiceSet['voice']) . '"';
                            $date = $selectedTenantVoice->date;

                            // tenant status: ex-tenant || current tenant
                            $tenantState = ($roomObj->getTenantState($roomId, $tenantId)) ? "Current Tenant" : "Ex-Tenant";
                            ?>
                            <div
                                class="user-voice shadow flex-column user-voice-element <?php echo ($tenantVoiceSet['issue_state']) ? 'user-voice-solved-element' : 'user-voice-unsolved-element'; ?>">
                                <div class="user-detail-div flex-row">
                                    <div class="image-div">
                                        <img src="../../Assests/Uploads/user/<?php echo $tenantPhoto; ?>" alt="">
                                    </div>

                                    <div class="username-div flex-column">
                                        <p class="p-form">
                                            <?php echo $tenantName; ?>
                                        </p>
                                        <p class="p-form n-light">
                                            <?php echo $tenantState; ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="user-voice-box">
                                    <p class="p-normal problem">
                                        <?php echo $voice; ?>
                                    </p>

                                    <p class="p-small">
                                        <?php echo $date; ?>
                                    </p>

                                    <p class="p-form negative">
                                        <?php echo $issueState; ?>
                                    </p>
                                </div>

                                <div class="bottom flex-row">
                                    <div class="section flex-row pointer">
                                        <img src="../../Assests/Icons/user.png" alt="">
                                        <?php
                                        $tenantId = $tenantVoiceSet['tenant_id'];
                                        $roomId = $tenantVoiceSet['room_id'];
                                        $link = "tenants-detail.php?tenantId=$tenantId&roomId=$roomId"; ?>
                                        <p class="p-form" onclick="window.location.href='<?php echo $link; ?>'"> Show tenant detail
                                        </p>
                                    </div>

                                    <div class="section flex-row pointer">
                                        <img src="../../Assests/Icons/room.png" alt="">
                                        <?php
                                        $link = "myroom-detail.php?roomId=$roomId";
                                        ?>
                                        <p class="p-form" onclick="window.location.href='<?php echo $link; ?>'"> Show room detail
                                        </p>
                                    </div>

                                    <?php
                                    $link = "tenant-voice.php?voiceId=" . $tenantVoiceId;
                                    ?>
                                    <div class="section flex-row pointer" onclick="window.location.href='<?php echo $link; ?>'">
                                        <img src="../../Assests/Icons/comment.png" alt="">
                                        <p class="p-form"> Show More </p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                } else {
                    ?>
                    <!-- dummy voice -->
                    <div class="user-voice shadow flex-column user-voice-element user-voice-replied-element hidden">
                        <div class="user-detail-div flex-row">
                            <div class="image-div">
                                <img src="../../Assests/Uploads/user/blank.jpg" alt="">
                            </div>

                            <div class="username-div flex-column">
                                <p class="p-form"> Tenant Name </p>
                                <p class="p-form n-light"> Current Tenant </p>
                            </div>
                        </div>

                        <div class="user-voice-box">
                            <p class="p-normal problem">
                                "Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse eaque officiis, ut ex
                                expedita sofficia libero."
                            </p>

                            <p class="p-small negative"> Unsolved </p>
                        </div>

                        <div class="bottom flex-row">
                            <div class="section flex-row pointer">
                                <img src="../../Assests/Icons/user.png" alt="">
                                <p class="p-form"> Show user detail </p>
                            </div>

                            <div class="section flex-row pointer">
                                <img src="../../Assests/Icons/comment.png" alt="">
                                <p class="p-form"> Reply </p>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>

            <!-- empty context -->
            <div class="container empty-data-container" id="empty-data-container">
                <div class="flex-column div empty-data-div" id="empty-data-div">
                    <p class="p-normal negative"> Empty! </p>
                </div>
            </div>
        </article>
    </div>

    <!-- jquery import -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>

    <!-- js section -->
    <script>
        const activeMenu = $('#tenant-voice-menu-id');
        activeMenu.css({
            "background-color" : "#DFDFDF"
        });
    </script>

    <script>
        $(document).ready(function () {
            var userVoiceTrigger = 0;

            var userVoiceElement = $('.user-voice-element');
            var userVoiceSolvedElement = $('.user-voice-solved-element');
            var userVoiceUnsolvedElement = $('.user-voice-unsolved-element');


            $('#all-voice-trigger').click(function () {
                userVoiceTrigger = 0;
                filterUserVoice();
            });

            $('#unreplied-voice-trigger').click(function () {
                userVoiceTrigger = 1;
                filterUserVoice();
            }
            );

            $('#replied-voice-trigger').click(function () {
                userVoiceTrigger = 2;
                filterUserVoice();
            });

            filterUserVoice = () => {
                userVoiceElement.hide();

                if (userVoiceTrigger == 0)
                    userVoiceElement.show();
                else if (userVoiceTrigger == 1)
                    userVoiceUnsolvedElement.show();
                else
                    userVoiceSolvedElement.show();

                var visibleUserVoiceElement = $('.user-voice-element:visible');

                $('#empty-data-container').hide();
                if (visibleUserVoiceElement.length == 0)
                    $('#empty-data-container').show();
            }

            filterUserVoice();
        });
    </script>
</body>

</html>