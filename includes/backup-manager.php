<?php
declare(strict_types=1);

/**
 * สำรอง / กู้คืนข้อมูลลูกค้า (CMS JSON + ไฟล์อัปโหลด)
 * ไม่รวมโค้ดระบบ — ใช้ก่อนอัปเดตเว็บจากนักพัฒนาได้
 */

const BACKUP_FORMAT_VERSION = 1;
const BACKUP_TIMEZONE = 'Asia/Bangkok';

function backup_timezone(): DateTimeZone
{
    return new DateTimeZone(BACKUP_TIMEZONE);
}

function backup_now(): DateTimeImmutable
{
    return new DateTimeImmutable('now', backup_timezone());
}

function backup_timestamp_slug(): string
{
    return backup_now()->format('Ymd-His');
}

/** แปลง ISO / unix / ชื่อไฟล์ เป็นข้อความเวลาไทย */
function backup_format_datetime(?string $iso = null, ?int $unix = null, ?string $filename = null): string
{
    $tz = backup_timezone();

    if ($iso !== null && $iso !== '') {
        try {
            $dt = new DateTimeImmutable($iso);
            return $dt->setTimezone($tz)->format('j M Y H:i') . ' น.';
        } catch (Exception) {
            // fall through
        }
    }

    if ($unix !== null && $unix > 0) {
        return (new DateTimeImmutable('@' . $unix))->setTimezone($tz)->format('j M Y H:i') . ' น.';
    }

    if ($filename !== null && preg_match('/fwd-backup-(\d{8})-(\d{6})\.zip$/', $filename, $m)) {
        $dt = DateTimeImmutable::createFromFormat('Ymd His', $m[1] . ' ' . $m[2], $tz);
        if ($dt !== false) {
            return $dt->format('j M Y H:i') . ' น.';
        }
    }

    return '—';
}

function backup_root(): string
{
    return dirname(__DIR__) . '/data/backups';
}

/** โฟลเดอร์ไฟล์ที่ลูกค้าอัปโหลด / แก้ไขผ่าน CMS */
function backup_customer_asset_dirs(): array
{
    return [
        'assets/cover',
        'assets/uploads',
        'assets/รีวิว',
        'assets/images',
    ];
}

function backup_project_root(): string
{
    return dirname(__DIR__);
}

function backup_ensure_dir(): bool
{
    $dir = backup_root();
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return false;
    }

    $htaccess = $dir . '/.htaccess';
    if (!is_file($htaccess)) {
        file_put_contents($htaccess, "Require all denied\n");
    }

    $index = $dir . '/index.html';
    if (!is_file($index)) {
        file_put_contents($index, '');
    }

    return true;
}

function backup_safe_rel_path(string $path): ?string
{
    $path = str_replace('\\', '/', $path);
    $path = ltrim($path, '/');
    if ($path === '' || str_contains($path, '..')) {
        return null;
    }

    return $path;
}

function backup_target_abs(string $rel): ?string
{
    $rel = backup_safe_rel_path($rel);
    if ($rel === null || $rel === 'manifest.json') {
        return null;
    }

    $root = backup_project_root();
    if (str_starts_with($rel, 'cms/')) {
        return $root . '/data/' . $rel;
    }
    foreach (backup_customer_asset_dirs() as $dir) {
        if ($rel === $dir || str_starts_with($rel, $dir . '/')) {
            return $root . '/' . $rel;
        }
    }

    return null;
}

function backup_collect_files(): array
{
    $root = backup_project_root();
    $files = [];

    $cmsDir = $root . '/data/cms';
    if (is_dir($cmsDir)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($cmsDir, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $rel = 'cms/' . str_replace('\\', '/', substr($file->getPathname(), strlen($cmsDir) + 1));
            $files[$rel] = $file->getPathname();
        }
    }

    foreach (backup_customer_asset_dirs() as $dir) {
        $abs = $root . '/' . $dir;
        if (!is_dir($abs)) {
            continue;
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($abs, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $rel = $dir . '/' . str_replace('\\', '/', substr($file->getPathname(), strlen($abs) + 1));
            $files[$rel] = $file->getPathname();
        }
    }

    ksort($files, SORT_NATURAL);
    return $files;
}

function backup_format_size(int $bytes): string
{
    if ($bytes < 1024) {
        return $bytes . ' B';
    }
    if ($bytes < 1024 * 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }

    return round($bytes / (1024 * 1024), 2) . ' MB';
}

function backup_manifest(array $extra = []): array
{
    return array_merge([
        'format_version' => BACKUP_FORMAT_VERSION,
        'created_at' => backup_now()->format('c'),
        'timezone' => BACKUP_TIMEZONE,
        'site_name' => function_exists('cms_get') ? cms_get('site', 'site_name', '') : '',
        'php_version' => PHP_VERSION,
        'includes' => [
            'cms' => 'data/cms — ข้อความ บทความ โปรโมชัน หมวดหมู่ บัญชี admin',
            'assets' => backup_customer_asset_dirs(),
        ],
    ], $extra);
}

function backup_validate_zip(string $zipPath): array
{
    if (!is_readable($zipPath)) {
        return ['ok' => false, 'error' => 'อ่านไฟล์สำรองไม่ได้'];
    }
    if (!class_exists('ZipArchive')) {
        return ['ok' => false, 'error' => 'เซิร์ฟเวอร์ไม่รองรับ ZipArchive'];
    }

    $zip = new ZipArchive();
    if ($zip->open($zipPath) !== true) {
        return ['ok' => false, 'error' => 'เปิดไฟล์ ZIP ไม่ได้'];
    }

    $manifestRaw = $zip->getFromName('manifest.json');
    if ($manifestRaw === false) {
        $zip->close();
        return ['ok' => false, 'error' => 'ไฟล์สำรองไม่ถูกต้อง (ไม่มี manifest.json)'];
    }

    $manifest = json_decode($manifestRaw, true);
    if (!is_array($manifest) || (int) ($manifest['format_version'] ?? 0) !== BACKUP_FORMAT_VERSION) {
        $zip->close();
        return ['ok' => false, 'error' => 'รูปแบบไฟล์สำรองไม่รองรับ'];
    }

    $fileCount = 0;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if ($name === false || $name === 'manifest.json' || str_ends_with($name, '/')) {
            continue;
        }
        if (backup_target_abs($name) === null) {
            $zip->close();
            return ['ok' => false, 'error' => 'พบไฟล์ที่ไม่อนุญาตในแพ็กเกจ: ' . $name];
        }
        $fileCount++;
    }

    $zip->close();

    return [
        'ok' => true,
        'manifest' => $manifest,
        'file_count' => $fileCount,
    ];
}

function backup_create(string $note = '', ?string $filename = null): array
{
    if (!class_exists('ZipArchive')) {
        return ['ok' => false, 'error' => 'เซิร์ฟเวอร์ไม่รองรับ ZipArchive — เปิด extension zip ใน php.ini'];
    }
    if (!backup_ensure_dir()) {
        return ['ok' => false, 'error' => 'สร้างโฟลเดอร์ data/backups ไม่ได้'];
    }

    $files = backup_collect_files();
    $filename = $filename ?: ('fwd-backup-' . backup_timestamp_slug() . '.zip');
    $filename = preg_replace('/[^a-zA-Z0-9\-_.]/', '', $filename) ?: ('fwd-backup-' . backup_timestamp_slug() . '.zip');
    $path = backup_root() . '/' . $filename;

    $zip = new ZipArchive();
    if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return ['ok' => false, 'error' => 'สร้างไฟล์ ZIP ไม่ได้'];
    }

    $manifest = backup_manifest([
        'note' => trim($note),
        'file_count' => count($files),
    ]);
    $zip->addFromString('manifest.json', json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n");

    foreach ($files as $rel => $abs) {
        $zip->addFile($abs, str_replace('\\', '/', $rel));
    }

    $zip->close();

    if (!is_file($path)) {
        return ['ok' => false, 'error' => 'บันทึกไฟล์สำรองไม่สำเร็จ'];
    }

    return [
        'ok' => true,
        'filename' => $filename,
        'path' => $path,
        'size' => (int) filesize($path),
        'file_count' => count($files),
        'manifest' => $manifest,
    ];
}

function backup_list(): array
{
    if (!is_dir(backup_root())) {
        return [];
    }

    $items = [];
    foreach (glob(backup_root() . '/fwd-backup-*.zip') ?: [] as $path) {
        $filename = basename($path);
        $meta = ['filename' => $filename, 'path' => $path, 'size' => (int) filesize($path), 'created_at' => ''];
        $zip = new ZipArchive();
        if ($zip->open($path) === true) {
            $raw = $zip->getFromName('manifest.json');
            $zip->close();
            if ($raw !== false) {
                $manifest = json_decode($raw, true);
                if (is_array($manifest)) {
                    $meta['created_at'] = (string) ($manifest['created_at'] ?? '');
                    $meta['site_name'] = (string) ($manifest['site_name'] ?? '');
                    $meta['note'] = (string) ($manifest['note'] ?? '');
                    $meta['file_count'] = (int) ($manifest['file_count'] ?? 0);
                }
            }
        }
        if ($meta['created_at'] === '') {
            $meta['created_at'] = backup_format_datetime(null, (int) filemtime($path), $filename);
        } else {
            $meta['created_at'] = backup_format_datetime($meta['created_at'], null, $filename);
        }
        $items[] = $meta;
    }

    usort($items, fn ($a, $b) => strcmp($b['filename'], $a['filename']));
    return $items;
}

function backup_delete(string $filename): array
{
    $filename = basename($filename);
    if (!preg_match('/^fwd-backup-[a-zA-Z0-9\-_.]+\.zip$/', $filename)) {
        return ['ok' => false, 'error' => 'ชื่อไฟล์ไม่ถูกต้อง'];
    }

    $path = backup_root() . '/' . $filename;
    if (!is_file($path)) {
        return ['ok' => false, 'error' => 'ไม่พบไฟล์สำรอง'];
    }

    return unlink($path) ? ['ok' => true] : ['ok' => false, 'error' => 'ลบไฟล์ไม่ได้'];
}

function backup_restore(string $zipPath, bool $safetyBackup = true): array
{
    $validation = backup_validate_zip($zipPath);
    if (!$validation['ok']) {
        return $validation;
    }

    $safetyBackupFile = null;
    if ($safetyBackup) {
        $safety = backup_create('auto-before-restore');
        if (!$safety['ok']) {
            return ['ok' => false, 'error' => 'สร้างสำรองความปลอดภัยก่อนกู้คืนไม่ได้: ' . ($safety['error'] ?? '')];
        }
        $safetyBackupFile = $safety['filename'] ?? null;
    }

    $zip = new ZipArchive();
    if ($zip->open($zipPath) !== true) {
        return ['ok' => false, 'error' => 'เปิดไฟล์สำรองไม่ได้'];
    }

    $restored = 0;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if ($name === false || $name === 'manifest.json' || str_ends_with($name, '/')) {
            continue;
        }

        $target = backup_target_abs($name);
        if ($target === null) {
            continue;
        }

        $dir = dirname($target);
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            $zip->close();
            return ['ok' => false, 'error' => 'สร้างโฟลเดอร์ไม่ได้: ' . $dir];
        }

        $contents = $zip->getFromIndex($i);
        if ($contents === false) {
            $zip->close();
            return ['ok' => false, 'error' => 'อ่านไฟล์จาก ZIP ไม่ได้: ' . $name];
        }

        $tmp = $target . '.' . uniqid('bak', true);
        if (file_put_contents($tmp, $contents) === false) {
            $zip->close();
            return ['ok' => false, 'error' => 'เขียนไฟล์ไม่ได้: ' . $name];
        }

        if (!rename($tmp, $target)) {
            @unlink($tmp);
            $zip->close();
            return ['ok' => false, 'error' => 'ย้ายไฟล์ไม่ได้: ' . $name];
        }

        $restored++;
    }

    $zip->close();

    return [
        'ok' => true,
        'restored' => $restored,
        'manifest' => $validation['manifest'],
        'safety_backup' => $safetyBackupFile,
    ];
}

function backup_is_valid_filename(string $filename): bool
{
    return (bool) preg_match('/^fwd-backup-[a-zA-Z0-9\-_.]+\.zip$/', basename($filename));
}
