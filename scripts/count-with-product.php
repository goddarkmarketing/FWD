<?php
require_once __DIR__ . '/../includes/fwd-parse-htm.php';
$files = glob(__DIR__ . '/../data/fwd-pages/*.htm');
$ok = 0;
foreach ($files as $file) {
    $html = file_get_contents($file);
    $data = fwd_extract_next_data($html);
    if (!empty($data['props']['pageProps']['product'])) {
        $ok++;
        echo basename($file, '.htm') . "\n";
    }
}
echo "\nTotal with product: $ok / " . count($files) . "\n";
