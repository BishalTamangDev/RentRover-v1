<?php 
include_once '../class/connection_class.php';
include_once '../class/announcement_class.php';

$connectionObj = new DatabaseConnection();

$id = $_GET['id'];
$url = $_GET['url'];

// echo $id.'<br>';
// echo $url.'<br>';

$temp = new AnnouncementResponse();
$temp->deleteAnnouncementResponse($id, $url);