<?php
$html = file_get_contents(__DIR__ . '/fwd-scrape-temp.html');
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
$xpath = new DOMXPath($dom);

$nodes = $xpath->query('//*[@data-testid="PlanOptionV3"]');
echo "PlanOptionV3 count: " . $nodes->length . "\n\n";

foreach ($nodes as $i => $node) {
    $text = preg_replace('/\s+/u', ' ', trim($node->textContent));
    echo "=== Plan $i ===\n$text\n\n";
}

// Search JSON for numbers near 500000
$nd = file_get_contents(__DIR__ . '/next-data-stroke.json');
preg_match_all('/"amount"\s*:\s*(\d+)/', $nd, $am);
echo "amount values: " . implode(', ', array_unique(array_slice($am[1], 0, 20))) . "\n";

preg_match_all('/"coverage(?:Sum|Amount|Value)?"\s*:\s*(\d+)/i', $nd, $cv);
if ($cv[1]) echo "coverage values: " . implode(', ', array_unique($cv[1])) . "\n";

preg_match_all('/500000[^}]{0,300}/', $nd, $blocks);
foreach (array_slice($blocks[0], 0, 3) as $b) {
    echo "\n500000 context: $b\n";
}
