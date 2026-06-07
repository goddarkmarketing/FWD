<?php
/**
 * Render one promo card.
 *
 * @param array{badge:string,badge_variant?:string,date:string,title:string,desc?:string,url:string,cta?:string} $promo
 */
function render_promo_card(array $promo): void
{
    $variant = $promo['badge_variant'] ?? 'orange';
    $variantClass = 'promo-card--' . $variant;
    $cta = $promo['cta'] ?? 'ดูรายละเอียด';
    $href = htmlspecialchars(page_url($promo['url']));
    ?>
<article class="promo-card <?= htmlspecialchars($variantClass) ?>">
    <div class="promo-card__accent" aria-hidden="true">
        <span class="promo-card__badge"><?= htmlspecialchars($promo['badge']) ?></span>
        <span class="promo-card__glow"></span>
        <span class="promo-card__ring"></span>
    </div>
    <div class="promo-card__body">
        <p class="promo-card__date">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <path d="M16 2v4M8 2v4M3 10h18"/>
            </svg>
            <?= htmlspecialchars($promo['date']) ?>
        </p>
        <h3><?= htmlspecialchars($promo['title']) ?></h3>
        <?php if (!empty($promo['desc'])): ?>
        <p class="promo-card__desc"><?= htmlspecialchars($promo['desc']) ?></p>
        <?php endif; ?>
        <a href="<?= $href ?>" class="promo-card__cta">
            <span><?= htmlspecialchars($cta) ?></span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M5 12h14M13 6l6 6-6 6"/>
            </svg>
        </a>
    </div>
</article>
    <?php
}
