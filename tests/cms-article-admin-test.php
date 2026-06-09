<?php
declare(strict_types=1);

return function (TestRunner $t): void {
    $root = dirname(__DIR__);
    require_once $root . '/tests/render-helper.php';

    $renderAdmin = function (string $file, array $get = []) use ($root): string {
        return test_render_admin_page($root, $file, $get);
    };

    $t->group('เมนูบทความ (Admin UI)', function (TestRunner $t) use ($root, $renderAdmin): void {
        $t->test('articles.php แสดงตารางและลิงก์แก้ไข', function (TestRunner $t) use ($renderAdmin): void {
            $html = $renderAdmin('articles.php');
            $t->assertNotEmpty($html);
            $t->assertFalse(str_contains($html, 'Fatal error'));
            $t->assertContains('รายการบทความ', $html);
            $t->assertContains('article-edit.php', $html);
            $t->assertContains('เพิ่มบทความ', $html);
            $t->assertContains('admin-table', $html);
            $t->assertContains('SEO', $html);
            $t->assertContains('admin-inline-form', $html);
            $t->assertContains('value="delete"', $html);
        });

        $t->test('article-edit.php (ใหม่) มีฟอร์ม + Quill + SEO', function (TestRunner $t) use ($renderAdmin): void {
            $html = $renderAdmin('article-edit.php', ['new' => '1']);
            $t->assertNotEmpty($html);
            $t->assertFalse(str_contains($html, 'Fatal error'));
            $t->assertContains('id="article-form"', $html);
            $t->assertContains('id="article-editor"', $html);
            $t->assertContains('id="content-input"', $html);
            $t->assertContains('quill@1.3.7/dist/quill.snow.css', $html);
            $t->assertContains('quill@1.3.7/dist/quill.min.js', $html);
            $t->assertContains('article-editor.js', $html);
            $t->assertContains('data-article-upload', $html);
            $t->assertContains('article-upload.php', $html);
            $t->assertContains('id="meta_title"', $html);
            $t->assertContains('id="meta_description"', $html);
            $t->assertContains('id="focus_keyword"', $html);
            $t->assertContains('seo-snippet-preview', $html);
            $t->assertContains('บันทึกบทความ', $html);
        });

        $t->test('article-edit.php โหลดบทความเดิมใน editor', function (TestRunner $t) use ($renderAdmin, $root): void {
            require_once $root . '/includes/article-helpers.php';
            require_once $root . '/includes/articles-data.php';
            $first = article_normalize(articles_all()[0] ?? []);
            $t->assertNotEmpty($first['slug']);
            $slug = $first['slug'];

            $html = $renderAdmin('article-edit.php', ['slug' => $slug]);
            $t->assertFalse(str_contains($html, 'Fatal error'));
            $t->assertContains('name="old_slug"', $html);
            $t->assertContains('value="' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '"', $html);
            $t->assertContains(htmlspecialchars($first['title'], ENT_QUOTES, 'UTF-8'), $html);
            $t->assertContains('ดูหน้าเว็บ', $html);

            $snippet = mb_substr(strip_tags($first['content']), 0, 20);
            if ($snippet !== '') {
                $t->assertContains($snippet, $html);
            }
        });

        $t->test('article-editor.js กำหนด toolbar Quill ครบ', function (TestRunner $t) use ($root): void {
            $js = file_get_contents($root . '/assets/js/article-editor.js');
            $t->assertNotEmpty($js);
            foreach (['new Quill', 'theme: \'snow\'', 'toolbar:', 'bold', 'italic', 'link', 'image', 'blockquote', 'content-input', 'dangerouslyPasteHTML', 'data-article-upload', 'article-image-picker', 'normalizeContentImages'] as $needle) {
                $t->assertContains($needle, $js, "Missing: {$needle}");
            }
        });

        $t->test('admin.css มีสไตล์ editor', function (TestRunner $t) use ($root): void {
            $css = file_get_contents($root . '/assets/css/admin.css');
            $t->assertContains('.article-editor', $css);
            $t->assertContains('.article-image-picker', $css);
            $t->assertContains('.seo-snippet-preview', $css);
        });
    });

    $t->group('บทความ Rich Text save → แสดงผล', function (TestRunner $t) use ($root): void {
        require_once $root . '/includes/cms-loader.php';
        require_once $root . '/includes/article-helpers.php';

        $t->test('บันทึก HTML จาก editor แล้วแสดงบนหน้าเว็บ', function (TestRunner $t) use ($root): void {
            $slug = '_test-rich-article-' . time();
            $richHtml = '<h2>หัวข้อรอง</h2><p>ย่อหน้า<strong>ตัวหนา</strong> และ <a href="contact.php">ลิงก์</a></p><ul><li>รายการ 1</li><li>รายการ 2</li></ul><blockquote>อ้างอิง</blockquote>';

            $store = cms_load('articles', ['items' => []]);
            $items = array_values(array_filter($store['items'] ?? [], fn ($a) => ($a['slug'] ?? '') !== $slug));

            $article = article_normalize([
                'slug' => $slug,
                'title' => 'บทความทดสอบ Rich Text',
                'excerpt' => 'คำโปรยทดสอบ editor',
                'category' => 'ทดสอบ',
                'date' => date('j M. Y'),
                'content' => article_sanitize_html($richHtml),
                'meta_title' => 'Meta Title ทดสอบบทความ Rich Text Editor',
                'meta_description' => str_repeat('คำอธิบาย SEO สำหรับทดสอบ editor ', 8),
                'focus_keyword' => 'ประกัน',
                'image' => 'assets/images/products/health.png',
                'image_alt' => 'รูปทดสอบ',
            ]);
            $items[] = $article;
            cms_save('articles', ['items' => $items]);

            $loaded = null;
            foreach (cms_load('articles')['items'] ?? [] as $row) {
                if (($row['slug'] ?? '') === $slug) {
                    $loaded = article_normalize($row);
                    break;
                }
            }
            $t->assertNotNull($loaded);
            $t->assertContains('<h2>', $loaded['content']);
            $t->assertContains('<strong>ตัวหนา</strong>', $loaded['content']);
            $t->assertContains('<ul>', $loaded['content']);
            $t->assertFalse(str_contains($loaded['content'], '<script'));

            $html = test_render_frontend($root, 'article.php', ['slug' => $slug]);
            $t->assertContains('<h2>หัวข้อรอง</h2>', $html);
            $t->assertContains('article-prose', $html);
            $t->assertContains('Meta Title ทดสอบบทความ Rich Text Editor', $html);
            $t->assertContains('application/ld+json', $html);

            $items = array_values(array_filter(cms_load('articles')['items'] ?? [], fn ($a) => ($a['slug'] ?? '') !== $slug));
            cms_save('articles', ['items' => $items]);
        });

        $t->test('article_sanitize อนุญาตแท็ก editor หลัก', function (TestRunner $t): void {
            $input = '<h2>H</h2><p><strong>B</strong></p><ul><li>A</li></ul><blockquote>Q</blockquote><p><a href="/x">L</a></p>';
            $out = article_sanitize_html($input);
            foreach (['<h2>', '<strong>', '<ul>', '<blockquote>', '<a href='] as $tag) {
                $t->assertContains($tag, $out, "Stripped: {$tag}");
            }
        });
    });
};
