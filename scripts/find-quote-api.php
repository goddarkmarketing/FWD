<?php
$raw = file_get_contents(__DIR__ . '/next-data-stroke.json');
$raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);

foreach (['quoteApi', 'apiUrl', 'API_URL', 'quote', 'premium', 'graphql', 'bff', 'gateway'] as $term) {
    if (stripos($raw, $term) !== false) {
        echo "Found term: $term\n";
    }
}

// Extract plan objects with coverage
if (preg_match('/"plans"\s*:\s*(\[[\s\S]*?\])\s*,\s*"insuredPersons"/', $raw, $m)) {
    $plans = json_decode($m[1], true);
    if ($plans) {
        echo "\nPlans:\n";
        echo json_encode($plans, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
}

// env from runtimeConfig
if (preg_match('/"runtimeConfig"\s*:\s*(\{[\s\S]*?\})\s*,\s*"isFallback"/', $raw, $rc)) {
    $cfg = json_decode($rc[1], true);
    if ($cfg) {
        echo "\nruntimeConfig keys: " . implode(', ', array_keys($cfg)) . "\n";
        foreach ($cfg as $k => $v) {
            if (is_string($v) && (stripos($k, 'api') !== false || stripos($k, 'url') !== false)) {
                echo "  $k = $v\n";
            }
        }
    }
}
