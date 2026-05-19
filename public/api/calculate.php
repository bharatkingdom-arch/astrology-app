<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

/* ==========================
INPUT VALIDATION
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
    ], JSON_PRETTY_PRINT);
    exit;
}

// Sanitize inputs
$date = preg_replace('/[^0-9.]/', '', $date);
$time = preg_replace('/[^0-9:]/', '', $time);
$lat = floatval($lat);
$lon = floatval($lon);
$timezone = floatval($timezone);

/* ==========================
CONVERT LOCAL TIME → UTC (FIXED)
========================== */

$dt = DateTime::createFromFormat("d.m.Y H:i", "$date $time");

if (!$dt) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid date/time format. Use DD.MM.YYYY HH:MM"
    ], JSON_PRETTY_PRINT);
    exit;
}

// Store original for response
$originalDate = $date;
$originalTime = $time;

// Convert to UTC - handles date change when time < timezone offset
$hours = floor(abs($timezone));
$minutes = abs($timezone - floor($timezone)) * 60;

if ($timezone >= 0) {
    $dt->modify("-{$hours} hours");
    $dt->modify("-{$minutes} minutes");
} else {
    $dt->modify("+{$hours} hours");
    $dt->modify("+{$minutes} minutes");
}

// Get UTC date AND time (BOTH are critical for Swiss Ephemeris!)
$utDate = $dt->format("d.m.Y");  // ← FIXED: UTC date (may be previous/next day)
$utTime = $dt->format("H:i");     // ← UTC time

/* ==========================
SWISS EPHEMERIS PATH
========================== */

$swetestPath = "/app/swisseph/swetest";
$ephePath    = "/app/ephemeris";

if (!file_exists($swetestPath)) {
    $swetestPath = __DIR__ . '/../../swisseph/swetest';
    $ephePath = __DIR__ . '/../../ephemeris';
}

/* ==========================
PLANETS COMMAND - USING UTC DATE!
========================== */

$planetCommand = "$swetestPath -edir$ephePath -sid1 -b$utDate -ut$utTime -p0123456789t -fPls";
$planetOutput = shell_exec($planetCommand);

if (!$planetOutput || trim($planetOutput) === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Swiss Ephemeris failed to return planetary data"
    ], JSON_PRETTY_PRINT);
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
    
    if ($deg == 360) {
        $deg = 0;
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
            "decimal" => round($value, 6),
            "dms" => decimalToDMS($value),
            "speed" => round($speed, 6),
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
        "decimal" => round($ketuDecimal, 6),
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
HOUSES - USING UTC DATE!
========================== */

$houseCommand = "$swetestPath -edir$ephePath -sid1 -b$utDate -ut$utTime -house$lon,$lat,P -fPl";
$houseOutput = shell_exec($houseCommand);

$houses = [];

if ($houseOutput && trim($houseOutput) !== '') {
    $houseLines = explode("\n", trim($houseOutput));

    foreach ($houseLines as $line) {
        $line = trim($line);

        if (strpos($line, 'house') === 0) {
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 3) {
                $houseNumber = $parts[1];
                $value = floatval($parts[2]);
                $houses["House $houseNumber"] = [
                    "decimal" => round($value, 6),
                    "dms" => decimalToDMS($value)
                ];
            }
        }

        if (strpos($line, 'Ascendant') === 0) {
            $parts = preg_split('/\s+/', $line);
            $asc = floatval($parts[1]);
            $houses["Ascendant"] = [
                "decimal" => round($asc, 6),
                "dms" => decimalToDMS($asc)
            ];
        }

        if (strpos($line, 'MC') === 0) {
            $parts = preg_split('/\s+/', $line);
            $mc = floatval($parts[1]);
            $houses["MC"] = [
                "decimal" => round($mc, 6),
                "dms" => decimalToDMS($mc)
            ];
        }
    }
}

/* ==========================
FINAL JSON
========================== */

echo json_encode([
    "status"         => "success",
    "local_date"     => $originalDate,
    "local_time"     => $originalTime,
    "utc_date"       => $utDate,      // ← ADDED: Shows actual UTC date used
    "utc_time"       => $utTime,
    "timezone"       => $timezone,
    "latitude"       => $lat,
    "longitude"      => $lon,
    "ayanamsa"       => "Lahiri",
    "planets"        => $planets,
    "houses"         => $houses
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);