<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['adminId']))
    header("Location: ../login.php");

if (isset($_GET['task']) && isset($_GET['houseId']) && $_GET['url']) {
    if ($_GET['houseId'] == '' || $_GET['url'] == '') {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            echo "No content found!";
        }
    } else {
        // retrieving data from the url
        $houseId = $_GET['houseId'];
        $url = $_GET['url'];
        $task = $_GET['task'];

        if ($task == 'verify' || $task == 'suspend') {

            // operation starts here
            include_once '../../../Class/house_class.php';

            $house = new House();

            $response = $house->houseOperation($task, $houseId);

            if ($response) {
                // notify user
                include_once '../../../Class/notification_class.php';
                $notification = new Notification();

                $landlordId = $house->getOwnerId($houseId);

                if ($task == 'verify') {
                    $notification->setHouseNotification(1, 'landlord', $landlordId, $houseId);
                    $notification->register();
                } elseif ($task == 'suspend') {
                    $notification->setHouseNotification(2, 'landlord', $landlordId, $houseId);
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


