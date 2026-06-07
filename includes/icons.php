<?php
/**
 * Inline SVG icons for header / footer.
 */
function icon_svg(string $name, int $size = 20): string
{
    $icons = [
        'phone' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>',
        'mail' => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="m22 6-10 7L2 6"/>',
        'chat' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        'facebook' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
        'line' => '<path d="M19.5 11.5c0 4.2-4 7.6-9 7.6-.9 0-1.8-.1-2.6-.3L4 21l1.4-3.4C4.5 16 4 14.3 4 12.5 4 8.3 8 5 12.5 5S19.5 7.8 19.5 11.5z"/>',
    ];

    $body = $icons[$name] ?? '';
    $s = (int) $size;

    return '<svg class="icon icon--' . htmlspecialchars($name) . '" width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $body . '</svg>';
}

/** Official LINE logo path (Simple Icons, viewBox 0 0 24 24) */
function line_logo_path(): string
{
    return 'M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314';
}

function icon_line_brand(int $size = 22): string
{
    $s = (int) $size;
    return '<svg class="icon icon--line-brand" width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" aria-hidden="true">'
        . '<rect width="24" height="24" rx="6" fill="#06C755"/>'
        . '<path fill="#fff" d="' . line_logo_path() . '"/>'
        . '</svg>';
}

function icon_facebook_brand(int $size = 22): string
{
    $s = (int) $size;
    return '<svg class="icon icon--facebook-brand" width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" aria-hidden="true">'
        . '<rect width="24" height="24" rx="6" fill="#1877F2"/>'
        . '<path fill="#fff" d="M16.5 12.5h-2.2v7H12v-7H10v-2.5h2V8.5c0-1.7 1-2.7 2.6-2.7.8 0 1.5.1 1.7.1v2h-1.2c-.7 0-.9.4-.9 1v1.4h2.3l-.3 2.5z"/>'
        . '</svg>';
}

/** Glyph only — ใช้ใน FAB / วงกลมพื้นหลังสีแบรนด์ */
function icon_brand_line(int $size = 22): string
{
    $s = (int) $size;
    return '<svg class="icon icon--brand-line" width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" aria-hidden="true">'
        . '<path fill="currentColor" d="' . line_logo_path() . '"/>'
        . '</svg>';
}

function icon_brand_facebook(int $size = 22): string
{
    $s = (int) $size;
    return '<svg class="icon icon--brand-facebook" width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" aria-hidden="true">'
        . '<path fill="currentColor" d="M14.5 8.5h2.3V5.5h-2.6c-2.5 0-3.9 1.5-3.9 4v1.5H8v3h2.3V19h3.5v-5h2.9l.4-3h-3.3v-1.2c0-.9.2-1.3 1.3-1.3z"/>'
        . '</svg>';
}

function icon_brand_gmail(int $size = 22): string
{
    $s = (int) $size;
    return '<svg class="icon icon--brand-gmail" width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" aria-hidden="true">'
        . '<path fill="currentColor" d="M20 7.5V18a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7.5l8 5 8-5z"/>'
        . '<path fill="currentColor" d="M20 6.5 12 11.5 4 6.5V6a1 1 0 0 1 1-1h14a1 1 0 0 1 1 .5v.5z"/>'
        . '</svg>';
}
