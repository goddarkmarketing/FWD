<?php
declare(strict_types=1);

return function (TestRunner $t): void {
    $root = dirname(__DIR__);

    $t->group('Plan helpers', function (TestRunner $t) use ($root): void {
        putenv('FWD_BASE_URL=/fwd');
        $_SERVER['SCRIPT_NAME'] = '/fwd/index.php';

        require_once $root . '/includes/config.php';
        require_once $root . '/includes/plan-helpers.php';

        $t->test('plan_catalog() มี slug ครบ', function (TestRunner $t): void {
            $catalog = plan_catalog();
            $t->assertEquals(37, count($catalog));
            foreach ($catalog as $item) {
                $t->assertArrayHasKeys(['slug', 'title', 'category', 'image'], $item);
            }
        });

        $t->test('plan_catalog_by_slug()', function (TestRunner $t): void {
            $item = plan_catalog_by_slug('precious-care');
            $t->assertNotNull($item);
            $t->assertEquals('precious-care', $item['slug']);
            $t->assertNull(plan_catalog_by_slug('nonexistent-slug-xyz'));
        });

        $t->test('plan_categories() 6 หมวด', function (TestRunner $t): void {
            $cats = plan_categories();
            $t->assertCount(6, $cats);
            $t->assertInArray('all', array_keys($cats));
        });

        $t->test('plan_category_menu_order()', function (TestRunner $t): void {
            $order = plan_category_menu_order();
            $t->assertCount(6, $order);
            $t->assertEquals('all', $order[0]);
        });

        $t->test('plans_by_category()', function (TestRunner $t): void {
            $all = plans_by_category('all');
            $health = plans_by_category('health');
            $t->assertEquals(37, count($all));
            $t->assertGreaterThan(0, count($health));
            $t->assertLessThan(37, count($health));
            foreach ($health as $plan) {
                $t->assertEquals('health', $plan['category']);
            }
        });

        $t->test('plan_category_page_url()', function (TestRunner $t): void {
            $t->assertContains('health-insurance.php', plan_category_page_url('health'));
            $t->assertContains('products.php', plan_category_page_url('all'));
        });

        $t->test('plan_url() และ plan_contact_url()', function (TestRunner $t): void {
            $url = plan_url('easy-e-health');
            $t->assertContains('plan.php', $url);
            $t->assertContains('easy-e-health', $url);
            $contact = plan_contact_url('easy-e-health', 'Easy E-Health');
            $t->assertContains('contact.php', $contact);
            $t->assertContains('plan=', $contact);
        });

        $t->test('plan_category_url() legacy mapping', function (TestRunner $t): void {
            $t->assertContains('savings', plan_category_url('savings-pension'));
            $t->assertContains('products.php', plan_category_url('all'));
        });

        $t->test('plan_default_application()', function (TestRunner $t): void {
            $online = plan_default_application(true);
            $offline = plan_default_application(false);
            $t->assertCount(3, $online);
            $t->assertCount(3, $offline);
            $t->assertArrayHasKeys(['icon', 'title', 'desc'], $online[0]);
        });

        $t->test('plan_detail() ทุก slug ในแคตตาล็อก', function (TestRunner $t): void {
            foreach (plan_catalog() as $item) {
                $slug = $item['slug'];
                $detail = plan_detail($slug);
                $t->assertNotNull($detail, "plan_detail missing: {$slug}");
                $t->assertNotEmpty($detail['title'] ?? '', $slug);
            }
        });

        $t->test('plan_detail_from_catalog() stub', function (TestRunner $t): void {
            $stub = plan_detail_from_catalog('precious-care');
            $t->assertNotNull($stub);
            $t->assertTrue($stub['no_calculator'] ?? false);
            $t->assertGreaterThan(0, count($stub['highlights'] ?? []));
        });
    });

    $t->group('Plans data builders', function (TestRunner $t) use ($root): void {
        require_once $root . '/includes/plans-data.php';

        $t->test('plan_mock_image_path()', function (TestRunner $t): void {
            $path = plan_mock_image_path('health', 1);
            $t->assertContains('product-mock.php', $path);
            $t->assertContains('health', $path);
        });

        $t->test('plan_products2_images() ไม่ว่าง', function (TestRunner $t): void {
            $images = plan_products2_images();
            $t->assertGreaterThan(0, count($images));
        });

        $t->test('plan_default_tags()', function (TestRunner $t): void {
            $tags = plan_default_tags('health', 'easy-e-health');
            $t->assertTrue(is_array($tags));
            $t->assertGreaterThan(0, count($tags));
        });

        $t->test('plans_build_catalog() รวม override', function (TestRunner $t): void {
            $catalog = plans_build_catalog();
            $t->assertEquals(37, count($catalog));
        });
    });
};
