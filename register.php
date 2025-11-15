<?php
session_start();
require_once 'db.php';
require_once 'auth.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pwd = $_POST['password'] ?? '';
    $conf = $_POST['confirm_password'] ?? '';
    
    if ($pwd !== $conf) {
        $err = 'Passwords Do Not Match';
    } else {
        if (register($email, $pwd)) {
            header('Location: login.php');
            exit;
        }
        $err = 'Email Already Exists';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="particles"></div>
    <div class="container">
        <div class="form-box">
            <h1>Register</h1>
            <?php if ($err): ?>
                <div class="error"><?= htmlspecialchars($err) ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit" class="btn">Register</button>
            </form>
            <button onclick="location.href='login.php'" class="btn btn-secondary">Back To Login</button>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>