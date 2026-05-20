<?php
session_start();
require_once __DIR__ . '/engine/db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
$email = $_SESSION['user_email'];

if ($id) {
    // Only delete if it belongs to the logged-in user
    $stmt = $conn->prepare("DELETE FROM kundlis WHERE id = :id AND user_email = :email");
    $stmt->execute(['id' => $id, 'email' => $email]);
}

header("Location: saved-kundlis.php");
exit;
?>
