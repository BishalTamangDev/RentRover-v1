<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['landlordUserId']))
    header("Location: ../login.php");

if (isset($_GET['roomId']) && isset($_GET['task']) && isset($_GET['url'])) {
    if ($_GET['roomId'] == '' || $_GET['task'] == '' || $_GET['url'] == '') {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            echo "No content found!";
        }
    } else {
        // retrieving data from the link
        $roomId = $_GET['roomId'];
        $url = $_GET['url'];
        $task = $_GET['task'];

        require_once '../../../class/house_class.php';

        $room = new Room();
        
        $tenantId = $room->getTenantId($roomId);
        $response = $room->roomOperation($task, $roomId);

        if($response){
            if($tenantId != 0){
                if($task == 'remove-tenant'){
                    // update tenancy history table
                    include_once '../../../Class/tenancy_history_class.php';
                    $tenancyHistory = new TenancyHistory();
                    $moveOutDate = date('Y-m-d H:i:s');
                    $response = $tenancyHistory->updateTenancyHistory($roomId, $tenantId, $moveOutDate);

                    if($response){
                        // notify tenant
                        include_once '../../../Class/notification_class.php';
                        $notification = new Notification();
                        $notification->setTenancyEndNotification($roomId, $tenantId);
                        $response = $notification->register();
                    }
                }
            }
        }
            
        header("Location: $url");
    }
} else {
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        echo "No content found!";
    }
}