<?php
/**
 * ดาวน์โหลด HTML หน้าผลิตภัณฑ์ที่ยังไม่มีใน data/fwd-pages/
 */
$root = dirname(__DIR__);
$catalog = json_decode(file_get_contents($root . '/data/fwd-urls/all-products.json'), true);
$pagesDir = $root . '/data/fwd-pages';

$ctx = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36\r\nAccept-Language: th-TH,th;q=0.9\r\n",
        'timeout' => 45,
    ],
]);

$aliases = ['fwd-power-saving' => 'fwd-power-saving-12-6'];
$ok = 0;
$fail = 0;

foreach ($catalog as $row) {
    $slug = $row['slug'];
    $pageSlug = $aliases[$slug] ?? $slug;
    $file = $pagesDir . '/' . $pageSlug . '.htm';
    if (is_readable($file)) {
        continue;
    }
    $url = $row['fwd_url'] ?? null;
    if (!$url) {
        echo "$slug: no URL\n";
        $fail++;
        continue;
    }
    echo "$slug ... ";
    $html = @file_get_contents($url, false, $ctx);
    if ($html === false || strlen($html) < 1000) {
        echo "FAIL\n";
        $fail++;
        continue;
    }
    file_put_contents($pagesDir . '/' . $slug . '.htm', $html);
    echo 'OK ' . strlen($html) . " bytes\n";
    $ok++;
    usleep(300000);
}

echo "\nFetched: $ok ok, $fail fail\n";
