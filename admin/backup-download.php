<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();
require_once dirname(__DIR__) . '/includes/backup-manager.php';

$filename = basename((string) ($_GET['file'] ?? ''));
if (!backup_is_valid_filename($filename)) {
    http_response_code(404);
    exit('Not found');
}

$path = backup_root() . '/' . $filename;
if (!is_readable($path)) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . (string) filesize($path));
header('Cache-Control: no-store');

readfile($path);
exit;
