<?php
$html = file_get_contents(__DIR__ . '/../data/fwd-pages/easy-e-stroke.htm');
preg_match('/id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m);
$data = json_decode($m[1], true);
$product = $data['props']['pageProps']['product'] ?? null;
if (!$product) {
    echo "no product\n";
    exit(1);
}
echo "product keys: " . implode(', ', array_keys($product)) . "\n\n";

function dumpStructure($arr, $prefix = '', $depth = 0) {
    if ($depth > 3 || !is_array($arr)) return;
    foreach ($arr as $k => $v) {
        $path = $prefix === '' ? $k : "$prefix.$k";
        if (is_string($v)) {
            if (mb_strlen($v) > 10 && mb_strlen($v) < 300) {
                echo "$path: " . mb_substr(strip_tags($v), 0, 80) . "\n";
            }
        } elseif (is_array($v) && isset($v[0]) && is_array($v[0])) {
            echo "$path: array[" . count($v) . "] keys=" . implode(',', array_keys($v[0])) . "\n";
            if ($depth < 2 && count($v) > 0) {
                dumpStructure($v[0], "$path[0]", $depth + 1);
            }
        } elseif (is_array($v) && !isset($v[0])) {
            dumpStructure($v, $path, $depth + 1);
        }
    }
}
dumpStructure($product);
