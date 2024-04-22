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

// after the form has been submitted
$submissionState = 'unknown';
if (isset($_GET['submission']))
    $submissionState = $_GET['submission'];

$amenityArray = [];
$amenityArray_sr;

$housePhoto1 = 'NULL';
$housePhoto2 = 'NULL';
$housePhoto3 = 'NULL';
$housePhoto4 = 'NULL';

// upload file destination
$housePhotoDestination = "../../assests/uploads/house/";

// creating objects
$user = new User();
$houseObj = new House();

$user->userId = $_SESSION['landlordUserId'];
$user->fetchSpecificRow($user->userId);

// on form submission
$errorMessageState = false;
$errorMessage = "This is error message";

$submitted = false;

if (isset($_POST['submit-house'])) {
    $submitted = true;

    global $housePhoto1;
    global $housePhoto2;
    global $housePhoto3;
    global $housePhoto4;

    global $amenityArray;
    global $amenityArray_sr;

    // retriving the form values
    $houseIdentityName = $_POST['house-identity-name'];

    if ($_POST['district'] == -1) {
        $errorMessageState = true;
        $errorMessage = "Select the district.";
    } else {
        $district = $_POST['district'];

        $areaName = $_POST['area-name'];
        $locationCoordinate = '0, 0';

        if ($_POST['general-requirement'] != '')
            $generalRequirement = $_POST['general-requirement'];

        // temporary
        $photoFileValid1 = fileValidityCheck($_FILES['house-photo-1']);
        $photoFileValid2 = fileValidityCheck($_FILES['house-photo-2']);
        $photoFileValid3 = fileValidityCheck($_FILES['house-photo-3']);
        $photoFileValid4 = fileValidityCheck($_FILES['house-photo-4']);

        $housePhoto1 = $_FILES['house-photo-1']['name'];
        $housePhoto2 = $_FILES['house-photo-2']['name'];
        $housePhoto3 = $_FILES['house-photo-3']['name'];
        $housePhoto4 = $_FILES['house-photo-4']['name'];

        for ($i = 0; $i < 12; $i++) {
            $id = 'amenity-checkbox-' . $i;
            if (isset($_POST[$id]))
                array_push($amenityArray, $i);
        }

        $amenityArray_sr = base64_encode(serialize($amenityArray));

        if ($photoFileValid1 || $photoFileValid2 || $photoFileValid3 || $photoFileValid4) {
            uploadFile("housePhoto1", $_FILES['house-photo-1']);
            uploadFile("housePhoto2", $_FILES['house-photo-2']);
            uploadFile("housePhoto3", $_FILES['house-photo-3']);
            uploadFile("housePhoto4", $_FILES['house-photo-4']);

            $houseObj->housePhoto1 = $housePhoto1;
            $houseObj->housePhoto2 = $housePhoto2;
            $houseObj->housePhoto3 = $housePhoto3;
            $houseObj->housePhoto4 = $housePhoto4;

            $ownerId = $_SESSION['landlordUserId'];
            $houseState = 1;
            $registerDate = date('Y-m-d H:i:s');

            $houseObj->setHouse($ownerId, $houseIdentityName, $district, $areaName, $locationCoordinate, $amenityArray_sr, $generalRequirement, $houseState, $registerDate);

            $immediateHouseId = $houseObj->registerHouse();

            if ($immediateHouseId != 0) {
                $houseObj->addHousePhoto($immediateHouseId);

                // create notification
                $userNotification = new Notification();
                $adminNotification = new Notification();

                $userNotification->setHouseNotification(0, 'landlord', $_SESSION['landlordUserId'], $immediateHouseId);
                $adminNotification->setHouseNotification(0, 'admin', $_SESSION['landlordUserId'], $immediateHouseId);

                $userNotificationState = $userNotification->register();
                $adminNotificationState = $adminNotification->register();

                header('location: add-house.php?submission=success');
            } else
                header('location: add-house.php?submission=failure');
        }
    }
}

function fileValidityCheck($formFile)
{
    global $errorMessage;
    global $errorMessageState;

    $fileValid = true;

    $fileName = $formFile['name'];
    $fileTmpName = $formFile['tmp_name'];
    $fileSize = $formFile['size'];
    $fileError = $formFile['error'];
    $fileType = $formFile['type'];

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
    global $housePhoto1;
    global $housePhoto2;
    global $housePhoto3;
    global $housePhoto4;

    global $housePhotoDestination;

    $fileName = $formFile['name'];
    $fileTmpName = $formFile['tmp_name'];

    // extension extraction
    $fileTempExtension = explode('.', $fileName);
    $fileExtension = strtolower(end($fileTempExtension));

    $newFileName = uniqid('', true) . "." . $fileExtension;

    // setting destination
    if ($fileCategory == "housePhoto1")
        $housePhoto1 = $newFileName;
    elseif ($fileCategory == "housePhoto2")
        $housePhoto2 = $newFileName;
    elseif ($fileCategory == "housePhoto3")
        $housePhoto3 = $newFileName;
    elseif ($fileCategory == "housePhoto4")
        $housePhoto4 = $newFileName;

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
    <link rel="stylesheet" href="../../CSS/Common/jquery.ui.plupload.css">

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <title> Add House </title>

    <!-- script section -->
    <script src="../../Js/main.js"> </script>
    <script src="../../Js/plupload.full.min.js"> </script>
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
                    <p class="f-bold negative"> House Registration </p>

                    <a href="add-house.php">
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
                            echo $_POST['house-identity-name']; ?>" onkeypress="avoidMistake('noPeriod')" required>

                        <select name="district" id="">
                            <?php
                            if (!isset($_POST['district'])) {
                                echo '<option value="-1" selected hidden> District </option>';
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
                            echo $_POST['area-name']; ?>" onkeypress="avoidMistake('noPeriod')" required>
                    </div>

                    <div class="map-div">
                        <div class="mapouter">
                            <div class="gmap_canvas">
                                <iframe class="gmap_iframe" frameborder="0" scrolling="no" marginheight="0"
                                    marginwidth="0"
                                    src="https://maps.google.com/maps?width=100%&amp;height=400&amp;hl=en&amp;q=kathmandu;&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=B&amp;output=embed">
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
                            <label for="house-photo-1" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                    alt="" class="icon-class"> Photo 1 </label>
                            <input type="file" name="house-photo-1" id="house-photo-1" accept=".jpeg, .jpg, .png"
                                required>
                        </div>

                        <div class="photo flex-column">
                            <label for="house-photo-2" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                    alt="" class="icon-class"> Photo 2 </label>
                            <input type="file" name="house-photo-2" id="house-photo-2" accept=".jpeg, .jpg, .png"
                                required>
                        </div>

                        <div class="photo flex-column">
                            <label for="house-photo-3" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                    alt="" class="icon-class"> Photo 3 </label>
                            <input type="file" name="house-photo-3" id="house-photo-3" accept=".jpeg, .jpg, .png"
                                required>
                        </div>

                        <div class="photo flex-column">
                            <label for="house-photo-4" class="flex-row"> <img src="../../Assests/Icons/upload.png"
                                    alt="" class="icon-class"> Photo 4 </label>
                            <input type="file" name="house-photo-4" id="house-photo-4" accept=".jpeg, .jpg, .png"
                                required>
                        </div>
                    </div>

                    <p class="p-form negative">
                        Upload 5 photos of the house and those photos are clear and should help in recognizing the
                        house.
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

                                    <input type="checkbox" <?php if (isset($_POST[$amenityCheckBoxId]))
                                        echo 'checked'; ?>
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

                    <textarea name="general-requirement" id="" cols="30" rows="10"><?php if (isset($_POST['general-requirement']))
                        echo $_POST['general-requirement']; ?></textarea>

                    <p class="p-form negative">
                        This requirement will be applicable for all the rooms associated with this hoise.
                    </p>
                </div>

                <div class="submit-button-container">
                    <input type="submit" name="submit-house" value="Add Now">
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
                                <?php
                                if ($submissionState == 'success') {
                                    ?>
                                    <p class="p-large f-bold positive"> Your house has been successfully registered. </p>
                                    <?php
                                } else if ($submissionState == 'failure') {
                                    ?>
                                        <p class="p-large f-bold negative"> Your house could not be registered. </p>
                                        <p class="p-normal"> Please try again. </p>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <div class="bottom-div flex-row">
                            <?php
                            if ($submissionState == 'success') {
                                ?>
                                <button onclick="window.location.href='add-house.php';" class="inverse-button"> Register
                                    Another House </button>
                                <button onclick="window.location.href='myhouse.php';"> See House Details </button>
                                <?php
                            } else if ($submissionState == 'failure') {
                                ?>
                                    <button onclick="window.location.href='add-house.php'" class="inverse-button"> Try Again
                                    </button>
                                <?php
                            }
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