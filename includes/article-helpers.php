<?php
declare(strict_types=1);

/**
 * บทความ — normalize, SEO, sanitize HTML
 */

function article_normalize(array $article): array
{
    $article['slug'] = trim((string) ($article['slug'] ?? ''));
    $article['title'] = trim((string) ($article['title'] ?? ''));
    $article['excerpt'] = trim((string) ($article['excerpt'] ?? ''));
    $article['category'] = trim((string) ($article['category'] ?? ''));
    $article['date'] = trim((string) ($article['date'] ?? ''));
    $article['read_min'] = max(1, (int) ($article['read_min'] ?? 5));
    $article['image'] = trim((string) ($article['image'] ?? ''));
    $article['image_alt'] = trim((string) ($article['image_alt'] ?? ''));
    $article['content'] = (string) ($article['content'] ?? '');
    $article['meta_title'] = trim((string) ($article['meta_title'] ?? ''));
    $article['meta_description'] = trim((string) ($article['meta_description'] ?? ''));
    $article['focus_keyword'] = trim((string) ($article['focus_keyword'] ?? ''));
    $article['og_image'] = trim((string) ($article['og_image'] ?? ''));
    $article['canonical'] = trim((string) ($article['canonical'] ?? ''));
    $article['noindex'] = !empty($article['noindex']);

    if ($article['content'] === '' && !empty($article['body']) && is_array($article['body'])) {
        $parts = [];
        foreach ($article['body'] as $paragraph) {
            $paragraph = trim((string) $paragraph);
            if ($paragraph !== '') {
                $parts[] = '<p>' . htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8') . '</p>';
            }
        }
        $article['content'] = implode("\n", $parts);
    }

    if ($article['image_alt'] === '' && $article['title'] !== '') {
        $article['image_alt'] = $article['title'];
    }

    return $article;
}

function article_slugify(string $title): string
{
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9\-]+/', '-', $slug) ?? '';
    $slug = trim($slug, '-');

    return $slug !== '' ? $slug : 'article-' . date('YmdHis');
}

function article_sanitize_html(string $html): string
{
    $html = trim($html);
    if ($html === '') {
        return '';
    }

    $allowed = '<p><br><h2><h3><h4><strong><b><em><i><u><s><ul><ol><li><a><img><blockquote><pre><code><span>';
    $html = strip_tags($html, $allowed);
    $html = preg_replace('/<a\s+([^>]*?)href\s*=\s*["\']\s*javascript:[^"\']*["\']([^>]*)>/i', '<a$1$2>', $html) ?? $html;
    $html = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html) ?? $html;

    return trim($html);
}

function article_seo_title(array $article): string
{
    $article = article_normalize($article);
    if ($article['meta_title'] !== '') {
        return $article['meta_title'];
    }

    return $article['title'];
}

function article_seo_description(array $article): string
{
    $article = article_normalize($article);
    if ($article['meta_description'] !== '') {
        return $article['meta_description'];
    }

    if ($article['excerpt'] !== '') {
        return $article['excerpt'];
    }

    return mb_substr(strip_tags($article['content']), 0, 160);
}

function article_og_image(array $article): string
{
    $article = article_normalize($article);
    $path = $article['og_image'] !== '' ? $article['og_image'] : $article['image'];

    return $path;
}

function article_canonical_url(array $article): string
{
    $article = article_normalize($article);
    if ($article['canonical'] !== '') {
        return $article['canonical'];
    }

    require_once __DIR__ . '/articles-data.php';
    return article_url($article['slug']);
}

function article_robots_meta(array $article): string
{
    return !empty($article['noindex']) ? 'noindex, nofollow' : 'index, follow';
}

function article_reading_time(array $article): int
{
    $article = article_normalize($article);
    if ($article['read_min'] > 0) {
        return $article['read_min'];
    }

    $words = preg_split('/\s+/u', strip_tags($article['content']), -1, PREG_SPLIT_NO_EMPTY);
    $count = is_array($words) ? count($words) : 0;

    return max(1, (int) ceil($count / 200));
}

function article_render_content(array $article): string
{
    $article = article_normalize($article);
    $html = article_sanitize_html($article['content']);

    if ($html === '' && !empty($article['body']) && is_array($article['body'])) {
        $out = '';
        foreach ($article['body'] as $paragraph) {
            $paragraph = trim((string) $paragraph);
            if ($paragraph !== '') {
                $out .= '<p>' . htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8') . '</p>';
            }
        }
        return $out;
    }

    return $html;
}

function article_schema_json(array $article): string
{
    $article = article_normalize($article);
    require_once __DIR__ . '/config.php';

    $data = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $article['title'],
        'description' => article_seo_description($article),
        'image' => image_url(article_og_image($article)),
        'datePublished' => $article['date'],
        'author' => [
            '@type' => 'Organization',
            'name' => SITE_NAME,
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => SITE_NAME,
        ],
        'mainEntityOfPage' => article_canonical_url($article),
    ];

    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
}

function article_seo_score(array $article): array
{
    $article = article_normalize($article);
    $checks = [];
    $score = 0;
    $max = 0;

    $add = function (string $label, bool $ok, string $hint) use (&$checks, &$score, &$max): void {
        $max += 10;
        if ($ok) {
            $score += 10;
        }
        $checks[] = ['label' => $label, 'ok' => $ok, 'hint' => $hint];
    };

    $titleLen = mb_strlen(article_seo_title($article));
    $add('หัวข้อ SEO', $titleLen >= 30 && $titleLen <= 60, 'แนะนำ 30–60 ตัวอักษร (ปัจจุบัน ' . $titleLen . ')');

    $descLen = mb_strlen(article_seo_description($article));
    $add('Meta description', $descLen >= 120 && $descLen <= 160, 'แนะนำ 120–160 ตัวอักษร (ปัจจุบัน ' . $descLen . ')');

    $add('คำสำคัญหลัก', $article['focus_keyword'] !== '', 'ระบุ Focus keyword เพื่อวางแผนเนื้อหา');

    $kw = $article['focus_keyword'];
    if ($kw !== '') {
        $hay = mb_strtolower(strip_tags($article['content']) . ' ' . $article['title']);
        $add('คำสำคัญในเนื้อหา', str_contains($hay, mb_strtolower($kw)), 'ใช้คำสำคัญในเนื้อหาหรือหัวข้อ');
    }

    $add('รูปปก + Alt', $article['image'] !== '' && $article['image_alt'] !== '', 'ใส่รูปและข้อความ alt');

    $add('Slug URL', $article['slug'] !== '' && strlen($article['slug']) <= 60, 'ใช้ slug สั้น อ่านง่าย');

    $pct = $max > 0 ? (int) round(($score / $max) * 100) : 0;

    return ['percent' => $pct, 'checks' => $checks];
}
