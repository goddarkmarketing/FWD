<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();
require_once dirname(__DIR__) . '/includes/plan-helpers.php';
require_once dirname(__DIR__) . '/includes/cms-plan-meta.php';

$page_title = 'รายละเอียดแผน';
$active_file = 'plans.php';
$details = plan_details_all();
$meta = cms_plans_meta();
$catalogMeta = cms_catalog_meta();
$hiddenPlans = $meta['hidden'] ?? [];
$hiddenCatalog = $catalogMeta['hidden'] ?? [];

$allSlugs = array_values(array_unique(array_merge(
    array_keys($details),
    plan_catalog_builtin_slugs(),
    array_keys($catalogMeta['custom'] ?? []),
    $hiddenPlans,
    $hiddenCatalog
)));
sort($allSlugs, SORT_NATURAL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $action = admin_post_string('action');
    $slug = admin_post_string('slug');

    if ($action === 'reset_cms' && $slug !== '' && isset($details[$slug])) {
        $path = cms_file_path('plans/' . $slug);
        if (is_file($path)) {
            unlink($path);
        }
        admin_flash('success', 'รีเซ็ตการแก้ไข CMS แล้ว');
        admin_redirect('plans.php');
    }

    if ($action === 'unhide' && $slug !== '') {
        cms_catalog_unhide($slug);
        admin_flash('success', 'นำหน้ารายละเอียดแผนกลับมาแสดงแล้ว');
        admin_redirect('plans.php');
    }

    if ($action === 'delete' && $slug !== '') {
        $isBuiltin = plan_detail_is_builtin($slug);
        cms_plan_detail_delete($slug);
        admin_flash('success', $isBuiltin ? 'ลบหน้ารายละเอียดแผนแล้ว (การ์ดในแคตตาล็อกถูกซ่อนด้วย)' : 'ลบแผนแล้ว');
        admin_redirect('plans.php');
    }
}

$q = trim((string) ($_GET['q'] ?? ''));
if ($q !== '') {
    $allSlugs = array_values(array_filter($allSlugs, function (string $slug) use ($q, $details): bool {
        $plan = $details[$slug] ?? [];
        $hay = $slug . ' ' . ($plan['title'] ?? '') . ' ' . ($plan['tagline'] ?? '');

        return stripos($hay, $q) !== false;
    }));
}

ob_start();
?>
<div class="admin-card">
    <div class="admin-card__head">
        <h2 class="admin-card__title">รายละเอียดแผนประกัน</h2>
    </div>
    <p class="admin-card__lead" style="margin-top:0">
        หน้า <code>plan.php?slug=...</code> · ปุ่ม <strong>ลบ</strong> จะซ่อนทั้งหน้ารายละเอียดและการ์ดในแคตตาล็อก (กู้คืนได้)
    </p>
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
                <tr>
                    <th>แผน</th>
                    <th>Tagline</th>
                    <th>CMS</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($allSlugs as $slug):
                $plan = $details[$slug] ?? null;
                $isHidden = plan_detail_is_hidden($slug);
                $hasCms = is_readable(cms_file_path('plans/' . $slug));
                $isBuiltin = plan_detail_is_builtin($slug);
            ?>
            <tr>
                <td>
                    <strong><?= admin_h($plan['title'] ?? $slug) ?></strong><br>
                    <code><?= admin_h($slug) ?></code>
                </td>
                <td style="max-width:280px;font-size:.85rem"><?= admin_h(mb_strimwidth($plan['tagline'] ?? '—', 0, 80, '…')) ?></td>
                <td><?= $hasCms ? '<span class="admin-badge">แก้ไขแล้ว</span>' : '—' ?></td>
                <td>
                    <?php if ($isHidden): ?>
                    <span class="admin-badge admin-badge--muted">ซ่อนอยู่</span>
                    <?php else: ?>
                    <span class="admin-badge admin-badge--live">แสดง</span>
                    <?php endif; ?>
                </td>
                <td class="admin-table-actions">
                    <?php if (!$isHidden && $plan !== null): ?>
                    <a href="<?= admin_h(admin_url('plan-edit.php?slug=' . urlencode($slug))) ?>" class="admin-btn admin-btn--outline admin-btn--sm">แก้ไข</a>
                    <?php endif; ?>
                    <?php if ($hasCms && !$isHidden): ?>
                    <?php admin_inline_post_form(
                        ['action' => 'reset_cms', 'slug' => $slug],
                        'รีเซ็ต CMS',
                        'ลบการแก้ไข CMS ของแผนนี้และใช้ค่าเริ่มต้น?',
                        'admin-btn admin-btn--outline admin-btn--sm'
                    ); ?>
                    <?php endif; ?>
                    <?php if ($isHidden): ?>
                    <?php admin_inline_post_form(
                        ['action' => 'unhide', 'slug' => $slug],
                        'แสดงอีกครั้ง',
                        'นำหน้ารายละเอียดแผนและการ์ดแคตตาล็อกกลับมาแสดง?',
                        'admin-btn admin-btn--outline admin-btn--sm'
                    ); ?>
                    <?php else: ?>
                    <?php admin_inline_post_form(
                        ['action' => 'delete', 'slug' => $slug],
                        'ลบ',
                        $isBuiltin
                            ? 'ลบหน้ารายละเอียดแผนและซ่อนการ์ดแคตตาล็อก? (กู้คืนได้จากปุ่ม แสดงอีกครั้ง)'
                            : 'ลบแผนนี้ถาวร?',
                        'admin-btn admin-btn--danger admin-btn--sm'
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
