<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();
require_once dirname(__DIR__) . '/includes/plan-helpers.php';

$page_title = 'แคตตาล็อกแผนประกัน';
$active_file = 'catalog.php';
$catalog = plan_catalog();
$overrides = cms_load('catalog', []);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    if (admin_post_string('action') === 'reset') {
        $slug = admin_post_string('slug');
        if ($slug !== '' && isset($overrides[$slug])) {
            unset($overrides[$slug]);
            cms_save('catalog', $overrides);
            admin_flash('success', 'รีเซ็ตแคตตาล็อกเป็นค่าเริ่มต้นแล้ว');
        }
        admin_redirect('catalog.php');
    }
}

$q = trim((string) ($_GET['q'] ?? ''));
if ($q !== '') {
    $catalog = array_values(array_filter($catalog, function ($p) use ($q) {
        $hay = ($p['title'] ?? '') . ' ' . ($p['slug'] ?? '') . ' ' . ($p['desc'] ?? '');
        return stripos($hay, $q) !== false;
    }));
}

ob_start();
?>
<div class="admin-card">
    <form method="get" class="admin-form admin-toolbar">
        <div class="form-row">
            <label for="q">ค้นหาแผน</label>
            <input type="search" id="q" name="q" value="<?= admin_h($q) ?>" placeholder="ชื่อหรือ slug...">
        </div>
        <button type="submit" class="admin-btn admin-btn--outline">ค้นหา</button>
    </form>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>แผน</th><th>หมวด</th><th>ส่วนลด</th><th>แก้ไข CMS</th></tr>
            </thead>
            <tbody>
            <?php foreach ($catalog as $p):
                $slug = $p['slug'] ?? '';
                $hasOverride = !empty($overrides[$slug]);
            ?>
            <tr>
                <td>
                    <strong><?= admin_h($p['title'] ?? '') ?></strong><br>
                    <code style="font-size:.8rem;color:var(--admin-muted)"><?= admin_h($slug) ?></code>
                </td>
                <td><?= admin_h($p['category_label'] ?? $p['category'] ?? '') ?></td>
                <td><?= admin_h($p['discount'] ?? '—') ?></td>
                <td class="admin-table-actions">
                    <a href="<?= admin_h(admin_url('catalog-edit.php?slug=' . urlencode($slug))) ?>" class="admin-btn admin-btn--outline admin-btn--sm">
                        แก้ไข<?= $hasOverride ? ' ●' : '' ?>
                    </a>
                    <?php if ($hasOverride): ?>
                    <?php admin_inline_post_form(
                        ['action' => 'reset', 'slug' => $slug],
                        'รีเซ็ต',
                        'ลบการแก้ไข CMS ของแผนนี้และใช้ค่าเริ่มต้น?',
                        'admin-btn admin-btn--outline admin-btn--sm'
                    ); ?>
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
