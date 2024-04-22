<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['tenantUserId']))
    header("Location: ../login.php");

if (isset($_GET['task']) && isset($_GET['notificationId']) && isset($_GET['url'])) {
    if ($_GET['task'] == '' || $_GET['notificationId'] == '' || $_GET['url'] == '') {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            echo "No content found!";
        }
    } else {
        include_once '../../../class/notification_class.php';

        // retrieving data from link
        $notificationId = $_GET['notificationId'];
        $task = $_GET['task'];
        $url = $_GET['url'];

        echo $notificationId, $task, $url;
        
        $notification = new Notification();

        if($task == "remove"){
            $notification = new Notification();
            $notification->deleteNotification($notificationId);            
        }
    }
    header("location: $url");
}else{
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        echo "No content found!";
    }
}