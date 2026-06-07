<?php
/**
 * Official FWD logo image (unchanged asset from brand file).
 *
 * @param 'header'|'footer' $variant
 */
function render_logo(string $variant = 'header'): void
{
    $href = htmlspecialchars(page_url('index.php'));
    $class = 'logo logo--' . ($variant === 'footer' ? 'footer' : 'header');
    $src = htmlspecialchars(image_url(SITE_LOGO_PATH));
    ?>
<a href="<?= $href ?>" class="<?= $class ?>">
    <img
        src="<?= $src ?>"
        alt="FWD by kruda — เว็บไซต์ตัวแทน FWD ประกันชีวิต"
        class="logo__img"
        width="320"
        height="120"
        decoding="async"
    >
</a>
    <?php
}

/**
 * Social / contact icon strip (header top or footer bottom).
 *
 * @param 'header'|'footer-bar' $context
 */
function render_contact_icons(string $context = 'header'): void
{
    require_once __DIR__ . '/icons.php';
    $large = $context === 'footer-bar';
    $iconSize = $large ? 18 : 16;
    $class = 'contact-icons contact-icons--' . $context;
    ?>
<nav class="<?= htmlspecialchars($class) ?>" aria-label="ช่องทางติดต่อ">
    <a href="<?= htmlspecialchars(tel_href(CONTACT_PHONE_1)) ?>" class="contact-icons__btn" aria-label="โทร <?= htmlspecialchars(CONTACT_PHONE_1) ?>">
        <?= icon_svg('phone', $iconSize) ?>
    </a>
    <a href="mailto:<?= htmlspecialchars(CONTACT_EMAIL) ?>" class="contact-icons__btn" aria-label="อีเมล <?= htmlspecialchars(CONTACT_EMAIL) ?>">
        <?= icon_svg('mail', $iconSize) ?>
    </a>
    <a href="<?= htmlspecialchars(CONTACT_LINE) ?>" class="contact-icons__btn" target="_blank" rel="noopener noreferrer" aria-label="LINE">
        <?= icon_svg('line', $iconSize) ?>
    </a>
    <a href="<?= htmlspecialchars(CONTACT_FACEBOOK) ?>" class="contact-icons__btn" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
        <?= icon_svg('facebook', $iconSize) ?>
    </a>
</nav>
    <?php
}
