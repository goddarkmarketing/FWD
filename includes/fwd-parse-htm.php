<?php
/**
 * Parse saved FWD product .htm → plan detail array.
 */

function fwd_strip_html(?string $html): string
{
    if ($html === null || $html === '') {
        return '';
    }
    $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
    $html = preg_replace('/<\/p>/i', "\n", $html);
    $html = preg_replace('/<\/li>/i', "\n", $html);
    $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace("/[ \t]+/u", ' ', $text);
    $text = preg_replace("/\n{3,}/u", "\n\n", $text);
    return trim($text);
}

function fwd_clean_faq_q(string $q): string
{
    return preg_replace('/^Q:\s*/u', '', trim($q));
}

function fwd_clean_faq_a(string $a): string
{
    return preg_replace('/^A:\s*/u', '', trim($a));
}

function fwd_html_to_condition_list(string $html): array
{
    $items = [];
    if (preg_match_all('/<p[^>]*>(.*?)<\/p>/uis', $html, $m)) {
        foreach ($m[1] as $p) {
            $line = fwd_strip_html($p);
            if ($line !== '' && mb_strlen($line) > 3) {
                $items[] = $line;
            }
        }
    }
    if ($items === []) {
        $plain = fwd_strip_html($html);
        if ($plain !== '') {
            foreach (preg_split("/\n+/u", $plain) as $line) {
                $line = trim($line);
                if ($line !== '') {
                    $items[] = $line;
                }
            }
        }
    }
    return $items;
}

function fwd_extract_next_data(string $html): ?array
{
    $needle = 'id="__NEXT_DATA__"';
    $pos = strpos($html, $needle);
    if ($pos === false) {
        return null;
    }
    $start = strpos($html, '>', $pos);
    if ($start === false) {
        return null;
    }
    $start++;
    $end = strpos($html, '</script>', $start);
    if ($end === false) {
        return null;
    }
    $json = substr($html, $start, $end - $start);
    $data = json_decode($json, true);
    return is_array($data) ? $data : null;
}

/**
 * Brochure PDF from product page __NEXT_DATA__ (call_to_action_links only, not related products).
 *
 * @return array{url:string,path:string,filename:?string,score:int}|null
 */
function fwd_extract_brochure_from_next_data(?array $data, string $slug = ''): ?array
{
    if ($data === null) {
        return null;
    }

    $candidates = [];
    $walk = static function ($node, string $path = '') use (&$walk, &$candidates): void {
        if (!is_array($node)) {
            return;
        }
        foreach ($node as $key => $value) {
            $childPath = $path === '' ? (string) $key : $path . '.' . $key;
            if ($key === 'url' && is_string($value) && preg_match('/\.pdf$/i', $value)
                && str_contains($childPath, 'call_to_action_links')
                && str_contains($childPath, '.file.url')
                && !preg_match('/relatedProducts|recommended_products/i', $childPath)) {
                $candidates[] = ['url' => $value, 'path' => $childPath, 'filename' => null, 'score' => 0];
            }
            if (is_array($value)) {
                $walk($value, $childPath);
            }
        }
    };
    $walk($data);

    if ($candidates === []) {
        return null;
    }

    $slugTokens = array_values(array_filter(preg_split('/[^a-z0-9]+/i', $slug) ?: []));
    $seen = [];
    foreach ($candidates as &$row) {
        if (isset($seen[$row['url']])) {
            $row['score'] -= 50;
        }
        $seen[$row['url']] = true;

        $path = $row['path'];
        if (preg_match('/layout\.0\.dataComponent\.body\.\d+\.content_section\.call_to_action_links/', $path)) {
            $row['score'] += 120;
        } elseif (preg_match('/layout\.\d+\.dataComponent\.call_to_action_links/', $path)) {
            $row['score'] += 100;
        } elseif (str_contains($path, 'content_section.call_to_action_links')) {
            $row['score'] += 80;
        } else {
            $row['score'] += 40;
        }

        if (preg_match('/\.body\.(\d+)\./', $path, $m)) {
            $row['score'] += max(0, 30 - (int) $m[1]);
        }

        $filename = basename(rawurldecode(parse_url($row['url'], PHP_URL_PATH) ?: ''));
        $row['filename'] = $filename;
        $haystack = strtolower($filename . ' ' . $path);
        foreach ($slugTokens as $token) {
            if (strlen($token) >= 3 && str_contains($haystack, strtolower($token))) {
                $row['score'] += 25;
            }
        }
        if (preg_match('/TH[_ ]?Brochure|Brochure_/i', $filename)) {
            $row['score'] += 10;
        }
    }
    unset($row);

    usort($candidates, static fn ($a, $b) => $b['score'] <=> $a['score']);

    return $candidates[0];
}

function fwd_extract_brochure_from_page(string $html, string $slug = ''): ?array
{
    return fwd_extract_brochure_from_next_data(fwd_extract_next_data($html), $slug);
}

function fwd_meta_from_html(string $html): array
{
    $meta = [];
    if (preg_match('/<title>([^<]+)<\/title>/u', $html, $m)) {
        $meta['page_title'] = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    if (preg_match('/name="description"\s+content="([^"]*)"/', $html, $m)) {
        $meta['description'] = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    $next = fwd_extract_next_data($html);
    if ($next !== null) {
        $seo = $next['props']['pageProps']['product']['meta']['seo'] ?? null;
        if (is_array($seo)) {
            $meta['description'] = $seo['description'] ?? $meta['description'] ?? '';
            $meta['page_title'] = $seo['title'] ?? $meta['page_title'] ?? '';
        }
    }
    return $meta;
}

function fwd_parse_product_sections(array $product): array
{
    $out = [
        'highlights' => [],
        'hero_bullets' => [],
        'coverage_blocks' => [],
        'conditions' => [],
        'faq' => [],
        'promo' => null,
    ];

    $banner = $product['topBanner'] ?? [];
    if (!empty($banner['caption'])) {
        $out['hero_bullets'][] = fwd_strip_html($banner['caption']);
    }
    if (!empty($banner['title'])) {
        $out['page_title'] = fwd_strip_html($banner['title']);
    }

    foreach ($product['sections'] ?? [] as $section) {
        $key = $section['key'] ?? '';
        $props = $section['props'] ?? [];

        if ($key === 'usps_section') {
            $desc = fwd_strip_html($props['description'] ?? '');
            if ($desc !== '' && mb_strlen($desc) <= 180) {
                $out['hero_bullets'][] = $desc;
            }
            foreach ($props['items'] ?? [] as $item) {
                $title = fwd_strip_html($item['title'] ?? '');
                $itemDesc = fwd_strip_html($item['description'] ?? '');
                if ($title === '') {
                    continue;
                }
                $out['highlights'][] = ['title' => $title, 'desc' => $itemDesc];
            }
        }

        if ($key === 'benefits_summary_section') {
            if (!empty($props['benefits'])) {
                $items = [];
                foreach ($props['benefits'] as $b) {
                    $t = fwd_strip_html($b['title'] ?? '');
                    $d = fwd_strip_html($b['description'] ?? '');
                    if ($t !== '') {
                        $items[] = $d !== '' ? "$t: $d" : $t;
                    }
                }
                if ($items !== []) {
                    $out['coverage_blocks'][] = [
                        'title' => fwd_strip_html($props['title'] ?? 'สรุปความคุ้มครอง'),
                        'desc' => fwd_strip_html($props['description'] ?? ''),
                        'items' => $items,
                    ];
                }
            }
            foreach ($props['details'] ?? [] as $detail) {
                $col = $detail['oneColumn'] ?? $detail['twoColumns'] ?? null;
                if (!is_array($col)) {
                    continue;
                }
                $title = fwd_strip_html($col['title'] ?? '');
                $body = $col['description'] ?? '';
                if ($title === '') {
                    continue;
                }
                if (mb_stripos($title, 'เงื่อนไข') !== false) {
                    $conds = fwd_html_to_condition_list($body);
                    if ($conds !== []) {
                        $out['conditions'] = array_merge($out['conditions'], $conds);
                    }
                    continue;
                }
                $plain = fwd_strip_html($body);
                $listItems = [];
                if (preg_match_all('/<li[^>]*>(.*?)<\/li>/uis', $body, $lm)) {
                    foreach ($lm[1] as $li) {
                        $t = fwd_strip_html($li);
                        if ($t !== '') {
                            $listItems[] = $t;
                        }
                    }
                }
                $block = ['title' => $title, 'desc' => $plain];
                if ($listItems !== []) {
                    $block['items'] = $listItems;
                    $block['desc'] = '';
                }
                $out['coverage_blocks'][] = $block;
            }
        }

        if ($key === 'faqs_section') {
            foreach ($props['questions'] ?? [] as $q) {
                $question = fwd_clean_faq_q(fwd_strip_html($q['title'] ?? $q['question'] ?? ''));
                $answer = fwd_clean_faq_a(fwd_strip_html($q['content'] ?? $q['answer'] ?? ''));
                if ($question !== '' && $answer !== '') {
                    $out['faq'][] = ['q' => $question, 'a' => $answer];
                }
            }
        }

        if ($key === 'utility_section' && !empty($props['items'])) {
            foreach ($props['items'] as $item) {
                $t = fwd_strip_html($item['title'] ?? '');
                $d = fwd_strip_html($item['description'] ?? $item['content'] ?? '');
                if ($t !== '' && $d !== '' && mb_strlen($d) > 20) {
                    $out['highlights'][] = ['title' => $t, 'desc' => $d];
                }
            }
        }
    }

    $out['hero_bullets'] = array_values(array_unique(array_filter($out['hero_bullets'])));
    return $out;
}

function fwd_load_dom_enrichment(string $slug): ?array
{
    $path = dirname(__DIR__) . '/data/fwd-pages/' . $slug . '.dom.json';
    if (!is_readable($path)) {
        return null;
    }
    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : null;
}

function fwd_apply_dom_enrichment(array $detail, array $dom): array
{
    if (!empty($dom['title'])) {
        $detail['title'] = preg_replace('/\s*\|.*$/u', '', $dom['title']);
    }
    if (!empty($dom['tagline'])) {
        $detail['tagline'] = $dom['tagline'];
        $detail['hero_bullets'] = array_values(array_unique(array_filter([
            $dom['tagline'],
            ...($detail['hero_bullets'] ?? []),
        ])));
    }
    if (!empty($dom['highlights'])) {
        $detail['highlights'] = $dom['highlights'];
    }
    if (!empty($dom['faq'])) {
        $detail['faq'] = $dom['faq'];
    }
    if (!empty($dom['coverage_lists'])) {
        $blocks = [];
        $skipItems = ['ลูกค้า', 'โรงพยาบาล', 'ตัวแทนฝ่ายขาย', 'โทร. 1351', 'แชทกับเรา'];
        foreach ($dom['coverage_lists'] as $list) {
            $items = array_values(array_filter($list['items'] ?? [], function ($item) use ($skipItems) {
                return !in_array($item, $skipItems, true) && mb_strlen($item) > 8;
            }));
            if ($items === []) {
                continue;
            }
            $blocks[] = [
                'title' => $list['title'] ?? 'ความคุ้มครอง',
                'desc' => '',
                'items' => $items,
            ];
        }
        if ($blocks !== []) {
            $detail['coverage_blocks'] = $blocks;
        }
    }
    return $detail;
}

/**
 * @param array{slug:string,title:string,desc?:string,category?:string,category_label?:string} $catalog
 */
function fwd_parse_htm_to_plan_detail(string $html, array $catalog): array
{
    $meta = fwd_meta_from_html($html);
    $next = fwd_extract_next_data($html);
    $product = $next['props']['pageProps']['product'] ?? null;

    $title = $catalog['title'];
    $tagline = $catalog['desc'] ?? '';
    if (!empty($meta['description'])) {
        $tagline = $meta['description'];
    }

    $detail = [
        'slug' => $catalog['slug'],
        'title' => $title,
        'tagline' => $tagline,
        'meta' => ($meta['page_title'] ?? $title) . ' — ' . ($catalog['category_label'] ?? ''),
        'category' => $catalog['category'],
        'category_label' => $catalog['category_label'],
        'no_calculator' => true,
        'hero_bullets' => [$tagline],
        'highlights' => [],
        'coverage_blocks' => [],
        'conditions' => [],
        'faq' => [],
        'imported_from_fwd' => true,
    ];

    if (is_array($product) && !empty($product['sections'])) {
        $parsed = fwd_parse_product_sections($product);
        if (!empty($parsed['page_title'])) {
            $detail['title'] = preg_replace('/\s*\|.*$/u', '', $parsed['page_title']);
        }
        if ($parsed['hero_bullets'] !== []) {
            $detail['hero_bullets'] = $parsed['hero_bullets'];
            $detail['tagline'] = $parsed['hero_bullets'][0];
        }
        if ($parsed['highlights'] !== []) {
            $detail['highlights'] = $parsed['highlights'];
        }
        if ($parsed['coverage_blocks'] !== []) {
            $detail['coverage_blocks'] = $parsed['coverage_blocks'];
        }
        if ($parsed['conditions'] !== []) {
            $detail['conditions'] = $parsed['conditions'];
        }
        if ($parsed['faq'] !== []) {
            $detail['faq'] = $parsed['faq'];
        }
    } else {
        $detail['highlights'] = [
            ['title' => 'ภาพรวมแผน', 'desc' => $tagline],
            ['title' => $catalog['category_label'] ?? 'ผลิตภัณฑ์', 'desc' => $catalog['desc'] ?? $tagline],
        ];
        $detail['conditions'] = [
            'รายละเอียดความคุ้มครองและข้อยกเว้นตามเอกสารกรมธรรม์',
            'ติดต่อทีมงานเพื่อรับข้อมูลเพิ่มเติมก่อนตัดสินใจ',
        ];
    }

    if ($detail['highlights'] === []) {
        $detail['highlights'] = [
            ['title' => $detail['title'], 'desc' => $detail['tagline']],
        ];
    }

    if ($detail['conditions'] === []) {
        $detail['conditions'] = [
            'อายุรับประกันและเงื่อนไขตามเงื่อนไขผลิตภัณฑ์',
            'ข้อยกเว้นตามที่ระบุในกรมธรรม์',
        ];
    }

    $dom = fwd_load_dom_enrichment($catalog['slug']);
    if ($dom !== null) {
        $detail = fwd_apply_dom_enrichment($detail, $dom);
    }

    return $detail;
}
