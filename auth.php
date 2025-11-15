<?php
function hashPwd($pwd) {
    return hash('sha256', $pwd);
}

function isLoggedIn() {
    return isset($_SESSION['uid']);
}

function getUserId() {
    return $_SESSION['uid'] ?? null;
}

function login($email, $pwd) {
    $db = getDb();
    $stmt = $db->prepare('SELECT id FROM users WHERE email=? AND password=?');
    $stmt->execute([$email, hashPwd($pwd)]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['uid'] = $user['id'];
        return true;
    }
    return false;
}

function register($email, $pwd) {
    $db = getDb();
    try {
        $stmt = $db->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
        $stmt->execute([$email, hashPwd($pwd)]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function logout() {
    session_destroy();
}
?>