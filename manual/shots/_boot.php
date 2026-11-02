<?php
/**
 * เปิด session แอดมินชั่วคราวสำหรับถ่ายภาพคู่มือ
 */
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['admin_user'] = $_SESSION['admin_user'] ?? 'supakitraksorn@gmail.com';
if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
}

require_once dirname(__DIR__, 2) . '/admin/_bootstrap.php';
require_once dirname(__DIR__, 2) . '/includes/plan-helpers.php';
require_once dirname(__DIR__, 2) . '/includes/cms-plan-meta.php';

function shot_h(?string $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}
