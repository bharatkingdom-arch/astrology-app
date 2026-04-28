<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

/* ==========================
INPUT VALIDATION & SANITIZATION
========================== */

$date     = $_GET['date']     ?? null;
$time     = $_GET['time']     ?? null;
$lat      = $_GET['lat']      ?? null;
$lon      = $_GET['lon']      ?? null;
$timezone = $_GET['timezone'] ?? 0;

// Validate required parameters
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

// Validate latitude and longitude ranges
if ($lat < -90 || $lat > 90) {
    echo json_encode([
        "status" => "error",
        "message" => "Latitude must be between -90 and 90"
    ], JSON_PRETTY_PRINT);
    exit;
}

if ($lon < -180 || $lon > 180) {
    echo json_encode([
        "status" => "error",
        "message" => "Longitude must be between -180 and 180"
    ], JSON_PRETTY_PRINT);
    exit;
}

// Validate timezone range
if ($timezone < -12 || $timezone > 14) {
    echo json_encode([
        "status" => "error",
        "message" => "Timezone must be between -12 and +14"
    ], JSON_PRETTY_PRINT);
    exit;
}

/* ==========================
CONVERT LOCAL TIME → UT
========================== */

$dt = DateTime::createFromFormat("d.m.Y H:i", "$date $time");

if (!$dt) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid date/time format. Use DD.MM.YYYY HH:MM"
    ], JSON_PRETTY_PRINT);
    exit;
}

// Handle timezone conversion correctly for negative timezones
$hours = floor(abs($timezone));
$minutes = ($timezone - floor($timezone)) * 60;

if ($timezone >= 0) {
    $dt->modify("-{$hours} hours");
    $dt->modify("-{$minutes} minutes");
} else {
    $dt->modify("+{$hours} hours");
    $dt->modify("+{$minutes} minutes");
}

$utTime = $dt->format("H:i");
$utDate = $dt->format("d.m.Y");

/* ==========================
SWISS EPHEMERIS PATH
========================== */

$swetestPath = "/app/swisseph/swetest";
$ephePath    = "/app/ephemeris";

// Check if swetest exists
if (!file_exists($swetestPath)) {
    echo json_encode([
        "status" => "error",
        "message" => "Swiss Ephemeris executable not found at: $swetestPath"
    ], JSON_PRETTY_PRINT);
    exit;
}

if (!file_exists($ephePath)) {
    echo json_encode([
        "status" => "error",
        "message" => "Ephemeris files not found at: $ephePath"
    ], JSON_PRETTY_PRINT);
    exit;
}

/* ==========================
SAFE COMMAND EXECUTION FUNCTION
========================== */

function executeSwetest($command, $args) {
    $escapedArgs = array_map('escapeshellarg', $args);
    $fullCommand = vsprintf($command, $escapedArgs);
    
    // Execute and capture both stdout and stderr
    $output = shell_exec("$fullCommand 2>&1");
    
    return $output;
}

/* ==========================
PLANETS COMMAND
========================== */

$planetCommand = "%s -edir%s -sid1 -b%s -ut%s -p0123456789t -fPls";
$planetOutput = executeSwetest($planetCommand, [
    $swetestPath, $ephePath, $utDate, $utTime
]);

if (!$planetOutput || trim($planetOutput) === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Swiss Ephemeris failed to return planetary data"
    ], JSON_PRETTY_PRINT);
    exit;
}

/* ==========================
DECIMAL → DMS CONVERSION
========================== */

function decimalToDMS($decimal) {
    $decimal = fmod($decimal, 360);
    if ($decimal < 0) $decimal += 360;

    $deg = floor($decimal);
    $minFloat = ($decimal - $deg) * 60;
    $min = floor($minFloat);
    $sec = round(($minFloat - $min) * 60);

    // Handle rounding overflow
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
COMBUST FUNCTION (IMPROVED)
========================== */

function isCombust($planet, $planet_long, $sun_long, $cazimi_threshold = 0.2833) { // 0.2833° = 17 minutes
    // Combustion limits in degrees (from traditional astrology)
    $limits = [
        "Mercury" => 14,
        "Venus" => 10,
        "Mars" => 17,
        "Jupiter" => 11,
        "Saturn" => 15,
        "Uranus" => 8,
        "Neptune" => 8,
        "Pluto" => 8
    ];
    
    // Moon has special combustion rules (not typically considered combust)
    if ($planet === "Moon") return false;
    
    // Only apply to planets with defined limits
    if (!isset($limits[$planet])) return false;

    $diff = abs($planet_long - $sun_long);
    
    if ($diff > 180) {
        $diff = 360 - $diff;
    }
    
    // Check for cazimi (within 17 minutes for very strong conjunction)
    if ($diff <= $cazimi_threshold) {
        return "cazimi";
    }
    
    // Check for combustion
    if ($diff <= $limits[$planet]) {
        return "combust";
    }
    
    return false;
}

/* ==========================
PARSE PLANETS
========================== */

$lines = explode("\n", trim($planetOutput));
$planets = [];

foreach ($lines as $line) {
    $line = trim($line);
    
    // Updated regex to handle various output formats
    if (preg_match('/^(Sun|Moon|Mercury|Venus|Mars|Jupiter|Saturn|Uranus|Neptune|Pluto|true Node|True Node)\s+([\d\.]+)\s+([-\d\.]+)/', $line, $matches)) {
        
        $name  = strtolower($matches[1]);
        $value = floatval($matches[2]);
        $speed = floatval($matches[3]);
        
        // Normalize node name
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

// Validate that we got at least some planets
if (empty($planets)) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to parse planetary data from Swiss Ephemeris",
        "raw_output" => $planetOutput
    ], JSON_PRETTY_PRINT);
    exit;
}

/* ==========================
ADD KETU (180° from Rahu)
========================== */

if (isset($planets['Rahu'])) {
    $rahuDecimal = $planets['Rahu']['decimal'];
    $ketuDecimal = fmod($rahuDecimal + 180, 360);
    if ($ketuDecimal < 0) $ketuDecimal += 360;
    
    $planets['Ketu'] = [
        "decimal" => round($ketuDecimal, 6),
        "dms" => decimalToDMS($ketuDecimal),
        "speed" => 0,
        "retrograde" => true, // Ketu is always retrograde
        "note" => "Calculated as Rahu + 180°"
    ];
}

/* ==========================
COMBUST CHECK
========================== */

if (isset($planets["Sun"])) {
    $sunLongitude = $planets["Sun"]["decimal"];
    
    foreach ($planets as $planet => $data) {
        if ($planet !== "Sun") { // Sun can't be combust with itself
            $combustStatus = isCombust($planet, $data["decimal"], $sunLongitude);
            if ($combustStatus) {
                $planets[$planet]["combust"] = $combustStatus;
                if ($combustStatus === "cazimi") {
                    $planets[$planet]["cazimi"] = true;
                }
            } else {
                $planets[$planet]["combust"] = false;
            }
        } else {
            $planets[$planet]["combust"] = false;
        }
    }
}

/* ==========================
HOUSES & ANGLES
========================== */

$houseCommand = "%s -edir%s -sid1 -b%s -ut%s -house%.6f,%.6f,P -fPl";
$houseOutput = executeSwetest($houseCommand, [
    $swetestPath, $ephePath, $utDate, $utTime, $lon, $lat
]);

$houses = [];

if ($houseOutput && trim($houseOutput) !== '') {
    $houseLines = explode("\n", trim($houseOutput));
    
    foreach ($houseLines as $line) {
        $line = trim($line);
        
        // Parse house cusps (format: "house 1 123.4567890")
        if (preg_match('/^house\s+(\d+)\s+([\d\.]+)/', $line, $matches)) {
            $houseNumber = intval($matches[1]);
            $value = floatval($matches[2]);
            
            // Only add houses 1-12
            if ($houseNumber >= 1 && $houseNumber <= 12) {
                $houses["House_" . $houseNumber] = [
                    "decimal" => round($value, 6),
                    "dms" => decimalToDMS($value)
                ];
            }
        }
        
        // Parse Ascendant
        if (preg_match('/^Ascendant\s+([\d\.]+)/', $line, $matches)) {
            $asc = floatval($matches[1]);
            $houses["Ascendant"] = [
                "decimal" => round($asc, 6),
                "dms" => decimalToDMS($asc)
            ];
        }
        
        // Parse Midheaven (MC)
        if (preg_match('/^MC\s+([\d\.]+)/', $line, $matches)) {
            $mc = floatval($matches[1]);
            $houses["MC"] = [
                "decimal" => round($mc, 6),
                "dms" => decimalToDMS($mc)
            ];
        }
        
        // Parse IC (Imum Coeli) if available
        if (preg_match('/^IC\s+([\d\.]+)/', $line, $matches)) {
            $ic = floatval($matches[1]);
            $houses["IC"] = [
                "decimal" => round($ic, 6),
                "dms" => decimalToDMS($ic)
            ];
        }
        
        // Parse Descendant if available
        if (preg_match('/^Descendant\s+([\d\.]+)/', $line, $matches)) {
            $desc = floatval($matches[1]);
            $houses["Descendant"] = [
                "decimal" => round($desc, 6),
                "dms" => decimalToDMS($desc)
            ];
        }
    }
}

// If no houses were parsed, provide error info
if (empty($houses)) {
    $houses = [
        "error" => "Failed to parse house data",
        "raw_output" => $houseOutput
    ];
}

/* ==========================
ADD ADDITIONAL INFO
========================== */

// Calculate zodiac signs for planets
$zodiac_signs = [
    "Aries", "Taurus", "Gemini", "Cancer", "Leo", "Virgo",
    "Libra", "Scorpio", "Sagittarius", "Capricorn", "Aquarius", "Pisces"
];

function getZodiacSign($decimal) {
    $signIndex = floor($decimal / 30);
    return $zodiac_signs[$signIndex];
}

// Add zodiac signs to planets
foreach ($planets as &$planet) {
    $planet["zodiac"] = getZodiacSign($planet["decimal"]);
    $planet["sign_longitude"] = round(fmod($planet["decimal"], 30), 6);
}

/* ==========================
FINAL JSON OUTPUT
========================== */

$response = [
    "status" => "success",
    "date" => $date,
    "time" => $time,
    "ut_date" => $utDate,
    "ut_time" => $utTime,
    "latitude" => $lat,
    "longitude" => $lon,
    "timezone_offset" => $timezone,
    "ayanamsa" => "Lahiri (Chitrapaksha)",
    "calculation_system" => "Swiss Ephemeris",
    "planets" => $planets,
    "houses" => $houses
];

// Add metadata
$response["metadata"] = [
    "generated" => date("Y-m-d H:i:s"),
    "planet_count" => count($planets),
    "house_count" => count($houses)
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);