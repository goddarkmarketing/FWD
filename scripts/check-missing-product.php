<?php
$files = glob(__DIR__ . '/../data/fwd-pages/*.htm');
$missing = [];
foreach ($files as $file) {
    $html = file_get_contents($file);
    $slug = basename($file, '.htm');
    if (!preg_match('/id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m)) {
        $missing[] = [$slug, 'no next data'];
        continue;
    }
    $data = json_decode($m[1], true);
    $product = $data['props']['pageProps']['product'] ?? null;
    if (!$product) {
        $keys = implode(',', array_keys($data['props']['pageProps'] ?? []));
        $missing[] = [$slug, "no product, keys=$keys"];
    }
}
echo count($files) . " files, " . count($missing) . " missing product\n";
foreach ($missing as $m) {
    echo $m[0] . ": " . $m[1] . "\n";
}
