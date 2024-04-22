<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['landlordUserId']) && !isset($_SESSION['tenantUserId']))
    header("Location: ../../index.php");

require_once '../../class/announcement_class.php';

if(isset($_GET['announcementId']) && isset($_GET['task']) && isset($_GET['userId']) && isset($_GET['url'])){
    $userId = (isset($_SESSION['landlordUserId']))?$_SESSION['landlordUserId']: $_SESSION['tenantUserId'];
    $url = $_GET['url'];
    $task = $_GET['task'];
    $announcementId = $_GET['announcementId'];

    echo "Announcement ID : ". $announcementId."<br>";
    echo "User ID : ".$userId."<br>";
    echo "Task : ".$task."<br>";
    echo "URL : ".$url."<br>";

    $state = true;
    
    if(isset($_SESSION['tenantUserId'])){
        if($_SESSION['tenantUserId'] != $_GET['userId'])
            $state = false;
    }elseif(isset($_SESSION['tenantUserId'])){
        if($_SESSION['tenantUserId'] != $_GET['userId'])
            $state = false;
    }

    if($state){
        $announcementResponse = New AnnouncementResponse();
        $response = $announcementResponse->announcementResponseOperation($announcementId, $task);
        header("location: $url");
    }
}else{
    return;
}