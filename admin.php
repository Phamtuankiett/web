<?php
function isAdmin($uid) {
    $db = getDb();
    $stmt = $db->prepare('SELECT is_admin, is_sub_admin FROM users WHERE id=?');
    $stmt->execute([$uid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user && ($user['is_admin'] == 1 || $user['is_sub_admin'] == 1);
}

function isSuperAdmin($uid) {
    $db = getDb();
    $stmt = $db->prepare('SELECT is_admin FROM users WHERE id=?');
    $stmt->execute([$uid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user && $user['is_admin'] == 1;
}

function createRedeemCode($code, $amount, $uses) {
    $db = getDb();
    try {
        $stmt = $db->prepare('INSERT INTO redeem_codes (code, amount, uses) VALUES (?, ?, ?)');
        $stmt->execute([$code, $amount, $uses]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function redeemCode($uid, $code) {
    $db = getDb();
    $db->beginTransaction();
    
    try {
        $stmt = $db->prepare('SELECT * FROM redeem_codes WHERE code=?');
        $stmt->execute([$code]);
        $redeem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$redeem) {
            $db->rollBack();
            return ['success' => false, 'msg' => 'Invalid Code'];
        }
        
        $stmt = $db->prepare('UPDATE users SET balance = balance + ? WHERE id=?');
        $stmt->execute([$redeem['amount'], $uid]);
        
        if ($redeem['uses'] <= 1) {
            $stmt = $db->prepare('DELETE FROM redeem_codes WHERE code=?');
            $stmt->execute([$code]);
        } else {
            $stmt = $db->prepare('UPDATE redeem_codes SET uses = uses - 1 WHERE code=?');
            $stmt->execute([$code]);
        }
        
        $db->commit();
        return ['success' => true, 'amount' => $redeem['amount']];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'msg' => 'Error Processing Code'];
    }
}

function addSubAdmin($email) {
    $db = getDb();
    try {
        $stmt = $db->prepare('UPDATE users SET is_sub_admin=1, key_limit=999999 WHERE email=?');
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

function removeSubAdmin($email) {
    $db = getDb();
    $stmt = $db->prepare('UPDATE users SET is_sub_admin=0 WHERE email=? AND is_admin=0');
    $stmt->execute([$email]);
    return $stmt->rowCount() > 0;
}

function getSubAdmins() {
    $db = getDb();
    $stmt = $db->query('SELECT email FROM users WHERE is_sub_admin=1 AND is_admin=0');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPlans() {
    $db = getDb();
    $stmt = $db->query('SELECT * FROM plans ORDER BY duration ASC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updatePlan($id, $price) {
    $db = getDb();
    $stmt = $db->prepare('UPDATE plans SET price=? WHERE id=?');
    $stmt->execute([$price, $id]);
}

function buyPlan($uid, $planId) {
    $db = getDb();
    $db->beginTransaction();
    
    try {
        $stmt = $db->prepare('SELECT * FROM plans WHERE id=?');
        $stmt->execute([$planId]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$plan) {
            $db->rollBack();
            return ['success' => false, 'msg' => 'Plan Not Found'];
        }
        
        $stmt = $db->prepare('SELECT balance FROM users WHERE id=?');
        $stmt->execute([$uid]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user['balance'] < $plan['price']) {
            $db->rollBack();
            return ['success' => false, 'msg' => 'Insufficient Balance'];
        }
        
        $expireDate = date('Y-m-d H:i:s', strtotime('+' . $plan['duration'] . ' days'));
        
        $stmt = $db->prepare('UPDATE users SET balance = balance - ?, plan=?, plan_expire=?, key_limit=? WHERE id=?');
        $stmt->execute([$plan['price'], $plan['name'], $expireDate, $plan['key_limit'], $uid]);
        
        $db->commit();
        return ['success' => true, 'plan' => $plan['name']];
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'msg' => 'Transaction Failed'];
    }
}

function getUserInfo($uid) {
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM users WHERE id=?');
    $stmt->execute([$uid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function canCreateKey($uid) {
    $db = getDb();
    $stmt = $db->prepare('SELECT is_admin, is_sub_admin, key_limit FROM users WHERE id=?');
    $stmt->execute([$uid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user['is_admin'] == 1 || $user['is_sub_admin'] == 1) {
        return true;
    }
    
    $stmt = $db->prepare('SELECT COUNT(*) FROM keys WHERE user_id=?');
    $stmt->execute([$uid]);
    $count = $stmt->fetchColumn();
    
    return $count < $user['key_limit'];
}
?>