<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();
require_once dirname(__DIR__) . '/includes/plan-helpers.php';
require_once dirname(__DIR__) . '/includes/cms-plan-meta.php';

$page_title = 'แคตตาล็อกแผนประกัน';
$active_file = 'catalog.php';
$overrides = cms_load('catalog', []);
$meta = cms_catalog_meta();
$hidden = $meta['hidden'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $action = admin_post_string('action');
    $slug = admin_post_string('slug');

    if ($action === 'reset' && $slug !== '') {
        if (isset($overrides[$slug])) {
            unset($overrides[$slug]);
            cms_save('catalog', $overrides);
            admin_flash('success', 'รีเซ็ตแคตตาล็อกเป็นค่าเริ่มต้นแล้ว');
        }
        admin_redirect('catalog.php');
    }

    if ($action === 'unhide' && $slug !== '') {
        cms_catalog_unhide($slug);
        admin_flash('success', 'นำแผนกลับมาแสดงแล้ว');
        admin_redirect('catalog.php');
    }

    if ($action === 'delete' && $slug !== '') {
        $isBuiltin = plan_catalog_is_builtin($slug);
        cms_catalog_delete($slug);
        admin_flash('success', $isBuiltin ? 'ซ่อนแผนแล้ว' : 'ลบแผนแล้ว');
        admin_redirect('catalog.php');
    }
}

$catalog = plan_catalog();
$allSlugs = array_values(array_unique(array_merge(
    array_map(static fn(array $p): string => (string) ($p['slug'] ?? ''), $catalog),
    plan_catalog_builtin_slugs(),
    array_keys($meta['custom'] ?? []),
    $hidden
)));
$allSlugs = array_values(array_filter($allSlugs));

$catalogBySlug = [];
foreach ($catalog as $p) {
    if (!empty($p['slug'])) {
        $catalogBySlug[$p['slug']] = $p;
    }
}

$q = trim((string) ($_GET['q'] ?? ''));
if ($q !== '') {
    $allSlugs = array_values(array_filter($allSlugs, function (string $slug) use ($q, $catalogBySlug, $meta, $overrides): bool {
        $p = $catalogBySlug[$slug] ?? ($meta['custom'][$slug] ?? []);
        $hay = $slug . ' ' . ($p['title'] ?? '') . ' ' . ($p['desc'] ?? '') . ' ' . ($overrides[$slug]['title'] ?? '');
        return stripos($hay, $q) !== false;
    }));
}

ob_start();
?>
<div class="admin-card">
    <div class="admin-card__head">
        <h2 class="admin-card__title">แคตตาล็อกแผนประกัน</h2>
        <a href="<?= admin_h(admin_url('catalog-add.php')) ?>" class="admin-btn admin-btn--primary admin-btn--sm">+ เพิ่มแผน</a>
    </div>
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
                <tr><th>แผน</th><th>หมวด</th><th>ส่วนลด</th><th>สถานะ</th><th>จัดการ</th></tr>
            </thead>
            <tbody>
            <?php foreach ($allSlugs as $slug):
                $p = $catalogBySlug[$slug] ?? ($meta['custom'][$slug] ?? null);
                $isHidden = in_array($slug, $hidden, true);
                $hasOverride = !empty($overrides[$slug]);
                $isBuiltin = plan_catalog_is_builtin($slug);
                $isCustom = isset($meta['custom'][$slug]);
            ?>
            <tr>
                <td>
                    <strong><?= admin_h($p['title'] ?? $overrides[$slug]['title'] ?? $slug) ?></strong><br>
                    <code style="font-size:.8rem;color:var(--admin-muted)"><?= admin_h($slug) ?></code>
                    <?php if ($isCustom): ?><span class="admin-badge">กำหนดเอง</span><?php endif; ?>
                </td>
                <td><?= admin_h($p['category_label'] ?? $p['category'] ?? '—') ?></td>
                <td><?= admin_h($p['discount'] ?? $overrides[$slug]['discount'] ?? '—') ?></td>
                <td>
                    <?php if ($isHidden): ?>
                    <span class="admin-badge admin-badge--muted">ซ่อนอยู่</span>
                    <?php else: ?>
                    <span class="admin-badge admin-badge--live">แสดง</span>
                    <?php endif; ?>
                </td>
                <td class="admin-table-actions">
                    <?php if (!$isHidden && $p !== null): ?>
                    <a href="<?= admin_h(admin_url('catalog-edit.php?slug=' . urlencode($slug))) ?>" class="admin-btn admin-btn--outline admin-btn--sm">
                        แก้ไข<?= $hasOverride ? ' ●' : '' ?>
                    </a>
                    <?php endif; ?>
                    <?php if ($hasOverride && !$isHidden): ?>
                    <?php admin_inline_post_form(
                        ['action' => 'reset', 'slug' => $slug],
                        'รีเซ็ต',
                        'ลบการแก้ไข CMS ของแผนนี้และใช้ค่าเริ่มต้น?',
                        'admin-btn admin-btn--outline admin-btn--sm'
                    ); ?>
                    <?php endif; ?>
                    <?php if ($isHidden): ?>
                    <?php admin_inline_post_form(
                        ['action' => 'unhide', 'slug' => $slug],
                        'แสดงอีกครั้ง',
                        'นำแผนนี้กลับมาแสดงบนเว็บไซต์?',
                        'admin-btn admin-btn--outline admin-btn--sm'
                    ); ?>
                    <?php else: ?>
                    <?php admin_inline_post_form(
                        ['action' => 'delete', 'slug' => $slug],
                        'ลบ',
                        $isBuiltin
                            ? 'ลบแผนนี้จากเว็บไซต์? (กู้คืนได้จากปุ่ม แสดงอีกครั้ง)'
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
