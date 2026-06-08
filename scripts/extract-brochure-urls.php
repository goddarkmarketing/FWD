<?php
/**
 * Extract brochure PDF URLs from saved FWD product pages (__NEXT_DATA__).
 */
require_once __DIR__ . '/../includes/fwd-parse-htm.php';
require_once __DIR__ . '/../includes/config.php';

$pagesDir = dirname(__DIR__) . '/data/fwd-pages';
$catalog = json_decode(file_get_contents(dirname(__DIR__) . '/data/fwd-urls/all-products.json'), true);
$bySlug = [];
foreach ($catalog as $row) {
    $bySlug[$row['slug']] = $row;
}

$results = [];
$existing = plan_brochure_catalog();

foreach ($bySlug as $slug => $row) {
    $htm = $pagesDir . '/' . $slug . '.htm';
    if (!is_readable($htm)) {
        $results[$slug] = ['status' => 'no_page', 'title' => $row['title'] ?? $slug];
        continue;
    }

    $html = file_get_contents($htm);
    $brochure = fwd_extract_brochure_from_page($html, $slug);

    $results[$slug] = [
        'status' => $brochure ? 'found' : 'not_found',
        'title' => $row['title'] ?? $slug,
        'url' => $brochure['url'] ?? null,
        'filename' => $brochure['filename'] ?? null,
        'path' => $brochure['path'] ?? null,
        'score' => $brochure['score'] ?? null,
        'has_local' => isset($existing[$slug]),
    ];
}

$outFile = dirname(__DIR__) . '/data/fwd-brochure-urls.json';
file_put_contents($outFile, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

$found = 0;
$missing = 0;
$already = 0;
$new = 0;

foreach ($results as $slug => $info) {
    if (($info['status'] ?? '') !== 'found') {
        if (($info['status'] ?? '') === 'no_page') {
            continue;
        }
        $missing++;
        echo "MISS $slug — {$info['title']}\n";
        continue;
    }
    $found++;
    if (!empty($info['has_local'])) {
        $already++;
    } else {
        $new++;
        echo "NEW  $slug\n  {$info['filename']}\n  {$info['url']}\n";
    }
}

echo "\nSummary:\n";
echo "  PDF found on FWD page: $found\n";
echo "  Already have local file: $already\n";
echo "  New downloadable: $new\n";
echo "  No PDF on saved page: $missing\n";
echo "  Written: data/fwd-brochure-urls.json\n";
