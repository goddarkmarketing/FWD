<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();

$page_title = 'สื่อ & รูปภาพ';
$active_file = 'media.php';
$root = dirname(__DIR__);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    $subdir = admin_post_string('subdir', 'uploads');
    $allowedDirs = ['cover', 'uploads', 'images', 'รีวิว'];
    if (!in_array($subdir, $allowedDirs, true)) {
        $subdir = 'uploads';
    }
    $upload = cms_upload('file', $subdir);
    if ($upload) {
        admin_flash('success', 'อัปโหลดเรียบร้อย: ' . $upload);
    } else {
        admin_flash('error', 'อัปโหลดไม่สำเร็จ');
    }
    admin_redirect('media.php');
}

function list_images(string $dir): array
{
    if (!is_dir($dir)) {
        return [];
    }
    $exts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf'];
    $files = [];
    foreach ($exts as $ext) {
        $files = array_merge($files, glob($dir . '/*.' . $ext) ?: [], glob($dir . '/*.' . strtoupper($ext)) ?: []);
    }
    rsort($files);
    return array_slice($files, 0, 30);
}

$coverFiles = list_images($root . '/assets/cover');
$reviewFiles = list_images($root . '/assets/รีวิว');
$uploadFiles = list_images($root . '/assets/uploads');

ob_start();
?>
<div class="admin-card">
    <h2>อัปโหลดไฟล์</h2>
    <form method="post" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">
        <div class="form-grid-2">
            <div class="form-row">
                <label for="subdir">โฟลเดอร์ปลายทาง</label>
                <select name="subdir" id="subdir">
                    <option value="cover">assets/cover (Hero หน้าแรก)</option>
                    <option value="uploads">assets/uploads</option>
                    <option value="images">assets/images</option>
                    <option value="รีวิว">assets/รีวิว (แกลเลอรีรีวิว)</option>
                </select>
            </div>
            <div class="form-row">
                <label for="file">ไฟล์</label>
                <input type="file" id="file" name="file" accept="image/*,.pdf" required>
            </div>
        </div>
        <p class="form-hint">Hero หน้าแรก: ตั้งชื่อไฟล์ <code>hero-banner.jpg</code> หรือ <code>hero-banner-mobile.jpg</code> เมื่ออัปโหลดไป cover</p>
        <button type="submit" class="admin-btn admin-btn--primary">อัปโหลด</button>
    </form>
</div>

<?php
function render_file_list(string $title, array $files, string $basePath): void {
    echo '<div class="admin-card"><h2>' . admin_h($title) . '</h2>';
    if ($files === []) {
        echo '<p style="color:var(--admin-muted)">ไม่มีไฟล์</p>';
    } else {
        echo '<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>ตัวอย่าง</th><th>ไฟล์</th><th>Path สำหรับ CMS</th></tr></thead><tbody>';
        foreach ($files as $f) {
            $rel = $basePath . '/' . basename($f);
            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
            echo '<tr><td>';
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                $src = admin_image_src($rel);
                if ($src !== null) {
                    echo '<img class="admin-media-thumb" src="' . admin_h($src) . '" alt="">';
                }
            } else {
                echo '—';
            }
            echo '</td><td>' . admin_h(basename($f)) . '</td><td><code>' . admin_h($rel) . '</code></td></tr>';
        }
        echo '</tbody></table></div>';
    }
    echo '</div>';
}
render_file_list('Hero (assets/cover)', $coverFiles, 'assets/cover');
render_file_list('รีวิว (assets/รีวิว)', $reviewFiles, 'assets/รีวิว');
render_file_list('อัปโหลดล่าสุด (assets/uploads)', $uploadFiles, 'assets/uploads');
?>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
