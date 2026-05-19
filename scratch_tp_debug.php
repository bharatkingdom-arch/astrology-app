<?php
require_once 'engine/TithiPraveshaEngine.php';
$birthDate = '18.09.1986';
$birthTime = '00:24:00';
$timezone = 5.5;
$S_b = 151.01;
$M_b = 313.2;

$engine = new TithiPraveshaEngine();
$T_b = fmod($M_b - $S_b + 360, 360);
$SunSign_b = floor($S_b / 30);
echo "T_b: $T_b, SunSign_b: $SunSign_b\n";

// test 2024
$year = 2024;
$dtStart = new DateTime("$year-09-18 00:00:00", new DateTimeZone('UTC'));
$dtStart->modify('-30 days');
$utDate = $dtStart->format('d.m.Y');
$utTime = '00:00:00';
$swetestPath = __DIR__ . '/swisseph/swetest';
$ephePath    = __DIR__ . '/ephemeris';

$cmd = "$swetestPath -edir$ephePath -b$utDate -ut$utTime -p01 -fTPl -sid1 -n60 -s1";
$output = shell_exec($cmd);
$lines = explode("\n", trim($output));
$daysData = [];

foreach ($lines as $line) {
    $line = trim($line);
    if (preg_match('/^(\d{2}\.\d{2}\.\d{4})\s+([\d:]+)\s+UT\s+(Sun|Moon)\s+([\d\.]+)/', $line, $matches)) {
        $d = $matches[1];
        $t = $matches[2];
        $planet = $matches[3];
        $long = floatval($matches[4]);

        $datetimeKey = "$d $t";
        if (!isset($daysData[$datetimeKey])) {
            $daysData[$datetimeKey] = [];
        }
        $daysData[$datetimeKey][$planet] = $long;
    }
}

$prevTithi = null;
foreach ($daysData as $dtKey => $planets) {
    if (!isset($planets['Sun']) || !isset($planets['Moon'])) continue;
    $S_t = $planets['Sun'];
    $M_t = $planets['Moon'];
    $T_t = fmod($M_t - $S_t + 360, 360);
    $SunSign_t = floor($S_t / 30);
    
    if ($prevTithi !== null) {
        $crossed = false;
        if ($prevTithi <= $T_b && $T_t >= $T_b && ($T_t - $prevTithi) < 30) {
            $crossed = true;
        } elseif ($prevTithi > 330 && $T_t < 30) {
            if ($T_b >= $prevTithi || $T_b <= $T_t) {
                $crossed = true;
            }
        }
        if ($crossed) {
            echo "Crossed! prevTithi: $prevTithi, T_t: $T_t, prevSunSign: $prevSunSign, SunSign_t: $SunSign_t\n";
        }
    }
    $prevTithi = $T_t;
    $prevSunSign = $SunSign_t;
}
