<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['adminId']))
    header("Location: ../login.php");

if (isset($_GET['id']) && $_GET['url']) {
    if ($_GET['id'] == '' || $_GET['url'] == '') {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }else{
            echo "No content found!";
        }
    } else {
        // retrieving data from the url
        $id = $_GET['id'];
        $url = $_GET['url'];

        include_once '../../../class/announcement_class.php';

        $announcement = new Announcement();

        $response = $announcement->deleteAnnouncement($id);

        if($response){
            // delete all the responses of this announcement
            $announcementResponse = new AnnouncementResponse();
            $announcementResponse->deleteAnnouncementResponseByParent($id);
        }

        header("location: $url");
    }
} else {
    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}


