<?php
$page_title = 'สมัครตัวแทน';
$page_description = 'สมัครเป็นตัวแทนประกันชีวิต FWD — เริ่มต้นอาชีพที่มั่นคง พร้อมทีมสนับสนุนและผลิตภัณฑ์ครบวงจร';
require_once __DIR__ . '/includes/header.php';

$education_levels = [
    'ประถมศึกษา',
    'ม.3',
    'ม.6',
    'ปวช.',
    'ปวส.',
    'ปริญญาตรี',
    'ปริญญาโท',
    'ปริญญาเอก',
    'อื่นๆ',
];
?>

<section class="page-hero page-hero--orange">
    <div class="container">
        <nav class="page-hero__breadcrumb" aria-label="breadcrumb">
            <a href="<?= htmlspecialchars(page_url('index.php')) ?>">หน้าแรก</a> / สมัครตัวแทน
        </nav>
        <h1>สมัครตัวแทน</h1>
        <p class="page-hero__lead">เริ่มต้นอาชีพตัวแทนประกันชีวิตกับ FWD — กรอกแบบฟอร์มด้านล่าง ทีมงานจะติดต่อกลับเพื่อแนะนำขั้นตอนต่อไป</p>
    </div>
</section>

<section class="section section--agent-apply">
    <div class="container">
            <div id="agent-form-success" class="contact-form__alert contact-form__alert--success reveal" hidden role="alert">
                <h4>ส่งใบสมัครเรียบร้อยแล้ว</h4>
                <p>ขอบคุณที่สนใจเป็นตัวแทน FWD ทีมงานจะติดต่อกลับภายใน 1–2 วันทำการ (เว็บไซต์ตัวอย่าง)</p>
            </div>

            <form id="agent-apply-form" class="agent-apply-form reveal" action="#" method="post" novalidate>
                <div class="agent-apply-form__section">
                    <h2 class="agent-apply-form__section-title">ข้อมูลส่วนตัว</h2>
                    <div class="agent-apply-form__grid">
                        <div class="form-group form-group--full">
                            <label for="agent_full_name">ชื่อ-นามสกุล <span class="required">*</span></label>
                            <input type="text" id="agent_full_name" name="full_name" required autocomplete="name" placeholder="ชื่อและนามสกุลตามบัตรประชาชน">
                        </div>
                        <div class="form-group">
                            <label for="agent_dob">เกิดวันที่ <span class="required">*</span></label>
                            <input type="date" id="agent_dob" name="dob" required>
                        </div>
                        <div class="form-group">
                            <label for="agent_age">อายุ</label>
                            <input type="text" id="agent_age" name="age" readonly placeholder="—" aria-readonly="true">
                        </div>
                        <div class="form-group form-group--full">
                            <label for="agent_phone">เบอร์โทรศัพท์ <span class="required">*</span></label>
                            <input type="tel" id="agent_phone" name="phone" required autocomplete="tel" placeholder="08xxxxxxxx">
                        </div>
                        <div class="form-group form-group--full">
                            <label for="agent_email">อีเมล <span class="required">*</span></label>
                            <input type="email" id="agent_email" name="email" required autocomplete="email" placeholder="name@email.com">
                        </div>
                    </div>
                </div>

                <div class="agent-apply-form__section">
                    <h2 class="agent-apply-form__section-title">ที่อยู่ตามบัตรประชาชน</h2>
                    <div class="agent-apply-form__grid">
                        <div class="form-group">
                            <label for="agent_address_no">บ้านเลขที่ / หมู่ <span class="required">*</span></label>
                            <input type="text" id="agent_address_no" name="address_no" required placeholder="เลขที่ หมู่">
                        </div>
                        <div class="form-group">
                            <label for="agent_street">ถนน</label>
                            <input type="text" id="agent_street" name="street" placeholder="ชื่อถนน">
                        </div>
                        <div class="form-group">
                            <label for="agent_subdistrict">ตำบล / แขวง <span class="required">*</span></label>
                            <input type="text" id="agent_subdistrict" name="subdistrict" required>
                        </div>
                        <div class="form-group">
                            <label for="agent_district">อำเภอ / เขต <span class="required">*</span></label>
                            <input type="text" id="agent_district" name="district" required>
                        </div>
                        <div class="form-group">
                            <label for="agent_province">จังหวัด <span class="required">*</span></label>
                            <input type="text" id="agent_province" name="province" required list="agent-province-list" placeholder="เลือกหรือพิมพ์จังหวัด">
                            <datalist id="agent-province-list">
                                <option value="กรุงเทพมหานคร">
                                <option value="เชียงใหม่">
                                <option value="ขอนแก่น">
                                <option value="ชลบุรี">
                                <option value="ภูเก็ต">
                                <option value="นครราชสีมา">
                                <option value="สงขลา">
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label for="agent_postal">รหัสไปรษณีย์ <span class="required">*</span></label>
                            <input type="text" id="agent_postal" name="postal" required inputmode="numeric" pattern="[0-9]{5}" maxlength="5" placeholder="10xxx">
                        </div>
                    </div>
                </div>

                <div class="agent-apply-form__section">
                    <h2 class="agent-apply-form__section-title">การศึกษาและประสบการณ์</h2>
                    <div class="agent-apply-form__grid">
                        <div class="form-group">
                            <label for="agent_education">วุฒิการศึกษา <span class="required">*</span></label>
                            <select id="agent_education" name="education" required>
                                <option value="">เลือกวุฒิการศึกษา</option>
                                <?php foreach ($education_levels as $level): ?>
                                <option value="<?= htmlspecialchars($level) ?>"><?= htmlspecialchars($level) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="agent_major">สาขาวิชา</label>
                            <input type="text" id="agent_major" name="major" placeholder="เช่น บริหารธุรกิจ">
                        </div>
                        <div class="form-group form-group--full">
                            <label for="agent_experience">ประสบการณ์การทำงาน</label>
                            <textarea id="agent_experience" name="experience" rows="4" placeholder="ระบุประสบการณ์ที่เกี่ยวข้อง เช่น การขาย การบริการลูกค้า หรืองานประกัน (ถ้ามี)"></textarea>
                        </div>
                    </div>
                </div>

                <div class="agent-apply-form__footer">
                    <label class="contact-form__consent">
                        <input type="checkbox" name="consent" required>
                        <span>ข้าพเจ้ายินยอมให้ FWD Life Insurance Public Company Limited เก็บรวบรวมและใช้ข้อมูลส่วนบุคคลเพื่อพิจารณาการสมัครเป็นตัวแทน ตามนโยบายความเป็นส่วนตัว <span class="required">*</span></span>
                    </label>
                    <div class="agent-apply-form__actions">
                        <a href="<?= htmlspecialchars(page_url('index.php')) ?>" class="btn btn--outline">ยกเลิก</a>
                        <button type="submit" class="btn btn--primary btn--lg">ยืนยันสมัคร</button>
                    </div>
                </div>
            </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
