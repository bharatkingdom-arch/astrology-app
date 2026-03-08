<?php

header("Content-Type: application/json");

$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';

if (!$date || !$time) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing date or time"
    ]);
    exit;
}

$cmd = "/app/swisseph/swetest -edir=/app/ephemeris/ -b{$date} -ut{$time} -p0123456789 -fPl";

$output = [];
exec($cmd, $output);

$planets = [];

foreach ($output as $line) {

    if (preg_match('/^(Sun|Moon|Mercury|Venus|Mars|Jupiter|Saturn|Uranus|Neptune|Pluto)\s+([0-9.]+)/', $line, $m)) {

        $planets[$m[1]] = [
            "decimal" => floatval($m[2])
        ];
    }
}

echo json_encode([
    "status" => "success",
    "planets" => $planets
]);