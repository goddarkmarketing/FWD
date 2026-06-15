<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();
require_once dirname(__DIR__) . '/includes/plan-helpers.php';
require_once dirname(__DIR__) . '/includes/cms-plan-meta.php';

$page_title = 'เพิ่มแผนประกัน';
$active_file = 'catalog.php';
$categories = plan_categories();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $slug = admin_slugify(admin_post_string('slug'));
    $category = admin_post_string('category');
    $title = admin_post_string('title');
    $desc = admin_post_string('desc');
    $discount = admin_post_string('discount');
    $tags = array_values(array_filter(array_map('trim', explode(',', admin_post_string('tags')))));
    $image = admin_post_string('image');

    if ($slug === '') {
        $errors[] = 'กรุณากรอก slug แผน';
    } elseif (plan_catalog_by_slug($slug) !== null) {
        $errors[] = 'slug นี้มีอยู่แล้ว';
    }
    if ($title === '') {
        $errors[] = 'กรุณากรอกชื่อแผน';
    }
    if ($category === '' || !isset($categories[$category])) {
        $errors[] = 'กรุณาเลือกหมวดหมู่';
    }

    if ($errors === []) {
        $meta = cms_catalog_meta();
        $meta['custom'][$slug] = [
            'slug' => $slug,
            'category' => $category,
            'title' => $title,
            'desc' => $desc,
            'discount' => $discount !== '' ? $discount : null,
            'tags' => $tags,
            'image' => $image,
            'url' => 'plan.php?slug=' . rawurlencode($slug),
        ];
        $meta['hidden'] = array_values(array_filter(
            $meta['hidden'] ?? [],
            static fn(string $item): bool => $item !== $slug
        ));
        cms_save_meta('catalog', $meta);

        admin_flash('success', 'เพิ่มแผนในแคตตาล็อกเรียบร้อย');
        admin_redirect('catalog-edit.php?slug=' . urlencode($slug));
    }
}

ob_start();
?>
<form method="post" class="admin-form">
    <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">
    <?php if ($errors !== []): ?>
    <div class="admin-alert admin-alert--error">
        <?php foreach ($errors as $error): ?>
        <p style="margin:0"><?= admin_h($error) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="admin-card">
        <div class="form-grid-2">
            <div class="form-row">
                <label for="slug">Slug แผน <span class="required">*</span></label>
                <input type="text" id="slug" name="slug" value="<?= admin_h(admin_post_string('slug')) ?>" required pattern="[a-z0-9\-]+" placeholder="เช่น my-new-plan">
            </div>
            <div class="form-row">
                <label for="category">หมวดหมู่ <span class="required">*</span></label>
                <select id="category" name="category" required>
                    <option value="">เลือกหมวด</option>
                    <?php foreach ($categories as $catId => $cat): ?>
                    <?php if ($catId === 'all') { continue; } ?>
                    <option value="<?= admin_h($catId) ?>"<?= admin_post_string('category') === $catId ? ' selected' : '' ?>><?= admin_h($cat['title'] ?? $catId) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row"><label>ชื่อแผน <span class="required">*</span></label><input type="text" name="title" value="<?= admin_h(admin_post_string('title')) ?>" required></div>
        <div class="form-row"><label>คำอธิบาย</label><textarea name="desc"><?= admin_h(admin_post_string('desc')) ?></textarea></div>
        <div class="form-grid-2">
            <div class="form-row"><label>ส่วนลด</label><input type="text" name="discount" value="<?= admin_h(admin_post_string('discount')) ?>" placeholder="เช่น ลด 15%"></div>
            <div class="form-row"><label>แท็ก (คั่นด้วย comma)</label><input type="text" name="tags" value="<?= admin_h(admin_post_string('tags')) ?>"></div>
        </div>
        <?php admin_image_field('image', 'รูปภาพ', admin_post_string('image'), null, ['size' => 'wide', 'subdir' => 'images/products2', 'hide_path' => true]); ?>
    </div>
    <div class="admin-actions">
        <a href="<?= admin_h(admin_url('catalog.php')) ?>" class="admin-btn admin-btn--outline">ยกเลิก</a>
        <button type="submit" class="admin-btn admin-btn--primary">เพิ่มแผน</button>
    </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
