<?php
define('DB_FILE', 'database.db');

function getDb() {
    $db = new PDO('sqlite:' . DB_FILE);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function initDb() {
    $db = getDb();
    
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        balance INTEGER DEFAULT 0,
        is_admin INTEGER DEFAULT 0,
        is_sub_admin INTEGER DEFAULT 0,
        plan TEXT DEFAULT 'free',
        plan_expire TEXT,
        key_limit INTEGER DEFAULT 0
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS keys (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        key_name TEXT NOT NULL,
        key_code TEXT UNIQUE NOT NULL,
        expires_at TEXT NOT NULL,
        tag_name TEXT DEFAULT 'kjtdzs1',
        spam_size TEXT DEFAULT '500KB',
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS redeem_codes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT UNIQUE NOT NULL,
        amount INTEGER NOT NULL,
        uses INTEGER DEFAULT 1,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS plans (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        duration INTEGER NOT NULL,
        price INTEGER NOT NULL,
        key_limit INTEGER NOT NULL
    )");
    
    $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE email=?');
    $stmt->execute(['kietne129kiet@gmail.com']);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $db->prepare('INSERT INTO users (email, password, is_admin, balance, key_limit) VALUES (?, ?, 1, 999999999, 999999)');
        $stmt->execute(['kietne129kiet@gmail.com', hash('sha256', 'ptkdzs1tg@')]);
    }
    
    $stmt = $db->prepare('SELECT COUNT(*) FROM plans');
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $plans = [
            ['Key 7 Days', 7, 5000, 20],
            ['Key 1 Month', 30, 15000, 50],
            ['Key 3 Months', 90, 30000, 100],
            ['Key 6 Months', 180, 70000, 9999]
        ];
        foreach ($plans as $p) {
            $stmt = $db->prepare('INSERT INTO plans (name, duration, price, key_limit) VALUES (?, ?, ?, ?)');
            $stmt->execute($p);
        }
    }
}

initDb();
?>