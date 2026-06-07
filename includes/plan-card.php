<?php
/**
 * Render one plan card. Expects $plan array.
 */
require_once __DIR__ . '/filter-icons.php';

$plan_search = strtolower(
    ($plan['category_label'] ?? '') . ' ' .
    ($plan['title'] ?? '') . ' ' .
    ($plan['desc'] ?? '') . ' ' .
    ($plan['discount'] ?? '')
);
?>
<article
    class="plan-card"
    data-category="<?= htmlspecialchars($plan['category']) ?>"
    data-search="<?= htmlspecialchars($plan_search) ?>"
>
    <?php
    $detail_href = page_url($plan['url']);
    ?>
    <a href="<?= htmlspecialchars($detail_href) ?>" class="plan-card__media">
        <?php if (!empty($plan['discount'])): ?>
        <span class="plan-card__badge"><?= htmlspecialchars($plan['discount']) ?></span>
        <?php endif; ?>
        <img
            src="<?= htmlspecialchars(image_url($plan['image'])) ?>"
            alt="<?= htmlspecialchars($plan['title']) ?>"
            loading="lazy"
        >
    </a>
    <div class="plan-card__body">
        <p class="plan-card__category">
            <span class="plan-card__cat-icon"><?= filter_icon_svg($plan['category']) ?></span>
            <?= htmlspecialchars($plan['category_label']) ?>
        </p>
        <h3 class="plan-card__title">
            <a href="<?= htmlspecialchars($detail_href) ?>"><?= htmlspecialchars($plan['title']) ?></a>
        </h3>
        <p class="plan-card__desc"><?= htmlspecialchars($plan['desc']) ?></p>
        <div class="plan-card__foot">
            <?php if (!empty($plan['tags'])): ?>
            <div class="plan-card__tags" aria-label="คุณสมบัติแผนประกัน">
                <?php foreach ($plan['tags'] as $tag): ?>
                <span class="plan-card__tag"><?= htmlspecialchars($tag) ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <a href="<?= htmlspecialchars($detail_href) ?>" class="plan-card__btn">รายละเอียด</a>
        </div>
    </div>
</article>
