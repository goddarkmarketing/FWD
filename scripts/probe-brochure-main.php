<?php
require_once __DIR__ . '/../includes/fwd-parse-htm.php';

$slugs = array_slice($argv, 1);
if ($slugs === []) {
    $slugs = ['heritage-plus', 'cancer-fighter', 'prakan-kon-kla', 'prakan-kon-klang', 'prakan-kon-jing', 'fwd-savvy-pension'];
}

foreach ($slugs as $slug) {
    $htm = dirname(__DIR__) . '/data/fwd-pages/' . $slug . '.htm';
    if (!is_readable($htm)) {
        echo "$slug: NO PAGE\n";
        continue;
    }
    $row = fwd_extract_brochure_from_page(file_get_contents($htm), $slug);
    if ($row === null) {
        echo "$slug: NOT FOUND\n";
        continue;
    }
    echo "$slug: " . ($row['filename'] ?? basename(parse_url($row['url'], PHP_URL_PATH))) . "\n  {$row['url']}\n  path: {$row['path']}\n";
}
