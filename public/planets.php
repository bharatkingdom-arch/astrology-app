<?php

require_once "../../engine/PlanetEngine.php";

header("Content-Type: application/json");

$date = $_GET['date'] ?? null;
$time = $_GET['time'] ?? null;

if (!$date || !$time) {

    echo json_encode([
        "status" => "error",
        "message" => "date or time missing"
    ]);

    exit;
}

$planets = PlanetEngine::getPlanets($date, $time);

echo json_encode([
    "status" => "success",
    "planets" => $planets
]);