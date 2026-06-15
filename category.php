<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/plan-helpers.php';

$category_slug = isset($_GET['cat']) ? trim((string) $_GET['cat']) : '';
$categories = plan_categories();

if ($category_slug === '' || !isset($categories[$category_slug])) {
    http_response_code(404);
    $page_title = 'ไม่พบหมวดหมู่';
    $page_description = 'ไม่พบหมวดหมู่ที่ต้องการ';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="page-hero">
        <div class="container">
            <h1>ไม่พบหมวดหมู่</h1>
            <p class="page-hero__lead"><a href="<?= htmlspecialchars(page_url('products.php')) ?>">กลับไปหน้าผลิตภัณฑ์</a></p>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$config = $categories[$category_slug];
$page_title = $config['title'] ?? 'ผลิตภัณฑ์';
$page_description = $config['lead'] ?? '';
require_once __DIR__ . '/includes/header.php';

require __DIR__ . '/includes/category-catalog-page.php';
require_once __DIR__ . '/includes/footer.php';
