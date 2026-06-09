<?php
require_once __DIR__ . '/includes/config.php';
$p = cms_page('claims', []);
$page_title = $p['page_title'] ?? 'การเคลมประกัน';
$page_description = $p['meta_description'] ?? '';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <nav class="page-hero__breadcrumb" aria-label="breadcrumb">
            <a href="<?= htmlspecialchars(page_url('index.php')) ?>">หน้าแรก</a> / การเคลม
        </nav>
        <h1><?= htmlspecialchars($p['hero_title'] ?? 'การเคลมประกัน') ?></h1>
        <p class="page-hero__lead"><?= htmlspecialchars($p['hero_lead'] ?? '') ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <header class="section__header reveal">
            <p class="section__eyebrow"><?= htmlspecialchars($p['steps_eyebrow'] ?? 'ขั้นตอน') ?></p>
            <h2 class="section__title"><?= htmlspecialchars($p['steps_title'] ?? 'เคลมประกันใน 4 ขั้นตอน') ?></h2>
        </header>
        <div class="steps reveal">
            <?php foreach ($p['steps'] ?? [] as $step): ?>
            <div class="step-item">
                <h3><?= htmlspecialchars($step['title'] ?? '') ?></h3>
                <p><?= htmlspecialchars($step['desc'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--gray">
    <div class="container">
        <div class="feature-row reveal">
            <div class="feature-row__content">
                <h2><?= htmlspecialchars($p['app_title'] ?? 'เคลมออนไลน์ผ่าน FWD Omne') ?></h2>
                <p><?= htmlspecialchars($p['app_desc'] ?? '') ?></p>
                <ul class="feature-list">
                    <?php foreach ($p['app_bullets'] ?? [] as $bullet): ?>
                    <li><?= htmlspecialchars($bullet) ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?= htmlspecialchars(page_url('contact.php')) ?>" class="btn btn--primary"><?= htmlspecialchars($p['app_cta'] ?? 'ติดต่อศูนย์บริการ') ?></a>
            </div>
            <div class="feature-row__media">
                <img src="<?= htmlspecialchars($p['app_image'] ?? '') ?>" alt="เคลมออนไลน์" loading="lazy">
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <header class="section__header reveal">
            <h2 class="section__title">เอกสารที่ใช้เคลม</h2>
            <p class="section__desc">เอกสารอาจแตกต่างตามประเภทการเคลม — ตรวจสอบเงื่อนไขในกรมธรรม์ของคุณ</p>
        </header>
        <div class="highlight-grid reveal">
            <div class="highlight-item">
                <h4>ค่ารักษาพยาบาล</h4>
                <p>ใบเสร็จรับเงิน ใบรับรองแพทย์ สำเนาบัตรประชาชน</p>
            </div>
            <div class="highlight-item">
                <h4>อุบัติเหตุ</h4>
                <p>ใบรับรองแพทย์ รายงานอุบัติเหตุ (ถ้ามี) สำเนาบัตรประชาชน</p>
            </div>
            <div class="highlight-item">
                <h4>โรคร้ายแรง</h4>
                <p>ผลการวินิจฉัยจากแพทย์ ประวัติการรักษา สำเนาบัตรประชาชน</p>
            </div>
            <div class="highlight-item">
                <h4>เสียชีวิต</h4>
                <p>มรณบัตร ทะเบียนบ้าน สำเนาบัตรผู้รับผลประโยชน์</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
