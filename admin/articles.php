<?php

require_once __DIR__ . '/_bootstrap.php';

admin_require_login();

require_once dirname(__DIR__) . '/includes/article-helpers.php';



$page_title = 'บทความ';

$active_file = 'articles.php';

$store = cms_load('articles', ['items' => []]);

$items = array_map('article_normalize', $store['items'] ?? []);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && admin_csrf_verify()) {
    if (admin_post_string('action') === 'delete') {
        $slug = admin_post_string('slug');
        if ($slug !== '') {
            $items = array_values(array_filter($store['items'] ?? [], fn ($a) => ($a['slug'] ?? '') !== $slug));
            cms_save('articles', ['items' => $items]);
            admin_flash('success', 'ลบบทความแล้ว');
        }
        admin_redirect('articles.php');
    }
}

$q = trim((string) ($_GET['q'] ?? ''));

if ($q !== '') {

    $items = array_values(array_filter($items, function ($a) use ($q) {

        $hay = ($a['slug'] ?? '') . ' ' . ($a['title'] ?? '') . ' ' . ($a['category'] ?? '') . ' ' . ($a['focus_keyword'] ?? '');

        return stripos($hay, $q) !== false;

    }));

}



ob_start();

?>

<div class="admin-card">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem">

        <h2 style="margin:0">รายการบทความ (<?= count($items) ?>)</h2>

        <a href="<?= admin_h(admin_url('article-edit.php?new=1')) ?>" class="admin-btn admin-btn--primary admin-btn--sm">+ เพิ่มบทความ</a>

    </div>

    <form method="get" class="admin-form admin-toolbar">

        <div class="form-row">

            <label for="q">ค้นหา</label>

            <input type="search" id="q" name="q" value="<?= admin_h($q) ?>" placeholder="หัวข้อ slug หมวด keyword...">

        </div>

        <button type="submit" class="admin-btn admin-btn--outline">ค้นหา</button>

    </form>

    <div class="admin-table-wrap">

        <table class="admin-table">

            <thead>

                <tr>

                    <th>หัวข้อ</th>

                    <th>หมวด</th>

                    <th>วันที่</th>

                    <th>SEO</th>

                    <th></th>

                </tr>

            </thead>

            <tbody>

            <?php foreach ($items as $a):

                $seo = article_seo_score($a);

            ?>

            <tr>

                <td>

                    <strong><?= admin_h($a['title'] ?? '') ?></strong><br>

                    <code><?= admin_h($a['slug'] ?? '') ?></code>

                </td>

                <td><?= admin_h($a['category'] ?? '') ?></td>

                <td><?= admin_h($a['date'] ?? '') ?></td>

                <td>

                    <span class="admin-badge <?= $seo['percent'] >= 70 ? 'admin-badge--live' : '' ?>"><?= (int) $seo['percent'] ?>%</span>

                    <?php if (!empty($a['noindex'])): ?><span class="admin-badge">noindex</span><?php endif; ?>

                </td>

                <td class="admin-table-actions">

                    <a href="<?= admin_h(admin_url('article-edit.php?slug=' . urlencode($a['slug'] ?? ''))) ?>" class="admin-btn admin-btn--outline admin-btn--sm">แก้ไข</a>

                    <a href="../article.php?slug=<?= urlencode($a['slug'] ?? '') ?>" target="_blank" rel="noopener" class="admin-btn admin-btn--outline admin-btn--sm">ดู ↗</a>

                    <?php admin_inline_post_form(
                        ['action' => 'delete', 'slug' => $a['slug'] ?? ''],
                        'ลบ',
                        'ลบบทความนี้?'
                    ); ?>

                </td>

            </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

<?php

$content = ob_get_clean();

require __DIR__ . '/_layout.php';

