<?php

require_once __DIR__ . '/_bootstrap.php';

admin_require_login();

require_once dirname(__DIR__) . '/includes/article-helpers.php';



$store = cms_load('articles', ['items' => []]);

$items = $store['items'] ?? [];

$editSlug = trim((string) ($_GET['slug'] ?? ''));

$isNew = isset($_GET['new']);



$edit = null;

if ($editSlug !== '') {

    foreach ($items as $a) {

        if (($a['slug'] ?? '') === $editSlug) {

            $edit = article_normalize($a);

            break;

        }

    }

    if ($edit === null) {

        admin_flash('error', 'ไม่พบบทความ');

        admin_redirect('articles.php');

    }

}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {

    $action = admin_post_string('action');



    if ($action === 'delete' && $editSlug !== '') {

        $items = array_values(array_filter($items, fn ($a) => ($a['slug'] ?? '') !== $editSlug));

        cms_save('articles', ['items' => $items]);

        admin_flash('success', 'ลบบทความแล้ว');

        admin_redirect('articles.php');

    }



    if ($action === 'save') {

        $slug = admin_post_string('slug');

        $title = admin_post_string('title');

        if ($slug === '') {

            $slug = article_slugify($title);

        } else {

            $slug = article_slugify($slug);

        }



        $content = article_sanitize_html(admin_post_string('content'));

        $readMin = admin_post_int('read_min', 0);

        if ($readMin <= 0) {

            $readMin = article_reading_time(['content' => $content]);

        }



        $article = article_normalize([

            'slug' => $slug,

            'title' => $title,

            'excerpt' => admin_post_string('excerpt'),

            'category' => admin_post_string('category'),

            'date' => admin_post_string('date', date('j M. Y')),

            'read_min' => $readMin,

            'image' => admin_post_string('image'),

            'image_alt' => admin_post_string('image_alt'),

            'content' => $content,

            'meta_title' => admin_post_string('meta_title'),

            'meta_description' => admin_post_string('meta_description'),

            'focus_keyword' => admin_post_string('focus_keyword'),

            'og_image' => admin_post_string('og_image'),

            'canonical' => admin_post_string('canonical'),

            'noindex' => !empty($_POST['noindex']),

        ]);



        $oldSlug = admin_post_string('old_slug');

        if ($oldSlug !== '' && $oldSlug !== $slug) {

            $items = array_values(array_filter($items, fn ($a) => ($a['slug'] ?? '') !== $oldSlug));

        } else {

            $items = array_values(array_filter($items, fn ($a) => ($a['slug'] ?? '') !== $slug));

        }

        $items[] = $article;

        usort($items, fn ($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));



        cms_save('articles', ['items' => $items]);

        admin_flash('success', 'บันทึกบทความเรียบร้อย');

        admin_redirect('article-edit.php?slug=' . urlencode($slug));

    }

}



$page_title = $isNew ? 'เพิ่มบทความ' : 'แก้ไขบทความ';

$active_file = 'articles.php';

$data = $edit ?? article_normalize([

    'slug' => '',

    'title' => '',

    'excerpt' => '',

    'category' => 'ความรู้ประกัน',

    'date' => date('j M. Y'),

    'read_min' => 5,

    'image' => '',

    'image_alt' => '',

    'content' => '',

    'meta_title' => '',

    'meta_description' => '',

    'focus_keyword' => '',

    'og_image' => '',

    'canonical' => '',

    'noindex' => false,

]);

$seo = article_seo_score($data);

$previewUrl = $data['slug'] !== '' ? '../article.php?slug=' . urlencode($data['slug']) : '';



ob_start();

?>

<form method="post" class="admin-form" id="article-form" data-article-upload="<?= admin_h(admin_url('article-upload.php')) ?>">

    <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">

    <input type="hidden" name="action" value="save">

    <?php if ($edit): ?><input type="hidden" name="old_slug" value="<?= admin_h($edit['slug']) ?>"><?php endif; ?>

    <input type="hidden" name="content" id="content-input" value="">



    <div class="admin-card">

        <div class="admin-card__head">

            <h2 class="admin-card__title">ข้อมูลบทความ</h2>

            <?php if ($previewUrl !== ''): ?>

            <a href="<?= admin_h($previewUrl) ?>" target="_blank" rel="noopener" class="admin-btn admin-btn--outline admin-btn--sm">ดูหน้าเว็บ ↗</a>

            <?php endif; ?>

        </div>

        <div class="form-row">

            <label for="title">หัวข้อบทความ <span class="req">*</span></label>

            <input type="text" id="title" name="title" value="<?= admin_h($data['title']) ?>" required data-seo-field>

        </div>

        <div class="form-grid-2">

            <div class="form-row">

                <label for="slug">Slug (URL) <span class="req">*</span></label>

                <input type="text" id="slug" name="slug" value="<?= admin_h($data['slug']) ?>" placeholder="เช่น health-insurance-guide" pattern="[a-z0-9\-]+">

                <p class="form-hint">article.php?slug=<code id="slug-preview"><?= admin_h($data['slug'] ?: 'your-slug') ?></code></p>

            </div>

            <div class="form-row">

                <label for="category">หมวด</label>

                <input type="text" id="category" name="category" value="<?= admin_h($data['category']) ?>" list="article-categories">

                <datalist id="article-categories">

                    <option value="ประกันสุขภาพ">

                    <option value="ประกันชีวิต">

                    <option value="ความรู้ประกัน">

                    <option value="การเงิน">

                    <option value="ซื้อออนไลน์">

                </datalist>

            </div>

            <div class="form-row">

                <label for="date">วันที่เผยแพร่</label>

                <input type="text" id="date" name="date" value="<?= admin_h($data['date']) ?>">

            </div>

            <div class="form-row">

                <label for="read_min">เวลาอ่าน (นาที)</label>

                <input type="number" id="read_min" name="read_min" min="1" max="60" value="<?= (int) $data['read_min'] ?>">

                <p class="form-hint">เว้นว่าง = คำนวณจากความยาวเนื้อหาอัตโนมัติ</p>

            </div>

        </div>

        <div class="form-row">

            <label for="excerpt">คำโปรย (แสดงในการ์ด + ใต้หัวข้อ)</label>

            <textarea id="excerpt" name="excerpt" rows="3" data-seo-field><?= admin_h($data['excerpt']) ?></textarea>

            <p class="form-hint char-hint" data-for="excerpt" data-max="200"></p>

        </div>

    </div>



    <div class="admin-card">

        <h2 class="admin-card__title">รูปภาพ</h2>

        <div class="form-grid-2">

            <?php admin_image_field('image', 'รูปปก (path)', $data['image'], null, ['size' => 'wide', 'id' => 'image']); ?>

            <div class="form-row">

                <label for="image_alt">Alt text รูปปก (SEO)</label>

                <input type="text" id="image_alt" name="image_alt" value="<?= admin_h($data['image_alt']) ?>" placeholder="อธิบายรูปสำหรับ Google">

            </div>

            <?php admin_image_field('og_image', 'รูป OG / แชร์โซเชียล (ไม่บังคับ)', $data['og_image'], null, ['size' => 'md', 'id' => 'og_image', 'hint' => 'เว้นว่าง = ใช้รูปปก']); ?>

        </div>

    </div>



    <div class="admin-card">

        <h2 class="admin-card__title">เนื้อหาบทความ</h2>

        <p class="admin-card__lead">เครื่องมือจัดรูปแบบครบ — หัวข้อ รายการ ลิงก์ รูปภาพ อ้างอิง</p>

        <div id="article-editor" class="article-editor"><?= $data['content'] ?></div>

    </div>



    <div class="admin-card admin-seo-panel">

        <div class="admin-card__head">

            <h2 class="admin-card__title">SEO</h2>

            <span class="admin-badge <?= $seo['percent'] >= 70 ? 'admin-badge--live' : '' ?>" id="seo-score-badge"><?= (int) $seo['percent'] ?>% SEO</span>

        </div>



        <div class="seo-snippet-preview" id="seo-snippet">

            <p class="seo-snippet-preview__url" id="seo-preview-url"><?= admin_h(cms_get('site', 'site_name', 'FWD')) ?> › article › <span id="seo-preview-slug"><?= admin_h($data['slug'] ?: 'slug') ?></span></p>

            <p class="seo-snippet-preview__title" id="seo-preview-title"><?= admin_h(article_seo_title($data)) ?></p>

            <p class="seo-snippet-preview__desc" id="seo-preview-desc"><?= admin_h(article_seo_description($data)) ?></p>

        </div>



        <div class="form-row">

            <label for="meta_title">Meta title</label>

            <input type="text" id="meta_title" name="meta_title" value="<?= admin_h($data['meta_title']) ?>" placeholder="เว้นว่าง = ใช้หัวข้อบทความ" data-seo-field>

            <p class="form-hint char-hint" data-for="meta_title" data-min="30" data-max="60"></p>

        </div>

        <div class="form-row">

            <label for="meta_description">Meta description</label>

            <textarea id="meta_description" name="meta_description" rows="3" placeholder="เว้นว่าง = ใช้คำโปรย" data-seo-field><?= admin_h($data['meta_description']) ?></textarea>

            <p class="form-hint char-hint" data-for="meta_description" data-min="120" data-max="160"></p>

        </div>

        <div class="form-grid-2">

            <div class="form-row">

                <label for="focus_keyword">Focus keyword</label>

                <input type="text" id="focus_keyword" name="focus_keyword" value="<?= admin_h($data['focus_keyword']) ?>" placeholder="เช่น ประกันสุขภาพเหมาจ่าย">

            </div>

            <div class="form-row">

                <label for="canonical">Canonical URL (ไม่บังคับ)</label>

                <input type="url" id="canonical" name="canonical" value="<?= admin_h($data['canonical']) ?>" placeholder="https://...">

            </div>

        </div>

        <div class="form-row">

            <label><input type="checkbox" name="noindex" value="1" <?= !empty($data['noindex']) ? 'checked' : '' ?>> ไม่ให้ Google index (noindex)</label>

        </div>



        <ul class="seo-checklist" id="seo-checklist">

            <?php foreach ($seo['checks'] as $check): ?>

            <li class="<?= $check['ok'] ? 'is-ok' : '' ?>">

                <span class="seo-checklist__icon"><?= $check['ok'] ? '✓' : '○' ?></span>

                <span><strong><?= admin_h($check['label']) ?></strong> — <?= admin_h($check['hint']) ?></span>

            </li>

            <?php endforeach; ?>

        </ul>

    </div>



    <div class="admin-actions">

        <a href="<?= admin_h(admin_url('articles.php')) ?>" class="admin-btn admin-btn--outline">กลับ</a>

        <button type="submit" class="admin-btn admin-btn--primary">บันทึกบทความ</button>

        <?php if ($edit): ?>

        <button type="submit" name="action" value="delete" class="admin-btn admin-btn--danger" onclick="return confirm('ลบบทความนี้?')">ลบ</button>

        <?php endif; ?>

    </div>

</form>



<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">

<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>

<script src="<?= admin_h('../assets/js/article-editor.js') ?>?v=3"></script>

<?php

$content = ob_get_clean();

require __DIR__ . '/_layout.php';

