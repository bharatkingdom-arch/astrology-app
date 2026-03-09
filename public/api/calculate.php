<?php

// Only send headers if called via browser
if (!headers_sent()) {
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
}

error_reporting(E_ALL);
ini_set('display_errors', 0);


// ==========================
// INPUT
// ==========================

$date = $_GET['date'] ?? null;
$time = $_GET['time'] ?? null;
$lat  = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lon  = isset($_GET['lon']) ? floatval($_GET['lon']) : null;
$timezone = isset($_GET['timezone']) ? floatval($_GET['timezone']) : 0;


if ($date === null || $time === null || $lat === null || $lon === null) {

    echo json_encode([
        "status" => "error",
        "message" => "Missing date, time, latitude or longitude"
    ]);
    exit;
}


// ==========================
// TIME (already UTC)
// ==========================

$dt = DateTime::createFromFormat("d.m.Y H:i", "$date $time");

if (!$dt) {

    echo json_encode([
        "status" => "error",
        "message" => "Invalid date/time format"
    ]);
    exit;
}

$utTime = $dt->format("H:i");


// ==========================
// SWISS EPHEMERIS PATH
// ==========================

$swetestPath = "/app/swisseph/swetest";
$ephePath    = "/app/ephemeris";


// ==========================
// PLANETS COMMAND
// ==========================

$planetCommand = "$swetestPath -edir$ephePath -sid1 -b$date -ut$utTime -p0123456789t -fPl";

$planetOutput = shell_exec($planetCommand);

if (!$planetOutput) {

    echo json_encode([
        "status" => "error",
        "message" => "Swiss Ephemeris failed (planets)"
    ]);
    exit;
}


// ==========================
// DECIMAL → DMS
// ==========================

function decimalToDMS($decimal)
{
    $decimal = fmod($decimal, 360);

    if ($decimal < 0) $decimal += 360;

    $deg = floor($decimal);
    $minFloat = ($decimal - $deg) * 60;
    $min = floor($minFloat);
    $sec = round(($minFloat - $min) * 60);

    if ($sec == 60) {
        $sec = 0;
        $min++;
    }

    if ($min == 60) {
        $min = 0;
        $deg++;
    }

    return sprintf("%d° %02d′ %02d″", $deg, $min, $sec);
}


// ==========================
// PARSE PLANETS
// ==========================

$lines = explode("\n", trim($planetOutput));
$planets = [];

foreach ($lines as $line) {

    if (preg_match('/^(Sun|Moon|Mercury|Venus|Mars|Jupiter|Saturn|Uranus|Neptune|Pluto|true Node|True Node)\s+([\d\.]+)/', trim($line), $matches)) {

        $name  = strtolower($matches[1]);
        $value = floatval($matches[2]);

        if ($name === 'true node') {
            $planetName = 'Rahu';
        } else {
            $planetName = ucfirst($name);
        }

        $planets[$planetName] = [
            "decimal" => $value,
            "dms" => decimalToDMS($value)
        ];
    }
}


// ==========================
// ADD KETU
// ==========================

if (isset($planets['Rahu'])) {

    $rahu = $planets['Rahu']['decimal'];

    $ketu = fmod($rahu + 180, 360);

    if ($ketu < 0) $ketu += 360;

    $planets['Ketu'] = [
        "decimal" => $ketu,
        "dms" => decimalToDMS($ketu)
    ];
}


// ==========================
// HOUSES + ASC
// ==========================

$houseCommand = "$swetestPath -edir$ephePath -sid1 -b$date -ut$utTime -house$lat,$lon,P -fPl";

$houseOutput = shell_exec($houseCommand);

$houses = [];

if ($houseOutput) {

    $houseLines = explode("\n", trim($houseOutput));

    foreach ($houseLines as $line) {

        $line = trim($line);

        if (strpos($line, 'house') === 0) {

            $parts = preg_split('/\s+/', $line);

            if (count($parts) >= 3) {

                $num = $parts[1];
                $val = floatval($parts[2]);

                $houses["House $num"] = [
                    "decimal" => $val,
                    "dms" => decimalToDMS($val)
                ];
            }
        }

        if (strpos($line, 'Ascendant') === 0) {

            $parts = preg_split('/\s+/', $line);

            $asc = floatval($parts[1]);

            $houses["Ascendant"] = [
                "decimal" => $asc,
                "dms" => decimalToDMS($asc)
            ];
        }

        if (strpos($line, 'MC') === 0) {

            $parts = preg_split('/\s+/', $line);

            $mc = floatval($parts[1]);

            $houses["MC"] = [
                "decimal" => $mc,
                "dms" => decimalToDMS($mc)
            ];
        }
    }
}


// ==========================
// OUTPUT
// ==========================

echo json_encode([
    "status" => "success",
    "date" => $date,
    "utc_time" => $utTime,
    "latitude" => $lat,
    "longitude" => $lon,
    "ayanamsa" => "Lahiri",
    "planets" => $planets,
    "houses" => $houses
], JSON_PRETTY_PRINT);