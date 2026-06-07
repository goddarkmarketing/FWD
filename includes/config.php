<?php
define('SITE_NAME', 'FWD ประเทศไทย');
define('SITE_TAGLINE', 'Celebrate living');
define('SITE_LOGO_PATH', 'assets/images/fwd-logo-kruda.png');
define('SITE_PHONE', '1351');
define('CONTACT_EMAIL', 'Supakitraksorn@gmail.com');
define('CONTACT_PHONE_1', '062-7416223');
define('CONTACT_PHONE_2', '086-600-4939');
define('CONTACT_PHONE_2_RAW', '0866004939');
define('CONTACT_FACEBOOK', 'https://www.facebook.com/share/1DmTYRVhAt/?mibextid=wwXIfr');
define('CONTACT_LINE', 'https://lin.ee/vYSHQ3O');
define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
define('BASE_URL', (BASE_PATH === '' || BASE_PATH === '/') ? '' : BASE_PATH);

function asset(string $path): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function image_url(string $path): string
{
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    return media_url($path);
}

function media_url(string $path): string
{
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $path = str_replace('\\', '/', ltrim($path, '/'));
    $parts = explode('/', $path);
    return BASE_URL . '/' . implode('/', array_map('rawurlencode', $parts));
}

function page_url(string $file): string
{
    return BASE_URL . '/' . ltrim($file, '/');
}

function is_active(string $file): bool
{
    return basename($_SERVER['PHP_SELF']) === $file;
}

function active_class(string $file, string $class = 'is-active'): string
{
    return is_active($file) ? $class : '';
}

function tel_href(string $displayNumber): string
{
    $digits = preg_replace('/\D+/', '', $displayNumber);
    return $digits !== '' ? 'tel:' . $digits : '#';
}

/**
 * Hero image for mobile (prefers hero-banner-mobile.*), 1:1 on homepage.
 */
function hero_cover_mobile_image(): ?string
{
    static $cached = false;
    static $path = null;

    if ($cached) {
        return $path;
    }
    $cached = true;

    $dir = dirname(__DIR__) . '/assets/cover';
    if (!is_dir($dir)) {
        return null;
    }

    $extensions = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
    $files = [];
    foreach ($extensions as $ext) {
        $files = array_merge($files, glob($dir . '/*.' . $ext) ?: [], glob($dir . '/*.' . strtoupper($ext)) ?: []);
    }
    $files = array_values(array_unique($files));
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);

    foreach ($files as $file) {
        if (preg_match('/^hero-banner-mobile\./i', basename($file))) {
            $path = 'assets/cover/' . basename($file);
            return $path;
        }
    }

    return null;
}

/**
 * First hero image from assets/cover (prefers hero-banner.*).
 */
function hero_cover_image(): ?string
{
    static $cached = false;
    static $path = null;

    if ($cached) {
        return $path;
    }
    $cached = true;

    $dir = dirname(__DIR__) . '/assets/cover';
    if (!is_dir($dir)) {
        return null;
    }

    $extensions = ['png', 'jpg', 'jpeg', 'webp', 'gif'];
    $files = [];
    foreach ($extensions as $ext) {
        $files = array_merge($files, glob($dir . '/*.' . $ext) ?: [], glob($dir . '/*.' . strtoupper($ext)) ?: []);
    }
    $files = array_values(array_unique($files));
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);

    foreach ($files as $file) {
        if (preg_match('/^hero-banner\./i', basename($file))) {
            $path = 'assets/cover/' . basename($file);
            return $path;
        }
    }

    if ($files !== []) {
        $path = 'assets/cover/' . basename($files[0]);
        return $path;
    }

    return null;
}

require_once __DIR__ . '/plan-helpers.php';
