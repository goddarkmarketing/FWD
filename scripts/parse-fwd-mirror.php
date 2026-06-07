<?php
/**
 * อ่านไฟล์จาก HTTrack mirror แล้วดึงแผน + ตัวอย่างเบี้ยจาก __NEXT_DATA__ และ FAQ
 *
 * ใช้: php scripts/parse-fwd-mirror.php "C:\path\to\Clone FWD\www.fwd.co.th"
 */
$mirrorRoot = $argv[1] ?? (__DIR__ . '/../data/fwd-mirror');
if (!is_dir($mirrorRoot)) {
    fwrite(STDERR, "ไม่พบโฟลเดอร์: $mirrorRoot\n");
    fwrite(STDERR, "ใช้: php scripts/parse-fwd-mirror.php \"C:\\path\\to\\www.fwd.co.th\"\n");
    exit(1);
}

$out = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($mirrorRoot, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file->isFile()) {
        continue;
    }
    $path = $file->getPathname();
    if (!preg_match('/\.html?$/i', $path)) {
        continue;
    }

    $html = @file_get_contents($path);
    if ($html === false || stripos($html, '__NEXT_DATA__') === false) {
        continue;
    }

    if (!preg_match('/<script id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m)) {
        continue;
    }

    $data = json_decode($m[1], true);
    if (!is_array($data)) {
        continue;
    }

    $raw = $m[1];
    $rel = str_replace('\\', '/', substr($path, strlen($mirrorRoot)));
    $entry = [
        'file' => $rel,
        'packages' => [],
        'premium_examples' => [],
        'product_id' => null,
    ];

    if (preg_match('/"productId"\s*:\s*"([^"]+)"/', $raw, $pid)) {
        $entry['product_id'] = $pid[1];
    }

    if (preg_match('/"plans"\s*:\s*(\[[\s\S]*?\])\s*,\s*"insuredPersons"/', $raw, $pm)) {
        $plans = json_decode($pm[1], true);
        if (is_array($plans)) {
            foreach ($plans as $plan) {
                $entry['packages'][] = [
                    'name' => $plan['planName'] ?? null,
                    'coverage' => $plan['coverage'] ?? null,
                    'plan_code' => $plan['planCode'] ?? null,
                    'popular' => !empty($plan['calloutLabel']),
                ];
            }
        }
    }

    if (preg_match_all(
        '/ตัวอย่างเบี้ย[^<]*(?:<[^>]+>)*\s*([^<]*(?:\d[\d,]*\s*บาท[^<]*)+)/u',
        $html,
        $ex
    )) {
        $entry['premium_examples'] = array_map('trim', $ex[1]);
    }

    if (!empty($entry['packages']) || !empty($entry['premium_examples'])) {
        $key = preg_replace('#^/th/#', '', $rel);
        $key = preg_replace('#/index\.html$#', '', $key);
        $out[$key] = $entry;
    }
}

$dest = dirname(__DIR__) . '/data/fwd-mirror-pricing.json';
file_put_contents($dest, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "พบหน้าผลิตภัณฑ์: " . count($out) . " ไฟล์\n";
echo "บันทึก: $dest\n";
