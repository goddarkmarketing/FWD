# วิธีเอาราคา/เบี้ยจาก FWD โดยไม่ต้องสกรีนช็อตทีละแผน

## ทำไมสกรีนช็อตไม่จำเป็น

เบี้ยบน FWD มาจาก **JavaScript + API (GraphQL `GET_QUOTE`)** ตอนเลือกอายุ/เพศ/แผน  
ถ้า “จับ” การตอบกลับของ API หรือให้เบราว์เซอร์รันเอง จะได้ตัวเลขครบเร็วกว่าถ่ายรูป

---

## วิธีที่ 1: สคริปต์เบราว์เซอร์ (แนะนำ — รันค้างคืน)

```bash
cd c:\xampp\htdocs\fwd
npm init -y
npm install playwright
npx playwright install chromium
node scripts/fwd-scrape-quotes.mjs
```

ผลลัพธ์: `data/fwd-real-pricing.json`  
แก้รายการ URL ใน `scripts/fwd-products.json` ให้ครบแผนที่ต้องการ

---

## วิธีที่ 2: HTTrack + สคริปต์ PHP (หลัง mirror เสร็จ)

```bash
php scripts/parse-fwd-mirror.php "C:\path\to\Clone FWD\www.fwd.co.th"
```

ได้: โครงแผน (ทุน, รหัสแผน) + ตัวอย่างเบี้ยใน FAQ  
**อาจไม่มี** เบี้ยทุกอายุ (เพราะ mirror ไม่รัน JS เต็มรูปแบบ)

---

## วิธีที่ 3: DevTools ครั้งเดียวต่อ 1 ผลิตภัณฑ์ (เร็วกว่าสกรีนช็อตมาก)

1. เปิดหน้า FWD จริง เช่น Easy E-Stroke  
2. F12 → แท็บ **Network** → กรอง `fetch` หรือ `graphql`  
3. เปลี่ยนอายุ/เพศ/แผน บนหน้า — จะเห็น request ซ้ำ  
4. คลิก request → **Response** → copy JSON ที่มี `yearlyPremium`  
5. วางในไฟล์ `data/fwd-real-pricing.json` หรือส่งให้ AI ช่วยแปลง

หรือ: คลิกขวาใน Network → **Save all as HAR** หลังลอง 3 แผน × 2 เพศ × 5 อายุ → ส่งไฟล์ `.har` มาแปลงทีเดียว

---

## วิธีที่ 4: ใช้เฉพาะตัวเลขที่ FWD เปิดเผยในหน้า

- แพ็ก Economy / Standard / Premium + ทุน  
- ข้อความ “ตัวอย่างเบี้ย…” ใน FAQ  
- ลิงก์ “ซื้อออนไลน์” ไปหน้า FWD สำหรับเบี้ยล่าสุด

เหมาะกับเว็บสาธิต + ข้อความว่า “ราคาอ้างอิง fwd.co.th”

---

## หลังได้ไฟล์ JSON

บอก path ของ `data/fwd-real-pricing.json` หรือ `data/fwd-mirror-pricing.json`  
จะนำไปอัปเดต `includes/plan-pricing.php` ให้เว็บแสดงตัวเลขจริง
