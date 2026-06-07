<?php
/**
 * Export รายการ URL จาก plans-catalog-definitions.php → data/fwd-urls/all-products.json
 */
$root = dirname(__DIR__);
$defs = require $root . '/includes/plans-catalog-definitions.php';
$out = [];

foreach ($defs as $categoryId => $category) {
    foreach ($category['products'] as $p) {
        $out[] = [
            'slug' => $p['slug'],
            'title' => $p['title'],
            'category' => $categoryId,
            'category_label' => $category['label'],
            'fwd_url' => $p['fwd_url'] ?? null,
        ];
    }
}

$dest = $root . '/data/fwd-urls/all-products.json';
file_put_contents($dest, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo count($out) . " products → $dest\n";
