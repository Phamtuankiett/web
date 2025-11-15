<?php
session_start();
require_once 'db.php';
require_once 'auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="particles"></div>
    <div class="container">
        <div class="form-box success-box">
            <div class="success-icon">✓</div>
            <h1>Transaction Successful!</h1>
            <p>Your purchase has been completed successfully.</p>
            <button onclick="location.href='dashboard.php'" class="btn">Return Home</button>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>