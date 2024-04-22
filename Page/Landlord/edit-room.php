<?php

// starting session
if (!session_start())
    session_start();

// redirecting to login page 
if (!isset($_SESSION['landlordUserId']))
    header("Location: login.php");

// checking link
if (!isset($_GET['roomId']))
    header("Location: myroom.php");
else {
    if ($_GET['roomId'] != ''){
        // check for autorized --landlord
        $roomId = $_GET['roomId'];
    }
    else
        header("location: myroom.php");
}

// including files
include_once '../../Class/user_class.php';
include_once '../../Class/functions.php';
include_once '../../Class/notification_class.php';
include_once '../../Class/house_class.php';

// creating objects
$user = new User();
$house = new House();
$oldRoom = new Room();
$newRoom = new Room();

// settin up objects
$user->userId = $_SESSION['landlordUserId'];
$user->fetchSpecificRow($user->userId);

$oldRoom->setKeyValue('id', $_GET['roomId']);
$oldRoom->fetchRoom($_GET['roomId']);

$house->setKeyValue('id', $oldRoom->houseId);
$house->fetchHouse($oldRoom->houseId);

// previous house detail
$houseId = $oldRoom->houseId; 
$house->setKeyValue('id', $houseId);
$house->fetchHouse($houseId);

// old room photo
$oldRoomPhoto1 = $oldRoom->roomPhotoArray[0]['room_photo'];
$oldRoomPhoto2 = $oldRoom->roomPhotoArray[1]['room_photo'];
$oldRoomPhoto3 = $oldRoom->roomPhotoArray[2]['room_photo'];
$oldRoomPhoto4 = $oldRoom->roomPhotoArray[3]['room_photo'];

// global variable
$newRoomPhoto1 = "";
$newRoomPhoto2 = "";
$newRoomPhoto3 = "";
$newRoomPhoto4 = "";

// old amenity
$oldAmenityArray = [];
$oldAmenities = unserialize(base64_decode($oldRoom->amenities));

foreach ($oldAmenities as $temp)
    $oldAmenityArray[] = $temp;

// new amenity 
$newAmenityArray = [];
$newAmenityArray_sr;

// upload file destination
$roomPhotoDestination = "../../assests/uploads/room/";

// on form submission
$errorMessageState = false;
$errorMessage = "This is error message";

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
    global $newRoomPhoto1;
    global $newRoomPhoto2;
    global $newRoomPhoto3;
    global $newRoomPhoto4;

    global $amenityArray;
    global $amenityArray_sr;

    global $roomPhotoDestination;

    $formValid = true;

    // retriving the form values
    $rent = $_POST['rent'];
    $roomNumber = $_POST['room-number'];
    $floor = $_POST['floor'];
    $numberOfRoom = $_POST['number-of-room'];
    $requirement = $_POST['requirement'];

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

        if ($formValid) {
            if ($_POST['furnishing'] == 0) {
                $errorMessageState = true;
                $errorMessage = "Please select the furnishing type.";
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
                    array_push($newAmenityArray, $i);
            }

            $newAmenityArray_sr = base64_encode(serialize($newAmenityArray));

            $newRoom->setRoom($oldRoom->houseId, $roomNumber, $rent, $roomType, $furnishing, $bhk, $numberOfRoom, $floor, $newAmenityArray_sr, $requirement, $oldRoom->isAcquired, $oldRoom->tenantId, $oldRoom->roomState, $oldRoom->registerDate);
            
            if ($newRoom->updateRoom($oldRoom->roomId)){
                // photo 1
                if (!is_null($_FILES['room-photo-1'])){
                    if(fileValidityCheck($_FILES['room-photo-1'])){
                        uploadFile("roomPhoto1", $_FILES['room-photo-1']);
                        $response = $oldRoom->updateRoomPhoto($oldRoomPhoto1, $newRoomPhoto1);
                        
                        if($response)
                            unlink($roomPhotoDestination . $oldRoomPhoto1);
                    }
                }

                // photo 2
                if (!is_null($_FILES['room-photo-2'])){
                    if (fileValidityCheck($_FILES['room-photo-2'])){
                        uploadFile("roomPhoto2", $_FILES['room-photo-2']);
                        $response = $oldRoom->updateRoomPhoto($oldRoomPhoto2, $newRoomPhoto2);
                        if($response)
                            unlink($roomPhotoDestination . $oldRoomPhoto2);
                    }
                }
                    
                // photo 3
                if (!is_null($_FILES['room-photo-3'])){
                    if (fileValidityCheck($_FILES['room-photo-3'])){
                        uploadFile("roomPhoto3", $_FILES['room-photo-3']);
                        $response = $oldRoom->updateRoomPhoto($oldRoomPhoto3, $newRoomPhoto3);
                        if($response)
                            unlink($roomPhotoDestination . $oldRoomPhoto3);
                    }
                }
                    
                // photo 4
                if (!is_null($_FILES['room-photo-4'])){
                    if (fileValidityCheck($_FILES['room-photo-4'])){
                        uploadFile("roomPhoto4", $_FILES['room-photo-4']);
                        $response = $oldRoom->updateRoomPhoto($oldRoomPhoto4, $newRoomPhoto4);
                        if($response)
                            unlink($roomPhotoDestination . $oldRoomPhoto4);
                    }
                }
                $submissionLink = $_SERVER['REQUEST_URI'] . "&submission=success";
            }else
                $submissionLink = $_SERVER['REQUEST_URI'] . "&submission=failure";
        
            header("location: $submissionLink");
        }
    }
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

            if (!in_array($fileExtension, $allowedExtension)) {
                $fileValid = false;
            }
        }
    }
    return $fileValid;
}

function uploadFile($fileCategory, $formFile)
{
    global $newRoomPhoto1;
    global $newRoomPhoto2;
    global $newRoomPhoto3;
    global $newRoomPhoto4;

    global $roomPhotoDestination;

    $fileName = $formFile['name'];
    $fileTmpName = $formFile['tmp_name'];

    // extension extraction
    $fileTempExtension = explode('.', $fileName);
    $fileExtension = strtolower(end($fileTempExtension));

    $newFileName = uniqid('', true) . "." . $fileExtension;

    // setting destination
    if ($fileCategory == "roomPhoto1")
        $newRoomPhoto1 = $newFileName;
    elseif ($fileCategory == "roomPhoto2")
        $newRoomPhoto2 = $newFileName;
    elseif ($fileCategory == "roomPhoto3")
        $newRoomPhoto3 = $newFileName;
    elseif ($fileCategory == "roomPhoto4")
        $newRoomPhoto4 = $newFileName;

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
    <link rel="stylesheet" href="../../CSS/landlord/edit-house.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- title -->
    <title> Edit Room </title>

    <!-- js script -->
    <script src="../../Js/main.js"> </script>
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
                    <p class="f-bold negative"> Room Edit </p>

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
                                echo $_POST['room-number']; else echo $oldRoom->roomNumber; ?>" required>
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
                                $roomType = ($oldRoom->roomType == 1)? "BHK":"Non-BHK"; 
                                echo '<option value="'.$oldRoom->roomType.'" selected hidden>'.$roomType.'</option>';
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
                                    echo $_POST['bhk']; else { if ($oldRoom->roomType == 1) echo $oldRoom->bhk;} ?>">
                            or
                            <input type="text" name="number-of-room" id="" placeholder="Number of rooms"
                                onkeypress="avoidMistake('integer')" value="<?php if (isset($_POST['number-of-room']))
                                    echo $_POST['number-of-room']; else { if ($oldRoom->roomType == 2) echo $oldRoom->numberOfRoom;}?>">
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
                                if ($oldRoom->furnishing == 1)  
                                    $furnishing = "Unfurnished";
                                else if($oldRoom->furnishing == 2)
                                    $furnishing = "Semi-furnished";
                                else if($oldRoom->furnishing == 3)
                                    $furnishing = "Fully-furnished"; 

                                echo '<option value="'.$oldRoom->furnishing.'" selected hidden>'.$furnishing.'</option>';

                                if (isset($_POST['furnishing']) && $_POST['furnishing'] != 0) {
                                    if ($_POST['furnishing'] == 1)
                                        echo '<option value="1" selected hidden> Non-furnished </option>';
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
                                    echo $_POST['rent']; else echo $oldRoom->rentAmount; ?>" required>
                        </div>
                    </div>

                    <!-- floor -->
                    <div class="floor-div flex-row">
                        <div class="left flex-column">
                            <p class="p-normal f-bold n-light"> Floor </p>
                        </div>

                        <div class="right">
                            <input type="text" name="floor" id="" onkeypress="avoidMistake('integer')" value="<?php if (isset($_POST['floor']))
                                echo $_POST['floor']; else echo $oldRoom->floor; ?>" required>
                        </div>
                    </div>

                    <!-- photos -->
                    <div class="photo-container flex-column">
                        <p class="p-normal f-bold n-light"> Room Photos </p>

                        <div class="photo-div">
                            <!-- photo 1 -->
                            <div class="photo flex-column">
                                <div class="old-photo-cotnainer">
                                    <img src="../../Assests/Uploads/Room/<?php echo $oldRoom->roomPhotoArray[0]['room_photo']; ?>" alt="Room photo 1">
                                </div>

                                <label for="room-photo-1" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                        alt=""> Photo 1 </label>
                                <input type="file" name="room-photo-1" id="room-photo-1" accept=".jpeg, .jpg, .png">
                            </div>

                            <!-- photo 2 -->
                            <div class="photo flex-column">
                                <div class="old-photo-cotnainer">
                                    <img src="../../Assests/Uploads/Room/<?php echo $oldRoom->roomPhotoArray[1]['room_photo']; ?>" alt="Room photo 2">
                                </div>

                                <label for="room-photo-2" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                        alt="">Photo 2 </label>
                                <input type="file" name="room-photo-2" id="room-photo-2" accept=".jpeg, .jpg, .png">
                            </div>

                            <!-- photo 3 -->
                            <div class="photo flex-column">
                                <div class="old-photo-cotnainer">
                                    <img src="../../Assests/Uploads/Room/<?php echo $oldRoom->roomPhotoArray[2]['room_photo']; ?>" alt="Room photo 3">
                                </div>

                                <label for="room-photo-3" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                        alt="">Photo 3 </label>
                                <input type="file" name="room-photo-3" id="room-photo-3" accept=".jpeg, .jpg, .png">
                            </div>

                            <!-- photo 4 -->
                            <div class="photo flex-column">
                                <div class="old-photo-cotnainer">
                                    <img src="../../Assests/Uploads/Room/<?php echo $oldRoom->roomPhotoArray[3]['room_photo']; ?>" alt="Room photo 4">
                                </div>

                                <label for="room-photo-4" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                        alt="">Photo 4 </label>
                                <input type="file" name="room-photo-4" id="room-photo-4" accept=".jpeg, .jpg, .png">
                            </div>
                        </div>

                        <p class="p-form negative note">
                            Only upload the photos that you want to update.
                        </p>
                    </div>

                    <!-- amenity -->
                    <div class="amenity-container flex-column">
                        <p class="p-normal f-bold n-light"> Amenities </p>

                        <div class="amenity-div">
                            <?php
                            $houseAmenities = unserialize(base64_decode($house->allAmenities));
                            $oldAmenities = unserialize(base64_decode($oldRoom->amenities));
                            for ($sn = 0; $sn < 12; $sn++) {
                                $amenityId = 'amenity-' . $sn;
                                $amenityCheckBoxId = 'amenity-checkbox-' . $sn;
                                ?>
                                <div class="amenity flex-row pointer <?php if (!in_array($sn, $houseAmenities))
                                    echo "hidden"; ?>" id="<?php echo $amenityId; ?>"
                                    onclick="toggleCheckbox('<?php echo $amenityCheckBoxId; ?>')">
                                    <div class="amenity-left">
                                        <input type="checkbox" <?php
                                    if (in_array($sn, $oldAmenityArray))
                                        echo 'checked';
                                    ?> name="<?php echo $amenityCheckBoxId; ?>"
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
                            echo $_POST['requirement']; else echo $oldRoom->requirement;?></textarea>
                    </div>

                    <div class="submit-button-container">
                        <!-- <input type="submit" name="submit-room" value="Update Now"> -->
                        <button type="submit" name="submit-room" class="button-with-icon">
                            <img src="../../Assests/Icons/update.png" alt="">
                            <p class="p-normal"> Update Now </p>
                        </button>
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
                            <td class="detail-title"> Location </td>
                            <td class="detail-detail">
                                <?php echo ucfirst($house->areaName) . ', ' . returnArrayValue('district', $house->district); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> House Id </td>
                            <td class="detail-detail">
                                <?php echo $house->houseId; ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> House Identity Name </td>
                            <td class="detail-detail">
                                <?php echo ucfirst($house->houseIdentity); ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="detail-title"> General Requirement </td>
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
                <div class="top-div flex-row">
                    <div class="flex-row top">
                        <p class="p-large f-bold"> 
                            <?php echo ($submissionState == 'success') ? "Room detail upadated successfully." : "Room detail upadated successfully." ?> 
                        </p>
                    </div>
                </div>

                <div class="bottom-div flex-row">
                    <div class="flex-row bottom">
                        <?php 
                        $viewLink="myroom-detail.php?roomId=$oldRoom->roomId";
                        $editLink="edit-room.php?roomId=$oldRoom->roomId";
                        echo ($submissionState == 'success') ? "<button onclick='window.location.href=\"$viewLink\"'> See Room Detail </button>" : "<button class=\"negative-button\" onclick='window.location.href=\"$editLink\"'> Try Again </button>";
                        ?>
                    </div>
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