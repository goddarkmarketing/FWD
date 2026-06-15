<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();
require_once dirname(__DIR__) . '/includes/plan-helpers.php';
require_once dirname(__DIR__) . '/includes/cms-plan-meta.php';

$page_title = 'หมวดหมู่แผน';
$active_file = 'categories.php';
$defaults = plan_category_defaults();
$data = cms_load('categories', $defaults);
$meta = cms_categories_meta();
$hidden = $meta['hidden'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $action = admin_post_string('action');
    $id = admin_post_string('id');

    if ($action === 'hide' && $id !== '' && $id !== 'all') {
        cms_category_hide($id);
        admin_flash('success', 'ซ่อนหมวดหมู่แล้ว — ไม่แสดงบนเว็บไซต์');
        admin_redirect('categories.php');
    }

    if ($action === 'unhide' && $id !== '') {
        cms_category_unhide($id);
        admin_flash('success', 'นำหมวดหมู่กลับมาแสดงแล้ว');
        admin_redirect('categories.php');
    }

    if ($action === 'delete' && $id !== '' && $id !== 'all') {
        cms_category_delete($id);
        admin_flash('success', plan_category_is_builtin($id) ? 'ซ่อนหมวดหมู่แล้ว' : 'ลบหมวดหมู่แล้ว');
        admin_redirect('categories.php');
    }
}

$allIds = array_values(array_unique(array_merge(array_keys($defaults), array_keys($data))));
sort($allIds, SORT_NATURAL);

$q = trim((string) ($_GET['q'] ?? ''));
if ($q !== '') {
    $allIds = array_values(array_filter($allIds, function (string $id) use ($q, $data, $defaults): bool {
        $cat = $data[$id] ?? $defaults[$id] ?? [];
        $hay = $id . ' ' . ($cat['title'] ?? '') . ' ' . ($cat['lead'] ?? '') . ' ' . ($cat['page'] ?? '');
        return stripos($hay, $q) !== false;
    }));
}

ob_start();
?>
<div class="admin-card">
    <div class="admin-card__head">
        <h2 class="admin-card__title">หมวดหมู่แผนประกัน</h2>
        <a href="<?= admin_h(admin_url('category-add.php')) ?>" class="admin-btn admin-btn--primary admin-btn--sm">+ เพิ่มหมวดหมู่</a>
    </div>
    <form method="get" class="admin-form admin-toolbar">
        <div class="form-row">
            <label for="q">ค้นหาหมวดหมู่</label>
            <input type="search" id="q" name="q" value="<?= admin_h($q) ?>" placeholder="ชื่อ หรือ slug...">
        </div>
        <button type="submit" class="admin-btn admin-btn--outline">ค้นหา</button>
    </form>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>หมวดหมู่</th>
                    <th>หน้าเว็บ</th>
                    <th>จำนวนแผน</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($allIds as $id):
                $cat = $data[$id] ?? $defaults[$id] ?? [];
                $isHidden = in_array($id, $hidden, true);
                $planCount = $isHidden ? 0 : count(plans_by_category($id));
                $isBuiltin = plan_category_is_builtin($id);
            ?>
            <tr>
                <td>
                    <strong><?= admin_h($cat['title'] ?? '') ?></strong><br>
                    <code><?= admin_h($id) ?></code>
                    <?php if (!$isBuiltin): ?><span class="admin-badge">กำหนดเอง</span><?php endif; ?>
                </td>
                <td><?= admin_h($cat['page'] ?? '—') ?></td>
                <td><?= (int) $planCount ?></td>
                <td>
                    <?php if ($isHidden): ?>
                    <span class="admin-badge admin-badge--muted">ซ่อนอยู่</span>
                    <?php else: ?>
                    <span class="admin-badge admin-badge--live">แสดง</span>
                    <?php endif; ?>
                </td>
                <td class="admin-table-actions">
                    <?php if (!$isHidden): ?>
                    <a href="<?= admin_h(admin_url('category-edit.php?id=' . urlencode($id))) ?>" class="admin-btn admin-btn--outline admin-btn--sm">แก้ไข</a>
                    <?php endif; ?>
                    <?php if ($id !== 'all'): ?>
                        <?php if ($isHidden): ?>
                        <?php admin_inline_post_form(
                            ['action' => 'unhide', 'id' => $id],
                            'แสดงอีกครั้ง',
                            'นำหมวดหมู่นี้กลับมาแสดงบนเว็บไซต์?',
                            'admin-btn admin-btn--outline admin-btn--sm'
                        ); ?>
                        <?php else: ?>
                        <?php admin_inline_post_form(
                            ['action' => 'delete', 'id' => $id],
                            'ลบ',
                            $isBuiltin
                                ? 'ลบหมวดหมู่นี้จากเว็บไซต์? (กู้คืนได้จากปุ่ม แสดงอีกครั้ง)'
                                : 'ลบหมวดหมู่นี้ถาวร?',
                            'admin-btn admin-btn--danger admin-btn--sm'
                        ); ?>
                        <?php endif; ?>
                    <?php endif; ?>
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
