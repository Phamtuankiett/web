<?php
function genKeyCode() {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < 10; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

function getUserKeys($uid) {
    $db = getDb();
    $db->exec("DELETE FROM keys WHERE expires_at < datetime('now')");
    $stmt = $db->prepare('SELECT * FROM keys WHERE user_id=? ORDER BY id DESC');
    $stmt->execute([$uid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createKey($uid, $name, $exp, $tag = 'kjtdzs1', $spam = '500KB') {
    $db = getDb();
    $code = genKeyCode();
    $stmt = $db->prepare('INSERT INTO keys (user_id, key_name, key_code, expires_at, tag_name, spam_size) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$uid, $name, $code, $exp, $tag, $spam]);
    return $code;
}

function updateKey($uid, $id, $name, $exp, $tag, $spam) {
    $db = getDb();
    $stmt = $db->prepare('UPDATE keys SET key_name=?, expires_at=?, tag_name=?, spam_size=? WHERE id=? AND user_id=?');
    $stmt->execute([$name, $exp, $tag, $spam, $id, $uid]);
}

function deleteKey($uid, $id) {
    $db = getDb();
    $stmt = $db->prepare('DELETE FROM keys WHERE id=? AND user_id=?');
    $stmt->execute([$id, $uid]);
}

function getKeyByCode($code) {
    $db = getDb();
    $stmt = $db->prepare('SELECT key_name, expires_at FROM keys WHERE key_code=?');
    $stmt->execute([$code]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function genSpamHtml($name) {
    $html = [];
    $js = [];
    
    for ($i = 0; $i < 8000; $i++) {
        $html[] = sprintf('<div class="c%d" id="e%d" data-v="%s">', $i, $i, bin2hex(random_bytes(80)));
        $html[] = sprintf('<span>%s</span>', base64_encode(random_bytes(60)));
        $html[] = sprintf('<p style="display:none">%s</p>', bin2hex(random_bytes(90)));
        $html[] = '</div>';
    }
    
    for ($i = 0; $i < 12000; $i++) {
        $js[] = sprintf('const a%d="%s";', $i, bin2hex(random_bytes(70)));
        $js[] = sprintf('let b%d={v:"%s",k:"%s"}', $i, base64_encode(random_bytes(65)), bin2hex(random_bytes(55)));
        $js[] = sprintf('var c%d=["%s","%s"];', $i, bin2hex(random_bytes(75)), base64_encode(random_bytes(70)));
    }
    
    $hiddenDivs = '';
    for ($i = 0; $i < 5000; $i++) {
        $hiddenDivs .= '<div style="display:none">' . bin2hex(random_bytes(100)) . '</div>';
    }
    
    return sprintf('<!DOCTYPE html><html><head><title>Page</title><style>
body{font-family:Arial;padding:20px;background:#f5f5f5}
.container{max-width:800px;margin:0 auto;background:white;padding:30px;border-radius:8px}
h1{color:#333;margin-bottom:20px}.content{line-height:1.6;color:#666}
</style></head><body><div class="container"><h1>Welcome</h1>
<div class="content"><p>This is a standard webpage with normal content. Everything looks completely normal here.</p>
<p>You can browse through this page and find various information displayed in a standard format.</p></div>
%s<kjtdzs1>%s</kjtdzs1>%s</div><script>%s</script></body></html>',
        implode('', $html), $name, $hiddenDivs, implode(';', $js));
}
?>