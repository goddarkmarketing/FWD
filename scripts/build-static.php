<?php
/**
 * Pre-render PHP site to static HTML for GitHub Pages.
 *
 * Usage: php scripts/build-static.php [/FWD]
 * Output: docs/
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$baseUrl = $argv[1] ?? '/FWD';
$baseUrl = '/' . trim($baseUrl, '/');
if ($baseUrl === '/') {
    $baseUrl = '';
}

putenv('FWD_STATIC_BUILD=1');
putenv('FWD_BASE_URL=' . $baseUrl);

$dist = $root . '/docs';

function rrmdir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    $items = scandir($dir);
    if ($items === false) {
        return;
    }
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            rrmdir($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
}

function rcopy(string $src, string $dest): void
{
    if (!is_dir($src)) {
        return;
    }
    if (!is_dir($dest)) {
        mkdir($dest, 0777, true);
    }
    $items = scandir($src);
    if ($items === false) {
        return;
    }
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $from = $src . DIRECTORY_SEPARATOR . $item;
        $to = $dest . DIRECTORY_SEPARATOR . $item;
        if (is_dir($from)) {
            rcopy($from, $to);
        } else {
            copy($from, $to);
        }
    }
}

function write_file(string $path, string $contents): void
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($path, $contents);
}

function render_php(string $phpFile, array $get, string $scriptBasename): string
{
    global $root, $baseUrl;

    $renderScript = $root . '/scripts/render-one.php';
    $queryJson = json_encode($get, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $cmd = [PHP_BINARY, $renderScript, $baseUrl, $phpFile, $scriptBasename, $queryJson];

    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $process = proc_open($cmd, $descriptors, $pipes, $root);
    if (!is_resource($process)) {
        throw new RuntimeException('Failed to start render process for ' . $phpFile);
    }

    fclose($pipes[0]);
    $html = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    if ($exitCode !== 0 || !is_string($html) || $html === '') {
        throw new RuntimeException(
            'Failed to render ' . $phpFile . ' (exit ' . $exitCode . '): ' . trim((string) $stderr)
        );
    }

    return postprocess_html($html);
}

function postprocess_html(string $html): string
{
    global $baseUrl;

    $b = preg_quote($baseUrl, '#');

    // Absolute URLs that still reference .php
    $html = preg_replace('#' . $b . '/index\.php([^"\'>\s]*)#', $baseUrl . '/$1', $html);
    $html = preg_replace('#' . $b . '/plan\.php\?slug=([^"&\'>\s]+)#', $baseUrl . '/plan/$1/', $html);
    $html = preg_replace('#' . $b . '/article\.php\?slug=([^"&\'>\s]+)#', $baseUrl . '/article/$1/', $html);
    $html = preg_replace('#' . $b . '/([a-z0-9-]+)\.php#', $baseUrl . '/$1.html', $html);

    // Relative links in breadcrumbs and legacy markup
    $html = preg_replace('#href="index\.php([^"]*)"#', 'href="' . $baseUrl . '/$1"', $html);
    $html = preg_replace("#href='index\.php([^']*)'#", "href='" . $baseUrl . '/$1\'', $html);
    $html = preg_replace('#href="(contact|articles|promotions|claims|about|products|health-insurance|critical-illness|life-insurance|savings-insurance|investment-linked|accident-insurance)\.php([^"]*)"#', 'href="' . $baseUrl . '/$1.html$2"', $html);
    $html = preg_replace('#href="plan\.php\?slug=([^"]+)"#', 'href="' . $baseUrl . '/plan/$1/"', $html);
    $html = preg_replace('#href="article\.php\?slug=([^"]+)"#', 'href="' . $baseUrl . '/article/$1/"', $html);

    // product-mock.php → placeholder SVG (if any remain)
    $html = preg_replace('#' . $b . '/product-mock\.php\?cat=([a-z0-9-]+)&amp;n=(\d+)#', $baseUrl . '/assets/mock/$1-$2.svg', $html);
    $html = preg_replace('#product-mock\.php\?cat=([a-z0-9-]+)&amp;n=(\d+)#', $baseUrl . '/assets/mock/$1-$2.svg', $html);
    $html = preg_replace('#product-mock\.php\?cat=([a-z0-9-]+)&n=(\d+)#', $baseUrl . '/assets/mock/$1-$2.svg', $html);

    return $html;
}

function redirect_html(string $target): string
{
    $escaped = htmlspecialchars($target, ENT_QUOTES, 'UTF-8');
    return '<!DOCTYPE html><html lang="th"><head><meta charset="UTF-8">'
        . '<meta http-equiv="refresh" content="0;url=' . $escaped . '">'
        . '<link rel="canonical" href="' . $escaped . '">'
        . '<script>location.replace(' . json_encode($target, JSON_UNESCAPED_SLASHES) . ');</script>'
        . '</head><body><p><a href="' . $escaped . '">ไปยังหน้าที่เกี่ยวข้อง</a></p></body></html>';
}

echo "Building static site to docs/ (base: {$baseUrl})\n";

if (is_dir($dist)) {
    rrmdir($dist);
}
mkdir($dist, 0777, true);

$staticPages = [
    'index.php',
    'products.php',
    'health-insurance.php',
    'critical-illness.php',
    'life-insurance.php',
    'savings-insurance.php',
    'investment-linked.php',
    'accident-insurance.php',
    'articles.php',
    'promotions.php',
    'contact.php',
    'agent-apply.php',
    'claims.php',
    'about.php',
];

foreach ($staticPages as $file) {
    echo "  page: {$file}\n";
    $html = render_php($file, [], basename($file));
    if ($file === 'index.php') {
        write_file($dist . '/index.html', $html);
    } else {
        $name = preg_replace('/\.php$/', '.html', $file);
        write_file($dist . '/' . $name, $html);
    }
}

require_once $root . '/includes/config.php';
require_once $root . '/includes/articles-data.php';

$planSlugs = array_keys(plan_details_all());
echo '  plans: ' . count($planSlugs) . " pages\n";
foreach ($planSlugs as $slug) {
    $html = render_php('plan.php', ['slug' => $slug], 'plan.php');
    write_file($dist . '/plan/' . $slug . '/index.html', $html);
}

$articleSlugs = array_column(articles_all(), 'slug');
echo '  articles: ' . count($articleSlugs) . " pages\n";
foreach ($articleSlugs as $slug) {
    $html = render_php('article.php', ['slug' => $slug], 'article.php');
    write_file($dist . '/article/' . $slug . '/index.html', $html);
}

$redirects = [
    'product-e-health' => static_page_url('plan.php?slug=precious-care'),
    'product-e-stroke' => static_page_url('plan.php?slug=ci-all-in-one'),
    'product-all-in-one' => static_page_url('plan.php?slug=easy-all-in-one'),
];
foreach ($redirects as $name => $target) {
    write_file($dist . '/' . $name . '.html', redirect_html($target));
}

echo "  copying assets/\n";
rcopy($root . '/assets', $dist . '/assets');

// Pre-render product-mock SVGs for fallback images
$mockDir = $dist . '/assets/mock';
if (!is_dir($mockDir)) {
    mkdir($mockDir, 0777, true);
}
$cats = ['life-accident', 'health', 'critical', 'investment', 'savings', 'all'];
for ($n = 1; $n <= 5; $n++) {
    foreach ($cats as $cat) {
        $_GET = ['cat' => $cat, 'n' => (string) $n];
        ob_start();
        include $root . '/product-mock.php';
        write_file($mockDir . '/' . $cat . '-' . $n . '.svg', ob_get_clean());
    }
}

write_file($dist . '/.nojekyll', '');
write_file($dist . '/404.html', redirect_html($baseUrl . '/'));

echo "Done. " . count(glob($dist . '/**/*.html', GLOB_BRACE) ?: []) . "+ HTML files in docs/\n";
echo "Preview URL: https://goddarkmarketing.github.io" . ($baseUrl ?: '') . "/\n";
