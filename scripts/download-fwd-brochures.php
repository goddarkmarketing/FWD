<?php
/**
 * Download brochure PDFs from data/fwd-brochure-urls.json into assets/โบรชัวร์แบบประกัน/{slug}.pdf
 *
 * Usage:
 *   php scripts/download-fwd-brochures.php           # download missing only
 *   php scripts/download-fwd-brochures.php --force     # re-download all found
 *   php scripts/download-fwd-brochures.php slug1 slug2 # specific slugs
 */
require_once __DIR__ . '/../includes/config.php';

$force = in_array('--force', $argv, true);
$onlySlugs = array_values(array_filter($argv, static fn ($arg) => $arg !== '--force' && !str_starts_with($arg, '-')));
array_shift($onlySlugs);

$jsonFile = dirname(__DIR__) . '/data/fwd-brochure-urls.json';
if (!is_readable($jsonFile)) {
    fwrite(STDERR, "Missing $jsonFile — run scripts/extract-brochure-urls.php first\n");
    exit(1);
}

$map = json_decode(file_get_contents($jsonFile), true);
if (!is_array($map)) {
    fwrite(STDERR, "Invalid JSON in data/fwd-brochure-urls.json\n");
    exit(1);
}

$brochureDir = dirname(__DIR__) . '/assets/โบรชัวร์แบบประกัน';
if (!is_dir($brochureDir) && !mkdir($brochureDir, 0755, true)) {
    fwrite(STDERR, "Cannot create brochure directory\n");
    exit(1);
}

$ok = 0;
$skip = 0;
$fail = 0;

foreach ($map as $slug => $info) {
    if ($onlySlugs !== [] && !in_array($slug, $onlySlugs, true)) {
        continue;
    }
    if (($info['status'] ?? '') !== 'found' || empty($info['url'])) {
        continue;
    }

    $dest = $brochureDir . '/' . $slug . '.pdf';
    if (!$force && is_readable($dest) && filesize($dest) > 1000) {
        $skip++;
        continue;
    }

    $url = $info['url'];
    echo "GET  $slug\n  $url\n";

    $ctx = stream_context_create([
        'http' => [
            'timeout' => 120,
            'header' => "User-Agent: FWD-Agent-Catalog/1.0\r\n",
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $bytes = @file_get_contents($url, false, $ctx);
    if ($bytes === false || strlen($bytes) < 1000) {
        echo "  FAIL download\n";
        $fail++;
        continue;
    }

    if (@file_put_contents($dest, $bytes) === false) {
        echo "  FAIL write $dest\n";
        $fail++;
        continue;
    }

    echo "  OK   " . basename($dest) . ' (' . number_format(strlen($bytes)) . " bytes)\n";
    $ok++;
}

echo "\nDone: downloaded=$ok skipped=$skip failed=$fail\n";
