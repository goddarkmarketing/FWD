<?php
$slug = $argv[1] ?? 'precious-care';
$html = file_get_contents(__DIR__ . "/../data/fwd-pages/$slug.htm");
$out = [];
if (preg_match('/<title>([^<]+)<\/title>/u', $html, $m)) {
    $out['title'] = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
if (preg_match('/name="description"\s+content="([^"]*)"/', $html, $m)) {
    $out['description'] = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
if (preg_match('/property="og:description"\s+content="([^"]*)"/', $html, $m)) {
    $out['og_description'] = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
