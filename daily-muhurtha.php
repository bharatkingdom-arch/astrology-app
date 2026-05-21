<?php
session_start();

require_once __DIR__ . '/engine/SunriseSunset.php';
require_once __DIR__ . '/engine/Panchanga.php';
require_once __DIR__ . '/engine/AdvancedPanchanga.php';

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
$advancedPanchanga = AdvancedPanchanga::calculate($timestamp, $lat, $lon, $timezone, $sunLon, $moonLon, $panchanga);

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
        "start" => date("h:i A", (int)$currentLagTime),
        "end" => date("h:i A", (int)$endTime),
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

<section class="kundli-section">
<div class="kundli-container">

    <div class="kundli-title">
        <h1>Panchanga & Muhurta</h1>
        <p>Discover the most auspicious times of the day based on Vedic Astrology.</p>
        <div class="kundli-divider"></div>
    </div>

    <!-- TABS NAVIGATION -->
    <div class="kundli-tabs">
        <a href="javascript:void(0)" class="active" onclick="showTab('tab-panchanga', this)">Panchanga</a>
        <a href="javascript:void(0)" onclick="showTab('tab-lagna', this)">Lagna</a>
        <a href="javascript:void(0)" onclick="showTab('tab-hora', this)">Hora</a>
        <a href="javascript:void(0)" onclick="showTab('tab-choghadiya', this)">Choghadiya</a>
        <a href="javascript:void(0)" onclick="showTab('tab-gowri', this)">Gowri</a>
    </div>

    <style>
    .tab-content { display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease-in; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .good-time { color: #2ecc71; font-weight: bold; }
    .evil-time { color: #e74c3c; font-weight: bold; }
    </style>

    <!-- META INFO -->
    <div class="details-container" style="margin-top: 20px;">
        <div class="details-row">
            <div class="details-box">
                <div class="detail-item">
                    <span>Date (Sunrise Time)</span>
                    <span><?= $currentDate ?></span>
                </div>
                <div class="detail-item">
                    <span>Location</span>
                    <span><?= $place ?></span>
                </div>
                <div class="detail-item">
                    <span>Time Zone</span>
                    <span><?= ($timezone > 0 ? "+" : "") . sprintf("%02d:%02d", floor($timezone), ($timezone - floor($timezone)) * 60) ?></span>
                </div>
                <div class="detail-item">
                    <span>Coordinates</span>
                    <span>Lat: <?= $lat ?>, Lon: <?= $lon ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- PANCHANGA TAB -->
    <div id="tab-panchanga" class="tab-content active">
        <div class="details-container" style="margin-top: 0;">
            <div class="details-row">
                <div class="details-box">
                    <h4>Vaara (Vedic Day)</h4>
                    <p style="color:var(--text-1); font-size:15px; font-weight:600;"><?= $panchanga['Vaara'] ?></p>
                </div>
                <div class="details-box">
                    <h4>Tithi</h4>
                    <p class="good-time" style="font-size:15px;"><?= $panchanga['Tithi']['name'] ?></p>
                    <p style="color:var(--text-2); font-size:13px; margin: 4px 0;"><?= $panchanga['Tithi']['type'] ?></p>
                    <p style="color:var(--text-3); font-size:12px;"><?= $panchanga['Tithi']['end'] ?></p>
                </div>
            </div>
            
            <div class="details-row">
                <div class="details-box">
                    <h4>Nakshatra</h4>
                    <p class="good-time" style="font-size:15px;"><?= $panchanga['Nakshatra']['name'] ?></p>
                    <p style="color:var(--text-3); font-size:12px; margin-top: 6px;"><?= $panchanga['Nakshatra']['end'] ?></p>
                </div>
                <div class="details-box">
                    <h4>Yoga</h4>
                    <p class="good-time" style="font-size:15px;"><?= $panchanga['Yoga']['name'] ?></p>
                    <p style="color:var(--text-3); font-size:12px; margin-top: 6px;"><?= $panchanga['Yoga']['end'] ?></p>
                </div>
                <div class="details-box">
                    <h4>Karana</h4>
                    <p class="good-time" style="font-size:15px;"><?= $panchanga['Karana']['name'] ?></p>
                    <p style="color:var(--text-3); font-size:12px; margin-top: 6px;"><?= $panchanga['Karana']['end'] ?></p>
                </div>
            </div>
            
            <?php if (!empty($advancedPanchanga)): ?>
            <div class="table-box" style="margin-top:20px;">
                <h4>Timings</h4>
                <table>
                    <?php foreach (($advancedPanchanga['Timings'] ?? []) as $label => $value): ?>
                    <tr>
                        <td style="font-weight:600; color:var(--text-1); width: 40%;"><?= $label ?></td>
                        <td><?= $value ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div class="table-box">
                <h4>Hindu Calendar</h4>
                <table>
                    <?php foreach (($advancedPanchanga['Calendar'] ?? []) as $label => $value): ?>
                    <tr>
                        <td style="font-weight:600; color:var(--text-1); width: 40%;"><?= $label ?></td>
                        <td><?= $value ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div class="table-box">
                <h4>Inauspicious Timings</h4>
                <table>
                    <?php foreach (($advancedPanchanga['Inauspicious'] ?? []) as $label => $value): ?>
                    <tr>
                        <td style="font-weight:600; color:var(--text-1); width: 40%;"><?= $label ?></td>
                        <td class="evil-time"><?= $value ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- LAGNA TAB -->
    <div id="tab-lagna" class="tab-content">
        <div class="details-container" style="margin-top: 0; margin-bottom: 20px;">
            <div class="details-row">
                <div class="details-box" style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <h4 style="margin-bottom:5px;">Vedic Sunrise</h4>
                        <p style="color:var(--text-1); font-weight:600; font-size: 16px;"><?= $sunriseStr ?> AM</p>
                    </div>
                    <div style="text-align:right;">
                        <h4 style="margin-bottom:5px;">Lagna at Sunrise</h4>
                        <p style="color:var(--text-1); font-weight:600; font-size: 16px;"><?= $lagnaAtSunrise ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-box">
            <h4>Lagna Timings</h4>
            <table>
                <tr>
                    <th>Lagna</th>
                    <th>Start Time - End Time</th>
                </tr>
                <?php foreach($lagnas as $l): ?>
                <tr>
                    <td style="font-weight:600; color:var(--text-1);"><?= $l['sign'] ?></td>
                    <td><?= $l['start'] ?> - <?= $l['end'] ?> <span style="color:var(--text-3);"><?= $l['is_next_day'] ? '*' : '' ?></span></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <!-- HORA TAB -->
    <div id="tab-hora" class="tab-content">
        <div class="table-box">
            <h4>Day Hora</h4>
            <table>
                <tr>
                    <th>Hora</th>
                    <th>Start Time - End Time</th>
                </tr>
                <?php foreach($horasDay as $h): ?>
                <tr>
                    <td style="font-weight:600; color:var(--text-1);"><?= $h['lord'] ?></td>
                    <td><?= $h['start'] ?> - <?= $h['end'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="table-box">
            <h4>Night Hora</h4>
            <table>
                <tr>
                    <th>Hora</th>
                    <th>Start Time - End Time</th>
                </tr>
                <?php foreach($horasNight as $h): ?>
                <tr>
                    <td style="font-weight:600; color:var(--text-1);"><?= $h['lord'] ?></td>
                    <td><?= $h['start'] ?> - <?= $h['end'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <!-- CHOGHADIYA TAB -->
    <div id="tab-choghadiya" class="tab-content">
        <div class="table-box">
            <h4>Day Choghadiya</h4>
            <table>
                <tr>
                    <th>Choghadiya</th>
                    <th>Start Time - End Time</th>
                </tr>
                <?php foreach($chogDay as $c): ?>
                <tr>
                    <td class="<?= $c['type'] == 'Good' ? 'good-time' : ($c['type'] == 'Evil' ? 'evil-time' : '') ?>" style="font-weight:600;">
                        <?= $c['name'] ?>
                    </td>
                    <td><?= $c['start'] ?> - <?= $c['end'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="table-box">
            <h4>Night Choghadiya</h4>
            <table>
                <tr>
                    <th>Choghadiya</th>
                    <th>Start Time - End Time</th>
                </tr>
                <?php foreach($chogNight as $c): ?>
                <tr>
                    <td class="<?= $c['type'] == 'Good' ? 'good-time' : ($c['type'] == 'Evil' ? 'evil-time' : '') ?>" style="font-weight:600;">
                        <?= $c['name'] ?>
                    </td>
                    <td><?= $c['start'] ?> - <?= $c['end'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <!-- GOWRI TAB -->
    <div id="tab-gowri" class="tab-content">
        <div class="table-box">
            <h4>Day Gowri Panchanga</h4>
            <table>
                <tr>
                    <th>Gowri</th>
                    <th>Start Time - End Time</th>
                </tr>
                <?php foreach($gowriDay as $g): ?>
                <tr>
                    <td class="<?= $g['type'] == 'Good' ? 'good-time' : ($g['type'] == 'Evil' ? 'evil-time' : '') ?>" style="font-weight:600;">
                        <?= $g['name'] ?>
                    </td>
                    <td><?= $g['start'] ?> - <?= $g['end'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="table-box">
            <h4>Night Gowri Panchanga</h4>
            <table>
                <tr>
                    <th>Gowri</th>
                    <th>Start Time - End Time</th>
                </tr>
                <?php foreach($gowriNight as $g): ?>
                <tr>
                    <td class="<?= $g['type'] == 'Good' ? 'good-time' : ($g['type'] == 'Evil' ? 'evil-time' : '') ?>" style="font-weight:600;">
                        <?= $g['name'] ?>
                    </td>
                    <td><?= $g['start'] ?> - <?= $g['end'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

</div>
</section>

<script>
function showTab(tabId, btn) {
    // Hide all tab content
    document.querySelectorAll('.tab-content').forEach(function(el) {
        el.classList.remove('active');
    });
    
    // Show selected tab content
    document.getElementById(tabId).classList.add('active');
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.kundli-tabs a').forEach(function(el) {
        el.classList.remove('active');
    });
    
    // Add active class to clicked button
    btn.classList.add('active');
}
</script>

<?php require 'footer.php'; ?>
