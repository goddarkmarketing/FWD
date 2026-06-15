<?php

/**
 * Insurance plans catalog — 37 agent-channel products in 5 subcategories (ตาม fwd.co.th/th/products/).
 * Product images: assets/images/products2/ (auto-assigned in catalog order).
 */

function plan_mock_image_path(string $category, int $index): string
{
    if (defined('FWD_STATIC_BUILD') && FWD_STATIC_BUILD) {
        return 'assets/mock/' . rawurlencode($category) . '-' . $index . '.svg';
    }

    return 'product-mock.php?cat=' . rawurlencode($category) . '&n=' . $index;
}

function plan_products2_images(): array
{
    static $images;
    if ($images !== null) {
        return $images;
    }

    $images = [];
    $dir = dirname(__DIR__) . '/assets/images/products2';
    if (!is_dir($dir)) {
        return $images;
    }

    $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $files = [];
    foreach ($extensions as $ext) {
        $lower = glob($dir . '/*.' . $ext) ?: [];
        $upper = glob($dir . '/*.' . strtoupper($ext)) ?: [];
        $files = array_merge($files, $lower, $upper);
    }

    $files = array_unique($files);
    $files = array_values(array_filter($files, function ($path) {
        $name = basename($path);
        return stripos($name, 'Header_TH') !== 0;
    }));
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);

    foreach ($files as $file) {
        $images[] = 'assets/images/products2/' . basename($file);
    }

    return $images;
}

function plan_default_tags(string $category, string $slug): array
{
    $sets = [
        'life-accident' => ['เสียชีวิต', 'ลดหย่อนภาษี', 'อุบัติเหตุ'],
        'health' => ['ค่ารักษา', 'ไม่สำรองจ่าย', 'คุ้มครอง IPD'],
        'critical' => ['โรคร้ายแรง', 'เงินก้อน', 'วินิจฉัยแล้วจ่าย'],
        'investment' => ['การลงทุน', 'เงินคืน', 'เสียชีวิต'],
        'savings' => ['เงินคืน', 'ลดหย่อนภาษี', 'ออมทรัพย์'],
    ];

    $tags = $sets[$category] ?? ['ช่องทางตัวแทน', 'คุ้มครอง', 'FWD'];

    return $tags;
}

function plans_build_catalog(): array
{
    $definitions = require __DIR__ . '/plans-catalog-definitions.php';
    $catalog = [];
    $imagePool = plan_products2_images();
    $imageIndex = 0;

    foreach ($definitions as $category => $group) {
        $label = $group['label'];
        foreach ($group['products'] as $item) {
            $slug = $item['slug'];

            if ($imagePool !== []) {
                $image = $imagePool[$imageIndex % count($imagePool)];
                $imageIndex++;
            } elseif (!empty($item['image'])) {
                $image = $item['image'];
            } else {
                $image = plan_mock_image_path($category, $imageIndex + 1);
                $imageIndex++;
            }

            $url = $item['url'] ?? ('plan.php?slug=' . rawurlencode($slug));

            $tags = $item['tags'] ?? plan_default_tags($category, $slug);

            $catalog[] = [
                'slug' => $slug,
                'category' => $category,
                'category_label' => $label,
                'title' => $item['title'],
                'desc' => $item['desc'],
                'discount' => $item['discount'] ?? null,
                'tags' => $tags,
                'url' => $url,
                'image' => $image,
            ];
        }
    }

    require_once __DIR__ . '/cms-loader.php';
    require_once __DIR__ . '/cms-plan-meta.php';
    $overrides = cms_load('catalog', []);
    if (is_array($overrides) && $overrides !== []) {
        foreach ($catalog as $i => $item) {
            $slug = $item['slug'] ?? '';
            if ($slug === '' || empty($overrides[$slug])) {
                continue;
            }
            $o = $overrides[$slug];
            foreach (['title', 'desc', 'discount', 'image'] as $field) {
                if (isset($o[$field]) && $o[$field] !== '') {
                    $catalog[$i][$field] = $o[$field];
                }
            }
            if (!empty($o['tags']) && is_array($o['tags'])) {
                $catalog[$i]['tags'] = $o['tags'];
            }
        }
    }

    $meta = cms_catalog_meta();
    $hidden = $meta['hidden'] ?? [];
    if ($hidden !== []) {
        $catalog = array_values(array_filter($catalog, static function (array $item) use ($hidden): bool {
            return !in_array($item['slug'] ?? '', $hidden, true);
        }));
    }

    $categories = plan_categories();
    foreach ($meta['custom'] ?? [] as $slug => $entry) {
        if (!is_array($entry) || in_array($slug, $hidden, true)) {
            continue;
        }
        $categoryId = (string) ($entry['category'] ?? 'health');
        $item = [
            'slug' => $slug,
            'category' => $categoryId,
            'category_label' => $categories[$categoryId]['title'] ?? ($entry['category_label'] ?? $categoryId),
            'title' => (string) ($entry['title'] ?? $slug),
            'desc' => (string) ($entry['desc'] ?? ''),
            'discount' => $entry['discount'] ?? null,
            'tags' => $entry['tags'] ?? plan_default_tags($categoryId, $slug),
            'url' => $entry['url'] ?? ('plan.php?slug=' . rawurlencode($slug)),
            'image' => (string) ($entry['image'] ?? plan_mock_image_path($categoryId, count($catalog) + 1)),
        ];
        if (!empty($overrides[$slug]) && is_array($overrides[$slug])) {
            $o = $overrides[$slug];
            foreach (['title', 'desc', 'discount', 'image'] as $field) {
                if (isset($o[$field]) && $o[$field] !== '') {
                    $item[$field] = $o[$field];
                }
            }
            if (!empty($o['tags']) && is_array($o['tags'])) {
                $item['tags'] = $o['tags'];
            }
        }
        $catalog[] = $item;
    }

    return $catalog;
}

return plans_build_catalog();
