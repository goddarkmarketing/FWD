<?php
$html = file_get_contents(__DIR__ . '/../data/fwd-pages/easy-e-health.htm');
preg_match('/id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m);
$data = json_decode($m[1], true);
$sections = $data['props']['pageProps']['product']['sections'] ?? [];
foreach ($sections as $s) {
    echo "\n=== {$s['key']} ===\n";
    echo json_encode($s['props'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo "\n";
    if (strlen(json_encode($s['props'])) > 3000) {
        echo "(truncated in output)\n";
        break;
    }
}
