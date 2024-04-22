<?php
// staring session
if (!session_start())
    session_start();

// including files
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../class/functions.php';
include '../../Class/wishlist_class.php';
include '../../Class/tenant_voice_class.php';
include '../../Class/tenancy_history_class.php';
include '../../Class/notification_class.php';
include '../../Class/announcement_class.php';
include '../../Class/feedback_class.php';
include '../../Class/room_review_class.php';
include '../../Class/leave_application_class.php';

// creating the object
$user = new User();
$house = new House();
$room = new Room();
$myRoom = new Room();
$newUser = new User();
$tenantVoice = new TenantVoice();
$wishlist = new Wishlist();
$tenancyHistory = new TenancyHistory();
$roomReview = new RoomReview();
$notification = new Notification();
$announcement = new Announcement();
$leaveApplication = new LeaveApplication();

if (!isset($_SESSION['tenantUserId']))
    header("Location: ../../index.php");
else
    $user->fetchSpecificRow($_SESSION['tenantUserId']);

if (isset($_GET['task'])) {
    $task = $_GET['task'];
    if ($task != "view-profile" && $task != "edit-profile" && $task != "view-password-and-security" && $task != "notification" && $task != "my-room" && $task != "tenancy-history" && $task != "applied-room" && $task != "custom-room" && $task != 'my-voice' && $task != 'announcement') {
        header("location: account.php?task=view-profile");
    }
} else
    header('location: account.php?task=view-profile');

$user->userId = $_SESSION['tenantUserId'];
$user->fetchSpecificRow($user->userId);

// countng wishlist
$wishlistCount = $wishlist->countWishes($_SESSION['tenantUserId']);

// getting notification count
$notificationCount = $notification->countNotification("tenant", $user->userId, "all");

// submission
$submissionState = "failure";
if (isset($_GET['submission']))
    $submissionState = $_GET['submission'];

$errorMessageState = false;
$errorMessage = "This is an error message.";

// updating password
if (isset($_POST['password-submit'])) {
    $oldPassword = $_POST['password-old'];
    $newPassword1 = $_POST['password-new-1'];
    $newPassword2 = $_POST['password-new-2'];
    if (strlen($oldPassword) < 8 || strlen($newPassword1) < 8 || strlen($newPassword2) < 8) {
        $errorMessageState = true;
        $errorMessage = 'Make sure the size of passwords are greater than or equal to 8.';
    } else {
        if ($oldPassword == $newPassword1) {
            $errorMessageState = true;
            $errorMessage = 'Please enter new password different than the old password.';
        } else {
            if ($newPassword1 != $newPassword2) {
                $errorMessageState = true;
                $errorMessage = 'Please enter the same password for the new passwords.';
            } else {
                $newPasswordUser = new User();
                $newPasswordUser->fetchSpecificRow($_SESSION['tenantUserId']);
                $newPasswordUser->userId = $_SESSION['tenantUserId'];

                $password_decrypt = password_verify($oldPassword, $newPasswordUser->password);
                if (!$password_decrypt) {
                    $errorMessageState = true;
                    $errorMessage = "The old password didn't match.";
                } else {
                    $encPassword = password_hash($newPassword1, PASSWORD_BCRYPT);
                    $result = $newPasswordUser->updatePassword($encPassword);
                    if ($result) {
                        header("location: account.php?task=view-password-and-security&submission=success");
                    } else {
                        $errorMessageState = true;
                        $errorMessage = "Error occured. Please try again later.";
                    }
                }
            }
        }
    }
}

// account edit
$newUser->userId = $user->userId;
$newUser->setKeyValue('password', $user->password);

// global variables
$newUserPhoto = "";

// updating user detail
if (isset($_POST['update-user'])) {
    global $newUserPhoto;

    $newUser->firstName = $_POST['first-name'];
    $newUser->middleName = $_POST['middle-name'];
    $newUser->lastName = $_POST['last-name'];
    $newUser->gender = $_POST['gender'];
    $newUser->dob = $_POST['dob'];
    $newUser->province = $_POST['province'];
    $newUser->district = $_POST['district'];
    $newUser->isVdc = $_POST['isVdc'];
    $newUser->areaName = $_POST['area-name'];
    $newUser->wardNumber = $_POST['ward-number'];
    $newUser->contact = $_POST['contact'];

    $result = $newUser->updateUser();

    $state = true; 
    
    if ($result){
        // user photo
        $oldUserPhoto = $user->userPhoto;

        if(isset($_FILES['new-user-photo']) && $_FILES['new-user-photo']['name'] != ""){             
            if(fileValidityCheck($_FILES['new-user-photo'])){
                uploadFile("userPhoto", $_FILES['new-user-photo']);
                $response = $user->updateUserPhoto($newUserPhoto, $_SESSION['tenantUserId']);
                unlink("../../Assests/Uploads/User/".$oldUserPhoto);                
            }else
                $state = false;
        } else {
            $state = false;
        }
    } else {
        $state = false;
    }

    if($state)
        header('location: account.php?task=edit-profile&submission=success');
    else
        header('location: account.php?task=edit-profile&submission=failure');
}

function fileValidityCheck($formFile)
{
    $fileValid = true;

    $fileName = $formFile['name'];
    $fileSize = $formFile['size'];
    $fileError = $formFile['error'];

    // error check
    if ($fileError) {
        $fileValid = false;
    } else {
        // size check
        if ($fileSize >= 2087152) {
            $fileValid = false;
        } else {
            // extension extraction
            $fileTempExtension = explode('.', $fileName);
            $fileExtension = strtolower(end($fileTempExtension));
            $allowedExtension = array('jpg', 'jpeg', 'png');

            if (!in_array($fileExtension, $allowedExtension))
                $fileValid = false;
        }
    }

    return $fileValid;
}

function uploadFile($fileCategory, $formFile)
{
    global $newUserPhoto;

    $fileName = $formFile['name'];
    $fileTmpName = $formFile['tmp_name'];

    // extension extraction
    $fileTempExtension = explode('.', $fileName);
    $fileExtension = strtolower(end($fileTempExtension));

    $newFileName = uniqid('', true) . "." . $fileExtension;

    // upload destination
    $userPhotoDestination = "../../Assests/Uploads/User/";

    // setting destination
    if ($fileCategory == "userPhoto") {
        $userPhotoName = $newFileName;
        $userPhotoTmpName = $fileTmpName;
        $newUserPhoto = $newFileName;
        $userPhotoDestinaltion = $userPhotoDestination . $userPhotoName;
        move_uploaded_file($userPhotoTmpName, $userPhotoDestinaltion);
    }
}

// my room
$myRoom = new Room();
$myRoomState = $myRoom->fetchMyRoom($user->userId);

// my room review submission
if (isset($_POST['my-room-review-submit-btn'])) {
    $roomId = $myRoom->roomId;
    $tenantId = $user->userId;
    $review = $_POST['my-room-review'];
    $rating = $_POST['my-room-rating'];
    $date = date('Y-m-d H:i:s');
    $state = 0;

    $roomReview->setRoomReview($roomId, $tenantId, $review, $rating, $date, $state);
    $immediateRoomReviewId = $roomReview->registerRoomReview();

    if ($immediateRoomReviewId != 0) {
        // create notification for landlord
        $room->fetchRoom($roomId);
        $houseId = $room->houseId;
        $landlordId = $house->getOwnerId($houseId);
        $tenantId = $_SESSION['tenantUserId'];
        $notification->setRoomReviewNotification($immediateRoomReviewId, $roomId, $landlordId, $tenantId);
        $response = $notification->register();
    }
}

// tenant voice submission
if (isset($_POST['convey-issue-btn'])) {
    $voice = $_POST['convey-issue-issue'];
    $date = date('Y-m-d H:i:s');
    $issueState = 0;

    $tenantVoice->setTenantVoice($user->userId, $myRoom->roomId, $voice, $date, $issueState);
    $immediateTenantVoiceId = $tenantVoice->registerTenantVoice();

    if ($immediateTenantVoiceId != 0) {
        // create notification for landlord
        $landlordId = $myRoom->getOwnerId($myRoom->houseId);
        $notification->setTenantVoiceNotification("tenant-voice-submit", $myRoom->roomId, $immediateTenantVoiceId, $landlordId, $user->userId);
        $notification->whose = "landlord";
        $notification->register();
    } else {
        // tenant voice submission failed
    }
}

// leave room application
if (isset($_POST['leave-room-btn'])) {
    // getting form values
    // application set
    $leaveDate = $_POST['leave-room-date'];
    $applicationDate = date('Y-m-d H:i:s');
    $state = 0;

    $roomId = $myRoom->roomId;

    $myRoomState = $room->fetchMyRoom($user->userId);

    // get landlord id

    $house->fetchHouse($room->houseId);

    $landlordId = $house->ownerId;

    $note = isset($_POST['leave-room-note']) ? $_POST['leave-room-note'] : NULL;


    $leaveApplication->setLeaveApplication($roomId, $landlordId, $user->userId, $leaveDate, $note, $state, $applicationDate);
    $immediateLeaveApplicationId = $leaveApplication->registerLeaveApplication();

    if ($immediateLeaveApplicationId != 0) {
        // notify the alndlord
        $notification->setLeaveApplicationNotification("room-leave-application-submit", $roomId, $landlordId, $user->userId, $immediateLeaveApplicationId);
        $notification->whose = "landlord";
        $notification->register();
    }

    $url = $_SERVER['REQUEST_URI'];
    header("location: $url");
}

// fetching announced data
$announcementSets = $announcement->fetchAllAnnouncement('tenant', $user->userId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- linking external css -->
    <link rel="stylesheet" href="../../CSS/Common/style.css">
    <link rel="stylesheet" href="../../CSS/Common/table.css">
    <link rel="stylesheet" href="../../CSS/tenant/navbar.css">
    <link rel="stylesheet" href="../../CSS/tenant/account.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/announcement.css">
    <link rel="stylesheet" href="../../CSS/Tenant/resided-room-detail.css">
    <link rel="stylesheet" href="../../CSS/Common/lightbox.min.css">
    <link rel="stylesheet" href="../../CSS/tenant/leave-room.css">
    <link rel="stylesheet" href="../../CSS/tenant/convey-issue.css">
    <!-- linghtbox -->
    <link rel="stylesheet" href="../../CSS/Common/lightbox.min.css">

    <style>
        .photo-container-new{
            align-items: center;

            .right{
                margin: 0;
                input{
                    border: none;
                }
            }
        }
    </style>

    <!-- title -->
    <title> My Profile </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- script -->
    <script src="../../Js/main.js"> </script>

    <!-- jquery -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>

    <!-- lightbox -->
    <script src="../../Js/lightbox-plus-jquery.min.js"> </script>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="account-container container flex-row hiddens">
        <div class="account-div div flex-row">
            <!-- menu -->
            <div class="account-menu flex-column">
                <!-- profile -->
                <div class="menu flex-row pointer <?php if ($task == "view-profile" || $task == "edit-profile")
                    echo "active-menu"; ?>" onclick="window.location.href='account.php?task=view-profile'">
                    <div class="menu-left flex-row">
                        <img src="../../Assests/Icons/user-square.svg" class="icon-class" alt="">
                    </div>

                    <div class="menu-right ">
                        <p class="p-normal"> Profile </p>
                    </div>
                </div>

                <!-- Password & Security -->
                <div class="menu flex-row pointer <?php if ($task == "view-password-and-security")
                    echo "active-menu"; ?>"
                    onclick="window.location.href='account.php?task=view-password-and-security'">
                    <div class="menu-left flex-row">
                        <img src="../../Assests/Icons/shield.png" class="icon-class" alt="">
                    </div>

                    <div class="menu-right ">
                        <p class="p-normal"> Password & Security </p>
                    </div>
                </div>

                <!-- Notification -->
                <div class="menu flex-row pointer <?php if ($task == "notification")
                    echo "active-menu"; ?>" onclick="window.location.href='account.php?task=notification'">
                    <div class="menu-left flex-row">
                        <img src="../../Assests/Icons/notification.svg" class="icon-class" alt="">
                    </div>

                    <div class="menu-right ">
                        <p class="p-normal"> Notification Setting </p>
                    </div>
                </div>

                <!-- my room -->
                <div class="menu flex-row pointer <?php if ($task == "my-room")
                    echo "active-menu"; ?>" onclick="window.location.href='account.php?task=my-room'">
                    <div class="menu-left flex-row">
                        <img src="../../Assests/Icons/room.png" class="icon-class" alt="">
                    </div>

                    <div class="menu-right ">
                        <p class="p-normal"> My Room </p>
                    </div>
                </div>

                <!-- announcement -->
                <div class="menu flex-row pointer <?php if ($task == "announcement")
                    echo "active-menu"; ?>" onclick="window.location.href='account.php?task=announcement'">
                    <div class="menu-left flex-row">
                        <img src="../../Assests/Icons/announcement.png" class="icon-class" alt="">
                    </div>

                    <div class="menu-right ">
                        <p class="p-normal"> Announcement </p>
                    </div>
                </div>

                <!-- tenancy history -->
                <div class="menu flex-row pointer <?php if ($task == "tenancy-history")
                    echo "active-menu"; ?>" onclick="window.location.href='account.php?task=tenancy-history'">
                    <div class="menu-left flex-row">
                        <img src="../../Assests/Icons/make-tenant.png" class="icon-class" alt="">
                    </div>

                    <div class="menu-right ">
                        <p class="p-normal"> Tenancy History </p>
                    </div>
                </div>

                <!-- applied room -->
                <div class="menu flex-row pointer <?php if ($task == "applied-room")
                    echo "active-menu"; ?>" onclick="window.location.href='account.php?task=applied-room'">
                    <div class="menu-left flex-row">
                        <img src="../../Assests/Icons/calendar.png" class="icon-class" alt="">
                    </div>

                    <div class="menu-right ">
                        <p class="p-normal"> Applied Room </p>
                    </div>
                </div>

                <!-- custom room -->
                <div class="menu flex-row pointer <?php if ($task == "custom-room")
                    echo "active-menu"; ?>" onclick="window.location.href='account.php?task=custom-room'">
                    <div class="menu-left flex-row">
                        <img src="../../Assests/Icons/setting.svg" class="icon-class" alt="">
                    </div>

                    <div class="menu-right ">
                        <p class="p-normal"> Custom Room </p>
                    </div>
                </div>

                <!-- my voices -->
                <div class="menu flex-row pointer <?php if ($task == "my-voice")
                    echo "active-menu"; ?>" onclick="window.location.href='account.php?task=my-voice'">
                    <div class="menu-left flex-row">
                        <img src="../../Assests/Icons/speaking.png" class="icon-class" alt="">
                    </div>

                    <div class="menu-right ">
                        <p class="p-normal"> My Voices </p>
                    </div>
                </div>
            </div>

            <div class="account-detail flex-column ">
                <!-- edit profile -->
                <div class="profile-detail-container flex-column <?php if (!($task == "view-profile" || $task == "edit-profile"))
                    echo "hidden"; ?>">
                    <!-- heading -->
                    <div class="container">
                        <p class="p-large heading f-bold"> My Profile </p>
                    </div>

                    <form action="" method="POST" class="profile-form flex-column" id="new-user-form" autocomplete="off" enctype="multipart/form-data">
                        <!-- photo & username -->
                        <div class="photo-container flex-column">
                            <div class="photo-div">
                                <img src="../../Assests/Uploads/user/<?php echo $user->userPhoto; ?>" alt="">
                            </div>
                        </div>

                        <!-- photo -->
                        <div class="flex-row photo-container-new <?php if ($task != "edit-profile")
                                echo "hidden"; ?>">
                            <div class="left">
                                <p class="p-normal"> Change User Photo </p>
                            </div>

                            <div class="middle">
                                <input type="file" name="new-user-photo" id="new-user-photo">
                                <input type="hidden" name="old-user-photo" id="old-user-photo" value="<?php echo $user->userPhoto; ?>">
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="role-div flex-row">
                            <div class="left flex-row">
                                <p class="p-normal"> Role </p>
                            </div>

                            <div class="middle flex-row">
                                <p class="p-normal">
                                    <?php echo ucfirst($user->role); ?>
                                </p>
                            </div>

                            <div class="right flex-row hidden">
                                <p class="p-normal"> Landlord / Tenant </p>
                            </div>
                        </div>

                        <!-- full name -->
                        <div class="name-container flex-row">
                            <div class="left flex-row">
                                <p class="p-normal"> Name </p>
                            </div>

                            <div class="middle flex-row <?php if ($task != "view-profile")
                                echo "hidden"; ?>">
                                <p class="p-normal">
                                    <?php echo returnFormattedName($user->firstName, $user->middleName, $user->lastName); ?>
                                </p>
                            </div>

                            <div class="name-div right hidden">
                                <input type="text" name="first-name" id="first-name"
                                    value="<?php echo $user->firstName; ?>" placeholder="First name" class="<?php if ($task != "edit-profile")
                                           echo "hidden";
                                       else
                                           echo " "; ?>" onkeypress="avoidMistake('word')" autocomplete="off" required>
                                <input type="text" name="middle-name" id="middle-name"
                                    value="<?php echo $user->middleName; ?>" placeholder="Middle name" class="<?php if ($task != "edit-profile")
                                           echo "hidden";
                                       else
                                           echo ""; ?>" onkeypress="avoidMistake('word')" autocomplete="off">
                                <input type="text" name="last-name" id="last-name"
                                    value="<?php echo $user->lastName; ?>" placeholder="Last name" class="<?php if ($task != "edit-profile")
                                           echo "hidden";
                                       else
                                           echo ""; ?>" onkeypress="avoidMistake('word')" autocomplete="off" required>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="flex-row">
                            <div class="left flex-row">
                                <p class="p-normal"> Gender </p>
                            </div>

                            <div class="middle flex-row <?php if ($task != "view-profile")
                                echo "hidden"; ?>">
                                <p class="p-normal">
                                    <?php echo ucfirst($user->gender); ?>
                                </p>
                            </div>

                            <div class="right flex-row <?php if ($task != "edit-profile")
                                echo "hidden"; ?>">
                                <select name="gender">
                                    <?php
                                    if ($user->gender == "male")
                                        echo '<option value="male" selected hidden> Male </option>';
                                    elseif ($user->gender == "female")
                                        echo '<option value="female" selected hidden> Female </option>';
                                    else
                                        echo '<option value="others" selected hidden> Others </option>';
                                    ?>

                                    <option value="0" hidden> Gender </option>
                                    <option value="male"> Male </option>
                                    <option value="female"> Female </option>
                                    <option value="others"> Others </option>
                                </select>
                            </div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="flex-row">
                            <div class="left flex-row">
                                <p class="p-normal"> Date of Birth </p>
                            </div>

                            <div class="middle flex-row <?php if ($task != "view-profile")
                                echo "hidden"; ?>">
                                <p class="p-normal">
                                    <?php echo ucfirst($user->dob); ?>
                                </p>
                            </div>

                            <div class="right flex-row <?php if ($task != "edit-profile")
                                echo "hidden"; ?>">
                                <input type="date" name="dob" value="<?php echo $user->dob; ?>">
                            </div>
                        </div>

                        <!-- address -->
                        <div class="address-container flex-row">
                            <div class="left flex-row">
                                <p class="p-normal"> Address </p>
                            </div>

                            <div class="middle flex-row <?php if ($task != "view-profile")
                                echo "hidden"; ?>">
                                <p class="p-normal">
                                    <?php echo returnFormattedAddress($user->province, $user->district, $user->areaName, $user->wardNumber) ?>
                                </p>
                            </div>

                            <div class="right address-div <?php if ($task != "edit-profile")
                                echo "hidden"; ?>">
                                <!-- province -->
                                <select name="province" class="<?php if ($task != "edit-profile")
                                    echo "hidden"; ?>">
                                    <?php echo '<option value="' . $user->province . '" selected hidden>' . returnArrayValue('province', $user->province) . '</option>'; ?>
                                    <?php
                                    for ($i = 1; $i <= 7; $i++)
                                        echo '<option value="' . $i . '">' . returnArrayValue('province', $i) . '</option>';
                                    ?>
                                </select>

                                <!-- district -->
                                <select name="district" class="<?php if ($task != "edit-profile")
                                    echo "hidden"; ?>">
                                    <?php echo '<option value="' . $user->district . '" selected hidden>' . returnArrayValue('district', $user->district) . '</option>'; ?>
                                    <?php
                                    for ($i = 1; $i <= 77; $i++)
                                        echo '<option value="' . $i . '">' . returnArrayValue('district', $i) . '</option>';
                                    ?>
                                </select>

                                <!-- isVDC -->
                                <select name="isVdc" class="<?php if ($task != "edit-profile")
                                    echo "hidden"; ?>">
                                    <?php
                                    if ($user->isVdc)
                                        echo '<option value="vdc" selected hidden> VDC </option>';
                                    else
                                        echo '<option value="municipality" selected hidden> Municipality </option>';
                                    ?>
                                    <option value="vdc"> VDC </option>
                                    <option value="municipality"> Municipality </option>
                                </select>

                                <!-- area name -->
                                <input type="text" name="area-name" id="area-name"
                                    value="<?php echo ucfirst($user->areaName); ?>" placeholder="Area name" class="<?php if ($task != "edit-profile")
                                           echo "hidden"; ?>" onkeypress="avoidMistake('word')" required>

                                <!-- ward number -->
                                <select name="ward-number" class="<?php if ($task != "edit-profile")
                                    echo "hidden"; ?>">
                                    <?php
                                    echo '<option value="' . $user->wardNumber . '" selected hidden>' . $user->wardNumber . '</option>';
                                    ?>
                                    <option value="1"> 1 </option>
                                    <option value="2"> 2 </option>
                                    <option value="3"> 3 </option>
                                    <option value="4"> 4 </option>
                                    <option value="5"> 5 </option>
                                    <option value="6"> 6 </option>
                                    <option value="7"> 7 </option>
                                    <option value="8"> 8 </option>
                                    <option value="9"> 9 </option>
                                </select>
                            </div>
                        </div>

                        <!-- Email address -->
                        <div class="flex-row">
                            <div class="left flex-row">
                                <p class="p-normal"> Email Address </p>
                            </div>

                            <div class="middle flex-row <?php if ($task != "view-profile")
                                echo "hidden"; ?>">
                                <p class="p-normal">
                                    <?php echo $user->email; ?>
                                </p>
                            </div>

                            <div class="right flex-row <?php if ($task != "edit-profile")
                                echo "hidden"; ?>">
                                <p class="p-normal n-light">
                                    <?php echo $user->email; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Contact -->
                        <div class="flex-row">
                            <div class="left flex-row">
                                <p class="p-normal"> Contact </p>
                            </div>

                            <div class="middle flex-row <?php if ($task != "view-profile")
                                echo "hidden"; ?>">
                                <p class="p-normal">
                                    <?php echo $user->contact; ?>
                                </p>
                            </div>

                            <div class="right flex-row <?php if ($task != "edit-profile")
                                echo "hidden"; ?>">
                                <input type="text" name="contact" id="contact" value="<?php echo $user->contact; ?>"
                                    onkeypress="avoidMistake('integer')" required>
                            </div>
                        </div>

                        <div class="flex-row document-section">
                            <div class="left">
                                <p class="p-normal"> Document </p>
                            </div>

                            <div class="flex-column right">
                                <?php
                                $citizenshipFrontPhoto = $user->citizenshipFrontPhoto;
                                $citizenshipBackPhoto = $user->citizenshipBackPhoto;
                                ?>

                                <?php $link = "../../Assests/Uploads/Citizenship/$citizenshipFrontPhoto";?>
                                <a href="<?php echo $link; ?>" data-lightbox="citizenship-photo">
                                    <p class="p-normal"> CItizenship Frontside </p>
                                </a>

                                <?php $link = "../../Assests/Uploads/Citizenship/$citizenshipBackPhoto";?>
                                <a href="<?php echo $link; ?>" data-lightbox="citizenship-photo">
                                    <p class="p-normal"> Citizenship Backside </p>
                                </a>
                            </div>
                        </div>

                        <!-- Account state -->
                        <div class="account-state-container flex-row">
                            <div class="left flex-row">
                                <p class="p-normal"> Account State </p>
                            </div>

                            <div class="account-state-div flex-row">
                                <p class="p-normal">
                                    <?php if ($user->accountState == 0)
                                        echo "Unverified";
                                    elseif ($user->accountState == 1)
                                        echo "Verified";
                                    else
                                        echo "Suspended"; ?>
                                </p>
                            </div>
                        </div>

                        <!-- edit profile : buttons -->
                        <div class="account-reset-div flex-row <?php if ($task != "edit-profile")
                            echo "hidden"; ?>">
                            <button type="submit" name="update-user"> Update </button>
                            <a href="account.php?task=edit-profile"> Reset </a>
                            <a href="account.php?task=view-profile"> Cancel </a>
                        </div>

                    </form>

                    <!-- view profile : buttons -->
                    <div class="account-deactive-div flex-row <?php if ($task != "view-profile")
                        echo "hidden"; ?>">
                        <button class="negative-button" onclick="deactivateAccount()"> Deactivate Account </button>
                        <button class="inverse-button" onclick="window.location.href='account.php?task=edit-profile'">
                            Edit Profile </button>
                    </div>
                </div>

                <!-- password & security -->
                <div class="password-security-container flex-column <?php if ($task != "view-password-and-security")
                    echo "hidden"; ?>">
                    <!-- heading -->
                    <div class="container flex-column">
                        <p class="p-large heading f-bold"> Password & Security </p>
                        <?php if ($errorMessageState)
                            echo '<p class="p-form negative">' . $errorMessage . '</p>';
                        ?>

                    </div>

                    <!-- form -->
                    <div class="top-section flex-row">
                        <img src="../../Assests/Icons/eye.svg" alt="" class="pointer" onclick="togglePassword()">
                        <p class="p-form pointer" id="show-password-label" onclick="togglePassword()"> Show Password
                        </p>
                    </div>

                    <form action="" method="POST" class="password-update-form flex-column" autocomplete="off">
                        <input type="password" name="password-old" id="password-old" placeholder="old password" value="<?php if (isset($_POST['password-old']))
                            echo $_POST['password-old']; ?>" required>
                        <input type="password" name="password-new-1" id="password-new-1" placeholder="new password"
                            value="<?php if (isset($_POST['password-new-1']))
                                echo $_POST['password-new-1']; ?>" required>
                        <input type="password" name="password-new-2" id="password-new-2"
                            placeholder="re-type new password" value="<?php if (isset($_POST['password-new-2']))
                                echo $_POST['password-new-2']; ?>" required>
                        <input type="submit" name="password-submit" value="Update">
                    </form>

                    <div class="initial-div flex-column">
                        <p class="p-form"> Note : Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas et
                            ipsum, unde, illo, rem nemo molestiae aliquid ullam obcaecati debitis nostrum rerum
                            distinctio delectus?</p>
                    </div>
                </div>

                <!-- dialog box -->
                <?php
                if ($submissionState == 'success') {
                    ?>
                    <div class="dialog-container flex-column">
                        <div class="dialog-div flex-column">
                            <div class="top-div flex-row">
                                <div class="message-div flex-column">
                                    <?php
                                    if ($task == 'view-password-and-security') {
                                        echo '<p class="p-large f-bold"> Password has been updated. </p>';
                                    } elseif ($task == 'edit-profile') {
                                        echo '<p class="p-large f-bold"> Profile has been updated succesfully. </p>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="operation-div flex-row">
                                <!-- <button onclick="window.location.href='add-house.php';" class="inverse-button"> Register Another House </button> -->
                                <!-- <button onclick="window.location.href='myhouse.php';"> See House Details </button> -->
                                <?php
                                if ($task == 'view-password-and-security' || $task == 'edit-profile') {
                                    ?>
                                    <button onclick="window.location.href='account.php?task=view-profile'" class="button"> View
                                        Profile </button>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                } ?>

                <!-- notification -->
                <div class="notification-setting-container flex-column <?php if ($task != "notification")
                    echo "hidden"; ?>">
                    <!-- heading -->
                    <div class="container flex-column">
                        <p class="p-large heading f-bold"> Notification Setting </p>
                    </div>

                    <p class="p-normal n-light"> Email : someone@gmail.com </p>

                    <p class="p-normal"> Get important notification in your email address? </p>

                    <!-- Toggle Slider Container -->
                    <label class="toggle-container">
                        <input type="checkbox">
                        <div class="slider"> </div>
                    </label>
                </div>

                <!-- my room -->
                <?php
                if ($myRoomState) {
                    ?>
                    <div class="notification-setting-container flex-column <?php if ($task != "my-room")
                        echo "hidden"; ?>">
                        <!-- selected room detail -->
                        <div class="room-detail-container container flex-row">
                            <div class="room-detail-div div flex-column">
                                <!-- house name and rating section -->
                                <div class="house-name-rating-button-container flex-row">
                                    <div class="flex-column house-name-rating-container">
                                        <div class="name-container">
                                            <p class="p-large f-bold">
                                                <?php echo ucfirst($house->getHouseIdentity($myRoom->houseId) . " >> Room No : " . $myRoom->roomNumber); ?>
                                            </p>
                                        </div>

                                        <?php
                                        // get room raing and number of ratings
                                        $myRoomRatingSet = $roomReview->getRoomRatings($myRoom->roomId);
                                        $numberOfReview = count($myRoomRatingSet);
                                        $myRoomRating = 0;
                                        if (sizeof($myRoomRatingSet) != 0)
                                            $myRoomRating = array_sum($myRoomRatingSet) / count($myRoomRatingSet);
                                        ?>

                                        <div class="flex-row rating-container">
                                            <div class="star-div flex-row">
                                                <?php
                                                if ($myRoomRating != 0) {
                                                    if (is_float($myRoomRating)) {
                                                        $myRoomRating = intval($myRoomRating);
                                                        for ($i = 0; $i < $myRoomRating; $i++) {
                                                            ?>
                                                            <img src="../../Assests/Icons/full-rating.png" class="icon class" alt="">
                                                            <?php
                                                        }
                                                        ?>
                                                        <img src="../../Assests/Icons/half-rating.png" class="icon class" alt="">
                                                        <?php
                                                    } else {
                                                        for ($i = 0; $i < $myRoomRating; $i++) {
                                                            ?>
                                                            <img src="../../Assests/Icons/full-rating.png" class="icon class" alt="">
                                                            <?php
                                                        }
                                                    }
                                                } else {
                                                    echo 'No ratings';
                                                }
                                                ?>
                                            </div>

                                            <div class="rating-div">
                                                <p class="p-form n-light"> (
                                                    <?php echo $numberOfReview; ?> Reviews)
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- operatin button section -->
                                    <div class="flex-row operation-section">
                                        <button class="warning-button leave-room-button" id="convey-issue-trigger">
                                            <img src="../../Assests/Icons/speaking.png" alt="" class="icon-class">
                                            <p class="p-normal"> Report an Issue </p>
                                        </button>

                                        <?php
                                        //  $roomId = $myRoom->id;
                                        //  $tenantId = $_SESSION['tenantUserId'];
                                        //  $url = $_SERVER['REQUEST_URI'];
                                        //  $link = "../operation/application-operation.php?roomId=$roomId&tenantId=$tenantId&url=$url";
                                        //  $link = $link.'&task='.$task;
                                        ?>
                                        <button class="negative-button leave-room-button" id="leave-room-trigger">
                                            <img src="../../Assests/Icons/logout.svg" alt="" class="icon-class">
                                            <p class="p-normal"> Leave Room </p>
                                        </button>
                                    </div>
                                </div>

                                <!-- room image section -->
                                <div class="room-photo-container content-container flex-row">
                                    <div class="left flex-column">
                                        <img src="../../Assests/Uploads/Room/<?php echo $myRoom->roomPhotoArray[0]['room_photo']; ?>"
                                            alt="">
                                    </div>

                                    <div class="right">
                                        <div class="photo-div">
                                            <a href="../../Assests/Uploads/Room/<?php echo $myRoom->roomPhotoArray[0]['room_photo']; ?>"
                                                data-lightbox="residing-room-photo">
                                                <img src="../../Assests/Uploads/Room/<?php echo $myRoom->roomPhotoArray[0]['room_photo']; ?>"
                                                    id="photo-4" alt="">
                                            </a>
                                        </div>

                                        <div class="photo-div">
                                            <a href="../../Assests/Uploads/Room/<?php echo $myRoom->roomPhotoArray[1]['room_photo']; ?>"
                                                data-lightbox="residing-room-photo">
                                                <img src="../../Assests/Uploads/Room/<?php echo $myRoom->roomPhotoArray[1]['room_photo']; ?>"
                                                    id="photo-4" alt="">
                                            </a>
                                        </div>

                                        <div class="photo-div">
                                            <a href="../../Assests/Uploads/Room/<?php echo $myRoom->roomPhotoArray[2]['room_photo']; ?>"
                                                data-lightbox="residing-room-photo">
                                                <img src="../../Assests/Uploads/Room/<?php echo $myRoom->roomPhotoArray[2]['room_photo']; ?>"
                                                    id="photo-4" alt="">
                                            </a>
                                        </div>

                                        <div class="photo-div">
                                            <a href="../../Assests/Uploads/Room/<?php echo $myRoom->roomPhotoArray[3]['room_photo']; ?>"
                                                data-lightbox="residing-room-photo">
                                                <img src="../../Assests/Uploads/Room/<?php echo $myRoom->roomPhotoArray[3]['room_photo']; ?>"
                                                    id="photo-4" alt="">
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- detail -->
                                <div class="room-detail-container content-container flex-row">
                                    <div class="room-detail-left-div flex-column">
                                        <!-- requirement -->
                                        <div class="requirement-container flex-column">
                                            <p class="p-large f-bold n-light"> Room Requirements </p>
                                            <div class="requirement-div">
                                                <p class="p-normal">
                                                    <?php echo (ucfirst($myRoom->requirement) == ' ') ? ucfirst($myRoom->requirement) : "No requirment"; ?>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- house requirement -->
                                        <div class="requirement-container content-container flex-column">
                                            <p class="p-large f-bold n-light"> House Requirements </p>
                                            <div class="requirement-div">
                                                <p class="p-normal">
                                                    <?php echo ucfirst($myRoom->getGeneralRequirement($myRoom->houseId)); ?>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- amenities container -->
                                        <div class="amenities-container content-container flex-column">
                                            <!-- heading -->
                                            <p class="p-large f-bold n-light"> Amenities </p>
                                            <div class="amenities-div">
                                                <?php
                                                $amenities = unserialize(base64_decode($myRoom->amenities));
                                                foreach ($amenities as $amenity) {
                                                    ?>
                                                    <div class="amenity flex-column card">
                                                        <?php
                                                        $amenityName = returnArrayValue("amenity", $amenity);
                                                        $amenityIconName = returnIconName($amenityName);
                                                        ?>
                                                        <img src="../../Assests/Icons/amenities/<?php echo $amenityIconName; ?>"
                                                            alt="Amenity icon">
                                                        <p class="p-normal">
                                                            <?php echo returnArrayValue("amenity", $amenity); ?>
                                                        </p>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>


                                        <p class="p-large f-bold n-light"> Feel free to review the room </p>

                                        <?php $myRoomReviewSets = $roomReview->fetchMyRoomReview($user->userId, $myRoom->roomId); ?>

                                        <div class="review-container">
                                            <div class="review-div flex-column">
                                                <p class="p-normal n-light <?php if (sizeof($myRoomReviewSets) == 0)
                                                    echo "hidden"; ?>">
                                                    Review you already submitted </p>
                                                <?php

                                                if (sizeof($myRoomReviewSets) > 0) {
                                                    foreach ($myRoomReviewSets as $myRoomReviewSet) {
                                                        ?>

                                                        <?php
                                                        $reviewerId = $myRoomReviewSet['tenant_id'];
                                                        $user->fetchUser($reviewerId);
                                                        $reviewerName = $user->getUserName($reviewerId);
                                                        $reviewerPhoto = $user->getUserPhoto($reviewerId);
                                                        $review = '"' . ucfirst($myRoomReviewSet['review_data']) . '"';
                                                        $rating = $myRoomReviewSet['rating'];
                                                        ?>

                                                        <!-- reviews -->
                                                        <div class="review flex-row">
                                                            <div class="left flex-column">
                                                                <img src="../../Assests/Uploads/user/<?php echo $reviewerPhoto; ?>"
                                                                    alt="">
                                                            </div>

                                                            <div class="right flex-column">
                                                                <div class="flex-row right-top">
                                                                    <div class="flex-column tenant-detail">
                                                                        <p class="p-form">
                                                                            <?php echo $reviewerName; ?>
                                                                        </p>
                                                                    </div>
                                                                </div>

                                                                <div class="flex-column right-bottom">
                                                                    <p class="p-normal">
                                                                        <?php echo $review; ?>
                                                                    </p>

                                                                    <div class="flex-row rating-div">
                                                                        <?php
                                                                        for ($i = 0; $i < $rating; $i++) {
                                                                            ?>
                                                                            <img src="../../Assests/Icons/full-rating.png"
                                                                                class="icon class" alt="">
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="operation-div">
                                                                <abbr title="Delete my review">
                                                                    <img src="../../Assests/Icons/delete.png" alt=""
                                                                        class="icon-class">
                                                                </abbr>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <div class="flex-row empty-room-review">
                                                        <p class="p-normal negative"> You haven't submitted the room review yet.
                                                        </p>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>


                                        <!-- review section -->
                                        <div class="flex-column myroom-review-section">
                                            <p class="p-normal n-light"> You can submit new review </p>

                                            <form action="" method="POST" class="flex-column my-room-review-form">
                                                <select name="my-room-rating" id="my-room-rating">
                                                    <option value="1" slected> 1 </option>
                                                    <option value="2"> 2 </option>
                                                    <option value="3"> 3 </option>
                                                    <option value="4"> 4 </option>
                                                    <option value="5"> 5 </option>
                                                </select>

                                                <textarea name="my-room-review" id="my-room-review" cols="30"
                                                    placeholder="write your review here.." rows="10"></textarea>

                                                <button type="submit" name="my-room-review-submit-btn"
                                                    class="flex-row my-room-submit-btn">
                                                    <p class="p-normal"> Submit Review </p>
                                                    <img src="../../Assests/Icons/send-white.png" alt="">
                                                </button>
                                            </form>
                                        </div>

                                        <!-- ad container -->
                                        <div class="ad-container">
                                            <img src="../../Assests/Images/ad-1.png" alt="">
                                        </div>
                                    </div>

                                    <!-- right section -->
                                    <div class="room-detail-right-div flex-column">
                                        <div class="verified-date-div flex-row">
                                            <div class="verified-div flex-row">
                                                <?php
                                                if ($myRoom->roomState == 0)
                                                    $icon = "report.png";
                                                elseif ($myRoom->roomState == 1)
                                                    $icon = "verified.png";
                                                else
                                                    $icon = "report.png";
                                                ?>
                                                <img src="../../Assests/Icons/<?php echo $icon; ?>" class="icon-class"
                                                    alt="">
                                                <p class="p-form">
                                                    <?php
                                                    if ($myRoom->roomState == 0)
                                                        echo "Unverified";
                                                    elseif ($myRoom->roomState == 1)
                                                        echo "Verified";
                                                    else
                                                        echo "Suspended";
                                                    ?>
                                                </p>
                                            </div>

                                            <!-- report -->
                                            <div class="date-div flex-row">
                                                <abbr title="Report this room">
                                                    <img src="../../Assests/Icons/report.png" class="icon-class" alt="">
                                                </abbr>
                                            </div>
                                        </div>

                                        <!-- table detail -->
                                        <table class="room-detail-table">
                                            <tr>
                                                <td class="detail-title"> Room Number </td>
                                                <td class="detail-data">
                                                    <?php echo $myRoom->roomNumber; ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="detail-title"> Room Type </td>
                                                <td class="detail-data">
                                                    <?php echo ($myRoom->roomType == 1) ? $myRoom->bhk . " BHK, " : "Non BHK, "; ?>
                                                    <?php
                                                    if ($myRoom->furnishing == 0)
                                                        echo "Unfurnished";
                                                    elseif ($myRoom->furnishing == 1)
                                                        echo "Semi-Furnished";
                                                    else
                                                        echo "Full-Furnished";
                                                    ?>
                                                </td>
                                            </tr>

                                            <!-- number of room -->
                                            <tr class="<?php if ($myRoom->roomType == 1)
                                                echo "hidden"; ?>">
                                                <td class="detail-title"> No. of Room </td>
                                                <td class="detail-data">
                                                    <?php echo ($myRoom->roomType == 1) ? ($myRoom->bhk + 2) : $myRoom->numberOfRoom; ?>
                                                </td>
                                            </tr>

                                            <!-- floor -->
                                            <tr>
                                                <td class="detail-title"> Floor </td>
                                                <td class="detail-data">
                                                    <?php echo ($myRoom->floor); ?>
                                                </td>
                                            </tr>

                                            <!-- rent -->
                                            <tr>
                                                <td class="detail-title"> Rent </td>
                                                <td class="detail-data">
                                                    <?php echo returnFormattedPrice($myRoom->rentAmount); ?>
                                                </td>
                                            </tr>

                                            <!-- location -->
                                            <tr>
                                                <td class="detail-title"> Location </td>
                                                <td class="detail-data">
                                                    <?php echo ucfirst($myRoom->getLocation($myRoom->houseId)); ?>
                                                </td>
                                            </tr>
                                        </table>

                                        <?php
                                        $house->fetchHouse($myRoom->houseId);
                                        ?>

                                        <!-- house details -->
                                        <div class="house-detail-container flex-column">
                                            <div class="heading-div f-bold p-large">
                                                <p class="p-normal" style="line-height:32px;"> House Photos </p>
                                            </div>

                                            <div class="top-photo-container">
                                                <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[0]['house_photo']; ?>"
                                                    alt="">
                                            </div>

                                            <div class="bottom-photo-container">
                                                <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[1]['house_photo']; ?>"
                                                    alt="">
                                                <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[2]['house_photo']; ?>"
                                                    alt="">
                                                <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[3]['house_photo']; ?>"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="room-not-registered-container <?php if ($task != "my-room")
                        echo "hidden"; ?>">
                        <p class="p-normal negative"> You are not registered to any room currently. </p> <br>
                        <button onclick="window.location.href='home.php'"> Find Room Now </button>
                    </div>
                    <?php
                }
                ?>

                <!-- announcement container -->
                <div class="announcement-container flex-column <?php if ($task != "announcement")
                    echo "hidden"; ?>">
                    <div class="announcement-div flex-column">
                        <!-- heading -->
                        <div class="container flex-column">
                            <p class="p-large heading f-bold"> Announcement </p>
                        </div>

                        <?php
                        if (sizeof($announcementSets) > 0) {
                            foreach ($announcementSets as $announcementSet) {
                                ?>
                                <div class="announcement flex-column announcement-element <?php
                                if ($announcementSet['target'] == 0)
                                    echo "announcement-all-house-element";
                                elseif ($announcementSet['target'] == 1)
                                    echo "announcement-specific-house-element";
                                elseif ($announcementSet['target'] == 2)
                                    echo "announcement-all-room-element";
                                elseif ($announcementSet['target'] == 3)
                                    echo "announcement-specific-room-element";
                                ?>">
                                    <div class="flex-row announcement-target-div">
                                        <p class="p-normal"> Target :
                                            <?php
                                            if ($announcementSet['target'] == 0)
                                                echo "All House";
                                            elseif ($announcementSet['target'] == 1)
                                                echo "Specific House";
                                            elseif ($announcementSet['target'] == 2)
                                                echo "All Rooms";
                                            elseif ($announcementSet['target'] == 3)
                                                echo "Specific Rooms";
                                            ?>
                                        </p>

                                        <!-- extra -->
                                        <p class="p-normal n-light">
                                            <?php
                                            if ($announcementSet['target'] == 1) {
                                                echo "House ID : " . $announcementSet['house_id'];
                                            } elseif ($announcementSet['target'] == 3) {
                                                echo "Room ID : " . $announcementSet['room_id'];
                                            }
                                            ?>
                                        </p>
                                    </div>

                                    <!-- top -->
                                    <div class="announcement-basic flex-row">
                                        <div class="announcement-basic-left flex-column">
                                            <p class="p-form f-bold"> Title :
                                                <?php echo ucfirst($announcementSet['title']); ?>
                                            </p>
                                            <p class="p-small n-light"> Announced date :
                                                <?php echo $announcementSet['announcement_date']; ?>
                                            </p>
                                        </div>

                                        <div class="announcement-basic-right hidden">
                                            <abbr title="Delete">
                                                <?php $link = "#"; ?>
                                                <a href="<?php echo $link; ?>">
                                                    <img src="../../assests/Icons/delete.png" alt="" class="icon-class">
                                                </a>
                                            </abbr>
                                        </div>
                                    </div>

                                    <!-- mid -->
                                    <div class="announcement-detail">
                                        <p class="p-normal">
                                            <?php echo ucfirst($announcementSet['announcement']); ?>
                                        </p>
                                    </div>

                                    <!-- bottom -->
                                    <div class="announcement-operation-div flex-row">
                                        <div class="left-div flex-row">
                                            <div class="like-div flex-row">
                                                <img src="../../assests/Icons/thumbs-up.png" alt="">
                                                <p class="p-form">
                                                    <?php echo '0'; ?>
                                                </p>
                                            </div>

                                            <div class="dislike-div flex-row">
                                                <img src="../../assests/Icons/thumbs-down.png" alt="">
                                                <p class="p-form">
                                                    <?php echo '0'; ?>
                                                </p>
                                            </div>

                                            <div class="comment-div flex-row">
                                                <img src="../../assests/Icons/comment.png" alt="">
                                                <p class="p-form">
                                                    <?php echo '0'; ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="right-div hidden">
                                            <a href=""> View Detail </a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "No announcements yet.";
                            ?>

                            <?php
                        }
                        ?>
                    </div>
                </div>

                <!-- tenancy-history -->
                <div class="flex-column container <?php if ($task != "tenancy-history")
                    echo "hidden"; ?>">
                    <p class="p-large heading f-bold"> Resided Rooms </p>

                    <!-- tenancy history table -->
                    <table class="content-container table-class resided-room-table">
                        <thead>
                            <th class="t- first-td"> S.N. </th>
                            <th class="t-"> Location </th>
                            <th class="t-"> Room </th>
                            <th class="t-"> Room Type </th>
                            <th class="t-"> Furnishing </th>
                            <th class="t-"> Floor </th>
                            <th class="t-"> Rent </th>
                            <th class="t-"> Move In Date </th>
                            <th class="t-"> Move Out Date </th>
                        </thead>

                        <tbody>
                            <?php
                            $tenancyHistorySets = $tenancyHistory->fetchTenancyHistoryOfTenant($_SESSION['tenantUserId']);
                            if (sizeof($tenancyHistorySets) > 0) {
                                $serial = 0;
                                foreach ($tenancyHistorySets as $tenancyHistorySet) {
                                    $room->roomId = $tenancyHistorySet['room_id'];
                                    $room->fetchRoom($room->roomId);
                                    $location = $room->getLocation($room->houseId);
                                    $roomLink = "room-details.php?roomId=" . $room->roomId;
                                    ?>
                                    <tr onclick="window.location.href='<?php echo $roomLink; ?>'">
                                        <!-- serial -->
                                        <td class="t- first-td">
                                            <?php echo ++$serial; ?>
                                        </td>

                                        <!-- location -->
                                        <td class="t-">
                                            <?php echo $location; ?>
                                        </td>

                                        <!-- room -->
                                        <td class="t-">
                                            <?php echo $tenancyHistorySet['room_id']; ?>
                                        </td>

                                        <!-- room type -->
                                        <td class="t-">
                                            <?php echo ($room->roomType == 0) ? "BHK" : "Non-BHK"; ?>
                                        </td>

                                        <!-- furnishing -->
                                        <td class="t-">
                                            <?php
                                            if ($room->furnishing == 0)
                                                echo "Unfurnished";
                                            elseif ($room->furnishing == 1)
                                                echo "Semi-furnished";
                                            else
                                                echo "Full-furnished";
                                            ?>
                                        </td>

                                        <!-- floor -->
                                        <td class="t-">
                                            <?php echo $room->floor; ?>
                                        </td>

                                        <!-- rent -->
                                        <td class="t-">
                                            <?php echo returnFormattedPrice($room->rentAmount); ?>
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

                    <?php
                    if (sizeof($tenancyHistorySets) == 0) {
                        ?>
                        <div class="flex-column empty-data-div" id="empty-tenancy-history-data-div">
                            <img src="../../Assests/Icons/empty.png" alt="">
                            <p class="p-normal negative"> No tenency history found! </p>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <!-- leave room form -->
                <div class="flex-row leave-room-container" id="leave-room-container">
                    <div class="leave-room-div">
                        <form action="" method="POST" class="flex-column leave-room-form">
                            <!-- top-section -->
                            <div class="flex-row top-section">
                                <p class="p-large f-bold"> Leave Room Form </p>
                                <img src="../../Assests/Icons/Cancel-filled.png" alt="" id="leave-room-close">
                            </div>

                            <!-- error message section -->
                            <p class="p-small negative"> Error message appears here... </p>

                            <p class="p-form"> Leaving Date </p>

                            <input type="date" name="leave-room-date" id="leave-room-date" required>

                            <textarea name="leave-room-note" id="leave-room-note" cols="30" rows="10"
                                placeholder="Mentions reason if possible..."></textarea>
                            <button class="negative-button" type="submit" name="leave-room-btn"> Submit </button>
                        </form>
                    </div>
                </div>

                <!-- applied room -->
                <div class="notification-setting-container flex-column <?php if ($task != "applied-room")
                    echo "hidden"; ?>">
                    <?php
                    include_once '../../Class/application_class.php';
                    $applicationObj = new Application();
                    $applicationSets = $applicationObj->fetchAllApplicationsForTenant($user->userId);
                    ?>
                    <!-- heading -->
                    <div class="container flex-column">
                        <p class="p-large heading f-bold"> Applied Room Application </p>
                    </div>

                    <!-- applied room cards -->
                    <div class="flex-row card-container applied-room-card-section">
                        <div class="flex-column pointer card applied-room-card" id="applied-room-card-all">
                            <p class="p-large">
                                <?php echo $applicationObj->countApplicationForTenant($user->userId, "all"); ?>
                            </p>
                            <p class="p-normal"> All </p>
                        </div>

                        <div class="flex-column pointer card applied-room-card" id="applied-room-card-pending">
                            <p class="p-large">
                                <?php echo $applicationObj->countApplicationForTenant($user->userId, "pending"); ?>
                            </p>
                            <p class="p-normal"> Pending </p>
                        </div>

                        <div class="flex-column pointer card applied-room-card" id="applied-room-card-accepted">
                            <p class="p-large">
                                <?php echo $applicationObj->countApplicationForTenant($user->userId, "accepted"); ?>
                            </p>
                            <p class="p-normal"> Accepted </p>
                        </div>

                        <div class="flex-column pointer card applied-room-card" id="applied-room-card-rejected">
                            <p class="p-large">
                                <?php echo $applicationObj->countApplicationForTenant($user->userId, "rejected"); ?>
                            </p>
                            <p class="p-normal"> Rejected </p>
                        </div>
                    </div>

                    <!-- applied room filter -->
                    <div class="container content-container flex-row filter-search-container">
                        <div class="flex-row filter-div">
                            <div class="flex-row filter-icon-div ">
                                <img src="../../Assests/Icons/filter.png" alt="">
                            </div>

                            <div class="flex-row filter-select-div application-type-select-div">
                                <label> Application Type </label>
                                <select name="application-type-select" id="applied-room-application-type-select">
                                    <option value="0"> All </option>
                                    <option value="1"> Pending </option>
                                    <option value="2"> Accepted </option>
                                    <option value="3"> Rejected </option>
                                </select>
                            </div>

                            <div class="flex-row pointer clear-filter-div" id="applied-room-clear-sort">
                                <p class="p-form"> Clear Sort </p>
                                <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                            </div>
                        </div>
                    </div>

                    <!-- applied room table -->
                    <div class="content-container table-container">
                        <table class="table-class" id="applied-room-table">
                            <thead>
                                <th class="first-td"> S.N. </th>
                                <th class="t-room-id"> Room ID </th>
                                <th class="t-location"> Location </th>
                                <th class="t-room-spec"> Room Spec </th>
                                <th class="t-rent-type"> Rent Type </th>
                                <th class="t-move-in-date"> Move In Date </th>
                                <th class="t-move-out-date"> Move Out Date </th>
                                <th class="t-note"> Note </th>
                                <th class="t-state"> State </th>
                                <th class="t-date"> Date </th>
                            </thead>

                            <tbody>
                                <?php
                                $serial = 1;
                                foreach ($applicationSets as $applicationSet) {
                                    $room->fetchRoom($applicationSet['room_id']);
                                    $location = $room->getLocation($room->houseId);
                                    ?>
                                    <tr class="<?php echo "applied-room-element ";
                                    if ($applicationSet['state'] == 0)
                                        echo "applied-room-pending-element";
                                    elseif ($applicationSet['state'] == 1)
                                        echo "applied-room-accepted-element";
                                    else
                                        echo "applied-room-rejected-element"; ?>"
                                        onclick="window.location.href='room-details.php?roomId=<?php echo $applicationSet['room_id']; ?>'">
                                        <td class="t-room-id first-td">
                                            <?php echo $serial++; ?>
                                        </td>
                                        <td>
                                            <?php echo $applicationSet['room_id']; ?>
                                        </td>
                                        <td class="t-location">
                                            <?php echo $location; ?>
                                        </td>
                                        <td class="t-room-spec">
                                            <?php
                                            echo ($room->roomType == 1) ? "BHK, " : "Non-BHK, ";
                                            echo "Floor: " . $room->floor;
                                            ?>
                                        </td>
                                        <td class="t-rent-type">
                                            <?php echo ucfirst($applicationSet['rent_type']); ?>
                                        </td>
                                        <td class="t-move-in-date">
                                            <?php echo $applicationSet['move_in_date']; ?>
                                        </td>
                                        <td class="t-move-out-date">
                                            <?php echo ($applicationSet['move_out_date'] != "0000-00-00") ? $applicationSet['move_out_date'] : "-"; ?>
                                        </td>
                                        <td class="t-note">
                                            <?php echo ucfirst($applicationSet['note']); ?>
                                        </td>
                                        <td class="t-state">
                                            <?php
                                            if ($applicationSet['state'] == 0)
                                                echo "Pending";
                                            elseif ($applicationSet['state'] == 1)
                                                echo "Accepted";
                                            elseif ($applicationSet['state'] == 2)
                                                echo "Rejected";
                                            else
                                                echo "Others";

                                            if ($applicationSet['cancel_count'] > 0)
                                                echo "/ Previously cancelled";
                                            ?>
                                        </td>
                                        <td class="t-date">
                                            <?php echo $applicationSet['application_date']; ?>
                                        </td>
                                    </tr>

                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex-column empty-data-div" id="empty-applied-room-data-div">
                        <img src="../../Assests/Icons/empty.png" alt="">
                        <p class="p-normal negative"> You haven't applied for any room yet! </p>
                    </div>
                </div>

                <!-- custom room -->
                <div class="notification-setting-container flex-column <?php if ($task != "custom-room")
                    echo "hidden"; ?>">
                    <?php
                    include '../../Class/custom_room_class.php';
                    $customObj = new CustomRoomApplication();
                    $customSets = $customObj->fetchAllCustomRoomApplicationTenant($user->userId);
                    $serial = 1;
                    ?>
                    <!-- heading -->
                    <div class="container flex-column">
                        <p class="p-large heading f-bold"> Custom Room Application </p>
                    </div>

                    <!-- cards -->
                    <div class="flex-row card-container custom-room-card-section">
                        <div class="flex-column pointer card custom-room-card" id="custom-room-card-all">
                            <p class="p-large">
                                <?php echo $customObj->countCustomApplication("all", $user->userId); ?>
                            </p>
                            <p class="p-normal"> Total </p>
                        </div>

                        <div class="flex-column pointer card custom-room-card" id="custom-room-card-unserved">
                            <p class="p-large">
                                <?php echo $customObj->countCustomApplication("unserved", $user->userId); ?>
                            </p>
                            <p class="p-normal"> Unserved </p>
                        </div>

                        <div class="flex-column pointer card custom-room-card" id="custom-room-card-served">
                            <p class="p-large">
                                <?php echo $customObj->countCustomApplication("served", $user->userId); ?>
                            </p>
                            <p class="p-normal"> Served </p>
                        </div>
                    </div>

                    <!-- custom room filter -->
                    <div class="container content-container flex-row filter-search-container">
                        <div class="flex-row filter-div">
                            <div class="flex-row filter-icon-div ">
                                <img src="../../Assests/Icons/filter.png" alt="">
                            </div>

                            <div class="flex-row filter-select-div application-type-select-div">
                                <label> Application Type </label>
                                <select name="application-type-select" id="custom-room-application-type-select">
                                    <option value="0"> All </option>
                                    <option value="1"> Unserved </option>
                                    <option value="2"> Served </option>
                                </select>
                            </div>

                            <div class="flex-row pointer clear-filter-div" id="custom-room-clear-sort">
                                <p class="p-form"> Clear Sort </p>
                                <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                            </div>
                        </div>
                    </div>

                    <!-- custom room table -->
                    <div class="content-container table-container">
                        <table id="custom-room-table">
                            <thead>
                                <th class="first-td"> S.N. </th>
                                <th> Location </th>
                                <th> Room Type </th>
                                <th> Furnishing </th>
                                <th> Min Rent </th>
                                <th> Max Rent </th>
                                <th> State </th>
                                <th> Date </th>
                            </thead>

                            <tbody>
                                <?php
                                foreach ($customSets as $customSet) {
                                    ?>
                                    <tr
                                        class="<?php echo 'custom-room-element ';
                                        echo ($customSet == 0) ? "custom-room-unserved-element" : "custom-room-served-element"; ?>">
                                        <td class="first-td">
                                            <?php echo $serial++; ?>
                                        </td>
                                        <td>
                                            <?php echo ucfirst($customSet['area_name']) . ', ' . returnArrayValue("district", $customSet['district']); ?>
                                        </td>
                                        <td>
                                            <?php echo ($customSet['room_type'] == 1) ? "BHK" : "Non-BHK"; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($customSet['furnishing'] == 0)
                                                echo "Unfurnished";
                                            elseif ($customSet['furnishing'] == 1)
                                                echo "Semi-furnished";
                                            else
                                                echo "Fully-furnished";
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo returnFormattedPrice($customSet['min_rent']); ?>
                                        </td>
                                        <td>
                                            <?php echo returnFormattedPrice($customSet['max_rent']); ?>
                                        </td>
                                        <td>
                                            <?php echo ($customSet['state'] == 0) ? "Unserved" : "Served"; ?>
                                        </td>
                                        <td>
                                            <?php echo $customSet['date']; ?>
                                        </td>
                                    </tr>

                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex-column empty-data-div" id="empty-custom-room-data-div">
                        <img src="../../Assests/Icons/empty.png" alt="">
                        <p class="p-normal negative"> You haven't applied for any custom room! </p>
                    </div>
                </div>

                <!-- my voice -->
                <div class="flex-column notification-setting-container my-voice-container <?php if ($task != "my-voice")
                    echo "hidden"; ?>">
                    <div class="container flex-column">
                        <p class="p-large heading f-bold"> My Voices </p>
                    </div>

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

                    <!-- my voice filter cards -->
                    <div class="container content-container flex-row filter-search-container">
                        <div class="flex-row filter-div">
                            <div class="flex-row filter-icon-div ">
                                <img src="../../Assests/Icons/filter.png" alt="">
                            </div>

                            <div class="flex-row filter-select-div my-voice-type-select-div">
                                <label> Type </label>
                                <select name="my-voice-type-select" id="my-voice-type-select">
                                    <option value="0"> All </option>
                                    <option value="1"> Unsolved </option>
                                    <option value="2"> Solved </option>
                                </select>
                            </div>

                            <div class="flex-row pointer clear-filter-div" id="my-voice-clear-sort">
                                <p class="p-form"> Clear Sort </p>
                                <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class">
                            </div>
                        </div>
                    </div>

                    <?php
                    $myTenantVoiceSets = $tenantVoice->fetchMyTenantVoice($user->userId);
                    if (sizeof($myTenantVoiceSets) > 0) {
                        ?>
                        <div class="content-container my-voice-table-container">
                            <table class="table-class my-voice-table" id="my-voice-table">
                                <thead>
                                    <th class="t- first-td"> S.N. </th>
                                    <th class="t-"> Room ID </th>
                                    <th class="t-"> Issue </th>
                                    <th class="t-"> Issue SOlved Date </th>
                                    <th class="t-"> Issue State </th>
                                    <th class="t-"> Date </th>
                                </thead>

                                <tbody>
                                    <?php
                                    $serial = 1;
                                    foreach ($myTenantVoiceSets as $myTenantVoiceSet) {
                                        $voiceId = $myTenantVoiceSet['tenant_voice_id'];
                                        $link = "my-voice.php?voiceId=$voiceId";
                                        ?>
                                        <tr onclick="window.location.href='<?php echo $link; ?>'"
                                            class="my-voice-element <?php echo ($myTenantVoiceSet['issue_state'] == 0) ? 'my-voice-unsolved-element' : 'my-voice-solved-element'; ?>">
                                            <td class="t- first-td">
                                                <?php echo $serial++; ?>
                                            </td>
                                            <td class="t-">
                                                <?php echo $myTenantVoiceSet['room_id']; ?>
                                            </td>
                                            <td class="t-">
                                                <?php echo ucfirst($myTenantVoiceSet['voice']); ?>
                                            </td>

                                            <td>
                                                <?php echo ($myTenantVoiceSet['issue_state'] == 0) ? " - " : $myTenantVoiceSet['issue_solved_date']; ?>
                                            </td>

                                            <td class="t-">
                                                <?php echo ($myTenantVoiceSet['issue_state'] == 0) ? "Unsolved" : "Solved"; ?>
                                            </td>
                                            <td class="t-">
                                                <?php echo $myTenantVoiceSet['date']; ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="flex-column empty-data-div <?php if (sizeof($myTenantVoiceSets) > 0)
                            echo "hidden"; ?>" id="empty-my-voice-data-div">
                            <img src="../../Assests/Icons/empty.png" alt="">
                            <p class="p-normal negative"> You haven't raised any voice! </p>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- convey issue -->
    <div class="flex-column container content-container convey-issue-container" id="convey-issue-container">
        <div class="div convey-issue-div">
            <div class="flex-row top-section">
                <p class="p-large"> Report Your Issues to the Landlord </p>
                <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="icon-class pointer"
                    id="convey-issue-close">
            </div>

            <form method="POST" class="flex-column convey-issue-form" autocomplete="off">
                <textarea name="convey-issue-issue" id="convey-issue-issue" cols="30" rows="0"
                    placeholder="Write your issues here..." required></textarea>

                <button type="submit" name="convey-issue-btn" class="negative-button flex-row">
                    <p class="p-normal"> Report </p>
                    <img src="../../Assests/Icons/send-white.png" alt="" class="icon-class">
                </button>
            </form>
        </div>
    </div>

    <div id="dark-background"> </div>

    <!-- js section -->
    <script>
        var passwordVisibility = false;
        const passwordBox1 = document.getElementById("password-old");
        const passwordBox2 = document.getElementById("password-new-1");
        const passwordBox3 = document.getElementById("password-new-2");
        const passwordToggleLabel = document.getElementById("show-password-label");

        deactivateAccount = () => {
            var choice = confirm("Do you want to deactivate your acount?");
            if (choice)
                window.location.href = '';
        }

        togglePassword = () => {
            passwordVisibility = !passwordVisibility;
            if (passwordVisibility) {
                passwordBox1.type = "text";
                passwordBox2.type = "text";
                passwordBox3.type = "text";
                passwordToggleLabel.innerText = "Hide password";
            } else {
                passwordBox1.type = "password";
                passwordBox2.type = "password";
                passwordBox3.type = "password";
                passwordToggleLabel.innerText = "Show password";
            }
        }

        function submitNewUserForm(event) {
            event.preventDefault();
            var firstName = document.getElementById('first-name');
            var lastName = document.getElementById('last-name');
            var areaName = document.getElementById('area-name');
            var contact = document.getElementById('contact');

            if (isEmpty(firstName) || isEmpty(lastName) || isEmpty(areaName) || isEmpty(contact)) {
                console.log("Please enter all the details first.");
            } else {
                document.getElementById("new-user-form").submit();
            }
        }

        // Trim removes leading and trailing whitespaces
        function isEmpty(inputElement) {
            return inputElement.value.trim() === '';
        }
    </script>

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
    </script>

    <!-- custom room js -->
    <script>
        // cards
        const customRoomCardAll = $('#custom-room-card-all');
        const customRoomCardUnserved = $('#custom-room-card-unserved');
        const customRoomCardServed = $('#custom-room-card-served');
        const customRoomApplicationSelect = $('#custom-room-application-type-select');
        const customRoomClearSort = $('#custom-room-clear-sort');

        const emptyCustomRoomDataDiv = $('#empty-custom-room-data-div');

        var customRoomElements = $('.custom-room-element');
        var customRoomUnservedElements = $('.custom-room-unserved-element');
        var customRoomServedElements = $('.custom-room-served-element');


        var customRoomType = 0;
        customRoomClearSort.hide();
        emptyCustomRoomDataDiv.hide();

        customRoomCardAll.click(function () {
            customRoomType = 0;
            customRoomApplicationSelect[0].value = customRoomType;
            filterCustomRoomApplication();
        });

        customRoomCardUnserved.click(function () {
            customRoomType = 1;
            customRoomApplicationSelect[0].value = customRoomType;
            filterCustomRoomApplication();
        });

        customRoomCardServed.click(function () {
            customRoomType = 2;
            customRoomApplicationSelect[0].value = customRoomType;
            filterCustomRoomApplication();
        });

        customRoomApplicationSelect.change(function () {
            customRoomType = customRoomApplicationSelect.val();
            filterCustomRoomApplication();
        });

        filterCustomRoomApplication = () => {
            if (customRoomType != 0)
                customRoomClearSort.show();
            else
                customRoomClearSort.hide();

            customRoomElements.hide();

            if (customRoomType == 0)
                customRoomElements.show();
            else if (customRoomType == 1)
                customRoomServedElements.show();
            else
                customRoomUnservedElements.show();

            if (countVisibleCustomRoomRows() == 0)
                emptyCustomRoomDataDiv.show();
            else
                emptyCustomRoomDataDiv.hide();
        }

        countVisibleCustomRoomRows = () => {
            var visibleRows = $("#custom-room-table tbody tr:visible");
            var visibleRowCount = visibleRows.length;
            return visibleRowCount;
        }

        customRoomClearSort.click(function () {
            customRoomType = 0;
            customRoomApplicationSelect[0].value = customRoomType;
            filterCustomRoomApplication();
        });

        filterCustomRoomApplication();
    </script>

    <!-- applied room js -->
    <script>
        // cards
        const appliedRoomCardAll = $('#applied-room-card-all');
        const appliedRoomCardPending = $('#applied-room-card-pending');
        const appliedRoomCardAccepted = $('#applied-room-card-accepted');
        const appliedRoomCardRejected = $('#applied-room-card-rejected');

        const appliedRoomApplicationSelect = $('#applied-room-application-type-select');
        const appliedRoomClearSort = $('#applied-room-clear-sort');

        const emptyAppliedRoomDataDiv = $('#empty-applied-room-data-div');

        var appliedRoomElements = $('.applied-room-element');
        var appliedRoomPendingElements = $('.applied-room-pending-element');
        var appliedRoomAcceptedElements = $('.applied-room-accepted-element');
        var appliedRoomRejectedElements = $('.applied-room-rejected-element');


        var appliedRoomType = 0;
        appliedRoomClearSort.hide();
        emptyAppliedRoomDataDiv.hide();

        appliedRoomCardAll.click(function () {
            appliedRoomType = 0;
            appliedRoomApplicationSelect[0].value = appliedRoomType;
            filterappliedRoomApplication();
        });

        appliedRoomCardPending.click(function () {
            appliedRoomType = 1;
            appliedRoomApplicationSelect[0].value = appliedRoomType;
            filterappliedRoomApplication();
        });

        appliedRoomCardAccepted.click(function () {
            appliedRoomType = 2;
            appliedRoomApplicationSelect[0].value = appliedRoomType;
            filterappliedRoomApplication();
        });

        appliedRoomCardRejected.click(function () {
            appliedRoomType = 3;
            appliedRoomApplicationSelect[0].value = appliedRoomType;
            filterappliedRoomApplication();
        });

        appliedRoomApplicationSelect.change(function () {
            appliedRoomType = appliedRoomApplicationSelect.val();
            filterappliedRoomApplication();
        });

        filterappliedRoomApplication = () => {
            if (appliedRoomType != 0)
                appliedRoomClearSort.show();
            else
                appliedRoomClearSort.hide();

            appliedRoomElements.hide();

            if (appliedRoomType == 0)
                appliedRoomElements.show();
            else if (appliedRoomType == 1)
                appliedRoomPendingElements.show();
            else if (appliedRoomType == 2)
                appliedRoomAcceptedElements.show();
            else
                appliedRoomRejectedElements.show();

            if (countVisibleAppliedRoomRows() == 0)
                emptyAppliedRoomDataDiv.show();
            else
                emptyAppliedRoomDataDiv.hide();
        }

        countVisibleAppliedRoomRows = () => {
            var visibleRows = $("#applied-room-table tbody tr:visible");
            var visibleRowCount = visibleRows.length;
            return visibleRowCount;
        }

        appliedRoomClearSort.click(function () {
            appliedRoomType = 0;
            appliedRoomApplicationSelect[0].value = appliedRoomType;
            filterappliedRoomApplication();
        });

        filterappliedRoomApplication();
    </script>

    <!-- leave room script -->
    <script>
        $darkBackground = $('#dark-background');
        $leaveRoomContainer = $('#leave-room-container');
        $leaveRoomTrigger = $('#leave-room-trigger');
        $leaveRoomClose = $('#leave-room-close');

        $darkBackground.hide();
        $leaveRoomContainer.hide();

        // showing leave room form
        $leaveRoomTrigger.click(function () {
            $darkBackground.show();
            $leaveRoomContainer.show();
        });

        // closing leave room form
        $leaveRoomClose.click(function () {
            $darkBackground.hide();
            $leaveRoomContainer.hide();
        });
    </script>

    <!-- convey issue js section -->
    <script>
        const darkBackground = $('#dark-background');
        const conveyIssueForm = $('#convey-issue-container');
        const conveyIssueTrigger = $('#convey-issue-trigger');
        const conveyIssueClose = $('#convey-issue-close');

        darkBackground.hide();
        conveyIssueForm.hide();

        // darkBackground.show();
        // conveyIssueForm.show();

        conveyIssueClose.click(function () {
            darkBackground.hide();
            conveyIssueForm.hide();
        });

        conveyIssueTrigger.click(function () {
            darkBackground.show();
            conveyIssueForm.show();
        });
    </script>

    <!-- my voice : page js section -->
    <script>
        // cards
        const myVoiceCardAll = $('#my-voice-card-all');
        const myVoiceCardPending = $('#my-voice-card-pending');
        const myVoiceCardSolved = $('#my-voice-card-solved');

        const myVoiceTypeSelect = $('#my-voice-type-select');
        const myVoiceClearSort = $('#my-voice-clear-sort');

        const emptyMyVoiceDataDiv = $('#empty-my-voice-data-div');

        var myVoiceElements = $('.my-voice-element');
        var myVoiceUnsolvedElements = $('.my-voice-unsolved-element');
        var myVoiceSolvedElements = $('.my-voice-solved-element');


        var myVoiceType = 0;
        myVoiceClearSort.hide();
        emptyMyVoiceDataDiv.hide();

        myVoiceCardAll.click(function () {
            myVoiceType = 0;
            myVoiceTypeSelect[0].value = myVoiceType;
            filterMyVoiceApplication();
        });

        myVoiceCardPending.click(function () {
            myVoiceType = 1;
            myVoiceTypeSelect[0].value = myVoiceType;
            filterMyVoiceApplication();
        });

        myVoiceCardSolved.click(function () {
            myVoiceType = 2;
            myVoiceTypeSelect[0].value = myVoiceType;
            filterMyVoiceApplication();
        });

        myVoiceTypeSelect.change(function () {
            myVoiceType = myVoiceTypeSelect.val();
            filterMyVoiceApplication();
        });

        filterMyVoiceApplication = () => {
            if (myVoiceType != 0)
                myVoiceClearSort.show();
            else
                myVoiceClearSort.hide();

            myVoiceElements.hide();

            if (myVoiceType == 0)
                myVoiceElements.show();
            else if (myVoiceType == 1)
                myVoiceUnsolvedElements.show();
            else
                myVoiceSolvedElements.show();

            if (countVisibleMyVoiceRows() == 0)
                emptyMyVoiceDataDiv.show();
            else
                emptyMyVoiceDataDiv.hide();
        }

        countVisibleMyVoiceRows = () => {
            var visibleRows = $("#my-voice-table tbody tr:visible");
            var visibleRowCount = visibleRows.length;
            return visibleRowCount;
        }

        myVoiceClearSort.click(function () {
            myVoiceType = 0;
            myVoiceTypeSelect[0].value = myVoiceType;
            filterMyVoiceApplication();
        });

        filterMyVoiceApplication();
    </script>
</body>

</html>