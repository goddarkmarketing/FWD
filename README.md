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

## ให้ลูกค้าทดสอบหลังบ้าน (ก่อนขึ้นโฮสต์จริง)

GitHub Pages แสดงได้แค่ **หน้าบ้าน (HTML)** — แอดมิน CMS ต้องรันบน **โฮสต์ PHP** แยกต่างหาก  
ใช้ GitHub ส่งหลังบ้านให้ลูกค้าทดสอบได้ผ่าน workflow **Deploy Staging PHP**

### URL ที่ส่งให้ลูกค้า

| ส่วน | URL ตัวอย่าง |
|------|----------------|
| **หน้าบ้าน (อัตโนมัติเมื่อ push `main`)** | https://goddarkmarketing.github.io/FWD/ |
| **หลังบ้าน CMS (staging)** | `https://โดเมน-staging-ของคุณ/fwd/admin/` |
| **หน้าเว็บ PHP บน staging (ดูทันทีหลังแก้ในแอดมิน)** | `https://โดเมน-staging-ของคุณ/fwd/` |

### ตั้งค่าครั้งเดียว

1. สร้าง **subdomain staging** บนโฮสต์ (เช่น `staging.yourdomain.com`) หรือโฟลเดอร์ `fwd-staging` บน cPanel
2. GitHub repo → **Settings → Secrets and variables → Actions** → เพิ่ม:

| Secret | ตัวอย่าง |
|--------|----------|
| `STAGING_FTP_SERVER` | `ftp.yourdomain.com` |
| `STAGING_FTP_USERNAME` | ชื่อ FTP |
| `STAGING_FTP_PASSWORD` | รหัส FTP |
| `STAGING_FTP_REMOTE_DIR` | `/public_html/fwd-staging/` (ลงท้าย `/`) |
| `STAGING_ADMIN_EMAIL` | อีเมล login แอดมิน staging |
| `STAGING_ADMIN_PASSWORD` | รหัสผ่านทดสอบ (ส่งให้ลูกค้า) |

3. (แนะนำ) สร้าง **Environment** ชื่อ `staging` ใน GitHub → Settings → Environments

### วิธี deploy หลังบ้านให้ลูกค้าทดสอบ

**แบบ A — กด deploy เอง**

1. GitHub → **Actions** → **Deploy Staging PHP** → **Run workflow**

**แบบ B — push branch `staging`**

```bash
git checkout -b staging
git push -u origin staging
```

ทุกครั้งที่ push branch `staging` จะ deploy หลังบ้านขึ้นโฮสต์ staging อัตโนมัติ

### ขั้นตอนงานแนะนำ

```
แก้เนื้อหาในแอดมิน (staging URL)
        ↓
ลูกค้าทดสอบหน้าบ้าน (GitHub Pages) + หลังบ้าน (staging)
        ↓
ลูกค้าอนุมัติ → deploy ชุดเดียวกันขึ้นโฮสต์จริง (FTP/cPanel เหมือน staging)
        ↓
commit data/cms/ + push main → หน้า GitHub Pages อัปเดตตาม
```

### หมายเหตุ

- ไฟล์ `data/cms/auth.json` ไม่ commit ใน git — workflow สร้างจาก Secrets ตอน deploy (หรือรัน `php scripts/setup-cms.php` บนเซิร์ฟเวอร์ครั้งแรก)
- แก้ในแอดมิน staging แล้วอยากให้ **GitHub Pages** ตรงกัน → commit `data/cms/*.json` แล้ว push `main`
- โฮสต์ staging ต้องรัน **PHP 8+** และ Apache mod_rewrite (หรือตั้ง Document Root ชี้โฟลเดอร์โปรเจกต์)

## หลังบ้านบนโดเมนจริง (Production PHP)

ใช้ workflow **Deploy PHP (Production)** — ส่งโค้ด PHP ทั้งหมด (หน้าบ้าน + `admin/` + ฟอร์ม) ขึ้นโฮสต์ cPanel/FTP

| ส่วน | URL ตัวอย่าง |
|------|----------------|
| **หน้าบ้าน PHP** | `https://yourdomain.com/` |
| **หลังบ้าน CMS** | `https://yourdomain.com/admin/` |
| **ฟอร์มส่งอีเมล** | `https://yourdomain.com/submit-form.php` |

### GitHub Secrets สำหรับโดเมนจริง

| Secret | ตัวอย่าง |
|--------|----------|
| `FTP_SERVER` | `ftp.yourdomain.com` |
| `FTP_USERNAME` | ชื่อ FTP |
| `FTP_PASSWORD` | รหัส FTP |
| `FTP_REMOTE_DIR` | `/public_html/` (ลงท้าย `/`) |
| `DEPLOY_ADMIN_EMAIL` | `Supakitraksorn@gmail.com` |
| `DEPLOY_ADMIN_PASSWORD` | รหัสผ่านแอดมิน |

เมื่อตั้ง Secrets ครบแล้ว ทุกครั้งที่ push `main` จะ deploy PHP ขึ้นโฮสต์อัตโนมัติ (หรือกด **Actions → Deploy PHP (Production) → Run workflow**)

> ถ้ายังไม่ได้ตั้ง FTP Secrets workflow จะข้ามขั้นตอน deploy — GitHub Pages ยังทำงานตามปกติ
