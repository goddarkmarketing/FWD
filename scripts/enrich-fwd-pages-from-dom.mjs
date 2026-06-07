/**
 * สำหรับหน้าที่ไม่มี product ใน __NEXT_DATA__ — ดึงเนื้อหาจาก DOM หลังเรนเดอร์
 * Usage: node scripts/enrich-fwd-pages-from-dom.mjs
 */
import { chromium } from "playwright";
import { readFileSync, writeFileSync, existsSync } from "fs";
import { dirname, join } from "path";
import { fileURLToPath } from "url";

const __dir = dirname(fileURLToPath(import.meta.url));
const root = join(__dir, "..");
const products = JSON.parse(
  readFileSync(join(root, "data/fwd-urls/all-products.json"), "utf8")
);
const pagesDir = join(root, "data/fwd-pages");

function needsEnrich(slug) {
  const htm = readFileSync(join(pagesDir, `${slug}.htm`), "utf8");
  const i = htm.indexOf('id="__NEXT_DATA__"');
  if (i < 0) return true;
  const start = htm.indexOf(">", i) + 1;
  const end = htm.indexOf("</script>", start);
  try {
    const d = JSON.parse(htm.slice(start, end));
    return !(d?.props?.pageProps?.product?.sections?.length > 0);
  } catch {
    return true;
  }
}

const extractDom = () => {
  const main = document.querySelector("main") || document.body;
  const h1 = main.querySelector("h1");
  const title = h1?.innerText?.trim() || "";
  let tagline = "";
  const h2first = main.querySelector("h2");
  if (h2first) tagline = h2first.innerText.trim();

  const highlights = [];
  const seen = new Set();
  for (const h of main.querySelectorAll("h2, h3")) {
    const t = h.innerText?.trim() || "";
    if (!t || t.length < 4 || t.length > 150) continue;
    if (/สนใจ|โบรชัวร์|ผลิตภัณฑ์ที่เกี่ยวข้อง|มีจำหน่าย|คำถามที่พบบ่อย/i.test(t))
      continue;
    if (seen.has(t)) continue;
    seen.add(t);
    let desc = "";
    let el = h.nextElementSibling;
    for (let i = 0; i < 3 && el; i++) {
      const tx = el.innerText?.trim() || "";
      if (tx.length > 20) {
        desc = tx.slice(0, 400);
        break;
      }
      el = el.nextElementSibling;
    }
    highlights.push({ title: t, desc });
    if (highlights.length >= 8) break;
  }

  const faq = [];
  for (const d of main.querySelectorAll("details")) {
    const q = d.querySelector("summary")?.innerText?.trim() || "";
    const a =
      d.querySelector("p")?.innerText?.trim() ||
      [...d.querySelectorAll("p")]
        .map((p) => p.innerText.trim())
        .join(" ")
        .slice(0, 800);
    if (q && a) faq.push({ q: q.replace(/^Q:\s*/i, ""), a: a.replace(/^A:\s*/i, "") });
  }

  const lists = [];
  for (const ul of main.querySelectorAll("ul")) {
    const items = [...ul.querySelectorAll("li")]
      .map((li) => li.innerText.trim())
      .filter((x) => x.length > 2 && x.length < 200);
    if (items.length >= 2 && items.length <= 20) {
      const prev = ul.previousElementSibling;
      const blockTitle =
        prev?.tagName?.match(/^H[2-4]$/) ? prev.innerText.trim() : "รายละเอียดความคุ้มครอง";
      lists.push({ title: blockTitle, items });
    }
  }

  return { title, tagline, highlights, faq, coverage_lists: lists.slice(0, 6) };
};

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();

let ok = 0;
for (const p of products) {
  if (!needsEnrich(p.slug)) continue;
  console.log(`DOM ${p.slug}`);
  try {
    await page.goto(p.fwd_url, { waitUntil: "networkidle", timeout: 120000 });
    try {
      await page.locator("#onetrust-accept-btn-handler").click({ timeout: 3000 });
    } catch {}
    await page.waitForTimeout(12000);
    const dom = await page.evaluate(extractDom);
    writeFileSync(
      join(pagesDir, `${p.slug}.dom.json`),
      JSON.stringify(dom, null, 2),
      "utf8"
    );
    const html = await page.content();
    writeFileSync(join(pagesDir, `${p.slug}.htm`), html, "utf8");
    ok++;
  } catch (e) {
    console.error(`  fail: ${e.message}`);
  }
}
await browser.close();
console.log(`Enriched ${ok} pages`);
