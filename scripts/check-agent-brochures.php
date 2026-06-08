<?php
require dirname(__DIR__) . '/includes/config.php';

$cat = require dirname(__DIR__) . '/includes/plans-catalog-definitions.php';
$slugs = [];
foreach ($cat as $group) {
    foreach ($group['products'] as $p) {
        $slugs[] = $p['slug'];
    }
}

$bro = plan_brochure_catalog();
$have = array_intersect($slugs, array_keys($bro));
$miss = array_diff($slugs, array_keys($bro));

echo count($slugs) . " agent plans\n";
echo count($have) . " with brochure\n";
echo count($miss) . " missing:\n";
foreach ($miss as $s) {
    $item = plan_catalog_by_slug($s);
    echo "  $s — " . ($item['title'] ?? $s) . "\n";
}
