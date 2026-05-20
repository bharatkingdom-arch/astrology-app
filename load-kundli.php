<?php
session_start();
require_once __DIR__ . '/engine/db.php';
require_once __DIR__ . '/engine/Panchanga.php';
require_once __DIR__ . '/engine/AdvancedPanchanga.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: saved-kundlis.php");
    exit;
}

$email = $_SESSION['user_email'];

$stmt = $conn->prepare("SELECT * FROM kundlis WHERE id = :id AND user_email = :email");
$stmt->execute(['id' => $id, 'email' => $email]);
$kundli = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kundli) {
    header("Location: saved-kundlis.php");
    exit;
}

$planets = json_decode($kundli['planets'], true);
$houses = json_decode($kundli['houses'], true);

// Extract date and time parts
$dateParts = explode('-', $kundli['birth_date']); // YYYY-MM-DD
$timeParts = explode(':', $kundli['birth_time']); // HH:MM:SS

$year = $dateParts[0];
$month = $dateParts[1];
$day = $dateParts[2];
$hour = $timeParts[0];
$minute = $timeParts[1];
$second = $timeParts[2];

$datetime = new DateTime("{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}", new DateTimeZone('Asia/Kolkata'));
$jd = $datetime->getTimestamp() / 86400 + 2440587.5;

$panchanga = Panchanga::calculate(
    $planets['Sun']['decimal'] ?? 0,
    $planets['Moon']['decimal'] ?? 0,
    $jd,
    $planets['Sun']['speed'] ?? 0.98,
    $planets['Moon']['speed'] ?? 13.1,
    $datetime->getTimestamp()
);

$advanced_panchanga = AdvancedPanchanga::calculate(
    $datetime->getTimestamp(),
    $kundli['latitude'],
    $kundli['longitude'],
    5.5, // Timezone
    $planets['Sun']['decimal'] ?? 0,
    $planets['Moon']['decimal'] ?? 0,
    $panchanga
);

$lagna = $houses['Ascendant']['decimal'] ?? null;

$_SESSION['kundli_data'] = [
    'name' => $kundli['name'],
    'gender' => $kundli['gender'],
    'date' => "{$day}-{$month}-{$year}",
    'time' => "{$hour}:{$minute}:{$second}",
    'latitude' => $kundli['latitude'],
    'longitude' => $kundli['longitude'],
    'planets' => $planets,
    'houses' => $houses,
    'panchanga' => $panchanga,
    'advanced_panchanga' => $advanced_panchanga,
    'lagna' => $lagna
];

header("Location: kundli-details.php");
exit;
?>
