<?php
session_start();
require_once __DIR__ . '/engine/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $error = "Email is already registered. Please login.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :hash)");
            if ($stmt->execute(['name' => $name, 'email' => $email, 'hash' => $hash])) {
                $success = "Registration successful! You can now <a href='login.php' style='color:#ff7a00;text-decoration:underline;'>Login</a>.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<?php require 'header.php'; ?>

<section class="kundli-section" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div class="kundli-form-box" style="max-width: 400px; width: 100%;">
        <h3 style="text-align: center;">Sign Up</h3>
        
        <?php if ($error): ?>
            <div style="background:#ffcccc; color:red; padding:10px; border-radius:5px; margin-bottom:15px; font-size:14px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div style="background:#ccffcc; color:green; padding:10px; border-radius:5px; margin-bottom:15px; font-size:14px;">
                <?= $success ?>
            </div>
        <?php else: ?>
            <form method="post" action="signup.php">
                <label>Name</label>
                <input type="text" name="name" required placeholder="Enter your full name" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px;">
                
                <label>Email</label>
                <input type="email" name="email" required placeholder="Enter your email" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px;">
                
                <label>Password</label>
                <input type="password" name="password" required placeholder="Create a password" style="width:100%; padding:10px; margin-bottom:20px; border:1px solid #ccc; border-radius:5px;">
                
                <button type="submit" class="generate-btn" style="width:100%;">Create Account</button>
            </form>
            
            <p style="text-align:center; margin-top:20px; font-size:14px;">
                Already have an account? <a href="login.php" style="color:#ff7a00; font-weight:bold;">Login here</a>
            </p>
        <?php endif; ?>
    </div>
</section>

<?php require 'bottom.php'; ?>
