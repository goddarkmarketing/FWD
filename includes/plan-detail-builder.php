<?php
/**
 * สร้างรายละเอียดแผนจากแคตตาล็อก (โครงสร้างเดียวกับหน้า FWD ไม่มีเครื่องคำนวณ)
 */
function plan_details_built_from_catalog(): array
{
    static $built;
    if ($built !== null) {
        return $built;
    }

    if (!function_exists('plan_default_application_contact')) {
        require_once __DIR__ . '/plan-helpers.php';
    }

    $built = [];
    $app = plan_default_application_contact();
    $definitions = require __DIR__ . '/plans-catalog-definitions.php';

    foreach ($definitions as $categoryId => $category) {
        $categoryLabel = $category['label'];
        foreach ($category['products'] as $product) {
            $slug = $product['slug'];
            $desc = $product['desc'] ?? '';
            $highlights = $product['highlights'] ?? [
                ['title' => 'ภาพรวมแผน', 'desc' => $desc],
                ['title' => $categoryLabel, 'desc' => 'ผลิตภัณฑ์จาก FWD ประเทศไทย'],
                ['title' => 'ปรึกษาฟรี', 'desc' => 'สอบถามรายละเอียดและเงื่อนไขก่อนตัดสินใจ'],
            ];

            $built[$slug] = [
                'slug' => $slug,
                'title' => $product['title'],
                'tagline' => $product['tagline'] ?? $desc,
                'meta' => $product['meta'] ?? ($product['title'] . ' — ' . $categoryLabel),
                'category' => $categoryId,
                'category_label' => $categoryLabel,
                'discount' => $product['discount'] ?? null,
                'no_calculator' => true,
                'hero_bullets' => $product['hero_bullets'] ?? array_values(array_filter([$desc])),
                'highlights' => $highlights,
                'coverage_blocks' => $product['coverage_blocks'] ?? [],
                'conditions' => $product['conditions'] ?? [
                    'อายุรับประกันและเงื่อนไขตามเอกสารขายของผลิตภัณฑ์',
                    'ข้อยกเว้นตามที่ระบุในกรมธรรม์',
                    'ผู้ซื้อควรศึกษารายละเอียดก่อนตัดสินใจทุกครั้ง',
                ],
                'faq' => $product['faq'] ?? [],
                'application' => $product['application'] ?? $app,
            ];
        }
    }

    return $built;
}
