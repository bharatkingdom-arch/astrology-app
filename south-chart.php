<?php
session_start();

require_once 'engine/Navamsha.php';

$data = $_SESSION['kundli_data'] ?? null;

if (!$data) {
    header("Location: freekundali.php");
    exit;
}

$planets = $data['planets'] ?? [];
$lagna = null;

if (isset($data['lagna']['decimal'])) {
    $lagna = $data['lagna']['decimal'];
} elseif (isset($data['lagna']) && is_numeric($data['lagna'])) {
    $lagna = $data['lagna'];
}

/* ================= SHORT NAMES ================= */

$short = [
    "Sun"=>"Su","Moon"=>"Mo","Mercury"=>"Me","Venus"=>"Ve",
    "Mars"=>"Ma","Jupiter"=>"Ju","Saturn"=>"Sa",
    "Rahu"=>"Ra","Ketu"=>"Ke",
    "Uranus"=>"Ur","Neptune"=>"Ne","Pluto"=>"Pl"
];

/* ================= HELPER FUNCTIONS ================= */

function getRasi($degree) {
    return floor($degree / 30) + 1;
}

function degreeInSign($degree) {
    $deg = fmod($degree, 30);
    if ($deg < 0) $deg += 30;

    $d = floor($deg);
    $mFloat = ($deg - $d) * 60;
    $m = floor($mFloat);
    $s = floor(($mFloat - $m) * 60);

    return sprintf("%02d° %02d' %02d\"", $d, $m, $s);
}

function getSignName($rasi) {
    $signs = [
        1=>"Aries",2=>"Taurus",3=>"Gemini",4=>"Cancer",
        5=>"Leo",6=>"Virgo",7=>"Libra",8=>"Scorpio",
        9=>"Sagittarius",10=>"Capricorn",11=>"Aquarius",12=>"Pisces"
    ];
    return $signs[$rasi] ?? '';
}

function getNakshatraPada($degree) {

    $nakshatras = [
        "Ashwini","Bharani","Krittika","Rohini","Mrigashira","Ardra",
        "Punarvasu","Pushya","Ashlesha","Magha","Purva Phalguni",
        "Uttara Phalguni","Hasta","Chitra","Swati","Vishakha",
        "Anuradha","Jyeshtha","Mula","Purva Ashadha","Uttara Ashadha",
        "Shravana","Dhanishta","Shatabhisha","Purva Bhadrapada",
        "Uttara Bhadrapada","Revati"
    ];

    $nakNumber = floor($degree / 13.333333);
    $nakName = $nakshatras[$nakNumber] ?? '';

    $balance = fmod($degree, 13.333333);
    $pada = floor($balance / 3.333333) + 1;

    return [$nakName, $pada];
}

/* ================= MAP D1 & D9 ================= */

$d1 = [];
$d9 = [];

foreach ($planets as $planet => $planetData) {

    if (!isset($planetData['decimal'])) continue;

    $degree = floatval($planetData['decimal']);

    // D1
    $rasi1 = getRasi($degree);
    $d1[$rasi1][] = [
        "short" => $short[$planet] ?? $planet,
        "deg"   => degreeInSign($degree)
    ];

    // D9
    $rasi9 = Navamsha::calculate($degree);
    $d9[$rasi9][] = [
        "short" => $short[$planet] ?? $planet
    ];
}

/* ---------- ADD LAGNA ---------- */

$lagnaRasiD1 = null;
$lagnaRasiD9 = null;

if ($lagna !== null) {

    $lagnaRasiD1 = getRasi($lagna);
    $d1[$lagnaRasiD1][] = [
        "short" => "Lagna",
        "deg"   => degreeInSign($lagna)
    ];

    $lagnaRasiD9 = Navamsha::calculate($lagna);
    $d9[$lagnaRasiD9][] = [
        "short" => "Lagna"
    ];
}

/* Ensure all houses exist */
for ($i=1; $i<=12; $i++) {
    if (!isset($d1[$i])) $d1[$i] = [];
    if (!isset($d9[$i])) $d9[$i] = [];
}
?>

<?php require 'header.php'; ?>

<style>
.kundli-tabs {
    display:flex;
    justify-content:space-between;
    background:#f3f3f3;
    border-radius:40px;
    padding:6px;
    margin:20px 0;
}

.kundli-tabs a {
    flex:1;
    text-align:center;
    padding:10px 0;
    text-decoration:none;
    color:#333;
    border-radius:30px;
}

.kundli-tabs .active {
    background:#f4c400;
    font-weight:600;
}

.charts-row {
    display:flex;
    justify-content:center;
    gap:80px;
    margin-top:40px;
}

.chart-box {
    text-align:center;
}

.table-box {
    margin-top:60px;
    padding:20px;
    background:#f4f4f4;
    border-radius:10px;
}

.table-box table {
    width:100%;
    border-collapse:collapse;
}

.table-box th,
.table-box td {
    padding:8px;
    border:1px solid #ccc;
    text-align:center;
}

.table-box th {
    background:#ddd;
}
</style>

<section class="kundli-section">
<div class="kundli-container">

<h2>Kundli Charts</h2>

<div class="kundli-tabs">
    <a href="basic-details.php">Basic</a>
    <a href="south-chart.php" class="active">Kundli</a>
    <a href="engine/kpdetails.php">KP</a>
    <a href="#">Ashtakavarga</a>
    <a href="#">Dasha</a>
</div>

<div class="charts-row">

<div class="chart-box">
<h3>Rasi (D1)</h3>
<?php renderSouthChart($d1, true, $lagnaRasiD1); ?>
</div>

<div class="chart-box">
<h3>Navamsa (D9)</h3>
<?php renderSouthChart($d9, false, $lagnaRasiD9); ?>
</div>

</div>

<<!-- ================= PLANETARY TABLE ================= -->

<div class="table-box">
<h3>Planetary Details</h3>

<table>
<tr>
    <th>Planet</th>
    <th>Sign</th>
    <th>Degree (DMS)</th>
    <th>Nakshatra</th>
    <th>Pada</th>
</tr>

<?php
/* ===== LAGNA FIRST ===== */
if ($lagna !== null):

    $rasi = getRasi($lagna);
    $sign = getSignName($rasi);
    $dms  = degreeInSign($lagna);
    list($nak, $pada) = getNakshatraPada($lagna);
?>

<tr style="background:#fff6b3; font-weight:bold;">
    <td>Lagna</td>
    <td><?= $sign ?></td>
    <td><?= $dms ?></td>
    <td><?= $nak ?></td>
    <td><?= $pada ?></td>
</tr>

<?php endif; ?>


<?php
/* ===== PLANETS ===== */
foreach ($planets as $planet => $planetData):

    if (!isset($planetData['decimal'])) continue;

    $deg  = floatval($planetData['decimal']);
    $rasi = getRasi($deg);
    $sign = getSignName($rasi);
    $dms  = degreeInSign($deg);
    list($nak, $pada) = getNakshatraPada($deg);
?>

<tr>
    <td><?= htmlspecialchars($planet) ?></td>
    <td><?= $sign ?></td>
    <td><?= $dms ?></td>
    <td><?= $nak ?></td>
    <td><?= $pada ?></td>
</tr>

<?php endforeach; ?>

</table>
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

echo '<svg width="400" height="400" style="background:#e6e0cf">';

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