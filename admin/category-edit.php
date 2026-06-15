<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();

$id = trim((string) ($_GET['id'] ?? ''));
$data = cms_load('categories', require dirname(__DIR__) . '/includes/plan-categories.php');

if ($id === '' || !isset($data[$id])) {
    admin_flash('error', 'ไม่พบหมวดหมู่');
    admin_redirect('categories.php');
}

$cat = $data[$id];
$page_title = 'แก้ไขหมวด: ' . ($cat['title'] ?? $id);
$active_file = 'categories.php';

$defaults = require dirname(__DIR__) . '/includes/plan-categories.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    if (admin_post_string('action') === 'reset') {
        if (isset($defaults[$id])) {
            $data[$id] = $defaults[$id];
            cms_save('categories', $data);
            admin_flash('success', 'รีเซ็ตหมวดหมู่เป็นค่าเริ่มต้นแล้ว');
        }
        admin_redirect('category-edit.php?id=' . urlencode($id));
    }

    if (admin_post_string('action') === 'delete') {
        require_once dirname(__DIR__) . '/includes/cms-plan-meta.php';
        if ($id === 'all') {
            admin_flash('error', 'ไม่สามารถลบหมวด "ทั้งหมด" ได้');
        } else {
            $isBuiltin = plan_category_is_builtin($id);
            cms_category_delete($id);
            admin_flash('success', $isBuiltin ? 'ซ่อนหมวดหมู่แล้ว' : 'ลบหมวดหมู่แล้ว');
            admin_redirect('categories.php');
        }
    }

    $data[$id]['title'] = admin_post_string('title');
    $data[$id]['lead'] = admin_post_string('lead');
    $data[$id]['mega_desc'] = admin_post_string('mega_desc');
    cms_save('categories', $data);
    admin_flash('success', 'บันทึกหมวดหมู่เรียบร้อย');
    admin_redirect('category-edit.php?id=' . urlencode($id));
}

$hasCustom = isset($defaults[$id]) && (
    ($cat['title'] ?? '') !== ($defaults[$id]['title'] ?? '')
    || ($cat['lead'] ?? '') !== ($defaults[$id]['lead'] ?? '')
    || ($cat['mega_desc'] ?? '') !== ($defaults[$id]['mega_desc'] ?? '')
);

ob_start();
?>
<form method="post" class="admin-form">
    <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">
    <div class="admin-card">
        <p style="margin:0 0 1.25rem;color:var(--admin-muted)">
            <code><?= admin_h($id) ?></code>
            · หน้าเว็บ: <code><?= admin_h($cat['page'] ?? '') ?></code>
            · <a href="../<?= admin_h($cat['page'] ?? 'products.php') ?>" target="_blank" rel="noopener">ดูหน้าเว็บ ↗</a>
        </p>
        <div class="form-row">
            <label for="title">ชื่อหมวด</label>
            <input type="text" id="title" name="title" value="<?= admin_h($cat['title'] ?? '') ?>" required>
        </div>
        <div class="form-row">
            <label for="lead">คำอธิบาย (หน้าหมวด)</label>
            <textarea id="lead" name="lead" class="tall"><?= admin_h($cat['lead'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <label for="mega_desc">คำอธิบาย (เมนู)</label>
            <input type="text" id="mega_desc" name="mega_desc" value="<?= admin_h($cat['mega_desc'] ?? '') ?>">
            <p class="form-hint">แสดงในเมนูผลิตภัณฑ์ด้านบนเว็บไซต์</p>
        </div>
    </div>
    <div class="admin-actions">
        <a href="<?= admin_h(admin_url('categories.php')) ?>" class="admin-btn admin-btn--outline">กลับ</a>
        <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
    </div>
</form>
<?php if ($hasCustom): ?>
<div class="admin-actions" style="margin-top:0;padding-top:0;border-top:0">
    <?php admin_inline_post_form(
        ['action' => 'reset'],
        'รีเซ็ตเป็นค่าเริ่มต้น',
        'ลบการแก้ไข CMS ของหมวดนี้และใช้ค่าเริ่มต้น?',
        'admin-btn admin-btn--danger'
    ); ?>
</div>
<?php endif; ?>
<?php if ($id !== 'all'): ?>
<div class="admin-actions" style="margin-top:0;padding-top:0;border-top:0">
    <?php
    require_once dirname(__DIR__) . '/includes/cms-plan-meta.php';
    admin_inline_post_form(
        ['action' => 'delete'],
        'ลบหมวดหมู่',
        plan_category_is_builtin($id)
            ? 'ลบหมวดหมู่นี้จากเว็บไซต์? (กู้คืนได้จากปุ่ม แสดงอีกครั้ง)'
            : 'ลบหมวดหมู่นี้ถาวร?',
        'admin-btn admin-btn--danger'
    );
    ?>
</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
