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

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        if (!canCreateKey(getUserId())) {
            header('Location: dashboard.php?error=limit');
            exit;
        }
        $name = $_POST['key_name'] ?? '';
        $tag = $_POST['tag_name'] ?? 'kjtdzs1';
        $spam = $_POST['spam_size'] ?? '500KB';
        $day = (int)($_POST['day'] ?? 1);
        $month = (int)($_POST['month'] ?? 1);
        $year = (int)($_POST['year'] ?? 2025);
        $exp = sprintf('%04d-%02d-%02d 23:59:59', $year, $month, $day);
        createKey(getUserId(), $name, $exp, $tag, $spam);
        break;
        
    case 'edit':
        $id = (int)($_POST['key_id'] ?? 0);
        $name = $_POST['key_name'] ?? '';
        $tag = $_POST['tag_name'] ?? 'kjtdzs1';
        $spam = $_POST['spam_size'] ?? '500KB';
        $day = (int)($_POST['day'] ?? 1);
        $month = (int)($_POST['month'] ?? 1);
        $year = (int)($_POST['year'] ?? 2025);
        $exp = sprintf('%04d-%02d-%02d 23:59:59', $year, $month, $day);
        updateKey(getUserId(), $id, $name, $exp, $tag, $spam);
        break;
        
    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        deleteKey(getUserId(), $id);
        break;
}

header('Location: dashboard.php');
?>