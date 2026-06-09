<?php
require_once __DIR__ . '/_bootstrap.php';
admin_require_login();
require_once dirname(__DIR__) . '/includes/config.php';

$allowed = ['hero', 'plans_section', 'plan_filters', 'consultation', 'why_fwd', 'reviews', 'promos_section', 'articles_section'];
$id = trim((string) ($_GET['id'] ?? ''));

if (!in_array($id, $allowed, true)) {
    admin_redirect('homepage.php');
}

$labels = [
    'hero' => 'ภาพ Hero',
    'plans_section' => 'ส่วนแผนประกัน',
    'plan_filters' => 'ตัวกรองแผนประกัน',
    'consultation' => 'ปรึกษาฟรี',
    'why_fwd' => 'ทำไมต้อง FWD',
    'reviews' => 'รีวิวลูกค้า',
    'promos_section' => 'โปรโมชัน',
    'articles_section' => 'บทความ',
];

$page_title = 'แก้ไขหน้าแรก: ' . $labels[$id];
$active_file = 'homepage.php';
$data = cms_load('homepage', []);
$site = cms_load('site', []);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    if ($id === 'hero') {
        $site['hero_alt'] = admin_post_string('hero_alt');
        cms_save('site', $site);
    } elseif ($id === 'plans_section') {
        $data['plans_section'] = [
            'eyebrow' => admin_post_string('eyebrow'),
            'title' => admin_post_string('title'),
            'desc' => admin_post_string('desc'),
            'search_placeholder' => admin_post_string('search_placeholder'),
        ];
        cms_save('homepage', $data);
    } elseif ($id === 'plan_filters') {
        $filters = [];
        $filterCount = (int) ($_POST['filter_count'] ?? 0);
        for ($i = 0; $i < $filterCount; $i++) {
            $fid = admin_post_string("filter_id_$i");
            if ($fid === '') {
                continue;
            }
            $filters[] = [
                'id' => $fid,
                'label' => admin_post_string("filter_label_$i"),
                'default' => !empty($_POST["filter_default_$i"]),
            ];
        }
        $data['plan_filters'] = $filters;

        $panelCopy = $data['plan_panel_copy'] ?? [];
        foreach (array_keys($panelCopy) as $key) {
            $panelCopy[$key] = [
                'title' => admin_post_string("panel_title_$key"),
                'desc' => admin_post_string("panel_desc_$key"),
            ];
        }
        $data['plan_panel_copy'] = $panelCopy;
        cms_save('homepage', $data);
    } elseif ($id === 'consultation') {
        $data['consultation'] = [
            'eyebrow' => admin_post_string('eyebrow'),
            'title' => admin_post_string('title'),
            'desc' => admin_post_string('desc'),
            'bullets' => array_values(array_filter(array_map('trim', explode("\n", admin_post_string('bullets'))))),
            'image' => admin_post_string('image'),
            'cta' => admin_post_string('cta'),
        ];
        cms_save('homepage', $data);
    } elseif ($id === 'why_fwd') {
        $cards = [];
        for ($i = 0; $i < 4; $i++) {
            $cards[] = [
                'value' => admin_post_string("why_value_$i"),
                'label' => admin_post_string("why_label_$i"),
            ];
        }
        $data['why_fwd'] = [
            'eyebrow' => admin_post_string('eyebrow'),
            'title' => admin_post_string('title'),
            'desc' => admin_post_string('desc'),
            'cards' => $cards,
        ];
        cms_save('homepage', $data);
    } elseif ($id === 'reviews') {
        $items = [];
        foreach ((array) ($_POST['reviews'] ?? []) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $text = trim((string) ($row['text'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));
            $meta = trim((string) ($row['meta'] ?? ''));
            $rating = max(1, min(5, (int) ($row['rating'] ?? 5)));
            if ($text === '' && $name === '') {
                continue;
            }
            $items[] = [
                'rating' => $rating,
                'text' => $text,
                'name' => $name,
                'meta' => $meta,
            ];
        }
        $data['reviews'] = [
            'eyebrow' => admin_post_string('eyebrow'),
            'title' => admin_post_string('title'),
            'desc' => admin_post_string('desc'),
            'items' => $items,
            'gallery' => [
                'eyebrow' => admin_post_string('gallery_eyebrow'),
                'title' => admin_post_string('gallery_title'),
            ],
        ];
        cms_save('homepage', $data);
    } elseif ($id === 'promos_section') {
        $data['promos_section'] = [
            'eyebrow' => admin_post_string('eyebrow'),
            'title' => admin_post_string('title'),
            'cta' => admin_post_string('cta'),
        ];
        cms_save('homepage', $data);
    } elseif ($id === 'articles_section') {
        $data['articles_section'] = [
            'eyebrow' => admin_post_string('eyebrow'),
            'title' => admin_post_string('title'),
            'lead' => admin_post_string('lead'),
            'cta' => admin_post_string('cta'),
        ];
        cms_save('homepage', $data);
    }

    admin_flash('success', 'บันทึกเรียบร้อย');
    admin_redirect('homepage-edit.php?id=' . urlencode($id));
}

ob_start();
?>
<form method="post" class="admin-form">
    <input type="hidden" name="_csrf" value="<?= admin_h(admin_csrf_token()) ?>">
    <div class="admin-card">
        <p style="margin:0 0 1.25rem;color:var(--admin-muted)">
            <code><?= admin_h($id) ?></code>
            · <a href="../index.php" target="_blank" rel="noopener">ดูหน้าแรก ↗</a>
        </p>

        <?php if ($id === 'hero'): ?>
        <div class="form-row">
            <label for="hero_alt">ข้อความ alt รูป Hero</label>
            <input type="text" id="hero_alt" name="hero_alt" value="<?= admin_h($site['hero_alt'] ?? '') ?>">
        </div>
        <?php
        $heroDesktop = hero_cover_image();
        $heroMobile = hero_cover_mobile_image();
        if ($heroDesktop !== null || $heroMobile !== null):
        ?>
        <div class="form-row">
            <label>รูป Hero ปัจจุบัน</label>
            <?php if ($heroDesktop !== null): ?>
            <p class="form-hint" style="margin:0 0 .35rem">Desktop</p>
            <?php admin_image_preview($heroDesktop, 'Hero desktop', 'lg'); ?>
            <?php endif; ?>
            <?php if ($heroMobile !== null): ?>
            <p class="form-hint" style="margin:.75rem 0 .35rem">Mobile</p>
            <?php admin_image_preview($heroMobile, 'Hero mobile', 'sm'); ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <p class="form-hint">อัปโหลดรูปที่ <a href="<?= admin_h(admin_url('media.php')) ?>">สื่อ & รูปภาพ</a> — ตั้งชื่อ <code>hero-banner.jpg</code> ในโฟลเดอร์ <code>assets/cover/</code></p>

        <?php elseif ($id === 'plans_section'):
            $ps = $data['plans_section'] ?? [];
        ?>
        <div class="form-grid-2">
            <div class="form-row"><label>Eyebrow</label><input type="text" name="eyebrow" value="<?= admin_h($ps['eyebrow'] ?? '') ?>"></div>
            <div class="form-row"><label>หัวข้อ</label><input type="text" name="title" value="<?= admin_h($ps['title'] ?? '') ?>"></div>
        </div>
        <div class="form-row"><label>คำอธิบาย</label><textarea name="desc"><?= admin_h($ps['desc'] ?? '') ?></textarea></div>
        <div class="form-row"><label>Placeholder ค้นหา</label><input type="text" name="search_placeholder" value="<?= admin_h($ps['search_placeholder'] ?? '') ?>"></div>

        <?php elseif ($id === 'plan_filters'):
            $filters = $data['plan_filters'] ?? [];
            $panelCopy = $data['plan_panel_copy'] ?? [];
        ?>
        <input type="hidden" name="filter_count" value="<?= count($filters) ?>">
        <h2 class="admin-card__title" style="margin-bottom:1rem">ปุ่มกรอง</h2>
        <?php foreach ($filters as $i => $f): ?>
        <div class="admin-repeater-item">
            <div class="form-grid-2">
                <div class="form-row"><label>ID</label><input type="text" name="filter_id_<?= $i ?>" value="<?= admin_h($f['id'] ?? '') ?>" readonly></div>
                <div class="form-row"><label>ชื่อปุ่ม</label><input type="text" name="filter_label_<?= $i ?>" value="<?= admin_h($f['label'] ?? '') ?>"></div>
            </div>
            <div class="form-row">
                <label><input type="checkbox" name="filter_default_<?= $i ?>" value="1" <?= !empty($f['default']) ? 'checked' : '' ?>> ค่าเริ่มต้น</label>
            </div>
        </div>
        <?php endforeach; ?>
        <h2 class="admin-card__title" style="margin:1.5rem 0 1rem">ข้อความแผงด้านซ้าย</h2>
        <?php foreach ($panelCopy as $pid => $panel): ?>
        <div class="admin-repeater-item">
            <p style="margin:0 0 .75rem;font-weight:600"><code><?= admin_h($pid) ?></code></p>
            <div class="form-row"><label>หัวข้อ</label><input type="text" name="panel_title_<?= admin_h($pid) ?>" value="<?= admin_h($panel['title'] ?? '') ?>"></div>
            <div class="form-row"><label>คำอธิบาย</label><textarea name="panel_desc_<?= admin_h($pid) ?>"><?= admin_h($panel['desc'] ?? '') ?></textarea></div>
        </div>
        <?php endforeach; ?>

        <?php elseif ($id === 'consultation'):
            $c = $data['consultation'] ?? [];
        ?>
        <div class="form-grid-2">
            <div class="form-row"><label>Eyebrow</label><input type="text" name="eyebrow" value="<?= admin_h($c['eyebrow'] ?? '') ?>"></div>
            <div class="form-row"><label>หัวข้อ</label><input type="text" name="title" value="<?= admin_h($c['title'] ?? '') ?>"></div>
        </div>
        <div class="form-row"><label>คำอธิบาย</label><textarea name="desc"><?= admin_h($c['desc'] ?? '') ?></textarea></div>
        <div class="form-row"><label>รายการ (บรรทัดละ 1)</label><textarea name="bullets"><?= admin_h(implode("\n", $c['bullets'] ?? [])) ?></textarea></div>
        <div class="form-grid-2">
            <?php admin_image_field('image', 'รูปภาพ (path)', $c['image'] ?? '', null, ['size' => 'wide']); ?>
            <div class="form-row"><label>ปุ่ม CTA</label><input type="text" name="cta" value="<?= admin_h($c['cta'] ?? '') ?>"></div>
        </div>

        <?php elseif ($id === 'why_fwd'):
            $w = $data['why_fwd'] ?? [];
            $cards = $w['cards'] ?? array_fill(0, 4, ['value' => '', 'label' => '']);
        ?>
        <div class="form-grid-2">
            <div class="form-row"><label>Eyebrow</label><input type="text" name="eyebrow" value="<?= admin_h($w['eyebrow'] ?? '') ?>"></div>
            <div class="form-row"><label>หัวข้อ</label><input type="text" name="title" value="<?= admin_h($w['title'] ?? '') ?>"></div>
        </div>
        <div class="form-row"><label>คำอธิบาย</label><textarea name="desc"><?= admin_h($w['desc'] ?? '') ?></textarea></div>
        <?php for ($i = 0; $i < 4; $i++): ?>
        <div class="form-grid-2" style="margin-top:.75rem">
            <div class="form-row"><label>การ์ด <?= $i + 1 ?> ตัวเลข</label><input type="text" name="why_value_<?= $i ?>" value="<?= admin_h($cards[$i]['value'] ?? '') ?>"></div>
            <div class="form-row"><label>การ์ด <?= $i + 1 ?> ข้อความ</label><input type="text" name="why_label_<?= $i ?>" value="<?= admin_h($cards[$i]['label'] ?? '') ?>"></div>
        </div>
        <?php endfor; ?>

        <?php elseif ($id === 'reviews'):
            $r = $data['reviews'] ?? [];
            $items = $r['items'] ?? [];
            if ($items === []) {
                $items = [['rating' => 5, 'text' => '', 'name' => '', 'meta' => '']];
            }
        ?>
        <div class="form-grid-2">
            <div class="form-row"><label>Eyebrow</label><input type="text" name="eyebrow" value="<?= admin_h($r['eyebrow'] ?? '') ?>"></div>
            <div class="form-row"><label>หัวข้อ</label><input type="text" name="title" value="<?= admin_h($r['title'] ?? '') ?>"></div>
        </div>
        <div class="form-row"><label>คำอธิบาย</label><textarea name="desc"><?= admin_h($r['desc'] ?? '') ?></textarea></div>
        <div class="admin-card__head" style="margin:1rem 0 .75rem">
            <h2 class="admin-card__title" style="margin:0">รีวิว</h2>
            <button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-repeater-add="#reviews-list">+ เพิ่มรีวิว</button>
        </div>
        <div id="reviews-list" data-repeater-list>
        <?php foreach ($items as $i => $item): ?>
        <div class="admin-repeater-item">
            <div class="admin-repeater-item__head">
                <p class="admin-repeater-item__title">รีวิวที่ <span data-repeater-index><?= $i + 1 ?></span></p>
                <button type="button" class="admin-btn admin-btn--outline admin-btn--sm" data-repeater-remove>ลบ</button>
            </div>
            <div class="form-grid-2">
                <div class="form-row"><label>ชื่อ</label><input type="text" name="reviews[<?= $i ?>][name]" value="<?= admin_h($item['name'] ?? '') ?>"></div>
                <div class="form-row"><label>Meta</label><input type="text" name="reviews[<?= $i ?>][meta]" value="<?= admin_h($item['meta'] ?? '') ?>"></div>
                <div class="form-row"><label>ดาว (1–5)</label><input type="number" min="1" max="5" name="reviews[<?= $i ?>][rating]" value="<?= (int) ($item['rating'] ?? 5) ?>"></div>
            </div>
            <div class="form-row"><label>ข้อความรีวิว</label><textarea name="reviews[<?= $i ?>][text]"><?= admin_h($item['text'] ?? '') ?></textarea></div>
        </div>
        <?php endforeach; ?>
        </div>
        <div class="form-grid-2" style="margin-top:1rem">
            <div class="form-row"><label>แกลเลอรี Eyebrow</label><input type="text" name="gallery_eyebrow" value="<?= admin_h($r['gallery']['eyebrow'] ?? '') ?>"></div>
            <div class="form-row"><label>แกลเลอรี หัวข้อ</label><input type="text" name="gallery_title" value="<?= admin_h($r['gallery']['title'] ?? '') ?>"></div>
        </div>
        <?php
        $reviewDir = dirname(__DIR__) . '/assets/รีวิว';
        $reviewImages = [];
        if (is_dir($reviewDir)) {
            foreach (['jpg', 'jpeg', 'png', 'webp', 'gif'] as $ext) {
                $reviewImages = array_merge($reviewImages, glob($reviewDir . '/*.' . $ext) ?: [], glob($reviewDir . '/*.' . strtoupper($ext)) ?: []);
            }
            $reviewImages = array_slice(array_values(array_unique($reviewImages)), 0, 12);
        }
        if ($reviewImages !== []):
        ?>
        <div class="form-row">
            <label>รูปแกลเลอรีปัจจุบัน (<?= count($reviewImages) ?>)</label>
            <div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.5rem">
            <?php foreach ($reviewImages as $imgFile):
                $rel = 'assets/รีวิว/' . basename($imgFile);
                $src = admin_image_src($rel);
                if ($src === null) continue;
            ?>
                <img class="admin-media-thumb" src="<?= admin_h($src) ?>" alt="" style="width:72px;height:72px">
            <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <p class="form-hint">รูปแกลเลอรี: อัปโหลดที่ <a href="<?= admin_h(admin_url('media.php')) ?>">สื่อ & รูปภาพ</a> → <code>assets/รีวิว/</code></p>

        <?php elseif ($id === 'promos_section'):
            $p = $data['promos_section'] ?? [];
        ?>
        <div class="form-grid-2">
            <div class="form-row"><label>Eyebrow</label><input type="text" name="eyebrow" value="<?= admin_h($p['eyebrow'] ?? '') ?>"></div>
            <div class="form-row"><label>หัวข้อ</label><input type="text" name="title" value="<?= admin_h($p['title'] ?? '') ?>"></div>
            <div class="form-row"><label>ปุ่ม CTA</label><input type="text" name="cta" value="<?= admin_h($p['cta'] ?? '') ?>"></div>
        </div>
        <p class="form-hint">รายการโปรโมชันแก้ที่เมนู <a href="<?= admin_h(admin_url('promotions.php')) ?>">โปรโมชัน</a></p>

        <?php elseif ($id === 'articles_section'):
            $a = $data['articles_section'] ?? [];
        ?>
        <div class="form-grid-2">
            <div class="form-row"><label>Eyebrow</label><input type="text" name="eyebrow" value="<?= admin_h($a['eyebrow'] ?? '') ?>"></div>
            <div class="form-row"><label>หัวข้อ</label><input type="text" name="title" value="<?= admin_h($a['title'] ?? '') ?>"></div>
            <div class="form-row"><label>Lead</label><input type="text" name="lead" value="<?= admin_h($a['lead'] ?? '') ?>"></div>
            <div class="form-row"><label>ปุ่ม CTA</label><input type="text" name="cta" value="<?= admin_h($a['cta'] ?? '') ?>"></div>
        </div>
        <p class="form-hint">รายการบทความแก้ที่เมนู <a href="<?= admin_h(admin_url('articles.php')) ?>">บทความ</a></p>
        <?php endif; ?>
    </div>
    <div class="admin-actions">
        <a href="<?= admin_h(admin_url('homepage.php')) ?>" class="admin-btn admin-btn--outline">กลับ</a>
        <button type="submit" class="admin-btn admin-btn--primary">บันทึก</button>
    </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
