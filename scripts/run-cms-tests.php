<?php
/**
 * รันทดสอบ CMS ทุกส่วน
 *
 * Usage: php scripts/run-cms-tests.php
 */
declare(strict_types=1);

$phpBin = (defined('PHP_BINARY') && PHP_BINARY !== '') ? PHP_BINARY : 'php';
if (!defined('TEST_PHP_BINARY')) {
    define('TEST_PHP_BINARY', $phpBin);
}

$root = dirname(__DIR__);
chdir($root);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require $root . '/tests/TestRunner.php';

$runner = new TestRunner();

$suites = [
    $root . '/tests/cms-loader-test.php',
    $root . '/tests/cms-data-test.php',
    $root . '/tests/cms-config-test.php',
    $root . '/tests/cms-plan-test.php',
    $root . '/tests/cms-content-test.php',
    $root . '/tests/cms-article-test.php',
    $root . '/tests/cms-article-admin-test.php',
    $root . '/tests/cms-frontend-test.php',
    $root . '/tests/cms-admin-test.php',
    $root . '/tests/cms-backup-test.php',
    $root . '/tests/cms-scripts-test.php',
];

echo "FWD CMS Test Suite\n";

foreach ($suites as $suiteFile) {
    if (!is_readable($suiteFile)) {
        echo "Missing suite: {$suiteFile}\n";
        exit(1);
    }
    $fn = require $suiteFile;
    if (!is_callable($fn)) {
        echo "Invalid suite: {$suiteFile}\n";
        exit(1);
    }
    $fn($runner);
}

$runner->summary();
exit($runner->exitCode());
