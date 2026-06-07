<?php
$path = $argv[1] ?? __DIR__ . '/next-data-stroke.json';
$raw = file_get_contents($path);

// Find all "premium*": number patterns
preg_match_all('/"(premium[A-Za-z_]*)"\s*:\s*(\d+)/', $raw, $m1, PREG_SET_ORDER);
echo "premium fields (sample 30):\n";
foreach (array_slice($m1, 0, 30) as $m) {
    echo "  {$m[1]} = {$m[2]}\n";
}

preg_match_all('/"(sumAssured|coverageAmount|sumInsured|coverageSum|planSum)"\s*:\s*(\d+)/', $raw, $m2, PREG_SET_ORDER);
echo "\nsum fields (sample 20):\n";
foreach (array_slice($m2, 0, 20) as $m) {
    echo "  {$m[1]} = {$m[2]}\n";
}

preg_match_all('/"(planName|planLabel|name)"\s*:\s*"(Economy|Standard|Premium|แผน[^"]+)"/u', $raw, $m3, PREG_SET_ORDER);
echo "\nplan names:\n";
foreach (array_slice($m3, 0, 15) as $m) {
    echo "  {$m[2]}\n";
}

// quoteWidget product code
if (preg_match('/"productCode"\s*:\s*"([^"]+)"/', $raw, $pc)) {
    echo "\nproductCode: {$pc[1]}\n";
}

// Extract blocks with 500000 near premium
preg_match_all('/\{[^{}]{0,800}"(?:sumAssured|coverageAmount|amount)"\s*:\s*500000[^{}]{0,800}\}/', $raw, $blocks);
echo "\nblocks with 500000: " . count($blocks[0]) . "\n";
if (!empty($blocks[0][0])) {
    echo substr($blocks[0][0], 0, 500) . "...\n";
}
