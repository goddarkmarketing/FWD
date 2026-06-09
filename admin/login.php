<?php
require_once __DIR__ . '/_bootstrap.php';
if (admin_is_logged_in()) {
    admin_redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(admin_post_string('email'));
    $password = (string) ($_POST['password'] ?? '');
    $auth = admin_auth_config();

    if (strtolower($auth['email']) === $email && password_verify($password, $auth['password_hash'])) {
        $_SESSION['admin_user'] = $auth['email'];
        session_regenerate_id(true);
        admin_redirect('index.php');
    }

    $error = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ — CMS</title>
    <link rel="stylesheet" href="../assets/css/admin.css?v=2">
</head>
<body class="admin-login-body">
    <div class="admin-login-wrap">
        <div class="admin-login-panel">
            <div class="admin-login-card">
                <div class="admin-login-card__brand">
                    <span class="admin-login-card__logo">FWD</span>
                    <span class="admin-login-card__title">Content Studio</span>
                </div>
                <h2>เข้าสู่ระบบ</h2>
                <p class="admin-login-sub">ใช้อีเมลและรหัสผ่านของผู้ดูแลระบบ</p>
                <?php if ($error): ?>
                <div class="admin-alert admin-alert--error" style="margin:0 0 1.25rem"><?= admin_h($error) ?></div>
                <?php endif; ?>
                <form method="post" class="admin-form">
                    <div class="form-row">
                        <label for="email">อีเมล</label>
                        <input type="email" id="email" name="email" required autocomplete="username" placeholder="name@example.com" value="<?= admin_h($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <label for="password">รหัสผ่าน</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                    </div>
                    <button type="submit" class="admin-btn admin-btn--primary">เข้าสู่ระบบ</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
