<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();
require_once dirname(__DIR__) . '/includes/plan-helpers.php';
require_once dirname(__DIR__) . '/includes/cms-plan-meta.php';

$page_title = 'เพิ่มหมวดหมู่';
$active_file = 'categories.php';
$defaults = plan_category_defaults();
$data = cms_load('categories', $defaults);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $id = admin_slugify(admin_post_string('id'));
    $title = admin_post_string('title');
    $lead = admin_post_string('lead');
    $megaDesc = admin_post_string('mega_desc');

    if ($id === '' || $id === 'all') {
        $errors[] = 'รหัสหมวด (slug) ต้องเป็นตัวอักษร a-z 0-9 และ - เท่านั้น';
    } elseif (isset($defaults[$id]) || isset($data[$id])) {
        $errors[] = 'รหัสหมวดนี้มีอยู่แล้ว';
    }
    if ($title === '') {
        $errors[] = 'กรุณากรอกชื่อหมวด';
    }

    if ($errors === []) {
        $data[$id] = [
            'id' => $id,
            'title' => $title,
            'lead' => $lead,
            'mega_desc' => $megaDesc !== '' ? $megaDesc : $lead,
            'page' => 'category.php?cat=' . rawurlencode($id),
        ];
        cms_save('categories', $data);

        $meta = cms_categories_meta();
        $order = $meta['order'] ?? plan_category_default_order();
        if (!in_array($id, $order, true)) {
            $order[] = $id;
        }
        $meta['order'] = $order;
        $meta['hidden'] = array_values(array_filter(
            $meta['hidden'] ?? [],
            static fn(string $item): bool => $item !== $id
        ));
        cms_save_meta('categories', $meta);

        admin_flash('success', 'เพิ่มหมวดหมู่เรียบร้อย');
        admin_redirect('category-edit.php?id=' . urlencode($id));
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
        <div class="form-row">
            <label for="id">รหัสหมวด (slug) <span class="required">*</span></label>
            <input type="text" id="id" name="id" value="<?= admin_h(admin_post_string('id')) ?>" required pattern="[a-z0-9\-]+" placeholder="เช่น pet-insurance">
            <p class="form-hint">ใช้ตัวพิมพ์เล็ก a-z, ตัวเลข, และ - เท่านั้น</p>
        </div>
        <div class="form-row">
            <label for="title">ชื่อหมวด <span class="required">*</span></label>
            <input type="text" id="title" name="title" value="<?= admin_h(admin_post_string('title')) ?>" required>
        </div>
        <div class="form-row">
            <label for="lead">คำอธิบาย (หน้าหมวด)</label>
            <textarea id="lead" name="lead" class="tall"><?= admin_h(admin_post_string('lead')) ?></textarea>
        </div>
        <div class="form-row">
            <label for="mega_desc">คำอธิบาย (เมนู)</label>
            <input type="text" id="mega_desc" name="mega_desc" value="<?= admin_h(admin_post_string('mega_desc')) ?>">
        </div>
    </div>
    <div class="admin-actions">
        <a href="<?= admin_h(admin_url('categories.php')) ?>" class="admin-btn admin-btn--outline">ยกเลิก</a>
        <button type="submit" class="admin-btn admin-btn--primary">เพิ่มหมวดหมู่</button>
    </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
