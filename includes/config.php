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
