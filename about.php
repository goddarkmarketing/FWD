<?php
require_once __DIR__ . '/includes/config.php';
$p = cms_page('about', []);
$page_title = $p['page_title'] ?? 'เกี่ยวกับ FWD';
$page_description = $p['meta_description'] ?? '';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero page-hero--orange">
    <div class="container">
        <nav class="page-hero__breadcrumb" aria-label="breadcrumb">
            <a href="<?= htmlspecialchars(page_url('index.php')) ?>">หน้าแรก</a> / เกี่ยวกับ FWD
        </nav>
        <h1><?= htmlspecialchars($p['hero_title'] ?? 'เกี่ยวกับ FWD') ?></h1>
        <p class="page-hero__lead"><?= htmlspecialchars($p['hero_lead'] ?? '') ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="feature-row reveal">
            <div class="feature-row__content">
                <p class="section__eyebrow"><?= htmlspecialchars($p['vision_eyebrow'] ?? 'วิสัยทัศน์') ?></p>
                <h2><?= htmlspecialchars($p['vision_title'] ?? 'Celebrate living') ?></h2>
                <?php foreach ($p['vision_paragraphs'] ?? [] as $para): ?>
                <p><?= htmlspecialchars($para) ?></p>
                <?php endforeach; ?>
            </div>
            <div class="feature-row__media">
                <img src="<?= htmlspecialchars($p['vision_image'] ?? '') ?>" alt="สำนักงาน FWD" loading="lazy">
            </div>
        </div>
    </div>
</section>

<section class="section section--gray">
    <div class="container">
        <header class="section__header reveal">
            <h2 class="section__title"><?= htmlspecialchars($p['values_title'] ?? 'ค่านิยมของเรา') ?></h2>
        </header>
        <div class="highlight-grid reveal">
            <?php foreach ($p['values'] ?? [] as $value): ?>
            <div class="highlight-item">
                <h4><?= htmlspecialchars($value['title'] ?? '') ?></h4>
                <p><?= htmlspecialchars($value['desc'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="feature-row feature-row--reverse reveal">
            <div class="feature-row__media">
                <img src="<?= htmlspecialchars($p['group_image'] ?? '') ?>" alt="ทีมงาน FWD" loading="lazy">
            </div>
            <div class="feature-row__content">
                <h2><?= htmlspecialchars($p['group_title'] ?? 'เครือ FWD Group') ?></h2>
                <?php foreach ($p['group_paragraphs'] ?? [] as $para): ?>
                <p><?= htmlspecialchars($para) ?></p>
                <?php endforeach; ?>
                <a href="<?= htmlspecialchars($p['group_url'] ?? '#') ?>" class="btn btn--outline"><?= htmlspecialchars($p['group_cta'] ?? 'เยี่ยมชม FWD Group') ?></a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
