<?php
$page_title = 'โปรโมชัน';
$page_description = 'โปรโมชันและข้อเสนอพิเศษจาก FWD ประจำเดือน';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/promotions-data.php';
require_once __DIR__ . '/includes/promo-card.php';
$all_promos = promotions_all();
?>

<section class="page-hero page-hero--orange">
    <div class="container">
        <nav class="page-hero__breadcrumb" aria-label="breadcrumb">
            <a href="index.php">หน้าแรก</a> / โปรโมชัน
        </nav>
        <h1>โปรโมชันและข้อเสนอพิเศษ</h1>
        <p class="page-hero__lead">ข้อเสนอพิเศษสำหรับผลิตภัณฑ์ที่เลือกซื้อออนไลน์</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="promo-grid promo-grid--page">
            <?php foreach ($all_promos as $promo): ?>
                <div class="reveal"><?php render_promo_card($promo); ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
