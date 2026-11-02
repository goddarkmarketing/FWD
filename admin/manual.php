<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();

$page_title = 'คู่มือการใช้งาน';
$active_file = 'manual.php';

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
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
