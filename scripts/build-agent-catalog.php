<?php
/**
 * สร้าง includes/plans-catalog-definitions.php จาก data/fwd-agent-products.json (channel=agent เท่านั้น)
 * Usage: php scripts/build-agent-catalog.php
 */
$root = dirname(__DIR__);
$data = json_decode(file_get_contents($root . '/data/fwd-agent-products.json'), true);
$existing = require $root . '/includes/plans-catalog-definitions.php';

$existingBySlug = [];
foreach ($existing as $cat => $group) {
    foreach ($group['products'] as $p) {
        $existingBySlug[$p['slug']] = $p;
    }
}

$slugAliases = [
    'fwd-power-saving' => 'fwd-power-saving-12-6',
];

$titles = [
    'be-sure' => 'Be Sure',
    'cancer-fighter' => 'Cancer Fighter',
    'cancer-knockout' => 'Cancer Knockout',
    'ci-50' => 'CI 50',
    'ci-all-in-one' => 'CI All-in-One',
    'ci-cash-a-day' => 'CI Cash A Day',
    'ci-fixed-pay' => 'CI Fixed Pay',
    'ci-med-all' => 'CI Med-ALL',
    'ci-reclaim-recare' => 'CI Re-Claim Re-Care',
    'ci-reclaim-recare-plus' => 'CI Re-Claim Re-Care Plus',
    'fwd-for-saving-25-15' => 'FWD For Saving 25/15',
    'fwd-freedom-linked-plus' => 'FWD Freedom Linked Plus 15/5',
    'fwd-future-linked-99-9' => 'FWD Future Linked 99/9',
    'fwd-life-saving-18-9' => 'FWD Life Saving 18/9',
    'fwd-life-saving-30-15' => 'FWD Life Saving 30/15',
    'fwd-one-linked' => 'FWD One Linked',
    'fwd-power-saving' => 'FWD Power Saving 12/6',
    'fwd-savvy-pension' => 'FWD Savvy Pension',
    'fwd-sure-pension' => 'FWD Sure Pension',
    'fwd-ultra-linked-99-99' => 'FWD Ultra Linked 99/99',
    'health-family-sharing' => 'Health Family Sharing',
    'heritage-plus' => 'FWD Heritage Plus',
    'hospital-benefit' => 'Hospital Benefit',
    'hospital-benefit-plus' => 'Hospital Benefit Plus',
    'lifetime-return' => 'FWD Lifetime Return 99/15',
    'opd-plus' => 'OPD Plus',
    'prakan-kon-jing' => 'ประกันคนจริง',
    'prakan-kon-kla' => 'ประกันคนกล้า',
    'prakan-kon-kla-max' => 'ประกันคนกล้า MAX',
    'prakan-kon-klang' => 'ประกันคนแกร่ง',
    'precious-care' => 'FWD Precious Care',
    'precious-care-for-kids' => 'FWD Precious Care for Kids',
    'precious-protection' => 'FWD Precious Protection',
    'prima-care' => 'Prima Care',
    'value-protector' => 'Value Protector',
    'whole-life' => 'Whole Life 99/99',
    'whole-life-extra' => 'Whole Life Extra',
];

function category_from_href(string $href): string
{
    if (str_contains($href, '/health-insurance/')) {
        return 'health';
    }
    if (str_contains($href, '/critical-illness-insurance/')) {
        return 'critical';
    }
    if (str_contains($href, '/life-and-accident-insurance/') || str_contains($href, '/accident/')) {
        return 'life-accident';
    }
    if (str_contains($href, '/savings-insurance/')) {
        return 'savings';
    }
    if (str_contains($href, '/investment-linked-insurance/')) {
        return 'investment';
    }
    return 'life-accident';
}

function clean_desc(string $text): string
{
    $text = preg_replace('/สอบถามรายละเอียดเพิ่มเติม.*$/u', '', $text) ?? $text;
    $text = preg_replace('/ซื้อประกันออนไลน์.*$/u', '', $text) ?? $text;
    $tags = [
        'เสียชีวิต', 'ลดหย่อนภาษี', 'สุขภาพ', 'โรคร้ายแรง', 'โรคมะเร็ง', 'อุบัติเหตุ',
        'เงินคืน', 'ค่ารักษา', 'ค่าชดเชยรายวัน', 'ค่ารักษาผู้ป่วยใน', 'ค่ารักษาผู้ป่วยนอก',
        'เกษียณ/บำนาญ', 'สะสมทรัพย์', 'การลงทุน/ยูนิตลิงค์',
    ];
    foreach ($tags as $tag) {
        $text = str_replace($tag, '', $text);
    }
    $text = preg_replace('/\s+/u', ' ', trim($text)) ?? trim($text);
    if (mb_strlen($text) > 160) {
        $text = mb_substr($text, 0, 157) . '...';
    }
    return $text;
}

$labels = [
    'health' => 'ประกันสุขภาพ',
    'critical' => 'ประกันโรคร้ายแรง',
    'life-accident' => 'ประกันชีวิตและอุบัติเหตุ',
    'savings' => 'ประกันสะสมทรัพย์',
    'investment' => 'ประกันชีวิตควบการลงทุน',
];

$order = ['health', 'critical', 'life-accident', 'savings', 'investment'];
$groups = array_fill_keys($order, ['label' => '', 'products' => []]);
foreach ($order as $cat) {
    $groups[$cat]['label'] = $labels[$cat];
}

$agentProducts = array_values(array_filter($data['products'], fn ($p) => ($p['channel'] ?? '') === 'agent'));

foreach ($agentProducts as $product) {
    $slug = $product['slug'];
    $href = $product['href'];
    $category = category_from_href($href);
    $alias = $slugAliases[$slug] ?? $slug;
    $prev = $existingBySlug[$slug] ?? ($existingBySlug[$alias] ?? null);

    $title = $titles[$slug] ?? ($prev['title'] ?? ucwords(str_replace('-', ' ', $slug)));
    $desc = $prev['desc'] ?? clean_desc($product['text'] ?? '');

    $entry = [
        'slug' => $slug,
        'title' => $title,
        'desc' => $desc,
        'fwd_url' => $href,
    ];
    if (!empty($prev['discount'])) {
        $entry['discount'] = $prev['discount'];
    }

    $groups[$category]['products'][] = $entry;
}

$out = "<?php\n";
$out .= "/**\n";
$out .= " * แคตตาล็อกผลิตภัณฑ์ช่องทางตัวแทน — 37 แผน (ตาม fwd.co.th/th/products/)\n";
$out .= " * fwd_url = อ้างอิงเนื้อหา (ไม่แสดงลิงก์ออกใน UI)\n";
$out .= " * Auto-generated by scripts/build-agent-catalog.php\n";
$out .= " */\nreturn " . var_export($groups, true) . ";\n";

file_put_contents($root . '/includes/plans-catalog-definitions.php', $out);

$total = array_sum(array_map(fn ($g) => count($g['products']), $groups));
echo "Wrote plans-catalog-definitions.php with {$total} agent products\n";
