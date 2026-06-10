<?php
/**
 * สร้าง data/cms/auth.json สำหรับ CI (ไฟล์จริงถูก gitignore)
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$authPath = $root . '/data/cms/auth.json';

if (is_readable($authPath)) {
    exit(0);
}

$payload = [
    'email' => 'supakitraksorn@gmail.com',
    'password_hash' => '$2y$10$jzbnOiooOanlbIncQAKGc.iBC7oUzcJM70YNW0LE8qrp5xq0H7bfq',
    'updated_at' => gmdate('c'),
];

if (!is_dir(dirname($authPath)) && !mkdir(dirname($authPath), 0755, true) && !is_dir(dirname($authPath))) {
    fwrite(STDERR, "Cannot create data/cms directory\n");
    exit(1);
}

file_put_contents(
    $authPath,
    json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n"
);
