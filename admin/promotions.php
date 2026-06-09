<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();

$page_title = 'โปรโมชัน';
$active_file = 'promotions.php';
$store = cms_load('promotions', ['items' => [], 'home_count' => 2]);
$items = $store['items'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $action = admin_post_string('action');

    if ($action === 'delete') {
        $idx = admin_post_int('idx', -1);
        if (isset($items[$idx])) {
            array_splice($items, $idx, 1);
            cms_save('promotions', ['items' => $items, 'home_count' => (int) ($store['home_count'] ?? 2)]);
            admin_flash('success', 'ลบโปรโมชันแล้ว');
        }
        admin_redirect('promotions.php');
    }

    if ($action === 'save_settings') {
        cms_save('promotions', ['items' => $items, 'home_count' => admin_post_int('home_count', 2)]);
        admin_flash('success', 'บันทึกการตั้งค่าแล้ว');
        admin_redirect('promotions.php');
    }

    if ($action === 'save') {
        $idx = admin_post_int('idx', -1);
        $promo = [
            'badge' => admin_post_string('badge'),
            'badge_variant' => admin_post_string('badge_variant', 'orange'),
            'date' => admin_post_string('date'),
            'title' => admin_post_string('title'),
            'desc' => admin_post_string('desc'),
            'url' => admin_post_string('url'),
            'cta' => admin_post_string('cta'),
        ];
        if ($idx >= 0 && isset($items[$idx])) {
            $items[$idx] = $promo;
        } else {
            $items[] = $promo;
        }
        cms_save('promotions', ['items' => $items, 'home_count' => (int) ($store['home_count'] ?? 2)]);
        admin_flash('success', 'บันทึกโปรโมชันเรียบร้อย');
        admin_redirect('promotions.php');
    }
}

$editIdx = isset($_GET['edit']) ? (int) $_GET['edit'] : -1;
$edit = ($editIdx >= 0 && isset($items[$editIdx])) ? $items[$editIdx] : null;
$isNew = isset($_GET['new']);

ob_start();
?>
<form method="post" class="admin-form admin-card" style="margin-bottom:1rem">
    <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">
    <input type="hidden" name="action" value="save_settings">
    <div class="form-row" style="max-width:200px">
        <label>จำนวนโปรโมชันบนหน้าแรก</label>
        <input type="number" name="home_count" min="1" max="10" value="<?= (int) ($store['home_count'] ?? 2) ?>">
    </div>
    <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm">บันทึกการตั้งค่า</button>
</form>

<div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
        <h2 style="margin:0">รายการโปรโมชัน</h2>
        <a href="?new=1" class="admin-btn admin-btn--primary admin-btn--sm">+ เพิ่มโปรโมชัน</a>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Badge</th><th>หัวข้อ</th><th>วันที่</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($items as $i => $p): ?>
            <tr>
                <td><span class="admin-badge"><?= admin_h($p['badge'] ?? '') ?></span></td>
                <td><?= admin_h($p['title'] ?? '') ?></td>
                <td><?= admin_h($p['date'] ?? '') ?></td>
                <td class="admin-table-actions">
                    <a href="?edit=<?= $i ?>" class="admin-btn admin-btn--outline admin-btn--sm">แก้ไข</a>
                    <?php admin_inline_post_form(
                        ['action' => 'delete', 'idx' => (string) $i],
                        'ลบ',
                        'ลบโปรโมชันนี้?'
                    ); ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($edit || $isNew): ?>
<form method="post" class="admin-form admin-card">
    <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="idx" value="<?= $isNew ? -1 : $editIdx ?>">
    <h2><?= $isNew ? 'เพิ่มโปรโมชัน' : 'แก้ไขโปรโมชัน' ?></h2>
    <div class="form-grid-2">
        <div class="form-row"><label>Badge</label><input type="text" name="badge" value="<?= admin_h($edit['badge'] ?? '') ?>"></div>
        <div class="form-row"><label>Badge สี</label>
            <select name="badge_variant">
                <?php foreach (['orange', 'aqua', 'green'] as $v): ?>
                <option value="<?= $v ?>" <?= ($edit['badge_variant'] ?? '') === $v ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row"><label>วันที่</label><input type="text" name="date" value="<?= admin_h($edit['date'] ?? '') ?>"></div>
        <div class="form-row"><label>ปุ่ม CTA</label><input type="text" name="cta" value="<?= admin_h($edit['cta'] ?? 'ดูผลิตภัณฑ์') ?>"></div>
    </div>
    <div class="form-row"><label>หัวข้อ</label><input type="text" name="title" value="<?= admin_h($edit['title'] ?? '') ?>" required></div>
    <div class="form-row"><label>คำอธิบาย</label><textarea name="desc"><?= admin_h($edit['desc'] ?? '') ?></textarea></div>
    <div class="form-row"><label>ลิงก์ (plan.php?slug=...)</label><input type="text" name="url" value="<?= admin_h($edit['url'] ?? '') ?>"></div>
    <div class="admin-actions">
        <a href="<?= admin_h(admin_url('promotions.php')) ?>" class="admin-btn admin-btn--outline">กลับ</a>
        <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
    </div>
</form>
<?php if ($edit): ?>
<div class="admin-actions" style="margin-top:0;padding-top:0;border-top:0">
    <?php admin_inline_post_form(
        ['action' => 'delete', 'idx' => (string) $editIdx],
        'ลบโปรโมชัน',
        'ลบโปรโมชันนี้?',
        'admin-btn admin-btn--danger'
    ); ?>
</div>
<?php endif; ?>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
