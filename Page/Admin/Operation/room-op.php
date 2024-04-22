<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['adminId']))
    header("Location: ../login.php");

if (isset($_GET['task']) && isset($_GET['roomId']) && $_GET['url']) {
    if ($_GET['roomId'] == '' || $_GET['url'] == '') {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            echo "No content found!";
        }
    } else {
        // retrieving data from the url
        $roomId = $_GET['roomId'];
        $url = $_GET['url'];
        $task = $_GET['task'];

        if ($task == 'verify' || $task == 'suspend') {

            // operation starts here
            include_once '../../../Class/house_class.php';

            $room = new Room();

            $response = $room->roomOperation($task, $roomId);

            if ($response) {
                // notify user
                include_once '../../../Class/notification_class.php';
                $notification = new Notification();

                $landlordId = $room->getOwnerId($roomId);

                if ($task == 'verify') {
                    $notification->setRoomNotification(1, 'landlord', $landlordId, $roomId);
                    $notification->register();
                } elseif ($task == 'suspend') {
                    $notification->setRoomNotification(2, 'landlord', $landlordId, $roomId);
                    $notification->register();
                }
            }
        }
        header("location: $url");
    }
} else {
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}


