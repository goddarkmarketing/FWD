<?php
require_once __DIR__ . '/includes/config.php';

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$plan = $slug !== '' ? plan_detail($slug) : null;

if ($plan === null) {
    http_response_code(404);
    $page_title = 'ไม่พบแผนประกัน';
    $page_description = 'ไม่พบรายละเอียดแผนประกันที่ต้องการ';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="page-hero">
        <div class="container">
            <h1>ไม่พบแผนประกัน</h1>
            <p class="page-hero__lead">แผนที่คุณค้นหาไม่มีในระบบ</p>
            <a href="<?= htmlspecialchars(page_url('index.php#plans')) ?>" class="btn btn--primary" style="margin-top: 1.5rem;">กลับไปเลือกแผนประกัน</a>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = $plan['title'];
$page_description = $plan['meta'] ?? $plan['tagline'];
$extra_scripts = [asset('assets/js/plan-detail.js')];
require_once __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/plan-detail-view.php';
require_once __DIR__ . '/includes/footer.php';
