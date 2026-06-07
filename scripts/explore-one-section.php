<?php
$slug = $argv[1] ?? 'easy-e-health';
$html = file_get_contents(__DIR__ . "/../data/fwd-pages/$slug.htm");
echo "size: " . strlen($html) . "\n";
if (!preg_match('/id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m)) {
    echo "no NEXT_DATA\n";
    exit(1);
}
$data = json_decode($m[1], true);
$product = $data['props']['pageProps']['product'] ?? null;
if (!$product) {
    echo "no product\n";
    exit(1);
}
echo "banner: " . ($product['topBanner']['title'] ?? '') . "\n";
echo "caption: " . mb_substr(strip_tags($product['topBanner']['caption'] ?? ''), 0, 100) . "\n";
foreach ($product['sections'] ?? [] as $s) {
    $k = $s['key'];
    $p = $s['props'];
    if ($k === 'usps_section') {
        echo "\nUSPS title: {$p['title']}\n";
        echo "desc: " . mb_substr(strip_tags($p['description'] ?? ''), 0, 120) . "\n";
        foreach ($p['items'] ?? [] as $i => $item) {
            echo "  item$i: {$item['title']} | " . mb_substr(strip_tags($item['description'] ?? ''), 0, 60) . "\n";
        }
    }
    if ($k === 'faqs_section') {
        echo "\nFAQ count: " . count($p['questions'] ?? []) . "\n";
        foreach (array_slice($p['questions'] ?? [], 0, 2) as $q) {
            echo "  Q: " . mb_substr($q['question'] ?? $q['title'] ?? '', 0, 60) . "\n";
        }
    }
    if ($k === 'benefits_summary_section') {
        echo "\nBenefits: {$p['title']}\n";
        echo "desc: " . mb_substr(strip_tags($p['description'] ?? ''), 0, 80) . "\n";
    }
    if ($k === 'utility_section' && !empty($p['items'])) {
        echo "\nutility: {$p['title']} items=" . count($p['items']) . "\n";
        foreach (array_slice($p['items'], 0, 2) as $item) {
            echo "  - " . mb_substr($item['title'] ?? '', 0, 50) . "\n";
        }
    }
}
