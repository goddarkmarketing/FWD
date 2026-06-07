# FWD ประเทศไทย — เว็บไซต์ตัวอย่าง

เว็บไซต์หลายหน้าจำลองโทนและโครงสร้าง [FWD ประเทศไทย](https://www.fwd.co.th/th/) สำหรับรันบน XAMPP

## เปิดใช้งาน

1. เปิด Apache ใน XAMPP
2. เปิดเบราว์เซอร์: `http://localhost/fwd/`

## ฟอนต์

| การใช้งาน | ฟอนต์ | แหล่งที่มา |
|-----------|--------|-----------|
| หัวข้อหลัก (h1–h3, ชื่อผลิตภัณฑ์) | **DB Adman X** | [LK3](https://github.com/daclubb/LK3) → `assets/fonts/DB-Adman-X.ttf` |
| เนื้อหา / รายละเอียด | **Chakra Petch** | [Google Fonts](https://fonts.google.com/specimen/Chakra+Petch) |

## โทนสี (ตามแบรนด์ FWD)

| สี | Hex |
|----|-----|
| ส้ม FWD | `#E87722` |
| เขียวเข้ม | `#183028` |
| ขาว | `#FFFFFF` |

## หน้าเว็บ

| ไฟล์ | เนื้อหา |
|------|---------|
| `index.php` | หน้าแรก |
| `health-insurance.php` | ประกันสุขภาพ |
| `critical-illness.php` | ประกันโรคร้ายแรง |
| `life-insurance.php` | ประกันชีวิตและอุบัติเหตุ |
| `savings-insurance.php` | ประกันออมทรัพย์ |
| `accident-insurance.php` | ประกันอุบัติเหตุ |
| `investment-linked.php` | ประกันยูนิตลิงก์ |
| `product-e-health.php` | รายละเอียดผลิตภัณฑ์ (ตัวอย่าง) |
| `product-e-stroke.php` | รายละเอียดผลิตภัณฑ์ (ตัวอย่าง) |
| `product-all-in-one.php` | รายละเอียดผลิตภัณฑ์ (ตัวอย่าง) |
| `promotions.php` | โปรโมชัน |
| `claims.php` | การเคลม |
| `about.php` | เกี่ยวกับ FWD |
| `contact.php` | ติดต่อ / ขอคำปรึกษา |

## โครงสร้าง

```
fwd/
├── includes/     # header, footer, templates
├── assets/css/   # สไตล์หลัก
├── assets/js/    # เมนู, แท็บ, animation
└── *.php         # หน้าเว็บ
```

> **หมายเหตุ:** เว็บนี้เป็นเว็บตัวอย่างเพื่อการสาธิต ไม่ใช่เว็บอย่างเป็นทางการของ FWD

## ส่งให้ลูกค้าดูบน GitHub Pages

เว็บ PHP ถูก pre-render เป็น HTML สำหรับ GitHub Pages อัตโนมัติ

**URL หลัง deploy:** https://goddarkmarketing.github.io/FWD/

### Build เอง (ทดสอบก่อน push)

```bash
php scripts/build-static.php /FWD
```

ไฟล์ static จะอยู่ในโฟลเดอร์ `docs/` (ไม่ commit — GitHub Actions จะ build ให้ตอน push)

### Deploy

1. Push โค้ดขึ้น `main`
2. GitHub → Settings → Pages → Source: **GitHub Actions**
3. Action `Deploy GitHub Pages` จะ build และ deploy ให้อัตโนมัติ

> รันบน XAMPP ตามปกติได้เหมือนเดิม — การ build static ไม่กระทบการพัฒนา PHP
