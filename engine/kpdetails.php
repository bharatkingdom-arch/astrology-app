<?php
session_start();
ini_set('display_errors',1);
error_reporting(E_ALL);

if(!isset($_SESSION['kundli_data'])){
    echo "<h2 style='color:red'>Session not found.</h2>";
    exit;
}

$data    = $_SESSION['kundli_data'];
$planets = $data['planets'] ?? [];
$houses  = $data['houses'] ?? [];

if(empty($planets) || empty($houses)){
    echo "<h2 style='color:red'>Planets or Houses missing.</h2>";
    exit;
}

require_once __DIR__.'/KP.php';
$kpResults = KP::calculateAll($planets);
$kpHouses  = KP::calculateAll($houses);

require_once __DIR__.'/../header.php';

/* ================= SIGN DATA ================= */

$signNames = [
0=>"Ar","Ta","Ge","Cn","Le","Vi",
"Li","Sc","Sg","Cp","Aq","Pi"
];

$signLords = [
"Mars","Venus","Mercury","Moon","Sun","Mercury",
"Venus","Mars","Jupiter","Saturn","Saturn","Jupiter"
];

/* ================= FIND HOUSE BY CUSP ================= */

function findHouseKP($deg,$houses){
    for($i=1;$i<=12;$i++){

        $start = $houses["House $i"]['decimal'];
        $next  = ($i==12)?1:$i+1;
        $end   = $houses["House $next"]['decimal'];

        if($start < $end){
            if($deg >= $start && $deg < $end) return $i;
        } else {
            if($deg >= $start || $deg < $end) return $i;
        }
    }
    return null;
}

/* ================= BUILD DATA ================= */

$bhavaPlanets = [];
$houseSigns   = [];
$houseDegrees = [];

for($i=1;$i<=12;$i++){
    $deg = $houses["House $i"]['decimal'];
    $signIndex = floor($deg/30);
    $houseSigns[$i] = $signIndex;
    $houseDegrees[$i] = $houses["House $i"]['dms'];
}

foreach($planets as $planet=>$pdata){

    if(!isset($pdata['decimal'])) continue;

    $deg = $pdata['decimal'];
    $house = findHouseKP($deg,$houses);

    if($house){
        $bhavaPlanets[$house][] = $planet;
    }
}

/* ================= SOUTH FIXED SIGN LAYOUT ================= */

$signPositions = [
11=>[20,30],
0 =>[120,30],
1 =>[220,30],
2 =>[320,30],

10=>[20,130],
3 =>[320,130],

9 =>[20,230],
4 =>[320,230],

8 =>[20,330],
7 =>[120,330],
6 =>[220,330],
5 =>[320,330],
];
?>

<style>
.kp-container{max-width:1100px;margin:40px auto;}
.kundli-tabs{display:flex;background:#f3f3f3;border-radius:40px;padding:6px;margin-bottom:30px;}
.kundli-tabs a{flex:1;text-align:center;padding:10px;text-decoration:none;color:#333;border-radius:30px;}
.kundli-tabs .active{background:#f4c400;font-weight:600;}
.table-box{background:#f4f4f4;padding:20px;border-radius:10px;margin-bottom:40px;}
.table-box table{width:100%;border-collapse:collapse;}
.table-box th,.table-box td{padding:8px;border:1px solid #ccc;text-align:center;}
.table-box th{background:#ddd;}
</style>

<section class="kp-container">

<div class="kundli-tabs">
<a href="../basic-details.php">Basic</a>
<a href="../south-chart.php">Kundli</a>
<a href="kpdetails.php" class="active">KP</a>
<a href="#">Ashtakavarga</a>
<a href="#">Dasha</a>
</div>

<div class="table-box" style="text-align:center">
<h3>Bhava Chart (KP)</h3>

<svg width="400" height="400" style="background:#e6e0cf">

<!-- Outer Border -->
<rect x="0" y="0" width="400" height="400" fill="none" stroke="#444" stroke-width="2"/>

<!-- Top Row (4 boxes) -->
<rect x="0"   y="0" width="100" height="100" fill="none" stroke="#444"/>
<rect x="100" y="0" width="100" height="100" fill="none" stroke="#444"/>
<rect x="200" y="0" width="100" height="100" fill="none" stroke="#444"/>
<rect x="300" y="0" width="100" height="100" fill="none" stroke="#444"/>

<!-- Bottom Row (4 boxes) -->
<rect x="0"   y="300" width="100" height="100" fill="none" stroke="#444"/>
<rect x="100" y="300" width="100" height="100" fill="none" stroke="#444"/>
<rect x="200" y="300" width="100" height="100" fill="none" stroke="#444"/>
<rect x="300" y="300" width="100" height="100" fill="none" stroke="#444"/>

<!-- Left Column (2 middle boxes) -->
<rect x="0"   y="100" width="100" height="100" fill="none" stroke="#444"/>
<rect x="0"   y="200" width="100" height="100" fill="none" stroke="#444"/>

<!-- Right Column (2 middle boxes) -->
<rect x="300" y="100" width="100" height="100" fill="none" stroke="#444"/>
<rect x="300" y="200" width="100" height="100" fill="none" stroke="#444"/>

<?php
foreach($signPositions as $sign=>$pos){

    $x=$pos[0];
    $y=$pos[1];

    // Sign Name
    echo "<text x='$x' y='$y' font-size='11' font-weight='bold'>{$signNames[$sign]}</text>";

    // House + Degree
    foreach($houseSigns as $house=>$sIndex){
        if($sIndex==$sign){

            echo "<text x='$x' y='".($y+14)."' font-size='9'>$house {$houseDegrees[$house]}</text>";

            if(!empty($bhavaPlanets[$house])){
                $py=$y+28;
                foreach($bhavaPlanets[$house] as $pl){
                    echo "<text x='$x' y='$py' font-size='11' font-weight='bold'>".substr($pl,0,2)."</text>";
                    $py+=14;
                }
            }
        }
    }
}
?>

</svg>
</div>

<div class="table-box">
<h3>KP Planetary Table</h3>
<table>
<tr>
<th>Planet</th>
<th>Longitude</th>
<th>Sign</th>
<th>Sign Lord</th>
<th>House</th>
<th>House Sign Lord</th>
<th>Nakshatra</th>
<th>Star Lord</th>
<th>Sub</th>
<th>Sub-Sub</th>
</tr>

<?php
foreach($kpResults as $planet=>$kp){

$deg = $planets[$planet]['decimal'];
$signIndex = floor($deg/30);
$house = findHouseKP($deg,$houses);
$houseSignLord = $signLords[$houseSigns[$house]] ?? '';

echo "<tr>
<td>$planet</td>
<td>{$planets[$planet]['dms']}</td>
<td>{$signNames[$signIndex]}</td>
<td>{$signLords[$signIndex]}</td>
<td>$house</td>
<td>$houseSignLord</td>
<td>{$kp['nakshatra']}</td>
<td>{$kp['star_lord']}</td>
<td>{$kp['sub_lord']}</td>
<td>{$kp['sub_sub_lord']}</td>
</tr>";
}
?>
</table>
</div>

<div class="table-box">
<h3>KP House Cusps</h3>
<table>
<tr>
<th>House</th>
<th>Longitude</th>
<th>Sign</th>
<th>Sign Lord</th>
<th>Nakshatra</th>
<th>Star</th>
<th>Sub</th>
<th>Sub-Sub</th>
</tr>

<?php
for($i=1;$i<=12;$i++){

$signIndex=$houseSigns[$i];
$kp=$kpHouses["House $i"];

echo "<tr>
<td>$i</td>
<td>{$houses["House $i"]['dms']}</td>
<td>{$signNames[$signIndex]}</td>
<td>{$signLords[$signIndex]}</td>
<td>{$kp['nakshatra']}</td>
<td>{$kp['star_lord']}</td>
<td>{$kp['sub_lord']}</td>
<td>{$kp['sub_sub_lord']}</td>
</tr>";
}
?>
</table>
</div>

</section>

<?php require_once __DIR__.'/../bottom.php'; ?>
