<?php
/**
 * Render admin page HTML for tests (mock login).
 *
 * Usage: php scripts/render-admin-page.php article-edit.php '{"slug":"health-insurance-guide"}'
 */
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$root = dirname(__DIR__);
$adminFile = $argv[1] ?? 'articles.php';
$queryJson = $argv[2] ?? '{}';

$get = json_decode($queryJson, true);
if (!is_array($get)) {
    $get = [];
}

$_SESSION['admin_user'] = 'test@example.com';
$_GET = $get;
$_POST = [];
$_REQUEST = $get;
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = '';
$_SERVER['SCRIPT_NAME'] = '/fwd/admin/' . basename($adminFile);
$_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
if ($get !== []) {
    $_SERVER['REQUEST_URI'] .= '?' . http_build_query($get);
}

chdir($root . '/admin');
include $root . '/admin/' . basename($adminFile);
