# FWD product pages (saved HTML)

ไฟล์ `.htm` ในโฟลเดอร์นี้ดาวน์โหลดจาก URL ใน `data/fwd-urls/all-products.json`

## ดาวน์โหลดใหม่

```bash
node scripts/download-fwd-pages.mjs
```

## นำเข้ารายละเอียดเข้าเว็บ

```bash
php scripts/import-fwd-pages-to-details.php
```

ผลลัพธ์: `includes/plans-detail-imported.php` (โหลดอัตโนมัติใน `plan_details_all()`)

## หมายเหตุ

- แผนที่โหลดข้อมูลผลิตภัณฑ์ครบใน `__NEXT_DATA__` จะได้จุดเด่น / ความคุ้มครอง / FAQ ครบ
- แผนที่โหลดแบบ CSR จะใช้คำอธิบายจาก meta tag ของหน้า FWD
