<?php
require dirname(__DIR__) . '/includes/config.php';

$catalog = plan_brochure_catalog();
echo count($catalog) . " plans with brochure\n";
foreach ($catalog as $slug => $path) {
    echo "  $slug\n";
}

$allSlugs = array_keys(plan_details_all());
$missing = array_diff($allSlugs, array_keys($catalog));
echo "\n" . count($missing) . " plans without brochure:\n";
foreach ($missing as $slug) {
    $item = plan_catalog_by_slug($slug);
    $title = $item['title'] ?? $slug;
    echo "  $slug — $title\n";
}
