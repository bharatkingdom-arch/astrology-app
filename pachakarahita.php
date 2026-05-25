<?php
session_start();
require 'header.php';

$date_input = $_GET['date'] ?? date('d-m-Y'); // Format DD-MM-YYYY
$dateParts = explode('-', $date_input);
if (count($dateParts) == 3) {
    $day = $dateParts[0];
    $month = $dateParts[1];
    $year = $dateParts[2];
    $dateStr = sprintf("%02d.%02d.%04d", $day, $month, $year);
} else {
    $dateStr = date('d.m.Y');
}

$timeStr = "06:00:00"; // Sunrise approx
$lat = 17.3850; // Hyderabad
$lon = 78.4867;
$timezone = 5.5;

// Convert to UTC
$dt = DateTime::createFromFormat("d.m.Y H:i:s", "$dateStr $timeStr");
if($timezone >= 0) {
    $dt->modify("-" . floor($timezone) . " hours");
    $dt->modify("-" . (($timezone - floor($timezone))*60) . " minutes");
}
$utDate = $dt->format("d.m.Y");
$utTime = $dt->format("H:i:s");

$swetestPath = __DIR__ . '/swisseph/swetest';
$ephePath = __DIR__ . '/ephemeris';
if(!file_exists($ephePath)) {
    $ephePath = __DIR__ . '/swisseph/ephe';
}

// 1. Get Planets
$planetCommand = "$swetestPath -edir$ephePath -sid1 -b$utDate -ut$utTime -p0123456789t -fPls";
$planetOutput = shell_exec($planetCommand);

function decimalToDMS($decimal) {
    $decimal = fmod($decimal, 360);
    if ($decimal < 0) $decimal += 360;
    $deg = floor($decimal);
    $minFloat = ($decimal - $deg) * 60;
    $min = floor($minFloat);
    $sec = round(($minFloat - $min) * 60);
    if ($sec == 60) { $sec = 0; $min++; }
    if ($min == 60) { $min = 0; $deg++; }
    if ($deg == 360) { $deg = 0; }
    return sprintf("%d° %02d′ %02d″", $deg, $min, $sec);
}

$planets = [];
if ($planetOutput) {
    $lines = explode("\n", trim($planetOutput));
    foreach ($lines as $line) {
        if (preg_match('/^(Sun|Moon|Mercury|Venus|Mars|Jupiter|Saturn|Uranus|Neptune|Pluto|true Node|True Node)\s+([\d\.]+)/', trim($line), $matches)) {
            $name = strtolower($matches[1]);
            $val = floatval($matches[2]);
            if ($name === 'true node') $planetName = 'Rahu';
            else $planetName = ucfirst($name);
            $planets[$planetName] = ['decimal' => $val];
        }
    }
}

if (isset($planets['Rahu'])) {
    $ketuDecimal = fmod($planets['Rahu']['decimal'] + 180, 360);
    if ($ketuDecimal < 0) $ketuDecimal += 360;
    $planets['Ketu'] = ['decimal' => $ketuDecimal];
}

// 2. Get Ascendant (Lagna)
$houseCommand = "$swetestPath -edir$ephePath -sid1 -b$utDate -ut$utTime -house$lon,$lat,P -fPl";
$houseOutput = shell_exec($houseCommand);
$ascendant = 0;
if ($houseOutput) {
    $lines = explode("\n", trim($houseOutput));
    foreach ($lines as $line) {
        if (strpos(trim($line), 'Ascendant') === 0) {
            $parts = preg_split('/\s+/', trim($line));
            $ascendant = floatval($parts[1]);
        }
    }
}

// 3. Panchaka Rahita Calculation
$sun = $planets['Sun']['decimal'] ?? 0;
$moon = $planets['Moon']['decimal'] ?? 0;

$diff = $moon - $sun;
if ($diff < 0) $diff += 360;
$tithi_num = floor($diff / 12) + 1; // 1 to 30

$nak_num = floor($moon / (13 + 1/3)) + 1; // 1 to 27
$lagna_num = floor($ascendant / 30) + 1; // 1 to 12

// Weekday (Sunday = 1)
$vara_num = date('w', strtotime($dateParts[2]."-".$dateParts[1]."-".$dateParts[0])) + 1;

$total_sum = $tithi_num + $vara_num + $nak_num + $lagna_num;
$panchaka_rem = $total_sum % 9;

$panchaka_names = [
    1 => "Mrityu Panchaka (Inauspicious - Danger)",
    2 => "Agni Panchaka (Inauspicious - Fire)",
    4 => "Raja Panchaka (Inauspicious - Bad Results)",
    6 => "Chora Panchaka (Inauspicious - Evil Happenings)",
    8 => "Roga Panchaka (Inauspicious - Disease)"
];

if (in_array($panchaka_rem, [3, 5, 7, 0])) {
    $panchaka_result = "Panchaka Rahita (Highly Auspicious / Blemish-Free)";
    $panchaka_color = "var(--success, #2ecc71)";
} else {
    $panchaka_result = $panchaka_names[$panchaka_rem];
    $panchaka_color = "var(--danger, #e74c3c)";
}

// 4. Lagnas of the Day (Approximate starting from Sunrise Lagna)
$signs = [
    1=>"Aries (Mesha)", 2=>"Taurus (Vrishabha)", 3=>"Gemini (Mithuna)", 4=>"Cancer (Karka)",
    5=>"Leo (Simha)", 6=>"Virgo (Kanya)", 7=>"Libra (Tula)", 8=>"Scorpio (Vrischika)",
    9=>"Sagittarius (Dhanu)", 10=>"Capricorn (Makara)", 11=>"Aquarius (Kumbha)", 12=>"Pisces (Meena)"
];

$lagnas_of_day = [];
$current_l_num = $lagna_num;
for ($i=0; $i<12; $i++) {
    $lagnas_of_day[] = $signs[$current_l_num];
    $current_l_num++;
    if ($current_l_num > 12) $current_l_num = 1;
}

// 5. Build D1 and D9 Charts
require_once 'engine/Navamsha.php';
$d1 = [];
$d9 = [];

$short = [
    "Sun"=>"Su","Moon"=>"Mo","Mercury"=>"Me","Venus"=>"Ve",
    "Mars"=>"Ma","Jupiter"=>"Ju","Saturn"=>"Sa","Rahu"=>"Ra","Ketu"=>"Ke"
];

foreach ($planets as $planet => $pData) {
    if (!isset($short[$planet])) continue;
    $deg = $pData['decimal'];
    $r1 = floor($deg / 30) + 1;
    $r9 = Navamsha::calculate($deg);
    $d1[$r1][] = ["short" => $short[$planet], "deg" => decimalToDMS($deg)];
    $d9[$r9][] = ["short" => $short[$planet]];
}

$lagnaRasiD1 = floor($ascendant / 30) + 1;
$lagnaRasiD9 = Navamsha::calculate($ascendant);
$d1[$lagnaRasiD1][] = ["short" => "Lagna", "deg" => decimalToDMS($ascendant)];
$d9[$lagnaRasiD9][] = ["short" => "Lagna"];

for ($i=1; $i<=12; $i++) {
    if (!isset($d1[$i])) $d1[$i] = [];
    if (!isset($d9[$i])) $d9[$i] = [];
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

$nakshatras = [
    1=>"Ashwini",2=>"Bharani",3=>"Krittika",4=>"Rohini",
    5=>"Mrigashira",6=>"Ardra",7=>"Punarvasu",8=>"Pushya",
    9=>"Ashlesha",10=>"Magha",11=>"Purva Phalguni",
    12=>"Uttara Phalguni",13=>"Hasta",14=>"Chitra",
    15=>"Swati",16=>"Vishakha",17=>"Anuradha",
    18=>"Jyeshtha",19=>"Mula",20=>"Purva Ashadha",
    21=>"Uttara Ashadha",22=>"Shravana",23=>"Dhanishta",
    24=>"Shatabhisha",25=>"Purva Bhadrapada",
    26=>"Uttara Bhadrapada",27=>"Revati"
];

$weekdays = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

?>

<style>
.pr-wrapper { padding: 40px 20px; font-family: 'Inter', sans-serif; background: var(--bg-primary, #f8f9fa); min-height: 80vh; }
.pr-container { max-width: 1000px; margin: 0 auto; background: var(--card-bg, #fff); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
.pr-header { background: linear-gradient(135deg, #e67e22, #d35400); padding: 30px; text-align: center; color: white; }
.pr-header h1 { margin: 0 0 10px 0; font-size: 2.2rem; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
.pr-header p { margin: 0; font-size: 1.1rem; opacity: 0.9; }

.pr-content { padding: 30px; }

.pr-card { background: var(--bg-tertiary, #f8f9fa); border: 1px solid var(--border, #ecf0f1); border-radius: 12px; padding: 25px; margin-bottom: 30px; }
.pr-card h2 { color: var(--text-primary, #2c3e50); font-size: 1.5rem; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid var(--border, #ecf0f1); padding-bottom: 10px; }

.pr-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px; }
.pr-item { display: flex; flex-direction: column; }
.pr-label { font-size: 0.9rem; color: var(--text-secondary, #7f8c8d); margin-bottom: 5px; font-weight: 600; text-transform: uppercase; }
.pr-val { font-size: 1.1rem; color: var(--text-primary, #2c3e50); font-weight: 700; }

.pr-result { margin-top: 20px; padding: 20px; border-radius: 12px; text-align: center; font-size: 1.25rem; font-weight: bold; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }

.charts-row { display: flex; flex-wrap: wrap; gap: 30px; justify-content: center; margin-top: 20px; }
.chart-box { flex: 1; min-width: 300px; text-align: center; }
.chart-box h3 { margin-bottom: 15px; color: var(--text-primary, #2c3e50); }

.lagna-list { display: flex; flex-wrap: wrap; gap: 10px; }
.lagna-tag { background: rgba(230, 126, 34, 0.1); color: #d35400; padding: 8px 16px; border-radius: 20px; font-size: 0.95rem; font-weight: 600; border: 1px solid rgba(230, 126, 34, 0.2); }

body.dark-mode .pr-wrapper { background: #121212; }
body.dark-mode .pr-container { background: #1e1e1e; }
body.dark-mode .pr-card { background: #2a2a2a; border-color: #333; }
body.dark-mode .pr-card h2 { color: #ecf0f1; border-color: #333; }
body.dark-mode .pr-label { color: #bdc3c7; }
body.dark-mode .pr-val { color: #ecf0f1; }
</style>

<div class="pr-wrapper">
    <div class="pr-container">
        <div class="pr-header">
            <h1>Panchaka Rahita & Daily Details</h1>
            <p>Astrological Insights for Date: <?= htmlspecialchars($date_input) ?> (Calculated at Sunrise)</p>
        </div>

        <div class="pr-content">
            
            <div class="pr-card">
                <h2>Muhurtha Elements</h2>
                <div class="pr-grid">
                    <div class="pr-item">
                        <span class="pr-label">Tithi (Index)</span>
                        <span class="pr-val"><?= $tithi_num ?></span>
                    </div>
                    <div class="pr-item">
                        <span class="pr-label">Weekday (Index)</span>
                        <span class="pr-val"><?= $weekdays[$vara_num - 1] ?> (<?= $vara_num ?>)</span>
                    </div>
                    <div class="pr-item">
                        <span class="pr-label">Nakshatra (Index)</span>
                        <span class="pr-val"><?= $nakshatras[$nak_num] ?> (<?= $nak_num ?>)</span>
                    </div>
                    <div class="pr-item">
                        <span class="pr-label">Sunrise Lagna (Index)</span>
                        <span class="pr-val"><?= $signs[$lagna_num] ?> (<?= $lagna_num ?>)</span>
                    </div>
                </div>
                
                <div class="pr-result" style="background: <?= $panchaka_color ?>;">
                    Total Sum: <?= $total_sum ?> | Remainder: <?= $panchaka_rem ?> <br>
                    <?= $panchaka_result ?>
                </div>
                <p style="margin-top:15px; font-size:0.9rem; color:var(--text-secondary); text-align:center;">
                    <em>Formula: (Tithi + Vara + Nakshatra + Lagna) ÷ 9. Remainders 1, 2, 4, 6, 8 are inauspicious.</em>
                </p>
            </div>

            <div class="pr-card">
                <h2>Lagnas of the Day</h2>
                <p style="font-size: 0.95rem; color: var(--text-secondary); margin-bottom: 15px;">
                    Starting from the Sunrise Ascendant (<?= $signs[$lagna_num] ?>), the Lagna changes approximately every 2 hours in the following sequence throughout the day:
                </p>
                <div class="lagna-list">
                    <?php foreach($lagnas_of_day as $index => $l): ?>
                        <div class="lagna-tag"><?= ($index+1) ?>. <?= $l ?></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="pr-card">
                <h2>Charts at Sunrise (<?= $timeStr ?>)</h2>
                <div class="charts-row">
                    <div class="chart-box">
                        <h3>Rasi (D1)</h3>
                        <?php renderSouthChart($d1, false, $lagnaRasiD1); ?>
                    </div>
                    <div class="chart-box">
                        <h3>Navamsa (D9)</h3>
                        <?php renderSouthChart($d9, false, $lagnaRasiD9); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require 'bottom.php'; ?>
