<?php
return [
    ['section' => 'ภาพรวม', 'items' => [
        ['label' => 'แดชบอร์ด', 'file' => 'index.php', 'icon' => 'dashboard'],
    ]],
    ['section' => 'ตั้งค่าเว็บ', 'items' => [
        ['label' => 'ข้อมูลเว็บไซต์', 'file' => 'site.php', 'icon' => 'settings'],
        ['label' => 'หน้าแรก', 'file' => 'homepage.php', 'icon' => 'home'],
        ['label' => 'Footer & Cookie', 'file' => 'footer.php', 'icon' => 'footer'],
        ['label' => 'สื่อ & รูปภาพ', 'file' => 'media.php', 'icon' => 'image'],
    ]],
    ['section' => 'ผลิตภัณฑ์', 'items' => [
        ['label' => 'หมวดหมู่แผน', 'file' => 'categories.php', 'icon' => 'category'],
        ['label' => 'แคตตาล็อก (37 แผน)', 'file' => 'catalog.php', 'icon' => 'catalog'],
        ['label' => 'รายละเอียดแผน', 'file' => 'plans.php', 'icon' => 'plan'],
    ]],
    ['section' => 'เนื้อหา', 'items' => [
        ['label' => 'บทความ', 'file' => 'articles.php', 'icon' => 'article'],
        ['label' => 'โปรโมชัน', 'file' => 'promotions.php', 'icon' => 'promo'],
    ]],
    ['section' => 'หน้าเว็บ', 'items' => [
        ['label' => 'เกี่ยวกับเรา', 'file' => 'page-edit.php?id=about', 'icon' => 'page'],
        ['label' => 'การเคลม', 'file' => 'page-edit.php?id=claims', 'icon' => 'page'],
        ['label' => 'ติดต่อเรา', 'file' => 'page-edit.php?id=contact', 'icon' => 'page'],
        ['label' => 'สมัครตัวแทน', 'file' => 'page-edit.php?id=agent-apply', 'icon' => 'page'],
    ]],
    ['section' => 'ระบบ', 'items' => [
        ['label' => 'สำรอง & กู้คืน', 'file' => 'backup.php', 'icon' => 'backup'],
    ]],
    ['section' => 'บัญชี', 'items' => [
        ['label' => 'เปลี่ยนรหัสผ่าน', 'file' => 'change-password.php', 'icon' => 'lock'],
        ['label' => 'ออกจากระบบ', 'file' => 'logout.php', 'icon' => 'logout'],
    ]],
];
