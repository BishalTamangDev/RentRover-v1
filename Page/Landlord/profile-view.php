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
$submissionState = "failure";
if (isset($_GET['submission']))
    $submissionState = $_GET['submission'];

$errorMessageState = false;
$errorMessage = "This is an error message.";


$newUser->setKeyValue('id', $user->userId);
$newUser->setKeyValue('password', $user->password);

// updating user detail
if (isset($_GET['update-user'])) {
    $newUser->firstName = $_GET['first-name'];
    $newUser->middleName = $_GET['middle-name'];
    $newUser->lastName = $_GET['last-name'];
    $newUser->gender = $_GET['gender'];
    $newUser->dob = $_GET['dob'];
    $newUser->province = $_GET['province'];
    $newUser->district = $_GET['district'];
    $newUser->isVdc = $_GET['isVdc'];
    $newUser->areaName = $_GET['area-name'];
    $newUser->wardNumber = $_GET['ward-number'];
    $newUser->contact = $_GET['contact'];

    $result = $newUser->updateUser();

    if ($result)
        header('location: accounts.php?task=edit-profile&submission=success');
    else
        header('location: accounts.php?task=edit-profile&submission=failure');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- title -->
    <title> My Profile </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- linking external css -->
    <link rel="stylesheet" href="../../CSS/Common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/Common/lightbox.min.css">
    <link rel="stylesheet" href="../../CSS/landlord/profile-view.css">

    <!-- script -->
    <script src="../../Js/main.js"> </script>

    <!-- jquery -->
    <script src="../../Js/lightbox-plus-jquery.min.js"> </script>
</head>

<body>
    <?php
    include 'aside.php';
    ?>

    <div class="flex-row body-container">
        <!-- empty section -->
        <aside class="empty-section"> </aside>

        <!-- main article -->
        <article class="flex-column content-article">
            <p class="p-normal f-bold" style="margin-top: 20px; padding-left:2px;"> My Profile </p>

            <form action="" method="GET" class="profile-form flex-column" id="new-user-form">
                <!-- photo & username -->
                <div class="photo-container flex-column">
                    <div class="photo-div flex-column">
                        <img src="../../Assests/Uploads/user/<?php echo $user->userPhoto; ?>" alt="">
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
                </div>

                <!-- full name -->
                <div class="name-container flex-row">
                    <div class="left flex-row">
                        <p class="p-normal"> Name </p>
                    </div>

                    <div class="middle flex-row">
                        <p class="p-normal">
                            <?php echo returnFormattedName($user->firstName, $user->middleName, $user->lastName); ?>
                        </p>
                    </div>
                </div>

                <!-- Gender -->
                <div class="flex-row">
                    <div class="left flex-row">
                        <p class="p-normal"> Gender </p>
                    </div>

                    <div class="middle flex-row">
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

                    <div class="middle flex-row">
                        <p class="p-normal">
                            <?php echo ucfirst($user->dob); ?>
                        </p>
                    </div>
                </div>

                <!-- address -->
                <div class="address-container flex-row">
                    <div class="left flex-row">
                        <p class="p-normal"> Address </p>
                    </div>

                    <div class="middle flex-row">
                        <p class="p-normal">
                            <?php echo returnFormattedAddress($user->province, $user->district, $user->areaName, $user->wardNumber) ?>
                        </p>
                    </div>
                </div>

                <!-- Email address -->
                <div class="flex-row">
                    <div class="left flex-row">
                        <p class="p-normal"> Email Address </p>
                    </div>

                    <div class="middle flex-row">
                        <p class="p-normal">
                            <?php echo $user->email; ?>
                        </p>
                    </div>
                </div>

                <!-- Contact -->
                <div class="flex-row">
                    <div class="left flex-row">
                        <p class="p-normal"> Contact </p>
                    </div>

                    <div class="middle flex-row">
                        <p class="p-normal">
                            <?php echo $user->contact; ?>
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
                            <?php if ($user->accountState == 0)
                                echo "Unverified";
                            elseif ($user->accountState == 1)
                                echo "Verified";
                            elseif ($user->accountState == 2)
                                echo "Suspended";
                            elseif ($user->accountState == 3)
                                echo "Deactivated";
                            ?>
                        </p>
                    </div>
                </div>

                <!-- document -->
                <div class="flex-row document-section">
                    <div class="left">
                        <p class="p-normal"> Documents </p>
                    </div>

                    <div class="flex-column right">
                        <img src="../../Assests/Uploads/Citizenship/" alt="">
                        <?php 
                        $citizenshipFront = $user->citizenshipFrontPhoto;
                        $link ="../../Assests/Uploads/Citizenship/$citizenshipFront";
                        ?>
                        
                        <a href="<?php echo $link; ?>" data-lightbox="citizenship-photo">
                            <p class="p-normal pointer"> Citizenship Frontside </p>
                        </a>

                        <?php 
                        $citizenshipBack = $user->citizenshipBackPhoto;
                        $link ="../../Assests/Uploads/Citizenship/$citizenshipBack";
                        ?>
                        <a href="<?php echo $link; ?>" data-lightbox="citizenship-photo">
                            <p class="p-normal pointer"> Citizenship Backside </p>
                        </a>
                    </div>
                </div>
            </form>

            <!-- view profile : buttons -->
            <div class="content-container account-deactive-div flex-row">
                <button class="negative-button <?php if($user->accountState != 1 ) echo "hidden"; ?>" onclick="deactivateAccount()"> Deactivate Account </button>
                <button class="positive-button <?php if($user->accountState != 3) echo "hidden"; ?>" onclick="activateAccount()"> Request Account Activation </button>
                <button class="inverse-button" onclick="window.location.href='profile-edit.php'"> Edit Profile </button>
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
                            <?php
                            if ($task == 'view-password-and-security' || $task == 'edit-profile') {
                                ?>
                                <button onclick="window.location.href='accounts.php?task=view-profile'" class="button"> View
                                    Profile </button>
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

    <!-- script -->
    <script>
        // account deactivation function
        deactivateAccount = () => {
            var choice = confirm("Do you want to deactivate your acount?");
            if (choice)
                window.location.href = 'operation/account-op.php?task=deactivate';
        }

        // account activation function
        activateAccount = () => {
            var choice = confirm("Do you want to activate your acount?");
            if (choice) {
                alert("You will be notified after you account get activated.");
                // window.location.href = 'operation/account-op.php?task=deactivate';
            }
        }
    </script>
</body>

</html>