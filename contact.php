<?php
require_once __DIR__ . '/includes/config.php';

$prefill_plan = isset($_GET['plan']) ? trim((string) $_GET['plan']) : '';
$prefill_name = isset($_GET['name']) ? trim((string) $_GET['name']) : '';
if ($prefill_plan !== '' && $prefill_name === '') {
    $catalog_item = plan_catalog_by_slug($prefill_plan);
    if ($catalog_item !== null) {
        $prefill_name = $catalog_item['title'];
    }
}

$page_title = 'ติดต่อเรา';
$page_description = 'ขอคำปรึกษาประกันฟรีจาก FWD — ฝากข้อมูล เราจะติดต่อกลับโดยเร็ว';
require_once __DIR__ . '/includes/header.php';

$interests = [
    'ประกันชีวิต',
    'ประกันสุขภาพ',
    'ประกันโรคร้ายแรง',
    'ประกันอุบัติเหตุ',
    'ประกันลงทุน',
    'ประกันออมทรัพย์',
    'ประกันบำนาญ',
];
?>

<section class="page-hero page-hero--orange">
    <div class="container">
        <nav class="page-hero__breadcrumb" aria-label="breadcrumb">
            <a href="<?= htmlspecialchars(page_url('index.php')) ?>">หน้าแรก</a> / ติดต่อเรา
        </nav>
        <h1>ขอคำปรึกษาฟรี</h1>
        <p class="page-hero__lead">ฝากข้อมูลไว้ ผู้เชี่ยวชาญของเราจะติดต่อกลับโดยเร็วที่สุด ไม่มีค่าใช้จ่าย</p>
    </div>
</section>

<section class="section section--contact">
    <div class="container">
        <div id="form-success" class="contact-form__alert contact-form__alert--success" hidden role="alert">
            <h4>ส่งข้อมูลเรียบร้อยแล้ว</h4>
            <p>ขอบคุณที่สนใจ FWD ทีมงานจะติดต่อกลับภายใน 1–2 วันทำการ (เว็บไซต์ตัวอย่าง)</p>
        </div>

        <?php if ($prefill_name !== ''): ?>
        <div class="contact-form__alert contact-form__alert--plan reveal" role="status">
            <p class="contact-form__alert-label">แผนที่สนใจ</p>
            <p class="contact-form__alert-value"><?= htmlspecialchars($prefill_name) ?></p>
        </div>
        <?php endif; ?>

        <form id="contact-form" class="contact-form reveal" action="#" method="post" novalidate>
            <?php if ($prefill_plan !== ''): ?>
            <input type="hidden" name="plan_slug" value="<?= htmlspecialchars($prefill_plan) ?>">
            <input type="hidden" name="plan_name" value="<?= htmlspecialchars($prefill_name) ?>">
            <?php endif; ?>

            <div class="contact-form__stack">
                <div class="contact-form__block">
                    <h2 class="contact-form__block-title">ช่องทางติดต่อกลับ</h2>
                    <p class="contact-form__block-desc">เลือกวิธีที่สะดวกให้เจ้าหน้าที่ติดต่อคุณ</p>
                    <div class="choice-group choice-group--row">
                        <label class="choice-chip">
                            <input type="radio" name="contact_method" value="phone" checked>
                            <span class="choice-chip__label">โทรศัพท์</span>
                        </label>
                        <label class="choice-chip">
                            <input type="radio" name="contact_method" value="face">
                            <span class="choice-chip__label">พบตัวต่อตัว</span>
                        </label>
                    </div>
                </div>

                <div class="contact-form__block">
                    <h2 class="contact-form__block-title">สนใจผลิตภัณฑ์</h2>
                    <p class="contact-form__block-desc">เลือกได้มากกว่า 1 รายการ</p>
                    <div class="choice-group choice-group--products">
                        <?php foreach ($interests as $i => $label): ?>
                        <label class="choice-chip choice-chip--check">
                            <input type="checkbox" name="interest[]" value="<?= htmlspecialchars($label) ?>"<?= $i === 1 ? ' checked' : '' ?>>
                            <span class="choice-chip__label"><?= htmlspecialchars($label) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="contact-form__block contact-form__block--plain">
                    <h2 class="contact-form__block-title">ข้อมูลของคุณ</h2>
                    <p class="contact-form__block-desc">กรอกข้อมูลให้ครบถ้วน ช่องที่มี * จำเป็นต้องกรอก</p>

                    <div class="contact-form__fields">
                        <div class="form-group">
                            <label for="first_name">ชื่อ <span class="required">*</span></label>
                            <input type="text" id="first_name" name="first_name" required autocomplete="given-name" placeholder="ชื่อจริง">
                        </div>
                        <div class="form-group">
                            <label for="last_name">นามสกุล <span class="required">*</span></label>
                            <input type="text" id="last_name" name="last_name" required autocomplete="family-name" placeholder="นามสกุล">
                        </div>
                        <div class="form-group">
                            <label for="dob">วันเกิด <span class="required">*</span></label>
                            <input type="date" id="dob" name="dob" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">เบอร์โทรศัพท์ <span class="required">*</span></label>
                            <input type="tel" id="phone" name="phone" required autocomplete="tel" placeholder="08xxxxxxxx">
                        </div>
                        <div class="form-group form-group--full">
                            <label for="email">อีเมล <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required autocomplete="email" placeholder="name@email.com">
                        </div>
                        <div class="form-group">
                            <label for="province">จังหวัด</label>
                            <select id="province" name="province">
                                <option value="">เลือกจังหวัด</option>
                                <option value="กรุงเทพมหานคร">กรุงเทพมหานคร</option>
                                <option value="เชียงใหม่">เชียงใหม่</option>
                                <option value="ขอนแก่น">ขอนแก่น</option>
                                <option value="ภูเก็ต">ภูเก็ต</option>
                                <option value="ชลบุรี">ชลบุรี</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="preferred_time">เวลาที่สะดวก</label>
                            <select id="preferred_time" name="preferred_time">
                                <option value="">เลือกเวลา</option>
                                <option value="09-12">09:00 – 12:00</option>
                                <option value="12-15">12:00 – 15:00</option>
                                <option value="15-18">15:00 – 18:00</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-form__footer">
                <label class="contact-form__consent">
                    <input type="checkbox" name="consent" required>
                    <span>ข้าพเจ้ายินยอมให้ FWD Life Insurance Public Company Limited เก็บรวบรวมและใช้ข้อมูลส่วนบุคคลเพื่อเสนอผลิตภัณฑ์และบริการ ตามนโยบายความเป็นส่วนตัว <span class="required">*</span></span>
                </label>
                <button type="submit" class="btn btn--primary btn--lg contact-form__submit">ส่งข้อมูล</button>
            </div>
        </form>
    </div>
</section>

<section class="section section--gray">
    <div class="container">
        <div class="stats-strip reveal" style="border: none; padding: 0;">
            <div class="stat-item">
                <strong>1351</strong>
                <span>สายด่วนตลอด 24 ชม.</span>
            </div>
            <div class="stat-item">
                <strong>09:00</strong>
                <span>เปิดให้บริการทุกวัน 09:00 น.</span>
            </div>
            <div class="stat-item">
                <strong>ออนไลน์</strong>
                <span>แชทผ่านเว็บไซต์และแอป</span>
            </div>
            <div class="stat-item">
                <strong>FWD MAX</strong>
                <span>จัดการกรมธรรม์ผ่านแอป</span>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
