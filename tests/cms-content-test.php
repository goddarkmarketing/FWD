<?php
declare(strict_types=1);

return function (TestRunner $t): void {
    $root = dirname(__DIR__);

    $t->group('Articles', function (TestRunner $t) use ($root): void {
        putenv('FWD_BASE_URL=/fwd');
        $_SERVER['SCRIPT_NAME'] = '/fwd/index.php';
        require_once $root . '/includes/config.php';
        require_once $root . '/includes/articles-data.php';

        $t->test('articles_all() มีข้อมูล', function (TestRunner $t): void {
            $articles = articles_all();
            $t->assertGreaterThan(0, count($articles));
            foreach ($articles as $a) {
                $t->assertArrayHasKeys(['slug', 'title', 'excerpt', 'image', 'content'], $a);
            }
        });

        $t->test('article_by_slug() พบและไม่พบ', function (TestRunner $t): void {
            $all = articles_all();
            $first = $all[0];
            $found = article_by_slug($first['slug']);
            $t->assertNotNull($found);
            $t->assertEquals($first['title'], $found['title']);
            $t->assertNull(article_by_slug('slug-that-does-not-exist-xyz'));
        });

        $t->test('article_url()', function (TestRunner $t): void {
            $url = article_url('test-slug');
            $t->assertContains('article.php', $url);
            $t->assertContains('test-slug', $url);
        });

        $t->test('article_stock_image()', function (TestRunner $t): void {
            $img = article_stock_image(1);
            $t->assertNotEmpty($img);
            $img2 = article_stock_image(2);
            $t->assertNotEmpty($img2);
        });
    });

    $t->group('Promotions', function (TestRunner $t) use ($root): void {
        require_once $root . '/includes/promotions-data.php';

        $t->test('promotions_all()', function (TestRunner $t): void {
            $all = promotions_all();
            $t->assertGreaterThan(0, count($all));
            foreach ($all as $p) {
                $t->assertArrayHasKeys(['badge', 'title', 'desc', 'url'], $p);
            }
        });

        $t->test('promotions_home() จำกัดตาม home_count', function (TestRunner $t) use ($root): void {
            require_once $root . '/includes/cms-loader.php';
            $cms = cms_load('promotions');
            $home = promotions_home();
            $expected = (int) ($cms['home_count'] ?? 2);
            $t->assertEquals($expected, count($home));
        });
    });

    $t->group('Thai provinces', function (TestRunner $t) use ($root): void {
        require_once $root . '/includes/thai-provinces.php';

        $t->test('thai_provinces() ครบ 77 จังหวัด', function (TestRunner $t): void {
            $provinces = thai_provinces();
            $t->assertEquals(77, count($provinces));
            $t->assertInArray('กรุงเทพมหานคร', $provinces);
            $t->assertInArray('เชียงใหม่', $provinces);
            $unique = array_unique($provinces);
            $t->assertEquals(77, count($unique), 'จังหวัดซ้ำ');
        });
    });

    $t->group('Admin helpers', function (TestRunner $t) use ($root): void {
        $_SESSION = [];
        require_once $root . '/admin/_bootstrap.php';

        $t->test('admin_url()', function (TestRunner $t): void {
            $t->assertContains('backup.php', admin_url('backup.php'));
        });

        $t->test('admin_post_string/int', function (TestRunner $t): void {
            $_POST = ['name' => '  test  ', 'num' => '42'];
            $t->assertEquals('test', admin_post_string('name'));
            $t->assertEquals('default', admin_post_string('missing', 'default'));
            $t->assertEquals(42, admin_post_int('num'));
            $t->assertEquals(5, admin_post_int('missing', 5));
        });

        $t->test('admin_verify_password()', function (TestRunner $t): void {
            $t->assertTrue(admin_verify_password('28Mar2523'));
            $t->assertFalse(admin_verify_password('wrong'));
        });

        $t->test('admin_image_src()', function (TestRunner $t) use ($root): void {
            $logo = cms_get('site', 'site_logo_path', '');
            if ($logo !== '' && is_file($root . '/' . $logo)) {
                $src = admin_image_src($logo);
                $t->assertNotNull($src);
                $t->assertContains('../', $src);
            }
            $t->assertNull(admin_image_src(''));
            $t->assertNull(admin_image_src('assets/not-exists-xyz.png'));
            $t->assertEquals('https://example.com/x.png', admin_image_src('https://example.com/x.png'));
        });

        $t->test('admin_icon() ทุกชื่อในเมนู', function (TestRunner $t) use ($root): void {
            require_once $root . '/admin/_icons.php';
            $menu = require $root . '/admin/_menu.php';
            foreach ($menu as $group) {
                foreach ($group['items'] as $item) {
                    $svg = admin_icon($item['icon']);
                    $t->assertContains('<svg', $svg, 'icon: ' . $item['icon']);
                }
            }
        });
    });
};
