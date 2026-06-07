<?php
$files = glob(__DIR__ . '/../data/fwd-pages/*.htm');
foreach ($files as $file) {
    $html = file_get_contents($file);
    $slug = basename($file, '.htm');
    $final = '';
    if (preg_match('/<link rel="canonical"\s+href="([^"]+)"/', $html, $m)) {
        $final = $m[1];
    }
    $is404 = stripos($html, '/th/404') !== false || stripos($html, 'ไม่พบหน้าที่ค้นหา') !== false;
    $hasProduct = false;
    if (preg_match('/id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m)) {
        $data = json_decode($m[1], true);
        $hasProduct = !empty($data['props']['pageProps']['product']);
    }
    if ($is404 || !$hasProduct) {
        echo "$slug | 404=" . ($is404 ? 'yes' : 'no') . " | product=" . ($hasProduct ? 'yes' : 'no') . " | canon=$final\n";
    }
}
