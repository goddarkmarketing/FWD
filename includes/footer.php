    </main>

<?php
require_once __DIR__ . '/logo.php';
require_once __DIR__ . '/icons.php';
$cmsFooter = cms_load('footer') ?? [];
$footerCtaTitle = $cmsFooter['cta_title'] ?? 'ให้ผู้เชี่ยวชาญช่วยเลือกแผนที่เหมาะกับคุณ';
$footerCtaDesc = $cmsFooter['cta_desc'] ?? 'ฝากข้อมูลไว้ เราจะติดต่อกลับโดยเร็วที่สุด ไม่มีค่าใช้จ่าย';
$footerCtaButton = $cmsFooter['cta_button'] ?? 'ขอคำปรึกษาฟรี';
$footerCopyright = $cmsFooter['copyright'] ?? ('© ' . date('Y') . ' เอฟดับบลิวดี ประเทศไทย สงวนลิขสิทธิ์');
$footerDisclaimer = $cmsFooter['disclaimer'] ?? 'FWD Life Insurance Public Company Limited — เว็บไซต์ตัวอย่างเพื่อการสาธิต';
$footerCookieText = $cmsFooter['cookie_text'] ?? 'เราใช้คุกกี้เพื่อมอบประสบการณ์ที่ดีที่สุดบนเว็บไซต์ การใช้งานต่อถือว่าคุณยอมรับนโยบายความเป็นส่วนตัวของเรา';
$footerCookieAccept = $cmsFooter['cookie_accept'] ?? 'ตกลง';
$footerPrivacyUrl = $cmsFooter['privacy_url'] ?? '#';
$footerTermsUrl = $cmsFooter['terms_url'] ?? '#';
?>
    <footer class="site-footer">
        <div class="footer-cta">
            <div class="container footer-cta__inner">
                <div class="footer-cta__text">
                    <h2><?= htmlspecialchars($footerCtaTitle) ?></h2>
                    <p><?= htmlspecialchars($footerCtaDesc) ?></p>
                </div>
                <a href="<?= page_url('contact.php') ?>" class="btn btn--white"><?= htmlspecialchars($footerCtaButton) ?></a>
            </div>
        </div>

        <div class="footer-main">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-brand">
                        <?php render_logo('footer'); ?>
                        <p class="footer-tagline"><?= SITE_TAGLINE ?></p>
                        <p class="footer-phone">
                            <a href="tel:1351"><?= icon_svg('phone', 16) ?> สายด่วน FWD <?= SITE_PHONE ?></a>
                        </p>
                        <div class="footer-agent">
                            <p class="footer-agent__office"><?= htmlspecialchars(AGENT_OFFICE_NAME) ?></p>
                            <p class="footer-agent__license">เลขที่ใบอนุญาต: <?= htmlspecialchars(AGENT_LICENSE_NO) ?></p>
                            <?php if (AGENT_LICENSE_IMAGE !== ''): ?>
                            <button type="button" class="btn btn--outline btn--sm footer-agent__btn" id="agent-license-open" aria-haspopup="dialog" aria-controls="agent-license-modal">ใบอนุญาต</button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="footer-col">
                        <h3>ผลิตภัณฑ์</h3>
                        <ul>
                            <li><a href="<?= htmlspecialchars(page_url('index.php#plans')) ?>">ผลิตภัณฑ์ทั้งหมด</a></li>
                            <?php foreach (plan_category_menu_order() as $catId):
                                $cat = plan_categories()[$catId] ?? null;
                                if ($cat === null) {
                                    continue;
                                }
                            ?>
                            <li><a href="<?= htmlspecialchars(plan_category_page_url($catId)) ?>"><?= htmlspecialchars($cat['title']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="footer-col">
                        <h3>บริการ</h3>
                        <ul>
                            <li><a href="<?= page_url('claims.php') ?>">การเคลมประกัน</a></li>
                            <li><a href="<?= page_url('promotions.php') ?>">โปรโมชัน</a></li>
                            <li><a href="<?= page_url('articles.php') ?>">บทความ</a></li>
                            <li><a href="<?= page_url('contact.php') ?>">ติดต่อเรา</a></li>
                            <li><a href="<?= htmlspecialchars($footerPrivacyUrl) ?>">นโยบายความเป็นส่วนตัว</a></li>
                            <li><a href="<?= htmlspecialchars($footerTermsUrl) ?>">ข้อกำหนดและเงื่อนไข</a></li>
                        </ul>
                    </div>

                    <div class="footer-col">
                        <h3>เกี่ยวกับเรา</h3>
                        <ul>
                            <li><a href="<?= page_url('about.php') ?>">เกี่ยวกับ FWD</a></li>
                            <li><a href="https://www.fwd.com/" target="_blank" rel="noopener noreferrer">FWD Group</a></li>
                            <li><a href="<?= page_url('agent-apply.php') ?>">ร่วมงานกับเรา</a></li>
                            <li><a href="<?= page_url('articles.php') ?>">ข่าวสารและกิจกรรม</a></li>
                        </ul>
                    </div>

                    <div class="footer-col footer-col--contact">
                        <h3>ติดต่อที่ปรึกษา</h3>
                        <ul class="footer-contact">
                            <li>
                                <a href="mailto:<?= htmlspecialchars(CONTACT_EMAIL) ?>" class="footer-contact__row">
                                    <span class="footer-contact__icon" aria-hidden="true"><?= icon_svg('mail', 18) ?></span>
                                    <span class="footer-contact__text">
                                        <span class="footer-contact__label">อีเมล</span>
                                        <?= htmlspecialchars(CONTACT_EMAIL) ?>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= htmlspecialchars(tel_href(CONTACT_PHONE_1)) ?>" class="footer-contact__row">
                                    <span class="footer-contact__icon" aria-hidden="true"><?= icon_svg('phone', 18) ?></span>
                                    <span class="footer-contact__text">
                                        <span class="footer-contact__label">โทรศัพท์</span>
                                        <?= htmlspecialchars(CONTACT_PHONE_1) ?>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= htmlspecialchars(tel_href(CONTACT_PHONE_2_RAW)) ?>" class="footer-contact__row">
                                    <span class="footer-contact__icon" aria-hidden="true"><?= icon_svg('phone', 18) ?></span>
                                    <span class="footer-contact__text">
                                        <span class="footer-contact__label">โทรศัพท์</span>
                                        <?= htmlspecialchars(CONTACT_PHONE_2) ?>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= htmlspecialchars(CONTACT_FACEBOOK) ?>" class="footer-contact__row" target="_blank" rel="noopener noreferrer">
                                    <span class="footer-contact__icon" aria-hidden="true"><?= icon_facebook_brand(20) ?></span>
                                    <span class="footer-contact__text">
                                        <span class="footer-contact__label">Facebook</span>
                                        <?= htmlspecialchars(CONTACT_FACEBOOK_NAME) ?>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= htmlspecialchars(CONTACT_LINE) ?>" class="footer-contact__row" target="_blank" rel="noopener noreferrer">
                                    <span class="footer-contact__icon" aria-hidden="true"><?= icon_line_brand(20) ?></span>
                                    <span class="footer-contact__text">
                                        <span class="footer-contact__label">LINE</span>
                                        เพิ่มเพื่อน LINE
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="footer-bottom">
                    <?php render_contact_icons('footer-bar'); ?>
                    <div class="footer-bottom__copy">
                        <p><?= htmlspecialchars($footerCopyright) ?></p>
                        <p class="footer-note"><?= htmlspecialchars($footerDisclaimer) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <?php if (defined('AGENT_LICENSE_IMAGE') && AGENT_LICENSE_IMAGE !== ''): ?>
    <div class="license-modal" id="agent-license-modal" role="dialog" aria-modal="true" aria-labelledby="agent-license-title" aria-hidden="true">
        <div class="license-modal__backdrop" data-license-close></div>
        <div class="license-modal__panel">
            <button type="button" class="license-modal__close" data-license-close aria-label="ปิด">&times;</button>
            <h2 class="license-modal__title" id="agent-license-title">ใบอนุญาตตัวแทนประกันชีวิต</h2>
            <p class="license-modal__meta"><?= htmlspecialchars(AGENT_OFFICE_NAME) ?> · เลขที่ <?= htmlspecialchars(AGENT_LICENSE_NO) ?></p>
            <div class="license-modal__image-wrap">
                <img src="<?= htmlspecialchars(image_url(AGENT_LICENSE_IMAGE)) ?>" alt="ใบอนุญาตตัวแทนประกันชีวิต เลขที่ <?= htmlspecialchars(AGENT_LICENSE_NO) ?>" width="640" height="400" loading="lazy" decoding="async">
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php require __DIR__ . '/contact-fab.php'; ?>

    <div class="cookie-banner" id="cookie-banner" role="dialog" aria-label="คุกกี้">
        <p><?= htmlspecialchars($footerCookieText) ?></p>
        <button type="button" class="btn btn--primary btn--sm" id="cookie-accept"><?= htmlspecialchars($footerCookieAccept) ?></button>
    </div>

    <script src="<?= asset('assets/js/main.js') ?>"></script>
    <?php if (!empty($extra_scripts)): ?>
        <?php foreach ($extra_scripts as $script): ?>
    <script src="<?= htmlspecialchars($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
