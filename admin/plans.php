<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();
require_once dirname(__DIR__) . '/includes/plan-helpers.php';

$page_title = 'รายละเอียดแผน';
$active_file = 'plans.php';
$details = plan_details_all();
$slugs = array_keys($details);
sort($slugs, SORT_NATURAL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    if (admin_post_string('action') === 'delete') {
        $slug = admin_post_string('slug');
        if ($slug !== '' && isset($details[$slug])) {
            $path = cms_file_path('plans/' . $slug);
            if (is_file($path)) {
                unlink($path);
            }
            admin_flash('success', 'ลบการแก้ไข CMS แล้ว');
        }
        admin_redirect('plans.php');
    }
}

$q = trim((string) ($_GET['q'] ?? ''));
if ($q !== '') {
    $slugs = array_values(array_filter($slugs, fn ($s) => stripos($s, $q) !== false || stripos($details[$s]['title'] ?? '', $q) !== false));
}

ob_start();
?>
<div class="admin-card">
    <form method="get" class="admin-form" style="display:flex;gap:.75rem;align-items:flex-end;margin-bottom:1rem">
        <div class="form-row" style="flex:1;margin:0">
            <label for="q">ค้นหา</label>
            <input type="search" id="q" name="q" value="<?= admin_h($q) ?>">
        </div>
        <button type="submit" class="admin-btn admin-btn--outline">ค้นหา</button>
    </form>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>แผน</th><th>Tagline</th><th>CMS</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($slugs as $slug):
                $plan = $details[$slug];
                $hasCms = is_readable(cms_file_path('plans/' . $slug));
            ?>
            <tr>
                <td><strong><?= admin_h($plan['title'] ?? $slug) ?></strong><br><code><?= admin_h($slug) ?></code></td>
                <td style="max-width:280px;font-size:.85rem"><?= admin_h(mb_strimwidth($plan['tagline'] ?? '', 0, 80, '…')) ?></td>
                <td><?= $hasCms ? '<span class="admin-badge">แก้ไขแล้ว</span>' : '—' ?></td>
                <td class="admin-table-actions">
                    <a href="<?= admin_h(admin_url('plan-edit.php?slug=' . urlencode($slug))) ?>" class="admin-btn admin-btn--outline admin-btn--sm">แก้ไข</a>
                    <?php if ($hasCms): ?>
                    <?php admin_inline_post_form(
                        ['action' => 'delete', 'slug' => $slug],
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
