<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['adminId']))
    header("Location: login.php");

// including files
include '../../Class/admin_class.php';
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/feedback_class.php';
include '../../class/functions.php';

// creating the object
$admin = new Admin();
$user = new User();
$room = new Room();
$house = new House();
$feedback = new Feedback();

// setting the values
$admin->adminId = $_SESSION['adminId'];
$admin->fetchAdmin($admin->adminId);
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
    <title> User Voice </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <!-- jquery -->
    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <?php
    include 'aside.php';
    ?>

    <div class="body-container flex-row">
        <aside class="empty-section"> </aside>

        <article class="content-article">
            <div class="section-heading-container content-container">
                <p class="heading f-bold negative"> User Voice </p>
            </div>

            <!-- voice card -->
            <div class="card-container voice-card-container flex-row">
                <?php
                $voiceCount = $feedback->countFeedback("all");
                $unrepliedVoiceCount = $feedback->countFeedback("unreplied");
                $repliedVoiceCount = $feedback->countFeedback("replied");
                ?>
                <div class="voice-card flex-column shadow pointer card" id="all-voice-trigger">
                    <p class="p-form">
                        <?php echo $voiceCount; ?>
                    </p>
                    <p class="p-form"> All Voices </p>
                </div>

                <div class="voice-card flex-column shadow pointer card" id="unreplied-voice-trigger">
                    <p class="p-form">
                        <?php echo $unrepliedVoiceCount; ?>
                    </p>
                    <p class="p-form"> Unreplied </p>
                </div>

                <div class="voice-card flex-column shadow pointer card" id="replied-voice-trigger">
                    <p class="p-form">
                        <?php echo $repliedVoiceCount; ?>
                    </p>
                    <p class="p-form"> Replied </p>
                </div>
            </div>

            <!-- heading -->
            <p class="p-normal f-bold" id="user-voice-heading"> All Voices </p>

            <div class="user-voice-div flex-column">
                <?php
                $feedbackCount = 0;
                $feedbackSets = $feedback->fetchAllFeedbacks();

                foreach ($feedbackSets as $feedbackSet) {
                    $feedbackData = $feedbackSet['feedback_data'];
                    $userId = $feedbackSet['user_id'];
                    $userName = $user->getUserName($feedbackSet['user_id']);
                    $feedbackData = ucfirst($feedbackSet['feedback_data']);
                    $feedbackDate = $feedbackSet['feedback_date'];
                    $role = ucfirst($user->getRole($feedbackSet['user_id']));
                    $userLink = "user-detail.php?userId=$userId";
                    $email = $user->getUserEmail($feedbackSet['user_id']);
                    $feedbackState = (isset($feedbackSet['response_data'])) ? "Replied" : "Unreplied";
                    $userPhoto = $user->getUserPhoto($feedbackSet['user_id']);
                    $feedbackCount++;
                    $rating = $feedbackSet['rating'];
                    $responseFormId = "user-voice-response-form-" . $feedbackCount;
                    ?>

                    <div
                        class="user-voice shadow flex-column user-voice-element <?php echo ($feedbackState == "Replied") ? "user-voice-replied-element" : "user-voice-unreplied-element" ?>">
                        <!-- top -->
                        <div class="user-detail-div flex-row">
                            <div class="image-div">
                                <img src="../../Assests/Uploads/user/<?php echo $userPhoto; ?>" alt="">
                            </div>

                            <div class="username-div flex-column">
                                <p class="p-form">
                                    <?php echo $userName; ?> /
                                    <?php echo $role; ?>
                                </p>

                                <p class="p-small n-light">
                                    <?php echo $email; ?>
                                </p>
                            </div>
                        </div>

                        <!-- middle -->
                        <div class="flex-column user-voice-box">
                            <p class="p-normal problem">
                                <?php echo '"'.$feedbackData.'"'; ?>"
                            </p>

                            <!-- rating -->
                            <div class="flex-row rating-div">
                                <?php
                                for($i=0;$i<$rating;$i++){
                                    ?>
                                    <img src="../../Assests/Icons/full-rating.png" alt="rating">
                                    <?php 
                                }
                                ?>
                            </div>

                            <p class="p-small n-light">
                                <?php echo $feedbackDate; ?>
                            </p>

                            <p class="p-small negative">
                                <?php echo $feedbackState; ?>
                            </p>
                        </div>

                        <!-- bottom -->
                        <div class="bottom flex-row">
                            <div class="section flex-row pointer">
                                <img src="../../Assests/Icons/user.png" alt="">
                                <a href="<?php echo $userLink ?>">
                                    <p class="p-form"> See User Profile </p>
                                </a>
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
                    <img src="../../Assests/Icons/empty.png" alt="">
                    <p class="p-normal negative"> No user voice found! </p>
                </div>
            </div>
        </article>
    </div>

    <!-- script -->
    <script>
        const activeMenu = $('#user-voice-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
        });
    </script>

    <script>
        var userVoiceTrigger = 0;

        var userVoiceElement = $('.user-voice-element');
        var userVoiceRepliedElement = $('.user-voice-replied-element');
        var userVoiceUnrepliedElement = $('.user-voice-unreplied-element');


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
            if (userVoiceTrigger == 0) {
                userVoiceElement.show();
                $('#user-voice-heading').text("All Voice");
            } else if (userVoiceTrigger == 1) {
                userVoiceUnrepliedElement.show();
                $('#user-voice-heading').text("Unreplied Voice");
            } else {
                userVoiceRepliedElement.show();
                $('#user-voice-heading').text("Replied Voice");
            }

            var visibleUserVoiceElement = $('.user-voice-element:visible');

            $('#empty-data-container').hide();
            if (visibleUserVoiceElement.length == 0)
                $('#empty-data-container').show();
        }

        filterUserVoice();
    </script>
</body>

</html>