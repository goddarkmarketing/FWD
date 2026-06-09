<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();

$page_title = 'เปลี่ยนรหัสผ่าน';
$active_file = 'change-password.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $current = (string) ($_POST['current_password'] ?? '');
    $new = (string) ($_POST['new_password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');
    $auth = admin_auth_config();

    if (!password_verify($current, $auth['password_hash'])) {
        $error = 'รหัสผ่านปัจจุบันไม่ถูกต้อง';
    } elseif (strlen($new) < 8) {
        $error = 'รหัสผ่านใหม่ต้องมีอย่างน้อย 8 ตัวอักษร';
    } elseif ($new !== $confirm) {
        $error = 'รหัสผ่านใหม่ไม่ตรงกัน';
    } else {
        $auth['password_hash'] = password_hash($new, PASSWORD_DEFAULT);
        $auth['updated_at'] = date('c');
        cms_save('auth', $auth);
        admin_flash('success', 'เปลี่ยนรหัสผ่านเรียบร้อย');
        admin_redirect('change-password.php');
    }
}

ob_start();
?>
<?php if ($error): ?>
<div class="admin-alert admin-alert--error"><?= admin_h($error) ?></div>
<?php endif; ?>
<form method="post" class="admin-form admin-card">
    <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">
    <div class="form-row">
        <label for="current_password">รหัสผ่านปัจจุบัน</label>
        <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
    </div>
    <div class="form-row">
        <label for="new_password">รหัสผ่านใหม่</label>
        <input type="password" id="new_password" name="new_password" required autocomplete="new-password">
    </div>
    <div class="form-row">
        <label for="confirm_password">ยืนยันรหัสผ่านใหม่</label>
        <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
    </div>
    <div class="admin-actions" style="border:0;padding:0;margin:0">
        <button type="submit" class="admin-btn admin-btn--primary">เปลี่ยนรหัสผ่าน</button>
    </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
