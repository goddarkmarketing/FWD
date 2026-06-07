<?php
/**
 * Placeholder product image (SVG) until real assets are provided.
 */
$cat = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['cat'] ?? 'all'));
$n = max(1, min(99, (int) ($_GET['n'] ?? 1)));

$palettes = [
    'life-accident' => ['#E87722', '#183028', '#FDF2EB'],
    'health' => ['#0097A9', '#183028', '#E8F6F8'],
    'critical' => ['#C41E3A', '#183028', '#FCE8EC'],
    'investment' => ['#5C2D91', '#183028', '#F3EDF9'],
    'savings' => ['#2E7D32', '#183028', '#E8F5E9'],
    'all' => ['#E87722', '#183028', '#F5F5F5'],
];
[$accent, $text, $bg] = $palettes[$cat] ?? $palettes['all'];

$labels = [
    'life-accident' => 'ชีวิตและอุบัติเหตุ',
    'health' => 'สุขภาพ',
    'critical' => 'โรคร้ายแรง',
    'investment' => 'การลงทุน',
    'savings' => 'สะสมทรัพย์',
    'all' => 'ผลิตภัณฑ์',
];
$label = $labels[$cat] ?? 'ผลิตภัณฑ์';

header('Content-Type: image/svg+xml; charset=utf-8');
header('Cache-Control: public, max-age=86400');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<svg xmlns="http://www.w3.org/2000/svg" width="600" height="450" viewBox="0 0 600 450" role="img" aria-label="Mockup <?= htmlspecialchars($label) ?>">
  <defs>
    <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="<?= $bg ?>"/>
      <stop offset="100%" stop-color="#ffffff"/>
    </linearGradient>
  </defs>
  <rect width="600" height="450" fill="url(#g)"/>
  <rect x="40" y="280" width="520" height="120" rx="16" fill="#ffffff" opacity="0.92"/>
  <circle cx="120" cy="160" r="72" fill="<?= $accent ?>" opacity="0.15"/>
  <circle cx="480" cy="100" r="48" fill="<?= $accent ?>" opacity="0.1"/>
  <rect x="72" y="120" width="200" height="14" rx="7" fill="<?= $accent ?>" opacity="0.35"/>
  <rect x="72" y="148" width="280" height="10" rx="5" fill="<?= $text ?>" opacity="0.12"/>
  <rect x="72" y="168" width="240" height="10" rx="5" fill="<?= $text ?>" opacity="0.08"/>
  <text x="300" y="345" text-anchor="middle" font-family="sans-serif" font-size="22" font-weight="700" fill="<?= $text ?>">FWD Mockup</text>
  <text x="300" y="375" text-anchor="middle" font-family="sans-serif" font-size="15" fill="<?= $accent ?>"><?= htmlspecialchars($label) ?> #<?= $n ?></text>
</svg>
