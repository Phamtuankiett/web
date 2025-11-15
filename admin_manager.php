<?php
session_start();
require_once 'db.php';
require_once 'auth.php';
require_once 'admin.php';

if (!isLoggedIn() || !isSuperAdmin(getUserId())) {
    header('Location: dashboard.php');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $email = trim($_POST['email'] ?? '');
    
    if ($action === 'add') {
        if (addSubAdmin($email)) {
            $msg = 'Sub Admin Added Successfully!';
        } else {
            $msg = 'User Not Found Or Already Admin!';
        }
    }
}

if (isset($_GET['remove'])) {
    $email = $_GET['remove'];
    if (removeSubAdmin($email)) {
        $msg = 'Sub Admin Removed!';
    }
}

$subAdmins = getSubAdmins();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body">
    <div class="particles"></div>
    
    <div class="header">
        <h1>Admin Manager</h1>
    </div>
    
    <div class="container" style="max-width: 800px; margin: 20px auto;">
        <div class="form-box">
            <h2>Add Sub Admin</h2>
            <?php if ($msg): ?>
                <div class="success"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <input type="email" name="email" placeholder="Enter Admin Email" required>
                <button type="submit" class="btn">Submit</button>
            </form>
        </div>
        
        <div class="admin-list">
            <h2>Sub Admins List</h2>
            <?php if (empty($subAdmins)): ?>
                <p class="empty-state">No Sub Admins Yet</p>
            <?php else: ?>
                <?php foreach ($subAdmins as $admin): ?>
                <div class="admin-item">
                    <span><?= htmlspecialchars($admin['email']) ?></span>
                    <a href="?remove=<?= urlencode($admin['email']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Remove This Admin?')">Remove</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <button onclick="location.href='dashboard.php'" class="btn btn-secondary">Back To Dashboard</button>
    </div>
    
    <script src="script.js"></script>
</body>
</html>