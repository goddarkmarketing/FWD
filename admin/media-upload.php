<?php
declare(strict_types=1);

require_once __DIR__ . '/_bootstrap.php';

admin_require_login();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!admin_csrf_verify()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'เซสชันหมดอายุ กรุณารีเฟรชหน้า'], JSON_UNESCAPED_UNICODE);
    exit;
}

$subdir = trim(str_replace('\\', '/', admin_post_string('subdir', 'uploads')), '/');
$fixedName = admin_post_string('fixed_name');

if (!cms_upload_subdir_allowed($subdir)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'โฟลเดอร์ปลายทางไม่ถูกต้อง'], JSON_UNESCAPED_UNICODE);
    exit;
}

$path = cms_upload('image', $subdir, $fixedName !== '' ? $fixedName : null, true);
if ($path === null) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'อัปโหลดไม่สำเร็จ — ใช้ไฟล์ JPG, PNG, WebP หรือ GIF'], JSON_UNESCAPED_UNICODE);
    exit;
}

$preview = admin_image_src($path) ?? ('../' . $path);

echo json_encode([
    'ok' => true,
    'path' => $path,
    'preview' => $preview,
], JSON_UNESCAPED_UNICODE);
