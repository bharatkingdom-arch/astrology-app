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

<div style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; display: flex; justify-content: space-between; align-items: center;">
    <a href="load-kundli.php?id=<?= $row['id'] ?>" style="text-decoration: none; color: #333; flex-grow: 1;">
        <strong><?= htmlspecialchars($row['name']) ?></strong><br>
        <span style="color: #666; font-size: 0.9em;">
            DOB: <?= htmlspecialchars($row['birth_date']) ?> | Time: <?= htmlspecialchars($row['birth_time']) ?><br>
            Place: <?= htmlspecialchars($row['birth_place'] ?? 'Unknown') ?>
        </span>
    </a>
    <a href="delete-kundli.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this Kundli?');" style="color: red; text-decoration: none; padding: 5px 10px; border: 1px solid red; border-radius: 4px; font-size: 0.9em;">
        Delete
    </a>
</div>

<?php endwhile; ?>