<?php

// starting session
if (!session_start())
    session_start();

// redirecting to login page 
if (!isset($_SESSION['landlordUserId']))
    header("Location: login.php");

// including files
include '../../Class/user_class.php';
include '../../Class/functions.php';
include '../../Class/house_class.php';
include '../../Class/notification_class.php';

// checking link
if (!isset($_GET['houseId']))
    header("Location: myhouse.php");
else {
    if ($_GET['houseId'] != '') {
        // check for autorized --landlord
        $houseId = $_GET['houseId'];
    } else
        header("location: myroom.php");
}

// after the form has been submitted
$submissionState = 'unknown';
if (isset($_GET['submission']))
    $submissionState = $_GET['submission'];

if (!isset($_GET['houseId']))
    header("location: myhouse.php");

// creating objects
$user = new User();
$oldHouse = new House();
$newHouse = new House();

$user->userId = $_SESSION['landlordUserId'];
$user->fetchSpecificRow($user->userId);

// previous house detail
$oldHouse->fetchHouse($_GET['houseId']);
$oldHouse->houseId = $_GET['houseId'];

$newHouse->setKeyValue('userId', $_GET['houseId']);

// old amenity
$oldAmenityArray = [];
$oldAmenities = unserialize(base64_decode($oldHouse->allAmenities));

foreach ($oldAmenities as $temp)
    $oldAmenityArray[] = $temp;

// new amenity 
$newAmenityArray = [];
$newAmenityArray_sr;

// house photos
$oldHousePhoto1 = $oldHouse->housePhotoArray[0]['house_photo'];
$oldHousePhoto2 = $oldHouse->housePhotoArray[1]['house_photo'];
$oldHousePhoto3 = $oldHouse->housePhotoArray[2]['house_photo'];
$oldHousePhoto4 = $oldHouse->housePhotoArray[3]['house_photo'];

// new house photo
$newHousePhoto1 = "";
$newHousePhoto2 = "";
$newHousePhoto3 = "";
$newHousePhoto4 = "";

// upload file destination
$housePhotoDestination = "../../Assests/Uploads/House/";

// on form submission
$errorMessageState = false;
$errorMessage = "This is error message";

$submitted = false;

if (isset($_POST['submit-house'])) {
    $submitted = true;

    global $newHousePhoto1;
    global $newHousePhoto2;
    global $newHousePhoto3;
    global $newHousePhoto4;

    global $newAmenityArray;
    global $newAmenityArray_sr;

    // retriving the form values
    $houseIdentity = $_POST['house-identity-name'];

    if ($_POST['district'] == -1) {
        $errorMessageState = true;
        $errorMessage = "Select the district.";
    } else {
        $district = $_POST['district'];
        $areaName = $_POST['area-name'];
        $locationCoordinate = $oldHouse->locationCoordinate;
        $generalRequirement = $_POST['general-requirement'];

        // amenity
        for ($i = 0; $i < 12; $i++) {
            $id = 'amenity-checkbox-' . $i;
            if (isset($_POST[$id]))
                array_push($newAmenityArray, $i);
        }

        $newAmenityArray_sr = base64_encode(serialize($newAmenityArray));

        $newHouse->setHouse($oldHouse->ownerId, $houseIdentity, $district, $areaName, $locationCoordinate, $newAmenityArray_sr, $generalRequirement, $oldHouse->houseState, $oldHouse->registerDate);

        $result = $newHouse->updateHouse($oldHouse->houseId);

        if ($result) {
            // photo 1
            if (!is_null($_FILES['house-photo-1'])) {
                if (fileValidityCheck($_FILES['house-photo-1'])) {
                    uploadFile("housePhoto1", $_FILES['house-photo-1']);
                    
                    $response = $oldHouse->updateHousePhoto($oldHousePhoto1, $newHousePhoto1);
                    
                    if($response)
                        unlink($housePhotoDestination . $oldHousePhoto1);
                }
            }

            // photo 2
            if (!is_null($_FILES['house-photo-2'])) {
                if (fileValidityCheck($_FILES['house-photo-2'])) {
                    uploadFile("housePhoto1", $_FILES['house-photo-1']);

                    $response = $oldHouse->updateHousePhoto($oldHousePhoto2, $newHousePhoto2);
                    
                    if($response)
                        unlink($housePhotoDestination . $oldHousePhoto2);
                }
            }

            // photo 3
            if (!is_null($_FILES['house-photo-3'])) {
                if (fileValidityCheck($_FILES['house-photo-3'])) {
                    uploadFile("housePhoto3", $_FILES['house-photo-3']);

                    $response = $oldHouse->updateHousePhoto($oldHousePhoto3, $newHousePhoto3);
                    
                    if($response)
                        unlink($housePhotoDestination . $oldHousePhoto3);
                }
            }

            // photo 4
            if (!is_null($_FILES['house-photo-4'])) {
                if (fileValidityCheck($_FILES['house-photo-4'])) {
                    uploadFile("housePhoto4", $_FILES['house-photo-4']);

                    $response = $oldHouse->updateHousePhoto($oldHousePhoto4, $newHousePhoto4);
                    
                    if($response)
                        unlink($housePhotoDestination . $oldHousePhoto4);
                }
            }

            $submissionLink = $_SERVER['REQUEST_URI'] . "&submission=success";
        } else {
            $submissionLink = $_SERVER['REQUEST_URI'] . "&submission=failure";
        }

        header("location: $submissionLink");
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
    global $newHousePhoto1;
    global $newHousePhoto2;
    global $newHousePhoto3;
    global $newHousePhoto4;

    $fileName = $formFile['name'];
    $fileTmpName = $formFile['tmp_name'];

    // extension extraction
    $fileTempExtension = explode('.', $fileName);
    $fileExtension = strtolower(end($fileTempExtension));

    $newFileName = uniqid('', true) . "." . $fileExtension;

    // setting destination
    if ($fileCategory == "housePhoto1")
        $newHousePhoto1 = $newFileName;
    elseif ($fileCategory == "housePhoto2")
        $newHousePhoto2 = $newFileName;
    elseif ($fileCategory == "housePhoto3")
        $newHousePhoto3 = $newFileName;
    elseif ($fileCategory == "housePhoto4")
        $newHousePhoto4 = $newFileName;

    // upload destination
    $housePhotoDestination = "../../Assests/Uploads/House/";

    $housePhotoDestinaltion = $housePhotoDestination . $newFileName;
    move_uploaded_file($fileTmpName, $housePhotoDestinaltion);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/common/table.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/landlord/myhouse.css">
    <link rel="stylesheet" href="../../CSS/landlord/add-house.css">
    <link rel="stylesheet" href="../../CSS/landlord/edit-house.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <title> Edit House </title>

    <!-- script section -->
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
                    <p class="f-bold negative"> House Edit </p>

                    <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">
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


            <form action="" method="POST" enctype="multipart/form-data" class="house-registration-form flex-column"
                id="registration-form" autocomplete="on">
                <!-- location name -->
                <div class="location-container flex-row">
                    <div class="location-name-div flex-column">
                        <input type="text" name="house-identity-name" placeholder="House Identity Name" value="<?php if (isset($_POST['house-identity-name']))
                            echo $_POST['house-identity-name'];
                        else
                            echo $oldHouse->getHouseIdentity($oldHouse->houseId); ?>"
                            onkeypress="avoidMistake('noPeriod')" required>

                        <select name="district" id="">
                            <?php
                            if (!isset($_POST['district'])) {
                                echo '<option value="' . $oldHouse->district . '" selected hidden>' . returnArrayValue("district", $oldHouse->district) . '</option>';
                            } else {
                                if ($_POST['district'] != -1)
                                    echo '<option value="' . returnArrayIndex("district", $_POST['district']) . '" selected hidden>' . returnArrayValue("district", $_POST['district']) . '</option>';
                                else
                                    echo '<option value="-1" selected hidden> District </option>';
                            }
                            ?>
                            <?php
                            for ($sn = 1; $sn <= 77; $sn++) {
                                echo '<option value="' . $sn . '">' . returnArrayValue("district", $sn) . '</option>';
                            }
                            ?>
                        </select>

                        <input type="text" name="area-name" id="area-name" placeholder="Specific area name..." value="<?php if (isset($_POST['area-name']))
                            echo $_POST['area-name'];
                        else
                            echo $oldHouse->areaName; ?>" onkeypress="avoidMistake('noPeriod')" required>
                    </div>

                    <div class="map-div">
                        <div class="mapouter">
                            <div class="gmap_canvas">
                                <iframe class="gmap_iframe" frameborder="0" scrolling="no" marginheight="0"
                                    marginwidth="0"
                                    src="https://smaps.google.com/maps?width=100%&amp;height=400&amp;hl=en&amp;q=kathmandu;&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=B&amp;output=embed">
                                </iframe>
                                <a href="https://connectionsgame.org/">Connections Puzzle</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- photos -->
                <div class="photo-container flex-column">
                    <p class="p-normal f-bold n-light"> Photos </p>

                    <div class="photo-div">
                        <div class="photo flex-column">
                            <div class="old-photo-cotnainer">
                                <img src="../../Assests/Uploads/House/<?php echo $oldHouse->housePhotoArray[0]['house_photo']; ?>"
                                    alt="">
                            </div>

                            <label for="house-photo-1" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                    alt="" class="icon-class"> Photo 1 </label>
                            <input type="file" name="house-photo-1" id="house-photo-1" accept=".jpeg, .jpg, .png">
                        </div>

                        <div class="photo flex-column">
                            <div class="old-photo-cotnainer">
                                <img src="../../Assests/Uploads/House/<?php echo $oldHouse->housePhotoArray[1]['house_photo']; ?>"
                                    alt="">
                            </div>
                            <label for="house-photo-2" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                    alt="" class="icon-class"> Photo 2 </label>
                            <input type="file" name="house-photo-2" id="house-photo-2" accept=".jpeg, .jpg, .png">
                        </div>

                        <div class="photo flex-column">
                            <div class="old-photo-cotnainer">
                                <img src="../../Assests/Uploads/House/<?php echo $oldHouse->housePhotoArray[2]['house_photo']; ?>"
                                    alt="">
                            </div>
                            <label for="house-photo-3" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                    alt="" class="icon-class"> Photo 3 </label>
                            <input type="file" name="house-photo-3" id="house-photo-3" accept=".jpeg, .jpg, .png">
                        </div>

                        <div class="photo flex-column">
                            <div class="old-photo-cotnainer">
                                <img src="../../Assests/Uploads/House/<?php echo $oldHouse->housePhotoArray[3]['house_photo']; ?>"
                                    alt="">
                            </div>
                            <label for="house-photo-4" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                    alt="" class="icon-class"> Photo 4 </label>
                            <input type="file" name="house-photo-4" id="house-photo-4" accept=".jpeg, .jpg, .png">
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
                        for ($sn = 0; $sn < 12; $sn++) {
                            $amenityId = 'amenity-' . $sn;
                            $amenityCheckBoxId = 'amenity-checkbox-' . $sn;
                            ?>
                            <div class="amenity flex-row pointer" id="<?php echo $amenityId; ?>"
                                onclick="toggleCheckbox('<?php echo $amenityCheckBoxId; ?>')">
                                <div class="left">
                                    <input type="checkbox" <?php
                                    if (in_array($sn, $oldAmenityArray))
                                        echo 'checked';
                                    ?>
                                        name="<?php echo $amenityCheckBoxId; ?>" id="<?php echo $amenityCheckBoxId; ?>">
                                </div>

                                <div class="right flex-column">
                                    <img src="../../Assests/Icons/Amenities/<?php echo returnIconName(returnArrayValue("amenity", $sn)); ?>"
                                        alt="icon">
                                    <p class="p-normal">
                                        <?php echo returnArrayValue("amenity", $sn); ?>
                                    </p>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                        <!-- backup -->
                        <div class="amenity flex-row pointer hidden" id="amenity-1"
                            onclick='toggleCheckbox("amenity-checkbox-1")'>
                            <div class="left">
                                <input type="checkbox" name="amenity-checkbox-1" id="amenity-checkbox-1">
                            </div>

                            <div class="right flex-column">
                                <img src="../../Assests/Icons/Amenities/balcony.png" alt="">
                                <p class="p-normal"> Amenity Name </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- requirement -->
                <div class="requirement-container flex-column">
                    <p class="p-normal f-bold n-light"> Requirements </p>

                    <textarea name="general-requirement" id="" cols="30" rows="10" required><?php if (isset($_POST['general-requirement']))
                        echo $_POST['general-requirement'];
                    else
                        echo $oldHouse->generalRequirement; ?></textarea>

                    <p class="p-form negative">
                        This requirement will be applicable for all the rooms associated with this hoise.
                    </p>
                </div>

                <div class="submit-button-container">
                    <!-- <input type="submit" name="submit-house" value="Add Now"> -->
                    <button type="submit" name="submit-house" class="button-with-icon">
                        <img src="../../Assests/Icons/update.png" alt="">
                        <p class="p-normal"> Update Now </p>
                    </button>
                </div>
            </form>

            <!-- dialog box -->
            <?php
            if ($submissionState == 'success' || $submissionState == 'failure') {
                ?>
                <div class="dialog-container flex-column">
                    <div class="dialog-div flex-column">
                        <div class="top-div flex-row">
                            <div class="message-div flex-column">
                                <?php echo ($submissionState == 'success') ? "House detail upadated successfully." : "House detail could not be updated."; ?>
                            </div>
                        </div>

                        <div class="bottom-div flex-row">
                            <?php
                            $houseId = $_GET['houseId'];
                            $editLink = "edit-house.php?houseId=$houseId";
                            $viewLink = "myhouse-detail.php?houseId=$houseId";
                            $link = $_SERVER['REQUEST_URI'];

                            echo ($submissionState == 'success') ? "<button onclick='window.location.href=\"$viewLink\"'> See House Detail </button>"
                                : "<button class=\"negative-button\" onclick='window.location.href=\"$editLink\"'> Try Again </button>";
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            } ?>
        </article>
    </div>

    <!-- js section -->
    <script>
        const activeMenu = $('#house-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
        });
    </script>
</body>

</html>