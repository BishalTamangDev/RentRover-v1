<?php

// starting session
if (!session_start())
    session_start();

    // redirecting to login page 
if (!isset($_SESSION['landlordUserId']))
header("Location: login.php");

// including files
include_once '../../Class/user_class.php';
include_once '../../Class/functions.php';
include_once '../../Class/notification_class.php';
include_once '../../Class/custom_room_class.php';
include_once '../../Class/house_class.php';

// updateHouseNotification();

if (!isset($_GET['houseId']))
    header("Location: myhouse.php");
else {
    if ($_GET['houseId'] != '')
        $houseId = $_GET['houseId'];
    else
        header("Location: myhouse.php");
}

// creating objects
$user = new User();
$house = new House();
$room = new Room();
$customRoomApplication = new CustomRoomApplication();

$user->userId = $_SESSION['landlordUserId'];
$user->fetchSpecificRow($user->userId);

$house->houseId = $houseId;
$house->fetchHouse($houseId);

$roomPhoto1 = 'NULL';
$roomPhoto2 = 'NULL';
$roomPhoto3 = 'NULL';
$roomPhoto4 = 'NULL';

// upload file destination
$roomPhotoDestination = "../../assests/uploads/room/";

$amenityArray = [];
$amenityArray_sr = 'NULL';

// on form submission
$errorMessageState = false;
$errorMessage = "This is error message";

$immediateRoomId = 0;

// after the form has been submitted
$submissionState = "unknown";
if (isset($_GET['submission'])) {
    if ($_GET['submission'] != '')
        $submissionState = $_GET['submission'];
    else
        $submissionState = "unknown";
}

// on form submission
if (isset($_POST['submit-room'])) {
    $submitted = true;

    global $roomPhoto1;
    global $roomPhoto2;
    global $roomPhoto3;
    global $roomPhoto4;
    
    global $immediateRoomId;

    global $amenityArray;
    global $amenityArray_sr;

    $formValid = true;

    // retriving the form values
    $rent = $_POST['rent'];
    $floor = $_POST['floor'];
    $roomNumber = $_POST['room-number'];
    $numberOfRoom = $_POST['number-of-room'];

    $requirement = (isset($_POST['requirement'])) ? $_POST['requirement'] : "NULL";

    if ($_POST['room-type'] == 0) {
        $errorMessageState = true;
        $errorMessage = "Select the room type.";
    } else {
        // room type is set
        $roomType = $_POST['room-type'];

        if ($roomType == 1) {
            if (isset($_POST['bhk'])) {
                $bhk = $_POST['bhk'];
            } else {
                $errorMessageState = true;
                $errorMessage = "Enter the bhk.";
                $formValid = false;
            }
        } else if ($roomType == 2) {
            if (isset($_POST['number-of-room'])) {
                $numberOfRoom = $_POST['number-of-room'];
            } else {
                $errorMessageState = true;
                $errorMessage = "Enter the number of room.";
                $formValid = false;
            }
        }

        // furnishing
        if ($formValid) {
            if ($_POST['furnishing'] == 0) {
                $errorMessageState = true;
                $errorMessage = "Please select the furnighing type.";
                $formValid = false;
            } else {
                $furnishing = $_POST['furnishing'];
            }
        }

        if ($formValid) {
            // amenity
            for ($i = 0; $i < 12; $i++) {
                $id = 'amenity-checkbox-' . $i;
                if (isset($_POST[$id]))
                    array_push($amenityArray, $i);
            }

            $amenityArray_sr = base64_encode(serialize($amenityArray));

            // photos
            $photoFileValid1 = fileValidityCheck($_FILES['room-photo-1']);
            $photoFileValid2 = fileValidityCheck($_FILES['room-photo-2']);
            $photoFileValid3 = fileValidityCheck($_FILES['room-photo-3']);
            $photoFileValid4 = fileValidityCheck($_FILES['room-photo-4']);

            $roomPhoto1 = $_FILES['room-photo-1']['name'];
            $roomPhoto2 = $_FILES['room-photo-2']['name'];
            $roomPhoto3 = $_FILES['room-photo-3']['name'];
            $roomPhoto4 = $_FILES['room-photo-4']['name'];

            if ($photoFileValid1 && $photoFileValid2 && $photoFileValid3 && $photoFileValid4) {
                uploadFile("roomPhoto1", $_FILES['room-photo-1']);
                uploadFile("roomPhoto2", $_FILES['room-photo-2']);
                uploadFile("roomPhoto3", $_FILES['room-photo-3']);
                uploadFile("roomPhoto4", $_FILES['room-photo-4']);

                $room->roomPhoto1 = $roomPhoto1;
                $room->roomPhoto2 = $roomPhoto2;
                $room->roomPhoto3 = $roomPhoto3;
                $room->roomPhoto4 = $roomPhoto4;

                $isApproved = false;
                $registerDate = date('Y-m-d H:i:s');
                $isAcquired = 0;
                $roomState = 1;
                $tenantId = 0;

                $room->setRoom($houseId, $roomNumber, $rent, $roomType, $furnishing, $bhk, $numberOfRoom, $floor, $amenityArray_sr, $requirement, $isAcquired, $tenantId, $roomState, $registerDate);
                $immediateRoomId = $room->registerRoom();

                if($immediateRoomId != 0){
                    // adding room photos
                    $room->addRoomPhoto($immediateRoomId);

                    // create notification
                    $userNotification = new Notification();
                    $adminNotification = new Notification();

                    $userNotification->setRoomNotification(0, 'landlord', $_SESSION['landlordUserId'], $immediateRoomId);
                    $adminNotification->setRoomNotification(0, 'admin', $_SESSION['landlordUserId'], $immediateRoomId);

                    $userNotificationState = $userNotification->register();
                    $adminNotificationState = $adminNotification->register();

                    // custom room application check
                    $customRoomApplicationSet = $customRoomApplication->checkForCustomRoomApplication($house->district, $house->areaName, $roomType, $rent, $furnishing);

                    if(sizeof($customRoomApplicationSet) != 0){
                        // $immediateRoomId
                        $customRoomNotification = new Notification();
                        $dateTime = date('Y-m-d H:i:s');
                        foreach($customRoomApplicationSet as $set){
                            $customRoomNotification->setCustomRoomNotification($set ,$immediateRoomId, $dateTime);
                            $customRoomNotificationState = $customRoomNotification->register();
                        }
                    }

                    $link = $_SERVER['REQUEST_URI']."&submission=success";
                    header('location: '.$link);
                }else{
                    $link = $_SERVER['REQUEST_URI']."&submission=failure";
                    header('location: '.$link);
                }
            }
        }
    }
}

function fileValidityCheck($formFile)
{
    global $errorMessage;
    global $errorMessageState;
    // global $roomPhotoArray;

    $fileValid = true;

    $fileName = $formFile['name'];
    // $fileTmpName = $formFile['tmp_name'];
    $fileSize = $formFile['size'];
    $fileError = $formFile['error'];
    // $fileType = $formFile['type'];

    // error check
    if ($fileError) {
        $fileValid = false;
        $errorMessageState = true;
        $errorMessage = "Error in uploading the file. Make sure you selected the image file that is less than or equal to 2MB.";
    } else {
        // size check
        if ($fileSize >= 2087152) {
            $fileValid = false;
            $errorMessageState = true;
            $errorMessage = "File size is too big.";
        } else {
            // extension extraction
            $fileTempExtension = explode('.', $fileName);
            $fileExtension = strtolower(end($fileTempExtension));
            $allowedExtension = array('jpg', 'jpeg', 'png');

            if (!in_array($fileExtension, $allowedExtension)) {
                $fileValid = false;
                $errorMessageState = true;
                $errorMessage = "Invalid file format.";
            }
        }
    }
    return $fileValid;
}

function uploadFile($fileCategory, $formFile)
{
    global $roomPhoto1;
    global $roomPhoto2;
    global $roomPhoto3;
    global $roomPhoto4;

    global $roomPhotoDestination;

    $fileName = $formFile['name'];
    $fileTmpName = $formFile['tmp_name'];

    // extension extraction
    $fileTempExtension = explode('.', $fileName);
    $fileExtension = strtolower(end($fileTempExtension));

    $newFileName = uniqid('', true) . "." . $fileExtension;

    // setting destination
    if ($fileCategory == "roomPhoto1")
        $roomPhoto1 = $newFileName;
    elseif ($fileCategory == "roomPhoto2")
        $roomPhoto2 = $newFileName;
    elseif ($fileCategory == "roomPhoto3")
        $roomPhoto3 = $newFileName;
    elseif ($fileCategory == "roomPhoto4")
        $roomPhoto4 = $newFileName;

    $roomPhotoDestinaltion = $roomPhotoDestination . $newFileName;
    move_uploaded_file($fileTmpName, $roomPhotoDestinaltion);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- css -->
    <!-- <link rel="stylesheet" href="../../CSS/common/style.css"> -->
    <link rel="stylesheet" href="../../CSS/common/table.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/landlord/myhouse.css">
    <link rel="stylesheet" href="../../CSS/landlord/add-room.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- title -->
    <title> Add Room </title>

    <!-- js script -->
    <script src="../../Js/main.js"></script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="body-container flex-row">
        <!-- empty side bar -->
        <div class="empty-section"> </div>

        <!-- main content section -->
        <article class="content-article flex-column">
            <!-- heading -->
            <div class="heading-div flex-column">
                <div class="content-container flex-row section-heading-container">
                    <p class="f-bold negative"> Room Registration </p>

                    <a href="<?php echo $_SERVER['REQUEST_URI'];?>">
                        <button> Reset </button>
                    </a>
                </div>

                <?php
                if ($errorMessageState) {
                    ?>
                    <p class="p-negative negative" style="margin-top: 12px;">
                        <?php
                        echo $errorMessage;
                        ?>
                    </p>
                    <?php
                }
                ?>
            </div>

            <!-- form -->
            <div class="add-room-form-container flex-row">
                <form action="" method="POST" enctype="multipart/form-data" class="room-registration-form flex-column"
                    id="registration-form" autocomplete="on">
                    <div class="room-number-div flex-row">
                        <div class="left flex-column">
                            <p class="p-normal f-bold n-light"> Room Number </p>
                        </div>

                        <div class="right">
                            <input type="text" name="room-number" id="" onkeypress="avoidMistake('integer')" value="<?php if (isset($_POST['room-number']))
                                echo $_POST['room-number']; ?>" required>
                        </div>
                    </div>

                    <!-- room type -->
                    <div class="room-type-div flex-row">
                        <div class="left flex-column">
                            <p class="p-normal f-bold n-light"> Room Type </p>
                        </div>

                        <div class="right flex-column">
                            <select name="room-type" id="">
                                <option value="0" selected hidden> Room Type </option>
                                <?php
                                if (isset($_POST['room-type']) && $_POST['room-type'] != 0) {
                                    if ($_POST['room-type'])
                                        echo '<option value="1" selected hidden> BHK </option>';
                                    else
                                        echo '<option value="2" selected hidden> Non BHK </option>';
                                }
                                ?>
                                <option value="1"> BHK </option>
                                <option value="2"> Non BHK </option>
                            </select>
                        </div>
                    </div>

                    <!-- bhk + number of room -->
                    <div class="bhk-div flex-row">
                        <div class="left"> </div>
                        <div class="right flex-row">
                            <input type="text" name="bhk" id="" placeholder="BHK" onkeypress="avoidMistake('integer')"
                                value="<?php if (isset($_POST['bhk']))
                                    echo $_POST['bhk']; ?>">
                            or
                            <input type="text" name="number-of-room" id="" placeholder="Number of rooms"
                                onkeypress="avoidMistake('integer')" value="<?php if (isset($_POST['number-of-room']))
                                    echo $_POST['number-of-room']; ?>">
                        </div>
                    </div>

                    <!-- furnishing -->
                    <div class="furnishing-div flex-row">
                        <div class="left flex-column">
                            <p class="p-normal f-bold n-light"> Furnishing </p>
                        </div>

                        <div class="right">
                            <select name="furnishing" id="">
                                <option value="0" selected hidden> Furnishing </option>
                                <?php
                                if (isset($_POST['furnishing']) && $_POST['furnishing'] != 0) {
                                    if ($_POST['furnishing'] == 1)
                                        echo '<option value="1" selected hidden> Unfurnished </option>';
                                    elseif ($_POST['furnishing'] == 2)
                                        echo '<option value="2" selected hidden> Semi-furnished </option>';
                                    else
                                        echo '<option value="3" selected hidden> Full-furnished </option>';
                                }
                                ?>
                                <option value="1"> Unfurnished </option>
                                <option value="2"> Semi-furnished </option>
                                <option value="3"> Full-furnished </option>
                            </select>
                        </div>
                    </div>

                    <!-- rent amount -->
                    <div class="rent-div flex-row">
                        <div class="left flex-column">
                            <p class="p-normal f-bold n-light"> Rent Amount </p>
                        </div>

                        <div class="right">
                            <input type="text" name="rent" id="" placeholder="NRs." onkeypress="avoidMistake('float')"
                                value="<?php if (isset($_POST['rent']))
                                    echo $_POST['rent']; ?>" required>
                        </div>
                    </div>

                    <!-- floor -->
                    <div class="floor-div flex-row">
                        <div class="left flex-column">
                            <p class="p-normal f-bold n-light"> Floor </p>
                        </div>

                        <div class="right">
                            <input type="text" name="floor" id="" onkeypress="avoidMistake('integer')" value="<?php if (isset($_POST['floor']))
                                echo $_POST['floor']; ?>" required>
                        </div>
                    </div>

                    <!-- photos -->
                    <div class="photo-container flex-column">
                        <p class="p-normal f-bold n-light"> Room Photos </p>

                        <div class="photo-div">
                            <div class="photo flex-column">
                                <label for="room-photo-1" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                        alt=""> Photo 1 </label>
                                <input type="file" name="room-photo-1" id="room-photo-1" accept=".jpeg, .jpg, .png"
                                    required>
                            </div>

                            <div class="photo flex-column">
                                <label for="room-photo-2" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                        alt=""> Photo 2 </label>
                                <input type="file" name="room-photo-2" id="room-photo-2" accept=".jpeg, .jpg, .png"
                                    required>
                            </div>

                            <div class="photo flex-column">
                                <label for="room-photo-3" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                        alt=""> Photo 3 </label>
                                <input type="file" name="room-photo-3" id="room-photo-3" accept=".jpeg, .jpg, .png"
                                    required>
                            </div>

                            <div class="photo flex-column">
                                <label for="room-photo-4" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                        alt=""> Photo 4 </label>
                                <input type="file" name="room-photo-4" id="room-photo-4" accept=".jpeg, .jpg, .png"
                                    required>
                            </div>
                        </div>

                        <p class="p-form negative">
                            Upload 5 photos of the room and those photos are clear and should help in recognizing the
                            room.
                        </p>
                    </div>

                    <!-- amenity -->
                    <div class="amenity-container flex-column">
                        <p class="p-normal f-bold n-light"> Amenities </p>

                        <div class="amenity-div">
                            <?php
                            $houseAmenities = unserialize(base64_decode($house->allAmenities));
                            for ($sn = 0; $sn < 12; $sn++) {
                                $amenityId = 'amenity-' . $sn;
                                $amenityCheckBoxId = 'amenity-checkbox-' . $sn;
                                ?>
                                <div class="amenity flex-row pointer <?php if (!in_array($sn, $houseAmenities))
                                    echo "hidden"; ?>" id="<?php echo $amenityId; ?>"
                                    onclick="toggleCheckbox('<?php echo $amenityCheckBoxId; ?>')">
                                    <div class="amenity-left">
                                        <input type="checkbox" name="<?php echo $amenityCheckBoxId; ?>"
                                            id="<?php echo $amenityCheckBoxId; ?>" <?php if (isset($_POST[$amenityCheckBoxId]))
                                                   echo 'checked'; ?>>
                                    </div>

                                    <div class="amenity-right flex-column">
                                        <img src="../../Assests/Icons/amenities/<?php echo returnIconName(returnArrayValue("amenity", $sn)); ?>"
                                            alt="icon">
                                        <p class="p-normal">
                                            <?php echo returnArrayValue("amenity", $sn); ?>
                                        </p>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    <!-- amenity : backup-->
                    <div class="amenity-container flex-column hidden">
                        <p class="p-normal f-bold n-light"> Amenities </p>

                        <div class="amenity-div">
                            <!-- amenity 1 -->
                            <div class="amenity flex-row pointer" id="amenity-1"
                                onclick='toggleCheckbox("amenity-checkbox-1")'>
                                <div class="amenity-left">
                                    <input type="checkbox" name="amenity-checkbox-1" id="amenity-checkbox-1">
                                </div>

                                <div class="amenity-right flex-column">
                                    <img src="../../Assests/Icons/amenities/balcony.png" alt="">
                                    <p class="p-normal"> Amenity Name </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- requirement -->
                    <div class="requirement-div flex-column">
                        <p class="p-normal f-bold n-light"> Requirement </p>
                        <textarea name="requirement" id="" cols="30" rows="10" placeholder="You can leave this empty."><?php if (isset($_POST['requirement']))
                            echo $_POST['requirement']; ?></textarea>
                    </div>

                    <div class="submit-button-container">
                        <input type="submit" name="submit-room" value="Add Now">
                    </div>
                </form>

                <!-- house detail -->
                <div class="house-detail-container flex-column">
                    <p class="p-large n-light"> Selected House Detail </p>
                    <!-- image -->
                    <div class="house-photo-container flex-column">
                        <img src="../../Assests/Uploads/House/<?php echo $house->housePhotoArray[0]['house_photo']; ?>" alt="">
                    </div>

                    <!-- table -->
                    <table class="house-detail-table">
                        <tr>
                            <td class="detail-icon">
                                <img src="../../Assests/Icons/map-pin.png" alt="" class="icon-class">
                            </td>
                            <td class="detail-title"> Location </td>
                            <td class="detail-detail">
                                <?php echo ucfirst($house->areaName) . ', ' . returnArrayValue('district', $house->district); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-icon">
                                <img src="../../Assests/Icons/building.png" alt="" class="icon-class">
                            </td>
                            <td class="detail-title"> House Id </td>
                            <td class="detail-detail">
                                <?php echo $house->houseId; ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-icon">
                                <img src="../../Assests/Icons/" alt="" class="icon-class">
                            </td>
                            <td class="detail-title"> House Identity </td>
                            <td class="detail-detail">
                                <?php echo ucfirst($house->getHouseIdentity($house->houseId)); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-icon">
                                <img src="../../Assests/Icons/" alt="" class="icon-class">
                            </td>
                            <td class="detail-title"> Requirement </td>
                            <td class="detail-detail">
                                <?php echo ucfirst($house->generalRequirement); ?>
                            </td>
                        </tr>
                    </table>

                    <button onclick="window.location.href='myhouse.php'"> Change House </button>
                </div>
            </div>
        </article>
    </div>

    <?php
    if ($submissionState == 'success' || $submissionState == 'failure') {
        ?>
        <div class="flex-column dialog-container">
            <div class="flex-column dialog-div">
                <div class="flex-row top">
                    <p class="p-large f-bold"> 
                        <?php echo ($submissionState == 'success') ? "Your room has been successfully registered." : "Your room could not be registered." ?> 
                    </p>
                </div>

                <div class="flex-row bottom">
                    <?php if($submissionState == 'success'){
                        ?>
                        <button onclick="window.location.href='myroom.php'"> See room detail </button>

                        <?php 
                        $houseId = $house->houseId;
                        $link = "add-room.php?houseId=$houseId"; ?>
                        <button class="inverse-button" onclick="window.location.href='<?php echo $link; ?>'"> Add Another room </button>
                        <?php
                    }else{
                        ?>
                        <button onclick="window.location.href='add-room.php?houseId=<?php echo $_GET['houseId'];?>'"> Try Again </button>
                        <?php
                    }?>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

    <!-- js section -->
    <script>
        const activeMenu = $('#room-menu-id');
        activeMenu.css({
            "background-color" : "#DFDFDF"
        });
    </script>
</body>

</html>