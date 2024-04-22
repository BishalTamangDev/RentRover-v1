<?php
// staring session
if (!session_start())
    session_start();

if (isset($_SESSION['tenantUserId'])) {
    unset($_SESSION['tenantUserId']);
    header("Location: ../../index.php");
}