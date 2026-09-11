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
}

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
    <div class="admin-card__head">
        <h2 class="admin-card__title">รายการโปรโมชัน</h2>
        <a href="<?= admin_h(admin_url('promotion-edit.php?new=1')) ?>" class="admin-btn admin-btn--primary admin-btn--sm">+ เพิ่มโปรโมชัน</a>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Badge</th>
                    <th>หัวข้อ</th>
                    <th>วันที่</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($items === []): ?>
            <tr>
                <td colspan="4" style="color:var(--admin-muted)">ยังไม่มีโปรโมชัน — กด “+ เพิ่มโปรโมชัน” เพื่อสร้างรายการแรก</td>
            </tr>
            <?php endif; ?>
            <?php foreach ($items as $i => $p): ?>
            <tr>
                <td><span class="admin-badge"><?= admin_h($p['badge'] ?? '') ?></span></td>
                <td><?= admin_h($p['title'] ?? '') ?></td>
                <td><?= admin_h($p['date'] ?? '') ?></td>
                <td class="admin-table-actions">
                    <a href="<?= admin_h(admin_url('promotion-edit.php?idx=' . $i)) ?>" class="admin-btn admin-btn--outline admin-btn--sm">แก้ไข</a>
                    <?php admin_inline_post_form(
                        ['action' => 'delete', 'idx' => (string) $i],
                        'ลบ',
                        'ลบโปรโมชันนี้?',
                        'admin-btn admin-btn--danger admin-btn--sm'
                    ); ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
