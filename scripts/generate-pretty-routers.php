<?php
/**
 * สร้างโฟลเดอร์ /plan/{slug}/index.php และ /article/{slug}/index.php
 * ให้ URL แบบสวย ๆ เปิดได้บนโฮสต์ที่ไม่ rewrite หรือยังไม่ได้อัป .htaccess
 *
 * Usage: php scripts/generate-pretty-routers.php
 */
declare(strict_types=1);

$root = dirname(__DIR__);
require_once $root . '/includes/config.php';
require_once $root . '/includes/plan-helpers.php';
require_once $root . '/includes/articles-data.php';

$planRouter = <<<'PHP'
<?php
declare(strict_types=1);
$slug = basename(dirname(__FILE__));
$_GET['slug'] = $slug;
$script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/plan.php'));
$rootScript = dirname(dirname(dirname($script))) . '/plan.php';
$_SERVER['SCRIPT_NAME'] = $rootScript;
$_SERVER['PHP_SELF'] = $rootScript;
require dirname(__DIR__, 2) . '/plan.php';
PHP;

$articleRouter = <<<'PHP'
<?php
declare(strict_types=1);
$slug = basename(dirname(__FILE__));
$_GET['slug'] = $slug;
$script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/article.php'));
$rootScript = dirname(dirname(dirname($script))) . '/article.php';
$_SERVER['SCRIPT_NAME'] = $rootScript;
$_SERVER['PHP_SELF'] = $rootScript;
require dirname(__DIR__, 2) . '/article.php';
PHP;

$planCount = 0;
foreach (plan_catalog() as $item) {
    $slug = trim((string) ($item['slug'] ?? ''));
    if ($slug === '' || !preg_match('/^[a-z0-9\-]+$/', $slug)) {
        continue;
    }
    $dir = $root . '/plan/' . $slug;
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        fwrite(STDERR, "Cannot create {$dir}\n");
        exit(1);
    }
    file_put_contents($dir . '/index.php', $planRouter);
    $planCount++;
}

$articleCount = 0;
foreach (articles_all() as $item) {
    $slug = trim((string) ($item['slug'] ?? ''));
    if ($slug === '' || !preg_match('/^[a-z0-9\-]+$/', $slug)) {
        continue;
    }
    $dir = $root . '/article/' . $slug;
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        fwrite(STDERR, "Cannot create {$dir}\n");
        exit(1);
    }
    file_put_contents($dir . '/index.php', $articleRouter);
    $articleCount++;
}

// Prevent listing
file_put_contents($root . '/plan/index.php', "<?php\nheader('Location: ../products.php', true, 302);\nexit;\n");
file_put_contents($root . '/article/index.php', "<?php\nheader('Location: ../articles.php', true, 302);\nexit;\n");

echo "Generated plan routers: {$planCount}\n";
echo "Generated article routers: {$articleCount}\n";
