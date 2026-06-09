<?php
/**
 * สร้างไฟล์สำรองข้อมูล CMS (CLI)
 *
 * Usage:
 *   php scripts/backup-cms.php
 *   php scripts/backup-cms.php --note="ก่อน deploy มิ.ย. 2026"
 */
declare(strict_types=1);

$root = dirname(__DIR__);
chdir($root);

require_once $root . '/includes/cms-loader.php';
require_once $root . '/includes/backup-manager.php';

$note = '';
foreach ($argv ?? [] as $arg) {
    if (str_starts_with($arg, '--note=')) {
        $note = substr($arg, 7);
    }
}

$result = backup_create($note);

if (!$result['ok']) {
    fwrite(STDERR, 'Backup failed: ' . ($result['error'] ?? 'unknown') . PHP_EOL);
    exit(1);
}

echo 'Backup created: ' . $result['filename'] . PHP_EOL;
echo 'Path: ' . $result['path'] . PHP_EOL;
echo 'Size: ' . backup_format_size((int) $result['size']) . PHP_EOL;
echo 'Files: ' . (int) $result['file_count'] . PHP_EOL;
exit(0);
