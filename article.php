<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/articles-data.php';
require_once __DIR__ . '/includes/article-helpers.php';

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$article = $slug !== '' ? article_by_slug($slug) : null;

if ($article === null) {
    http_response_code(404);
    $page_title = 'ไม่พบบทความ';
    $page_description = 'ไม่พบบทความที่ต้องการ';
    $page_robots = 'noindex, nofollow';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="page-hero">
        <div class="container">
            <h1>ไม่พบบทความ</h1>
            <p class="page-hero__lead">บทความที่คุณค้นหาไม่มีในระบบ</p>
            <a href="<?= htmlspecialchars(page_url('articles.php')) ?>" class="btn btn--primary" style="margin-top: 1.5rem;">กลับไปหน้าบทความ</a>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$article = article_normalize($article);
$page_title = $article['title'];
$page_description = article_seo_description($article);
$page_robots = article_robots_meta($article);
$page_canonical = ensure_absolute_url(article_canonical_url($article));
$page_og_title = article_seo_title($article) . ' | ' . SITE_NAME;
$page_og_description = $page_description;
$articleOgPath = article_og_image($article);
$page_og_image = ensure_absolute_url(image_url($articleOgPath));
$articleOgFile = dirname(__DIR__) . '/' . str_replace('\\', '/', ltrim($articleOgPath, '/'));
if (is_file($articleOgFile) && function_exists('getimagesize')) {
    $articleOgInfo = @getimagesize($articleOgFile);
    if (is_array($articleOgInfo) && !empty($articleOgInfo[0]) && !empty($articleOgInfo[1])) {
        $page_og_image_width = (int) $articleOgInfo[0];
        $page_og_image_height = (int) $articleOgInfo[1];
    }
}
$page_og_image_alt = $article['title'];
$page_og_type = 'article';
$page_og_url = $page_canonical;
$page_schema_json = article_schema_json($article);
$imageAlt = $article['image_alt'] !== '' ? $article['image_alt'] : $article['title'];
$readMin = article_reading_time($article);

require_once __DIR__ . '/includes/header.php';
?>

<article class="article-detail" itemscope itemtype="https://schema.org/Article">
    <header class="article-detail__hero">
        <div class="article-detail__hero-media">
            <img src="<?= htmlspecialchars(image_url($article['image'])) ?>" alt="<?= htmlspecialchars($imageAlt) ?>" itemprop="image">
        </div>
        <div class="container article-detail__hero-inner">
            <nav class="article-detail__breadcrumb" aria-label="breadcrumb">
                <a href="<?= htmlspecialchars(page_url('index.php')) ?>">หน้าแรก</a>
                <span aria-hidden="true"> / </span>
                <a href="<?= htmlspecialchars(page_url('articles.php')) ?>">บทความ</a>
            </nav>
            <p class="article-detail__meta">
                <span itemprop="articleSection"><?= htmlspecialchars($article['category']) ?></span>
                <span aria-hidden="true"> · </span>
                <time itemprop="datePublished"><?= htmlspecialchars($article['date']) ?></time>
                <span aria-hidden="true"> · </span>
                <span>อ่าน <?= (int) $readMin ?> นาที</span>
            </p>
            <h1 class="article-detail__title" itemprop="headline"><?= htmlspecialchars($article['title']) ?></h1>
            <p class="article-detail__lead" itemprop="description"><?= htmlspecialchars($article['excerpt']) ?></p>
        </div>
    </header>

    <div class="container article-detail__body article-prose" itemprop="articleBody">
        <?= article_render_content($article) ?>
        <p class="article-detail__back">
            <a href="<?= htmlspecialchars(page_url('articles.php')) ?>">← กลับไปหน้าบทความ</a>
        </p>
    </div>
</article>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
