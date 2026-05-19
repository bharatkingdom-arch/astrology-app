<?php
session_start();

$data = $_SESSION['kundli_data'] ?? null;

if (!$data) {
    header("Location: freekundali.php");
    exit;
}

$planets   = $data['planets'] ?? [];
$S_b       = $planets['Sun']['decimal'] ?? 0;
$M_b       = $planets['Moon']['decimal'] ?? 0;

$dateParts = explode('-', $data['date']);
$timeParts = explode(':', $data['time']);

$birthDate = sprintf("%02d.%02d.%04d", $dateParts[0], $dateParts[1], $dateParts[2]);
$birthTime = sprintf("%02d:%02d:%02d", $timeParts[0], $timeParts[1], $timeParts[2]);
$timezone  = 5.5; // Defaulting to IST as in freekundali.php

require_once 'engine/TithiPraveshaEngine.php';

$birthYear = (int)$dateParts[2];
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Calculate only for the selected year
$results = TithiPraveshaEngine::calculateForYears($birthDate, $birthTime, $timezone, $S_b, $M_b, $selectedYear, 0);
$tpData = $results[0] ?? null;

$tpPlanets = [];
if ($tpData) {
    // tpData['date'] is "DD-MM-YYYY"
    // tpData['time'] is "hh:mm:ss A" (12 hour)
    
    // Convert to DD.MM.YYYY and HH:MM
    $dt = DateTime::createFromFormat("d-m-Y h:i:s A", $tpData['date'] . " " . $tpData['time']);
    if ($dt) {
        $apiDate = $dt->format("d.m.Y");
        $apiTime = $dt->format("H:i");

        $_GET['date'] = $apiDate;
        $_GET['time'] = $apiTime;
        $_GET['lat'] = $data['latitude'] ?? 0;
        $_GET['lon'] = $data['longitude'] ?? 0;
        $_GET['timezone'] = $timezone;

        ob_start();
        require __DIR__ . '/public/api/calculate.php';
        $apiResponse = ob_get_clean();
        header('Content-Type: text/html; charset=utf-8'); // Restore HTML header
        
        $apiDecoded = json_decode($apiResponse, true);
        if ($apiDecoded && isset($apiDecoded['planets'])) {
            $tpPlanets = $apiDecoded['planets'];
            $tpHouses = $apiDecoded['houses'] ?? [];
            $tpLagna = $tpHouses['Ascendant']['decimal'] ?? null;
        }
    }
}

/* ================= CHART MAPPING ================= */
$short = [
    "Sun"=>"Su","Moon"=>"Mo","Mercury"=>"Me","Venus"=>"Ve",
    "Mars"=>"Ma","Jupiter"=>"Ju","Saturn"=>"Sa",
    "Rahu"=>"Ra","Ketu"=>"Ke",
    "Uranus"=>"Ur","Neptune"=>"Ne","Pluto"=>"Pl"
];

$d1 = [];
for ($i=1; $i<=12; $i++) { $d1[$i] = []; }

foreach ($tpPlanets as $planet => $pData) {
    if (!isset($pData['decimal'])) continue;
    $degree = floatval($pData['decimal']);
    
    $pLabel = $short[$planet] ?? substr($planet, 0, 2);
    if (!empty($pData['retrograde'])) $pLabel .= '(R)';
    
    $rasi1 = floor($degree / 30) + 1;
    $d1[$rasi1][] = [
        "short" => $pLabel
    ];
}

$lagnaRasiD1 = null;
if ($tpLagna !== null) {
    $lagnaRasiD1 = floor($tpLagna / 30) + 1;
    $d1[$lagnaRasiD1][] = [
        "short" => "Lagna"
    ];
}

?>

<?php require 'header.php'; ?>

<section class="kundli-section">
<div class="kundli-container">

<h2>Kundli Details</h2>

<div class="kundli-tabs">
    <a href="basic-details.php">Basic</a>
    <a href="south-chart.php">Kundli</a>
    <a href="engine/kpdetails.php">KP</a>
    <a href="#">Ashtakavarga</a>
    <a href="#">Charts</a>
    <a href="#">Dasha</a>
    <a href="#">Free Report</a>
    <a href="panchanga.php">Panchanga</a>
    <a href="tithi-pravesha.php" class="active">Tithi Pravesha</a>
</div>

<div class="details-container">
    <div class="details-box" style="width: 100%;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Annual Tithi Pravesha</h3>
            <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                <label for="year" style="font-weight: bold;">Select Year:</label>
                <select name="year" id="year" style="padding: 8px; border-radius: 4px; border: 1px solid var(--border-color, #ccc); background: var(--card-bg, #fff); color: var(--text-color, #333);">
                    <?php for ($y = $birthYear; $y <= $birthYear + 100; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php if ($y == $selectedYear) echo 'selected'; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" style="padding: 8px 16px; background: var(--primary-color, #ff9f43); color: #fff; border: none; border-radius: 4px; cursor: pointer;">Get Details</button>
            </form>
        </div>

        <p style="margin-bottom: 20px; color: var(--text-muted, #ccc);">
            Tithi Pravesha marks the exact moment each year when the transit Moon and Sun are at the same angular distance as they were at the time of your birth, while the Sun is in its natal sidereal sign. This moment is considered your true Vedic birthday for the year.
        </p>

        <?php if (!$tpData): ?>
            <div style="padding: 20px; text-align: center; border: 1px solid var(--border-color, rgba(255,255,255,0.1)); border-radius: 8px;">
                No data available for the selected year.
            </div>
        <?php else: ?>
            <div style="background: var(--bg-card, rgba(255,255,255,0.05)); border: 1px solid var(--border-color, rgba(255,255,255,0.1)); border-radius: 8px; padding: 20px;">
                <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: space-around;">
                    <div style="text-align: center;">
                        <span style="display: block; font-size: 0.9rem; color: var(--text-muted, #ccc);">Year</span>
                        <strong style="font-size: 1.2rem;"><?php echo $tpData['year']; ?></strong>
                    </div>
                    <div style="text-align: center;">
                        <span style="display: block; font-size: 0.9rem; color: var(--text-muted, #ccc);">Exact Date</span>
                        <strong style="font-size: 1.2rem;"><?php echo htmlspecialchars($tpData['date']); ?></strong>
                    </div>
                    <div style="text-align: center;">
                        <span style="display: block; font-size: 0.9rem; color: var(--text-muted, #ccc);">Exact Time (Local)</span>
                        <strong style="font-size: 1.2rem; color: var(--primary-color, #ff9f43);"><?php echo htmlspecialchars($tpData['time']); ?></strong>
                    </div>
                    <div style="text-align: center;">
                        <span style="display: block; font-size: 0.9rem; color: var(--text-muted, #ccc);">Weekday</span>
                        <strong style="font-size: 1.2rem;"><?php echo htmlspecialchars($tpData['weekday']); ?></strong>
                    </div>
                </div>
            </div>

            <?php if (!empty($tpPlanets)): ?>
            <div style="margin-top: 30px; text-align: center;">
                <h4>Tithi Pravesha Chart (Rasi D1)</h4>
                <div style="display: flex; justify-content: center; margin-top: 15px;">
                    <?php renderSouthChart($d1, false, $lagnaRasiD1); ?>
                </div>
            </div>

            <div style="margin-top: 30px;">
                <h4>Planetary Positions at Tithi Pravesha</h4>
                <table class="data-table" style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <thead>
                        <tr style="background: var(--bg-card, rgba(255,255,255,0.05)); border-bottom: 1px solid var(--border-color, rgba(255,255,255,0.1));">
                            <th style="padding: 12px; text-align: left;">Planet</th>
                            <th style="padding: 12px; text-align: left;">Longitude (°)</th>
                            <th style="padding: 12px; text-align: left;">Retrograde</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tpPlanets as $planet => $pData): ?>
                        <tr style="border-bottom: 1px solid var(--border-color, rgba(255,255,255,0.05));">
                            <td style="padding: 12px; font-weight: bold;"><?php echo htmlspecialchars($planet); ?></td>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($pData['dms'] ?? ''); ?></td>
                            <td style="padding: 12px;"><?php echo (!empty($pData['retrograde']) ? 'Yes' : 'No'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

</div>
</section>

<?php require 'bottom.php'; ?>

<?php
/* ================= SOUTH CHART RENDER ================= */

function renderSouthChart($data, $showDegree = false, $lagnaRasi = null) {

$positions = [
    12 => [10,20], 1=>[110,20], 2=>[210,20], 3=>[310,20],
    11 => [10,120], 4=>[310,120],
    10 => [10,220], 5=>[310,220],
    9 => [10,320], 8=>[110,320], 7=>[210,320], 6=>[310,320],
];

echo '<svg viewBox="0 0 400 400" width="100%" style="background:#e6e0cf; max-width:400px; height:auto;">';

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

            echo '<text x="'.$pos[0].'" y="'.$y.'" font-size="12" fill="#000">';
            echo $p['short'];

            if ($showDegree && isset($p['deg'])) {
                echo ' '.$p['deg'];
            }

            echo '</text>';
            $y += 15;
        }
    }
}

echo '</svg>';
}
?>
