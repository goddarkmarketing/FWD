<?php
declare(strict_types=1);

return function (TestRunner $t): void {
    $root = dirname(__DIR__);

    $t->group('Admin Auth & Helpers', function (TestRunner $t) use ($root): void {
        $_SESSION = [];
        require_once $root . '/admin/_bootstrap.php';

        $t->test('admin_auth_config มี email + hash', function (TestRunner $t): void {
            $auth = admin_auth_config();
            $t->assertArrayHasKeys(['email', 'password_hash'], $auth);
            $t->assertContains('@', $auth['email']);
        });

        $t->test('password_verify กับรหัสที่ตั้งไว้', function (TestRunner $t): void {
            $auth = admin_auth_config();
            $t->assertTrue(password_verify('28Mar2523', $auth['password_hash']));
            $t->assertFalse(password_verify('wrong-password', $auth['password_hash']));
        });

        $t->test('admin_csrf_token + verify', function (TestRunner $t): void {
            $token = admin_csrf_token();
            $t->assertEquals(64, strlen($token));
            $_POST['_csrf'] = $token;
            $t->assertTrue(admin_csrf_verify());
            $_POST['_csrf'] = 'invalid';
            $t->assertFalse(admin_csrf_verify());
        });

        $t->test('admin_flash get/clear', function (TestRunner $t): void {
            admin_flash('success', 'ทดสอบ');
            $flash = admin_flash_get();
            $t->assertEquals('success', $flash['type'] ?? '');
            $t->assertEquals('ทดสอบ', $flash['message'] ?? '');
            $t->assertTrue(admin_flash_get() === null);
        });

        $t->test('admin_h escape HTML', function (TestRunner $t): void {
            $t->assertEquals('&lt;script&gt;', admin_h('<script>'));
        });

        $t->test('admin_is_logged_in session', function (TestRunner $t): void {
            $t->assertFalse(admin_is_logged_in());
            $_SESSION['admin_user'] = 'test@example.com';
            $t->assertTrue(admin_is_logged_in());
        });

        $t->test('admin_csrf_token คงที่ใน session เดียว', function (TestRunner $t): void {
            $a = admin_csrf_token();
            $b = admin_csrf_token();
            $t->assertEquals($a, $b);
        });

        $t->test('admin_h null และ unicode', function (TestRunner $t): void {
            $t->assertEquals('', admin_h(null));
            $t->assertEquals('ทดสอบ', admin_h('ทดสอบ'));
        });

        $t->test('admin_inline_post_form สร้างฟอร์มลบ', function (TestRunner $t): void {
            ob_start();
            admin_inline_post_form(['action' => 'delete', 'slug' => 'test-slug'], 'ลบ', 'ยืนยัน?');
            $html = (string) ob_get_clean();
            $t->assertContains('admin-inline-form', $html);
            $t->assertContains('name="action"', $html);
            $t->assertContains('value="delete"', $html);
            $t->assertContains('value="test-slug"', $html);
        });
    });

    $t->group('Admin page includes', function (TestRunner $t) use ($root): void {
        $t->test('includes/header.php โหลดได้', function (TestRunner $t) use ($root): void {
            putenv('FWD_BASE_URL=/fwd');
            $_SERVER['SCRIPT_NAME'] = '/fwd/index.php';
            if (!defined('SITE_NAME')) {
                require_once $root . '/includes/config.php';
            }
            ob_start();
            $page_title = 'Test';
            include $root . '/includes/header.php';
            $html = (string) ob_get_clean();
            $t->assertContains('<html', $html);
            $t->assertContains(SITE_NAME, $html);
        });

        $t->test('includes/footer.php โหลดได้', function (TestRunner $t) use ($root): void {
            putenv('FWD_BASE_URL=/fwd');
            $_SERVER['SCRIPT_NAME'] = '/fwd/index.php';
            if (!defined('SITE_NAME')) {
                require_once $root . '/includes/config.php';
            }
            require_once $root . '/includes/cms-loader.php';
            ob_start();
            include $root . '/includes/footer.php';
            $html = (string) ob_get_clean();
            $t->assertContains('</footer>', $html);
        });
    });

    $t->group('Admin PHP Files', function (TestRunner $t) use ($root): void {
        $adminFiles = glob($root . '/admin/*.php') ?: [];
        $t->assertGreaterThan(10, count($adminFiles));

        foreach ($adminFiles as $file) {
            $base = basename($file);
            $t->test("syntax OK: admin/{$base}", function (TestRunner $t) use ($file, $base): void {
                $php = defined('TEST_PHP_BINARY') ? TEST_PHP_BINARY : 'php';
                $cmd = escapeshellarg($php) . ' -l ' . escapeshellarg($file) . ' 2>&1';
                $out = shell_exec($cmd) ?? '';
                $t->assertContains('No syntax errors', $out, "Syntax error in {$base}");
            });
        }
    });

    $t->group('Admin Assets', function (TestRunner $t) use ($root): void {
        $t->test('admin.css มีอยู่', function (TestRunner $t) use ($root): void {
            $t->assertTrue(is_file($root . '/assets/css/admin.css'));
        });
        $t->test('admin.js มีอยู่', function (TestRunner $t) use ($root): void {
            $t->assertTrue(is_file($root . '/assets/js/admin.js'));
        });
        $t->test('_menu.php มีครบ section', function (TestRunner $t) use ($root): void {
            $menu = require $root . '/admin/_menu.php';
            $labels = [];
            foreach ($menu as $group) {
                foreach ($group['items'] as $item) {
                    $labels[] = $item['file'];
                }
            }
            foreach (['site.php', 'homepage.php', 'catalog.php', 'plans.php', 'articles.php', 'page-edit.php', 'backup.php'] as $required) {
                $found = false;
                foreach ($labels as $l) {
                    if (str_contains($l, $required) || $l === $required) {
                        $found = true;
                        break;
                    }
                }
                $t->assertTrue($found, "Menu missing {$required}");
            }
        });
    });
};
