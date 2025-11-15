<?php
session_start();
require_once 'db.php';
require_once 'auth.php';
require_once 'keys.php';
require_once 'admin.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$keys = getUserKeys(getUserId());
$user = getUserInfo(getUserId());
$canCreate = canCreateKey(getUserId());
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body">
    <div class="particles"></div>
    
    <div class="header">
        <h1>Dashboard</h1>
        <div class="header-info">
            <span class="balance">Balance: <?= number_format($user['balance']) ?> VNĐ</span>
            <span class="plan-info"><?= $user['plan'] ? 'Plan: ' . $user['plan'] : 'No Plan' ?></span>
        </div>
        <div class="header-actions">
            <button class="btn" onclick="showModal('createModal')" <?= !$canCreate ? 'disabled' : '' ?>>Create Key</button>
            <button class="btn btn-danger" onclick="location.href='logout.php'">Logout</button>
        </div>
    </div>
    
    <div class="keys-container">
        <?php foreach ($keys as $k): ?>
        <div class="key-card">
            <div class="key-name"><?= htmlspecialchars($k['key_name']) ?></div>
            <div class="key-info">
                <span>URL:</span> 
                <a href="key.php?c=<?= $k['key_code'] ?>" target="_blank">key.php?c=<?= $k['key_code'] ?></a>
            </div>
            <div class="key-info">
                <span>Tag:</span> &lt;<?= htmlspecialchars($k['tag_name']) ?>&gt;
            </div>
            <div class="key-info">
                <span>Spam Size:</span> <?= htmlspecialchars($k['spam_size']) ?>
            </div>
            <div class="key-info">
                <span>Expires:</span> <?= date('Y-m-d H:i', strtotime($k['expires_at'])) ?>
            </div>
            <div class="key-actions">
                <button class="btn btn-success" onclick='showCode(<?= json_encode($k) ?>)'>Show Code</button>
                <button class="btn btn-success" onclick='editKey(<?= json_encode($k) ?>)'>Edit</button>
                <button class="btn btn-danger" onclick="deleteKey(<?= $k['id'] ?>)">Delete</button>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($keys)): ?>
        <div class="empty-state">
            <p>No keys yet. Create your first key!</p>
        </div>
        <?php endif; ?>
    </div>

    <div class="nav-buttons-wrapper">
        <div class="nav-buttons">
            <button class="nav-btn" onclick="location.href='gift_code.php'">Gift Code</button>
            <button class="nav-btn" onclick="location.href='buy_key.php'">Buy Key</button>
            
            <?php if (isAdmin(getUserId())): ?>
            <button class="nav-btn" onclick="location.href='create_redeem.php'">Create Redeem Code</button>
            <button class="nav-btn" onclick="location.href='edit_plans.php'">Edit Package Price</button>
            <?php if (isSuperAdmin(getUserId())): ?>
            <button class="nav-btn" onclick="location.href='admin_manager.php'">Admin Manager</button>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div id="createModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <h2>Create Key</h2>
            <form method="POST" action="actions.php">
                <input type="hidden" name="action" value="create">
                <input type="text" name="key_name" placeholder="Enter Key Name" required>
                <input type="text" name="tag_name" placeholder="Tag Name (e.g., kjtdzs1)" value="kjtdzs1" required>
                <input type="text" name="spam_size" placeholder="Spam Size (e.g., 1MB or 500KB)" value="500KB">
                <small class="note">Note: Less spam = faster key loading</small>
                <label>Expiration Date</label>
                <div class="date-select">
                    <select name="day" required>
                        <?php for ($d = 1; $d <= 31; $d++): ?>
                        <option value="<?= $d ?>"><?= $d ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="month" required>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>"><?= $m ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="year" required>
                        <?php for ($y = 2025; $y <= 2030; $y++): ?>
                        <option value="<?= $y ?>"><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn">Create</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <h2>Edit Key</h2>
            <form method="POST" action="actions.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="key_id" id="edit_id">
                <input type="text" name="key_name" id="edit_name" placeholder="Enter Key Name" required>
                <input type="text" name="tag_name" id="edit_tag" placeholder="Tag Name" required>
                <input type="text" name="spam_size" id="edit_spam" placeholder="Spam Size">
                <small class="note">Note: Less spam = faster key loading</small>
                <label>Expiration Date</label>
                <div class="date-select">
                    <select name="day" id="edit_day" required>
                        <?php for ($d = 1; $d <= 31; $d++): ?>
                        <option value="<?= $d ?>"><?= $d ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="month" id="edit_month" required>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>"><?= $m ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="year" id="edit_year" required>
                        <?php for ($y = 2025; $y <= 2030; $y++): ?>
                        <option value="<?= $y ?>"><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn">Update</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="codeModal" class="modal">
        <div class="modal-content code-modal">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <h2>Ex Code Check Key Python:</h2>
            <pre id="pyCode"></pre>
            <button class="btn" onclick="copyCode()">Copy Code</button>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>