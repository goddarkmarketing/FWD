<?php
$page_title = 'หน้าแรก';
$page_description = 'เอฟดับบลิวดี ประเทศไทย — ประกันชีวิต ประกันสุขภาพ ซื้อออนไลน์ง่าย คุ้มครองครบ';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';

$hero_image = hero_cover_image();
$hero_mobile = hero_cover_mobile_image();
$hero_src = $hero_image ?? $hero_mobile;
?>

<?php if ($hero_src !== null): ?>
<section class="hero hero--static" aria-label="ภาพหน้าแรก">
    <div class="hero-static">
        <picture>
            <?php if ($hero_mobile !== null): ?>
            <source media="(max-width: 899px)" srcset="<?= htmlspecialchars(asset($hero_mobile)) ?>">
            <?php endif; ?>
            <img
                src="<?= htmlspecialchars(asset($hero_src)) ?>"
                alt="FWD by kruda — ประกันที่เข้าใจง่าย ให้คุณใช้ชีวิตได้เต็มที่"
                width="1200"
                height="1200"
                fetchpriority="high"
                decoding="sync"
            >
        </picture>
    </div>
</section>
<?php endif; ?>

<section class="section section--gray" id="plans">
    <div class="container">
        <?php
        require_once __DIR__ . '/includes/filter-icons.php';
        $plan_products = require __DIR__ . '/includes/plans-data.php';

        $plan_filters = [
            ['id' => 'all', 'label' => 'ผลิตภัณฑ์ทั้งหมด', 'default' => true],
            ['id' => 'life-accident', 'label' => 'ประกันชีวิตและอุบัติเหตุ'],
            ['id' => 'health', 'label' => 'ประกันสุขภาพ'],
            ['id' => 'critical', 'label' => 'ประกันโรคร้ายแรง'],
            ['id' => 'investment', 'label' => 'การลงทุน'],
            ['id' => 'savings', 'label' => 'ประกันสะสมทรัพย์'],
        ];

        $plan_panel_copy = [
            'all' => [
                'title' => 'ซื้อประกันออนไลน์ สมัครง่าย คุ้มครองทันที',
                'desc' => 'แผนประกันออนไลน์ที่มีส่วนลดพิเศษ ครอบคลุมทุกประเภท',
            ],
            'life-accident' => [
                'title' => 'ประกันชีวิตและอุบัติเหตุ',
                'desc' => 'คุ้มครองชีวิต อุบัติเหตุ และค่าชดเชย — ซื้อออนไลน์ได้ทันที',
            ],
            'health' => [
                'title' => 'ประกันสุขภาพ',
                'desc' => 'ดูแลสุขภาพครบวงจร ค่ารักษา ค่าห้อง และโรคร้ายแรง',
            ],
            'critical' => [
                'title' => 'ประกันโรคร้ายแรง',
                'desc' => 'เงินก้อนชดเชยเมื่อวินิจฉัยโรคร้ายแรง ใช้จ่ายได้อิสระ',
            ],
            'investment' => [
                'title' => 'การลงทุน',
                'desc' => 'ผสมผสานความคุ้มครองกับการลงทุน สร้างโอกาสเติบโต',
            ],
            'savings' => [
                'title' => 'ประกันสะสมทรัพย์',
                'desc' => 'ออมและคุ้มครองไปพร้อมกัน วางแผนอนาคตอย่างมั่นใจ',
            ],
        ];
        $default_panel = $plan_panel_copy['all'];
        ?>

        <header class="section__header reveal">
            <p class="section__eyebrow">ผลิตภัณฑ์ของเรา</p>
            <h2 class="section__title">ค้นหาแผนประกันที่เหมาะกับคุณ</h2>
            <p class="section__desc">เลือกแผนประกันที่เหมาะกับคุณ ซื้อออนไลน์ได้ทันที หรือปรึกษาผู้เชี่ยวชาญฟรี</p>
        </header>

        <div class="plans-toolbar reveal" id="plans-toolbar">
            <div class="search-bar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="search" id="product-search" placeholder="กำลังมองหาอะไรอยู่..." aria-label="ค้นหาผลิตภัณฑ์" autocomplete="off">
            </div>
            <div class="product-filters" id="product-filters" role="group" aria-label="กรองตามประเภท">
                <?php foreach ($plan_filters as $filter):
                    $panel = $plan_panel_copy[$filter['id']] ?? $default_panel;
                ?>
                <button
                    type="button"
                    class="product-filters__btn<?= !empty($filter['default']) ? ' is-active' : '' ?>"
                    data-filter="<?= htmlspecialchars($filter['id']) ?>"
                    data-panel-title="<?= htmlspecialchars($panel['title']) ?>"
                    data-panel-desc="<?= htmlspecialchars($panel['desc']) ?>"
                    data-panel-label="<?= htmlspecialchars($filter['label']) ?>"
                    aria-pressed="<?= !empty($filter['default']) ? 'true' : 'false' ?>"
                >
                    <span class="product-filters__icon"><?= filter_icon_svg($filter['id']) ?></span>
                    <span class="product-filters__label"><?= htmlspecialchars($filter['label']) ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="plan-grid-wrap plan-grid-wrap--promo reveal" id="plan-grid-wrap">
            <div class="plans-promo<?= empty($plan_products) ? ' plans-promo--empty' : '' ?>">
                <aside class="plans-promo__panel" id="plans-promo-panel" aria-label="<?= htmlspecialchars($default_panel['title']) ?>">
                    <span class="plans-promo__icon" id="plans-promo-icon" aria-hidden="true"><?= filter_icon_svg('all') ?></span>
                    <p class="plans-promo__eyebrow" id="plans-promo-eyebrow"><?= htmlspecialchars($plan_filters[0]['label']) ?></p>
                    <h2 class="plans-promo__title" id="plans-promo-title"><?= htmlspecialchars($default_panel['title']) ?></h2>
                    <p class="plans-promo__desc" id="plans-promo-desc"><?= htmlspecialchars($default_panel['desc']) ?></p>
                </aside>

                <div class="plans-promo__cards">
                    <div class="product-slider product-slider--promo<?= empty($plan_products) ? ' is-empty' : '' ?>" id="plan-grid-slider" data-per-view="3">
                        <?php if ($plan_products): ?>
                        <div class="product-slider__wrap">
                            <button type="button" class="product-slider__arrow product-slider__arrow--prev" aria-label="เลื่อนซ้าย">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
                            </button>
                            <div class="product-slider__viewport">
                                <div class="plan-grid plan-grid--carousel" id="plan-grid">
                                    <?php foreach ($plan_products as $plan): ?>
                                    <div class="product-slider__item" data-category="<?= htmlspecialchars($plan['category']) ?>">
                                        <?php include __DIR__ . '/includes/plan-card.php'; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button type="button" class="product-slider__arrow product-slider__arrow--next" aria-label="เลื่อนขวา">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                            </button>
                        </div>
                        <?php else: ?>
                        <p class="product-slider__empty">ไม่มีแผนโปรโมชันในขณะนี้</p>
                        <?php endif; ?>
                    </div>
                    <div class="product-slider__dots product-slider__dots--promo" id="plan-grid-dots" role="tablist" aria-label="เลือกหน้าสไลด์"></div>
                </div>
            </div>
            <p class="plan-grid__empty" id="plan-empty" hidden>ไม่พบแผนประกันที่ตรงกับการค้นหา</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="feature-row reveal">
            <div class="feature-row__media">
                <img src="<?= htmlspecialchars(image_url('assets/images/consultation.png')) ?>" alt="ปรึกษาประกันออนไลน์" loading="lazy">
            </div>
            <div class="feature-row__content">
                <p class="section__eyebrow">บริการของเรา</p>
                <h2>รับคำปรึกษาฟรีจากผู้เชี่ยวชาญ</h2>
                <p>ฝากข้อมูลไว้ ทีมงาน FWD จะติดต่อกลับเพื่อช่วยเลือกแผนที่เหมาะกับคุณ ไม่มีค่าใช้จ่าย ไม่มีข้อผูกมัด</p>
                <ul class="feature-list">
                    <li>ปรึกษาทางโทรศัพท์หรือพบตัวต่อตัว</li>
                    <li>เปรียบเทียบแผนประกันหลายแบบ</li>
                    <li>ซื้อออนไลน์ได้ภายในไม่กี่นาที</li>
                </ul>
                <a href="contact.php" class="btn btn--primary">ขอคำปรึกษาฟรี</a>
            </div>
        </div>
    </div>
</section>

<section class="section section--why-fwd">
    <div class="container">
        <header class="section__header reveal">
            <p class="section__eyebrow">ทำไมต้อง FWD</p>
            <h2 class="section__title">ประกันที่ออกแบบมาเพื่อคุณ</h2>
            <p class="section__desc">มั่นใจได้ด้วยบริการที่ทันสมัย ครอบคลุม และเข้าถึงง่าย</p>
        </header>

        <div class="why-cards">
            <article class="why-card reveal">
                <div class="why-card__icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <p class="why-card__value">10M+</p>
                <p class="why-card__label">ลูกค้าทั่วโลกในเครือ FWD</p>
            </article>

            <article class="why-card reveal">
                <div class="why-card__icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/></svg>
                </div>
                <p class="why-card__value">24/7</p>
                <p class="why-card__label">บริการเคลมออนไลน์</p>
            </article>

            <article class="why-card reveal">
                <div class="why-card__icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <p class="why-card__value">1351</p>
                <p class="why-card__label">สายด่วนตลอด 24 ชม.</p>
            </article>

            <article class="why-card reveal">
                <div class="why-card__icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
                </div>
                <p class="why-card__value">100%</p>
                <p class="why-card__label">ซื้อออนไลน์ได้บางผลิตภัณฑ์</p>
            </article>
        </div>
    </div>
</section>

<?php
$customer_reviews = [
    [
        'rating' => 5,
        'text' => 'ซื้อประกันสุขภาพออนไลน์ได้ภายใน 10 นาที เอกสารเข้าใจง่าย ตอนป่วยเคลมผ่านแอปไม่ต้องสำรองจ่ายเลย ประทับใจมากครับ',
        'name' => 'คุณธนกร',
        'meta' => 'ประกันสุขภาพ · กรุงเทพฯ',
    ],
    [
        'rating' => 5,
        'text' => 'ทีมงานโทรมาอธิบายเปรียบเทียบแผนให้ชัดเจน ไม่กดดัน ได้แผนที่เหมาะกับงบประมาณจริงๆ บริการเป็นมิตรมากค่ะ',
        'name' => 'คุณพิมพ์ใจ',
        'meta' => 'ประกันชีวิต · เชียงใหม่',
    ],
    [
        'rating' => 5,
        'text' => 'เคลมประกันอุบัติเหตุเร็วมาก ส่งเอกสารออนไลน์วันจันทร์ วันพุธเงินเข้าบัญชีแล้ว ติดตามสถานะในแอปได้ตลอด',
        'name' => 'คุณอนุชา',
        'meta' => 'ประกันอุบัติเหตุ · ชลบุรี',
    ],
    [
        'rating' => 4,
        'text' => 'เพิ่งซื้อประกันโรคร้ายแรงครั้งแรก หน้าเว็บใช้ง่าย มีโปรตรวจสุขภาพฟรีด้วย รู้สึกมั่นใจขึ้นเยอะเลยค่ะ',
        'name' => 'คุณมณี',
        'meta' => 'ประกันโรคร้ายแรง · ขอนแก่น',
    ],
];
?>

<section class="section section--reviews" id="reviews">
    <div class="container">
        <header class="section__header reveal">
            <p class="section__eyebrow">รีวิวจากลูกค้า</p>
            <h2 class="section__title">เสียงจากผู้ที่ไว้วางใจ FWD</h2>
            <p class="section__desc">ประสบการณ์จริงจากลูกค้าที่ใช้บริการซื้อประกันและเคลมกับเรา</p>
        </header>

        <div class="review-grid">
            <?php foreach ($customer_reviews as $review): ?>
            <article class="review-card reveal">
                <div class="review-card__stars" aria-label="<?= (int) $review['rating'] ?> จาก 5 ดาว">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                    <span class="star<?= $s <= $review['rating'] ? ' star--full' : ' star--empty' ?>">★</span>
                    <?php endfor; ?>
                </div>
                <blockquote class="review-card__quote">“<?= htmlspecialchars($review['text']) ?>”</blockquote>
                <footer class="review-card__author">
                    <?php
                    $review_initial = function_exists('mb_substr')
                        ? mb_substr(preg_replace('/^คุณ/u', '', $review['name']), 0, 1, 'UTF-8')
                        : substr($review['name'], -3, 3);
                    ?>
                    <div class="review-card__avatar" aria-hidden="true"><?= htmlspecialchars($review_initial) ?></div>
                    <div>
                        <cite class="review-card__name"><?= htmlspecialchars($review['name']) ?></cite>
                        <p class="review-card__meta"><?= htmlspecialchars($review['meta']) ?></p>
                    </div>
                </footer>
            </article>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
    require_once __DIR__ . '/includes/review-gallery.php';
    $gallery_images = review_gallery_images();
    if ($gallery_images):
        $gallery_half = (int) ceil(count($gallery_images) / 2);
        $gallery_row_top = array_slice($gallery_images, 0, $gallery_half);
        $gallery_row_bottom = array_slice($gallery_images, $gallery_half);
        if (empty($gallery_row_bottom)) {
            $gallery_row_bottom = $gallery_row_top;
        }
    ?>
    <div class="review-gallery reveal">
        <div class="container">
            <header class="review-gallery__header">
                <p class="section__eyebrow">แกลเลอรี</p>
                <h3 class="review-gallery__title">ภาพรีวิวจริงจากลูกค้า</h3>
            </header>
        </div>
        <div class="gallery-marquee" aria-label="แกลเลอรีรีวิวลูกค้า">
            <?php render_gallery_marquee_row($gallery_row_top, 'left'); ?>
            <?php render_gallery_marquee_row($gallery_row_bottom, 'right'); ?>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php
require_once __DIR__ . '/includes/promotions-data.php';
require_once __DIR__ . '/includes/promo-card.php';
require_once __DIR__ . '/includes/articles-data.php';
require_once __DIR__ . '/includes/article-card.php';
$home_promos = promotions_home();
$home_articles = articles_all();
?>

<section class="section section--gray">
    <div class="container">
        <header class="section__header reveal">
            <p class="section__eyebrow">โปรโมชัน</p>
            <h2 class="section__title">ข้อเสนอพิเศษประจำเดือน</h2>
        </header>
        <div class="promo-grid promo-grid--home reveal">
            <?php foreach ($home_promos as $promo): ?>
                <?php render_promo_card($promo); ?>
            <?php endforeach; ?>
        </div>
        <div class="section__footer reveal">
            <a href="<?= htmlspecialchars(page_url('promotions.php')) ?>" class="btn btn--outline">โปรโมชั่นเพิ่มเติม</a>
        </div>
    </div>
</section>

<section class="section" id="articles">
    <div class="container">
        <header class="section__header reveal">
            <p class="section__eyebrow">บทความ</p>
            <h2 class="section__title">ความรู้เรื่องประกัน</h2>
            <p class="section__lead">เคล็ดลับและข้อมูลที่ช่วยให้คุณเลือกประกันได้อย่างมั่นใจ</p>
        </header>
        <div class="article-grid reveal">
            <?php foreach ($home_articles as $article): ?>
                <?php render_article_card($article); ?>
            <?php endforeach; ?>
        </div>
        <div class="section__footer reveal">
            <a href="<?= htmlspecialchars(page_url('articles.php')) ?>" class="btn btn--outline">ดูบทความทั้งหมด</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
