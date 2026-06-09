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
 * Path text field + optional file upload + preview.
 *
 * @param array{id?: string, hint?: string, size?: string} $opts
 */
function admin_image_field(string $name, string $label, ?string $value = '', ?string $uploadName = null, array $opts = []): void
{
    $id = $opts['id'] ?? $name;
    $size = $opts['size'] ?? 'md';
    $value = $value ?? '';

    echo '<div class="form-row admin-image-field" data-admin-image-field>';
    echo '<label for="' . admin_h($id) . '">' . admin_h($label) . '</label>';
    admin_image_preview($value, $label, $size);
    echo '<input type="text" id="' . admin_h($id) . '" name="' . admin_h($name) . '" value="' . admin_h($value) . '" data-admin-image-path>';
    if ($uploadName !== null) {
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
