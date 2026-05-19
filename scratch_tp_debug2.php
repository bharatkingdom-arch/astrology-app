<?php
require_once 'engine/TithiPraveshaEngine.php';
$swetestPath = __DIR__ . '/swisseph/swetest';
$ephePath    = __DIR__ . '/ephemeris';

$cmd = "$swetestPath -edir$ephePath -b19.08.2024 -ut00:00:00 -p01 -fTPl -sid1 -n5 -s1";
echo shell_exec($cmd);
