<?php
session_start();
require_once 'db.php';
require_once 'auth.php';
require_once 'admin.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planId = (int)($_POST['plan_id'] ?? 0);
    $result = buyPlan(getUserId(), $planId);
    
    if ($result['success']) {
        header('Location: success.php');
        exit;
    } else {
        $error = $result['msg'];
    }
}

$user = getUserInfo(getUserId());
$plans = getPlans();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Key</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body">
    <div class="particles"></div>
    
    <div class="header">
        <h1>Buy Key</h1>
        <div class="balance-display">
            <span>Balance: <?= number_format($user['balance']) ?> VNĐ</span>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="error-banner"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="plans-container">
        <?php foreach ($plans as $plan): ?>
        <div class="plan-card">
            <h2><?= htmlspecialchars($plan['name']) ?></h2>
            <div class="plan-price"><?= number_format($plan['price']) ?> VNĐ</div>
            <div class="plan-features">
                <div class="feature">✓ Key Creation Limit: <?= $plan['key_limit'] ?> Keys</div>
                <div class="feature">✓ Anti Crack Key: Active</div>
                <div class="feature">✓ Custom Tag Name</div>
                <div class="feature">✓ Custom Spam Size</div>
                <div class="feature">✓ 24/7 Support</div>
                <div class="feature">✓ Fast Key Loading</div>
            </div>
            <form method="POST">
                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                <button type="submit" class="btn">Buy Now</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    
    <button onclick="location.href='dashboard.php'" class="btn btn-secondary" style="margin: 20px auto; display: block; width: 300px;">Back To Dashboard</button>
    
    <script src="script.js"></script>
</body>
</html>