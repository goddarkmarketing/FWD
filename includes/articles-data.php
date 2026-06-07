<?php
/**
 * บทความความรู้เรื่องประกัน
 */
function article_stock_image(int $index): string
{
    static $pool;
    if ($pool === null) {
        $pool = [];
        $dir = dirname(__DIR__) . '/assets/images/products2';
        if (is_dir($dir)) {
            $extensions = ['jpg', 'jpeg', 'png', 'webp'];
            $files = [];
            foreach ($extensions as $ext) {
                $files = array_merge($files, glob($dir . '/*.' . $ext) ?: [], glob($dir . '/*.' . strtoupper($ext)) ?: []);
            }
            $files = array_values(array_unique($files));
            sort($files, SORT_NATURAL | SORT_FLAG_CASE);
            foreach ($files as $file) {
                if (stripos(basename($file), 'Header_TH') === 0) {
                    continue;
                }
                $pool[] = 'assets/images/products2/' . basename($file);
            }
        }
        if ($pool === []) {
            $pool[] = 'product-mock.php?cat=health&n=1';
        }
    }
    $i = ($index - 1) % count($pool);
    return $pool[$i];
}

function articles_all(): array
{
    static $articles;
    if ($articles !== null) {
        return $articles;
    }

    $articles = [
        [
            'slug' => 'health-insurance-guide',
            'title' => 'ทำไมต้องมีประกันสุขภาพเหมาจ่าย?',
            'excerpt' => 'ค่ารักษาแพงขึ้นทุกปี ประกันสุขภาพเหมาจ่ายช่วยลดภาระค่าใช้จ่ายก้อน และเลือกรักษาได้ตามต้องการ',
            'category' => 'ประกันสุขภาพ',
            'date' => '28 พ.ค. 2026',
            'read_min' => 5,
            'body' => [
                'ค่ารักษาพยาบาลในประเทศไทยเพิ่มขึ้นอย่างต่อเนื่อง โดยเฉพาะกรณีนอนโรงพยาบาลหรือผ่าตัดใหญ่ อาจสูงถึงหลายแสนหรือล้านบาท',
                'ประกันสุขภาพแบบเหมาจ่าย (IPD/OPD) ช่วยให้คุณวางแผนค่าใช้จ่ายได้ ไม่ต้องกังวลเรื่องวงเงินสวัสดิการไม่พอ และยังเลือกโรงพยาบาลหรือแพทย์ที่ถนัดใจได้ในหลายแผน',
                'การซื้อออนไลน์ทำได้ง่าย — เพียงตอบคำถามสุขภาพตามจริง เปรียบเทียบแผน และเริ่มความคุ้มครองได้ทันทีหลังอนุมัติ',
            ],
        ],
        [
            'slug' => 'tax-deduction-life-insurance',
            'title' => 'ลดหย่อนภาษีด้วยประกันชีวิต — ควรรู้อะไรบ้าง',
            'excerpt' => 'เบี้ยประกันชีวิตและสุขภาพลดหย่อนได้ตามกฎหมาย วางแผนให้ครบถ้วนก่อนยื่นภาษี',
            'category' => 'การเงิน',
            'date' => '20 พ.ค. 2026',
            'read_min' => 6,
            'body' => [
                'เบี้ยประกันชีวิตลดหย่อนภาษีได้สูงสุด 100,000 บาทต่อปี ส่วนเบี้ยประกันสุขภาพลดหย่อนได้สูงสุด 25,000 บาท (รวมกันไม่เกิน 100,000 บาท ตามที่กรมสรรพากรกำหนด)',
                'ควรเก็บใบเสร็จและตรวจสอบว่าบริษัทประกันรายงานข้อมูลให้กรมสรรพากรครบถ้วน',
                'เลือกแผนที่เหมาะกับงบและความคุ้มครองจริง ไม่ใช่ซื้อเพื่อลดหย่อนอย่างเดียว',
            ],
        ],
        [
            'slug' => 'critical-vs-health',
            'title' => 'ประกันโรคร้ายแรง กับ ประกันสุขภาพ ต่างกันอย่างไร',
            'excerpt' => 'สองแบบนี้เสริมกันได้ — โรคร้ายแรงให้เงินก้อนเมื่อวินิจฉัย สุขภาพดูแลค่ารักษา',
            'category' => 'ความรู้ประกัน',
            'date' => '12 พ.ค. 2026',
            'read_min' => 5,
            'body' => [
                'ประกันสุขภาพช่วยจ่ายค่ารักษาพยาบาลตามจำนวนจริงหรือตามวงเงินในกรมธรรม์ เหมาะกับทุกวันที่ป่วยหรือนอน รพ.',
                'ประกันโรคร้ายแรงจ่ายเงินก้อนเมื่อวินิจฉัยโรคที่กำหนด (เช่น มะเร็ง หัวใจ หลอดเลือดสมอง) ใช้เงินนี้รักษา ใช้ชีวิต หรือชดเชยรายได้ที่หายไปได้',
                'หลายคนเลือกถือทั้งสองแบบเพื่อความอุ่นใจครบมิติ',
            ],
        ],
        [
            'slug' => 'buy-insurance-online-tips',
            'title' => 'ซื้อประกันออนไลน์ ต้องเตรียมอะไรบ้าง',
            'excerpt' => 'เช็กลิสต์ก่อนกดซื้อ — เอกสาร คำถามสุขภาพ และการเลือกผู้รับประโยชน์',
            'category' => 'ซื้อออนไลน์',
            'date' => '5 พ.ค. 2026',
            'read_min' => 4,
            'body' => [
                'เตรียมบัตรประชาชน และบัญชีสำหรับชำระเบี้ย (บัตรเครดิต / Mobile Banking / Thai QR)',
                'ตอบคำถามสุขภาพตามความจริง — ข้อมูลไม่ตรงอาจมีผลต่อการเคลมหรือสิทธิ์ความคุ้มครอง',
                'กำหนดผู้รับประโยชน์ให้ชัดเจน และเก็บเอกสารกรมธรรม์ / e-Policy ไว้ให้ครบ',
            ],
        ],
    ];

    foreach ($articles as $i => &$article) {
        $article['image'] = article_stock_image($i + 8);
    }
    unset($article);

    return $articles;
}

function article_by_slug(string $slug): ?array
{
    foreach (articles_all() as $article) {
        if ($article['slug'] === $slug) {
            return $article;
        }
    }
    return null;
}

function article_url(string $slug): string
{
    return page_url('article.php?slug=' . rawurlencode($slug));
}
