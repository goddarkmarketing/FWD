<?php
declare(strict_types=1);

return function (TestRunner $t): void {
    $root = dirname(__DIR__);

    $t->group('Config & URL helpers', function (TestRunner $t) use ($root): void {
        putenv('FWD_STATIC_BUILD=0');
        putenv('FWD_BASE_URL=/fwd');
        $_SERVER['SCRIPT_NAME'] = '/fwd/index.php';
        $_SERVER['PHP_SELF'] = '/fwd/index.php';

        if (!defined('BASE_URL')) {
            require_once $root . '/includes/config.php';
        }

        $t->test('asset() สร้าง URL ถูกต้อง', function (TestRunner $t): void {
            $t->assertEquals('/fwd/assets/css/style.css', asset('assets/css/style.css'));
            $t->assertEquals('/fwd/images/x.png', asset('/images/x.png'));
        });

        $t->test('media_url() encode อักขระพิเศษ', function (TestRunner $t): void {
            $url = media_url('assets/รีวิว/test image.png');
            $t->assertContains('/fwd/', $url);
            $t->assertContains('%', $url);
        });

        $t->test('image_url() รองรับ http', function (TestRunner $t): void {
            $ext = 'https://example.com/a.png';
            $t->assertEquals($ext, image_url($ext));
        });

        $t->test('page_url() ชี้ไปไฟล์ PHP', function (TestRunner $t): void {
            $t->assertEquals('/fwd/contact.php', page_url('contact.php'));
        });

        $t->test('tel_href() แปลงเบอร์โทร', function (TestRunner $t): void {
            $t->assertEquals('tel:0866004939', tel_href('086-600-4939'));
            $t->assertEquals('#', tel_href(''));
        });

        $t->test('is_active() และ active_class()', function (TestRunner $t): void {
            $_SERVER['PHP_SELF'] = '/fwd/index.php';
            $t->assertTrue(is_active('index.php'));
            $t->assertFalse(is_active('about.php'));
            $t->assertEquals('is-active', active_class('index.php'));
            $t->assertEquals('', active_class('about.php'));
        });

        $t->test('hero_cover_image() คืน path หรือ null', function (TestRunner $t) use ($root): void {
            $hero = hero_cover_image();
            if (is_dir($root . '/assets/cover')) {
                $files = glob($root . '/assets/cover/*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE) ?: [];
                if ($files !== []) {
                    $t->assertNotNull($hero);
                    $t->assertContains('assets/cover/', $hero);
                }
            }
        });

        $t->test('SITE_NAME โหลดจาก CMS', function (TestRunner $t) use ($root): void {
            require_once $root . '/includes/cms-loader.php';
            $t->assertEquals(cms_get('site', 'site_name', ''), SITE_NAME);
        });
    });
};
