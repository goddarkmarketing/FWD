<?php
declare(strict_types=1);

return function (TestRunner $t): void {
    $root = dirname(__DIR__);
    $php = defined('TEST_PHP_BINARY') ? TEST_PHP_BINARY : 'php';
    require_once $root . '/tests/render-helper.php';

    $t->group('CLI scripts', function (TestRunner $t) use ($root, $php): void {
        $t->test('scripts/backup-cms.php รันได้', function (TestRunner $t) use ($root, $php): void {
            if (!class_exists('ZipArchive')) {
                $t->assertTrue(true, 'ZipArchive not available — skip');
                return;
            }
            $result = test_run_proc(
                [$php, $root . '/scripts/backup-cms.php', '--note=automated-test'],
                $root
            );
            $out = $result['combined'];
            $t->assertContains('Backup created:', $out, $out);

            if (preg_match('/fwd-backup-[\w\-]+\.zip/', $out, $m)) {
                require_once $root . '/includes/backup-manager.php';
                backup_delete($m[0]);
            }
        });

        $t->test('scripts/setup-cms.php มีอยู่และ syntax OK', function (TestRunner $t) use ($root, $php): void {
            $script = $root . '/scripts/setup-cms.php';
            $t->assertFileExists($script);
            $result = test_run_proc([$php, '-l', $script], $root);
            $t->assertContains('No syntax errors', $result['combined']);
        });

        $t->test('scripts/build-static.php syntax OK', function (TestRunner $t) use ($root, $php): void {
            $script = $root . '/scripts/build-static.php';
            $t->assertFileExists($script);
            $result = test_run_proc([$php, '-l', $script], $root);
            $t->assertContains('No syntax errors', $result['combined']);
        });
    });
};
