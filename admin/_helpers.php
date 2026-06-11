<?php
declare(strict_types=1);

/**
 * Resolve a CMS image path to a URL relative to admin/ (for <img src>).
 */
function admin_image_src(?string $path): ?string
{
    if ($path === null || trim($path) === '') {
        return null;
    }

    $path = str_replace('\\', '/', trim($path));
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $path = ltrim($path, '/');
    $abs = dirname(__DIR__) . '/' . $path;
    if (!is_file($abs)) {
        return null;
    }

    $parts = explode('/', $path);
    return '../' . implode('/', array_map('rawurlencode', $parts));
}

/**
 * Render an image preview when the file exists.
 */
function admin_image_preview(?string $path, string $alt = '', string $size = 'md'): void
{
    $src = admin_image_src($path);
    if ($src === null) {
        echo '<div class="admin-img-preview-wrap admin-img-preview-wrap--' . admin_h($size) . ' is-hidden" data-admin-preview>';
        echo '<img class="admin-img-preview" src="" alt="' . admin_h($alt) . '" loading="lazy">';
        echo '</div>';
        return;
    }

    echo '<div class="admin-img-preview-wrap admin-img-preview-wrap--' . admin_h($size) . '" data-admin-preview>';
    echo '<img class="admin-img-preview" src="' . admin_h($src) . '" alt="' . admin_h($alt) . '" loading="lazy">';
    echo '</div>';
}

/**
 * Inline upload control (AJAX → media-upload.php).
 *
 * @param array{fixed_name?: string, multiple?: bool} $opts
 */
function admin_inline_upload_control(string $subdir, array $opts = []): void
{
    $fixedName = $opts['fixed_name'] ?? '';
    $multiple = !empty($opts['multiple']);
    $attrs = 'accept="image/*" data-admin-inline-upload data-admin-upload-subdir="' . admin_h($subdir) . '"';
    if ($fixedName !== '') {
        $attrs .= ' data-admin-upload-fixed="' . admin_h($fixedName) . '"';
    }
    if ($multiple) {
        $attrs .= ' multiple';
    }

    echo '<div class="admin-upload-row">';
    echo '<label class="admin-btn admin-btn--outline admin-btn--sm admin-upload-label">';
    echo '<input type="file" ' . $attrs . ' hidden>';
    echo 'อัปโหลดรูป</label>';
    echo '<span class="admin-upload-status" data-admin-upload-status aria-live="polite"></span>';
    echo '</div>';
}

/**
 * Preview + inline upload (no path field) — e.g. Hero banner.
 *
 * @param array{fixed_name?: string, size?: string} $opts
 */
function admin_inline_image_upload(string $label, string $subdir, ?string $currentPath = null, array $opts = []): void
{
    $size = $opts['size'] ?? 'md';
    $uploadOpts = [];
    if (!empty($opts['fixed_name'])) {
        $uploadOpts['fixed_name'] = $opts['fixed_name'];
    }

    echo '<div class="form-row admin-image-field" data-admin-image-field data-admin-upload-only>';
    echo '<label>' . admin_h($label) . '</label>';
    admin_image_preview($currentPath, $label, $size);
    admin_inline_upload_control($subdir, $uploadOpts);
    echo '</div>';
}

/**
 * Path text field + optional file upload + preview.
 *
 * @param array{id?: string, hint?: string, size?: string, subdir?: string, hide_path?: bool} $opts
 */
function admin_image_field(string $name, string $label, ?string $value = '', ?string $uploadName = null, array $opts = []): void
{
    $id = $opts['id'] ?? $name;
    $size = $opts['size'] ?? 'md';
    $subdir = $opts['subdir'] ?? null;
    $value = $value ?? '';

    echo '<div class="form-row admin-image-field" data-admin-image-field>';
    echo '<label for="' . admin_h($id) . '">' . admin_h($label) . '</label>';
    admin_image_preview($value, $label, $size);
    if (!empty($opts['hide_path'])) {
        echo '<input type="hidden" id="' . admin_h($id) . '" name="' . admin_h($name) . '" value="' . admin_h($value) . '" data-admin-image-path>';
    } else {
        echo '<input type="text" id="' . admin_h($id) . '" name="' . admin_h($name) . '" value="' . admin_h($value) . '" data-admin-image-path class="admin-image-path-input">';
    }
    if ($subdir !== null) {
        admin_inline_upload_control($subdir);
    } elseif ($uploadName !== null) {
        echo '<input type="file" name="' . admin_h($uploadName) . '" accept="image/*" data-admin-image-upload style="margin-top:.5rem">';
    }
    if (!empty($opts['hint'])) {
        echo '<p class="form-hint">' . $opts['hint'] . '</p>';
    }
    echo '</div>';
}

/**
 * ฟอร์ม POST แบบ inline สำหรับลบ/รีเซ็ตจากตารางรายการ
 *
 * @param array<string, string> $fields
 */
function admin_inline_post_form(array $fields, string $buttonLabel, string $confirmMessage, string $buttonClass = 'admin-btn admin-btn--danger admin-btn--sm'): void
{
    echo '<form method="post" class="admin-inline-form" onsubmit="return confirm(' . json_encode($confirmMessage, JSON_UNESCAPED_UNICODE) . ')">';
    echo '<input type="hidden" name="_csrf" value="' . admin_h(admin_csrf_token()) . '">';
    foreach ($fields as $name => $value) {
        echo '<input type="hidden" name="' . admin_h($name) . '" value="' . admin_h($value) . '">';
    }
    echo '<button type="submit" class="' . admin_h($buttonClass) . '">' . admin_h($buttonLabel) . '</button>';
    echo '</form>';
}
