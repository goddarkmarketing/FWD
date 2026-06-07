<?php
/**
 * Shared product category page template.
 * Expects: $category_title, $category_lead, $category_slug, $products (array)
 */
if (!isset($products)) {
    $products = [];
}
?>
<section class="page-hero">
    <div class="container">
        <nav class="page-hero__breadcrumb" aria-label="breadcrumb">
            <a href="<?= htmlspecialchars(page_url('index.php')) ?>">หน้าแรก</a> / <?= htmlspecialchars($category_title) ?>
        </nav>
        <h1><?= htmlspecialchars($category_title) ?></h1>
        <p class="page-hero__lead"><?= htmlspecialchars($category_lead) ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
            <article class="product-tile reveal">
                <div class="product-tile__image<?= !preg_match('#^https?://#i', $product['image']) ? ' product-tile__image--brand' : '' ?>">
                    <img src="<?= htmlspecialchars(image_url($product['image'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
                </div>
                <div class="product-tile__body">
                    <?php if (!empty($product['tag'])): ?>
                    <span class="product-tile__tag"><?= htmlspecialchars($product['tag']) ?></span>
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p><?= htmlspecialchars($product['desc']) ?></p>
                    <a href="<?= htmlspecialchars(page_url($product['url'])) ?>" class="product-tile__link">ดูรายละเอียด →</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--gray">
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
