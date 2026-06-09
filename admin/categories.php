<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();
require_once dirname(__DIR__) . '/includes/plan-helpers.php';

$page_title = 'หมวดหมู่แผน';
$active_file = 'categories.php';
$data = cms_load('categories', require dirname(__DIR__) . '/includes/plan-categories.php');

$q = trim((string) ($_GET['q'] ?? ''));
$categories = $data;
if ($q !== '') {
    $categories = array_filter($categories, function ($cat, $id) use ($q) {
        $hay = $id . ' ' . ($cat['title'] ?? '') . ' ' . ($cat['lead'] ?? '') . ' ' . ($cat['page'] ?? '') . ' ' . ($cat['mega_desc'] ?? '');
        return stripos($hay, $q) !== false;
    }, ARRAY_FILTER_USE_BOTH);
}

ob_start();
?>
<div class="admin-card">
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
                    <th>แก้ไข CMS</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $id => $cat):
                $planCount = count(plans_by_category($id));
            ?>
            <tr>
                <td>
                    <strong><?= admin_h($cat['title'] ?? '') ?></strong><br>
                    <code><?= admin_h($id) ?></code>
                </td>
                <td><?= admin_h($cat['page'] ?? '—') ?></td>
                <td><?= (int) $planCount ?></td>
                <td>
                    <a href="<?= admin_h(admin_url('category-edit.php?id=' . urlencode($id))) ?>" class="admin-btn admin-btn--outline admin-btn--sm">แก้ไข</a>
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
