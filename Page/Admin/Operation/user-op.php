<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['adminId']))
    header("Location: ../login.php");

if (isset($_GET['task']) && $_GET['userId'] && $_GET['url']) {
    if ($_GET['task'] == '' || $_GET['userId'] == '' || $_GET['url'] == '') {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            echo "No content found!";
        }
    } else {
        // retrieving data from the url
        $task = $_GET['task'];
        $userId = $_GET['userId'];
        $url = $_GET['url'];

        echo $task . ', ' . $userId . ', ' . $url;

        include '../../../Class/user_class.php';

        $user = new User();

        $response = $user->userOperation($task, $userId);
        
        if($response){
            include '../../../Class/notification_class.php';
            $notification = new Notification();

            $role = $user->getRole($userId);

            // notify user
            if($task == 'verify'){
                $notification->setUserNotification(1, strtolower($role), $userId, strtolower($role));
                $notification->register();
            }elseif($task == 'suspend'){
                $notification->setUserNotification(2, strtolower($role), $userId, strtolower($role));
                $notification->register();
            }
        }

        header("location: $url");
    }
} else {
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }else {
        echo "No content found!";
    }
}
