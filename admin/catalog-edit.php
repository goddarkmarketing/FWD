<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();
require_once dirname(__DIR__) . '/includes/plan-helpers.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$item = plan_catalog_by_slug($slug);
if ($item === null) {
    admin_flash('error', 'ไม่พบแผน: ' . $slug);
    admin_redirect('catalog.php');
}

$page_title = 'แก้ไขแคตตาล็อก: ' . ($item['title'] ?? $slug);
$active_file = 'catalog.php';
$overrides = cms_load('catalog', []);
$data = array_merge([
    'title' => $item['title'] ?? '',
    'desc' => $item['desc'] ?? '',
    'discount' => $item['discount'] ?? '',
    'image' => $item['image'] ?? '',
    'tags' => implode(', ', $item['tags'] ?? []),
], $overrides[$slug] ?? []);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $action = admin_post_string('action');
    if ($action === 'reset') {
        unset($overrides[$slug]);
        cms_save('catalog', $overrides);
        admin_flash('success', 'รีเซ็ตเป็นค่าเริ่มต้นแล้ว');
        admin_redirect('catalog-edit.php?slug=' . urlencode($slug));
    }

    $overrides[$slug] = [
        'title' => admin_post_string('title'),
        'desc' => admin_post_string('desc'),
        'discount' => admin_post_string('discount'),
        'image' => admin_post_string('image'),
        'tags' => array_values(array_filter(array_map('trim', explode(',', admin_post_string('tags'))))),
    ];
    $upload = cms_upload('image_upload', 'images/products2');
    if ($upload) {
        $overrides[$slug]['image'] = $upload;
    }
    cms_save('catalog', $overrides);
    admin_flash('success', 'บันทึกแคตตาล็อกเรียบร้อย');
    admin_redirect('catalog-edit.php?slug=' . urlencode($slug));
}

ob_start();
?>
<form method="post" enctype="multipart/form-data" class="admin-form">
    <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">
    <div class="admin-card">
        <p style="margin:0 0 1rem;color:var(--admin-muted)">Slug: <code><?= admin_h($slug) ?></code></p>
        <div class="form-row"><label>ชื่อแผน</label><input type="text" name="title" value="<?= admin_h($data['title'] ?? '') ?>"></div>
        <div class="form-row"><label>คำอธิบาย</label><textarea name="desc"><?= admin_h($data['desc'] ?? '') ?></textarea></div>
        <div class="form-grid-2">
            <div class="form-row"><label>ส่วนลด</label><input type="text" name="discount" value="<?= admin_h($data['discount'] ?? '') ?>" placeholder="เช่น ลด 15%"></div>
            <div class="form-row"><label>แท็ก (คั่นด้วย comma)</label><input type="text" name="tags" value="<?= admin_h(is_array($data['tags'] ?? null) ? implode(', ', $data['tags']) : ($data['tags'] ?? '')) ?>"></div>
        </div>
        <?php admin_image_field('image', 'รูปภาพ (path)', $data['image'] ?? '', 'image_upload', ['size' => 'wide']); ?>
    </div>
    <div class="admin-actions">
        <a href="<?= admin_h(admin_url('catalog.php')) ?>" class="admin-btn admin-btn--outline">กลับ</a>
        <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
        <button type="submit" name="action" value="reset" class="admin-btn admin-btn--danger" onclick="return confirm('รีเซ็ตการแก้ไข CMS ของแผนนี้?')">รีเซ็ต</button>
    </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
