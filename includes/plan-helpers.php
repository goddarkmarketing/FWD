<?php

function plan_catalog(): array
{
    static $catalog;
    if ($catalog === null) {
        $catalog = require __DIR__ . '/plans-data.php';
    }
    return $catalog;
}

function plan_categories(): array
{
    static $categories;
    if ($categories === null) {
        $categories = require __DIR__ . '/plan-categories.php';
    }
    return $categories;
}

function plan_category_menu_order(): array
{
    return ['all', 'life-accident', 'health', 'critical', 'investment', 'savings'];
}

function plan_category_page_url(string $categoryId): string
{
    $categories = plan_categories();
    $config = $categories[$categoryId] ?? $categories['all'];
    return page_url($config['page']);
}

function plans_by_category(string $categoryId): array
{
    $all = plan_catalog();
    if ($categoryId === 'all') {
        return $all;
    }
    return array_values(array_filter($all, function ($plan) use ($categoryId) {
        return ($plan['category'] ?? '') === $categoryId;
    }));
}

function plan_details_all(): array
{
    static $details;
    if ($details === null) {
        require_once __DIR__ . '/plan-detail-builder.php';
        $details = plan_details_built_from_catalog();
        $legacy = require __DIR__ . '/plans-detail-data.php';
        foreach ($legacy as $slug => $row) {
            $details[$slug] = array_merge($details[$slug] ?? [], $row);
        }
        $life = require __DIR__ . '/plans-detail-life-accident.php';
        foreach ($life as $slug => $row) {
            if (isset($details[$slug])) {
                $details[$slug] = array_merge($details[$slug], $row);
            }
        }
        $importedFile = __DIR__ . '/plans-detail-imported.php';
        if (is_readable($importedFile)) {
            $imported = require $importedFile;
            foreach ($imported as $slug => $row) {
                $details[$slug] = array_merge($details[$slug] ?? [], $row);
            }
        }
    }
    return $details;
}

function plan_catalog_by_slug(string $slug): ?array
{
    foreach (plan_catalog() as $item) {
        if (($item['slug'] ?? '') === $slug) {
            return $item;
        }
    }
    return null;
}

function plan_detail_from_catalog(string $slug): ?array
{
    $item = plan_catalog_by_slug($slug);
    if ($item === null) {
        return null;
    }

    $highlights = [
        ['title' => $item['category_label'], 'desc' => $item['desc']],
        ['title' => 'ปรึกษาฟรี', 'desc' => 'ทีมที่ปรึกษาพร้อมช่วยเลือกแผนที่เหมาะกับคุณ'],
    ];
    if (!empty($item['tags'])) {
        $highlights[] = [
            'title' => 'จุดเด่น',
            'desc' => implode(' · ', array_slice($item['tags'], 0, 4)),
        ];
    }

    return [
        'slug' => $slug,
        'title' => $item['title'],
        'tagline' => $item['desc'],
        'meta' => $item['title'] . ' — ' . ($item['category_label'] ?? ''),
        'category' => $item['category'],
        'category_label' => $item['category_label'],
        'image' => $item['image'],
        'no_calculator' => true,
        'hero_bullets' => [$item['desc']],
        'highlights' => $highlights,
        'conditions' => [
            'รายละเอียดความคุ้มครองและข้อยกเว้นตามเอกสารกรมธรรม์',
            'ติดต่อทีมงานเพื่อรับคำปรึกษาและข้อมูลเพิ่มเติมก่อนตัดสินใจ',
        ],
        'faq' => [],
        'application' => plan_default_application_contact(),
        'is_catalog_stub' => true,
    ];
}

function plan_detail(string $slug): ?array
{
    $details = plan_details_all();
    $plan = $details[$slug] ?? null;
    if ($plan === null) {
        return plan_detail_from_catalog($slug);
    }

    $catalog = plan_catalog_by_slug($slug);
    if ($catalog !== null) {
        // ใช้รูปเดียวกับการ์ดสินค้าในแคตตาล็อก (products2)
        $plan['image'] = $catalog['image'];
        if (empty($plan['discount']) && !empty($catalog['discount'])) {
            $plan['discount'] = $catalog['discount'];
        }
    }
    $plan['slug'] = $slug;

    if ($catalog !== null) {
        $plan['no_calculator'] = true;
    }

    if (!empty($plan['no_calculator'])) {
        return $plan;
    }

    require_once __DIR__ . '/plan-pricing.php';
    return plan_with_pricing($plan, $slug);
}

function plan_url(string $slug): string
{
    return page_url('plan.php?slug=' . rawurlencode($slug));
}

function plan_contact_url(string $slug, ?string $title = null): string
{
    $query = ['plan' => $slug];
    if ($title !== null && $title !== '') {
        $query['name'] = $title;
    }
    return page_url('contact.php?' . http_build_query($query));
}

function plan_category_url(string $category): string
{
    if ($category === 'all' || $category === '') {
        return plan_category_page_url('all');
    }
    if (isset(plan_categories()[$category])) {
        return plan_category_page_url($category);
    }
    $legacy = [
        'savings-pension' => 'savings',
    ];
    $id = $legacy[$category] ?? $category;
    return plan_category_page_url($id);
}

function plan_default_application(bool $online = true): array
{
    if (!$online) {
        return [
            ['icon' => 'id', 'title' => 'บัตรประชาชน', 'desc' => 'ใช้ประกอบการขอเอาประกันภัย'],
            ['icon' => 'doc', 'title' => 'ข้อมูลสุขภาพ', 'desc' => 'ตามแบบฟอร์มคำถามสุขภาพของผลิตภัณฑ์'],
            ['icon' => 'phone', 'title' => 'ติดต่อที่ปรึกษา', 'desc' => 'โทร 1351 หรือกรอกแบบฟอร์มขอคำปรึกษา'],
        ];
    }
    return [
        ['icon' => 'id', 'title' => 'เตรียมบัตรประชาชนตัวจริง', 'desc' => 'ใช้ยืนยันตัวตนและกรอกข้อมูล'],
        ['icon' => 'camera', 'title' => 'ถ่ายรูปเซลฟี่ถือบัตรประชาชน', 'desc' => 'เพื่อยืนยันตัวตนเมื่อซื้อออนไลน์'],
        ['icon' => 'card', 'title' => 'บัตรเครดิตหรือแอปธนาคาร', 'desc' => 'ชำระเบี้ยประกันทันที (Visa / Mastercard / Thai QR)'],
    ];
}

function plan_default_application_contact(): array
{
    return [
        ['icon' => 'id', 'title' => 'ข้อมูลติดต่อ', 'desc' => 'ชื่อ เบอร์โทร และอีเมลในแบบฟอร์มด้านล่าง'],
        ['icon' => 'doc', 'title' => 'แผนที่สนใจ', 'desc' => 'ระบุชื่อแผนหรือความต้องการคุ้มครอง'],
        ['icon' => 'phone', 'title' => 'รอทีมงานติดต่อกลับ', 'desc' => 'ผู้เชี่ยวชาญจะโทรหรือนัดพบตามช่องทางที่เลือก'],
    ];
}
