<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['landlordUserId']))
    header("Location: ../login.php");

if (isset($_GET['announcementId']) && isset($_GET['task']) && isset($_GET['url'])) {
    if ($_GET['announcementId'] == '' || $_GET['task'] == '' || $_GET['url'] == '') {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            echo "No content found!";
        }
    } else {
        // retrieving data from the link
        $announcementId = $_GET['announcementId'];
        $url = $_GET['url'];
        $task = $_GET['task'];

        echo $announcementId."<br>";
        echo $task."<br>";
        echo $url."<br>";

        require_once '../../../class/announcement_class.php';

        $announcement = new Announcement();

        if($task == 'remove'){
            $announcement->deleteAnnouncement($announcementId);
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