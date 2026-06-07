<?php
$raw = file_get_contents(__DIR__ . '/next-data-stroke.json');
if (preg_match('/"buildId"\s*:\s*"([^"]+)"/', $raw, $m)) {
    echo "buildId: {$m[1]}\n";
}
if (preg_match('/"assetPrefix"\s*:\s*"([^"]*)"/', $raw, $m)) {
    echo "assetPrefix: {$m[1]}\n";
}
