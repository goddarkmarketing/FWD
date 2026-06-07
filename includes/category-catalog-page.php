<?php
/**
 * Category listing with plan cards from catalog.
 * Expects: $category_slug ('all' or category id)
 */
$category_slug = $category_slug ?? 'all';
$categories = plan_categories();
$config = $categories[$category_slug] ?? $categories['all'];

$category_title = $category_title ?? $config['title'];
$category_lead = $category_lead ?? $config['lead'];
$plans = plans_by_category($category_slug);
$plan_count = count($plans);
?>
<section class="page-hero">
    <div class="container">
        <nav class="page-hero__breadcrumb" aria-label="breadcrumb">
            <a href="<?= htmlspecialchars(page_url('index.php')) ?>">หน้าแรก</a>
            / <?= htmlspecialchars($category_title) ?>
        </nav>
        <h1><?= htmlspecialchars($category_title) ?></h1>
        <p class="page-hero__lead"><?= htmlspecialchars($category_lead) ?></p>
        <p class="page-hero__meta"><?= (int) $plan_count ?> ผลิตภัณฑ์</p>
    </div>
</section>

<section class="section section--gray">
    <div class="container">
        <?php if ($plans): ?>
        <div class="plan-grid plan-grid--category reveal is-visible">
            <?php foreach ($plans as $plan): ?>
                <?php include __DIR__ . '/plan-card.php'; ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="plan-grid__empty">ไม่มีผลิตภัณฑ์ในหมวดนี้</p>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="feature-row feature-row--reverse reveal">
            <div class="feature-row__media">
                <img src="<?= htmlspecialchars(image_url('assets/images/consultation.png')) ?>" alt="ปรึกษาประกัน" loading="lazy">
            </div>
            <div class="feature-row__content">
                <h2>ไม่แน่ใจว่าแผนไหนเหมาะกับคุณ?</h2>
                <p>ผู้เชี่ยวชาญของเราพร้อมช่วยเปรียบเทียบและแนะนำแผนที่ตรงกับความต้องการและงบประมาณของคุณ</p>
                <a href="<?= htmlspecialchars(page_url('contact.php')) ?>" class="btn btn--primary">ขอคำปรึกษาฟรี</a>
            </div>
        </div>
    </div>
</section>
