<?php
// staring session
if(!session_start())
    session_start();

if(isset($_SESSION['landlordUserId'])){
    unset($_SESSION['landlordUserId']);
    header("Location: ../../index.php");
}