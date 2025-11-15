<?php
session_start();
require_once 'db.php';
require_once 'auth.php';
require_once 'keys.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

header('Location: dashboard.php');
?>