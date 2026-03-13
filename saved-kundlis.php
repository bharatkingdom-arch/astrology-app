<?php
session_start();
require 'engine/db.php';

$email = $_SESSION['user_email'];

$result = $conn->query("
SELECT * FROM kundlis
WHERE user_email='$email'
ORDER BY created_at DESC
");
?>

<h2>Your Saved Kundlis</h2>

<?php while($row = $result->fetch_assoc()): ?>

<div>
<?= $row['name'] ?> -
<?= $row['birth_date'] ?> -
<?= $row['birth_time'] ?>
</div>

<?php endwhile; ?>