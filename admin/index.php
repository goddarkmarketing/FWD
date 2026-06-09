<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/_icons.php';
admin_require_login();

$page_title = 'แดชบอร์ด';
$active_file = 'index.php';
$siteName = cms_get('site', 'site_name', 'FWD AGENT');

$dashCards = [
    ['href' => 'site.php', 'icon' => 'settings', 'title' => 'ข้อมูลเว็บไซต์', 'desc' => 'ชื่อเว็บ ติดต่อ ใบอนุญาต โลโก้'],
    ['href' => 'homepage.php', 'icon' => 'home', 'title' => 'หน้าแรก', 'desc' => 'Hero แผนประกัน รีวิว ทำไมต้อง FWD'],
    ['href' => 'catalog.php', 'icon' => 'catalog', 'title' => 'แคตตาล็อก 37 แผน', 'desc' => 'ชื่อ คำอธิบาย ส่วนลด รูปภาพ'],
    ['href' => 'plans.php', 'icon' => 'plan', 'title' => 'รายละเอียดแผน', 'desc' => 'แก้ไขเนื้อหาหน้า plan แต่ละแผน'],
    ['href' => 'articles.php', 'icon' => 'article', 'title' => 'บทความ', 'desc' => 'เพิ่ม แก้ไข ลบบทความ'],
    ['href' => 'promotions.php', 'icon' => 'promo', 'title' => 'โปรโมชัน', 'desc' => 'ข้อเสนอพิเศษทุกแผน'],
    ['href' => 'footer.php', 'icon' => 'footer', 'title' => 'Footer', 'desc' => 'CTA ลิขสิทธิ์ Cookie banner'],
    ['href' => 'media.php', 'icon' => 'image', 'title' => 'สื่อ & รูปภาพ', 'desc' => 'อัปโหลด Hero ใบอนุญาต รูปอื่นๆ'],
    ['href' => 'backup.php', 'icon' => 'backup', 'title' => 'สำรอง & กู้คืน', 'desc' => 'ดาวน์โหลดและกู้คืนข้อมูล CMS + รูปภาพ'],
];

ob_start();
?>
<div class="admin-welcome">
    <h2>สวัสดีครับ 👋</h2>
    <p>ยินดีต้อนรับสู่ระบบจัดการ <?= admin_h($siteName) ?> — เลือกส่วนที่ต้องการแก้ไขด้านล่าง</p>
</div>

<div class="admin-grid">
    <?php foreach ($dashCards as $card): ?>
    <a class="admin-dash-card" href="<?= admin_h(admin_url($card['href'])) ?>">
        <span class="admin-dash-card__icon"><?= admin_icon($card['icon']) ?></span>
        <h3><?= admin_h($card['title']) ?></h3>
        <p><?= admin_h($card['desc']) ?></p>
        <span class="admin-dash-card__arrow">เปิดแก้ไข →</span>
    </a>
    <?php endforeach; ?>
</div>

<div class="admin-card">
    <div class="admin-card__head">
        <h2 class="admin-card__title">คำแนะนำ</h2>
        <span class="admin-badge admin-badge--live">Live on XAMPP</span>
    </div>
    <p style="margin:0;color:var(--admin-muted);font-size:0.92rem;line-height:1.6">
        หลังแก้ไขเนื้อหา หน้าเว็บจะอัปเดตทันทีบน XAMPP
        · สำหรับ GitHub Pages ให้รัน <code>php scripts/build-static.php</code> แล้ว deploy
        · ทดสอบระบบด้วย <code>php scripts/run-cms-tests.php</code>
    </p>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
