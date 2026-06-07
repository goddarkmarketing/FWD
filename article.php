<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/articles-data.php';

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$article = $slug !== '' ? article_by_slug($slug) : null;

if ($article === null) {
    http_response_code(404);
    $page_title = 'ไม่พบบทความ';
    $page_description = 'ไม่พบบทความที่ต้องการ';
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

$page_title = $article['title'];
$page_description = $article['excerpt'];
require_once __DIR__ . '/includes/header.php';
?>

<article class="article-detail">
    <header class="article-detail__hero">
        <div class="article-detail__hero-media">
            <img src="<?= htmlspecialchars(image_url($article['image'])) ?>" alt="">
        </div>
        <div class="container article-detail__hero-inner">
            <nav class="article-detail__breadcrumb" aria-label="breadcrumb">
                <a href="<?= htmlspecialchars(page_url('index.php')) ?>">หน้าแรก</a>
                <span aria-hidden="true"> / </span>
                <a href="<?= htmlspecialchars(page_url('articles.php')) ?>">บทความ</a>
            </nav>
            <p class="article-detail__meta">
                <span><?= htmlspecialchars($article['category']) ?></span>
                <span aria-hidden="true"> · </span>
                <time><?= htmlspecialchars($article['date']) ?></time>
                <span aria-hidden="true"> · </span>
                <span>อ่าน <?= (int) $article['read_min'] ?> นาที</span>
            </p>
            <h1 class="article-detail__title"><?= htmlspecialchars($article['title']) ?></h1>
            <p class="article-detail__lead"><?= htmlspecialchars($article['excerpt']) ?></p>
        </div>
    </header>

    <div class="container article-detail__body">
        <?php foreach ($article['body'] as $paragraph): ?>
        <p><?= htmlspecialchars($paragraph) ?></p>
        <?php endforeach; ?>
        <p class="article-detail__back">
            <a href="<?= htmlspecialchars(page_url('articles.php')) ?>">← กลับไปหน้าบทความ</a>
        </p>
    </div>
</article>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
