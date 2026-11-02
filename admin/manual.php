<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();

$page_title = 'คู่มือการใช้งาน';
$active_file = 'manual.php';

$pdfTh = '../manual/' . rawurlencode('คู่มือระบบหลังบ้าน-FWD-CMS.pdf');
$pdfEn = '../manual/FWD-CMS-Admin-Manual.pdf';
$html = '../manual/admin-manual.html';
$productHtml = '../manual/product-steps.html';
$productPdf = '../manual/' . rawurlencode('คู่มือเพิ่ม-ยกเลิกสินค้า-FWD.pdf');

ob_start();
?>
<div class="admin-card">
    <div class="admin-card__head">
        <h2 class="admin-card__title">คู่มือภาพ — เพิ่ม / ยกเลิกสินค้า</h2>
    </div>
    <p style="margin:0 0 1rem;color:var(--admin-muted);line-height:1.6">
        สอนทีละขั้นพร้อมภาพ กรอบสี และลูกศรชี้ปุ่มที่ต้องกด
        สำหรับเพิ่มแผนใหม่ หรือซ่อน/ลบแผนที่มีอยู่บนเว็บ
    </p>
    <div class="admin-actions" style="margin-top:0;padding-top:0;border-top:0">
        <a href="<?= admin_h($productHtml) ?>" target="_blank" rel="noopener" class="admin-btn admin-btn--primary">เปิดคู่มือเพิ่ม/ยกเลิกสินค้า</a>
        <a href="<?= admin_h($productPdf) ?>" target="_blank" rel="noopener" class="admin-btn admin-btn--outline">ดาวน์โหลด PDF</a>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card__head">
        <h2 class="admin-card__title">คู่มือระบบหลังบ้าน CMS (ทั้งหมด)</h2>
    </div>
    <p style="margin:0 0 1rem;color:var(--admin-muted);line-height:1.6">
        คู่มืออธิบายทีละเมนู พร้อมภาพจำลอง กรอบสี และลูกศรชี้จุดสำคัญ
        — เปิดดูออนไลน์ หรือดาวน์โหลด PDF เก็บไว้ในเครื่อง
    </p>
    <div class="admin-actions" style="margin-top:0;padding-top:0;border-top:0">
        <a href="<?= admin_h($html) ?>" target="_blank" rel="noopener" class="admin-btn admin-btn--primary">เปิดคู่มือ (HTML)</a>
        <a href="<?= admin_h($pdfTh) ?>" target="_blank" rel="noopener" class="admin-btn admin-btn--outline">ดาวน์โหลด PDF</a>
        <a href="<?= admin_h($pdfEn) ?>" target="_blank" rel="noopener" class="admin-btn admin-btn--outline">PDF (ชื่อไฟล์ EN)</a>
    </div>
</div>

<div class="admin-card">
    <h2 class="admin-card__title">สรุปสั้น ๆ</h2>
    <ul style="margin:0;padding-left:1.2rem;line-height:1.8;color:var(--admin-muted)">
        <li>แก้เนื้อหาแล้วกด <strong>บันทึก</strong> — ผลขึ้นที่เว็บ PHP ทันที</li>
        <li>ถ้าไม่เห็นผล ให้รีเฟรชแรง ๆ (Ctrl+F5)</li>
        <li>โปรโมชัน: ช่อง “จำนวนบนหน้าแรก” ≠ จำนวนรายการทั้งหมด</li>
        <li>ลบแผนเดิม = ซ่อน (กู้คืนได้) · แผนที่เพิ่มเอง = ลบถาวร</li>
        <li>GitHub Pages เป็นไฟล์นิ่ง ไม่ดึงจากแอดมินอัตโนมัติ</li>
    </ul>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
