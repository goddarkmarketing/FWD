    </main>

<?php
require_once __DIR__ . '/logo.php';
require_once __DIR__ . '/icons.php';
?>
    <footer class="site-footer">
        <div class="footer-cta">
            <div class="container footer-cta__inner">
                <div class="footer-cta__text">
                    <h2>ให้ผู้เชี่ยวชาญช่วยเลือกแผนที่เหมาะกับคุณ</h2>
                    <p>ฝากข้อมูลไว้ เราจะติดต่อกลับโดยเร็วที่สุด ไม่มีค่าใช้จ่าย</p>
                </div>
                <a href="<?= page_url('contact.php') ?>" class="btn btn--white">ขอคำปรึกษาฟรี</a>
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
                            <li><a href="#">นโยบายความเป็นส่วนตัว</a></li>
                            <li><a href="#">ข้อกำหนดและเงื่อนไข</a></li>
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
                        <p>&copy; <?= date('Y') ?> เอฟดับบลิวดี ประเทศไทย สงวนลิขสิทธิ์</p>
                        <p class="footer-note">FWD Life Insurance Public Company Limited — เว็บไซต์ตัวอย่างเพื่อการสาธิต</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <?php require __DIR__ . '/contact-fab.php'; ?>

    <div class="cookie-banner" id="cookie-banner" role="dialog" aria-label="คุกกี้">
        <p>เราใช้คุกกี้เพื่อมอบประสบการณ์ที่ดีที่สุดบนเว็บไซต์ การใช้งานต่อถือว่าคุณยอมรับนโยบายความเป็นส่วนตัวของเรา</p>
        <button type="button" class="btn btn--primary btn--sm" id="cookie-accept">ตกลง</button>
    </div>

    <script src="<?= asset('assets/js/main.js') ?>"></script>
    <?php if (!empty($extra_scripts)): ?>
        <?php foreach ($extra_scripts as $script): ?>
    <script src="<?= htmlspecialchars($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
