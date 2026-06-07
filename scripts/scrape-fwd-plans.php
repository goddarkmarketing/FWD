<?php
/**
 * Scrape FWD product pages and extract plan options + premiums from SSR HTML.
 * Run: php scripts/scrape-fwd-plans.php
 * Output: data/fwd-pricing-cache.json
 */

$urls = [
    'easy-e-stroke' => 'https://www.fwd.co.th/th/critical-illness-insurance/easy-e-stroke/',
    'easy-e-health' => 'https://www.fwd.co.th/th/health-insurance/easy-e-health/',
    'easy-all-in-one' => 'https://www.fwd.co.th/th/life-and-accident-insurance/easy-all-in-one/',
    'easy-e-life' => 'https://www.fwd.co.th/th/life-and-accident-insurance/easy-e-life/',
    'easy-e-accident' => 'https://www.fwd.co.th/th/life-and-accident-insurance/easy-e-accident/',
    'easy-e-cancer' => 'https://www.fwd.co.th/th/critical-illness-insurance/easy-e-cancer/',
];

$ctx = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0\r\nAccept-Language: th-TH,th;q=0.9\r\n",
        'timeout' => 45,
    ],
    'ssl' => ['verify_peer' => true],
]);

$out = [];

foreach ($urls as $slug => $url) {
    echo "Fetching $slug...\n";
    $html = @file_get_contents($url, false, $ctx);
    if ($html === false) {
        $out[$slug] = ['error' => 'fetch failed', 'url' => $url];
        continue;
    }

    $result = [
        'url' => $url,
        'fetched_at' => date('c'),
        'packages' => [],
        'premium_examples' => [],
    ];

    // PlanOptionV3 blocks: title + sum + premium text
    if (preg_match_all(
        '/data-testid="PlanOptionV3"[^>]*>.*?<\/a>/us',
        $html,
        $planBlocks
    )) {
        foreach ($planBlocks[0] as $block) {
            $pkg = [];
            if (preg_match('/>(Economy|Standard|Premium|แผน[^<]{0,40})</u', $block, $m)) {
                $pkg['name'] = trim(strip_tags($m[1]));
            }
            if (preg_match('/([\d,]+)\s*บาท/u', $block, $m)) {
                $pkg['sum_text'] = $m[0];
                $pkg['sum'] = (int) str_replace(',', '', $m[1]);
            }
            if (preg_match_all('/([\d,]+)\s*บาท\/(?:ปี|เดือน)/u', $block, $pm)) {
                $pkg['premiums'] = $pm[0];
                $pkg['premium_values'] = array_map(function ($v) {
                    return (int) str_replace(',', '', preg_replace('/[^\d]/', '', $v));
                }, $pm[1]);
            }
            if (preg_match('/เบี้ยประกัน[^<]*?([\d,]+)\s*บาท/u', $block, $m)) {
                $pkg['premium_from'] = (int) str_replace(',', '', $m[1]);
            }
            if ($pkg) {
                $result['packages'][] = $pkg;
            }
        }
    }

    // Fallback: extract visible premium numbers near plan names
    if (empty($result['packages'])) {
        if (preg_match_all(
            '/(Economy|Standard|Premium)[^<]{0,200}?([\d,]+)\s*บาท/uis',
            $html,
            $m,
            PREG_SET_ORDER
        )) {
            foreach ($m as $row) {
                $result['packages'][] = [
                    'name' => $row[1],
                    'sum' => (int) str_replace(',', '', $row[2]),
                ];
            }
        }
    }

    // FAQ / footnote premium examples
    if (preg_match_all(
        '/ตัวอย่างเบี้ย[^<]+<[^>]+>([^<]+(?:บาท[^<]*)+)/u',
        $html,
        $ex
    )) {
        $result['premium_examples'] = array_map('strip_tags', $ex[1]);
    }

    // __NEXT_DATA__ product / quote config
    if (preg_match('/<script id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $nd)) {
        $json = json_decode($nd[1], true);
        if ($json) {
            $raw = $nd[1];
            if (preg_match('/"productCode"\s*:\s*"([^"]+)"/', $raw, $pc)) {
                $result['product_code'] = $pc[1];
            }
            if (preg_match('/"productId"\s*:\s*"([^"]+)"/', $raw, $pi)) {
                $result['product_id'] = $pi[1];
            }
            // plan list in quoteWidget
            if (preg_match_all(
                '/"label"\s*:\s*"(Economy|Standard|Premium)"[^}]{0,400}?"(?:yearly|annual)(?:Premium|Price)"\s*:\s*(\d+)/i',
                $raw,
                $plans,
                PREG_SET_ORDER
            )) {
                foreach ($plans as $p) {
                    $result['packages'][] = [
                        'name' => $p[1],
                        'premium_yearly' => (int) $p[2],
                        'source' => 'next_data',
                    ];
                }
            }
        }
    }

    $out[$slug] = $result;
    echo "  packages: " . count($result['packages']) . "\n";
    usleep(500000);
}

$cacheDir = dirname(__DIR__) . '/data';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}
$file = $cacheDir . '/fwd-pricing-cache.json';
file_put_contents($file, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Written $file\n";
