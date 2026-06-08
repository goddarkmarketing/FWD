<?php
require_once __DIR__ . '/../includes/fwd-parse-htm.php';

$slug = $argv[1] ?? 'precious-care-for-kids';
$html = file_get_contents(dirname(__DIR__) . '/data/fwd-pages/' . $slug . '.htm');
$data = fwd_extract_next_data($html);

function walk($node, callable $fn, string $path = ''): void
{
    if (!is_array($node)) {
        return;
    }
    foreach ($node as $key => $value) {
        $p = $path === '' ? (string) $key : $path . '.' . $key;
        $fn($key, $value, $p);
        if (is_array($value)) {
            walk($value, $fn, $p);
        }
    }
}

$pdfs = [];
walk($data, function ($key, $value, $path) use (&$pdfs) {
    if (is_string($value) && preg_match('/\.pdf$/i', $value)) {
        $pdfs[] = ['path' => $path, 'url' => $value, 'key' => $key];
    }
});

foreach ($pdfs as $row) {
    echo $row['path'] . "\n  " . $row['url'] . "\n";
}
