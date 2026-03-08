<?php
require_once 'engine/Panchanga.php';

$sun  = 224.2344;
$moon = 262.4731;
$jd   = 2444938.86805556;

$result = Panchanga::calculate($sun, $moon, $jd);

echo "<pre>";
print_r($result);
echo "</pre>";
