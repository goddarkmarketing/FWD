<?php
/**
 * ปุ่มลอยมุมขวาล่าง — เปิดช่องทางติดต่อ
 */
require_once __DIR__ . '/icons.php';

$fab_icon_size = 22;

$fab_actions = [
    [
        'href' => page_url('contact.php'),
        'label' => 'ขอคำปรึกษา',
        'icon' => icon_svg('chat', $fab_icon_size),
        'variant' => 'primary',
    ],
    [
        'href' => 'tel:1351',
        'label' => 'โทร ' . SITE_PHONE,
        'icon' => icon_svg('phone', $fab_icon_size),
        'variant' => 'fwd',
    ],
    [
        'href' => tel_href(CONTACT_PHONE_1),
        'label' => CONTACT_PHONE_1,
        'icon' => icon_svg('phone', $fab_icon_size),
        'variant' => 'phone',
    ],
    [
        'href' => CONTACT_LINE,
        'label' => 'LINE',
        'icon' => icon_brand_line($fab_icon_size),
        'variant' => 'line',
        'external' => true,
    ],
    [
        'href' => CONTACT_FACEBOOK,
        'label' => 'Facebook',
        'icon' => icon_brand_facebook($fab_icon_size),
        'variant' => 'facebook',
        'external' => true,
    ],
    [
        'href' => 'mailto:' . CONTACT_EMAIL,
        'label' => 'อีเมล',
        'icon' => icon_brand_gmail($fab_icon_size),
        'variant' => 'mail',
    ],
];
?>
<div class="contact-fab" id="contact-fab">
    <button
        type="button"
        class="contact-fab__backdrop"
        id="contact-fab-backdrop"
        aria-label="ปิดเมนูติดต่อ"
        tabindex="-1"
    ></button>
    <div class="contact-fab__panel">
        <ul class="contact-fab__list" id="contact-fab-list" aria-label="ช่องทางติดต่อ">
            <?php foreach ($fab_actions as $i => $action): ?>
            <li class="contact-fab__item" style="--fab-i: <?= (int) $i ?>">
                <a
                    href="<?= htmlspecialchars($action['href']) ?>"
                    class="contact-fab__action contact-fab__action--<?= htmlspecialchars($action['variant']) ?>"
                    <?php if (!empty($action['external'])): ?>
                    target="_blank"
                    rel="noopener noreferrer"
                    <?php endif; ?>
                >
                    <span class="contact-fab__chip"><?= htmlspecialchars($action['label']) ?></span>
                    <span class="contact-fab__icon" aria-hidden="true"><?= $action['icon'] ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <button
            type="button"
            class="contact-fab__toggle"
            id="contact-fab-toggle"
            aria-expanded="false"
            aria-controls="contact-fab-list"
            aria-label="เปิดช่องทางติดต่อ"
        >
            <span class="contact-fab__toggle-icon contact-fab__toggle-icon--open" aria-hidden="true">
                <?= icon_svg('chat', 24) ?>
            </span>
            <span class="contact-fab__toggle-icon contact-fab__toggle-icon--close" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" aria-hidden="true">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </span>
        </button>
    </div>
</div>
