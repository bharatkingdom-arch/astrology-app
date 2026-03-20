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

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= $BASE_URL ?>style.css">

    <!-- Places Autocomplete CSS -->
    <link rel="stylesheet" href="<?= $BASE_URL ?>css/places.css">

    <!-- Theme Initialization Script (Prevents FOUC) -->
    <script>
        if (localStorage.getItem('astroTheme') === 'light') {
            document.documentElement.classList.add('light-theme');
        }
    </script>

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
                <button class="theme-btn" id="themeToggle" aria-label="Toggle Theme">
                    <span class="icon-moon">🌙</span>
                    <span class="icon-sun">☀️</span>
                </button>
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('themeToggle');

        toggleBtn.addEventListener('click', () => {
            document.documentElement.classList.toggle('light-theme');
            const isLight = document.documentElement.classList.contains('light-theme');
            localStorage.setItem('astroTheme', isLight ? 'light' : 'dark');
        });
    });
</script>