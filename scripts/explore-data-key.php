<?php
$html = file_get_contents(__DIR__ . '/../data/fwd-pages/ci-med-all.htm');
preg_match('/id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m);
$data = json_decode($m[1], true);
$pp = $data['props']['pageProps'] ?? [];
echo "keys: " . implode(', ', array_keys($pp)) . "\n";
if (isset($pp['data'])) {
    echo "data type: " . gettype($pp['data']) . "\n";
    if (is_array($pp['data'])) {
        echo "data keys: " . implode(', ', array_keys($pp['data'])) . "\n";
        if (isset($pp['data']['product'])) {
            echo "has nested product\n";
        }
    }
}
