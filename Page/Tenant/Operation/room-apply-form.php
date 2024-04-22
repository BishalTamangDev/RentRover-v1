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
$rentType = $_POST['room-apply-rent-type-select'];

$rentType = ($rentType == 0)?"not-fixed":"fixed";

$moveInDate = $_POST['room-apply-move-in-date'];
$moveOutDate = isset($_POST['room-apply-move-out-date']) ? $_POST['room-apply-move-out-date'] : 0;
$note = $_POST['room-apply-note'];
$applicationDate = date('Y-m-d H:i:s');

// echo "Tenant ID : " . $tenantId . "<br>";
// echo "Room ID : " . $roomId . "<br>";
// echo "Rent Type : " . $rentType . "<br>";
// echo "Move in date : " . $moveInDate . "<br>";
// echo "Move out date : " . $moveOutDate . "<br>";
// echo "Note : " . $note . "<br>";
// echo "Application date : " . $applicationDate . "<br>";

// object values
$room->fetchRoom($roomId);
$houseId = $room->houseId;

$house->fetchHouse($houseId);
$landlordId = $house->ownerId;

$application->setApplication($roomId, $landlordId, $tenantId, $rentType, $moveInDate, $moveOutDate, $note, 0 , 0, 1, $applicationDate);
$immediateApplicationId = $application->registerApplication();

if ($immediateApplicationId == 0) {
    $link = "../room-details.php?roomId=$roomId" . '&submission=failure';
    header("location: $link");
} else {
    // create notification for landlord
    $landlordNotification->setApplicationNotification("room-application-submit", $roomId, $landlordId, $tenantId, $immediateApplicationId);
    $landlordNotification->whose = "landlord";
    $landlordNotification->register();
    $link = "../room-details.php?roomId=$roomId" . '&submission=success';
    header("location: $link");
}