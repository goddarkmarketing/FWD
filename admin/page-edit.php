<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();

$allowed = ['about', 'claims', 'contact', 'agent-apply'];
$id = trim((string) ($_GET['id'] ?? ''));
if (!in_array($id, $allowed, true)) {
    admin_redirect('index.php');
}

$labels = [
    'about' => 'เกี่ยวกับเรา',
    'claims' => 'การเคลม',
    'contact' => 'ติดต่อเรา',
    'agent-apply' => 'สมัครตัวแทน',
];

$page_title = 'แก้ไขหน้า: ' . $labels[$id];
$active_file = 'page-edit.php?id=' . $id;
$data = cms_load('pages/' . $id, []);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $fields = array_keys($data);
    // collect all posted fields
    foreach ($_POST as $key => $val) {
        if ($key === '_csrf' || $key === 'action') {
            continue;
        }
        if (is_string($val)) {
            $data[$key] = $val;
        }
    }
    // multiline list fields
    foreach (['vision_paragraphs', 'group_paragraphs', 'app_bullets', 'interests', 'education_levels', 'steps'] as $listKey) {
        if (isset($_POST[$listKey . '_text'])) {
            if ($listKey === 'steps') {
                $lines = array_filter(array_map('trim', explode("\n", admin_post_string($listKey . '_text'))));
                $steps = [];
                foreach ($lines as $line) {
                    $parts = explode('|', $line, 2);
                    $steps[] = ['title' => trim($parts[0]), 'desc' => trim($parts[1] ?? '')];
                }
                $data['steps'] = $steps;
            } else {
                $data[$listKey] = array_values(array_filter(array_map('trim', explode("\n", admin_post_string($listKey . '_text')))));
            }
        }
    }
    if (isset($_POST['values_text'])) {
        $values = [];
        foreach (array_filter(array_map('trim', explode("\n", admin_post_string('values_text')))) as $line) {
            $parts = explode('|', $line, 2);
            $values[] = ['title' => trim($parts[0]), 'desc' => trim($parts[1] ?? '')];
        }
        $data['values'] = $values;
    }
    cms_save('pages/' . $id, $data);
    admin_flash('success', 'บันทึกหน้าเรียบร้อย');
    admin_redirect('page-edit.php?id=' . urlencode($id));
}

function page_field(array $data, string $key, string $label, string $type = 'text'): void
{
    $val = $data[$key] ?? '';
    if ($type === 'image') {
        admin_image_field($key, $label, $val, null, ['id' => $key, 'size' => 'wide']);
        return;
    }
    echo '<div class="form-row"><label for="' . admin_h($key) . '">' . admin_h($label) . '</label>';
    if ($type === 'textarea') {
        echo '<textarea id="' . admin_h($key) . '" name="' . admin_h($key) . '">' . admin_h($val) . '</textarea>';
    } else {
        echo '<input type="text" id="' . admin_h($key) . '" name="' . admin_h($key) . '" value="' . admin_h($val) . '">';
    }
    echo '</div>';
}

ob_start();
?>
<form method="post" class="admin-form">
    <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">
    <div class="admin-card">
        <p style="margin:0 0 1rem"><a href="../<?= admin_h($id === 'agent-apply' ? 'agent-apply.php' : $id . '.php') ?>" target="_blank">ดูหน้าเว็บ ↗</a></p>
        <?php
        page_field($data, 'page_title', 'Page title (SEO)');
        page_field($data, 'meta_description', 'Meta description', 'textarea');
        page_field($data, 'hero_title', 'หัวข้อ Hero');
        page_field($data, 'hero_lead', 'คำอธิบาย Hero', 'textarea');
        ?>

        <?php if ($id === 'about'): ?>
        <?php page_field($data, 'vision_eyebrow', 'วิสัยทัศน์ Eyebrow'); ?>
        <?php page_field($data, 'vision_title', 'วิสัยทัศน์ หัวข้อ'); ?>
        <div class="form-row"><label>วิสัยทัศน์ ย่อหน้า (บรรทัดละ 1)</label><textarea name="vision_paragraphs_text"><?= admin_h(implode("\n", $data['vision_paragraphs'] ?? [])) ?></textarea></div>
        <?php page_field($data, 'vision_image', 'รูปวิสัยทัศน์', 'image'); ?>
        <?php page_field($data, 'values_title', 'หัวข้อค่านิยม'); ?>
        <div class="form-row"><label>ค่านิยม (รูปแบบ: ชื่อ|คำอธิบาย บรรทัดละ 1)</label><textarea name="values_text"><?php
            foreach ($data['values'] ?? [] as $v) {
                echo admin_h(($v['title'] ?? '') . '|' . ($v['desc'] ?? '')) . "\n";
            }
        ?></textarea></div>
        <?php page_field($data, 'group_title', 'FWD Group หัวข้อ'); ?>
        <div class="form-row"><label>FWD Group ย่อหน้า</label><textarea name="group_paragraphs_text"><?= admin_h(implode("\n", $data['group_paragraphs'] ?? [])) ?></textarea></div>
        <?php page_field($data, 'group_image', 'รูป FWD Group', 'image'); ?>
        <?php page_field($data, 'group_cta', 'ปุ่ม FWD Group'); ?>
        <?php page_field($data, 'group_url', 'ลิงก์ FWD Group'); ?>

        <?php elseif ($id === 'claims'): ?>
        <?php page_field($data, 'steps_eyebrow', 'ขั้นตอน Eyebrow'); ?>
        <?php page_field($data, 'steps_title', 'ขั้นตอน หัวข้อ'); ?>
        <div class="form-row"><label>ขั้นตอน (ชื่อ|คำอธิบาย บรรทัดละ 1)</label><textarea name="steps_text"><?php
            foreach ($data['steps'] ?? [] as $s) {
                echo admin_h(($s['title'] ?? '') . '|' . ($s['desc'] ?? '')) . "\n";
            }
        ?></textarea></div>
        <?php page_field($data, 'app_title', 'แอป หัวข้อ'); ?>
        <?php page_field($data, 'app_desc', 'แอป คำอธิบาย', 'textarea'); ?>
        <div class="form-row"><label>แอป รายการ</label><textarea name="app_bullets_text"><?= admin_h(implode("\n", $data['app_bullets'] ?? [])) ?></textarea></div>
        <?php page_field($data, 'app_image', 'รูปแอป', 'image'); ?>
        <?php page_field($data, 'app_cta', 'ปุ่ม CTA'); ?>

        <?php elseif ($id === 'contact'): ?>
        <?php page_field($data, 'success_title', 'ข้อความสำเร็จ หัวข้อ'); ?>
        <?php page_field($data, 'success_message', 'ข้อความสำเร็จ', 'textarea'); ?>
        <div class="form-row"><label>ตัวเลือกความสนใจ (บรรทัดละ 1)</label><textarea name="interests_text"><?= admin_h(implode("\n", $data['interests'] ?? [])) ?></textarea></div>

        <?php elseif ($id === 'agent-apply'): ?>
        <?php page_field($data, 'success_title', 'ข้อความสำเร็จ หัวข้อ'); ?>
        <?php page_field($data, 'success_message', 'ข้อความสำเร็จ', 'textarea'); ?>
        <div class="form-row"><label>ระดับการศึกษา (บรรทัดละ 1)</label><textarea name="education_levels_text"><?= admin_h(implode("\n", $data['education_levels'] ?? [])) ?></textarea></div>
        <?php endif; ?>
    </div>
    <div class="admin-actions">
        <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
    </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
