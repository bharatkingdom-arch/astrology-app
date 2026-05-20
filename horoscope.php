<?php
session_start();
require __DIR__ . '/header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Zodiac signs array with names and symbols
$zodiac_signs = [
    ['name' => 'Aries', 'date' => 'Mar 21 - Apr 19', 'symbol' => '♈'],
    ['name' => 'Taurus', 'date' => 'Apr 20 - May 20', 'symbol' => '♉'],
    ['name' => 'Gemini', 'date' => 'May 21 - Jun 20', 'symbol' => '♊'],
    ['name' => 'Cancer', 'date' => 'Jun 21 - Jul 22', 'symbol' => '♋'],
    ['name' => 'Leo', 'date' => 'Jul 23 - Aug 22', 'symbol' => '♌'],
    ['name' => 'Virgo', 'date' => 'Aug 23 - Sep 22', 'symbol' => '♍'],
    ['name' => 'Libra', 'date' => 'Sep 23 - Oct 22', 'symbol' => '♎'],
    ['name' => 'Scorpio', 'date' => 'Oct 23 - Nov 21', 'symbol' => '♏'],
    ['name' => 'Sagittarius', 'date' => 'Nov 22 - Dec 21', 'symbol' => '♐'],
    ['name' => 'Capricorn', 'date' => 'Dec 22 - Jan 19', 'symbol' => '♑'],
    ['name' => 'Aquarius', 'date' => 'Jan 20 - Feb 18', 'symbol' => '♒'],
    ['name' => 'Pisces', 'date' => 'Feb 19 - Mar 20', 'symbol' => '♓']
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
                <p style="font-size:12px; color:var(--text-3);"><?= $sign['date'] ?></p>
            </a>
            <?php endforeach; ?>

        </div>
        
    </div>
</section>

<?php require __DIR__ . '/bottom.php'; ?>
