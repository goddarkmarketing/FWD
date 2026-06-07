<?php
/**
 * Parse one saved FWD product page (.htm).
 * Usage: php scripts/parse-saved-fwd-page.php "path/to/page.htm" [--json]
 */
require_once __DIR__ . '/../includes/fwd-parse-htm.php';

$file = $argv[1] ?? '';
if ($file === '' || !is_readable($file)) {
    fwrite(STDERR, "Usage: php parse-saved-fwd-page.php <file.htm> [--json]\n");
    exit(1);
}

$html = file_get_contents($file);
$slug = pathinfo($file, PATHINFO_FILENAME);
$catalog = [
    'slug' => $slug,
    'title' => $slug,
    'desc' => '',
    'category' => '',
    'category_label' => '',
];

$listPath = dirname(__DIR__) . '/data/fwd-urls/all-products.json';
if (is_readable($listPath)) {
    foreach (json_decode(file_get_contents($listPath), true) as $row) {
        if ($row['slug'] === $slug) {
            $catalog = array_merge($catalog, $row);
            break;
        }
    }
}

$detail = fwd_parse_htm_to_plan_detail($html, $catalog);

if (in_array('--json', $argv, true)) {
    echo json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
    exit(0);
}

echo "Title: {$detail['title']}\n";
echo "Tagline: " . mb_substr($detail['tagline'], 0, 120) . "\n";
echo 'Highlights: ' . count($detail['highlights']) . "\n";
echo 'Coverage blocks: ' . count($detail['coverage_blocks']) . "\n";
echo 'FAQ: ' . count($detail['faq']) . "\n";
echo 'Conditions: ' . count($detail['conditions']) . "\n";
