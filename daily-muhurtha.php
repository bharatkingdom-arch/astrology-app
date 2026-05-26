<?php
session_start();

require_once __DIR__ . '/engine/SunriseSunset.php';
require_once __DIR__ . '/engine/Panchanga.php';
require_once __DIR__ . '/engine/AdvancedPanchanga.php';

// Location Handling
if (isset($_GET['lat']) && isset($_GET['lon'])) {
    $lat = floatval($_GET['lat']);
    $lon = floatval($_GET['lon']);
    $timezone = floatval($_GET['timezone'] ?? 5.5);
    $place = htmlspecialchars($_GET['place'] ?? "Custom Location");
} elseif (isset($_COOKIE['default_location'])) {
    $loc = json_decode($_COOKIE['default_location'], true);
    $lat = floatval($loc['lat']);
    $lon = floatval($loc['lon']);
    $timezone = floatval($loc['timezone']);
    $place = htmlspecialchars($loc['place']);
} else {
    $lat = 16.23;
    $lon = 80.64;
    $timezone = 5.5; // +05:30
    $place = "Tenali, Andhra Pradesh";
}

// Current Date
date_default_timezone_set('Asia/Kolkata');

$date_input = $_GET['date'] ?? null;
if ($date_input) {
    // Expected format DD-MM-YYYY
    $timestamp = strtotime($date_input . " 12:00:00");
    if (!$timestamp) {
        $timestamp = time();
    }
} else {
    $timestamp = time();
}

$currentDate = date("d-M-Y", $timestamp);
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

$srDt = new DateTime("@$sunriseTs");
$srUtDate = $srDt->format('d.m.Y');
$srUtTime = $srDt->format('H:i:s');

$cmd = "$swetestPath -edir$ephePath -b$srUtDate -ut$srUtTime -p0123456789t -fPl -sid1";
$output = shell_exec($cmd);
$lines = explode("\n", trim($output));
$sunLon = 0;
$moonLon = 0;
$allPlanets = [];
$short = [
    "Sun"=>"Su","Moon"=>"Mo","Mercury"=>"Me","Venus"=>"Ve",
    "Mars"=>"Ma","Jupiter"=>"Ju","Saturn"=>"Sa","Rahu"=>"Ra","Ketu"=>"Ke"
];
foreach ($lines as $line) {
    if (preg_match('/^(Sun|Moon|Mercury|Venus|Mars|Jupiter|Saturn|Uranus|Neptune|Pluto|true Node|True Node)\s+([\d\.]+)/', trim($line), $matches)) {
        $name = strtolower($matches[1]);
        $val = floatval($matches[2]);
        if ($name === 'true node') $planetName = 'Rahu';
        else $planetName = ucfirst($name);
        $allPlanets[$planetName] = ['decimal' => $val];
        
        if ($planetName == 'Sun') $sunLon = $val;
        if ($planetName == 'Moon') $moonLon = $val;
    }
}
if (isset($allPlanets['Rahu'])) {
    $ketuDecimal = fmod($allPlanets['Rahu']['decimal'] + 180, 360);
    if ($ketuDecimal < 0) $ketuDecimal += 360;
    $allPlanets['Ketu'] = ['decimal' => $ketuDecimal];
}

require_once __DIR__ . '/engine/Navamsha.php';
$d1 = [];
$d9 = [];
foreach ($allPlanets as $planet => $pData) {
    if (!isset($short[$planet])) continue;
    $deg = $pData['decimal'];
    $r1 = floor($deg / 30) + 1;
    $r9 = Navamsha::calculate($deg);
    $d1[$r1][] = ["short" => $short[$planet]];
    $d9[$r9][] = ["short" => $short[$planet]];
}


// JD Calculation
$y = (int)gmdate("Y", $sunriseTs);
$m = (int)gmdate("n", $sunriseTs);
$d = (int)gmdate("j", $sunriseTs);
if ($m <= 2) { $y -= 1; $m += 12; }
$A = floor($y / 100);
$B = 2 - $A + floor($A / 4);
$jd = floor(365.25 * ($y + 4716)) + floor(30.6001 * ($m + 1)) + $d + $B - 1524.5;
$hourFrac = ((int)gmdate("H", $sunriseTs) + ((int)gmdate("i", $sunriseTs)/60)) / 24;
$jd += $hourFrac;

// Create getPositions closure for exact calculations
$getPositions = function($ts) use ($swetestPath, $ephePath) {
    $dt = new DateTime("@" . (int)$ts);
    $utDate = $dt->format("d.m.Y");
    $utTime = $dt->format("H:i:s");
    $cmd = "$swetestPath -edir$ephePath -sid1 -b$utDate -ut$utTime -p01 -fPl";
    $out = shell_exec($cmd);
    if (!$out) return null;
    $sun = 0; $moon = 0;
    foreach (explode("\n", trim($out)) as $line) {
        if (strpos(trim($line), 'Sun') === 0) {
            $parts = preg_split('/\s+/', trim($line));
            $sun = floatval($parts[1]);
        } elseif (strpos(trim($line), 'Moon') === 0) {
            $parts = preg_split('/\s+/', trim($line));
            $moon = floatval($parts[1]);
        }
    }
    return ['sun' => $sun, 'moon' => $moon];
};

// Calculate Panchanga
$panchanga = Panchanga::calculate($sunLon, $moonLon, $jd, 0.98, 13.1, $sunriseTs, $getPositions);
$advancedPanchanga = AdvancedPanchanga::calculate($timestamp, $lat, $lon, $timezone, $sunLon, $moonLon, $panchanga);

// ------------------------------------------------------------------
// LAGNA CALCULATION (Precise using Swiss Ephemeris)
// ------------------------------------------------------------------
$signs = ["Mesha", "Vrishabha", "Mithuna", "Kataka", "Simha", "Kanya", "Thula", "Vrischika", "Dhanus", "Makara", "Kumbha", "Meena"];
$nakshatras = ["Ashwini","Bharani","Krittika","Rohini","Mrigashira","Ardra","Punarvasu","Pushya","Ashlesha","Magha","P-phalguni","U-phalguni","Hasta","Chitra","Swati","Vishakha","Anuradha","Jyeshtha","Moola","P-ashadha","U-ashadha","Shravana","Dhanishta","Shatabhisha","P-bhadra","U-bhadra","Revati"];

$sunriseSignIdx = floor($sunLon / 30);
$sunriseNakIdx = floor($sunLon / (13 + 1/3));

$lagnaAtSunrise = $signs[$sunriseSignIdx] . " - " . $nakshatras[$sunriseNakIdx];

$getAsc = function($ts) use ($lat, $lon, $swetestPath, $ephePath) {
    $dt = new DateTime("@$ts");
    $utDate = $dt->format("d.m.Y");
    $utTime = $dt->format("H:i:s");
    $cmd = "$swetestPath -edir$ephePath -sid1 -b$utDate -ut$utTime -house$lon,$lat,P -fPl";
    $out = shell_exec($cmd);
    if ($out) {
        $lines = explode("\n", trim($out));
        foreach ($lines as $line) {
            if (strpos(trim($line), 'Ascendant') === 0) {
                $parts = preg_split('/\s+/', trim($line));
                return floatval($parts[1]);
            }
        }
    }
    return 0;
};

$getMuhurthaVars = function($ts) use ($swetestPath, $ephePath) {
    $dt = new DateTime("@$ts");
    $utDate = $dt->format("d.m.Y");
    $utTime = $dt->format("H:i:s");
    $cmd = "$swetestPath -edir$ephePath -sid1 -b$utDate -ut$utTime -p01 -fPl";
    $out = shell_exec($cmd);
    $sun = 0; $moon = 0;
    if ($out) {
        $lines = explode("\n", trim($out));
        foreach ($lines as $line) {
            if (strpos(trim($line), 'Sun') === 0) {
                $parts = preg_split('/\s+/', trim($line));
                $sun = floatval($parts[1]);
            } elseif (strpos(trim($line), 'Moon') === 0) {
                $parts = preg_split('/\s+/', trim($line));
                $moon = floatval($parts[1]);
            }
        }
    }
    $diff = $moon - $sun;
    if ($diff < 0) $diff += 360;
    $tithi = floor($diff / 12) + 1;
    $nak = floor($moon / (13 + 1/3)) + 1;
    return ['tithi' => $tithi, 'nak' => $nak];
};

$lagnas = [];
$currentTs = $sunriseTs;
$endTs = $currentTs + 86400; // 24 hours
$vara_num = date('w', $currentTs) + 1; // 1 = Sunday

$lastAsc = $getAsc($currentTs);
$lastSign = floor($lastAsc / 30); // 0-based index for $signs

$lagnaRasiD1 = floor($lastAsc / 30) + 1;
$lagnaRasiD9 = Navamsha::calculate($lastAsc);
$d1[$lagnaRasiD1][] = ["short" => "Lagna"];
$d9[$lagnaRasiD9][] = ["short" => "Lagna"];

for ($i=1; $i<=12; $i++) {
    if (!isset($d1[$i])) $d1[$i] = [];
    if (!isset($d9[$i])) $d9[$i] = [];
}

$firstAsc = $getAsc($currentTs);
$firstSign = floor($firstAsc / 30);
$testTs = $currentTs;
$degCovered = $firstAsc - ($firstSign * 30);
if ($degCovered < 0) $degCovered += 30;

$jumpMins = floor($degCovered * 3.5);
if ($jumpMins < 5) $jumpMins = 5;
$testTs -= ($jumpMins * 60);

$testAsc = $getAsc($testTs);
$testSign = floor($testAsc / 30);

while ($testSign == $firstSign && $testTs > ($currentTs - 86400)) {
    $testTs -= 300;
    $testAsc = $getAsc($testTs);
    $testSign = floor($testAsc / 30);
}

while ($testSign != $firstSign && $testTs < $currentTs) {
    $testTs += 60;
    $testAsc = $getAsc($testTs);
    $testSign = floor($testAsc / 30);
}

$currentStart = $testTs;

for ($i = 0; $i < 12; $i++) {
    $remDeg = ($lastSign + 1) * 30 - $lastAsc;
    if ($remDeg <= 0) $remDeg += 30;
    
    $jumpMins = floor($remDeg * 3.5);
    if ($jumpMins < 5) $jumpMins = 5;
    
    $testTs = $currentStart + ($jumpMins * 60);
    $testAsc = $getAsc($testTs);
    $testSign = floor($testAsc / 30);
    
    while ($testSign == $lastSign && $testTs < $endTs) {
        $testTs += 300;
        $testAsc = $getAsc($testTs);
        $testSign = floor($testAsc / 30);
    }
    
    while ($testSign != $lastSign && $testTs > $currentStart) {
        $testTs -= 60;
        $testAsc = $getAsc($testTs);
        $testSign = floor($testAsc / 30);
    }
    
    $endOfLagna = $testTs;
    $mvars = $getMuhurthaVars($currentStart);
    
    // Panchaka Rahita Calculation: Tithi + Vara + Nakshatra + Lagna
    // Note: $lastSign is 0-based (0=Mesha), so we use $lastSign + 1 for math
    $lagna_num = $lastSign + 1; 
    $p_sum = $mvars['tithi'] + $vara_num + $mvars['nak'] + $lagna_num;
    $p_rem = $p_sum % 9;
    $is_rahita = in_array($p_rem, [3, 5, 7, 0]);
    
    $panchaka_names = [
        1 => "Mrityu Panchaka",
        2 => "Agni Panchaka",
        4 => "Raja Panchaka",
        6 => "Chora Panchaka",
        8 => "Roga Panchaka"
    ];
    $dosha_name = isset($panchaka_names[$p_rem]) ? $panchaka_names[$p_rem] : "Dosha";
    
    // Format display strings to avoid 1-minute visual gap
    $displayStart = isset($lagnas[count($lagnas)-1]) ? $lagnas[count($lagnas)-1]['end'] : date("h:i A", (int)$currentStart);
    $displayEnd = date("h:i A", (int)$endOfLagna);

    $lagnas[] = [
        "sign" => $signs[$lastSign],
        "start" => $displayStart,
        "end" => $displayEnd,
        "is_next_day" => ($endOfLagna > strtotime("midnight tomorrow", $sunriseTs)),
        "is_rahita" => $is_rahita,
        "rem" => $p_rem,
        "dosha_name" => $dosha_name
    ];
    
    $currentStart = $endOfLagna + 60;
    $lastAsc = $getAsc($currentStart);
    $lastSign = floor($lastAsc / 30);
    
    if ($currentStart >= $endTs) {
        break;
    }
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

function renderSouthChart($data, $showDegree = false, $lagnaRasi = null) {
    $positions = [
        12 => [10,20], 1=>[110,20], 2=>[210,20], 3=>[310,20],
        11 => [10,120], 4=>[310,120],
        10 => [10,220], 5=>[310,220],
        9 => [10,320], 8=>[110,320], 7=>[210,320], 6=>[310,320],
    ];
    
    echo '<svg viewBox="0 0 400 400" width="100%" style="background:#e6e0cf; max-width:400px; height:auto; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1);">';
    
    if ($lagnaRasi !== null && isset($positions[$lagnaRasi])) {
        $x = floor($positions[$lagnaRasi][0] / 100) * 100;
        $y = floor($positions[$lagnaRasi][1] / 100) * 100;
        echo '<rect x="'.$x.'" y="'.$y.'" width="100" height="100" fill="#fff6b3"/>';
    }
    
    echo '<rect x="0" y="0" width="400" height="400" fill="none" stroke="#444" stroke-width="2"/>';
    
    echo '<line x1="100" y1="0" x2="100" y2="400" stroke="#444"/>';
    echo '<line x1="200" y1="0" x2="200" y2="100" stroke="#444"/>';
    echo '<line x1="200" y1="300" x2="200" y2="400" stroke="#444"/>';
    echo '<line x1="300" y1="0" x2="300" y2="400" stroke="#444"/>';
    
    echo '<line x1="0" y1="100" x2="400" y2="100" stroke="#444"/>';
    echo '<line x1="0" y1="200" x2="100" y2="200" stroke="#444"/>';
    echo '<line x1="300" y1="200" x2="400" y2="200" stroke="#444"/>';
    echo '<line x1="0" y1="300" x2="400" y2="300" stroke="#444"/>';
    
    foreach ($positions as $rasi => $pos) {
        if (!empty($data[$rasi])) {
            $y = $pos[1];
            foreach ($data[$rasi] as $p) {
                echo '<text x="'.$pos[0].'" y="'.$y.'" font-size="12" fill="#000" font-weight="600">';
                echo $p['short'];
                if ($showDegree && isset($p['deg'])) {
                    echo ' <tspan font-size="10" fill="#555">'.$p['deg'].'</tspan>';
                }
                echo '</text>';
                $y += 16;
            }
        }
    }
    echo '</svg>';
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
        <a href="javascript:void(0)" onclick="showTab('tab-kundli', this)">Kundli</a>
    </div>

    <style>
    .tab-content { display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s ease-in; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .good-time { color: #2ecc71; font-weight: bold; }
    .evil-time { color: #e74c3c; font-weight: bold; }
    </style>

    <!-- META INFO -->
    <div style="display:flex; justify-content:flex-end; margin-top: 10px;">
        <button onclick="document.getElementById('edit-modal').style.display='block'" class="update-btn" style="padding: 10px 20px; font-size: 14px;">
            Edit Date & Place
        </button>
    </div>
    <div class="details-container" style="margin-top: 15px;">
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
                    <th>Panchaka Status</th>
                </tr>
                <?php foreach($lagnas as $l): ?>
                <tr style="<?= $l['is_rahita'] ? 'background: rgba(46, 204, 113, 0.05);' : 'background: rgba(231, 76, 60, 0.05);' ?>">
                    <td style="font-weight:600; color:var(--text-1);"><?= $l['sign'] ?></td>
                    <td><?= $l['start'] ?> - <?= $l['end'] ?> <span style="color:var(--text-3);"><?= $l['is_next_day'] ? '*' : '' ?></span></td>
                    <td>
                        <?php if($l['is_rahita']): ?>
                            <span style="color:#2ecc71; font-weight:bold; font-size:14px;">Panchaka Rahita</span>
                        <?php else: ?>
                            <span style="color:#e74c3c; font-weight:bold; font-size:14px;"><?= $l['dosha_name'] ?></span>
                        <?php endif; ?>
                    </td>
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

    <!-- KUNDLI TAB -->
    <div id="tab-kundli" class="tab-content">
        <div class="table-box" style="display:flex; flex-wrap:wrap; gap:30px; justify-content:center; padding: 20px;">
            <div style="flex:1; min-width:300px; text-align:center;">
                <h4 style="margin-bottom:15px; color:var(--text-primary);">Rasi (D1) at Sunrise</h4>
                <?php renderSouthChart($d1, false, $lagnaRasiD1); ?>
            </div>
            <div style="flex:1; min-width:300px; text-align:center;">
                <h4 style="margin-bottom:15px; color:var(--text-primary);">Navamsa (D9) at Sunrise</h4>
                <?php renderSouthChart($d9, false, $lagnaRasiD9); ?>
            </div>
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

// Edit Modal Logic
const editModal = document.getElementById('edit-modal');
const apiKey = "fce70220d8a54a3b898d9363403bcae1";
const placeInput = document.getElementById("edit_place");
const suggestionsBox = document.getElementById("edit_suggestions");
let placeTimeout = null;

placeInput.addEventListener("input", function() {
    clearTimeout(placeTimeout);
    const text = this.value;
    
    if(text.length < 3) {
        suggestionsBox.innerHTML = "";
        suggestionsBox.style.display = "none";
        return;
    }
    
    placeTimeout = setTimeout(async () => {
        let url = "https://api.geoapify.com/v1/geocode/autocomplete?text=" + encodeURIComponent(text) + "&limit=5&apiKey=" + apiKey;
        let res = await fetch(url);
        let data = await res.json();
        
        suggestionsBox.innerHTML = "";
        suggestionsBox.style.display = "block";
        
        if(!data.features.length){
            suggestionsBox.innerHTML = "<div class='place-empty' style='padding:10px; color:#666;'>No results</div>";
            return;
        }
        
        data.features.forEach(place => {
            let item = document.createElement("div");
            item.className = "place-item";
            item.style.padding = "10px";
            item.style.cursor = "pointer";
            item.style.borderBottom = "1px solid #eee";
            item.innerText = place.properties.formatted;
            
            item.onclick = function() {
                placeInput.value = place.properties.formatted;
                document.getElementById("edit_lat").value = place.properties.lat;
                document.getElementById("edit_lon").value = place.properties.lon;
                
                // Get timezone mapping roughly from Geoapify timezone
                if (place.properties.timezone && place.properties.timezone.offset_STD) {
                    let offsetString = place.properties.timezone.offset_STD; // e.g., "+05:30"
                    let sign = offsetString.charAt(0) === '-' ? -1 : 1;
                    let parts = offsetString.substring(1).split(':');
                    let hours = parseInt(parts[0]);
                    let minutes = parseInt(parts[1]);
                    document.getElementById("edit_timezone").value = sign * (hours + (minutes / 60));
                } else {
                    document.getElementById("edit_timezone").value = "5.5"; // default fallback
                }
                
                suggestionsBox.innerHTML = "";
                suggestionsBox.style.display = "none";
            };
            suggestionsBox.appendChild(item);
        });
    }, 300);
});

// Custom Date Selectors Logic
function updateHiddenDate() {
    let d = document.getElementById('edit_day').value;
    let m = document.getElementById('edit_month').value;
    let y = document.getElementById('edit_year').value;
    document.getElementById('edit_date').value = d + '-' + m + '-' + y;
}
document.getElementById('edit_day').addEventListener('change', updateHiddenDate);
document.getElementById('edit_month').addEventListener('change', updateHiddenDate);
document.getElementById('edit_year').addEventListener('change', updateHiddenDate);

// Close modal if clicking outside
window.onclick = function(event) {
    if (event.target == editModal) {
        editModal.style.display = "none";
    }
}
</script>

<style>
/* Edit Modal Styles */
.modal {
    display: none; 
    position: fixed; 
    z-index: 1000; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    background-color: rgba(0,0,0,0.5); 
}
.modal-content {
    background-color: #fff;
    margin: 10% auto; 
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    position: relative;
}
.close-btn {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    margin-top: -10px;
}
.close-btn:hover { color: #333; }
.modal-form label {
    display: block;
    margin-top: 15px;
    margin-bottom: 5px;
    font-weight: 600;
    color: var(--text-primary);
}
.modal-form input {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
    box-sizing: border-box;
}
.place-suggestions {
    border: 1px solid #ddd;
    border-top: none;
    max-height: 200px;
    overflow-y: auto;
    background: #fff;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
.place-item:hover {
    background-color: #f1f1f1;
}
.update-btn {
    background: linear-gradient(135deg, #e67e22, #f39c12);
    color: white;
    border: none;
    padding: 12px 20px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3);
}
.update-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(230, 126, 34, 0.4);
}
</style>

<!-- Edit Modal HTML -->
<div id="edit-modal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="document.getElementById('edit-modal').style.display='none'">&times;</span>
        <h2 style="margin-top:0; color:var(--text-primary); border-bottom:1px solid #eee; padding-bottom:10px;">Edit Date & Place</h2>
        
        <form method="GET" action="daily-muhurtha.php" class="modal-form">
            <label>Date</label>
            <div style="display:flex; gap:10px;">
                <select id="edit_day" style="flex:1; padding:10px; border-radius:8px; border:1px solid #ddd; font-size:16px; background:#fff;">
                    <?php for($i=1; $i<=31; $i++) echo "<option value='".sprintf("%02d", $i)."'" . (date('d', $timestamp) == $i ? ' selected' : '') . ">".sprintf("%02d", $i)."</option>"; ?>
                </select>
                <select id="edit_month" style="flex:1; padding:10px; border-radius:8px; border:1px solid #ddd; font-size:16px; background:#fff;">
                    <?php for($i=1; $i<=12; $i++) echo "<option value='".sprintf("%02d", $i)."'" . (date('m', $timestamp) == $i ? ' selected' : '') . ">".date('M', mktime(0,0,0,$i,1))."</option>"; ?>
                </select>
                <select id="edit_year" style="flex:1; padding:10px; border-radius:8px; border:1px solid #ddd; font-size:16px; background:#fff;">
                    <?php 
                    $currY = date('Y');
                    for($i=$currY-100; $i<=$currY+50; $i++) echo "<option value='$i'" . (date('Y', $timestamp) == $i ? ' selected' : '') . ">$i</option>"; 
                    ?>
                </select>
            </div>
            <input type="hidden" id="edit_date" name="date" value="<?= date('d-m-Y', $timestamp) ?>">
            
            <label>Location</label>
            <input type="text" id="edit_place" name="place" value="<?= htmlspecialchars($place) ?>" autocomplete="off" required>
            <div id="edit_suggestions" class="place-suggestions" style="display:none;"></div>
            
            <input type="hidden" id="edit_lat" name="lat" value="<?= $lat ?>">
            <input type="hidden" id="edit_lon" name="lon" value="<?= $lon ?>">
            <input type="hidden" id="edit_timezone" name="timezone" value="<?= $timezone ?>">
            
            <button type="submit" class="update-btn" style="width:100%; margin-top: 25px;">Update Details</button>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>
