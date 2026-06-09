<?php
/**
 * CMS loader — อ่าน/เขียน JSON ใน data/cms/
 */

function cms_root(): string
{
    return dirname(__DIR__) . '/data/cms';
}

function cms_file_path(string $key): string
{
    if (preg_match('#^pages/([a-z0-9\-]+)$#', $key, $m)) {
        return cms_root() . '/pages/' . $m[1] . '.json';
    }
    if (preg_match('#^plans/([a-z0-9\-]+)$#', $key, $m)) {
        return cms_root() . '/plans/' . $m[1] . '.json';
    }

    $map = [
        'site' => 'site.json',
        'homepage' => 'homepage.json',
        'footer' => 'footer.json',
        'categories' => 'categories.json',
        'articles' => 'articles.json',
        'promotions' => 'promotions.json',
        'catalog' => 'catalog-overrides.json',
        'auth' => 'auth.json',
    ];

    $file = $map[$key] ?? basename($key);
    if (!str_ends_with($file, '.json')) {
        $file .= '.json';
    }

    return cms_root() . '/' . $file;
}

function cms_load(string $key, $default = null)
{
    $path = cms_file_path($key);
    if (!is_readable($path)) {
        return $default;
    }

    $raw = file_get_contents($path);
    if ($raw === false || trim($raw) === '') {
        return $default;
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : $default;
}

function cms_save(string $key, array $data): bool
{
    $path = cms_file_path($key);
    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return false;
    }

    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($json === false) {
        return false;
    }

    $tmp = $path . '.' . uniqid('tmp', true);
    if (file_put_contents($tmp, $json . "\n") === false) {
        return false;
    }

    return rename($tmp, $path);
}

function cms_get(string $key, string $field, $default = null)
{
    $data = cms_load($key);
    if (!is_array($data)) {
        return $default;
    }

    return $data[$field] ?? $default;
}

function cms_merge_defaults(array $defaults, ?array $overrides): array
{
    if ($overrides === null) {
        return $defaults;
    }

    return array_replace_recursive($defaults, $overrides);
}

function cms_page(string $pageId, array $defaults = []): array
{
    $stored = cms_load('pages/' . $pageId);
    return cms_merge_defaults($defaults, $stored);
}

function cms_plan_slugs(): array
{
    require_once __DIR__ . '/plan-helpers.php';
    $slugs = [];
    foreach (plan_catalog() as $item) {
        if (!empty($item['slug'])) {
            $slugs[] = $item['slug'];
        }
    }
    sort($slugs, SORT_NATURAL);
    return $slugs;
}

function cms_plan_override(string $slug): array
{
    return cms_load('plans/' . $slug, []) ?? [];
}

function cms_upload(string $fieldName, string $subdir = 'uploads'): ?string
{
    if (empty($_FILES[$fieldName]['tmp_name']) || !is_uploaded_file($_FILES[$fieldName]['tmp_name'])) {
        return null;
    }

    $file = $_FILES[$fieldName];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    $destDir = dirname(__DIR__) . '/assets/' . trim($subdir, '/');
    if (!is_dir($destDir) && !mkdir($destDir, 0755, true) && !is_dir($destDir)) {
        return null;
    }

    $basename = preg_replace('/[^a-z0-9\-]+/i', '-', pathinfo($file['name'], PATHINFO_FILENAME));
    $basename = trim($basename, '-') ?: 'file';
    $filename = $basename . '-' . date('Ymd-His') . '.' . $ext;
    $dest = $destDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }

    return 'assets/' . trim($subdir, '/') . '/' . $filename;
}
