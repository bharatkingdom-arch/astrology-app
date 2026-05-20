<?php
session_start();
require __DIR__ . '/header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Zodiac signs array with names and symbols
$zodiac_signs = [
    ['name' => 'Aries', 'symbol' => '♈'],
    ['name' => 'Taurus', 'symbol' => '♉'],
    ['name' => 'Gemini', 'symbol' => '♊'],
    ['name' => 'Cancer', 'symbol' => '♋'],
    ['name' => 'Leo', 'symbol' => '♌'],
    ['name' => 'Virgo', 'symbol' => '♍'],
    ['name' => 'Libra', 'symbol' => '♎'],
    ['name' => 'Scorpio', 'symbol' => '♏'],
    ['name' => 'Sagittarius', 'symbol' => '♐'],
    ['name' => 'Capricorn', 'symbol' => '♑'],
    ['name' => 'Aquarius', 'symbol' => '♒'],
    ['name' => 'Pisces', 'symbol' => '♓']
];
?>

<section class="kundli-section">
    <div class="kundli-container">
        
        <div class="kundli-title">
            <h1>Today's Horoscope</h1>
            <p>Select your zodiac sign to read your daily horoscope prediction.</p>
            <div class="kundli-divider"></div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--s5); margin-top: var(--s8);">
            
            <?php foreach($zodiac_signs as $sign): ?>
            <a href="horoscope-details.php?sign=<?= strtolower($sign['name']) ?>" class="comp-card" style="display:block; text-decoration: none;">
                <div class="comp-icon" style="font-size: 28px; line-height: 1;"><?= $sign['symbol'] ?></div>
                <h4 style="font-size:18px; margin-bottom:5px; color:var(--amber);"><?= $sign['name'] ?></h4>
            </a>
            <?php endforeach; ?>

        </div>
        
    </div>
</section>

<?php require __DIR__ . '/bottom.php'; ?>
