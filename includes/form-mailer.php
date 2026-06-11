<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function form_notify_email(): string
{
    $email = trim((string) (cms_get('site', 'form_notify_email', '') ?: CONTACT_EMAIL));

    return $email !== '' ? $email : 'Supakitraksorn@gmail.com';
}

function form_submit_endpoint(): string
{
    if (defined('FWD_STATIC_BUILD') && FWD_STATIC_BUILD) {
        $override = trim((string) cms_get('site', 'form_submit_php_url', ''));
        if ($override !== '') {
            return $override;
        }

        return 'https://formsubmit.co/ajax/' . rawurlencode(form_notify_email());
    }

    return page_url('submit-form.php');
}

function form_is_honeypot_triggered(): bool
{
    return trim((string) ($_POST['_hp'] ?? '')) !== '';
}

/** @return list<string> */
function form_validate_contact(array $data): array
{
    $errors = [];
    if (trim((string) ($data['first_name'] ?? '')) === '') {
        $errors[] = 'กรุณากรอกชื่อ';
    }
    if (trim((string) ($data['last_name'] ?? '')) === '') {
        $errors[] = 'กรุณากรอกนามสกุล';
    }
    if (trim((string) ($data['phone'] ?? '')) === '') {
        $errors[] = 'กรุณากรอกเบอร์โทรศัพท์';
    }
    $email = trim((string) ($data['email'] ?? ''));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'กรุณากรอกอีเมลที่ถูกต้อง';
    }
    if (empty($data['consent'])) {
        $errors[] = 'กรุณายอมรับนโยบายความเป็นส่วนตัว';
    }

    return $errors;
}

/** @return list<string> */
function form_validate_agent_apply(array $data): array
{
    $errors = [];
    $required = [
        'full_name' => 'ชื่อ-นามสกุล',
        'dob' => 'วันเกิด',
        'phone' => 'เบอร์โทรศัพท์',
        'email' => 'อีเมล',
        'address_no' => 'บ้านเลขที่ / หมู่',
        'subdistrict' => 'ตำบล / แขวง',
        'district' => 'อำเภอ / เขต',
        'province' => 'จังหวัด',
        'postal' => 'รหัสไปรษณีย์',
        'education' => 'วุฒิการศึกษา',
    ];
    foreach ($required as $key => $label) {
        if (trim((string) ($data[$key] ?? '')) === '') {
            $errors[] = 'กรุณากรอก' . $label;
        }
    }
    $email = trim((string) ($data['email'] ?? ''));
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'อีเมลไม่ถูกต้อง';
    }
    if (empty($data['consent'])) {
        $errors[] = 'กรุณายอมรับนโยบายความเป็นส่วนตัว';
    }

    return $errors;
}

function form_label_map(string $type): array
{
    if ($type === 'agent-apply') {
        return [
            'full_name' => 'ชื่อ-นามสกุล',
            'dob' => 'วันเกิด',
            'age' => 'อายุ',
            'phone' => 'เบอร์โทรศัพท์',
            'email' => 'อีเมล',
            'address_no' => 'บ้านเลขที่ / หมู่',
            'street' => 'ถนน',
            'subdistrict' => 'ตำบล / แขวง',
            'district' => 'อำเภอ / เขต',
            'province' => 'จังหวัด',
            'postal' => 'รหัสไปรษณีย์',
            'education' => 'วุฒิการศึกษา',
            'major' => 'สาขาวิชา',
            'experience' => 'ประสบการณ์การทำงาน',
        ];
    }

    return [
        'contact_method' => 'ช่องทางติดต่อกลับ',
        'interest' => 'สนใจผลิตภัณฑ์',
        'first_name' => 'ชื่อ',
        'last_name' => 'นามสกุล',
        'dob' => 'วันเกิด',
        'phone' => 'เบอร์โทรศัพท์',
        'email' => 'อีเมล',
        'province' => 'จังหวัด',
        'preferred_time' => 'เวลาที่สะดวก',
        'plan_slug' => 'แผนที่สนใจ (slug)',
        'plan_name' => 'แผนที่สนใจ',
    ];
}

function form_format_contact_method(string $value): string
{
    return match ($value) {
        'phone' => 'โทรศัพท์',
        'face' => 'พบตัวต่อตัว',
        default => $value,
    };
}

function form_format_preferred_time(string $value): string
{
    return match ($value) {
        '09-12' => '09:00 – 12:00',
        '12-15' => '12:00 – 15:00',
        '15-18' => '15:00 – 18:00',
        '19-21' => '19:00 – 21:00',
        default => $value,
    };
}

function form_format_value(string $key, mixed $value): string
{
    if (is_array($value)) {
        $value = implode(', ', array_map('strval', $value));
    }
    $value = trim((string) $value);
    if ($value === '') {
        return '—';
    }
    if ($key === 'contact_method') {
        return form_format_contact_method($value);
    }
    if ($key === 'preferred_time') {
        return form_format_preferred_time($value);
    }

    return $value;
}

function form_build_email_body(string $type, array $data): string
{
    $labels = form_label_map($type);
    $title = $type === 'agent-apply'
        ? 'ใบสมัครตัวแทนใหม่จากเว็บไซต์'
        : 'คำขอปรึกษาใหม่จากเว็บไซต์';

    $lines = [$title, str_repeat('=', 40), ''];

    foreach ($labels as $key => $label) {
        if (!array_key_exists($key, $data)) {
            continue;
        }
        $lines[] = $label . ': ' . form_format_value($key, $data[$key]);
    }

    $lines[] = '';
    $lines[] = 'ส่งเมื่อ: ' . date('d/m/Y H:i:s');
    $lines[] = 'จาก: ' . SITE_NAME;

    return implode("\n", $lines);
}

function form_log_submission(string $type, array $data): void
{
    $dir = dirname(__DIR__) . '/data/form-submissions';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return;
    }

    $htaccess = $dir . '/.htaccess';
    if (!is_file($htaccess)) {
        file_put_contents($htaccess, "Require all denied\n");
    }

    $file = $dir . '/' . $type . '-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.json';
    file_put_contents($file, json_encode([
        'type' => $type,
        'submitted_at' => date('c'),
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n");
}

function form_send_notification(string $type, array $data): bool
{
    $to = form_notify_email();
    $subject = $type === 'agent-apply'
        ? '[' . SITE_NAME . '] ใบสมัครตัวแทนใหม่'
        : '[' . SITE_NAME . '] คำขอปรึกษาใหม่';

    $body = form_build_email_body($type, $data);
    $replyEmail = trim((string) ($data['email'] ?? ''));
    $replyName = $type === 'agent-apply'
        ? trim((string) ($data['full_name'] ?? ''))
        : trim((string) (($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')));

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . SITE_NAME . ' <noreply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '>',
    ];
    if ($replyEmail !== '' && filter_var($replyEmail, FILTER_VALIDATE_EMAIL)) {
        $fromName = $replyName !== '' ? $replyName : $replyEmail;
        $headers[] = 'Reply-To: ' . $fromName . ' <' . $replyEmail . '>';
    }

    form_log_submission($type, $data);

    if (!function_exists('mail')) {
        return false;
    }

    return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));
}

function form_json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}
