<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();

$page_title = 'คู่มือการใช้งาน';
$active_file = 'manual.php';

$pdfTh = '../manual/' . rawurlencode('คู่มือระบบหลังบ้าน-FWD-CMS.pdf');
$productHtml = '../manual/product-guide-visual.html';
$productPdf = '../manual/' . rawurlencode('คู่มือเพิ่ม-ยกเลิกสินค้า-หน้าจอจริง.pdf');

ob_start();
?>
<div class="admin-card">
    <div class="admin-card__head">
        <h2 class="admin-card__title">ทัวร์แนะนำทีละขั้นตอน</h2>
    </div>
    <p style="margin:0 0 1rem;color:var(--admin-muted);line-height:1.6">
        กดหัวข้อด้านล่าง ระบบจะพาไปหน้าจริงในหลังบ้าน
        แล้วขึ้นป๊อปอัปอธิบายทีละจุดจนจบขั้นตอน
    </p>

    <div class="admin-tour-grid">
        <div class="admin-tour-card">
            <span class="admin-tour-card__badge">ผลิตภัณฑ์</span>
            <h3>เพิ่มแผน</h3>
            <p>สอนเปิดแคตตาล็อก กดเพิ่มแผน กรอก slug / หมวด / ชื่อ แล้วบันทึกขึ้นเว็บ</p>
            <button type="button" class="admin-btn admin-btn--primary admin-btn--sm" data-start-tour="add-plan">เริ่มทัวร์</button>
        </div>
        <div class="admin-tour-card">
            <span class="admin-tour-card__badge">ผลิตภัณฑ์</span>
            <h3>ยกเลิก / ซ่อนแผน</h3>
            <p>สอนค้นหาแผน กดลบ ดูสถานะซ่อนอยู่ และกู้คืนด้วยปุ่มแสดงอีกครั้ง</p>
            <button type="button" class="admin-btn admin-btn--primary admin-btn--sm" data-start-tour="remove-plan">เริ่มทัวร์</button>
        </div>
        <div class="admin-tour-card">
            <span class="admin-tour-card__badge">เนื้อหา</span>
            <h3>แก้ไขโปรโมชัน</h3>
            <p>สอนความหมายของจำนวนบนหน้าแรก การเพิ่ม และการแก้ไขวันที่/หัวข้อ</p>
            <button type="button" class="admin-btn admin-btn--primary admin-btn--sm" data-start-tour="promotions">เริ่มทัวร์</button>
        </div>
    </div>

    <p style="margin:0;font-size:.88rem;color:var(--admin-muted)">
        ระหว่างทัวร์ กด <strong>ถัดไป</strong> เพื่อไปขั้นถัดไป · กด <strong>ข้ามทัวร์</strong> เพื่อยกเลิกได้ทุกเมื่อ
    </p>
</div>

<div class="admin-card">
    <div class="admin-card__head">
        <h2 class="admin-card__title">เอกสารคู่มือ (PDF / ภาพ)</h2>
    </div>
    <p style="margin:0 0 1rem;color:var(--admin-muted);line-height:1.6">
        หากต้องการเปิดอ่านแบบเอกสาร หรือส่งให้ทีมงาน ดาวน์โหลดไฟล์ด้านล่างได้
    </p>
    <div class="admin-actions" style="margin-top:0;padding-top:0;border-top:0">
        <a href="<?= admin_h($productHtml) ?>" target="_blank" rel="noopener" class="admin-btn admin-btn--outline">คู่มือภาพหน้าจอจริง</a>
        <a href="<?= admin_h($productPdf) ?>" target="_blank" rel="noopener" class="admin-btn admin-btn--outline">PDF เพิ่ม/ยกเลิกสินค้า</a>
        <a href="<?= admin_h($pdfTh) ?>" target="_blank" rel="noopener" class="admin-btn admin-btn--outline">PDF คู่มือทั้งหมด</a>
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
