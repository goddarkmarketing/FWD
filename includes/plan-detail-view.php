<?php
/**
 * FWD-style product detail page (with or without premium calculator).
 */
require_once __DIR__ . '/plan-apply-icons.php';

$p = $plan;
$slug = $p['slug'] ?? '';
$categoryUrl = plan_category_url($p['category']);
$contactUrl = plan_contact_url($slug, $p['title'] ?? '');
$noCalc = !empty($p['no_calculator']);
$heroImage = !empty($p['image']) ? image_url($p['image']) : '';
$heroBullets = [];
if ($noCalc && !empty($p['hero_bullets'])) {
    $taglineNorm = trim($p['tagline'] ?? '');
    foreach ($p['hero_bullets'] as $bullet) {
        $bullet = trim($bullet);
        if ($bullet === '' || ($taglineNorm !== '' && $bullet === $taglineNorm)) {
            continue;
        }
        if (mb_strlen($bullet) > 200) {
            continue;
        }
        $heroBullets[] = $bullet;
    }
    $heroBullets = array_values(array_unique($heroBullets));
    $heroBullets = array_slice($heroBullets, 0, 5);
}
$packages = $p['packages'] ?? [];
$defaults = $p['calculator_defaults'] ?? ['gender' => 'male', 'age' => 30, 'package_index' => 0, 'payment' => 'yearly'];
$calcJson = htmlspecialchars(json_encode([
    'packages' => $packages,
    'defaults' => $defaults,
    'premium_table' => $p['premium_table'] ?? null,
], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
?>
<div class="plan-fwd" id="plan-fwd"<?= $noCalc ? '' : ' data-plan-config="' . $calcJson . '"' ?>>
    <section class="plan-fwd-hero<?= $heroImage !== '' ? ' plan-fwd-hero--media' : '' ?>"<?= $heroImage !== '' ? ' style="--plan-hero-image: url(\'' . htmlspecialchars($heroImage, ENT_QUOTES) . '\')"' : '' ?>>
        <div class="plan-fwd-hero__overlay" aria-hidden="true"></div>
        <div class="container plan-fwd-hero__inner">
            <nav class="plan-fwd-hero__breadcrumb" aria-label="breadcrumb">
                <a href="<?= htmlspecialchars(page_url('index.php')) ?>">หน้าแรก</a> /
                <a href="<?= htmlspecialchars($categoryUrl) ?>"><?= htmlspecialchars($p['category_label']) ?></a>
            </nav>
            <div class="plan-fwd-hero__head">
                <div>
                    <?php if (!empty($p['discount'])): ?>
                    <span class="plan-hero__badge"><?= htmlspecialchars($p['discount']) ?></span>
                    <?php endif; ?>
                    <h1><?= htmlspecialchars($p['title']) ?></h1>
                    <p class="plan-fwd-hero__tagline"><?= htmlspecialchars($p['tagline']) ?></p>
                </div>
            </div>

            <?php if ($noCalc): ?>
            <?php if ($heroBullets !== []): ?>
            <ul class="plan-fwd-bullets plan-fwd-bullets--hero">
                <?php foreach ($heroBullets as $bullet): ?>
                <li><?= htmlspecialchars($bullet) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <div class="plan-fwd-hero__cta">
                <a href="<?= htmlspecialchars($contactUrl) ?>" class="btn btn--hero-cta btn--lg">สนใจแผนนี้ — ขอคำปรึกษา</a>
                <p class="plan-fwd-hero__cta-note">กรอกแบบฟอร์มติดต่อ ทีมงานจะโทรกลับโดยเร็ว ไม่มีค่าใช้จ่าย</p>
            </div>
            <?php else: ?>
            <div class="plan-calc" id="plan-calc">
                <div class="plan-calc__form">
                    <p class="plan-calc__label">ฉันเป็น</p>
                    <div class="plan-calc__gender" role="group" aria-label="เพศ">
                        <button type="button" class="plan-calc__gender-btn is-active" data-gender="male">ผู้ชาย</button>
                        <button type="button" class="plan-calc__gender-btn" data-gender="female">ผู้หญิง</button>
                    </div>
                    <label class="plan-calc__field">
                        <span>อายุ</span>
                        <select id="plan-calc-age" aria-label="อายุ">
                            <?php for ($a = 18; $a <= 55; $a++): ?>
                            <option value="<?= $a ?>"<?= (int) $defaults['age'] === $a ? ' selected' : '' ?>><?= $a ?> ปี</option>
                            <?php endfor; ?>
                        </select>
                    </label>
                    <label class="plan-calc__field">
                        <span>เลือกแพ็กเกจ</span>
                        <select id="plan-calc-package" aria-label="แพ็กเกจ">
                            <?php foreach ($packages as $i => $pkg): ?>
                            <option value="<?= $i ?>"<?= (int) $defaults['package_index'] === $i ? ' selected' : '' ?>>
                                <?= htmlspecialchars($pkg['name']) ?> —
                                <?php
                                echo number_format($pkg['sum']) . ' บาท';
                                ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <div class="plan-calc__pay" role="group" aria-label="งวดชำระ">
                        <button type="button" class="plan-calc__pay-btn is-active" data-pay="yearly">รายปี</button>
                        <button type="button" class="plan-calc__pay-btn" data-pay="monthly">รายเดือน</button>
                    </div>
                </div>
                <div class="plan-calc__result">
                    <p class="plan-calc__result-label">เบี้ยประกันของคุณ</p>
                    <p class="plan-calc__result-price" id="plan-calc-price">—</p>
                    <p class="plan-calc__result-sum" id="plan-calc-sum">—</p>
                    <a href="<?= htmlspecialchars($contactUrl) ?>" class="btn btn--primary plan-calc__buy">ขอคำปรึกษา</a>
                    <p class="plan-calc__note">* ราคาตัวอย่าง — ติดต่อเพื่อรับเบี้ยและเงื่อนไขที่ถูกต้อง</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="container plan-fwd-layout">
        <aside class="plan-fwd-nav" aria-label="เมนูในหน้า">
            <nav>
                <a href="#plan-highlights">จุดเด่นแผนประกัน</a>
                <?php if (!$noCalc): ?>
                <a href="#plan-premium-table">ตารางเบี้ยประกันภัย</a>
                <a href="#plan-compare">เปรียบเทียบแผน</a>
                <?php endif; ?>
                <?php if (!empty($p['coverage_blocks'])): ?>
                <a href="#plan-coverage">รายละเอียดความคุ้มครอง</a>
                <?php endif; ?>
                <?php if (!empty($p['faq'])): ?>
                <a href="#plan-faq">คำถามที่พบบ่อย</a>
                <?php endif; ?>
                <a href="#plan-contact-cta">ติดต่อสอบถาม</a>
            </nav>
        </aside>

        <div class="plan-fwd-main">
            <section id="plan-highlights" class="plan-fwd-section">
                <h2 class="plan-fwd-section__title">จุดเด่นของแผน <?= htmlspecialchars($p['title']) ?></h2>
                <div class="plan-fwd-highlights">
                    <?php foreach ($p['highlights'] as $i => $h): ?>
                    <article class="plan-fwd-highlight">
                        <span class="plan-fwd-highlight__num"><?= $i + 1 ?></span>
                        <h3><?= htmlspecialchars($h['title']) ?></h3>
                        <p><?= htmlspecialchars($h['desc']) ?></p>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php if (!$noCalc && !empty($p['hero_bullets'])): ?>
                <ul class="plan-fwd-bullets">
                    <?php foreach ($p['hero_bullets'] as $bullet): ?>
                    <li><?= htmlspecialchars($bullet) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </section>

            <?php if (!$noCalc): ?>
            <section id="plan-premium-table" class="plan-fwd-section plan-fwd-section--gray">
                <h2 class="plan-fwd-section__title">ตารางเบี้ยประกันภัย</h2>
                <p class="plan-fwd-section__lead">เลือกเพศและปรับทุนประกันเพื่อดูเบี้ยโดยประมาณ<?php if (!empty($p['pricing_source'])): ?> (อ้างอิงโครงจาก <a href="<?= htmlspecialchars($p['pricing_source']) ?>" target="_blank" rel="noopener">FWD</a>)<?php endif; ?></p>

                <div class="plan-table-controls">
                    <div class="plan-table-tabs" role="tablist">
                        <button type="button" class="plan-table-tab is-active" data-table-gender="male">ชาย</button>
                        <button type="button" class="plan-table-tab" data-table-gender="female">หญิง</button>
                    </div>
                    <?php if (!empty($p['premium_table']['sums'])): ?>
                    <label class="plan-table-slider-label">
                        <span>ทุนประกัน: <strong id="plan-table-sum-label"><?= number_format($p['premium_table']['sums'][0]) ?></strong> บาท</span>
                        <input type="range" id="plan-table-sum" min="0" max="<?= count($p['premium_table']['sums']) - 1 ?>" value="0" step="1">
                    </label>
                    <?php endif; ?>
                </div>

                <div class="plan-table-wrap">
                    <table class="plan-table" id="plan-premium-table">
                        <thead>
                            <tr>
                                <th>ช่วงอายุ</th>
                                <th>เบี้ย/ปี (บาท)</th>
                                <th>เบี้ย/เดือน (บาท)</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <p class="plan-pricing-note"><?= htmlspecialchars($p['pricing_disclaimer'] ?? '') ?></p>
            </section>

            <section id="plan-compare" class="plan-fwd-section">
                <h2 class="plan-fwd-section__title">เปรียบเทียบแผนความคุ้มครอง</h2>
                <div class="plan-compare-wrap">
                    <table class="plan-compare">
                        <thead>
                            <tr>
                                <th></th>
                                <?php foreach ($packages as $pkg): ?>
                                <th>
                                    <span class="plan-compare__name"><?= htmlspecialchars($pkg['name']) ?></span>
                                    <?php if (!empty($pkg['badge'])): ?>
                                    <span class="plan-compare__badge"><?= htmlspecialchars($pkg['badge']) ?></span>
                                    <?php endif; ?>
                                    <span class="plan-compare__sum"><?= number_format($pkg['sum']) ?> บาท</span>
                                    <span class="plan-compare__premium">เริ่ม <?= number_format($pkg['premium_yearly']) ?> บาท/ปี</span>
                                    <a href="<?= htmlspecialchars($contactUrl) ?>" class="btn btn--primary btn--sm">ขอคำปรึกษา</a>
                                </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($p['compare_rows'] ?? [] as $row): ?>
                            <tr>
                                <th scope="row"><?= htmlspecialchars($row['feature']) ?></th>
                                <?php foreach ($row['values'] as $val): ?>
                                <td>
                                    <?php if ($val === true): ?>
                                    <span class="plan-compare__check" aria-label="มี">✓</span>
                                    <?php elseif ($val === false): ?>
                                    <span class="plan-compare__dash">—</span>
                                    <?php else: ?>
                                    <?= htmlspecialchars((string) $val) ?>
                                    <?php endif; ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <?php foreach ($packages as $pkg): ?>
                                <td>
                                    <a href="<?= htmlspecialchars($contactUrl) ?>" class="btn btn--primary btn--sm">ขอคำปรึกษา</a>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
            <?php endif; ?>

            <?php if (!empty($p['coverage_blocks'])): ?>
            <section id="plan-coverage" class="plan-fwd-section plan-fwd-section--gray">
                <h2 class="plan-fwd-section__title">รายละเอียดความคุ้มครอง</h2>
                <div class="plan-coverage-grid">
                    <?php foreach ($p['coverage_blocks'] as $block): ?>
                    <article class="plan-coverage-card">
                        <?php if (!empty($block['step'])): ?>
                        <span class="plan-coverage-card__step"><?= htmlspecialchars($block['step']) ?></span>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($block['title']) ?></h3>
                        <?php if (!empty($block['desc'])): ?>
                        <p><?= htmlspecialchars($block['desc']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($block['items'])): ?>
                        <ul>
                            <?php foreach ($block['items'] as $item): ?>
                            <li><?= htmlspecialchars($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if (!empty($p['application'])): ?>
            <section class="plan-fwd-section plan-apply">
                <h2 class="plan-fwd-section__title"><?= $noCalc ? 'ขั้นตอนเมื่อสนใจแผนนี้' : 'สิ่งที่ต้องใช้ในการสมัคร' ?></h2>
                <div class="plan-apply__grid">
                    <?php foreach ($p['application'] as $req): ?>
                    <div class="plan-apply__item">
                        <span class="plan-apply__icon plan-apply__icon--<?= htmlspecialchars($req['icon']) ?>" aria-hidden="true"><?= plan_apply_icon_svg($req['icon']) ?></span>
                        <div>
                            <h3><?= htmlspecialchars($req['title']) ?></h3>
                            <p><?= htmlspecialchars($req['desc']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if (!empty($p['conditions'])): ?>
            <section class="plan-fwd-section">
                <h2 class="plan-fwd-section__title">เงื่อนไขกรมธรรม์</h2>
                <ul class="feature-list">
                    <?php foreach ($p['conditions'] as $cond): ?>
                    <li><?= htmlspecialchars($cond) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php if (!empty($p['promo'])): ?>
                <div class="plan-promo-box" style="margin-top: 1.5rem;">
                    <p><strong>โปรโมชัน:</strong> <?= $p['promo'] ?></p>
                </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>

            <?php if (!empty($p['faq'])): ?>
            <section id="plan-faq" class="plan-fwd-section plan-fwd-section--gray">
                <h2 class="plan-fwd-section__title">คำถามที่พบบ่อย</h2>
                <div class="faq-accordion faq-accordion--wide">
                    <?php foreach ($p['faq'] as $i => $item): ?>
                    <details class="faq-item"<?= $i === 0 ? ' open' : '' ?>>
                        <summary><?= htmlspecialchars($item['q']) ?></summary>
                        <p><?= htmlspecialchars($item['a']) ?></p>
                    </details>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <section id="plan-contact-cta" class="plan-fwd-section plan-fwd-inline-cta">
                <h2 class="plan-fwd-section__title">สนใจแผน <?= htmlspecialchars($p['title']) ?>?</h2>
                <p>ฝากข้อมูลไว้ ผู้เชี่ยวชาญจะติดต่อกลับเพื่ออธิบายความคุ้มครองและขั้นตอนต่อไป</p>
                <a href="<?= htmlspecialchars($contactUrl) ?>" class="btn btn--primary btn--lg">ไปที่แบบฟอร์มติดต่อ</a>
            </section>
        </div>
    </div>

    <section class="plan-fwd-cta">
        <div class="container plan-fwd-cta__inner">
            <?php if ($noCalc): ?>
            <h2>พร้อมวางแผนความคุ้มครองกับเรา?</h2>
            <p>กรอกแบบฟอร์มติดต่อ — ปรึกษาฟรี ไม่มีค่าใช้จ่าย</p>
            <?php else: ?>
            <h2>จ่ายเบี้ยหลักพัน คุ้มครองหลักล้าน</h2>
            <p>เริ่มต้นวางแผนวันนี้ — ขอคำปรึกษาหรือโทรสอบถาม</p>
            <?php endif; ?>
            <div class="plan-fwd-cta__actions">
                <a href="<?= htmlspecialchars($contactUrl) ?>" class="btn btn--white">ขอคำปรึกษา</a>
                <a href="tel:1351" class="btn btn--outline btn--white-outline">โทร 1351</a>
            </div>
        </div>
    </section>
</div>
