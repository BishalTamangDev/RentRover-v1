<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['landlordUserId']))
    header("Location: ../login.php");

if (isset($_GET['houseId']) && isset($_GET['task']) && isset($_GET['url'])) {
    if ($_GET['houseId'] == '' || $_GET['task'] == '' || $_GET['url'] == '') {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            echo "No content found!";
        }
    } else {
        // retrieving data from the link
        $houseId = $_GET['houseId'];
        $url = $_GET['url'];
        $task = $_GET['task'];

        require_once '../../../class/house_class.php';

        $house = new House();
        
        $response =  $house->houseOperation($task, $houseId);
        
        if ($response) {
            // remove all the rooms of this house
            $room = new Room();
            $response = $room->roomOperation("removeAllRooms", $houseId);
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