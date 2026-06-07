<?php
/**
 * Render a single PHP page to stdout (used by build-static.php).
 *
 * Usage: php scripts/render-one.php /FWD index.php index.php '{}'
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$baseUrl = $argv[1] ?? '/FWD';
$phpFile = $argv[2] ?? 'index.php';
$scriptBasename = $argv[3] ?? basename($phpFile);
$queryJson = $argv[4] ?? '{}';

putenv('FWD_STATIC_BUILD=1');
putenv('FWD_BASE_URL=' . $baseUrl);

$get = json_decode($queryJson, true);
if (!is_array($get)) {
    $get = [];
}

$_GET = $get;
$_POST = [];
$_REQUEST = $get;
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'on';
$_SERVER['SCRIPT_NAME'] = rtrim($baseUrl, '/') . '/' . $scriptBasename;
$_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
if ($get !== []) {
    $_SERVER['REQUEST_URI'] .= '?' . http_build_query($get);
}

chdir($root);
include $root . '/' . $phpFile;
