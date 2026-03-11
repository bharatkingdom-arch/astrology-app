
<?php

require '../engine/init.php';

require_once '../engine/SunriseSunset.php';
require_once '../engine/MuhurthaLagna.php';



/* ================= LOAD SESSION ================= */
$data = $_SESSION['kundli_data'] ?? null;

if (!$data || empty($data['planets'])) {
    header("Location: ../freekundali.php");
    exit;
}

/* ================= LOAD PR ENGINE ================= */
require_once '../engine/PRCoreEngine.php';
$result = PRCoreEngine::calculate($data);

/* ================= SHORTCUT VARIABLES ================= */
$planetTable = $result['planet_table'];


$planets = $data['planets'] ?? [];
$houses  = $data['houses'] ?? [];

/* ================= CREATE PR PLANET ================= */

if (isset($planets['Sun']['decimal'])) {

    $sunLon = $planets['Sun']['decimal'];

    $prLon = $sunLon - 30;
    if ($prLon < 0) $prLon += 360;

    $planets['PR'] = [
        'decimal' => $prLon,
        'dms' => round($prLon,6).'°'
    ];

    /* SAVE PR INTO SESSION */
    $_SESSION['kundli_data']['planets'] = $planets;
}

/* ================= ZODIAC DATA ================= */

$SIGNS = [
"Aries","Taurus","Gemini","Cancer",
"Leo","Virgo","Libra","Scorpio",
"Sagittarius","Capricorn","Aquarius","Pisces"
];

$SIGN_LORD = [
"Aries"=>"Mars","Taurus"=>"Venus","Gemini"=>"Mercury","Cancer"=>"Moon",
"Leo"=>"Sun","Virgo"=>"Mercury","Libra"=>"Venus","Scorpio"=>"Mars",
"Sagittarius"=>"Jupiter","Capricorn"=>"Saturn",
"Aquarius"=>"Saturn","Pisces"=>"Jupiter"
];

$MOVABLE = ["Aries","Cancer","Libra","Capricorn"];
$FIXED   = ["Taurus","Leo","Scorpio","Aquarius"];
$DUAL    = ["Gemini","Virgo","Sagittarius","Pisces"];

$NAK_NAMES = [
"Ashwini","Bharani","Krittika","Rohini","Mrigashira",
"Ardra","Punarvasu","Pushya","Ashlesha",
"Magha","P-Phalguni","U-Phalguni",
"Hasta","Chitra","Swati","Vishakha",
"Anuradha","Jyeshtha","Moola",
"P-ashadha","U-ashadha",
"Shravana","Dhanishta","Shatabhisha",
"P-Bhadra","U-Bhadra","Revati"
];

$NAK_LORDS = [
"Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter","Saturn","Mercury",
"Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter","Saturn","Mercury",
"Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter","Saturn","Mercury"
];

/* ================= NAVAMSA CORE ================= */

function getNavamsaSign($nakIndex,$padaIndex,$SIGNS){
    return $SIGNS[(($nakIndex*4)+$padaIndex)%12];
}

function getNavamsaStart($sign,$SIGNS,$MOVABLE,$FIXED,$DUAL){
    $index = array_search($sign,$SIGNS);
    if(in_array($sign,$MOVABLE)) $offset=0;
    elseif(in_array($sign,$FIXED)) $offset=8;
    else $offset=4;
    return $SIGNS[($index+$offset)%12];
}

function generateNavamsaSequence($sign,$SIGNS,$MOVABLE,$FIXED,$DUAL){
    $start = getNavamsaStart($sign,$SIGNS,$MOVABLE,$FIXED,$DUAL);
    $startIndex = array_search($start,$SIGNS);
    $seq=[];
    for($i=0;$i<9;$i++){
        $seq[]=$SIGNS[($startIndex+$i)%12];
    }
    return $seq;
}

function generatePR81($nakIndex,$padaIndex,
                      $SIGNS,$SIGN_LORD,$MOVABLE,$FIXED,$DUAL){

    $navSign  = getNavamsaSign($nakIndex,$padaIndex,$SIGNS);
    $mainLord = $SIGN_LORD[$navSign];

    $subSigns = generateNavamsaSequence(
        $navSign,$SIGNS,$MOVABLE,$FIXED,$DUAL
    );

    $result=[];
    $part=1;

    foreach($subSigns as $subSign){

        $subLord=$SIGN_LORD[$subSign];

        $subSubSigns = generateNavamsaSequence(
            $subSign,$SIGNS,$MOVABLE,$FIXED,$DUAL
        );

        foreach($subSubSigns as $subSubSign){

            $result[$part]=[
                "main"=>$mainLord,
                "sub"=>$subLord,
                "subsub"=>$SIGN_LORD[$subSubSign]
            ];
            $part++;
        }
    }
    return $result;
}

/* ================= FULL PR ANALYSIS FUNCTION ================= */

function getFullPR($lon,$SIGNS,$SIGN_LORD,$MOVABLE,$FIXED,$DUAL,$NAK_NAMES,$NAK_LORDS){

    $signIndex = floor($lon/30);
    $signName  = $SIGNS[$signIndex];
    $signLord  = $SIGN_LORD[$signName];

    $nakSize=360/27;
    $padaSize=$nakSize/4;
    $partSize=$padaSize/81;

    $nakIndex=floor($lon/$nakSize);
    $nakName=$NAK_NAMES[$nakIndex];
    $starLord=$NAK_LORDS[$nakIndex];

    $withinNak=fmod($lon,$nakSize);
    $padaIndex=floor($withinNak/$padaSize)+1;

    $withinPada=fmod($withinNak,$padaSize);
    $partIndex=floor($withinPada/$partSize)+1;

    $pr81=generatePR81(
        $nakIndex,$padaIndex-1,
        $SIGNS,$SIGN_LORD,$MOVABLE,$FIXED,$DUAL
    );

    return [
        "longitude"=>round($lon,6),
        "sign"=>$signName,
        "signLord"=>$signLord,
        "nak"=>$nakName,
        "star"=>$starLord,
        "pada"=>$padaIndex,
        "part"=>$partIndex,
        "main"=>$pr81[$partIndex]["main"],
        "sub"=>$pr81[$partIndex]["sub"],
        "subsub"=>$pr81[$partIndex]["subsub"]
    ];
}

require '../header.php';

?>

<style>
html, body {
    overflow-x: hidden;
}

/* MOST IMPORTANT FIX */
.kundli-section,
.kundli-container,
.table-box {
    overflow: visible !important;
}
</style>

<section class="kundli-section">
<div class="kundli-container" id="pdf-content">

<!-- ================= TOP TABS ================= -->
<div class="kundli-tabs">
    <a href="../basic-details.php">Basic</a>
    <a href="../south-chart.php">Kundli</a>
    <a href="../engine/kpdetails.php">KP</a>
    <a href="#">Ashtakavarga</a>
    <a href="#">Charts</a>
    <a href="../calculators/PRDasa.php">PRDasha</a>
</div>

<br>

<div style="text-align:right; margin-bottom:20px;">
    <a href="../generate-pr-pdf.php" target="_blank">
        <button style="padding:10px 20px; background:#000; color:#fff; border:none; cursor:pointer;">
            Download PR PDF
        </button>
    </a>
</div>

<h2>Poorva Ravi – Complete Detailed Analysis</h2>

<?php

$prHouseMap = [];

if (isset($planets['PR']['decimal'])) {

    $prSignIndex = floor($planets['PR']['decimal'] / 30);

    for ($i = 0; $i < 12; $i++) {

        // Zodiac forward counting
        $signIndex = ($prSignIndex + $i) % 12;

        // Store house number against sign index
        $prHouseMap[$signIndex] = $i + 1;
    }
}

/* ================= TRANSIT CALCULATION ================= */

$today = new DateTime('now', new DateTimeZone('Asia/Kolkata'));

$transitDate = $today->format('d.m.Y');
$transitTime = $today->format('H:i');

$lat = $data['latitude'] ?? 16.2390;
$lon = $data['longitude'] ?? 80.6400;
$timezone = 5.5;

$transitUrl = "https://astroloak.com/astroapi/calculate.php"
    . "?date={$transitDate}"
    . "&time={$transitTime}"
    . "&lat={$lat}"
    . "&lon={$lon}"
    . "&timezone={$timezone}";

$transitResponse = file_get_contents($transitUrl);

$transitPlanets = [];

if ($transitResponse !== false) {
    $transitData = json_decode($transitResponse, true);
    if (isset($transitData['planets'])) {
        $transitPlanets = $transitData['planets'];
    }
}

/* ================= TRANSIT LAGNA ================= */

$transitHouses = $transitData['houses'] ?? [];
/* ================= SUNRISE / SUNSET ================= */

$sunCalc = new SunriseSunset();

$todayDate = $today->format('Y-m-d');

$sunTimes = $sunCalc->calculate(
    $todayDate,
    $lat,
    $lon,
    $timezone
);

$sunriseTime = $todayDate . " " . $sunTimes['sunrise'];
$sunsetTime  = $todayDate . " " . $sunTimes['sunset'];

/* ================= MUHURTHA LAGNA ================= */

$mlSign = '';

if (isset($planets['PR']['decimal'])) {

    $mlEngine = new MuhurthaLagna();

    $currentDateTime = $today->format('Y-m-d H:i:s');

    $mlSign = $mlEngine->getMLSign(
        $planets['PR']['decimal'],
        $sunriseTime,
        $currentDateTime
    );
}

/* ================= TRANSIT PR ================= */

if (isset($transitPlanets['Sun']['decimal'])) {

    $tSunLon = $transitPlanets['Sun']['decimal'];

    $tPrLon = $tSunLon - 30;
    if ($tPrLon < 0) $tPrLon += 360;

    $transitPlanets['T-PR'] = [
        'decimal' => $tPrLon
    ];
}
/* ================= BUILD SOUTH CHART ================= */

$chart = [];

/* Step 1: Insert PR house numbers first */
foreach ($prHouseMap as $signIndex => $houseNumber) {
    $chart[$signIndex + 1] =
        "<strong>PR-$houseNumber</strong><br>";
}

/* Step 2: Add D1 planets correctly */
foreach ($planets as $planet => $info) {

    if (!isset($info['decimal'])) continue;

    $house = floor($info['decimal'] / 30) + 1;
    if ($house > 12) $house = 12;

    $chart[$house] =
        ($chart[$house] ?? '') .
        $planet . "<br>";
}

/* Step 3: Insert Lagna */
if (isset($houses['Ascendant']['decimal'])) {

    $lagnaLon = $houses['Ascendant']['decimal'];
    $lagnaSignIndex = floor($lagnaLon / 30);

    $chart[$lagnaSignIndex + 1] =
        "<strong style='color:red'>Lagna</strong><br>" .
        ($chart[$lagnaSignIndex + 1] ?? '');
}

$prChartFinal = $chart;   // ✅ SAVE PR CHART

/* ================= BUILD TRANSIT SOUTH CHART ================= */

$transitChart = [];

/* ---- Insert Transit Lagna First ---- */

if (isset($transitHouses['Ascendant']['decimal'])) {

    $tLagnaLon = $transitHouses['Ascendant']['decimal'];
    $tLagnaHouse = floor($tLagnaLon / 30) + 1;

    $transitChart[$tLagnaHouse] =
        "<strong style='color:green;'>T-Lagna</strong><br>";
}

/* ---- Insert ML Sign ---- */

if (!empty($mlSign)) {

    $mlIndex = array_search($mlSign, $SIGNS);

    if ($mlIndex !== false) {

        $mlHouse = $mlIndex + 1;

        $transitChart[$mlHouse] =
            "<strong style='color:red;'>ML</strong><br>" .
            ($transitChart[$mlHouse] ?? '');
    }
}

/* ---- Insert Transit Planets + T-PR ---- */

foreach ($transitPlanets as $planet => $info) {

    if (!isset($info['decimal'])) continue;

    $house = floor($info['decimal'] / 30) + 1;

    $color = "blue";
    if ($planet == 'T-PR') $color = "purple";

    $transitChart[$house] =
        ($transitChart[$house] ?? '') .
        "<span style='color:$color;'>$planet</span><br>";
}

$transitChartFinal = $transitChart;   // ✅ SAVE TRANSIT CHART
?>


<style>
.chart-row{
    display:flex;
    justify-content:center;
    align-items:flex-start;
    gap:60px;
    margin:40px 0;
}

.chart-box{
    text-align:center;
}

/* IMPORTANT: remove width forcing */
.table-box{
    width:auto !important;
}
</style>

<div class="chart-row">

    <div class="chart-box">
    <h4>PR South Chart</h4>

    <?php
    $chartCenter = "
    <div><strong>NATAL PR</strong></div>
    <div>{$data['date']}</div>
    <div>{$data['time']}</div>
    <div>Lat: {$data['latitude']}</div>
    <div>Lon: {$data['longitude']}</div>
    ";
    require '../components/south-chart.php';
    ?>
</div>

    <div class="chart-box">
    <h4>Transit South Chart</h4>

    <?php
    $chart = $transitChartFinal;
    $chartCenter = "
<div><strong>TRANSIT</strong></div>
<div>{$today->format('d-m-Y')}</div>
<div>{$today->format('H:i')}</div>
<div>Sunrise: {$sunTimes['sunrise']}</div>
<div>Sunset: {$sunTimes['sunset']}</div>
<div>ML: {$mlSign}</div>
<div>Lat: {$lat}</div>
<div>Lon: {$lon}</div>
";
    require '../components/south-chart.php';
    ?>
</div>
</div>

<!-- ================= PLANETS ================= -->

<div class="table-box">
<h4>Planets</h4>

<table>
<tr>
<th>Planet</th>
<th>Longitude</th>
<th>Sign</th>
<th>Sign Lord</th>
<th>Nakshatra</th>
<th>Star Lord</th>
<th>Pada</th>
<th>Part</th>
<th>Main</th>
<th>Sub</th>
<th>Sub-Sub</th>
</tr>

<?php foreach($planets as $planet=>$info):

if(!isset($info['decimal'])) continue;

$pr=getFullPR(
    $info['decimal'],
    $SIGNS,$SIGN_LORD,$MOVABLE,$FIXED,$DUAL,
    $NAK_NAMES,$NAK_LORDS
);
?>

<tr>
<td><?= $planet ?></td>
<td><?= $pr["longitude"] ?>°</td>
<td><?= $pr["sign"] ?></td>
<td><?= $pr["signLord"] ?></td>
<td><?= $pr["nak"] ?></td>
<td><?= $pr["star"] ?></td>
<td><?= $pr["pada"] ?></td>
<td><?= $pr["part"] ?></td>
<td><?= $pr["main"] ?></td>
<td><?= $pr["sub"] ?></td>
<td><?= $pr["subsub"] ?></td>
</tr>

<?php endforeach; ?>

</table>
</div>

<!-- ================= HOUSES ================= -->

<?php if(!empty($houses)): ?>

<div class="table-box">
<h4>Houses</h4>

<table>
<tr>
<th>House</th>
<th>Longitude</th>
<th>Sign</th>
<th>Sign Lord</th>
<th>Nakshatra</th>
<th>Star Lord</th>
<th>Pada</th>
<th>Part</th>
<th>Main</th>
<th>Sub</th>
<th>Sub-Sub</th>
</tr>

<?php foreach($houses as $house=>$info):

if(!isset($info['decimal'])) continue;

$pr=getFullPR(
    $info['decimal'],
    $SIGNS,$SIGN_LORD,$MOVABLE,$FIXED,$DUAL,
    $NAK_NAMES,$NAK_LORDS
);
?>

<tr>

<?php
$houseLabel = $house;

// If value is like "House 1", extract only the number
if (preg_match('/House\s*(\d+)/i', $house, $match)) {
    $houseLabel = $match[1];
}
?>

<td><?= $houseLabel ?></td>
<td><?= $pr["longitude"] ?>°</td>
<td><?= $pr["sign"] ?></td>
<td><?= $pr["signLord"] ?></td>
<td><?= $pr["nak"] ?></td>
<td><?= $pr["star"] ?></td>
<td><?= $pr["pada"] ?></td>
<td><?= $pr["part"] ?></td>
<td><?= $pr["main"] ?></td>
<td><?= $pr["sub"] ?></td>
<td><?= $pr["subsub"] ?></td>

</tr>

<?php endforeach; ?>

</table>
</div>

<?php endif; ?>


<!-- ================= D1 vs PR HOUSE TABLE ================= -->

<?php
/* ================= BUILD D1 vs PR HOUSE TABLE ================= */

/* D1 Lagna Sign Index */
$d1SignIndex = null;
if (isset($data['lagna']['decimal'])) {
    $d1SignIndex = floor($data['lagna']['decimal'] / 30);
}

/* PR Sign Index */
$prSignIndex = null;
if (isset($planets['PR']['decimal'])) {
    $prSignIndex = floor($planets['PR']['decimal'] / 30);
}
?>

<div class="table-box">
<h4>D1 vs PR House Comparison</h4>

<table>
<tr>
<th>Sign</th>
<th>D1 House</th>
<th>PR House</th>
</tr>

<?php for ($i = 0; $i < 12; $i++): 

    $signName = $SIGNS[$i];

    $d1House = ($d1SignIndex !== null)
        ? ($i - $d1SignIndex + 12) % 12 + 1
        : '';

    $prHouse = ($prSignIndex !== null)
        ? ($i - $prSignIndex + 12) % 12 + 1
        : '';
?>

<tr>
<td><?= $signName ?></td>
<td><?= $d1House ?></td>
<td><?= $prHouse ?></td>
</tr>

<?php endfor; ?>

</table>
</div>


</div>
</section>

<?php

/* ================= BUILD FULL HOUSE PR TABLE FOR PDF ================= */

$houseTableFull = [];

foreach ($houses as $house => $info) {

    if (!isset($info['decimal'])) continue;

    $pr = getFullPR(
        $info['decimal'],
        $SIGNS,
        $SIGN_LORD,
        $MOVABLE,
        $FIXED,
        $DUAL,
        $NAK_NAMES,
        $NAK_LORDS
    );

    $houseTableFull[$house] = $pr;
}


/* ================= STORE PDF DATA ================= */

$_SESSION['pr_pdf_data'] = [
    'prChart'        => $prChartFinal,
'transitChart'   => $transitChartFinal,
    'centerPR'       => "
        <div><strong>NATAL PR</strong></div>
        <div>{$data['date']}</div>
        <div>{$data['time']}</div>
        <div>Lat: {$data['latitude']}</div>
        <div>Lon: {$data['longitude']}</div>
    ",
    'centerTransit'  => "
        <div><strong>TRANSIT</strong></div>
        <div>{$today->format('d-m-Y')}</div>
        <div>{$today->format('H:i')}</div>
        <div>Sunrise: {$sunTimes['sunrise']}</div>
        <div>Sunset: {$sunTimes['sunset']}</div>
        <div>ML: {$mlSign}</div>
        <div>Lat: {$lat}</div>
        <div>Lon: {$lon}</div>
    ",
    'planetTable'    => $planetTable,
    'houseTable'     => $houseTableFull   // ✅ Correct data
];

require '../bottom.php';
?>
