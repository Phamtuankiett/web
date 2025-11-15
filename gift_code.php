<?php
session_start();
require_once 'db.php';
require_once 'auth.php';
require_once 'admin.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$msg = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $result = redeemCode(getUserId(), $code);
    
    if ($result['success']) {
        $msg = 'Successfully Added ' . number_format($result['amount']) . ' VNĐ To Your Balance!';
        $success = true;
    } else {
        $msg = $result['msg'];
    }
}

$user = getUserInfo(getUserId());
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift Code</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="particles"></div>
    <div class="container">
        <div class="form-box">
            <h1>Gift Code</h1>
            <div class="balance-display">
                <p>Current Balance: <strong><?= number_format($user['balance']) ?> VNĐ</strong></p>
            </div>
            <?php if ($msg): ?>
                <div class="<?= $success ? 'success' : 'error' ?>"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="code" placeholder="Enter Gift Code" required>
                <button type="submit" class="btn">Submit</button>
            </form>
            <button onclick="location.href='dashboard.php'" class="btn btn-secondary">Back To Dashboard</button>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>