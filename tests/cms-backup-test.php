<?php
declare(strict_types=1);

return function (TestRunner $t): void {
    $root = dirname(__DIR__);

    $t->group('Backup & Restore', function (TestRunner $t) use ($root): void {
        require_once $root . '/includes/cms-loader.php';
        require_once $root . '/includes/backup-manager.php';

        $t->test('backup_root และ backup_project_root', function (TestRunner $t): void {
            $t->assertDirectoryExists(backup_root());
            $projectRoot = strtolower(str_replace('\\', '/', backup_project_root()));
            $t->assertTrue(str_ends_with($projectRoot, '/fwd'), 'backup_project_root should end with /fwd');
        });

        $t->test('backup_customer_asset_dirs()', function (TestRunner $t): void {
            $dirs = backup_customer_asset_dirs();
            $t->assertCount(4, $dirs);
            $t->assertInArray('assets/cover', $dirs);
            $t->assertInArray('assets/uploads', $dirs);
        });

        $t->test('backup_ensure_dir() สร้าง .htaccess', function (TestRunner $t): void {
            $t->assertTrue(backup_ensure_dir());
            $t->assertFileExists(backup_root() . '/.htaccess');
        });

        $t->test('backup_format_datetime() ใช้เวลาไทย', function (TestRunner $t): void {
            $formatted = backup_format_datetime('2026-06-09T05:58:00+00:00');
            $t->assertContains('12:58', $formatted);
            $t->assertContains('น.', $formatted);

            $fromFile = backup_format_datetime(null, null, 'fwd-backup-20260609-143022.zip');
            $t->assertContains('14:30', $fromFile);
        });

        $t->test('backup_timestamp_slug() รูปแบบถูกต้อง', function (TestRunner $t): void {
            $slug = backup_timestamp_slug();
            $t->assertTrue((bool) preg_match('/^\d{8}-\d{6}$/', $slug));
        });

        $t->test('backup_format_size()', function (TestRunner $t): void {
            $t->assertEquals('512 B', backup_format_size(512));
            $t->assertContains('KB', backup_format_size(2048));
            $t->assertContains('MB', backup_format_size(2 * 1024 * 1024));
        });

        $t->test('backup_safe_rel_path()', function (TestRunner $t): void {
            $t->assertEquals('cms/site.json', backup_safe_rel_path('cms/site.json'));
            $t->assertNull(backup_safe_rel_path('../evil'));
            $t->assertNull(backup_safe_rel_path(''));
        });

        $t->test('backup_manifest()', function (TestRunner $t): void {
            $m = backup_manifest(['note' => 'test']);
            $t->assertEquals(BACKUP_FORMAT_VERSION, $m['format_version']);
            $t->assertEquals('test', $m['note']);
            $t->assertTrue(isset($m['includes']));
        });

        $t->test('backup_collect_files มี cms และ assets', function (TestRunner $t): void {
            $files = backup_collect_files();
            $t->assertGreaterThan(0, count($files));
            $hasCms = false;
            foreach (array_keys($files) as $rel) {
                if (str_starts_with($rel, 'cms/')) {
                    $hasCms = true;
                    break;
                }
            }
            $t->assertTrue($hasCms, 'ควรมีไฟล์ใน cms/');
        });

        if (!class_exists('ZipArchive')) {
            $t->test('ZipArchive ไม่พร้อม — ข้าม create/restore', function (TestRunner $t): void {
                $t->assertTrue(true);
            });
            return;
        }

        $t->test('backup_create + validate + list + delete', function (TestRunner $t): void {
            $name = 'fwd-backup-test-' . uniqid('', true) . '.zip';
            $result = backup_create('phpunit-test', $name);
            $t->assertTrue($result['ok'] ?? false, $result['error'] ?? 'create failed');
            $t->assertFileExists($result['path']);

            $valid = backup_validate_zip($result['path']);
            $t->assertTrue($valid['ok'] ?? false, $valid['error'] ?? 'validate failed');
            $t->assertEquals(BACKUP_FORMAT_VERSION, (int) ($valid['manifest']['format_version'] ?? 0));

            $listed = backup_list();
            $found = false;
            foreach ($listed as $row) {
                if (($row['filename'] ?? '') === $name) {
                    $found = true;
                    break;
                }
            }
            $t->assertTrue($found, 'backup_list should include new file');

            $del = backup_delete($name);
            $t->assertTrue($del['ok'] ?? false);
            $t->assertFalse(is_file($result['path']));
        });

        $t->test('backup_target_abs ป้องกัน path traversal', function (TestRunner $t): void {
            $t->assertNotNull(backup_target_abs('cms/site.json'));
            $t->assertNotNull(backup_target_abs('assets/uploads/test.jpg'));
            $t->assertNull(backup_target_abs('../etc/passwd'));
            $t->assertNull(backup_target_abs('cms/../../../etc/passwd'));
            $t->assertNull(backup_target_abs('scripts/evil.php'));
            $t->assertNull(backup_target_abs('manifest.json'));
        });

        $t->test('backup_is_valid_filename', function (TestRunner $t): void {
            $t->assertTrue(backup_is_valid_filename('fwd-backup-20260101-120000.zip'));
            $t->assertFalse(backup_is_valid_filename('../evil.zip'));
            $t->assertFalse(backup_is_valid_filename('not-a-backup.zip'));
        });

        $t->test('backup_restore roundtrip กู้คืนไฟล์ CMS', function (TestRunner $t) use ($root): void {
            $testKey = 'pages/_backup-restore-test';
            $original = ['hero_title' => 'ORIGINAL ' . time(), 'hero_lead' => 'lead'];
            $modified = ['hero_title' => 'MODIFIED', 'hero_lead' => 'changed'];

            cms_save($testKey, $original);

            $zipName = 'fwd-backup-restore-' . uniqid('', true) . '.zip';
            $created = backup_create('restore-test', $zipName);
            $t->assertTrue($created['ok'] ?? false, $created['error'] ?? '');

            cms_save($testKey, $modified);
            $afterModify = cms_load($testKey);
            $t->assertEquals('MODIFIED', $afterModify['hero_title']);

            $restored = backup_restore($created['path'], false);
            $t->assertTrue($restored['ok'] ?? false, $restored['error'] ?? 'restore failed');
            $t->assertGreaterThan(0, (int) ($restored['restored'] ?? 0));

            $afterRestore = cms_load($testKey);
            $t->assertEquals($original['hero_title'], $afterRestore['hero_title']);

            @unlink(cms_file_path($testKey));
            backup_delete($zipName);
        });

        $t->test('backup_validate_zip ปฏิเสธไฟล์เสีย', function (TestRunner $t): void {
            $bad = backup_root() . '/fwd-backup-invalid-test.zip';
            file_put_contents($bad, 'not a zip');
            $valid = backup_validate_zip($bad);
            $t->assertFalse($valid['ok'] ?? true);
            @unlink($bad);
        });

        $t->test('backup_delete ปฏิเสธชื่อไม่ถูกต้อง', function (TestRunner $t): void {
            $del = backup_delete('../../../evil.zip');
            $t->assertFalse($del['ok'] ?? true);
        });
    });
};
