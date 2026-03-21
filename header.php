<?php
// Base URL for routing
$BASE_URL = "/";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Astroloak</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Astroloak — India's trusted astrology platform for Free Kundli, Horoscope, and Astrologer consultations.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= $BASE_URL ?>style.css">

    <!-- Places Autocomplete CSS -->
    <link rel="stylesheet" href="<?= $BASE_URL ?>css/places.css">

    <!-- Theme initialization (prevents FOUC) -->
    <script>
        if (localStorage.getItem('astroTheme') === 'light') {
            document.documentElement.classList.add('light-theme');
        }
    </script>
</head>
<body>

<!-- ==================== NAVBAR ==================== -->
<header class="navbar">
    <nav class="nav-wrapper">

        <!-- Logo -->
        <a href="<?= $BASE_URL ?>" class="logo-section">
            <div class="logo-circle">✦</div>
            <div class="logo-text">Astroloak</div>
        </a>

        <!-- Hamburger (mobile) -->
        <button class="hamburger" id="hamburgerBtn" aria-label="Toggle navigation menu" aria-expanded="false">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>

        <!-- Menu -->
        <div class="menu-section" id="menuSection" role="navigation">

            <!-- Primary nav -->
            <div class="top-menu">
                <a href="<?= $BASE_URL ?>freekundali.php">Free Kundli</a>
                <a href="#">Kundli Matching</a>
                <a href="#">Compatibility</a>
                <div class="dropdown">
                    <a href="#">Calculators ▾</a>
                    <div class="dropdown-content">
                        <a href="<?= $BASE_URL ?>calculators/PRcalculator.php">PR Calculator</a>
                    </div>
                </div>
                <a href="#">Horoscopes ▾</a>
                <a href="#">Eng ▾</a>
                <button class="theme-btn" id="themeToggle" aria-label="Toggle theme">
                    <span class="icon-moon">🌙</span>
                    <span class="icon-sun">☀️</span>
                </button>
                <button class="login-btn">Login</button>
            </div>

            <!-- Secondary nav -->
            <div class="bottom-menu">
                <a href="#">Best Astrologers ▾</a>
                <a href="#">Chat with Astrologer</a>
                <a href="#">Talk to Astrologer</a>
                <a href="#">Astromall</a>
                <a href="#">Astroloak Store</a>
                <a href="#">Blogs ▾</a>
            </div>

        </div>
    </nav>
</header>

<!-- ==================== SCRIPTS ==================== -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Theme toggle
    const themeBtn = document.getElementById('themeToggle');
    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            document.documentElement.classList.toggle('light-theme');
            const isLight = document.documentElement.classList.contains('light-theme');
            localStorage.setItem('astroTheme', isLight ? 'light' : 'dark');
        });
    }

    // Hamburger toggle
    const hamburger = document.getElementById('hamburgerBtn');
    const menu = document.getElementById('menuSection');

    if (hamburger && menu) {
        hamburger.addEventListener('click', () => {
            const isOpen = hamburger.classList.toggle('active');
            menu.classList.toggle('menu-open');
            hamburger.setAttribute('aria-expanded', isOpen.toString());
        });

        // Close menu on link click
        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                menu.classList.remove('menu-open');
                hamburger.setAttribute('aria-expanded', 'false');
            });
        });
    }
});
</script>