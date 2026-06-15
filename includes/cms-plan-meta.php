<?php
declare(strict_types=1);

function cms_meta_path(string $name): string
{
    return cms_root() . '/' . $name . '-meta.json';
}

function cms_load_meta(string $name, array $default = []): array
{
    $path = cms_meta_path($name);
    if (!is_readable($path)) {
        return $default;
    }

    $raw = file_get_contents($path);
    if ($raw === false || trim($raw) === '') {
        return $default;
    }

    $data = json_decode($raw, true);

    return is_array($data) ? array_merge($default, $data) : $default;
}

function cms_save_meta(string $name, array $data): bool
{
    return cms_save('_meta/' . $name, $data);
}

function cms_categories_meta(): array
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $cached = cms_load_meta('categories', ['hidden' => [], 'order' => []]);

    return $cached;
}

function cms_catalog_meta(): array
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $cached = cms_load_meta('catalog', ['hidden' => [], 'custom' => []]);

    return $cached;
}

function cms_plans_meta(): array
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $cached = cms_load_meta('plans', ['hidden' => []]);

    return $cached;
}

function cms_reset_categories_meta_cache(): void
{
    // Force reload on next read within same request after save.
    $GLOBALS['_cms_categories_meta'] = null;
}

function plan_category_defaults(): array
{
    static $defaults = null;
    if ($defaults === null) {
        $defaults = require __DIR__ . '/plan-categories.php';
    }

    return $defaults;
}

function plan_category_default_order(): array
{
    return ['all', 'life-accident', 'health', 'critical', 'investment', 'savings'];
}

/** @return list<string> */
function plan_catalog_builtin_slugs(): array
{
    static $slugs = null;
    if ($slugs !== null) {
        return $slugs;
    }

    $slugs = [];
    $definitions = require __DIR__ . '/plans-catalog-definitions.php';
    foreach ($definitions as $group) {
        foreach ($group['products'] ?? [] as $product) {
            if (!empty($product['slug'])) {
                $slugs[] = $product['slug'];
            }
        }
    }

    return $slugs;
}

function plan_catalog_is_builtin(string $slug): bool
{
    return in_array($slug, plan_catalog_builtin_slugs(), true);
}

function plan_category_is_builtin(string $id): bool
{
    return isset(plan_category_defaults()[$id]);
}

function plan_category_is_hidden(string $id): bool
{
    $hidden = cms_categories_meta()['hidden'] ?? [];

    return in_array($id, $hidden, true);
}

function plan_catalog_is_hidden(string $slug): bool
{
    $hidden = cms_catalog_meta()['hidden'] ?? [];

    return in_array($slug, $hidden, true);
}

function plan_detail_is_hidden(string $slug): bool
{
    if (plan_catalog_is_hidden($slug)) {
        return true;
    }

    $hidden = cms_plans_meta()['hidden'] ?? [];

    return in_array($slug, $hidden, true);
}

function plan_detail_is_builtin(string $slug): bool
{
    if (isset(cms_catalog_meta()['custom'][$slug])) {
        return false;
    }

    if (plan_catalog_is_builtin($slug)) {
        return true;
    }

    if (!function_exists('plan_catalog_by_slug')) {
        require_once __DIR__ . '/plan-helpers.php';
    }

    $planFile = cms_root() . '/plans/' . $slug . '.json';
    if (is_file($planFile) && plan_catalog_by_slug($slug) === null) {
        return false;
    }

    return true;
}

function cms_category_order_save(array $order): bool
{
    $meta = cms_categories_meta();
    $meta['order'] = array_values(array_unique($order));

    return cms_save_meta('categories', $meta);
}

function cms_category_hide(string $id): bool
{
    if ($id === 'all') {
        return false;
    }

    $meta = cms_categories_meta();
    $hidden = $meta['hidden'] ?? [];
    if (!in_array($id, $hidden, true)) {
        $hidden[] = $id;
    }
    $meta['hidden'] = $hidden;

    return cms_save_meta('categories', $meta);
}

function cms_category_unhide(string $id): bool
{
    $meta = cms_categories_meta();
    $hidden = array_values(array_filter(
        $meta['hidden'] ?? [],
        static fn(string $item): bool => $item !== $id
    ));
    $meta['hidden'] = $hidden;

    return cms_save_meta('categories', $meta);
}

function cms_category_delete(string $id): bool
{
    if ($id === 'all') {
        return false;
    }

    if (plan_category_is_builtin($id)) {
        return cms_category_hide($id);
    }

    $data = cms_load('categories', plan_category_defaults());
    unset($data[$id]);
    cms_save('categories', $data);

    $meta = cms_categories_meta();
    $meta['order'] = array_values(array_filter(
        $meta['order'] ?? plan_category_default_order(),
        static fn(string $item): bool => $item !== $id
    ));
    $meta['hidden'] = array_values(array_filter(
        $meta['hidden'] ?? [],
        static fn(string $item): bool => $item !== $id
    ));

    return cms_save_meta('categories', $meta);
}

function cms_plan_detail_hide(string $slug): bool
{
    $meta = cms_plans_meta();
    $hidden = $meta['hidden'] ?? [];
    if (!in_array($slug, $hidden, true)) {
        $hidden[] = $slug;
    }
    $meta['hidden'] = $hidden;

    return cms_save_meta('plans', $meta);
}

function cms_plan_detail_unhide(string $slug): bool
{
    $meta = cms_plans_meta();
    $hidden = array_values(array_filter(
        $meta['hidden'] ?? [],
        static fn(string $item): bool => $item !== $slug
    ));
    $meta['hidden'] = $hidden;

    return cms_save_meta('plans', $meta);
}

function cms_plan_detail_delete(string $slug): bool
{
    if (plan_detail_is_builtin($slug)) {
        cms_catalog_hide($slug);

        return cms_plan_detail_hide($slug);
    }

    cms_catalog_delete($slug);
    cms_plan_detail_unhide($slug);

    $planFile = cms_root() . '/plans/' . $slug . '.json';
    if (is_file($planFile)) {
        unlink($planFile);
    }

    return true;
}

function cms_catalog_hide(string $slug): bool
{
    $meta = cms_catalog_meta();
    $hidden = $meta['hidden'] ?? [];
    if (!in_array($slug, $hidden, true)) {
        $hidden[] = $slug;
    }
    $meta['hidden'] = $hidden;
    cms_save_meta('catalog', $meta);

    return cms_plan_detail_hide($slug);
}

function cms_catalog_unhide(string $slug): bool
{
    $meta = cms_catalog_meta();
    $hidden = array_values(array_filter(
        $meta['hidden'] ?? [],
        static fn(string $item): bool => $item !== $slug
    ));
    $meta['hidden'] = $hidden;
    cms_save_meta('catalog', $meta);

    return cms_plan_detail_unhide($slug);
}

function cms_catalog_delete(string $slug): bool
{
    if (plan_catalog_is_builtin($slug)) {
        return cms_catalog_hide($slug);
    }

    $meta = cms_catalog_meta();
    unset($meta['custom'][$slug]);
    $hidden = array_values(array_filter(
        $meta['hidden'] ?? [],
        static fn(string $item): bool => $item !== $slug
    ));
    $meta['hidden'] = $hidden;
    cms_save_meta('catalog', $meta);

    $overrides = cms_load('catalog', []);
    if (isset($overrides[$slug])) {
        unset($overrides[$slug]);
        cms_save('catalog', $overrides);
    }

    $planFile = cms_root() . '/plans/' . $slug . '.json';
    if (is_file($planFile)) {
        unlink($planFile);
    }

    return true;
}
