<?php
declare(strict_types=1);

final class TestRunner
{
    private int $passed = 0;
    private int $failed = 0;

    /** @var list<array{group: string, name: string, message: string}> */
    private array $failures = [];

    public function group(string $title, callable $fn): void
    {
        echo "\n=== {$title} ===\n";
        $fn($this);
    }

    public function test(string $name, callable $fn): void
    {
        try {
            $fn($this);
            $this->passed++;
            echo "  ✓ {$name}\n";
        } catch (Throwable $e) {
            $this->failed++;
            $this->failures[] = ['group' => '', 'name' => $name, 'message' => $e->getMessage()];
            echo "  ✗ {$name}\n    → {$e->getMessage()}\n";
        }
    }

    public function assertTrue(mixed $condition, string $message = 'Expected true'): void
    {
        if (!$condition) {
            throw new RuntimeException($message);
        }
    }

    public function assertFalse(mixed $condition, string $message = 'Expected false'): void
    {
        if ($condition) {
            throw new RuntimeException($message);
        }
    }

    public function assertEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            $msg = $message !== '' ? $message : 'Values are not equal';
            throw new RuntimeException($msg . ' (expected ' . $this->repr($expected) . ', got ' . $this->repr($actual) . ')');
        }
    }

    public function assertNotEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected === $actual) {
            throw new RuntimeException($message !== '' ? $message : 'Values should differ');
        }
    }

    public function assertNotEmpty(mixed $value, string $message = 'Expected non-empty value'): void
    {
        if ($value === null || $value === '' || $value === []) {
            throw new RuntimeException($message);
        }
    }

    /** @param list<string> $keys */
    public function assertArrayHasKeys(array $keys, array $array, string $context = ''): void
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                $prefix = $context !== '' ? "{$context}: " : '';
                throw new RuntimeException($prefix . "Missing key '{$key}'");
            }
        }
    }

    public function assertContains(string $needle, string $haystack, string $message = ''): void
    {
        if (!str_contains($haystack, $needle)) {
            throw new RuntimeException($message !== '' ? $message : "String does not contain: {$needle}");
        }
    }

    public function assertGreaterThan(int $min, int $actual, string $message = ''): void
    {
        if ($actual <= $min) {
            throw new RuntimeException($message !== '' ? $message : "Expected > {$min}, got {$actual}");
        }
    }

    public function assertLessThan(int $max, int $actual, string $message = ''): void
    {
        if ($actual >= $max) {
            throw new RuntimeException($message !== '' ? $message : "Expected < {$max}, got {$actual}");
        }
    }

    public function assertCount(int $expected, array $array, string $message = ''): void
    {
        $actual = count($array);
        if ($actual !== $expected) {
            throw new RuntimeException($message !== '' ? $message : "Expected count {$expected}, got {$actual}");
        }
    }

    public function assertNull(mixed $value, string $message = 'Expected null'): void
    {
        if ($value !== null) {
            throw new RuntimeException($message);
        }
    }

    public function assertNotNull(mixed $value, string $message = 'Expected non-null'): void
    {
        if ($value === null) {
            throw new RuntimeException($message);
        }
    }

    public function assertInArray(mixed $needle, array $haystack, string $message = ''): void
    {
        if (!in_array($needle, $haystack, true)) {
            throw new RuntimeException($message !== '' ? $message : 'Value not in array');
        }
    }

    public function assertFileExists(string $path, string $message = ''): void
    {
        if (!is_file($path)) {
            throw new RuntimeException($message !== '' ? $message : "File not found: {$path}");
        }
    }

    public function assertDirectoryExists(string $path, string $message = ''): void
    {
        if (!is_dir($path)) {
            throw new RuntimeException($message !== '' ? $message : "Directory not found: {$path}");
        }
    }

    public function exitCode(): int
    {
        return $this->failed > 0 ? 1 : 0;
    }

    public function summary(): void
    {
        $total = $this->passed + $this->failed;
        echo "\n----------------------------------------\n";
        echo "ผลทดสอบ: {$this->passed}/{$total} ผ่าน";
        if ($this->failed > 0) {
            echo ", {$this->failed} ล้มเหลว\n";
            foreach ($this->failures as $f) {
                echo "  - {$f['name']}: {$f['message']}\n";
            }
        } else {
            echo " — ทั้งหมดผ่าน\n";
        }
    }

    private function repr(mixed $value): string
    {
        if (is_string($value)) {
            return '"' . mb_substr($value, 0, 60) . '"';
        }
        return var_export($value, true);
    }
}
