
<?php
session_start();

$data = $_SESSION['kundli_data'] ?? null;
if (!$data) exit;

$lat = $data['latitude'] ?? 16.2390;
$lon = $data['longitude'] ?? 80.6400;
$timezone = 5.5;

/* ==== CURRENT TIME ==== */
$now = new DateTime('now', new DateTimeZone('Asia/Kolkata'));

$date = $now->format('d.m.Y');
$time = $now->format('H:i:s');

/* ==== API CALL ==== */
$url = "https://astroloak.com/astroapi/calculate.php"
    . "?date={$date}"
    . "&time={$time}"
    . "&lat={$lat}"
    . "&lon={$lon}"
    . "&timezone={$timezone}";

$response = file_get_contents($url);
if (!$response) exit;

$dataTransit = json_decode($response, true);

$transitPlanets = $dataTransit['planets'] ?? [];
$transitHouses  = $dataTransit['houses'] ?? [];

/* ==== TRANSIT PR ==== */
if (isset($transitPlanets['Sun']['decimal'])) {
    $sun = $transitPlanets['Sun']['decimal'];
    $pr = $sun - 30;
    if ($pr < 0) $pr += 360;

    $transitPlanets['T-PR'] = ['decimal' => $pr];
}

/* ==== BUILD CHART ==== */
$chart = [];

/* Transit Lagna */
if (isset($transitHouses['Ascendant']['decimal'])) {
    $lagna = floor($transitHouses['Ascendant']['decimal'] / 30) + 1;
    $chart[$lagna] = "<strong style='color:green'>T-Lagna</strong><br>";
}

/* Planets */
foreach ($transitPlanets as $planet => $info) {

    if (!isset($info['decimal'])) continue;

    $house = floor($info['decimal'] / 30) + 1;

    $color = ($planet == 'T-PR') ? 'purple' : 'blue';

    $chart[$house] =
        ($chart[$house] ?? '') .
        "<span style='color:$color'>$planet</span><br>";
}

/* ==== CENTER INFO ==== */
$chartCenter = "
<div><strong>TRANSIT LIVE</strong></div>
<div>{$now->format('d-m-Y')}</div>
<div>{$now->format('H:i:s')}</div>
<div>Lat: {$lat}</div>
<div>Lon: {$lon}</div>
";

/* ==== RENDER ==== */
require '../components/south-chart.php';
