<?php
// staring session
if (!session_start())
    session_start();

if (!isset($_SESSION['landlordUserId']))
    header("Location: ../login.php");

if (isset($_GET['task'])) {
    if ($_GET['task'] == '') {
        header("Location: ../profile-view.php");
    } else {
        // retrieving data from the link
        $task = $_GET['task'];

        echo $task."<br>";

        include_once '../../../Class/user_class.php';

        $user = new User();
        $user->userOperation('deactivate', $_SESSION['landlordUserId']);
    }
    header("Location: ../profile-view.php");
} else {
    header("Location: ../profile-view.php");
}