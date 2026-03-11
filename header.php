<?php
// Since DocumentRoot is /var/www/html/astrolook
// Base URL should always be root "/"
$BASE_URL = "/";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Astroloak</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= $BASE_URL ?>style.css">
</head>

<body>

<header class="navbar">
    <div class="nav-wrapper">

        <!-- LOGO SECTION -->
        <div class="logo-section">
            <div class="logo-circle">☀</div>
            <div class="logo-text">Astroloak</div>
        </div>

        <!-- MENU SECTION -->
        <div class="menu-section">

            <!-- TOP MENU -->
            <div class="top-menu">
                <a href="<?= $BASE_URL ?>freekundali.php">Free Kundli</a>
                <a href="#">Kundli Matching</a>
                <a href="#">Compatibility</a>
                <div class="dropdown">
    <a href="#">Calculators ▼</a>
    <div class="dropdown-content">
        <a href="<?= $BASE_URL ?>calculators/PRcalculator.php">PR Calculator</a>
        
        <!-- Add more calculators here -->
    </div>
</div>
                <a href="#">Horoscopes ▼</a>
                <a href="#">Eng ▼</a>
                <button class="login-btn">Login</button>
            </div>

            <!-- BOTTOM MENU -->
            <div class="bottom-menu">
                <a href="#">Best Astrologers ▼</a>
                <a href="#">Chat with Astrologer</a>
                <a href="#">Talk to Astrologer</a>
                <a href="#">Astromall</a>
                <a href="#">Astroloak Store</a>
                <a href="#">Blogs ▼</a>
            </div>

        </div>

    </div>
</header>