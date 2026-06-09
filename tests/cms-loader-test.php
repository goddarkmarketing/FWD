<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/cms-loader.php';

return function (TestRunner $t): void {
    $t->group('CMS Loader', function (TestRunner $t): void {
        $t->test('cms_root ชี้ไป data/cms', function (TestRunner $t): void {
            $root = cms_root();
            $t->assertTrue(is_dir($root), 'cms_root is not a directory');
            $t->assertTrue(str_ends_with(str_replace('\\', '/', $root), '/data/cms'));
        });

        $t->test('cms_file_path แมป key ถูกต้อง', function (TestRunner $t): void {
            $t->assertContains('site.json', cms_file_path('site'));
            $t->assertContains('pages/about.json', cms_file_path('pages/about'));
            $t->assertContains('plans/precious-care.json', cms_file_path('plans/precious-care'));
            $t->assertContains('catalog-overrides.json', cms_file_path('catalog'));
        });

        $t->test('cms_load อ่าน site.json ได้', function (TestRunner $t): void {
            $site = cms_load('site');
            $t->assertTrue(is_array($site));
            $t->assertArrayHasKeys(['site_name', 'contact_email'], $site);
        });

        $t->test('cms_get อ่าน field เดี่ยว', function (TestRunner $t): void {
            $name = cms_get('site', 'site_name', '');
            $t->assertNotEmpty($name);
            $t->assertEquals('FWD AGENT ประเทศไทย', $name);
        });

        $t->test('cms_merge_defaults รวม override', function (TestRunner $t): void {
            $merged = cms_merge_defaults(['a' => 1, 'b' => ['x' => 1]], ['b' => ['y' => 2]]);
            $t->assertEquals(1, $merged['a']);
            $t->assertEquals(1, $merged['b']['x']);
            $t->assertEquals(2, $merged['b']['y']);
        });

        $t->test('cms_save + cms_load roundtrip', function (TestRunner $t): void {
            $key = 'pages/_test-runner';
            $payload = ['_test' => true, 'ts' => time(), 'thai' => 'ทดสอบ'];
            $t->assertTrue(cms_save($key, $payload));
            $loaded = cms_load($key);
            $t->assertEquals($payload, $loaded);
            @unlink(cms_file_path($key));
        });

        $t->test('cms_page โหลด about', function (TestRunner $t): void {
            $page = cms_page('about', ['hero_title' => 'fallback']);
            $t->assertNotEmpty($page['hero_title'] ?? '');
            $t->assertNotEquals('fallback', $page['hero_title']);
        });

        $t->test('cms_plan_slugs มีอย่างน้อย 37 แผน', function (TestRunner $t): void {
            $slugs = cms_plan_slugs();
            $t->assertGreaterThan(36, count($slugs));
            $t->assertTrue(in_array('precious-care', $slugs, true));
        });

        $t->test('cms_load คืน default เมื่อไม่มีไฟล์', function (TestRunner $t): void {
            $default = ['fallback' => true];
            $loaded = cms_load('pages/_nonexistent-file-xyz', $default);
            $t->assertEquals($default, $loaded);
        });

        $t->test('cms_merge_defaults null overrides', function (TestRunner $t): void {
            $defaults = ['a' => 1];
            $t->assertEquals($defaults, cms_merge_defaults($defaults, null));
        });

        $t->test('cms_plan_override ว่างเมื่อไม่มีไฟล์', function (TestRunner $t): void {
            $t->assertEquals([], cms_plan_override('_nonexistent-plan-xyz'));
        });

        $t->test('cms_save สร้าง nested directory', function (TestRunner $t): void {
            $key = 'plans/_test-runner-plan';
            $payload = ['tagline' => 'ทดสอบ nested'];
            $t->assertTrue(cms_save($key, $payload));
            $t->assertEquals($payload, cms_load($key));
            @unlink(cms_file_path($key));
        });

        $t->test('cms_page merge defaults', function (TestRunner $t): void {
            $page = cms_page('_fake-page', ['hero_title' => 'DEFAULT TITLE']);
            $t->assertEquals('DEFAULT TITLE', $page['hero_title']);
        });

        $t->test('cms_upload คืน null เมื่อไม่มีไฟล์', function (TestRunner $t): void {
            unset($_FILES['missing_upload_field']);
            $t->assertNull(cms_upload('missing_upload_field'));
        });
    });
};
