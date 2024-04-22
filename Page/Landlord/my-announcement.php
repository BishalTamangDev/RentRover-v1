<?php
// staring session
if (!session_start())
    session_start();

// including files
include '../../Class/user_class.php';
include '../../Class/house_class.php';
include '../../Class/announcement_class.php';
include '../../class/functions.php';

// creating the object
$user = new User();
$house = new House();
$room = new Room();
$announcement = new Announcement();

$user->userId = $_SESSION['landlordUserId'];

if (!isset($_SESSION['landlordUserId']))
    header("Location: home.php");
else
    $user->fetchSpecificRow($_SESSION['landlordUserId']);

// fetching house ids
$myHouseIdArray = [];
$myHouseIdArray = $house->returnMyHouseIdArray($_SESSION['landlordUserId']);

// fetching room ids
$myRoomIdArray = [];
$myAcquiredRoomIdArray = [];

if (sizeof($myHouseIdArray) > 0) {
    $myRoomIdArray = $room->returnMyRoomIdArray($myHouseIdArray);
    $myAcquiredRoomIdArray = $room->returnMyAcquiredRoomIdArray($myHouseIdArray);
}

// fetching announced data
$announcementSets = $announcement->fetchAllAnnouncement('landlord', $user->userId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- external css -->
    <link rel="stylesheet" href="../../CSS/common/style.css">
    <link rel="stylesheet" href="../../CSS/admin/admin.css">
    <link rel="stylesheet" href="../../CSS/admin/announcement.css">

    <!-- title -->
    <title> My Announcements </title>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="../../Assests/Images/RentRover-Logo.png">

    <!-- script section -->
    <!-- prevent resubmission of the form -->
    <script>
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>

    <script src="../../Js/jquery-3.7.1.min.js"> </script>
</head>

<body>
    <?php include 'aside.php'; ?>

    <div class="body-container flex-row">
        <aside class="empty-section"> </aside>

        <article class="content-article">
            <!-- announcement -->
            <div class="section-heading-container content-container">
                <p class="heading f-bold negative"> Announcements </p>
            </div>

            <button class="button-with-icon" id="announcement-btn" onclick="toggleAnnouncementForm()">
                <img src="../../Assests/Icons/announcement.png" alt="">
                <p class="p-normal"> Make an announcement </p>
            </button>

            <!-- announcement cards -->
            <div class="content-container flex-row card-container-v2">
                <!-- total house card -->
                <div class="card flex-row pointer" id="announcement-filter-card-all">
                    <p class="p-normal"> Total </p>
                    <p class="p-normal f-bold counter">
                        <?php echo $announcement->countAnnouncement('landlord', $user->userId, -1); ?>
                    </p>
                </div>

                <!-- all house announcement card -->
                <div class="card flex-row pointer" id="announcement-filter-card-all-house">
                    <p class="p-normal"> All House </p>
                    <p class="p-normal f-bold counter">
                        <?php echo $announcement->countAnnouncement('landlord', $user->userId, 0); ?>
                    </p>
                </div>

                <!-- specific house announcement card -->
                <div class="card flex-row pointer" id="announcement-filter-card-specific-house">
                    <p class="p-normal"> Specific House </p>
                    <p class="p-normal f-bold counter">
                        <?php echo $announcement->countAnnouncement('landlord', $user->userId, 1); ?>
                    </p>
                </div>

                <!-- all room announcement card -->
                <div class="card flex-row pointer" id="announcement-filter-card-all-room">
                    <p class="p-normal"> All Room </p>
                    <p class="p-normal f-bold counter">
                        <?php echo $announcement->countAnnouncement('landlord', $user->userId, 2); ?>
                    </p>
                </div>

                <!-- specific announcement card -->
                <div class="card flex-row pointer" id="announcement-filter-card-specific-room">
                    <p class="p-normal"> Specific Room </p>
                    <p class="p-normal f-bold counter">
                        <?php echo $announcement->countAnnouncement('landlord', $user->userId, 3); ?>
                    </p>
                </div>
            </div>

            <?php
            $url = $_SERVER['REQUEST_URI'];
            $link = "operation/announce-op.php?url=$url";
            ?>

            <form method="POST" action="<?php echo $link; ?>" class="flex-column announcement-form shadows"
                id="announcement-form">
                <div class="top-div flex-row">
                    <p class="p-large f-bold n-light"> Announcment Form </p>
                    <img src="../../Assests/Icons/Cancel-filled.png" alt="" class="pointer"
                        onclick="toggleAnnouncementForm()">
                </div>

                <!-- landlord's house and room details -->
                <div class="flex-column announcement-house-room-detail-div hidden">
                    <div class="card">
                        <p class="p-form n-light"> Total House :
                            <?php echo sizeof($myHouseIdArray); ?>
                        </p>
                    </div>

                    <div class="card">
                        <p class="p-form n-light"> Total Rooms :
                            <?php echo sizeof($myRoomIdArray); ?>
                        </p>
                    </div>

                    <div class="card">
                        <p class="p-form n-light"> Acquired Rooms :
                            <?php echo sizeof($myAcquiredRoomIdArray); ?>
                        </p>
                    </div>
                </div>

                <p class="p-form negative" id="error-message"> Error occured </p>

                <!-- announcement type select -->
                <div class="announcement-type-select-div">
                    <p class="p-form"> Announcement Types </p>
                    <select name="announcement-type-select" id="announcement-type-select">
                        <option value="0" selected hidden> Select the announcement </option>
                        <option value="1"> House Specific </option>
                        <option value="2"> Room Specific </option>
                    </select>
                </div>

                <!-- house select -->
                <div class="announcement-house-select-div" id="announcement-house-id-select-div">
                    <p class="p-form"> House </p>
                    <select name="announcement-house-id-select" id="announcement-house-id-select">
                        <option value="-1" selected hidden> Select the house </option>
                        <option value="0"> All Houses </option>
                        <?php
                        if (sizeof($myHouseIdArray) > 0) {
                            foreach ($myHouseIdArray as $myHouseId) {
                                $house->fetchHouse($myHouseId);
                                ?>
                                <option value="<?php echo $myHouseId; ?>">
                                    <?php echo $myHouseId . ', ' . $house->getLocation($myHouseId); ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- room id -->
                <div class="announcement-room-select-div" id="announcement-room-id-select-div">
                    <p class="p-form"> Room </p>
                    <select name="announcement-room-id-select" id="announcement-room-id-select">
                        <option value="-1" selected hidden> Select the room </option>
                        <option value="0"> All Room </option>
                        <?php
                        if (sizeof($myAcquiredRoomIdArray) > 0) {
                            foreach ($myAcquiredRoomIdArray as $myAcquiredRoomId) {
                                $room->fetchRoom($myAcquiredRoomId);
                                ?>
                                <option value="<?php echo $myAcquiredRoomId; ?>">
                                    <?php echo 'ID - ' . $myAcquiredRoomId . ', ' . $house->getLocation($room->houseId) . ', Floor - ' . $room->floor . ', Room No - ' . $room->roomNumber; ?>
                                </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>

                <p class="p-form"> Title </p>
                <!-- <input type="text" name="announcement-title"> -->
                <input type="text" name="announcement-title" required>

                <p class="p-form"> Announcement </p>
                <!-- <textarea name="announcement-announcement"></textarea> -->
                <textarea name="announcement-announcement" required></textarea>

                <button type="submit" name="announcement-submit-btn" class="positive-button flex-row"> <img
                        src="../../Assests/Icons/announcement.png" alt=""> Announce Now </button>
            </form>

            <!-- announcement container -->
            <div class="announcement-div content-container flex-column">
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

                                <div class="announcement-basic-right">
                                    <abbr title="Delete">
                                        <?php 
                                        $announcementId = $announcementSet['announcement_id'];
                                        $link = "operation/announcement-op.php?task=remove&announcementId=$announcementId&url=$url"; ?>
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
                }
                ?>
            </div>

            <div class="flex-column empty-data-div" id="empty-data-container">
                <p class="p-normal negative"> Empty! </p>
            </div>
        </article>
    </div>

    <!-- js section -->
    <!-- js section -->
    <script>
        const activeMenu = $('#announcement-menu-id');
        activeMenu.css({
            "background-color": "#DFDFDF"
        });
    </script>

    <script>
        var announcementFormState = false;
        var errorMessage = $('#error-message');
        const announcementForm = $('#announcement-form');
        const announcementTypeSelect = $('#announcement-type-select');

        const announcementHouseSelect = $('#announcement-house-id-select');
        const announcementHouseSelectDiv = $('#announcement-house-id-select-div');

        const announcementRoomSelect = $('#announcement-room-id-select');
        const announcementRoomSelectDiv = $('#announcement-room-id-select-div');


        announcementFormState = false;

        // announcementForm.hide();
        announcementForm.show();

        announcementRoomSelectDiv.hide();
        announcementHouseSelectDiv.hide();

        toggleAnnouncementForm = () => {
            if (!announcementFormState) {
                announcementForm.hide();
                announcementFormState = true;
            }
            else {
                announcementForm.show();
                announcementFormState = false;
            }
        }

        announcementTypeSelect.change(function () {
            if (announcementTypeSelect[0].value == 1) {
                announcementHouseSelectDiv.show();
                announcementRoomSelectDiv.hide();
            } else {
                announcementHouseSelectDiv.hide();
                announcementRoomSelectDiv.show();
            }
        });

        document.getElementById('announcement-form').addEventListener('submit', function (event) {
            event.preventDefault();
            $type = announcementTypeSelect[0].value;
            $houseId = announcementHouseSelect[0].value;
            $roomId = announcementRoomSelect[0].value;

            $state = true;

            // logic
            if ($type == 0) {
                errorMessage.text("Select the announcement type first.");
                $state = false;
            } else {
                if ($type == 1) {
                    if ($houseId == -1) {
                        errorMessage.text("Select the house id first.");
                        $state = false;
                    }
                } else {
                    if ($roomId == -1) {
                        errorMessage.text("Select the room id first.");
                        $state = false;
                    }
                }
            }

            if ($state) {
                errorMessage.text("Processing...");
                this.submit();
            }
        });

        toggleAnnouncementForm();

        // filter cards
        const announcementFilterCardAll = $('#announcement-filter-card-all');
        const announcementFilterCardAllHouse = $('#announcement-filter-card-all-house');
        const announcementFilterCardSpecificHouse = $('#announcement-filter-card-specific-house');
        const announcementFilterCardAllRooms = $('#announcement-filter-card-all-room');
        const announcementFilterCardSpecificRoom = $('#announcement-filter-card-specific-room');

        // announcement elements
        var announcementElement = $('.announcement-element');
        var announcementAllHouseElement = $('.announcement-all-house-element');
        var announcementSpecificHouseElement = $('.announcement-specific-house-element');
        var announcementAllRoomElement = $('.announcement-all-room-element');
        var announcementSpecificRoomElement = $('.announcement-specific-room-element');

        type = -1;

        announcementFilterCardAll.click(function () {
            type = -1;
            filterAnnouncement();
        });

        announcementFilterCardAllHouse.click(function () {
            type = 0;
            filterAnnouncement();
        });

        announcementFilterCardSpecificHouse.click(function () {
            type = 1;
            filterAnnouncement();
        });

        announcementFilterCardAllRooms.click(function () {
            type = 2;
            filterAnnouncement();
        });

        announcementFilterCardSpecificRoom.click(function () {
            type = 3;
            filterAnnouncement();
        });

        filterAnnouncement = () => {
            console.log(type);
            announcementElement.hide();

            if (type == -1)
                announcementElement.show();
            else if (type == 0)
                announcementAllHouseElement.show();
            else if (type == 1)
                announcementSpecificHouseElement.show();
            else if (type == 2)
                announcementAllRoomElement.show();
            else if (type == 3)
                announcementSpecificRoomElement.show();

            // empty context
            visibleElementCount = $('.announcement-element:visible').length;
            if (visibleElementCount == 0)
                $('#empty-data-container').show();
            else
                $('#empty-data-container').hide();
        }

        filterAnnouncement();
    </script>
</body>

</html>