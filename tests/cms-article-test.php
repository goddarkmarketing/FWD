<?php
declare(strict_types=1);

return function (TestRunner $t): void {
    $root = dirname(__DIR__);

    $t->group('Article helpers & SEO', function (TestRunner $t) use ($root): void {
        require_once $root . '/includes/article-helpers.php';

        $t->test('article_normalize migrate body → content', function (TestRunner $t): void {
            $a = article_normalize([
                'title' => 'Test',
                'body' => ['ย่อหน้าแรก', 'ย่อหน้าสอง'],
            ]);
            $t->assertContains('<p>ย่อหน้าแรก</p>', $a['content']);
            $t->assertContains('<p>ย่อหน้าสอง</p>', $a['content']);
        });

        $t->test('article_sanitize_html ลบ script', function (TestRunner $t): void {
            $clean = article_sanitize_html('<p>OK</p><script>alert(1)</script><p>More</p>');
            $t->assertContains('<p>OK</p>', $clean);
            $t->assertFalse(str_contains($clean, 'script'));
        });

        $t->test('article_seo_title และ description fallback', function (TestRunner $t): void {
            $a = ['title' => 'หัวข้อ', 'excerpt' => 'คำโปรย'];
            $t->assertEquals('หัวข้อ', article_seo_title($a));
            $t->assertEquals('คำโปรย', article_seo_description($a));
            $a['meta_title'] = 'SEO Title';
            $t->assertEquals('SEO Title', article_seo_title($a));
        });

        $t->test('article_slugify', function (TestRunner $t): void {
            $t->assertEquals('hello-world', article_slugify('Hello World!'));
        });

        $t->test('article_seo_score', function (TestRunner $t): void {
            $score = article_seo_score([
                'title' => 'หัวข้อบทความที่ยาวพอสมควรสำหรับ SEO',
                'excerpt' => str_repeat('คำอธิบาย ', 20),
                'focus_keyword' => 'ประกัน',
                'content' => '<p>เนื้อหาเกี่ยวกับประกันสุขภาพ</p>',
                'image' => 'assets/test.jpg',
                'image_alt' => 'alt',
                'slug' => 'test-slug',
            ]);
            $t->assertGreaterThan(50, $score['percent']);
            $t->assertGreaterThan(0, count($score['checks']));
        });

        $t->test('article_render_content HTML', function (TestRunner $t): void {
            $html = article_render_content([
                'content' => '<p>Hello</p><h2>Heading</h2>',
            ]);
            $t->assertContains('<h2>Heading</h2>', $html);
        });
    });
};
