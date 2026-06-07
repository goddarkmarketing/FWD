<?php
/**
 * Product detail template.
 * Expects: $product_name, $product_tagline, $product_price, $product_price_note,
 *          $category_url, $category_name, $highlights (array), $tabs (array of id => title)
 */
if (!isset($highlights)) {
    $highlights = [];
}
if (!isset($tabs)) {
    $tabs = ['highlights' => 'จุดเด่น', 'coverage' => 'ความคุ้มครอง', 'conditions' => 'เงื่อนไข'];
}
$first_tab = array_key_first($tabs);
?>
<section class="page-hero">
    <div class="container">
        <nav class="page-hero__breadcrumb" aria-label="breadcrumb">
            <a href="index.php">หน้าแรก</a> /
            <a href="<?= htmlspecialchars($category_url) ?>"><?= htmlspecialchars($category_name) ?></a> /
            <?= htmlspecialchars($product_name) ?>
        </nav>
        <h1><?= htmlspecialchars($product_name) ?></h1>
        <p class="page-hero__lead"><?= htmlspecialchars($product_tagline) ?></p>
    </div>
</section>

<section class="section">
    <div class="container product-detail-layout">
        <div class="product-detail__main">
            <div class="product-detail__tabs">
                <?php foreach ($tabs as $id => $title): ?>
                <button type="button" class="tab-btn<?= $id === $first_tab ? ' is-active' : '' ?>" data-tab="<?= htmlspecialchars($id) ?>">
                    <?= htmlspecialchars($title) ?>
                </button>
                <?php endforeach; ?>
            </div>

            <div class="tab-panel<?= $first_tab === 'highlights' ? ' is-active' : '' ?>" data-panel="highlights">
                <div class="highlight-grid">
                    <?php foreach ($highlights as $h): ?>
                    <div class="highlight-item">
                        <h4><?= htmlspecialchars($h['title']) ?></h4>
                        <p><?= htmlspecialchars($h['desc']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="tab-panel" data-panel="coverage">
                <ul class="feature-list">
                    <?php if (!empty($coverage_items)): ?>
                        <?php foreach ($coverage_items as $item): ?>
                        <li><?= htmlspecialchars($item) ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>คุ้มครองตามแผนที่เลือก — ดูรายละเอียดในเอกสารกรมธรรม์</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="tab-panel" data-panel="conditions">
                <p style="color: var(--fwd-gray-500); margin-bottom: 1rem;">เงื่อนไขเป็นไปตามที่ระบุในกรมธรรม์ กรุณาอ่านรายละเอียดก่อนตัดสินใจซื้อ</p>
                <ul class="feature-list">
                    <li>อายุรับประกันและอายุครบสัญญาตามเงื่อนไขผลิตภัณฑ์</li>
                    <li>ระยะเวลาที่ไม่คุ้มครอง (Waiting period) ตามประเภทความคุ้มครอง</li>
                    <li>ข้อยกเว้นตามที่ระบุในกรมธรรม์</li>
                    <li>เบี้ยประกันขึ้นอยู่กับอายุ เพศ และแผนที่เลือก</li>
                </ul>
            </div>

            <?php if (isset($tabs['promo'])): ?>
            <div class="tab-panel" data-panel="promo">
                <p>โปรโมชันปัจจุบัน: <a href="promotions.php">ดูโปรโมชันทั้งหมด</a></p>
            </div>
            <?php endif; ?>
        </div>

        <aside class="sidebar-card">
            <span class="product-tile__tag">ซื้อออนไลน์</span>
            <h3><?= htmlspecialchars($product_name) ?></h3>
            <p class="price"><?= htmlspecialchars($product_price) ?> <small><?= htmlspecialchars($product_price_note ?? '/เดือน') ?></small></p>
            <a href="contact.php" class="btn btn--primary">ซื้อออนไลน์</a>
            <a href="contact.php" class="btn btn--outline" style="margin-top: 0.5rem;">ขอคำปรึกษา</a>
            <p style="font-size: 0.8125rem; color: var(--fwd-gray-500); margin-top: 1rem;">เบี้ยประกันขึ้นอยู่กับอายุและแผนที่เลือก ราคาข้างต้นเป็นตัวอย่าง</p>
        </aside>
    </div>
</section>
