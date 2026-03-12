<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

/* ==========================
INPUT
========================== */

$date     = $_GET['date']     ?? null;
$time     = $_GET['time']     ?? null;
$lat      = $_GET['lat']      ?? null;
$lon      = $_GET['lon']      ?? null;
$timezone = $_GET['timezone'] ?? 0;

if (!$date || !$time || !$lat || !$lon) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing date, time, latitude or longitude"
    ]);
    exit;
}

/* ==========================
CONVERT LOCAL TIME → UT
========================== */

$dt = DateTime::createFromFormat("d.m.Y H:i", "$date $time");

if (!$dt) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid date/time format"
    ]);
    exit;
}

$hours = floor($timezone);
$minutes = ($timezone - $hours) * 60;

$dt->modify("-{$hours} hours");
$dt->modify("-{$minutes} minutes");

$utTime = $dt->format("H:i");

/* ==========================
SWISS EPHEMERIS PATH
========================== */

$swetestPath = "/app/swisseph/swetest";
$ephePath    = "/app/ephemeris";

/* ==========================
PLANETS COMMAND
========================== */

$planetCommand = "$swetestPath -edir$ephePath -sid1 -b$date -ut$utTime -p0123456789t -fPls";

$planetOutput = shell_exec($planetCommand);

if (!$planetOutput) {
    echo json_encode([
        "status" => "error",
        "message" => "Swiss Ephemeris failed"
    ]);
    exit;
}

/* ==========================
DECIMAL → DMS
========================== */

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

/* ==========================
COMBUST FUNCTION
========================== */

function isCombust($planet, $planet_long, $sun_long)
{
    $limits = [
        "Mercury" => 14,
        "Venus" => 10,
        "Mars" => 17,
        "Jupiter" => 11,
        "Saturn" => 15
    ];

    if (!isset($limits[$planet])) return false;

    $diff = abs($planet_long - $sun_long);

    if ($diff > 180) {
        $diff = 360 - $diff;
    }

    return $diff <= $limits[$planet];
}

/* ==========================
PARSE PLANETS
========================== */

$lines = explode("\n", trim($planetOutput));
$planets = [];

foreach ($lines as $line) {

    $line = trim($line);

    if (preg_match('/^(Sun|Moon|Mercury|Venus|Mars|Jupiter|Saturn|Uranus|Neptune|Pluto|true Node|True Node)\s+([\d\.]+)\s+([-\d\.]+)/', $line, $matches)) {

        $name  = strtolower($matches[1]);
        $value = floatval($matches[2]);
        $speed = floatval($matches[3]);

        if ($name === 'true node') {
            $planetName = 'Rahu';
        } else {
            $planetName = ucfirst($name);
        }

        $planets[$planetName] = [
            "decimal" => $value,
            "dms" => decimalToDMS($value),
            "speed" => $speed,
            "retrograde" => ($speed < 0)
        ];
    }
}

/* ==========================
ADD KETU
========================== */

if (isset($planets['Rahu'])) {

    $rahuDecimal = $planets['Rahu']['decimal'];

    $ketuDecimal = fmod($rahuDecimal + 180, 360);
    if ($ketuDecimal < 0) $ketuDecimal += 360;

    $planets['Ketu'] = [
        "decimal" => $ketuDecimal,
        "dms" => decimalToDMS($ketuDecimal),
        "speed" => 0,
        "retrograde" => true
    ];
}

/* ==========================
COMBUST CHECK
========================== */

if (isset($planets["Sun"])) {

    $sunLongitude = $planets["Sun"]["decimal"];

    foreach ($planets as $planet => $data) {

    $planets[$planet]["combust"] = isCombust($planet, $data["decimal"], $sunLongitude);

}
}

/* ==========================
HOUSES
========================== */

$houseCommand = "$swetestPath -edir$ephePath -sid1 -b$date -ut$utTime -house$lon,$lat,P -fPl";

$houseOutput = shell_exec($houseCommand);

$houses = [];

if ($houseOutput) {

    $houseLines = explode("\n", trim($houseOutput));

    foreach ($houseLines as $line) {

        $line = trim($line);

        if (strpos($line, 'house') === 0) {

            $parts = preg_split('/\s+/', $line);

            if (count($parts) >= 3) {

                $houseNumber = $parts[1];
                $value = floatval($parts[2]);

                $houses["House $houseNumber"] = [
                    "decimal" => $value,
                    "dms" => decimalToDMS($value)
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

/* ==========================
FINAL JSON
========================== */

echo json_encode([
    "status"    => "success",
    "date"      => $date,
    "time"      => $time,
    "ut_time"   => $utTime,
    "latitude"  => $lat,
    "longitude" => $lon,
    "ayanamsa"  => "Lahiri",
    "planets"   => $planets,
    "houses"    => $houses
], JSON_PRETTY_PRINT);