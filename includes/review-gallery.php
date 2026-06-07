<?php
/**
 * Load review screenshot paths from assets/รีวิว
 */
function review_gallery_images(): array
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $cached = [];
    $base = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'รีวิว';
    if (!is_dir($base)) {
        return $cached;
    }

    $files = [];
    foreach (['jpg', 'jpeg', 'png', 'webp', 'gif'] as $ext) {
        $lower = glob($base . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*.' . $ext) ?: [];
        $upper = glob($base . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . '*.' . strtoupper($ext)) ?: [];
        $files = array_merge($files, $lower, $upper);
    }

    $files = array_unique($files);
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);

    foreach ($files as $full) {
        $rel = 'assets/รีวิว/' . str_replace('\\', '/', substr($full, strlen($base) + 1));
        $cached[] = $rel;
    }

    return $cached;
}

function render_gallery_marquee_row(array $images, string $direction): void
{
    if (empty($images)) {
        return;
    }

    $dirClass = $direction === 'right' ? 'gallery-marquee__row--right' : 'gallery-marquee__row--left';
    $loop = array_merge($images, $images);
    ?>
    <div class="gallery-marquee__row <?= $dirClass ?>">
        <div class="gallery-marquee__track">
            <?php foreach ($loop as $i => $src): ?>
            <figure class="gallery-marquee__item">
                <img
                    src="<?= htmlspecialchars(media_url($src)) ?>"
                    alt="รีวิวจากลูกค้า FWD <?= (int) ($i % count($images)) + 1 ?>"
                    loading="lazy"
                    decoding="async"
                >
            </figure>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
