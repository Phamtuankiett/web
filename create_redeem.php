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
    $code = trim($_POST['code'] ?? '');
    $amount = (int)($_POST['amount'] ?? 0);
    $uses = (int)($_POST['uses'] ?? 1);
    
    if (createRedeemCode($code, $amount, $uses)) {
        $msg = 'Redeem Code Created Successfully!';
    } else {
        $msg = 'Code Already Exists!';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Redeem Code</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="particles"></div>
    <div class="container">
        <div class="form-box">
            <h1>Create Redeem Code</h1>
            <?php if ($msg): ?>
                <div class="<?= strpos($msg, 'Successfully') !== false ? 'success' : 'error' ?>"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>
            <form method="POST">
                <label>Code Name</label>
                <input type="text" name="code" placeholder="Enter Code Name" required>
                
                <label>Amount (VNĐ)</label>
                <input type="number" name="amount" placeholder="Enter Amount (e.g., 1000)" required>
                
                <label>Number Of Uses</label>
                <input type="number" name="uses" placeholder="Enter Uses Count" value="1" required>
                
                <button type="submit" class="btn">Submit</button>
                <button type="button" onclick="location.href='dashboard.php'" class="btn btn-secondary">Back To Dashboard</button>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>