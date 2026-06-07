<?php
/**
 * Package tiers & premium tables (demo — aligned with FWD public product pages).
 * Exact premiums vary by age/health; see pricing_source on each plan.
 */

function plan_pricing_templates(): array
{
    return [
        'life_3tier' => [
            'sums' => [500000, 1000000, 1500000],
            'packages' => [
                ['key' => 'economy', 'name' => 'Economy', 'badge' => null, 'sum' => 500000, 'premium_yearly' => 1290, 'premium_monthly' => 119],
                ['key' => 'standard', 'name' => 'Standard', 'badge' => 'ยอดนิยม', 'sum' => 1000000, 'premium_yearly' => 2190, 'premium_monthly' => 199, 'popular' => true],
                ['key' => 'premium', 'name' => 'Premium', 'badge' => null, 'sum' => 1500000, 'premium_yearly' => 3190, 'premium_monthly' => 289],
            ],
            'compare' => [
                ['feature' => 'ทุนประกันชีวิต (บาท)', 'values' => ['500,000', '1,000,000', '1,500,000']],
                ['feature' => 'ระยะคุ้มครอง', 'values' => ['10 ปี', '10 ปี', '10 ปี']],
                ['feature' => 'เสียชีวิตทุกกรณี', 'values' => [true, true, true]],
                ['feature' => 'ลดหย่อนภาษี', 'values' => [true, true, true]],
                ['feature' => 'ซื้อออนไลน์', 'values' => [true, true, true]],
            ],
            'table_male' => [
                [980, 1680, 2450],
                [1050, 1790, 2620],
                [1180, 1990, 2890],
                [1420, 2380, 3450],
                [1890, 3120, 4520],
                [2580, 4280, 6180],
            ],
            'table_female' => [
                [820, 1420, 2080],
                [880, 1520, 2210],
                [990, 1710, 2480],
                [1180, 2010, 2920],
                [1520, 2580, 3750],
                [2050, 3420, 4950],
            ],
        ],
        'life_high' => [
            'sums' => [1000000, 1500000, 2000000],
            'packages' => [
                ['key' => 'basic', 'name' => 'Basic', 'badge' => null, 'sum' => 1000000, 'premium_yearly' => 2890, 'premium_monthly' => 265],
                ['key' => 'plus', 'name' => 'Plus', 'badge' => 'ยอดนิยม', 'sum' => 1500000, 'premium_yearly' => 4190, 'premium_monthly' => 385, 'popular' => true],
                ['key' => 'max', 'name' => 'Max', 'badge' => null, 'sum' => 2000000, 'premium_yearly' => 5490, 'premium_monthly' => 505],
            ],
            'compare' => [
                ['feature' => 'ทุนความคุ้มครองชีวิต', 'values' => ['1,000,000', '1,500,000', '2,000,000']],
                ['feature' => 'ระยะเวลา Term', 'values' => ['5 / 10 / 15 ปี', '5 / 10 / 15 ปี', '5 / 10 / 15 ปี']],
                ['feature' => 'อุบัติเหตุเสริม', 'values' => [true, true, true]],
                ['feature' => 'ค่าชดเชยรายวัน', 'values' => [true, true, true]],
                ['feature' => 'ลดหย่อนภาษี (Term 10/15)', 'values' => [true, true, true]],
            ],
            'table_male' => [
                [2450, 3580, 4680],
                [2680, 3920, 5120],
                [2950, 4320, 5650],
                [3380, 4950, 6480],
                [4120, 6050, 7920],
                [5280, 7750, 10150],
            ],
            'table_female' => [
                [2080, 3050, 3990],
                [2280, 3340, 4380],
                [2510, 3680, 4820],
                [2880, 4220, 5520],
                [3520, 5160, 6750],
                [4510, 6620, 8660],
            ],
        ],
        'health_3tier' => [
            'sums' => [500000, 1000000, 1500000],
            'packages' => [
                ['key' => 'plan500', 'name' => 'แผน 500K', 'badge' => null, 'sum' => 500000, 'premium_yearly' => 5400, 'premium_monthly' => 450],
                ['key' => 'plan1m', 'name' => 'แผน 1M', 'badge' => 'ยอดนิยม', 'sum' => 1000000, 'premium_yearly' => 9000, 'premium_monthly' => 750, 'popular' => true],
                ['key' => 'plan15m', 'name' => 'แผน 1.5M', 'badge' => null, 'sum' => 1500000, 'premium_yearly' => 13200, 'premium_monthly' => 1100],
            ],
            'compare' => [
                ['feature' => 'วงเงิน IPD/ปี', 'values' => ['500,000', '1,000,000', '1,500,000']],
                ['feature' => 'เหมาจ่ายต่อครั้ง', 'values' => [true, true, true]],
                ['feature' => 'ไม่สำรองจ่าย (เครือข่าย)', 'values' => [true, true, true]],
                ['feature' => 'เพิ่ม OPD ได้', 'values' => [true, true, true]],
                ['feature' => 'ซื้อออนไลน์', 'values' => [true, true, true]],
            ],
            'table_male' => [
                [4200, 7200, 10500],
                [4500, 7800, 11400],
                [4800, 8400, 12300],
                [5100, 9000, 13200],
                [5400, 9600, 14100],
                [6000, 10800, 15900],
            ],
            'table_female' => [
                [4500, 7800, 11400],
                [4800, 8400, 12300],
                [5100, 9000, 13200],
                [5400, 9600, 14100],
                [5700, 10200, 15000],
                [6300, 11400, 16800],
            ],
        ],
        'critical_3tier' => [
            'sums' => [500000, 1000000, 2000000],
            'packages' => [
                ['key' => 'economy', 'name' => 'Economy', 'badge' => null, 'sum' => 500000, 'premium_yearly' => 890, 'premium_monthly' => 79],
                ['key' => 'standard', 'name' => 'Standard', 'badge' => 'ยอดนิยม', 'sum' => 1000000, 'premium_yearly' => 1590, 'premium_monthly' => 145, 'popular' => true],
                ['key' => 'premium', 'name' => 'Premium', 'badge' => null, 'sum' => 2000000, 'premium_yearly' => 2890, 'premium_monthly' => 265],
            ],
            'compare' => [
                ['feature' => 'ทุนประกัน (บาท)', 'values' => ['500,000', '1,000,000', '2,000,000']],
                ['feature' => 'จ่ายเมื่อวินิจฉัย', 'values' => ['100%', '100%', '100%']],
                ['feature' => 'Waiting period', 'values' => ['90 วัน', '90 วัน', '90 วัน']],
                ['feature' => 'ซื้อออนไลน์', 'values' => [true, true, true]],
                ['feature' => 'ลดหย่อนภาษี', 'values' => [false, false, false]],
            ],
            'table_male' => [
                [720, 1280, 2280],
                [780, 1390, 2480],
                [850, 1520, 2720],
                [920, 1650, 2950],
                [1050, 1880, 3380],
                [1280, 2290, 4120],
            ],
            'table_female' => [
                [680, 1210, 2150],
                [740, 1320, 2350],
                [810, 1450, 2590],
                [880, 1570, 2810],
                [990, 1780, 3190],
                [1210, 2160, 3880],
            ],
        ],
        'accident_3tier' => [
            'sums' => [100000, 500000, 1000000],
            'packages' => [
                ['key' => 'basic', 'name' => 'Basic', 'badge' => null, 'sum' => 100000, 'premium_yearly' => 590, 'premium_monthly' => 55],
                ['key' => 'standard', 'name' => 'Standard', 'badge' => 'ยอดนิยม', 'sum' => 500000, 'premium_yearly' => 1290, 'premium_monthly' => 119, 'popular' => true],
                ['key' => 'plus', 'name' => 'Plus', 'badge' => null, 'sum' => 1000000, 'premium_yearly' => 2190, 'premium_monthly' => 199],
            ],
            'compare' => [
                ['feature' => 'ทุนอุบัติเหตุ', 'values' => ['100,000', '500,000', '1,000,000']],
                ['feature' => 'ค่ารักษาจากอุบัติเหตุ', 'values' => [true, true, true]],
                ['feature' => 'ทุพพลภาพถาวร', 'values' => [true, true, true]],
                ['feature' => 'ซื้อออนไลน์', 'values' => [true, true, true]],
            ],
            'table_male' => [
                [490, 990, 1890],
                [520, 1050, 1990],
                [550, 1120, 2120],
                [590, 1190, 2250],
                [650, 1290, 2450],
                [720, 1450, 2780],
            ],
            'table_female' => [
                [450, 920, 1750],
                [480, 980, 1860],
                [510, 1040, 1980],
                [550, 1110, 2110],
                [610, 1210, 2310],
                [680, 1360, 2610],
            ],
        ],
        'savings_2tier' => [
            'sums' => [100000, 200000],
            'packages' => [
                ['key' => 'save10', 'name' => 'ออม 10 ปี', 'badge' => null, 'sum' => 100000, 'premium_yearly' => 100000, 'premium_monthly' => null, 'sum_label' => 'ทุนประกัน'],
                ['key' => 'edu', 'name' => 'การศึกษา', 'badge' => 'ยอดนิยม', 'sum' => 200000, 'premium_yearly' => 120000, 'premium_monthly' => null, 'popular' => true, 'sum_label' => 'ทุนประกัน'],
            ],
            'compare' => [
                ['feature' => 'ระยะชำระเบี้ย', 'values' => ['10 ปี', 'ตามแผน']],
                ['feature' => 'เงินคืน/ผลตอบแทน', 'values' => [true, true]],
                ['feature' => 'ความคุ้มครองชีวิต', 'values' => [true, true]],
                ['feature' => 'ลดหย่อนภาษี', 'values' => [true, true]],
            ],
            'table_male' => [
                [95000, 115000],
                [100000, 120000],
                [105000, 125000],
                [112000, 135000],
                [125000, 150000],
                [138000, 168000],
            ],
            'table_female' => [
                [95000, 115000],
                [100000, 120000],
                [105000, 125000],
                [112000, 135000],
                [125000, 150000],
                [138000, 168000],
            ],
        ],
    ];
}

function plan_pricing_slug_map(): array
{
    return [
        'easy-all-in-one' => 'life_high',
        'fwd-term-life' => 'life_3tier',
        'fwd-whole-life' => 'life_3tier',
        'easy-e-accident' => 'accident_3tier',
        'fwd-family-accident' => 'accident_3tier',
        'easy-e-health' => 'health_3tier',
        'fwd-health-plus' => 'health_3tier',
        'fwd-health-basic' => 'health_3tier',
        'easy-e-stroke' => 'critical_3tier',
        'easy-e-cancer' => 'critical_3tier',
        'fwd-critical-360' => 'critical_3tier',
        'fwd-savings-plan-10' => 'savings_2tier',
        'fwd-education-plan' => 'savings_2tier',
        'fwd-retirement-plus' => 'savings_2tier',
        'fwd-pension-easy' => 'life_3tier',
    ];
}

function plan_fwd_source_urls(): array
{
    return [
        'easy-all-in-one' => 'https://www.fwd.co.th/th/life-and-accident-insurance/easy-all-in-one/',
        'easy-e-health' => 'https://www.fwd.co.th/th/health-insurance/easy-e-health/',
        'easy-e-stroke' => 'https://www.fwd.co.th/th/critical-illness-insurance/easy-e-stroke/',
        'fwd-term-life' => 'https://www.fwd.co.th/th/life-and-accident-insurance/easy-e-life/',
        'easy-e-accident' => 'https://www.fwd.co.th/th/life-and-accident-insurance/',
    ];
}

function plan_with_pricing(array $plan, string $slug): array
{
    if (!empty($plan['packages'])) {
        return $plan;
    }

    $templates = plan_pricing_templates();
    $map = plan_pricing_slug_map();
    $key = $map[$slug] ?? 'life_3tier';
    $tpl = $templates[$key] ?? $templates['life_3tier'];

    $ages = ['18-25', '26-30', '31-35', '36-40', '41-45', '46-50'];

    $plan['packages'] = $tpl['packages'];
    $plan['premium_table'] = [
        'sums' => $tpl['sums'],
        'ages' => $ages,
        'male' => $tpl['table_male'],
        'female' => $tpl['table_female'],
        'unit' => $plan['category'] === 'savings-pension' ? 'เบี้ย/ปี (บาท)' : 'เบี้ย/ปี (บาท)',
    ];
    $plan['compare_rows'] = $tpl['compare'];
    $plan['pricing_source'] = plan_fwd_source_urls()[$slug] ?? 'https://www.fwd.co.th/';
    $plan['pricing_disclaimer'] = 'ราคาและเบี้ยในหน้านี้เป็นตัวอย่างสำหรับเว็บสาธิต อ้างอิงโครงแพ็กเกจจากเว็บ FWD — ราคาจริงคำนวณตามอายุ เพศ และคำถามสุขภาพบนหน้าสมัครของ FWD';

    $defaultIdx = 0;
    foreach ($plan['packages'] as $i => $pkg) {
        if (!empty($pkg['popular'])) {
            $defaultIdx = $i;
            break;
        }
    }
    $plan['calculator_defaults'] = [
        'gender' => 'male',
        'age' => 30,
        'package_index' => $defaultIdx,
        'payment' => 'yearly',
    ];

    return $plan;
}
