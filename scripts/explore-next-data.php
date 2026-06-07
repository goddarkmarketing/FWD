<?php
$html = file_get_contents(__DIR__ . '/../data/fwd-pages/easy-e-stroke.htm');
preg_match('/id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m);
$data = json_decode($m[1], true);

function findKeys($arr, $prefix = '', $depth = 0) {
    if ($depth > 4 || !is_array($arr)) return;
    foreach ($arr as $k => $v) {
        $path = $prefix === '' ? $k : "$prefix.$k";
        if (is_string($v) && mb_strlen($v) > 20 && mb_strlen($v) < 500) {
            if (preg_match('/(highlight|faq|coverage|benefit|feature|title|desc|bullet|tagline)/i', $path . $k)) {
                echo substr($path, 0, 80) . " => " . mb_substr($v, 0, 60) . "\n";
            }
        }
        if (is_array($v) && !isset($v[0])) {
            findKeys($v, $path, $depth + 1);
        }
    }
}

$pp = $data['props']['pageProps'] ?? [];
echo "pageProps keys: " . implode(', ', array_keys($pp)) . "\n\n";
findKeys($pp);
