<?php
session_start();
require 'engine/db.php';

$email = $_SESSION['user_email'];

$stmt = $conn->prepare("
SELECT * FROM kundlis
WHERE user_email=:email
ORDER BY created_at DESC
");
$stmt->execute(['email' => $email]);
?>

<h2>Your Saved Kundlis</h2>

<?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>

<div>
<?= $row['name'] ?> -
<?= $row['birth_date'] ?> -
<?= $row['birth_time'] ?>
</div>

<?php endwhile; ?>