<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();

$page_title = 'หน้าแรก';
$active_file = 'homepage.php';
$data = cms_load('homepage', []);
$site = cms_load('site', []);

$sections = [
    [
        'id' => 'hero',
        'name' => 'ภาพ Hero',
        'title' => $site['hero_alt'] ?? '—',
        'meta' => 'assets/cover/',
    ],
    [
        'id' => 'plans_section',
        'name' => 'ส่วนแผนประกัน',
        'title' => $data['plans_section']['title'] ?? '—',
        'meta' => '#plans',
    ],
    [
        'id' => 'plan_filters',
        'name' => 'ตัวกรองแผนประกัน',
        'title' => $data['plan_filters'][0]['label'] ?? '—',
        'meta' => count($data['plan_filters'] ?? []) . ' ตัวกรอง',
    ],
    [
        'id' => 'consultation',
        'name' => 'ปรึกษาฟรี',
        'title' => $data['consultation']['title'] ?? '—',
        'meta' => 'feature-row',
    ],
    [
        'id' => 'why_fwd',
        'name' => 'ทำไมต้อง FWD',
        'title' => $data['why_fwd']['title'] ?? '—',
        'meta' => count($data['why_fwd']['cards'] ?? []) . ' การ์ด',
    ],
    [
        'id' => 'reviews',
        'name' => 'รีวิวลูกค้า',
        'title' => $data['reviews']['title'] ?? '—',
        'meta' => count($data['reviews']['items'] ?? []) . ' รีวิว',
    ],
    [
        'id' => 'promos_section',
        'name' => 'โปรโมชัน',
        'title' => $data['promos_section']['title'] ?? '—',
        'meta' => '#promotions',
    ],
    [
        'id' => 'articles_section',
        'name' => 'บทความ',
        'title' => $data['articles_section']['title'] ?? '—',
        'meta' => '#articles',
    ],
];

$q = trim((string) ($_GET['q'] ?? ''));
if ($q !== '') {
    $sections = array_values(array_filter($sections, function ($row) use ($q) {
        $hay = $row['id'] . ' ' . $row['name'] . ' ' . $row['title'] . ' ' . $row['meta'];
        return stripos($hay, $q) !== false;
    }));
}

ob_start();
?>
<div class="admin-card">
    <form method="get" class="admin-form admin-toolbar">
        <div class="form-row">
            <label for="q">ค้นหาส่วนหน้าแรก</label>
            <input type="search" id="q" name="q" value="<?= admin_h($q) ?>" placeholder="ชื่อส่วน หรือ slug...">
        </div>
        <button type="submit" class="admin-btn admin-btn--outline">ค้นหา</button>
    </form>
    <p class="form-hint" style="margin:0 0 1rem">
        <a href="../index.php" target="_blank" rel="noopener">ดูหน้าแรก ↗</a>
        · รูป Hero / แกลเลอรีรีวิว อัปโหลดได้ในหน้าแก้ไขแต่ละส่วน
    </p>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ส่วน</th>
                    <th>หัวข้อหลัก</th>
                    <th>รายละเอียด</th>
                    <th>แก้ไข CMS</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($sections as $row): ?>
            <tr>
                <td>
                    <strong><?= admin_h($row['name']) ?></strong><br>
                    <code><?= admin_h($row['id']) ?></code>
                </td>
                <td><?= admin_h(mb_strimwidth($row['title'], 0, 60, '…')) ?></td>
                <td><?= admin_h($row['meta']) ?></td>
                <td>
                    <a href="<?= admin_h(admin_url('homepage-edit.php?id=' . urlencode($row['id']))) ?>" class="admin-btn admin-btn--outline admin-btn--sm">แก้ไข</a>
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
