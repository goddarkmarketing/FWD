<?php
require_once __DIR__ . '/../includes/fwd-parse-htm.php';
$html = file_get_contents(__DIR__ . '/../data/fwd-pages/precious-care.htm');
echo 'len=' . strlen($html) . "\n";
echo 'has script id NEXT_DATA: ' . (preg_match('/id="__NEXT_DATA__"/', $html) ? 'yes' : 'no') . "\n";
$data = fwd_extract_next_data($html);
echo 'extracted: ' . (is_array($data) ? 'yes' : 'no') . "\n";
if ($data) {
    echo 'pageProps keys: ' . implode(',', array_keys($data['props']['pageProps'] ?? [])) . "\n";
    echo 'has product: ' . (!empty($data['props']['pageProps']['product']) ? 'yes' : 'no') . "\n";
}
if (preg_match('/"buildId":"([^"]+)"/', $html, $m)) {
    echo 'buildId=' . $m[1] . "\n";
}
