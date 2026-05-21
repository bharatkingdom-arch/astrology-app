<?php
session_start();
require __DIR__ . '/header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$sign = $_GET['sign'] ?? 'mesha';
$sign = strtolower(trim($sign));

$lang = $_GET['lang'] ?? 'en';
if ($lang !== 'en' && $lang !== 'te') {
    $lang = 'en';
}

$zodiac_data = [
    'mesha' => ['name_en' => 'Mesha (Aries)', 'name_te' => 'మేష రాశి (Mesha)', 'symbol' => '🐏'],
    'vrishabha' => ['name_en' => 'Vrishabha (Taurus)', 'name_te' => 'వృషభ రాశి (Vrishabha)', 'symbol' => '🐂'],
    'mithuna' => ['name_en' => 'Mithuna (Gemini)', 'name_te' => 'మిథున రాశి (Mithuna)', 'symbol' => '👫'],
    'karka' => ['name_en' => 'Karka (Cancer)', 'name_te' => 'కర్కాటక రాశి (Karka)', 'symbol' => '🦀'],
    'simha' => ['name_en' => 'Simha (Leo)', 'name_te' => 'సింహ రాశి (Simha)', 'symbol' => '🦁'],
    'kanya' => ['name_en' => 'Kanya (Virgo)', 'name_te' => 'కన్య రాశి (Kanya)', 'symbol' => '👧'],
    'tula' => ['name_en' => 'Tula (Libra)', 'name_te' => 'తులా రాశి (Tula)', 'symbol' => '⚖️'],
    'vrischika' => ['name_en' => 'Vrischika (Scorpio)', 'name_te' => 'వృశ్చిక రాశి (Vrischika)', 'symbol' => '🦂'],
    'dhanu' => ['name_en' => 'Dhanu (Sagittarius)', 'name_te' => 'ధనుస్సు రాశి (Dhanu)', 'symbol' => '🏹'],
    'makara' => ['name_en' => 'Makara (Capricorn)', 'name_te' => 'మకర రాశి (Makara)', 'symbol' => '🐊'],
    'kumbha' => ['name_en' => 'Kumbha (Aquarius)', 'name_te' => 'కుంభ రాశి (Kumbha)', 'symbol' => '🏺'],
    'meena' => ['name_en' => 'Meena (Pisces)', 'name_te' => 'మీన రాశి (Meena)', 'symbol' => '🐟']
];

if (!array_key_exists($sign, $zodiac_data)) {
    $sign = 'mesha';
}

$current_sign = $zodiac_data[$sign];
$display_name = ($lang === 'te') ? $current_sign['name_te'] : $current_sign['name_en'];

require_once __DIR__ . '/engine/TransitEngine.php';
$predictions = TransitEngine::getDailyHoroscope($sign, $lang);

// Static text translations
$static = [
    'en' => [
        'daily_horoscope' => 'Daily Horoscope',
        'based_on' => 'Based on Current Vedic Planetary Transits',
        'overview_title' => 'Transit Overview for Today',
        'overview_desc' => "Welcome, {$display_name}! Today's predictions are dynamically formulated based on the precise current planetary transits (Lahiri Ayanamsa) computed in real-time. Read below to see how these transits uniquely influence your Moon sign today.",
        'view_another' => 'View Another Zodiac',
        'switch_lang' => 'తెలుగులో చదవండి (Read in Telugu)'
    ],
    'te' => [
        'daily_horoscope' => 'నేటి రాశి ఫలాలు',
        'based_on' => 'ప్రస్తుత వేద గ్రహ సంచారాల ఆధారంగా',
        'overview_title' => 'నేటి గ్రహ సంచారాల అవలోకనం',
        'overview_desc' => "స్వాగతం, {$display_name}! నేటి రాశి ఫలాలు ఖచ్చితమైన ప్రస్తుత గ్రహ స్థానాల (లాహిరి అయనాంశ) ఆధారంగా ఎప్పటికప్పుడు లెక్కించబడతాయి. ఈ గ్రహ సంచారాలు ఈరోజు మీ రాశిపై ఎలాంటి ప్రభావం చూపుతాయో కింద చదవండి.",
        'view_another' => 'మరొక రాశిని చూడండి',
        'switch_lang' => 'Read in English'
    ]
];

$t = $static[$lang];
$toggle_lang = ($lang === 'en') ? 'te' : 'en';

?>

<section class="kundli-section">
    <div class="kundli-container">
        
        <div class="kundli-title" style="margin-bottom:var(--s8);">
            
            <div style="text-align: right; margin-bottom: 20px;">
                <a href="?sign=<?= $sign ?>&lang=<?= $toggle_lang ?>" style="display: inline-block; padding: 6px 12px; border-radius: 20px; border: 1px solid var(--amber); color: var(--amber); font-size: 13px; text-decoration: none;">
                    <?= $t['switch_lang'] ?>
                </a>
            </div>

            <div style="font-size: 64px; color: var(--amber); margin-bottom: 10px; line-height: 1;"><?= $current_sign['symbol'] ?></div>
            <h1 style="text-transform: uppercase; letter-spacing: 2px;"><?= $t['daily_horoscope'] ?>: <?= $display_name ?></h1>
            <p><?= $t['based_on'] ?></p>
            <div class="kundli-divider"></div>
        </div>

        <div style="max-width: 800px; margin: 0 auto;">
            
            <div style="background:var(--bg-tertiary); border:1px solid var(--border); padding:var(--s7); border-radius:var(--r-lg); margin-bottom:var(--s6);">
                <h3 style="color:var(--amber); margin-bottom:var(--s4); font-size:20px;"><?= $t['overview_title'] ?></h3>
                <p style="color:var(--text-2); font-size:15px; line-height:1.8;">
                    <?= $t['overview_desc'] ?>
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
                <a href="horoscope.php" class="generate-btn" style="display:inline-block; max-width:300px; text-decoration:none;"><?= $t['view_another'] ?></a>
            </div>

        </div>
        
    </div>
</section>

<?php require __DIR__ . '/bottom.php'; ?>
