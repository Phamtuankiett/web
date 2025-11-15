<?php
session_start();
require_once 'db.php';
require_once 'auth.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pwd = $_POST['password'] ?? '';
    
    if (login($email, $pwd)) {
        header('Location: dashboard.php');
        exit;
    }
    $err = 'Invalid Credentials';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="particles"></div>
    <div class="container">
        <div class="form-box">
            <h1>Login</h1>
            <?php if ($err): ?>
                <div class="error"><?= htmlspecialchars($err) ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn">Login</button>
            </form>
            <button onclick="location.href='register.php'" class="btn btn-secondary">Register</button>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>