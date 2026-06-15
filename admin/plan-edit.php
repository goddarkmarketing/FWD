<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();
require_once dirname(__DIR__) . '/includes/plan-helpers.php';
require_once dirname(__DIR__) . '/includes/cms-plan-meta.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$all = plan_details_all();
$base = $all[$slug] ?? null;
if ($base === null) {
    admin_flash('error', 'ไม่พบแผน');
    admin_redirect('plans.php');
}

$page_title = 'แก้ไขแผน: ' . ($base['title'] ?? $slug);
$active_file = 'plans.php';
$cms = cms_plan_override($slug);
$hasCms = $cms !== [];

function plan_edit_list(array $base, array $cms, string $key): array
{
    $items = $cms[$key] ?? $base[$key] ?? [];
    if (!is_array($items)) {
        $items = array_values(array_filter(array_map('trim', explode("\n", (string) $items))));
    }
    return $items !== [] ? $items : [''];
}

function plan_edit_pairs(array $base, array $cms, string $key, array $fields): array
{
    $items = $cms[$key] ?? $base[$key] ?? [];
    if (!is_array($items)) {
        return [['title' => '', 'desc' => '']];
    }
    if ($items === []) {
        $empty = [];
        foreach ($fields as $f) {
            $empty[$f] = '';
        }
        return [$empty];
    }
    return $items;
}

$data = [
    'title' => $cms['title'] ?? $base['title'] ?? '',
    'tagline' => $cms['tagline'] ?? $base['tagline'] ?? '',
    'meta' => $cms['meta'] ?? $base['meta'] ?? '',
    'discount' => $cms['discount'] ?? $base['discount'] ?? '',
    'no_calculator' => array_key_exists('no_calculator', $cms)
        ? !empty($cms['no_calculator'])
        : !empty($base['no_calculator']),
];
$heroBullets = plan_edit_list($base, $cms, 'hero_bullets');
$highlights = plan_edit_pairs($base, $cms, 'highlights', ['title', 'desc']);
$conditions = plan_edit_list($base, $cms, 'conditions');
$faq = plan_edit_pairs($base, $cms, 'faq', ['q', 'a']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    if (admin_post_string('action') === 'delete') {
        $isBuiltin = plan_detail_is_builtin($slug);
        cms_plan_detail_delete($slug);
        admin_flash('success', $isBuiltin ? 'ลบหน้ารายละเอียดแผนแล้ว' : 'ลบแผนแล้ว');
        admin_redirect('plans.php');
    }

    if (admin_post_string('action') === 'reset_cms') {
        $path = cms_file_path('plans/' . $slug);
        if (is_file($path)) {
            unlink($path);
        }
        admin_flash('success', 'รีเซ็ตการแก้ไข CMS แล้ว');
        admin_redirect('plan-edit.php?slug=' . urlencode($slug));
    }

    $heroBullets = array_values(array_filter(array_map('trim', (array) ($_POST['hero_bullets'] ?? []))));

    $highlights = [];
    foreach ((array) ($_POST['highlights'] ?? []) as $row) {
        if (!is_array($row)) {
            continue;
        }
        $title = trim((string) ($row['title'] ?? ''));
        $desc = trim((string) ($row['desc'] ?? ''));
        if ($title !== '' || $desc !== '') {
            $highlights[] = ['title' => $title, 'desc' => $desc];
        }
    }

    $conditions = array_values(array_filter(array_map('trim', (array) ($_POST['conditions'] ?? []))));

    $faq = [];
    foreach ((array) ($_POST['faq'] ?? []) as $row) {
        if (!is_array($row)) {
            continue;
        }
        $q = trim((string) ($row['q'] ?? ''));
        $a = trim((string) ($row['a'] ?? ''));
        if ($q !== '' || $a !== '') {
            $faq[] = ['q' => $q, 'a' => $a];
        }
    }

    $save = [
        'title' => admin_post_string('title'),
        'tagline' => admin_post_string('tagline'),
        'meta' => admin_post_string('meta'),
        'discount' => admin_post_string('discount'),
        'hero_bullets' => $heroBullets,
        'highlights' => $highlights,
        'conditions' => $conditions,
        'faq' => $faq,
        'no_calculator' => !empty($_POST['no_calculator']),
    ];
    cms_save('plans/' . $slug, $save);
    admin_flash('success', 'บันทึกรายละเอียดแผนเรียบร้อย');
    admin_redirect('plan-edit.php?slug=' . urlencode($slug));
}

ob_start();
?>
<form method="post" class="admin-form">
    <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">

    <div class="admin-card">
        <p style="margin:0 0 1rem">
            <code><?= admin_h($slug) ?></code>
            <?php if ($hasCms): ?><span class="admin-badge" style="margin-left:.5rem">มีการแก้ไข CMS</span><?php endif; ?>
            · <a href="../plan.php?slug=<?= urlencode($slug) ?>" target="_blank" rel="noopener">ดูหน้าเว็บ ↗</a>
        </p>
        <div class="admin-card__head">
            <h2 class="admin-card__title">ข้อมูลหลัก</h2>
        </div>
        <p class="admin-card__lead">ชื่อแผน คำโปรย และข้อมูล SEO ที่แสดงด้านบนหน้ารายละเอียด</p>
        <div class="form-grid-2">
            <div class="form-row">
                <label for="title">ชื่อแผน</label>
                <input type="text" id="title" name="title" value="<?= admin_h($data['title']) ?>">
            </div>
            <div class="form-row">
                <label for="discount">ป้ายส่วนลด</label>
                <input type="text" id="discount" name="discount" value="<?= admin_h($data['discount']) ?>" placeholder="เช่น รับส่วนลด 10%">
            </div>
        </div>
        <div class="form-row">
            <label for="tagline">Tagline (คำโปรยใต้ชื่อ)</label>
            <textarea id="tagline" name="tagline" rows="2"><?= admin_h($data['tagline']) ?></textarea>
        </div>
        <div class="form-row">
            <label for="meta">Meta description (SEO)</label>
            <input type="text" id="meta" name="meta" value="<?= admin_h($data['meta']) ?>">
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card__head">
            <h2 class="admin-card__title">จุดเด่น Hero</h2>
            <button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-repeater-add="#hero-bullets-list">+ เพิ่มข้อ</button>
        </div>
        <p class="admin-card__lead">รายการ bullet ใต้ชื่อแผน — แสดงเป็นจุดๆ ด้านบนหน้า</p>
        <div id="hero-bullets-list" data-repeater-list>
            <?php foreach ($heroBullets as $i => $bullet): ?>
            <div class="admin-repeater-item">
                <div class="admin-repeater-item__head">
                    <p class="admin-repeater-item__title">ข้อที่ <span data-repeater-index><?= $i + 1 ?></span></p>
                    <button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-repeater-remove>ลบ</button>
                </div>
                <div class="form-row" style="margin:0">
                    <input type="text" name="hero_bullets[<?= $i ?>]" value="<?= admin_h($bullet) ?>" placeholder="เช่น คุ้มครองชีวิตและอุบัติเหตุในฉบับเดียว">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card__head">
            <h2 class="admin-card__title">จุดเด่น (แท็บ Highlights)</h2>
            <button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-repeater-add="#highlights-list">+ เพิ่มจุดเด่น</button>
        </div>
        <p class="admin-card__lead">การ์ดจุดเด่นในแท็บ «จุดเด่น» — มีหัวข้อและคำอธิบาย</p>
        <div id="highlights-list" data-repeater-list>
            <?php foreach ($highlights as $i => $row): ?>
            <div class="admin-repeater-item">
                <div class="admin-repeater-item__head">
                    <p class="admin-repeater-item__title">จุดเด่นที่ <span data-repeater-index><?= $i + 1 ?></span></p>
                    <button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-repeater-remove>ลบ</button>
                </div>
                <div class="form-grid-2">
                    <div class="form-row">
                        <label>หัวข้อ</label>
                        <input type="text" name="highlights[<?= $i ?>][title]" value="<?= admin_h($row['title'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <label>คำอธิบาย</label>
                        <textarea name="highlights[<?= $i ?>][desc]" rows="2"><?= admin_h($row['desc'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card__head">
            <h2 class="admin-card__title">เงื่อนไข</h2>
            <button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-repeater-add="#conditions-list">+ เพิ่มข้อ</button>
        </div>
        <p class="admin-card__lead">ข้อความเงื่อนไขท้ายหน้า — แสดงเป็นรายการ</p>
        <div id="conditions-list" data-repeater-list>
            <?php foreach ($conditions as $i => $line): ?>
            <div class="admin-repeater-item">
                <div class="admin-repeater-item__head">
                    <p class="admin-repeater-item__title">ข้อที่ <span data-repeater-index><?= $i + 1 ?></span></p>
                    <button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-repeater-remove>ลบ</button>
                </div>
                <div class="form-row" style="margin:0">
                    <textarea name="conditions[<?= $i ?>]" rows="2"><?= admin_h($line) ?></textarea>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card__head">
            <h2 class="admin-card__title">คำถามที่พบบ่อย (FAQ)</h2>
            <button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-repeater-add="#faq-list">+ เพิ่มคำถาม</button>
        </div>
        <p class="admin-card__lead">คำถามและคำตอบที่แสดงด้านล่างหน้าแผน</p>
        <div id="faq-list" data-repeater-list>
            <?php foreach ($faq as $i => $row): ?>
            <div class="admin-repeater-item">
                <div class="admin-repeater-item__head">
                    <p class="admin-repeater-item__title">คำถามที่ <span data-repeater-index><?= $i + 1 ?></span></p>
                    <button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-repeater-remove>ลบ</button>
                </div>
                <div class="form-row">
                    <label>คำถาม</label>
                    <input type="text" name="faq[<?= $i ?>][q]" value="<?= admin_h($row['q'] ?? '') ?>">
                </div>
                <div class="form-row">
                    <label>คำตอบ</label>
                    <textarea name="faq[<?= $i ?>][a]" rows="3"><?= admin_h($row['a'] ?? '') ?></textarea>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="admin-card">
        <h2 class="admin-card__title">ตั้งค่าเพิ่มเติม</h2>
        <p class="admin-card__lead">ตัวเลือกการแสดงผลบนหน้าเว็บ</p>
        <div class="form-row">
            <label>
                <input type="checkbox" name="no_calculator" value="1" <?= !empty($data['no_calculator']) ? 'checked' : '' ?>>
                ปิดเครื่องคำนวณเบี้ยบนหน้านี้
            </label>
        </div>
    </div>

    <div class="admin-actions">
        <a href="<?= admin_h(admin_url('plans.php')) ?>" class="admin-btn admin-btn--outline">กลับ</a>
        <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
        <?php if ($hasCms): ?>
        <button type="submit" name="action" value="reset_cms" class="admin-btn admin-btn--outline" onclick="return confirm('รีเซ็ตการแก้ไข CMS และใช้ค่าเดิม?')">รีเซ็ต CMS</button>
        <?php endif; ?>
        <button type="submit" name="action" value="delete" class="admin-btn admin-btn--danger" onclick="return confirm('<?= plan_detail_is_builtin($slug) ? 'ลบหน้ารายละเอียดแผนและซ่อนการ์ดแคตตาล็อก? (กู้คืนได้)' : 'ลบแผนนี้ถาวร?' ?>')">ลบแผน</button>
    </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
