<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$adminRoot = dirname(__DIR__);
require_once $adminRoot . '/includes/cms-loader.php';
require_once __DIR__ . '/_helpers.php';

define('ADMIN_BASE', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

function admin_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return ADMIN_BASE . ($path !== '' ? '/' . $path : '');
}

function admin_redirect(string $path): void
{
    header('Location: ' . admin_url($path));
    exit;
}

function admin_flash(string $type, string $message): void
{
    $_SESSION['admin_flash'] = ['type' => $type, 'message' => $message];
}

function admin_flash_get(): ?array
{
    if (empty($_SESSION['admin_flash'])) {
        return null;
    }
    $flash = $_SESSION['admin_flash'];
    unset($_SESSION['admin_flash']);
    return $flash;
}

function admin_csrf_token(): string
{
    if (empty($_SESSION['admin_csrf'])) {
        $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf'];
}

function admin_csrf_verify(): bool
{
    $token = $_POST['_csrf'] ?? '';
    return is_string($token) && hash_equals(admin_csrf_token(), $token);
}

function admin_auth_config(): array
{
    $auth = cms_load('auth');
    if (!is_array($auth) || empty($auth['email']) || empty($auth['password_hash'])) {
        return [
            'email' => 'supakitraksorn@gmail.com',
            'password_hash' => '$2y$10$jzbnOiooOanlbIncQAKGc.iBC7oUzcJM70YNW0LE8qrp5xq0H7bfq',
        ];
    }
    return $auth;
}

function admin_is_logged_in(): bool
{
    return !empty($_SESSION['admin_user']);
}

function admin_require_login(): void
{
    if (!admin_is_logged_in()) {
        admin_redirect('login.php');
    }
}

function admin_h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function admin_post_string(string $key, string $default = ''): string
{
    return trim((string) ($_POST[$key] ?? $default));
}

function admin_post_int(string $key, int $default = 0): int
{
    return (int) ($_POST[$key] ?? $default);
}

function admin_slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\-]+/', '-', $text);

    return trim($text, '-');
}

function admin_verify_password(string $password): bool
{
    $auth = admin_auth_config();
    return password_verify($password, (string) ($auth['password_hash'] ?? ''));
}
