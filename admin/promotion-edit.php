<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();

$page_title = 'แก้ไขโปรโมชัน';
$active_file = 'promotions.php';
$store = cms_load('promotions', ['items' => [], 'home_count' => 2]);
$items = $store['items'] ?? [];
$isNew = isset($_GET['new']);
$idx = isset($_GET['idx']) ? (int) $_GET['idx'] : -1;

if (!$isNew && ($idx < 0 || !isset($items[$idx]))) {
    admin_flash('error', 'ไม่พบโปรโมชัน');
    admin_redirect('promotions.php');
}

$edit = $isNew ? null : $items[$idx];
$page_title = $isNew ? 'เพิ่มโปรโมชัน' : ('แก้ไขโปรโมชัน: ' . ($edit['title'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $action = admin_post_string('action');

    if ($action === 'delete' && !$isNew && isset($items[$idx])) {
        array_splice($items, $idx, 1);
        cms_save('promotions', ['items' => $items, 'home_count' => (int) ($store['home_count'] ?? 2)]);
        admin_flash('success', 'ลบโปรโมชันแล้ว');
        admin_redirect('promotions.php');
    }

    $promo = [
        'badge' => admin_post_string('badge'),
        'badge_variant' => admin_post_string('badge_variant', 'orange'),
        'date' => admin_post_string('date'),
        'title' => admin_post_string('title'),
        'desc' => admin_post_string('desc'),
        'url' => admin_post_string('url'),
        'cta' => admin_post_string('cta'),
    ];

    if ($promo['title'] === '') {
        admin_flash('error', 'กรุณากรอกหัวข้อโปรโมชัน');
        admin_redirect($isNew ? 'promotion-edit.php?new=1' : ('promotion-edit.php?idx=' . $idx));
    }

    if ($isNew) {
        $items[] = $promo;
    } else {
        $items[$idx] = $promo;
    }

    cms_save('promotions', ['items' => $items, 'home_count' => (int) ($store['home_count'] ?? 2)]);
    admin_flash('success', 'บันทึกโปรโมชันเรียบร้อย');
    admin_redirect('promotions.php');
}

ob_start();
?>
<form method="post" class="admin-form">
    <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">
    <div class="admin-card">
        <div class="form-grid-2">
            <div class="form-row">
                <label for="badge">Badge</label>
                <input type="text" id="badge" name="badge" value="<?= admin_h($edit['badge'] ?? '') ?>" placeholder="เช่น แนะนำ">
            </div>
            <div class="form-row">
                <label for="badge_variant">สี Badge</label>
                <select id="badge_variant" name="badge_variant">
                    <?php foreach (['orange' => 'ส้ม', 'aqua' => 'ฟ้า', 'green' => 'เขียว'] as $v => $label): ?>
                    <option value="<?= $v ?>"<?= ($edit['badge_variant'] ?? 'orange') === $v ? ' selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-row">
                <label for="date">วันที่ / ระยะเวลา</label>
                <input type="text" id="date" name="date" value="<?= admin_h($edit['date'] ?? '') ?>" placeholder="เช่น ถึง 30 ก.ย. 2026">
            </div>
            <div class="form-row">
                <label for="cta">ข้อความปุ่ม CTA</label>
                <input type="text" id="cta" name="cta" value="<?= admin_h($edit['cta'] ?? 'ดูผลิตภัณฑ์') ?>">
            </div>
        </div>
        <div class="form-row">
            <label for="title">หัวข้อ <span class="required">*</span></label>
            <input type="text" id="title" name="title" value="<?= admin_h($edit['title'] ?? '') ?>" required>
        </div>
        <div class="form-row">
            <label for="desc">คำอธิบาย</label>
            <textarea id="desc" name="desc" class="tall"><?= admin_h($edit['desc'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <label for="url">ลิงก์</label>
            <input type="text" id="url" name="url" value="<?= admin_h($edit['url'] ?? '') ?>" placeholder="plan.php?slug=precious-care">
            <p class="form-hint">ใส่ path ในเว็บ เช่น <code>plan.php?slug=...</code> หรือ <code>contact.php</code></p>
        </div>
    </div>
    <div class="admin-actions">
        <a href="<?= admin_h(admin_url('promotions.php')) ?>" class="admin-btn admin-btn--outline">กลับ</a>
        <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
        <?php if (!$isNew): ?>
        <button type="submit" name="action" value="delete" class="admin-btn admin-btn--danger" onclick="return confirm('ลบโปรโมชันนี้?')">ลบโปรโมชัน</button>
        <?php endif; ?>
    </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
