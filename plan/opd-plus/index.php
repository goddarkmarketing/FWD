<?php
declare(strict_types=1);
$slug = basename(dirname(__FILE__));
$_GET['slug'] = $slug;
$script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/plan.php'));
$rootScript = dirname(dirname(dirname($script))) . '/plan.php';
$_SERVER['SCRIPT_NAME'] = $rootScript;
$_SERVER['PHP_SELF'] = $rootScript;
require dirname(__DIR__, 2) . '/plan.php';