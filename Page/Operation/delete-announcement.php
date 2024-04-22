<?php
include_once '../../class/connection_class.php';
include_once '../../class/announcement_class.php';
$connectionObj = new DatabaseConnection();

$id = $_GET['id'];
$url = $_GET['url'];

$temp = new Announcement();
$temp2 = new AnnouncementResponse();

$temp->deleteAnnouncement($id);
$temp2->deleteAnnouncementResponseByParent($id, $url);