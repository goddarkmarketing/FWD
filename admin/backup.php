<?php

require_once __DIR__ . '/_bootstrap.php';

admin_require_login();

require_once dirname(__DIR__) . '/includes/backup-manager.php';



$page_title = 'สำรอง & กู้คืนข้อมูล';

$active_file = 'backup.php';

$zipAvailable = class_exists('ZipArchive');

$collected = backup_collect_files();



if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {

    $action = admin_post_string('action');



    if ($action === 'create') {

        $result = backup_create(admin_post_string('note'));

        if ($result['ok']) {

            admin_flash('success', 'สร้างไฟล์สำรองเรียบร้อย: ' . $result['filename'] . ' (' . backup_format_size((int) $result['size']) . ', ' . (int) $result['file_count'] . ' ไฟล์)');

        } else {

            admin_flash('error', $result['error'] ?? 'สร้างสำรองไม่สำเร็จ');

        }

        admin_redirect('backup.php');

    }



    if ($action === 'delete') {

        $result = backup_delete(admin_post_string('filename'));

        admin_flash($result['ok'] ? 'success' : 'error', $result['ok'] ? 'ลบไฟล์สำรองแล้ว' : ($result['error'] ?? 'ลบไม่สำเร็จ'));

        admin_redirect('backup.php');

    }



    if ($action === 'restore' || $action === 'restore_upload') {

        if (!admin_verify_password(admin_post_string('confirm_password'))) {

            admin_flash('error', 'รหัสผ่านไม่ถูกต้อง — ยกเลิกการกู้คืน');

            admin_redirect('backup.php');

        }



        $zipPath = null;

        if ($action === 'restore') {

            $filename = basename(admin_post_string('filename'));

            if (!backup_is_valid_filename($filename)) {

                admin_flash('error', 'ชื่อไฟล์สำรองไม่ถูกต้อง');

                admin_redirect('backup.php');

            }

            $zipPath = backup_root() . '/' . $filename;

        } else {

            if (empty($_FILES['backup_file']['tmp_name']) || !is_uploaded_file($_FILES['backup_file']['tmp_name'])) {

                admin_flash('error', 'กรุณาเลือกไฟล์สำรอง (.zip)');

                admin_redirect('backup.php');

            }

            if (!backup_ensure_dir()) {

                admin_flash('error', 'เตรียมโฟลเดอร์สำรองไม่ได้');

                admin_redirect('backup.php');

            }

            $uploadName = 'fwd-restore-upload-' . backup_timestamp_slug() . '.zip';

            $zipPath = backup_root() . '/' . $uploadName;

            if (!move_uploaded_file($_FILES['backup_file']['tmp_name'], $zipPath)) {

                admin_flash('error', 'อัปโหลดไฟล์สำรองไม่สำเร็จ');

                admin_redirect('backup.php');

            }

        }



        $result = backup_restore($zipPath, !empty($_POST['safety_backup']));

        if ($action === 'restore_upload' && is_file($zipPath)) {

            @unlink($zipPath);

        }



        if ($result['ok']) {

            $msg = 'กู้คืนข้อมูลเรียบร้อย — ' . (int) $result['restored'] . ' ไฟล์';

            if (!empty($result['safety_backup'])) {

                $msg .= ' (สำรองความปลอดภัยก่อนกู้คืน: ' . $result['safety_backup'] . ')';

            }

            admin_flash('success', $msg);

        } else {

            admin_flash('error', $result['error'] ?? 'กู้คืนไม่สำเร็จ');

        }

        admin_redirect('backup.php');

    }

}



$backups = backup_list();



ob_start();

?>

<div class="admin-card">

    <h2 class="admin-card__title">สำรองข้อมูลลูกค้า</h2>

    <p class="admin-card__lead">

        รวมข้อมูล CMS ทั้งหมด (ข้อความ บทความ โปรโมชัน บัญชี admin) และไฟล์รูปที่อัปโหลด

        — <strong>ไม่รวมโค้ดระบบ</strong> เหมาะสำหรับเก็บก่อนอัปเดตเว็บจากนักพัฒนา

    </p>

    <?php if (!$zipAvailable): ?>

    <p class="admin-alert admin-alert--error" style="margin:0">เซิร์ฟเวอร์ไม่รองรับ ZipArchive — เปิด extension <code>zip</code> ใน php.ini</p>

    <?php else: ?>

    <ul style="margin:0 0 1.25rem;padding-left:1.25rem;color:var(--admin-muted);font-size:.9rem;line-height:1.7">

        <li>ข้อมูล CMS: <code>data/cms/</code> (<?= count(array_filter(array_keys($collected), fn ($k) => str_starts_with($k, 'cms/'))) ?> ไฟล์)</li>

        <?php foreach (backup_customer_asset_dirs() as $dir):

            $count = count(array_filter(array_keys($collected), fn ($k) => $k === $dir || str_starts_with($k, $dir . '/')));

        ?>

        <li>ไฟล์: <code><?= admin_h($dir) ?></code> (<?= $count ?> ไฟล์)</li>

        <?php endforeach; ?>

        <li>รวมทั้งหมดที่จะสำรอง: <strong><?= count($collected) ?></strong> ไฟล์</li>

    </ul>

    <form method="post" class="admin-form">

        <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">

        <input type="hidden" name="action" value="create">

        <div class="form-row" style="max-width:480px">

            <label for="note">หมายเหตุ (ไม่บังคับ)</label>

            <input type="text" id="note" name="note" placeholder="เช่น ก่อนอัปเดตเว็บ มิ.ย. 2026">

        </div>

        <button type="submit" class="admin-btn admin-btn--primary">สร้างไฟล์สำรองตอนนี้</button>

    </form>

    <?php endif; ?>

</div>



<div class="admin-card">

    <h2 class="admin-card__title">ไฟล์สำรองบนเซิร์ฟเวอร์</h2>

    <p class="admin-card__lead">ดาวน์โหลดเก็บไว้นอกเซิร์ฟเวอร์ หรือกู้คืนจากรายการด้านล่าง</p>

    <?php if ($backups === []): ?>

    <p style="margin:0;color:var(--admin-muted)">ยังไม่มีไฟล์สำรอง — กดปุ่มด้านบนเพื่อสร้างครั้งแรก</p>

    <?php else: ?>

    <div class="admin-table-wrap">

        <table class="admin-table">

            <thead>

                <tr>

                    <th>ไฟล์</th>

                    <th>วันที่</th>

                    <th>ขนาด</th>

                    <th>ไฟล์ในแพ็ก</th>

                    <th>หมายเหตุ</th>

                    <th></th>

                </tr>

            </thead>

            <tbody>

            <?php foreach ($backups as $row): ?>

            <tr>

                <td><code><?= admin_h($row['filename']) ?></code></td>

                <td><?= admin_h($row['created_at'] !== '' ? $row['created_at'] : '—') ?></td>

                <td><?= admin_h(backup_format_size((int) $row['size'])) ?></td>

                <td><?= (int) ($row['file_count'] ?? 0) ?></td>

                <td><?= admin_h($row['note'] ?? '') ?></td>

                <td style="white-space:nowrap">

                    <a href="<?= admin_h(admin_url('backup-download.php?file=' . urlencode($row['filename']))) ?>" class="admin-btn admin-btn--outline admin-btn--sm">ดาวน์โหลด</a>

                    <form method="post" style="display:inline" onsubmit="return confirm('กู้คืนจากไฟล์นี้? ข้อมูลปัจจุบันจะถูกแทนที่')">

                        <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">

                        <input type="hidden" name="action" value="restore">

                        <input type="hidden" name="filename" value="<?= admin_h($row['filename']) ?>">

                        <input type="hidden" name="confirm_password" value="" class="js-restore-password">

                        <input type="hidden" name="safety_backup" value="1">

                        <button type="button" class="admin-btn admin-btn--outline admin-btn--sm js-restore-btn" data-filename="<?= admin_h($row['filename']) ?>">กู้คืน</button>

                    </form>

                    <form method="post" style="display:inline" onsubmit="return confirm('ลบไฟล์สำรองนี้?')">

                        <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">

                        <input type="hidden" name="action" value="delete">

                        <input type="hidden" name="filename" value="<?= admin_h($row['filename']) ?>">

                        <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">ลบ</button>

                    </form>

                </td>

            </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

    <?php endif; ?>

</div>



<?php if ($zipAvailable): ?>

<div class="admin-card">

    <h2 class="admin-card__title">กู้คืนจากไฟล์ที่ดาวน์โหลดไว้</h2>

    <p class="admin-card__lead">

        อัปโหลดไฟล์ <code>.zip</code> ที่เคยสำรองไว้ (จากเซิร์ฟเวอร์นี้หรือเซิร์ฟเวอร์อื่น)

        — ระบบจะสร้างสำรองความปลอดภัยอัตโนมัติก่อนกู้คืน

    </p>

    <form method="post" enctype="multipart/form-data" class="admin-form" id="restore-upload-form">

        <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">

        <input type="hidden" name="action" value="restore_upload">

        <input type="hidden" name="safety_backup" value="1">

        <input type="hidden" name="confirm_password" id="restore-upload-password" value="">

        <div class="form-row" style="max-width:480px">

            <label for="backup_file">ไฟล์สำรอง (.zip)</label>

            <input type="file" id="backup_file" name="backup_file" accept=".zip,application/zip" required>

        </div>

        <button type="button" class="admin-btn admin-btn--primary js-restore-upload-btn">อัปโหลดและกู้คืน</button>

    </form>

</div>



<div class="admin-card">

    <h2 class="admin-card__title">คำแนะนำสำหรับนักพัฒนา</h2>

    <p class="admin-card__lead">เมื่ออัปเดตโค้ดเว็บ — ข้อมูลลูกค้าต้องไม่หาย</p>

    <ol style="margin:0;padding-left:1.25rem;color:var(--admin-muted);font-size:.9rem;line-height:1.8">

        <li>ให้ลูกค้าสร้างไฟล์สำรองจากหน้านี้ก่อนอัปเดต (หรือรัน <code>php scripts/backup-cms.php</code> บนโฮสต์)</li>

        <li>นักพัฒนาอัปโหลดเฉพาะโค้ดใหม่ — <strong>อย่าทับ</strong> <code>data/cms/</code> และไฟล์ใน <code>assets/cover</code>, <code>assets/uploads</code>, <code>assets/รีวิว</code></li>

        <li>หากข้อมูลหาย — กู้คืนจากไฟล์สำรองที่ลูกค้าเก็บไว้</li>

    </ol>

</div>

<?php endif; ?>



<script>

(function () {

    function askPassword(message, onOk) {

        var pwd = window.prompt(message || 'กรอกรหัสผ่าน admin เพื่อยืนยันการกู้คืน:');

        if (pwd === null || pwd === '') return;

        onOk(pwd);

    }



    document.querySelectorAll('.js-restore-btn').forEach(function (btn) {

        btn.addEventListener('click', function () {

            var form = btn.closest('form');

            if (!form) return;

            askPassword('กู้คืนจาก ' + (btn.getAttribute('data-filename') || 'ไฟล์นี้') + '?\n\nกรอกรหัสผ่าน admin:', function (pwd) {

                form.querySelector('.js-restore-password').value = pwd;

                form.submit();

            });

        });

    });



    var uploadBtn = document.querySelector('.js-restore-upload-btn');

    if (uploadBtn) {

        uploadBtn.addEventListener('click', function () {

            var form = document.getElementById('restore-upload-form');

            var file = form && form.querySelector('#backup_file');

            if (!file || !file.files || !file.files.length) {

                alert('กรุณาเลือกไฟล์สำรอง');

                return;

            }

            if (!confirm('กู้คืนข้อมูลจากไฟล์ที่อัปโหลด?\n\nข้อมูลปัจจุบันจะถูกแทนที่ (มีสำรองความปลอดภัยอัตโนมัติ)')) return;

            askPassword('กรอกรหัสผ่าน admin:', function (pwd) {

                document.getElementById('restore-upload-password').value = pwd;

                form.submit();

            });

        });

    }

})();

</script>

<?php

$content = ob_get_clean();

require __DIR__ . '/_layout.php';

