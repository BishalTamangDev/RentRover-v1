<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['landlordUserId']))
    header("Location: ../login.php");

if (isset($_GET['announcementResponseId']) && isset($_GET['task']) && isset($_GET['url'])) {
    if ($_GET['announcementResponseId'] == '' || $_GET['task'] == '' || $_GET['url'] == '') {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            echo "No content found!";
        }
    } else {
        // retrieving data from the link
        $announcementResponseId = $_GET['announcementResponseId'];
        $url = $_GET['url'];
        $task = $_GET['task'];

        // echo $announcementResponseId."<br>";
        // echo $task."<br>";
        // echo $url."<br>";

        require_once '../../../class/announcement_class.php';

        $announcementResponse = new AnnouncementResponse();

        if($task == 'remove')
            echo $announcementResponse->announcementResponseOperation($announcementResponseId, 'remove');

        header("Location: $url");
    }
} else {
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        echo "No content found!";
    }
}