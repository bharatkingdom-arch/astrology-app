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

<div style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
    <a href="load-kundli.php?id=<?= $row['id'] ?>" style="text-decoration: none; color: #333; display: block;">
        <strong><?= htmlspecialchars($row['name']) ?></strong><br>
        <span style="color: #666; font-size: 0.9em;">
            DOB: <?= htmlspecialchars($row['birth_date']) ?> | Time: <?= htmlspecialchars($row['birth_time']) ?>
        </span>
    </a>
</div>

<?php endwhile; ?>