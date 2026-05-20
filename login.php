<?php
session_start();
require_once __DIR__ . '/engine/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_email'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Setup session
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<?php require 'header.php'; ?>

<section class="kundli-section" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div class="kundli-form-box" style="max-width: 400px; width: 100%;">
        <h3 style="text-align: center;">Login</h3>
        
        <?php if ($error): ?>
            <div style="background:#ffcccc; color:red; padding:10px; border-radius:5px; margin-bottom:15px; font-size:14px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="login.php">
            <label>Email</label>
            <input type="email" name="email" required placeholder="Enter your email" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px;">
            
            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter your password" style="width:100%; padding:10px; margin-bottom:20px; border:1px solid #ccc; border-radius:5px;">
            
            <button type="submit" class="generate-btn" style="width:100%;">Login</button>
        </form>
        
        <p style="text-align:center; margin-top:20px; font-size:14px;">
            Don't have an account? <a href="signup.php" style="color:#ff7a00; font-weight:bold;">Sign up here</a>
        </p>
    </div>
</section>

<?php require 'bottom.php'; ?>
