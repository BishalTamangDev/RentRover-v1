<?php
// staring session
if (!session_start())
    session_start();

// including files
include '../../Class/user_class.php';
include '../../class/functions.php';

// creating the object
$user = new User();
$newUser = new User();

if (!isset($_SESSION['landlordUserId']))
    header("Location: login.php");
else
    $user->fetchSpecificRow($_SESSION['landlordUserId']);

$user->userId = $_SESSION['landlordUserId'];
$user->fetchSpecificRow($user->userId);

// submission
$submissionState = false;
if (isset($_GET['submission']))
    $submissionState = $_GET['submission'];

$errorMessageState = "unset";
$errorMessage = "Error message.";

$newUser->setKeyValue('id', $user->userId);
$newUser->setKeyValue('password', $user->password);

// global variables
$newUserPhoto = "";

// updating user detail
if (isset($_POST['update-user'])) {
    global $newUserPhoto;

    $newUser->userId = $_SESSION['landlordUserId'];
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

    if ($result){
        // user photo
        $oldUserPhoto = $_POST['old-user-photo'];
        
        if(isset($_FILES['new-user-photo']) && $_FILES['new-user-photo']['name'] != ''){        
            if(fileValidityCheck($_FILES['new-user-photo'])){
                uploadFile("userPhoto", $_FILES['new-user-photo']);
                $response = $user->updateUserPhoto($newUserPhoto, $_SESSION['landlordUserId']);

                unlink("../../Assests/Uploads/User/".$oldUserPhoto);                
            }  
        }
        header('location: profile-edit.php?submission=success');
    } else {
        header('location: profile-edit.php?submission=failure');
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- linking external css -->
    <link rel="stylesheet" href="../../CSS/Common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/landlord/profile-edit.css">
    
    <!-- lightbox -->
    <link rel="stylesheet" href="../../CSS/Common/lightbox.min.css">

    <!-- title -->
    <title> Edit Profile </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- script -->
    <script src="../../Js/main.js"> </script>
</head>

<body>
    <!-- menu -->
    <?php include 'aside.php'; ?>

    <div class="body-container flex-row">
        <!-- empty section -->
        <aside class="empty-section"> </aside>

        <!-- main article -->
        <article class="content-article flex-column">
            <div class="container">
            <p class="p-normal f-bold" style="margin-top: 20px; padding-left:2px;"> My Profile </p>
                <?php
                if(!$errorMessageState == "failure"){
                ?>
                <p class="negative f-small"> <?php echo $errorMessage;?> </p>
                <?php 
                }
                ?>
            </div>

            <form action="" method="POST" class="profile-form flex-column" id="new-user-form" enctype="multipart/form-data">
                <!-- photo & username -->
                <div class="photo-container flex-column">
                    <div class="photo-div">
                        <img src="../../Assests/Uploads/user/<?php echo $user->userPhoto; ?>" alt=""
                            class="icon-class">
                    </div>
                </div>

                <!-- photo -->
                <div class="flex-row photo-container-new">
                    <div class="left">
                        <p class="p-normal"> Change User Photo </p>
                    </div>

                    <div class="right">
                        <input type="file" name="new-user-photo" id="new-user-photo">
                        <input type="hidden" name="old-user-photo" id="old-user-photo" value="<?php echo $user->userPhoto; ?>">
                    </div>
                </div>

                <!-- full name -->
                <div class="name-container flex-row">
                    <div class="left flex-row">
                        <p class="p-normal"> Name </p>
                    </div>

                    <div class="name-div right">
                        <input type="text" name="first-name" id="first-name" value="<?php echo $user->firstName; ?>"
                            placeholder="First name" onkeypress="avoidMistake('word')" required>
                        <input type="text" name="middle-name" id="middle-name" value="<?php echo $user->middleName; ?>"
                            placeholder="Middle name" onkeypress="avoidMistake('word')">
                        <input type="text" name="last-name" id="last-name" value="<?php echo $user->lastName; ?>"
                            placeholder="Last name" onkeypress="avoidMistake('word')" required>
                    </div>
                </div>

                <!-- Gender -->
                <div class="flex-row">
                    <div class="left flex-row">
                        <p class="p-normal"> Gender </p>
                    </div>

                    <div class="right flex-row">
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

                    <div class="right flex-row">
                        <input type="date" name="dob" value="<?php echo $user->dob; ?>">
                    </div>
                </div>

                <!-- address -->
                <div class="address-container flex-row">
                    <div class="left flex-row">
                        <p class="p-normal"> Address </p>
                    </div>

                    <div class="right address-div">
                        <!-- province -->
                        <select name="province">
                            <?php echo '<option value="' . $user->province . '" selected hidden>' . returnArrayValue('province', $user->province) . '</option>'; ?>
                            <?php
                            for ($i = 1; $i <= 7; $i++)
                                echo '<option value="' . $i . '">' . returnArrayValue('province', $i) . '</option>';
                            ?>
                        </select>

                        <!-- district -->
                        <select name="district">
                            <?php echo '<option value="' . $user->district . '" selected hidden>' . returnArrayValue('district', $user->district) . '</option>'; ?>
                            <?php
                            for ($i = 1; $i <= 77; $i++)
                                echo '<option value="' . $i . '">' . returnArrayValue('district', $i) . '</option>';
                            ?>
                        </select>

                        <!-- isVDC -->
                        <select name="isVdc">
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
                            value="<?php echo ucfirst($user->areaName); ?>" placeholder="Area name"
                            onkeypress="avoidMistake('word')" required>

                        <!-- ward number -->
                        <select name="ward-number">
                            <?php
                            echo '<option value="' . $user->wardNumber . '" selected hidden>' . 'Ward '.$user->wardNumber . '</option>';
                            ?>
                            <option value="1"> Ward 1 </option>
                            <option value="2"> Ward 2 </option>
                            <option value="3"> Ward 3 </option>
                            <option value="4"> Ward 4 </option>
                            <option value="5"> Ward 5 </option>
                            <option value="6"> Ward 6 </option>
                            <option value="7"> Ward 7 </option>
                            <option value="8"> Ward 8 </option>
                            <option value="9"> Ward 9 </option>
                        </select>
                    </div>
                </div>

                <!-- Contact -->
                <div class="flex-row">
                    <div class="left flex-row">
                        <p class="p-normal"> Contact </p>
                    </div>

                    <div class="right flex-row">
                        <input type="text" name="contact" id="contact" value="<?php echo $user->contact; ?>"
                            onkeypress="avoidMistake('integer')" required>
                    </div>
                </div>

                <!-- edit profile : buttons -->
                <div class="button-div flex-row">
                    <button type="submit" name="update-user"> Update </button>
                    <a href="profile-edit.php"> Reset </a>
                    <a href="profile-view.php"> Cancel </a>
                </div>
            </form>
        </article>

        <!-- dialog box -->
        <?php
        if ($submissionState == 'success') {
            ?>
            <div class="dialog-container flex-column">
                <div class="dialog-div flex-column">
                    <div class="top-div flex-row">
                        <div class="message-div flex-column">
                            <?php echo '<p class="p-large f-bold"> Detail has been updated successfully. </p>'; ?>
                        </div>
                    </div>

                    <div class="operation-div flex-row">
                        <?php
                            ?>
                            <button onclick="window.location.href='profile-view.php'" class="button"> View Profile </button>
                            <button onclick="window.location.href='profile-edit.php'" class="inverse-button"> Close </button>
                            <?php
                        ?>
                    </div>
                </div>
            </div>
            <?php
        } ?>
        </article>
    </div>

    <script>
        var passwordVisibility = false;
        const passwordBox1 = document.getElementById("password-old");
        const passwordBox2 = document.getElementById("password-new-1");
        const passwordBox3 = document.getElementById("password-new-2");
        const passwordToggleLabel = document.getElementById("show-password-label");

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
</body>

</html>