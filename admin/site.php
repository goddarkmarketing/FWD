<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();

$page_title = 'ข้อมูลเว็บไซต์';
$active_file = 'site.php';
$data = cms_load('site', []);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $fields = [
        'site_name', 'site_tagline', 'site_logo_path', 'site_phone',
        'contact_email', 'contact_phone_1', 'contact_phone_2', 'contact_phone_2_raw',
        'contact_facebook', 'contact_facebook_name', 'contact_line',
        'agent_office_name', 'agent_license_no', 'hero_alt',
    ];
    foreach ($fields as $f) {
        $data[$f] = admin_post_string($f);
    }
    cms_save('site', $data);
    admin_flash('success', 'บันทึกข้อมูลเว็บไซต์เรียบร้อย');
    admin_redirect('site.php');
}

ob_start();
?>
<form method="post" class="admin-form">
    <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">

    <div class="admin-card">
        <h2>แบรนด์</h2>
        <div class="form-grid-2">
            <div class="form-row">
                <label for="site_name">ชื่อเว็บไซต์</label>
                <input type="text" id="site_name" name="site_name" value="<?= admin_h($data['site_name'] ?? '') ?>">
            </div>
            <div class="form-row">
                <label for="site_tagline">Tagline</label>
                <input type="text" id="site_tagline" name="site_tagline" value="<?= admin_h($data['site_tagline'] ?? '') ?>">
            </div>
        </div>
        <?php admin_image_field('site_logo_path', 'โลโก้', $data['site_logo_path'] ?? '', null, ['id' => 'site_logo_path', 'size' => 'logo', 'subdir' => 'images', 'hide_path' => true]); ?>
        <div class="form-row">
            <label for="hero_alt">ข้อความ alt รูป Hero</label>
            <input type="text" id="hero_alt" name="hero_alt" value="<?= admin_h($data['hero_alt'] ?? '') ?>">
        </div>
    </div>

    <div class="admin-card">
        <h2>ติดต่อ</h2>
        <div class="form-grid-2">
            <div class="form-row">
                <label for="site_phone">สายด่วน FWD</label>
                <input type="text" id="site_phone" name="site_phone" value="<?= admin_h($data['site_phone'] ?? '') ?>">
            </div>
            <div class="form-row">
                <label for="contact_email">อีเมล</label>
                <input type="email" id="contact_email" name="contact_email" value="<?= admin_h($data['contact_email'] ?? '') ?>">
            </div>
            <div class="form-row">
                <label for="contact_phone_1">โทรศัพท์ 1</label>
                <input type="text" id="contact_phone_1" name="contact_phone_1" value="<?= admin_h($data['contact_phone_1'] ?? '') ?>">
            </div>
            <div class="form-row">
                <label for="contact_phone_2">โทรศัพท์ 2</label>
                <input type="text" id="contact_phone_2" name="contact_phone_2" value="<?= admin_h($data['contact_phone_2'] ?? '') ?>">
            </div>
            <div class="form-row">
                <label for="contact_phone_2_raw">โทรศัพท์ 2 (ตัวเลข)</label>
                <input type="text" id="contact_phone_2_raw" name="contact_phone_2_raw" value="<?= admin_h($data['contact_phone_2_raw'] ?? '') ?>">
            </div>
            <div class="form-row">
                <label for="contact_facebook_name">ชื่อ Facebook</label>
                <input type="text" id="contact_facebook_name" name="contact_facebook_name" value="<?= admin_h($data['contact_facebook_name'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <label for="contact_facebook">ลิงก์ Facebook</label>
            <input type="url" id="contact_facebook" name="contact_facebook" value="<?= admin_h($data['contact_facebook'] ?? '') ?>">
        </div>
        <div class="form-row">
            <label for="contact_line">ลิงก์ LINE</label>
            <input type="url" id="contact_line" name="contact_line" value="<?= admin_h($data['contact_line'] ?? '') ?>">
        </div>
    </div>

    <div class="admin-card">
        <h2>ตัวแทน / ใบอนุญาต</h2>
        <div class="form-row">
            <label for="agent_office_name">ชื่อสำนักงาน</label>
            <input type="text" id="agent_office_name" name="agent_office_name" value="<?= admin_h($data['agent_office_name'] ?? '') ?>">
        </div>
        <div class="form-row">
            <label for="agent_license_no">เลขใบอนุญาต</label>
            <input type="text" id="agent_license_no" name="agent_license_no" value="<?= admin_h($data['agent_license_no'] ?? '') ?>">
        </div>
    </div>

    <div class="admin-actions">
        <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
    </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
