<?php
session_start();

require_once 'engine/Navamsha.php';

$data = $_SESSION['kundli_data'] ?? null;

if (!$data) {
    header("Location: freekundali.php");
    exit;
}

$planets = $data['planets'] ?? [];

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

/* ================= MAP D1 & D9 ================= */

$d1 = [];
$d9 = [];

/* ================= ASCENDANT ================= */

$ascDegree = $data['houses']['Ascendant']['decimal'] ?? null;

if ($ascDegree !== null) {

    $ascRasi = getRasi($ascDegree);

    if ($ascRasi >= 1 && $ascRasi <= 12) {

        $d1[$ascRasi][] = [
            "short" => "Asc",
            "deg"   => degreeInSign($ascDegree)
        ];
    }
}

foreach ($planets as $planet => $planetData) {
        $degree = $planetData['decimal'];   // ✅ extract decimal value

    // ---------- D1 ----------
    $rasi1 = getRasi($degree);

    if ($rasi1 >= 1 && $rasi1 <= 12) {
        $d1[$rasi1][] = [
            "short" => $short[$planet] ?? $planet,
            "deg"   => degreeInSign($degree)
        ];
    }

    // ---------- D9 ----------
    $rasi9 = Navamsha::calculate($degree);

    if ($rasi9 >= 1 && $rasi9 <= 12) {
        $d9[$rasi9][] = [
            "short" => $short[$planet] ?? $planet
        ];
    }
}

/* Ensure all houses exist */
for ($i=1; $i<=12; $i++) {
    if (!isset($d1[$i])) $d1[$i] = [];
    if (!isset($d9[$i])) $d9[$i] = [];
}
?>

<?php require 'header.php'; ?>

<style>

/* ================= TABS ================= */
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

/* ================= LAYOUT ================= */

.charts-row {
    display:flex;
    justify-content:center;
    gap:80px;
    margin-top:40px;
}

.chart-box {
    text-align:center;
}

svg {
    background:#e6e0cf;
}

</style>

<section class="kundli-section">
<div class="kundli-container">

<h2>Kundli Charts</h2>

<!-- ================= TABS ================= -->
<div class="kundli-tabs">
    <a href="basic-details.php">Basic</a>
    <a href="south-chart.php" class="active">Kundli</a>
    <a href="engine/kpdetails.php">KP</a>

    <a href="#">Ashtakavarga</a>
    <a href="#">Dasha</a>
</div>

<div class="charts-row">

<!-- ================= D1 ================= -->
<div class="chart-box">
<h3>Rasi (D1)</h3>
<?php renderSouthChart($d1, true); ?>
</div>

<!-- ================= D9 ================= -->
<div class="chart-box">
<h3>Navamsa (D9)</h3>
<?php renderSouthChart($d9, false); ?>
</div>

</div>

</div>
</section>

<?php require 'bottom.php'; ?>

<?php
/* =====================================================
   SOUTH INDIAN CHART RENDER FUNCTION (400x400 FIXED)
===================================================== */

function renderSouthChart($data, $showDegree = false) {

$positions = [
    12 => [10,20],
    1  => [110,20],
    2  => [210,20],
    3  => [310,20],

    11 => [10,120],
    4  => [310,120],

    10 => [10,220],
    5  => [310,220],

    9  => [10,320],
    8  => [110,320],
    7  => [210,320],
    6  => [310,320],
];

echo '<svg width="400" height="400" style="background:#e6e0cf">';

/* Outer Border */
echo '<rect x="0" y="0" width="400" height="400" fill="none" stroke="#444" stroke-width="2"/>';

/* Vertical Lines */
echo '<line x1="100" y1="0" x2="100" y2="400" stroke="#444" stroke-width="1"/>';
echo '<line x1="200" y1="0" x2="200" y2="100" stroke="#444" stroke-width="1"/>';
echo '<line x1="200" y1="300" x2="200" y2="400" stroke="#444" stroke-width="1"/>';
echo '<line x1="300" y1="0" x2="300" y2="400" stroke="#444" stroke-width="1"/>';

/* Horizontal Lines */
echo '<line x1="0" y1="100" x2="400" y2="100" stroke="#444" stroke-width="1"/>';
echo '<line x1="0" y1="200" x2="100" y2="200" stroke="#444" stroke-width="1"/>';
echo '<line x1="300" y1="200" x2="400" y2="200" stroke="#444" stroke-width="1"/>';
echo '<line x1="0" y1="300" x2="400" y2="300" stroke="#444" stroke-width="1"/>';

/* Planet Text */
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
