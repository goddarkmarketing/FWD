<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/form-mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    form_json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

if (form_is_honeypot_triggered()) {
    form_json_response(['ok' => true, 'message' => 'ส่งข้อมูลเรียบร้อยแล้ว']);
}

$type = trim((string) ($_POST['form_type'] ?? ''));
if (!in_array($type, ['contact', 'agent-apply'], true)) {
    form_json_response(['ok' => false, 'error' => 'ประเภทฟอร์มไม่ถูกต้อง'], 400);
}

$data = $_POST;
unset($data['form_type'], $data['_hp']);

if ($type === 'contact') {
    if (isset($data['interest']) && !is_array($data['interest'])) {
        $data['interest'] = [$data['interest']];
    }
    $errors = form_validate_contact($data);
} else {
    $errors = form_validate_agent_apply($data);
}

if ($errors !== []) {
    form_json_response(['ok' => false, 'error' => $errors[0], 'errors' => $errors], 422);
}

$sent = form_send_notification($type, $data);

form_json_response([
    'ok' => true,
    'message' => 'ส่งข้อมูลเรียบร้อยแล้ว',
    'email_sent' => $sent,
]);
