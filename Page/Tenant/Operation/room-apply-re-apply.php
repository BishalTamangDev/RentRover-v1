<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['tenantUserId']))
    header("Location: ../../index.php");

include '../../../Class/house_class.php';
include '../../../Class/notification_class.php';
include '../../../Class/application_class.php';

// creating objects
$room = new Room();
$house = new House();
$application = new Application();
$landlordNotification = new Notification();

// form values
$tenantId = $_SESSION['tenantUserId'];
$roomId = $_GET['roomId'];
$task = $_GET['task'];
$url = $_GET['url'];

$room->fetchRoom($roomId);
$houseId = $room->houseId;

$house->fetchHouse($houseId);
$landlordId = $house->ownerId;

$state = $application->applicationOperation($task, $roomId, $tenantId);
$link = "../room-details.php?roomId=$roomId";

if($state){
    // create notification for landlord
    $notificationType = "room-application-re-apply";
    
    // get application id
    $applicationId = $application->getApplicationId($roomId, $tenantId);

    if($applicationId != 0){
        $landlordNotification->setApplicationNotification($notificationType, $roomId, $landlordId, $tenantId, $applicationId);
        $landlordNotification->whose = "landlord";
        $landlordNotification->register();
    }
}

header("location: $link");
