<?php
/**
 * Fetch /_next/data/{buildId}/{path}.json for product pages missing SSR product.
 */
require_once __DIR__ . '/../includes/fwd-parse-htm.php';

$root = dirname(__DIR__);
$catalog = json_decode(file_get_contents($root . '/data/fwd-urls/all-products.json'), true);
$pagesDir = $root . '/data/fwd-pages';
$ctx = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\nAccept: application/json\r\nAccept-Language: th-TH\r\n",
        'timeout' => 45,
    ],
]);

$ok = 0;
$fail = 0;

foreach ($catalog as $row) {
    $slug = $row['slug'];
    $htm = $pagesDir . '/' . $slug . '.htm';
    if (!is_readable($htm)) {
        continue;
    }
    $html = file_get_contents($htm);
    $existing = fwd_extract_next_data($html);
    if (!empty($existing['props']['pageProps']['product']['sections'])) {
        continue;
    }
    if (!preg_match('/"buildId":"([^"]+)"/', $html, $bm)) {
        $fail++;
        echo "$slug: no buildId\n";
        continue;
    }
    $buildId = $bm[1];
    $path = parse_url($row['fwd_url'], PHP_URL_PATH);
    $path = trim($path, '/');
    $jsonUrl = 'https://www.fwd.co.th/_next/data/' . $buildId . '/' . $path . '.json';
    $jsonPath = $pagesDir . '/' . $slug . '.next.json';

    echo "$slug ... ";
    $body = @file_get_contents($jsonUrl, false, $ctx);
    if ($body === false) {
        $fail++;
        echo "fetch fail\n";
        continue;
    }
    $data = json_decode($body, true);
    if (!is_array($data) || empty($data['pageProps']['product'])) {
        $fail++;
        echo "no product in json\n";
        continue;
    }
    file_put_contents($jsonPath, $body);
    $ok++;
    echo "ok\n";
}

echo "\nFetched product JSON: $ok ok, $fail fail\n";
