<?php
/**
 * โบรชัวร์ PDF ต่อ slug — ไฟล์อยู่ใน assets/โบรชัวร์แบบประกัน/
 */
function plan_brochure_catalog(): array
{
    static $catalog;
    if ($catalog !== null) {
        return $catalog;
    }

    $brochureDir = dirname(__DIR__) . '/assets/โบรชัวร์แบบประกัน';
    $byName = [];
    foreach (glob($brochureDir . '/*.pdf') ?: [] as $fullPath) {
        $name = basename($fullPath);
        $byName[$name] = 'assets/โบรชัวร์แบบประกัน/' . $name;
    }

    $asciiMap = [
        'precious-care' => 'Brochure_FWD_Precious_Care.pdf',
        'ci-50' => 'TH_Brochure_CI50.pdf',
        'ci-fixed-pay' => 'TH_Brochure_CI_Fixed_Pay.pdf',
        'ci-reclaim-recare' => 'CI_Re-Claim_Re-Care_Omne.pdf',
        'ci-reclaim-recare-plus' => 'CI_Re-Claim_Re-Care_Omne.pdf',
        'value-protector' => 'TH_Brochure_V_Protector.pdf',
        'whole-life' => 'TH_Brochure_Whole_Life_99-99.pdf',
        'whole-life-extra' => 'TH_Brochure_Whole_Life_Extra.pdf',
        'fwd-for-saving-25-15' => 'TH_Brochure_FWD_For_Savings_25-15.pdf',
        'fwd-power-saving' => 'TH_Brochure_FWD_Power_savings_12-6_(002).pdf',
        'lifetime-return' => 'TH_Brochure_FWD_Lifetime_Return_99-15.pdf',
    ];

    $catalog = [];

    // โบรชัวร์จาก fwd.co.th ตั้งชื่อ {slug}.pdf — ใช้ก่อนแผนที่ชื่อไฟล์เดิม
    foreach (glob($brochureDir . '/*.pdf') ?: [] as $fullPath) {
        $filename = basename($fullPath);
        $slug = basename($fullPath, '.pdf');
        if ($slug !== '' && !str_contains($slug, ' ')) {
            $catalog[$slug] = 'assets/โบรชัวร์แบบประกัน/' . $filename;
        }
    }

    foreach ($asciiMap as $slug => $filename) {
        if (!isset($catalog[$slug]) && isset($byName[$filename])) {
            $catalog[$slug] = $byName[$filename];
        }
    }

    foreach ($byName as $filename => $relativePath) {
        if (str_contains($filename, 'มั่นใจ') && !isset($catalog['be-sure'])) {
            $catalog['be-sure'] = $relativePath;
        } elseif (str_contains($filename, 'คนจริง') && !isset($catalog['prakan-kon-jing'])) {
            $catalog['prakan-kon-jing'] = $relativePath;
        } elseif (str_contains($filename, 'PA_MAX') && !isset($catalog['prakan-kon-kla-max'])) {
            $catalog['prakan-kon-kla-max'] = $relativePath;
        }
    }

    return $catalog;
}

function plan_brochure_relative_path(string $slug): ?string
{
    return plan_brochure_catalog()[$slug] ?? null;
}

function plan_brochure_url(string $slug): ?string
{
    $path = plan_brochure_relative_path($slug);
    return $path !== null ? media_url($path) : null;
}

function icon_download(int $size = 20): string
{
    $s = (int) $size;
    return '<svg class="icon icon--download" width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>'
        . '<polyline points="7 10 12 15 17 10"/>'
        . '<line x1="12" y1="15" x2="12" y2="3"/>'
        . '</svg>';
}
