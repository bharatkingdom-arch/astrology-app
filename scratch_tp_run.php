<?php
require_once 'engine/TithiPraveshaEngine.php';
$birthDate = '18.09.1986';
$birthTime = '00:24:00';
$timezone = 5.5;
// Sample Sun/Moon for this date
$S_b = 151.01;
$M_b = 313.2;

$res = TithiPraveshaEngine::calculateForYears($birthDate, $birthTime, $timezone, $S_b, $M_b, 2024, 2);
print_r($res);
