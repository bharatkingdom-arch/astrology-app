<?php
session_start();

require_once __DIR__ . '/engine/SunriseSunset.php';
require_once __DIR__ . '/engine/Panchanga.php';

// Default Location (Tenali, Andhra Pradesh as per screenshot)
$lat = 16.23;
$lon = 80.64;
$timezone = 5.5; // +05:30
$place = "Tenali, Andhra Pradesh";

// Current Date
date_default_timezone_set('Asia/Kolkata');
$currentDate = date("d-M-Y");
$timestamp = time();
$dateObj = new DateTime("@$timestamp");
$utDate = $dateObj->format('d.m.Y');
$utTime = $dateObj->format('H:i:s');

// Get Sunrise and Sunset
$ss = new SunriseSunset();
$ssData = $ss->calculate(date("Y-m-d", $timestamp), $lat, $lon, $timezone);
$sunriseStr = $ssData['sunrise']; // e.g., 05:39:31
$sunsetStr = $ssData['sunset'];

$sunriseTs = strtotime(date("Y-m-d", $timestamp) . " " . $sunriseStr);
$sunsetTs = strtotime(date("Y-m-d", $timestamp) . " " . $sunsetStr);

$nextDayTs = $timestamp + 86400;
$nextSsData = $ss->calculate(date("Y-m-d", $nextDayTs), $lat, $lon, $timezone);
$nextSunriseTs = strtotime(date("Y-m-d", $nextDayTs) . " " . $nextSsData['sunrise']);

// Get Sun and Moon Longitudes at Sunrise (UTC for Sunrise)
$swetestPath = __DIR__ . '/swisseph/swetest';
$ephePath    = __DIR__ . '/ephemeris';
if (!file_exists($swetestPath)) {
    $swetestPath = '/app/swisseph/swetest';
    $ephePath = '/app/ephemeris';
}

$sunriseUtcTs = $sunriseTs - ($timezone * 3600);
$srDt = new DateTime("@$sunriseUtcTs");
$srUtDate = $srDt->format('d.m.Y');
$srUtTime = $srDt->format('H:i:s');

$cmd = "$swetestPath -edir$ephePath -b$srUtDate -ut$srUtTime -p01 -fPl -sid1";
$output = shell_exec($cmd);
$lines = explode("\n", trim($output));
$sunLon = 0;
$moonLon = 0;
foreach ($lines as $line) {
    if (strpos($line, 'Sun') !== false) {
        $parts = preg_split('/\s+/', trim($line));
        $sunLon = floatval($parts[1]);
    }
    if (strpos($line, 'Moon') !== false) {
        $parts = preg_split('/\s+/', trim($line));
        $moonLon = floatval($parts[1]);
    }
}

// JD Calculation
$y = (int)date("Y", $sunriseUtcTs);
$m = (int)date("n", $sunriseUtcTs);
$d = (int)date("j", $sunriseUtcTs);
if ($m <= 2) { $y -= 1; $m += 12; }
$A = floor($y / 100);
$B = 2 - $A + floor($A / 4);
$jd = floor(365.25 * ($y + 4716)) + floor(30.6001 * ($m + 1)) + $d + $B - 1524.5;
$hourFrac = ((int)date("H", $sunriseUtcTs) + ((int)date("i", $sunriseUtcTs)/60)) / 24;
$jd += $hourFrac;

// Calculate Panchanga
$panchanga = Panchanga::calculate($sunLon, $moonLon, $jd, 0.98, 13.1, $sunriseTs);

// ------------------------------------------------------------------
// LAGNA CALCULATION (Approximate based on Sun at Sunrise)
// ------------------------------------------------------------------
$signs = ["Mesha", "Vrishabha", "Mithuna", "Kataka", "Simha", "Kanya", "Thula", "Vrischika", "Dhanus", "Makara", "Kumbha", "Meena"];
$nakshatras = ["Ashwini","Bharani","Krittika","Rohini","Mrigashira","Ardra","Punarvasu","Pushya","Ashlesha","Magha","P-phalguni","U-phalguni","Hasta","Chitra","Swati","Vishakha","Anuradha","Jyeshtha","Moola","P-ashadha","U-ashadha","Shravana","Dhanishta","Shatabhisha","P-bhadra","U-bhadra","Revati"];

$sunriseSignIdx = floor($sunLon / 30);
$sunriseNakIdx = floor($sunLon / (13 + 1/3));

$lagnaAtSunrise = $signs[$sunriseSignIdx] . " - " . $nakshatras[$sunriseNakIdx];

$lagnas = [];
$currentLagLon = $sunLon;
$currentLagTime = $sunriseTs;
for ($i = 0; $i <= 12; $i++) {
    $idx = ($sunriseSignIdx + $i) % 12;
    $degRemainingInSign = 30 - fmod($currentLagLon, 30);
    // Approx 4 minutes per degree = 240 seconds
    $durationSeconds = $degRemainingInSign * 240;
    
    $endTime = $currentLagTime + $durationSeconds;
    
    $lagnas[] = [
        "sign" => $signs[$idx],
        "start" => date("h:i A", $currentLagTime),
        "end" => date("h:i A", $endTime),
        "is_next_day" => ($endTime > strtotime("midnight tomorrow", $sunriseTs))
    ];
    
    $currentLagLon += $degRemainingInSign;
    $currentLagTime = $endTime;
}

// ------------------------------------------------------------------
// HORA CALCULATION
// ------------------------------------------------------------------
$weekdayIdx = date("w", $sunriseTs);
$horaLords = ["Sun", "Venus", "Mercury", "Moon", "Saturn", "Jupiter", "Mars"];
$weekdayToHoraLordMap = [
    0 => 0, // Sunday -> Sun
    1 => 3, // Monday -> Moon
    2 => 6, // Tuesday -> Mars
    3 => 2, // Wednesday -> Mercury
    4 => 5, // Thursday -> Jupiter
    5 => 1, // Friday -> Venus
    6 => 4  // Saturday -> Saturn
];

$dayHoraLen = ($sunsetTs - $sunriseTs) / 12;
$nightHoraLen = ($nextSunriseTs - $sunsetTs) / 12;

$horasDay = [];
$horasNight = [];
$hLordIdx = $weekdayToHoraLordMap[$weekdayIdx];

// Day Horas
for ($i = 0; $i < 12; $i++) {
    $horasDay[] = [
        "lord" => $horaLords[$hLordIdx % 7],
        "start" => date("h:i A", (int)($sunriseTs + ($i * $dayHoraLen))),
        "end" => date("h:i A", (int)($sunriseTs + (($i+1) * $dayHoraLen)))
    ];
    $hLordIdx++;
}

// Night Horas
for ($i = 0; $i < 12; $i++) {
    $horasNight[] = [
        "lord" => $horaLords[$hLordIdx % 7],
        "start" => date("h:i A", (int)($sunsetTs + ($i * $nightHoraLen))),
        "end" => date("h:i A", (int)($sunsetTs + (($i+1) * $nightHoraLen)))
    ];
    $hLordIdx++;
}

// ------------------------------------------------------------------
// CHOGHADIYA CALCULATION
// ------------------------------------------------------------------
$choghadiyaNames = ["Amrut", "Rog", "Labh", "Shubh", "Udveg", "Chal", "Kal"];
$choghadiyaTypes = ["Good", "Evil", "Good", "Good", "Evil", "Neutral", "Evil"];

// Starting indices for Day Choghadiya based on weekday (Sun=0 to Sat=6)
$dayChoghadiyaStart = [4, 0, 1, 2, 3, 5, 6]; 
// Starting indices for Night Choghadiya
$nightChoghadiyaStart = [3, 5, 1, 4, 0, 2, 6]; 

$dayChogLen = ($sunsetTs - $sunriseTs) / 8;
$nightChogLen = ($nextSunriseTs - $sunsetTs) / 8;

$chogDay = [];
$chogNight = [];

$dIdx = $dayChoghadiyaStart[$weekdayIdx];
for ($i = 0; $i < 8; $i++) {
    $idx = $dIdx % 7;
    $chogDay[] = [
        "name" => $choghadiyaNames[$idx],
        "type" => $choghadiyaTypes[$idx],
        "start" => date("h:i A", (int)($sunriseTs + ($i * $dayChogLen))),
        "end" => date("h:i A", (int)($sunriseTs + (($i+1) * $dayChogLen)))
    ];
    $dIdx++;
}

$nIdx = $nightChoghadiyaStart[$weekdayIdx];
for ($i = 0; $i < 8; $i++) {
    $idx = $nIdx % 7;
    $chogNight[] = [
        "name" => $choghadiyaNames[$idx],
        "type" => $choghadiyaTypes[$idx],
        "start" => date("h:i A", (int)($sunsetTs + ($i * $nightChogLen))),
        "end" => date("h:i A", (int)($sunsetTs + (($i+1) * $nightChogLen)))
    ];
    $nIdx++;
}

// ------------------------------------------------------------------
// GOWRI PANCHANGA CALCULATION
// ------------------------------------------------------------------
$gowriNames = ["Udyog", "Amrut", "Rog", "Labh", "Dhana", "Shubh", "Visha"];
$gowriTypes = ["Neutral", "Good", "Evil", "Good", "Good", "Good", "Evil"];
// Weekday starts
$dayGowriStart = [0, 1, 2, 3, 4, 5, 6]; 
$nightGowriStart = [4, 5, 6, 0, 1, 2, 3]; 

$gowriDay = [];
$gowriNight = [];

$gDIdx = $dayGowriStart[$weekdayIdx];
for ($i = 0; $i < 8; $i++) {
    $idx = $gDIdx % 7;
    $gowriDay[] = [
        "name" => $gowriNames[$idx],
        "type" => $gowriTypes[$idx],
        "start" => date("h:i A", (int)($sunriseTs + ($i * $dayChogLen))), // Same duration as Choghadiya
        "end" => date("h:i A", (int)($sunriseTs + (($i+1) * $dayChogLen)))
    ];
    $gDIdx++;
}

$gNIdx = $nightGowriStart[$weekdayIdx];
for ($i = 0; $i < 8; $i++) {
    $idx = $gNIdx % 7;
    $gowriNight[] = [
        "name" => $gowriNames[$idx],
        "type" => $gowriTypes[$idx],
        "start" => date("h:i A", (int)($sunsetTs + ($i * $nightChogLen))),
        "end" => date("h:i A", (int)($sunsetTs + (($i+1) * $nightChogLen)))
    ];
    $gNIdx++;
}

require 'header.php';
?>

<style>
/* PANCHANGA PAGE STYLES */
.panchanga-header {
    background-color: var(--primary-color, #1e5a8c);
    color: white;
    padding: 15px;
    display: flex;
    align-items: center;
}
.panchanga-header h2 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
}

.muhurtha-nav {
    display: flex;
    background-color: var(--primary-color, #1e5a8c);
    color: rgba(255,255,255,0.7);
    overflow-x: auto;
}
.muhurtha-nav button {
    background: none;
    border: none;
    color: inherit;
    padding: 12px 20px;
    font-size: 1rem;
    cursor: pointer;
    white-space: nowrap;
}
.muhurtha-nav button.active {
    color: white;
    border-bottom: 3px solid #66b2b2;
}

.meta-info {
    background-color: #f4f6f8;
    padding: 15px;
    font-size: 0.9rem;
    color: #444;
    border-bottom: 1px solid #ddd;
}
.meta-info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}

.panchanga-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    margin: 10px;
    border-radius: 4px;
}
.panchanga-card-title {
    font-weight: bold;
    padding: 12px 15px;
    border-bottom: 1px solid #e0e0e0;
    background: #fafafa;
    color: #333;
}
.panchanga-card-content {
    padding: 12px 15px;
}
.highlight-green {
    color: #27ae60;
    font-weight: 500;
}
.highlight-red {
    color: #c0392b;
    font-weight: 500;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}
.data-table th, .data-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    text-align: left;
}
.data-table th {
    background: #f9f9f9;
    font-weight: 600;
    color: #555;
}
.section-title {
    background: #f0f0f0;
    padding: 10px 15px;
    font-weight: bold;
    color: #333;
    margin: 0;
}

body.dark-mode .meta-info { background: #222; color: #ccc; border-color: #333; }
body.dark-mode .panchanga-card { background: #1a1a1a; border-color: #333; }
body.dark-mode .panchanga-card-title { background: #222; color: #eee; border-color: #333; }
body.dark-mode .data-table th { background: #222; color: #ccc; border-color: #333; }
body.dark-mode .data-table td { border-color: #333; color: #ddd; }
body.dark-mode .section-title { background: #222; color: #eee; }
</style>

<div style="max-width: 600px; margin: 0 auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); background: var(--card-bg, #fff); min-height: 100vh;">

    <div class="panchanga-header">
        <a href="<?= $BASE_URL ?>" style="color:white; text-decoration:none; margin-right:15px;">&#8592;</a>
        <h2>Panchanga & Muhurta</h2>
    </div>

    <div class="muhurtha-nav" id="tabs">
        <button class="active" onclick="showTab('tab-panchanga', this)">Panchanga</button>
        <button onclick="showTab('tab-lagna', this)">Lagna</button>
        <button onclick="showTab('tab-hora', this)">Hora</button>
        <button onclick="showTab('tab-choghadiya', this)">Choghadiya</button>
        <button onclick="showTab('tab-gowri', this)">Gowri Panchanga</button>
    </div>

    <div class="meta-info">
        <div class="meta-info-row">
            <span>Date: <?= $currentDate ?> (Sunrise Time)</span>
            <div><button style="border:none;background:#1e5a8c;color:#fff;padding:2px 8px;">&#8592;</button> <button style="border:none;background:#1e5a8c;color:#fff;padding:2px 8px;">&#8594;</button></div>
        </div>
        <div class="meta-info-row">
            <span>Time Zone: <?= ($timezone > 0 ? "+" : "") . sprintf("%02d:%02d", floor($timezone), ($timezone - floor($timezone)) * 60) ?> | Place: <?= $place ?></span>
        </div>
        <div class="meta-info-row">
            <span>Longitude: <?= $lon ?> | Latitude: <?= $lat ?></span>
        </div>
    </div>

    <!-- PANCHANGA TAB -->
    <div id="tab-panchanga" class="tab-content active">
        <div class="panchanga-card">
            <div class="panchanga-card-title">Vaara (Vedic Day)</div>
            <div class="panchanga-card-content"><?= $panchanga['Vaara'] ?></div>
        </div>
        
        <div class="panchanga-card">
            <div class="panchanga-card-title">Tithi</div>
            <div class="panchanga-card-content">
                <div class="highlight-green"><?= $panchanga['Tithi']['name'] ?></div>
                <div style="margin:5px 0;"><?= $panchanga['Tithi']['type'] ?></div>
                <div style="color:#777; font-size:0.9rem;"><?= $panchanga['Tithi']['end'] ?></div>
            </div>
        </div>

        <div class="panchanga-card">
            <div class="panchanga-card-title">Nakshatra</div>
            <div class="panchanga-card-content">
                <div class="highlight-green"><?= $panchanga['Nakshatra']['name'] ?></div>
                <div style="color:#777; font-size:0.9rem; margin-top:5px;"><?= $panchanga['Nakshatra']['end'] ?></div>
            </div>
        </div>

        <div class="panchanga-card">
            <div class="panchanga-card-title">Yoga</div>
            <div class="panchanga-card-content">
                <div class="highlight-green"><?= $panchanga['Yoga']['name'] ?></div>
                <div style="color:#777; font-size:0.9rem; margin-top:5px;"><?= $panchanga['Yoga']['end'] ?></div>
            </div>
        </div>

        <div class="panchanga-card">
            <div class="panchanga-card-title">Karana</div>
            <div class="panchanga-card-content">
                <div class="highlight-green"><?= $panchanga['Karana']['name'] ?></div>
                <div style="color:#777; font-size:0.9rem; margin-top:5px;"><?= $panchanga['Karana']['end'] ?></div>
            </div>
        </div>
    </div>

    <!-- LAGNA TAB -->
    <div id="tab-lagna" class="tab-content">
        <div class="panchanga-card" style="display:flex; justify-content:space-between; align-items:center;">
            <div style="font-weight:600; padding:15px;">Vedic Sunrise<br><br>Lagna at Sunrise</div>
            <div style="padding:15px; text-align:right;">
                <?= $sunriseStr ?> AM<br><br>
                <?= $sunriseStr ?><br>(<?= $lagnaAtSunrise ?>)
            </div>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Lagna</th>
                    <th>Start Time - End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($lagnas as $l): ?>
                <tr>
                    <td style="font-weight:600;"><?= $l['sign'] ?></td>
                    <td><?= $l['start'] ?> - <?= $l['end'] ?> <?= $l['is_next_day'] ? '*' : '' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- HORA TAB -->
    <div id="tab-hora" class="tab-content">
        <div class="section-title">Day Hora</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Start Time - End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($horasDay as $h): ?>
                <tr>
                    <td style="font-weight:600;"><?= $h['lord'] ?></td>
                    <td><?= $h['start'] ?> - <?= $h['end'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="section-title">Night Hora</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Start Time - End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($horasNight as $h): ?>
                <tr>
                    <td style="font-weight:600;"><?= $h['lord'] ?></td>
                    <td><?= $h['start'] ?> - <?= $h['end'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- CHOGHADIYA TAB -->
    <div id="tab-choghadiya" class="tab-content">
        <div class="section-title">Day Choghadiya</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Choghadiya</th>
                    <th>Start Time - End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($chogDay as $c): ?>
                <tr>
                    <td class="<?= $c['type'] == 'Good' ? 'highlight-green' : ($c['type'] == 'Evil' ? 'highlight-red' : '') ?>" style="font-weight:600;"><?= $c['name'] ?></td>
                    <td><?= $c['start'] ?> - <?= $c['end'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="section-title">Night Choghadiya</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Choghadiya</th>
                    <th>Start Time - End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($chogNight as $c): ?>
                <tr>
                    <td class="<?= $c['type'] == 'Good' ? 'highlight-green' : ($c['type'] == 'Evil' ? 'highlight-red' : '') ?>" style="font-weight:600;"><?= $c['name'] ?></td>
                    <td><?= $c['start'] ?> - <?= $c['end'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- GOWRI TAB -->
    <div id="tab-gowri" class="tab-content">
        <div class="section-title">Day Gowri Panchanga</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Gowri</th>
                    <th>Start Time - End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($gowriDay as $g): ?>
                <tr>
                    <td class="<?= $g['type'] == 'Good' ? 'highlight-green' : ($g['type'] == 'Evil' ? 'highlight-red' : '') ?>" style="font-weight:600;"><?= $g['name'] ?></td>
                    <td><?= $g['start'] ?> - <?= $g['end'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="section-title">Night Gowri Panchanga</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Gowri</th>
                    <th>Start Time - End Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($gowriNight as $g): ?>
                <tr>
                    <td class="<?= $g['type'] == 'Good' ? 'highlight-green' : ($g['type'] == 'Evil' ? 'highlight-red' : '') ?>" style="font-weight:600;"><?= $g['name'] ?></td>
                    <td><?= $g['start'] ?> - <?= $g['end'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
function showTab(tabId, btn) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(function(el) {
        el.classList.remove('active');
    });
    // Show selected tab
    document.getElementById(tabId).classList.add('active');
    
    // Update button states
    document.querySelectorAll('.muhurtha-nav button').forEach(function(el) {
        el.classList.remove('active');
    });
    btn.classList.add('active');
}
</script>

<?php require 'footer.php'; ?>
