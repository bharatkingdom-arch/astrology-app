<?php
session_start();
require __DIR__ . '/header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$sign = $_GET['sign'] ?? 'aries';
$sign = strtolower(trim($sign));

$zodiac_data = [
    'aries' => ['name' => 'Aries', 'date' => 'Mar 21 - Apr 19', 'symbol' => '♈'],
    'taurus' => ['name' => 'Taurus', 'date' => 'Apr 20 - May 20', 'symbol' => '♉'],
    'gemini' => ['name' => 'Gemini', 'date' => 'May 21 - Jun 20', 'symbol' => '♊'],
    'cancer' => ['name' => 'Cancer', 'date' => 'Jun 21 - Jul 22', 'symbol' => '♋'],
    'leo' => ['name' => 'Leo', 'date' => 'Jul 23 - Aug 22', 'symbol' => '♌'],
    'virgo' => ['name' => 'Virgo', 'date' => 'Aug 23 - Sep 22', 'symbol' => '♍'],
    'libra' => ['name' => 'Libra', 'date' => 'Sep 23 - Oct 22', 'symbol' => '♎'],
    'scorpio' => ['name' => 'Scorpio', 'date' => 'Oct 23 - Nov 21', 'symbol' => '♏'],
    'sagittarius' => ['name' => 'Sagittarius', 'date' => 'Nov 22 - Dec 21', 'symbol' => '♐'],
    'capricorn' => ['name' => 'Capricorn', 'date' => 'Dec 22 - Jan 19', 'symbol' => '♑'],
    'aquarius' => ['name' => 'Aquarius', 'date' => 'Jan 20 - Feb 18', 'symbol' => '♒'],
    'pisces' => ['name' => 'Pisces', 'date' => 'Feb 19 - Mar 20', 'symbol' => '♓']
];

if (!array_key_exists($sign, $zodiac_data)) {
    $sign = 'aries';
}

$current_sign = $zodiac_data[$sign];

// Mock daily predictions for demo purposes
$predictions = [
    'Personal' => 'Today is a great day to focus on your emotional well-being. Take some time for yourself to meditate or relax.',
    'Profession' => 'New opportunities are on the horizon. Stay alert and be ready to showcase your skills when the moment arrives.',
    'Health' => 'Your energy levels might fluctuate today. Ensure you stay hydrated and do not skip meals.',
    'Travel' => 'A short trip could prove to be extremely beneficial and refreshing for your mind.',
    'Luck' => 'The stars are aligned in your favor today. Trust your intuition when making quick decisions.'
];

?>

<section class="kundli-section">
    <div class="kundli-container">
        
        <div class="kundli-title" style="margin-bottom:var(--s8);">
            <div style="font-size: 64px; color: var(--amber); margin-bottom: 10px; line-height: 1;"><?= $current_sign['symbol'] ?></div>
            <h1 style="text-transform: uppercase; letter-spacing: 2px;">Daily Horoscope: <?= $current_sign['name'] ?></h1>
            <p><?= $current_sign['date'] ?></p>
            <div class="kundli-divider"></div>
        </div>

        <div style="max-width: 800px; margin: 0 auto;">
            
            <div style="background:var(--bg-tertiary); border:1px solid var(--border); padding:var(--s7); border-radius:var(--r-lg); margin-bottom:var(--s6);">
                <h3 style="color:var(--amber); margin-bottom:var(--s4); font-size:20px;">Overview for Today</h3>
                <p style="color:var(--text-2); font-size:15px; line-height:1.8;">
                    Welcome, <?= $current_sign['name'] ?>! The planetary positions today indicate a mix of vibrant energy and quiet reflection. You are likely to find a wonderful balance between work and personal life. Read below for detailed insights across different areas of your life today.
                </p>
            </div>
            
            <div class="grid-2">
                <?php foreach($predictions as $category => $text): ?>
                <div style="background:var(--bg-tertiary); border:1px solid var(--border); padding:var(--s5); border-radius:var(--r-lg);">
                    <h4 style="color:var(--amber); margin-bottom:var(--s2); font-size:16px;"><?= $category ?></h4>
                    <p style="color:var(--text-1); font-size:14px; line-height:1.6;"><?= $text ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align:center; margin-top:var(--s8);">
                <a href="horoscope.php" class="generate-btn" style="display:inline-block; max-width:300px; text-decoration:none;">View Another Zodiac</a>
            </div>

        </div>
        
    </div>
</section>

<?php require __DIR__ . '/bottom.php'; ?>
