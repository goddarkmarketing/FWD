<?php
require_once __DIR__ . '/cms-loader.php';

$cmsSite = cms_load('site') ?? [];
$siteCfg = array_merge([
    'site_name' => 'FWD AGENT ประเทศไทย',
    'site_tagline' => 'Celebrate living',
    'site_logo_path' => 'assets/images/fwd-logo-kruda.png',
    'site_phone' => '1351',
    'contact_email' => 'Supakitraksorn@gmail.com',
    'contact_phone_1' => '062-7416223',
    'contact_phone_2' => '086-600-4939',
    'contact_phone_2_raw' => '0866004939',
    'contact_facebook' => 'https://www.facebook.com/share/1DmTYRVhAt/?mibextid=wwXIfr',
    'contact_facebook_name' => 'FWD ประกันชีวิต Agent thailand',
    'contact_line' => 'https://lin.ee/vYSHQ3O',
    'agent_office_name' => 'คุณ ลัดดา รักซ้อน',
    'agent_license_no' => '5801089096',
    'agent_license_image' => 'assets/images/agent-license.png',
    'hero_alt' => 'FWD by kruda — ประกันที่เข้าใจง่าย ให้คุณใช้ชีวิตได้เต็มที่',
], $cmsSite);

define('SITE_NAME', $siteCfg['site_name']);
define('SITE_TAGLINE', $siteCfg['site_tagline']);
define('SITE_LOGO_PATH', $siteCfg['site_logo_path']);
define('SITE_PHONE', $siteCfg['site_phone']);
define('CONTACT_EMAIL', $siteCfg['contact_email']);
define('CONTACT_PHONE_1', $siteCfg['contact_phone_1']);
define('CONTACT_PHONE_2', $siteCfg['contact_phone_2']);
define('CONTACT_PHONE_2_RAW', $siteCfg['contact_phone_2_raw']);
define('CONTACT_FACEBOOK', $siteCfg['contact_facebook']);
define('CONTACT_FACEBOOK_NAME', $siteCfg['contact_facebook_name']);
define('CONTACT_LINE', $siteCfg['contact_line']);
define('AGENT_OFFICE_NAME', $siteCfg['agent_office_name']);
define('AGENT_LICENSE_NO', $siteCfg['agent_license_no']);
define('AGENT_LICENSE_IMAGE', $siteCfg['agent_license_image']);
define('HERO_ALT', $siteCfg['hero_alt']);

if (!defined('FWD_STATIC_BUILD')) {
    define('FWD_STATIC_BUILD', getenv('FWD_STATIC_BUILD') === '1');
}

$fwdBaseOverride = getenv('FWD_BASE_URL');
if ($fwdBaseOverride !== false && $fwdBaseOverride !== '') {
    $fwdBase = rtrim(str_replace('\\', '/', $fwdBaseOverride), '/');
    define('BASE_PATH', $fwdBase);
    define('BASE_URL', $fwdBase === '' ? '' : $fwdBase);
} else {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
    define('BASE_URL', (BASE_PATH === '' || BASE_PATH === '/') ? '' : BASE_PATH);
}

function static_page_url(string $file): string
{
    $fragment = '';
    if (($pos = strpos($file, '#')) !== false) {
        $fragment = substr($file, $pos);
        $file = substr($file, 0, $pos);
    }

    if (($qpos = strpos($file, '?')) !== false) {
        $path = substr($file, 0, $qpos);
        parse_str(substr($file, $qpos + 1), $params);
        if ($path === 'plan.php' && !empty($params['slug'])) {
            return BASE_URL . '/plan/' . rawurlencode($params['slug']) . '/' . $fragment;
        }
        if ($path === 'article.php' && !empty($params['slug'])) {
            return BASE_URL . '/article/' . rawurlencode($params['slug']) . '/' . $fragment;
        }
        if ($path === 'contact.php') {
            return BASE_URL . '/contact.html?' . http_build_query($params) . $fragment;
        }
    }

    if ($file === '' || $file === 'index.php') {
        return $fragment !== '' ? BASE_URL . '/' . $fragment : BASE_URL . '/';
    }

    return BASE_URL . '/' . preg_replace('/\.php$/', '.html', $file) . $fragment;
}

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

function site_origin(): string
{
    static $origin = null;
    if ($origin !== null) {
        return $origin;
    }

    $override = getenv('FWD_SITE_ORIGIN');
    if ($override !== false && $override !== '') {
        $origin = rtrim(str_replace('\\', '/', $override), '/');
        return $origin;
    }

    if (PHP_SAPI === 'cli' || (defined('FWD_STATIC_BUILD') && FWD_STATIC_BUILD)) {
        $origin = 'https://goddarkmarketing.github.io';
        return $origin;
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $origin = $scheme . '://' . $host;

    return $origin;
}

function ensure_absolute_url(string $url): string
{
    if (preg_match('#^https?://#i', $url)) {
        return $url;
    }

    if ($url === '') {
        return site_origin() . (BASE_URL === '' ? '/' : rtrim(BASE_URL, '/') . '/');
    }

    if ($url[0] !== '/') {
        $url = '/' . $url;
    }

    return site_origin() . $url;
}

function current_canonical_url(): string
{
    $file = basename($_SERVER['PHP_SELF'] ?? 'index.php');
    if ($file === 'index.php') {
        return ensure_absolute_url(BASE_URL === '' ? '/' : rtrim(BASE_URL, '/') . '/');
    }
    if ($file === 'plan.php' && !empty($_GET['slug'])) {
        return ensure_absolute_url(rtrim(BASE_URL, '/') . '/plan/' . rawurlencode((string) $_GET['slug']) . '/');
    }
    if ($file === 'article.php' && !empty($_GET['slug'])) {
        return ensure_absolute_url(rtrim(BASE_URL, '/') . '/article/' . rawurlencode((string) $_GET['slug']) . '/');
    }

    return ensure_absolute_url(page_url($file));
}

function site_share_image_path(): ?string
{
    static $resolved = false;
    static $path = null;

    if ($resolved) {
        return $path;
    }
    $resolved = true;

    $custom = trim((string) cms_get('site', 'og_image', ''));
    if ($custom !== '') {
        $path = $custom;
        return $path;
    }

    $candidates = [
        'assets/images/og-share.jpg',
        'assets/images/og-share.png',
        'assets/images/og-share.webp',
    ];
    foreach ($candidates as $candidate) {
        if (is_file(dirname(__DIR__) . '/' . $candidate)) {
            $path = $candidate;
            return $path;
        }
    }

    $hero = hero_cover_image();
    if ($hero !== null) {
        $path = $hero;
        return $path;
    }

    return null;
}

function site_share_image_meta(): ?array
{
    $relativePath = site_share_image_path();
    if ($relativePath === null) {
        return null;
    }

    $absoluteFile = dirname(__DIR__) . '/' . str_replace('\\', '/', ltrim($relativePath, '/'));
    $width = 1200;
    $height = 630;
    if (is_file($absoluteFile) && function_exists('getimagesize')) {
        $info = @getimagesize($absoluteFile);
        if (is_array($info) && !empty($info[0]) && !empty($info[1])) {
            $width = (int) $info[0];
            $height = (int) $info[1];
        }
    }

    return [
        'url' => ensure_absolute_url(image_url($relativePath)),
        'width' => $width,
        'height' => $height,
        'alt' => defined('HERO_ALT') ? HERO_ALT : SITE_NAME,
    ];
}

function page_url(string $file): string
{
    if (FWD_STATIC_BUILD) {
        return static_page_url($file);
    }

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
require_once __DIR__ . '/plan-brochures.php';
