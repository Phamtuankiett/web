<?php
session_start();
require_once 'db.php';
require_once 'auth.php';
require_once 'admin.php';

if (!isLoggedIn() || !isAdmin(getUserId())) {
    header('Location: dashboard.php');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['plan_id'] ?? 0);
    $price = (int)($_POST['price'] ?? 0);
    updatePlan($id, $price);
    $msg = 'Plan Price Updated Successfully!';
}

$plans = getPlans();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package Price</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body">
    <div class="particles"></div>
    
    <div class="header">
        <h1>Edit Package Price</h1>
    </div>
    
    <div class="container" style="max-width: 800px; margin: 20px auto;">
        <?php if ($msg): ?>
            <div class="success"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        
        <?php foreach ($plans as $plan): ?>
        <div class="plan-edit-card">
            <h2><?= htmlspecialchars($plan['name']) ?></h2>
            <p>Duration: <?= $plan['duration'] ?> Days | Key Limit: <?= $plan['key_limit'] ?></p>
            <form method="POST">
                <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                <input type="number" name="price" value="<?= $plan['price'] ?>" placeholder="Price (VNĐ)" required>
                <button type="submit" class="btn">Update</button>
            </form>
        </div>
        <?php endforeach; ?>
        
        <button onclick="location.href='dashboard.php'" class="btn btn-secondary" style="width: 100%; margin-top: 10px;">Back To Dashboard</button>
    </div>
    
    <script src="script.js"></script>
</body>
</html>