<?php
$url = $argv[1] ?? 'https://www.fwd.co.th/th/health-insurance/easy-e-health/';
$ctx = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36\r\nAccept-Language: th-TH,th;q=0.9\r\n",
        'timeout' => 45,
    ],
]);
$html = @file_get_contents($url, false, $ctx);
if ($html === false) {
    echo "FAIL\n";
    exit(1);
}
echo 'OK ' . strlen($html) . " bytes\n";
echo '__NEXT_DATA__=' . (strpos($html, '__NEXT_DATA__') !== false ? 'yes' : 'no') . "\n";
