<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();

$page_title = 'Footer & Cookie';
$active_file = 'footer.php';
$data = cms_load('footer', []);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    foreach (['cta_title', 'cta_desc', 'cta_button', 'copyright', 'disclaimer', 'cookie_text', 'cookie_accept', 'privacy_url', 'terms_url'] as $f) {
        $data[$f] = admin_post_string($f);
    }
    cms_save('footer', $data);
    admin_flash('success', 'บันทึก Footer เรียบร้อย');
    admin_redirect('footer.php');
}

ob_start();
?>
<form method="post" class="admin-form">
    <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">
    <div class="admin-card">
        <h2>Footer CTA</h2>
        <div class="form-row">
            <label for="cta_title">หัวข้อ</label>
            <input type="text" id="cta_title" name="cta_title" value="<?= admin_h($data['cta_title'] ?? '') ?>">
        </div>
        <div class="form-row">
            <label for="cta_desc">คำอธิบาย</label>
            <textarea id="cta_desc" name="cta_desc"><?= admin_h($data['cta_desc'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <label for="cta_button">ปุ่ม</label>
            <input type="text" id="cta_button" name="cta_button" value="<?= admin_h($data['cta_button'] ?? '') ?>">
        </div>
    </div>
    <div class="admin-card">
        <h2>ลิขสิทธิ์ & Cookie</h2>
        <div class="form-row">
            <label for="copyright">Copyright</label>
            <input type="text" id="copyright" name="copyright" value="<?= admin_h($data['copyright'] ?? '') ?>">
        </div>
        <div class="form-row">
            <label for="disclaimer">ข้อความ disclaimer</label>
            <textarea id="disclaimer" name="disclaimer"><?= admin_h($data['disclaimer'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <label for="cookie_text">ข้อความ Cookie banner</label>
            <textarea id="cookie_text" name="cookie_text"><?= admin_h($data['cookie_text'] ?? '') ?></textarea>
        </div>
        <div class="form-grid-2">
            <div class="form-row">
                <label for="cookie_accept">ปุ่มยอมรับ Cookie</label>
                <input type="text" id="cookie_accept" name="cookie_accept" value="<?= admin_h($data['cookie_accept'] ?? '') ?>">
            </div>
            <div class="form-row">
                <label for="privacy_url">ลิงก์นโยบายความเป็นส่วนตัว</label>
                <input type="text" id="privacy_url" name="privacy_url" value="<?= admin_h($data['privacy_url'] ?? '') ?>">
            </div>
            <div class="form-row">
                <label for="terms_url">ลิงก์ข้อกำหนด</label>
                <input type="text" id="terms_url" name="terms_url" value="<?= admin_h($data['terms_url'] ?? '') ?>">
            </div>
        </div>
    </div>
    <div class="admin-actions">
        <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
    </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
