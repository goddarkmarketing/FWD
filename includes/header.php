<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/form-mailer.php';
require_once __DIR__ . '/logo.php';
require_once __DIR__ . '/icons.php';
if (!isset($page_title)) {
    $page_title = SITE_NAME;
}
if (!isset($page_description)) {
    $page_description = 'บริษัทประกันชีวิตและประกันสุขภาพ — ออกแบบประกันให้เข้าใจง่าย ซื้อออนไลน์ได้';
}
$page_robots = $page_robots ?? 'index, follow';
$page_canonical = $page_canonical ?? '';
$page_og_title = $page_og_title ?? ($page_title . ' | ' . SITE_NAME);
$page_og_description = $page_og_description ?? $page_description;
$page_og_image = $page_og_image ?? '';
$page_og_image_width = $page_og_image_width ?? null;
$page_og_image_height = $page_og_image_height ?? null;
$page_og_image_alt = $page_og_image_alt ?? (defined('HERO_ALT') ? HERO_ALT : SITE_NAME);
$page_og_type = $page_og_type ?? 'website';
$page_og_url = $page_og_url ?? '';

if ($page_og_image === '') {
    $shareMeta = site_share_image_meta();
    if ($shareMeta !== null) {
        $page_og_image = $shareMeta['url'];
        $page_og_image_width = $shareMeta['width'];
        $page_og_image_height = $shareMeta['height'];
        $page_og_image_alt = $shareMeta['alt'];
    }
} elseif (!preg_match('#^https?://#i', $page_og_image)) {
    $page_og_image = ensure_absolute_url($page_og_image);
}

if ($page_og_url === '') {
    $page_og_url = current_canonical_url();
}
if ($page_canonical === '') {
    $page_canonical = $page_og_url;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($page_description) ?>">
    <meta name="robots" content="<?= htmlspecialchars($page_robots) ?>">
    <?php if ($page_canonical !== ''): ?>
    <link rel="canonical" href="<?= htmlspecialchars($page_canonical) ?>">
    <?php endif; ?>
    <title><?= htmlspecialchars($page_og_title) ?></title>
    <meta property="og:type" content="<?= htmlspecialchars($page_og_type) ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars(SITE_NAME) ?>">
    <meta property="og:locale" content="th_TH">
    <meta property="og:title" content="<?= htmlspecialchars($page_og_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_og_description) ?>">
    <?php if ($page_og_url !== ''): ?>
    <meta property="og:url" content="<?= htmlspecialchars($page_og_url) ?>">
    <?php endif; ?>
    <?php if ($page_og_image !== ''): ?>
    <meta property="og:image" content="<?= htmlspecialchars($page_og_image) ?>">
    <meta property="og:image:secure_url" content="<?= htmlspecialchars($page_og_image) ?>">
    <?php if (!empty($page_og_image_width) && !empty($page_og_image_height)): ?>
    <meta property="og:image:width" content="<?= (int) $page_og_image_width ?>">
    <meta property="og:image:height" content="<?= (int) $page_og_image_height ?>">
    <?php endif; ?>
    <meta property="og:image:alt" content="<?= htmlspecialchars($page_og_image_alt) ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($page_og_title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($page_og_description) ?>">
    <?php if ($page_og_image !== ''): ?>
    <meta name="twitter:image" content="<?= htmlspecialchars($page_og_image) ?>">
    <?php endif; ?>
    <?php if (!empty($page_schema_json)): ?>
    <script type="application/ld+json"><?= $page_schema_json ?></script>
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('assets/css/fonts.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body data-form-endpoint="<?= htmlspecialchars(form_submit_endpoint()) ?>" data-site-name="<?= htmlspecialchars(SITE_NAME) ?>">
    <a class="skip-link" href="#main">ข้ามไปเนื้อหาหลัก</a>

    <header class="site-header" id="site-header">
        <div class="header-top">
            <div class="container header-top__inner">
                <div class="header-top__cluster">
                    <div class="header-top__links">
                        <a href="tel:1351" class="header-top__item">
                            <?= icon_svg('phone', 16) ?>
                            โทร <?= SITE_PHONE ?>
                        </a>
                        <a href="<?= htmlspecialchars(tel_href(CONTACT_PHONE_1)) ?>" class="header-top__item">
                            <?= icon_svg('phone', 16) ?>
                            <?= htmlspecialchars(CONTACT_PHONE_1) ?>
                        </a>
                    </div>
                    <?php render_contact_icons('header'); ?>
                </div>
                <div class="header-top__actions">
                    <a href="#" class="header-top__login">เข้าสู่ระบบ</a>
                    <span class="lang-switch" aria-label="ภาษา">ไทย</span>
                </div>
            </div>
        </div>

        <div class="header-main">
            <div class="container header-main__inner">
                <?php render_logo('header'); ?>

                <button type="button" class="nav-toggle" id="nav-toggle" aria-expanded="false" aria-controls="site-nav">
                    <span class="nav-toggle__bar"></span>
                    <span class="nav-toggle__bar"></span>
                    <span class="nav-toggle__bar"></span>
                    <span class="sr-only">เปิดเมนู</span>
                </button>

                <nav class="site-nav" id="site-nav" aria-label="เมนูหลัก">
                    <ul class="site-nav__list">
                        <li class="site-nav__item site-nav__item--has-sub">
                            <button type="button" class="site-nav__link site-nav__trigger <?= active_class('index.php') ?>" aria-expanded="false">
                                ผลิตภัณฑ์
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <?php
                            $mega_categories = plan_categories();
                            $mega_order = plan_category_menu_order();
                            ?>
                            <div class="mega-menu">
                                <div class="mega-menu__grid">
                                    <?php foreach ($mega_order as $catId):
                                        $cat = $mega_categories[$catId] ?? null;
                                        if ($cat === null) {
                                            continue;
                                        }
                                        $catPage = basename($cat['page']);
                                    ?>
                                    <a href="<?= htmlspecialchars(plan_category_page_url($catId)) ?>" class="mega-menu__link<?= is_active($catPage) ? ' is-current' : '' ?>">
                                        <strong><?= htmlspecialchars($cat['title']) ?></strong>
                                        <span><?= htmlspecialchars($cat['mega_desc']) ?></span>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </li>
                        <li class="site-nav__item">
                            <a href="<?= page_url('promotions.php') ?>" class="site-nav__link <?= active_class('promotions.php') ?>">โปรโมชัน</a>
                        </li>
                        <li class="site-nav__item">
                            <a href="<?= page_url('articles.php') ?>" class="site-nav__link <?= (is_active('articles.php') || is_active('article.php')) ? 'is-active' : '' ?>">บทความ</a>
                        </li>
                        <li class="site-nav__item">
                            <a href="<?= page_url('claims.php') ?>" class="site-nav__link <?= active_class('claims.php') ?>">การเคลม</a>
                        </li>
                        <li class="site-nav__item">
                            <a href="<?= page_url('about.php') ?>" class="site-nav__link <?= active_class('about.php') ?>">เกี่ยวกับ FWD</a>
                        </li>
                        <li class="site-nav__item">
                            <a href="<?= page_url('contact.php') ?>" class="site-nav__link <?= active_class('contact.php') ?>">ติดต่อเรา</a>
                        </li>
                    </ul>
                    <div class="header-cta-group">
                        <a href="<?= page_url('agent-apply.php') ?>" class="btn btn--outline header-cta header-cta--agent <?= active_class('agent-apply.php') ?>">สมัครตัวแทน</a>
                        <a href="<?= page_url('contact.php') ?>" class="btn btn--primary header-cta">ขอคำปรึกษาฟรี</a>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <main id="main">
