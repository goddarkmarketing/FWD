<?php
$page_title = 'การเคลมประกัน';
$page_description = 'วิธีเคลมประกันกับ FWD — ง่าย รวดเร็ว เคลมออนไลน์ได้ 24 ชั่วโมง';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <nav class="page-hero__breadcrumb" aria-label="breadcrumb">
            <a href="<?= htmlspecialchars(page_url('index.php')) ?>">หน้าแรก</a> / การเคลม
        </nav>
        <h1>การเคลมประกัน</h1>
        <p class="page-hero__lead">เคลมออนไลน์ได้ตลอด 24 ชั่วโมง หรือติดต่อศูนย์บริการลูกค้า 1351</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <header class="section__header reveal">
            <p class="section__eyebrow">ขั้นตอน</p>
            <h2 class="section__title">เคลมประกันใน 4 ขั้นตอน</h2>
        </header>
        <div class="steps reveal">
            <div class="step-item">
                <h3>แจ้งเคลม</h3>
                <p>แจ้งเคลมผ่านแอป FWD Omne หรือเว็บไซต์ หรือโทร 1351</p>
            </div>
            <div class="step-item">
                <h3>ส่งเอกสาร</h3>
                <p>อัปโหลดเอกสารที่จำเป็น เช่น ใบรับรองแพทย์ ใบเสร็จ</p>
            </div>
            <div class="step-item">
                <h3>ตรวจสอบ</h3>
                <p>ทีมงานตรวจสอบเอกสารและแจ้งผลภายในระยะเวลาที่กำหนด</p>
            </div>
            <div class="step-item">
                <h3>รับเงิน</h3>
                <p>รับเงินค่าสินไหมทดแทนผ่านบัญชีธนาคารที่ลงทะเบียน</p>
            </div>
        </div>
    </div>
</section>

<section class="section section--gray">
    <div class="container">
        <div class="feature-row reveal">
            <div class="feature-row__content">
                <h2>เคลมออนไลน์ผ่าน FWD Omne</h2>
                <p>ดาวน์โหลดแอป FWD Omne เพื่อแจ้งเคลม ติดตามสถานะ และจัดการกรมธรรม์ได้ในที่เดียว</p>
                <ul class="feature-list">
                    <li>แจ้งเคลมค่ารักษาพยาบาล (ผู้ป่วยนอก/ใน)</li>
                    <li>แจ้งเคลมประกันอุบัติเหตุ</li>
                    <li>แจ้งเคลมประกันโรคร้ายแรง</li>
                    <li>ติดตามสถานะแบบเรียลไทม์</li>
                </ul>
                <a href="<?= htmlspecialchars(page_url('contact.php')) ?>" class="btn btn--primary">ติดต่อศูนย์บริการ</a>
            </div>
            <div class="feature-row__media">
                <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800&q=80" alt="เคลมออนไลน์" loading="lazy">
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <header class="section__header reveal">
            <h2 class="section__title">เอกสารที่ใช้เคลม</h2>
            <p class="section__desc">เอกสารอาจแตกต่างตามประเภทการเคลม — ตรวจสอบเงื่อนไขในกรมธรรม์ของคุณ</p>
        </header>
        <div class="highlight-grid reveal">
            <div class="highlight-item">
                <h4>ค่ารักษาพยาบาล</h4>
                <p>ใบเสร็จรับเงิน ใบรับรองแพทย์ สำเนาบัตรประชาชน</p>
            </div>
            <div class="highlight-item">
                <h4>อุบัติเหตุ</h4>
                <p>ใบรับรองแพทย์ รายงานอุบัติเหตุ (ถ้ามี) สำเนาบัตรประชาชน</p>
            </div>
            <div class="highlight-item">
                <h4>โรคร้ายแรง</h4>
                <p>ผลการวินิจฉัยจากแพทย์ ประวัติการรักษา สำเนาบัตรประชาชน</p>
            </div>
            <div class="highlight-item">
                <h4>เสียชีวิต</h4>
                <p>มรณบัตร ทะเบียนบ้าน สำเนาบัตรผู้รับผลประโยชน์</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
