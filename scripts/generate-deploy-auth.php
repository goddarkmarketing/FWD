<?php
/**
 * สร้าง data/cms/auth.json สำหรับ deploy ขึ้นโฮสต์ PHP (อ่านจาก env)
 *
 * Usage:
 *   DEPLOY_ADMIN_EMAIL=... DEPLOY_ADMIN_PASSWORD=... php scripts/generate-deploy-auth.php
 */
declare(strict_types=1);

$root = dirname(__DIR__);
require_once $root . '/includes/cms-loader.php';

$email = trim((string) (getenv('DEPLOY_ADMIN_EMAIL') ?: getenv('STAGING_ADMIN_EMAIL')));
$password = (string) (getenv('DEPLOY_ADMIN_PASSWORD') ?: getenv('STAGING_ADMIN_PASSWORD'));

if ($email === '' || $password === '') {
    fwrite(STDERR, "Skip: set DEPLOY_ADMIN_EMAIL and DEPLOY_ADMIN_PASSWORD (or STAGING_*) to generate auth.json\n");
    exit(0);
}

$dir = cms_root();
if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
    fwrite(STDERR, "Cannot create CMS directory\n");
    exit(1);
}

cms_save('auth', [
    'email' => $email,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'updated_at' => date('c'),
]);

echo "Created deploy auth for {$email}\n";
