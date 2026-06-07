<?php
$html = file_get_contents(__DIR__ . '/../data/fwd-pages/easy-e-stroke.htm');
preg_match('/id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m);
$data = json_decode($m[1], true);
foreach ($data['props']['pageProps']['product']['sections'] as $s) {
    if (($s['key'] ?? '') !== 'benefits_summary_section') continue;
    echo json_encode($s['props'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
