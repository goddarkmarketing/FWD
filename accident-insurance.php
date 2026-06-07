<?php
$page_title = 'ประกันอุบัติเหตุ';
$page_description = 'ประกันอุบัติเหตุ FWD คุ้มครองทุกวัน ทั้งค่ารักษาและค่าชดเชย';
require_once __DIR__ . '/includes/header.php';

$category_title = 'ประกันอุบัติเหตุ';
$category_lead = 'คุ้มครองอุบัติเหตุทุกวัน 24 ชั่วโมง ทั้งในและนอกที่ทำงาน';
$products = [
    [
        'name' => 'Easy E-Accident',
        'tag' => 'ออนไลน์',
        'desc' => 'ซื้อออนไลน์ได้ คุ้มครองอุบัติเหตุและค่ารักษา',
        'image' => 'assets/images/products/accident.png',
        'url' => 'contact.php',
    ],
    [
        'name' => 'FWD Accident Plus',
        'tag' => 'ครอบคลุม',
        'desc' => 'ค่าชดเชยสูง รวมอุบัติเหตุจากการเล่นกีฬา',
        'image' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=600&q=80',
        'url' => 'contact.php',
    ],
    [
        'name' => 'FWD Family Accident',
        'tag' => 'ครอบครัว',
        'desc' => 'คุ้มครองทั้งครอบครัวในกรมธรรม์เดียว',
        'image' => 'https://images.unsplash.com/photo-1511895426328-dc8714191300?w=600&q=80',
        'url' => 'contact.php',
    ],
];
require __DIR__ . '/includes/product-page.php';
require_once __DIR__ . '/includes/footer.php';
