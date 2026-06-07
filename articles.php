<?php
$page_title = 'บทความ';
$page_description = 'บทความความรู้เรื่องประกัน — เคล็ดลับเลือกซื้อและวางแผนคุ้มครอง';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/articles-data.php';
require_once __DIR__ . '/includes/article-card.php';
$articles = articles_all();
?>

<section class="page-hero page-hero--orange">
    <div class="container">
        <nav class="page-hero__breadcrumb" aria-label="breadcrumb">
            <a href="<?= htmlspecialchars(page_url('index.php')) ?>">หน้าแรก</a> / บทความ
        </nav>
        <h1>บทความความรู้เรื่องประกัน</h1>
        <p class="page-hero__lead">เคล็ดลับและข้อมูลที่ช่วยให้คุณเลือกประกันได้อย่างมั่นใจ</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="article-grid article-grid--page">
            <?php foreach ($articles as $article): ?>
                <div class="reveal"><?php render_article_card($article); ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
