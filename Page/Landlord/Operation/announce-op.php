<?php
// staring session
if (!session_start())
    session_start();

// including files
include '../../../Class/user_class.php';
include '../../../Class/house_class.php';
include '../../../Class/notification_class.php';
include '../../../Class/announcement_class.php';

// creating the object
$user = new User();
$room = new Room();
$house = new House();
$notification = new Notification();
$announcement = new Announcement();

if (isset($_GET['url'])) {
    $url = $_GET['url'];
} else {
    header('location: ../my-announcement.php');
}

$state = false;

$type = $_POST['announcement-type-select'];
$houseId = $_POST['announcement-house-id-select'];
$roomId = $_POST['announcement-room-id-select'];
$title = $_POST['announcement-title'];
$announcementData = $_POST['announcement-announcement'];

$user->userId = $_SESSION['landlordUserId'];

if (!isset($_SESSION['landlordUserId']))
    header("Location: home.php");
else
    $user->fetchSpecificRow($_SESSION['landlordUserId']);

$landlordId = $_SESSION['landlordUserId'];

// array values
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

$notification->dateTime = date('Y-m-d H:i:s');

if ($type == 1) {
    // house specific
    // echo "Type : House Announcement" . "<br><br>";
    if ($_POST['announcement-house-id-select'] == 0) {
        // all houses
        // echo "Target : All houses" . "<br><br>";
        // echo "House ID : ";

        // foreach ($myHouseIdArray as $myHouseId) {
            // echo $myHouseId, ' ';
        // }

        // fetching all the rooms of this owner
        $myRoomIdArray = $room->returnMyRoomIdArray($myHouseIdArray);
        $myAcquiredRoomIdArray = $room->returnMyAcquiredRoomIdArray($myHouseIdArray);

        // printing acquired room ids
        // echo "<br><br>Acquired room Details : <br>";
        $announcementDate = $notification->dateTime;

        if (sizeof($myAcquiredRoomIdArray) > 0) {
            foreach ($myAcquiredRoomIdArray as $myAcquiredRoomId) {
                // getting tenant id
                $room->fetchRoom($myAcquiredRoomId);
                $tenantId = $room->tenantId;
                // echo "Room ID : " . $myAcquiredRoomId . " - Tenant ID : " . $tenantId . "<br>";

                // setting announcement
                $announcement->setAnnouncement(0, "landlord", 0, $landlordId, $tenantId, $room->houseId, $myAcquiredRoomId, $title, $announcementData, $announcementDate);
                $immediateAnnouncementId = $announcement->registerAnnouncement();

                // $immediateAnnouncementId = 123;
                if ($immediateAnnouncementId != 0) {
                    // notify tenant
                    $notification->resetObject();
                    $notification->setAnnouncementNotification("landlord", "announcement", $landlordId, $houseId, $myAcquiredRoomId, $tenantId, $immediateAnnouncementId);
                    $notification->whose = "tenant";
                    $notification->register();

                    $state = true;
                }
            }
        }
    } else {
        // specific house
        // echo "Target house id : " . $houseId . "<br>";
        $myHouseIdArray = [];
        $myHouseIdArray[] = $houseId;

        // foreach ($myHouseIdArray as $myHouseId) {
            // echo "House ID : $myHouseId";
        // }

        // fetch rooms
        $myRoomIdArray = [];
        $myAcquiredRoomIdArray = [];

        // room id array
        $myRoomIdArray = $room->returnMyRoomIdArray($myHouseIdArray);
        $myAcquiredRoomIdArray = $room->returnMyAcquiredRoomIdArray($myHouseIdArray);

        $announcementDate = $notification->dateTime;

        // printing room id
        foreach ($myAcquiredRoomIdArray as $myAcquiredRoomId) {
            // getting tenant id
            $room->fetchRoom($myAcquiredRoomId);
            $tenantId = $room->tenantId;
            // echo "<br><br>Room ID : " . $myAcquiredRoomId . " - Tenant ID : " . $tenantId . "<br>";

            // setting announcement
            $announcement->setAnnouncement(0, "landlord", 1, $landlordId, $tenantId, $room->houseId, $myAcquiredRoomId, $title, $announcementData, $announcementDate);
            $immediateAnnouncementId = $announcement->registerAnnouncement();

            // $immediateAnnouncementId = 123;
            if ($immediateAnnouncementId != 0) {
                // notify tenant
                $notification->resetObject();
                $notification->setAnnouncementNotification("landlord", "announcement", $landlordId, $room->houseId, $myAcquiredRoomId, $tenantId, $immediateAnnouncementId);
                $notification->whose = "tenant";
                $notification->register();

                $state = true;
            }
        }
    }
} elseif ($type == 2) {
    // room specific
    // echo "Type : Room Announcement" . "<br>";

    if ($_POST['announcement-room-id-select'] == 0) {
        // all rooms
        // echo "Target : All rooms" . "<br> Room ID : ";
        $announcementDate = $notification->dateTime;
        foreach ($myAcquiredRoomIdArray as $myAcquiredRoomId) {
            echo $myAcquiredRoomId, ', ';
            $room->fetchRoom($myAcquiredRoomId);
            $tenantId = $room->tenantId;

            // setting announcement
            $announcement->setAnnouncement(0, "landlord", 2, $landlordId, $tenantId, $room->houseId, $myAcquiredRoomId, $title, $announcementData, $announcementDate);
            $immediateAnnouncementId = $announcement->registerAnnouncement();

            // $immediateAnnouncementId = 123;
            if ($immediateAnnouncementId != 0) {
                // notify tenant
                $notification->resetObject();
                $notification->setAnnouncementNotification("landlord", "announcement", $landlordId, $room->houseId, $myAcquiredRoomId, $tenantId, $immediateAnnouncementId);
                $notification->whose = "tenant";
                $notification->register();

                $state = true;
            }
        }
    } else {
        // specific room
        // echo "Target room id : " . $roomId . "<br>";

        // fetching room
        $room->fetchRoom($roomId);
        $tenantId = $room->tenantId;
        $announcementDate = $notification->dateTime;

        // setting announcement
        $announcement->setAnnouncement(0, "landlord", 3, $landlordId, $tenantId, $room->houseId, $roomId, $title, $announcementData, $announcementDate);
        $immediateAnnouncementId = $announcement->registerAnnouncement();

        // $immediateAnnouncementId = 123;
        if ($immediateAnnouncementId != 0) {
            // notify tenant
            $notification->resetObject();
            $notification->setAnnouncementNotification("landlord", "announcement", $landlordId, $room->houseId, $roomId, $tenantId, $immediateAnnouncementId);
            $notification->whose = "tenant";
            $notification->register();

            $state = true;
        }
    }
}

if($state)
    $url = $url.'?submission=success';
else
    $url = $url.'?submission=failure';

header("location: $url");