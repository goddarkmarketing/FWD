<?php
$html = file_get_contents(__DIR__ . '/fwd-scrape-temp.html');
if (preg_match('/ci-table-plan[^>]*>.*?<\/table>/uis', $html, $m)) {
    echo $m[0];
} else {
    // all tables
    preg_match_all('/<table[^>]*>.*?<\/table>/uis', $html, $tables);
    echo "tables found: " . count($tables[0]) . "\n";
    foreach (array_slice($tables[0], 0, 3) as $i => $t) {
        echo "\n=== Table $i (len " . strlen($t) . ") ===\n";
        echo substr(strip_tags($t), 0, 800) . "\n";
    }
}

// premium rate rows in content
if (preg_match_all('/<tr[^>]*>.*?<\/tr>/uis', $html, $rows)) {
    $premiumRows = [];
    foreach ($rows[0] as $row) {
        if (preg_match('/\d{3,}.*\d{3,}/', strip_tags($row))) {
            $premiumRows[] = strip_tags(preg_replace('/\s+/', ' ', $row));
        }
    }
    echo "\nRows with numbers (" . count($premiumRows) . "):\n";
    foreach (array_slice($premiumRows, 0, 25) as $r) {
        echo $r . "\n";
    }
}
