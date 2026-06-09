<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/cms-loader.php';

return function (TestRunner $t): void {
    $t->group('CMS Data Files', function (TestRunner $t): void {
        $t->test('site.json ครบฟิลด์หลัก', function (TestRunner $t): void {
            $d = cms_load('site');
            $t->assertArrayHasKeys([
                'site_name', 'site_tagline', 'site_phone', 'contact_email',
                'contact_phone_1', 'agent_office_name', 'agent_license_no', 'hero_alt',
            ], $d ?? []);
        });

        $t->test('homepage.json ครบ section', function (TestRunner $t): void {
            $d = cms_load('homepage');
            $t->assertArrayHasKeys([
                'plans_section', 'plan_filters', 'consultation', 'why_fwd',
                'reviews', 'promos_section', 'articles_section',
            ], $d ?? []);
            $t->assertGreaterThan(0, count($d['reviews']['items'] ?? []));
            $t->assertGreaterThan(3, count($d['why_fwd']['cards'] ?? []));
        });

        $t->test('footer.json ครบฟิลด์', function (TestRunner $t): void {
            $d = cms_load('footer');
            $t->assertArrayHasKeys(['cta_title', 'cta_desc', 'copyright', 'cookie_text'], $d ?? []);
        });

        $t->test('categories.json มี 6 หมวด', function (TestRunner $t): void {
            $d = cms_load('categories');
            $t->assertEquals(6, count($d ?? []));
            foreach (['all', 'health', 'critical', 'life-accident', 'investment', 'savings'] as $id) {
                $t->assertTrue(isset($d[$id]), "Missing category {$id}");
                $t->assertArrayHasKeys(['title', 'lead', 'page'], $d[$id]);
            }
        });

        $t->test('articles.json มี items ถูกต้อง', function (TestRunner $t): void {
            $d = cms_load('articles');
            $items = $d['items'] ?? [];
            $t->assertGreaterThan(0, count($items));
            foreach ($items as $i => $article) {
                $t->assertArrayHasKeys(['slug', 'title', 'excerpt'], $article, "article[{$i}]");
                $hasBody = isset($article['body']) && is_array($article['body']) && $article['body'] !== [];
                $hasContent = isset($article['content']) && trim((string) $article['content']) !== '';
                $t->assertTrue($hasBody || $hasContent, "article[{$i}]: ต้องมี body หรือ content");
            }
        });

        $t->test('promotions.json มี items + home_count', function (TestRunner $t): void {
            $d = cms_load('promotions');
            $t->assertGreaterThan(0, count($d['items'] ?? []));
            $t->assertTrue(($d['home_count'] ?? 0) >= 1);
        });

        $t->test('catalog-overrides.json เป็น object/array', function (TestRunner $t): void {
            $path = cms_file_path('catalog');
            if (!is_readable($path)) {
                cms_save('catalog', []);
            }
            $d = cms_load('catalog', []);
            $t->assertTrue(is_array($d));
        });

        foreach (['about', 'claims', 'contact', 'agent-apply'] as $pageId) {
            $t->test("pages/{$pageId}.json มี hero", function (TestRunner $t) use ($pageId): void {
                $d = cms_load('pages/' . $pageId);
                $t->assertArrayHasKeys(['hero_title', 'hero_lead', 'page_title'], $d ?? [], $pageId);
            });
        }

        $t->test('auth.json ใช้ hash ไม่เก็บรหัส plain', function (TestRunner $t): void {
            $auth = cms_load('auth');
            if (!is_array($auth)) {
                return; // gitignored on some machines — skip
            }
            $t->assertArrayHasKeys(['email', 'password_hash'], $auth);
            $t->assertFalse(isset($auth['password']));
            $t->assertContains('$2y$', $auth['password_hash']);
        });
    });
};
