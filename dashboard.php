<?php
session_start();

if(!isset($_SESSION['user_email'])){
    header("Location: freekundali.php");
    exit;
}
?>

<h1>Welcome <?= $_SESSION['user_name'] ?></h1>

<p>Your email: <?= $_SESSION['user_email'] ?></p>

<a href="freekundali.php">Generate Kundli</a>