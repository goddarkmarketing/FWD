<?php
/**
 * Render one article card.
 *
 * @param array $article
 */
function render_article_card(array $article): void
{
    $href = article_url($article['slug']);
    ?>
<article class="article-card">
    <a href="<?= htmlspecialchars($href) ?>" class="article-card__media">
        <img src="<?= htmlspecialchars(image_url($article['image'])) ?>" alt="" loading="lazy">
    </a>
    <div class="article-card__body">
        <p class="article-card__meta">
            <span class="article-card__category"><?= htmlspecialchars($article['category']) ?></span>
            <span class="article-card__dot" aria-hidden="true">·</span>
            <time datetime=""><?= htmlspecialchars($article['date']) ?></time>
        </p>
        <h3 class="article-card__title">
            <a href="<?= htmlspecialchars($href) ?>"><?= htmlspecialchars($article['title']) ?></a>
        </h3>
        <p class="article-card__excerpt"><?= htmlspecialchars($article['excerpt']) ?></p>
        <a href="<?= htmlspecialchars($href) ?>" class="article-card__link">อ่านต่อ →</a>
    </div>
</article>
    <?php
}
