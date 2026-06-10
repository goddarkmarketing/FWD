<?php
declare(strict_types=1);

/**
 * Render PHP pages for tests without shell quoting issues (Windows-safe).
 */
function test_run_proc(array $cmd, string $cwd): array
{
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $process = proc_open($cmd, $descriptors, $pipes, $cwd);
    if (!is_resource($process)) {
        return ['exit' => 1, 'stdout' => '', 'stderr' => '', 'combined' => ''];
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    $stdout = is_string($stdout) ? $stdout : '';
    $stderr = is_string($stderr) ? $stderr : '';
    $combined = trim($stdout . ($stderr !== '' ? "\n" . $stderr : ''));

    return [
        'exit' => $exitCode,
        'stdout' => $stdout,
        'stderr' => $stderr,
        'combined' => $combined,
    ];
}

function test_render_proc(array $cmd, string $cwd): string
{
    $result = test_run_proc($cmd, $cwd);
    if ($result['exit'] !== 0) {
        return $result['stderr'];
    }

    return $result['stdout'];
}

function test_render_admin_page(string $root, string $file, array $get = []): string
{
    $php = defined('TEST_PHP_BINARY') ? TEST_PHP_BINARY : PHP_BINARY;
    $queryJson = json_encode($get, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    return test_render_proc(
        [$php, $root . '/scripts/render-admin-page.php', $file, $queryJson],
        $root
    );
}

function test_render_frontend(string $root, string $phpFile, array $get = [], string $baseUrl = '/fwd'): string
{
    $php = defined('TEST_PHP_BINARY') ? TEST_PHP_BINARY : PHP_BINARY;
    $queryJson = json_encode($get, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    return test_render_proc(
        [$php, $root . '/scripts/render-one.php', $baseUrl, $phpFile, basename($phpFile), $queryJson],
        $root
    );
}
