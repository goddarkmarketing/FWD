/**
 * Download FWD product pages as .htm (full rendered HTML).
 * Usage: node scripts/download-fwd-pages.mjs
 * Input:  data/fwd-urls/all-products.json
 * Output: data/fwd-pages/{slug}.htm
 */
import { chromium } from "playwright";
import { readFileSync, writeFileSync, mkdirSync, existsSync } from "fs";
import { dirname, join } from "path";
import { fileURLToPath } from "url";

const __dir = dirname(fileURLToPath(import.meta.url));
const root = join(__dir, "..");
const products = JSON.parse(
  readFileSync(join(root, "data/fwd-urls/all-products.json"), "utf8")
);
const outDir = join(root, "data/fwd-pages");
mkdirSync(outDir, { recursive: true });

const browser = await chromium.launch({
  headless: true,
  args: ["--disable-blink-features=AutomationControlled"],
});
const context = await browser.newContext({
  locale: "th-TH",
  viewport: { width: 1366, height: 900 },
  userAgent:
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
});
await context.addInitScript(() => {
  Object.defineProperty(navigator, "webdriver", { get: () => false });
});
const page = await context.newPage();

const manifest = [];
let ok = 0;
let fail = 0;

for (const p of products) {
  const url = p.fwd_url;
  const slug = p.slug;
  const dest = join(outDir, `${slug}.htm`);
  if (!url) {
    manifest.push({ slug, url, status: "skip", error: "no url" });
    fail++;
    continue;
  }
  try {
    console.log(`[${ok + fail + 1}/${products.length}] ${slug}`);
    const res = await page.goto(url, { waitUntil: "networkidle", timeout: 120000 });
    try {
      await page
        .locator("#onetrust-accept-btn-handler, button:has-text('ยอมรับ')")
        .first()
        .click({ timeout: 4000 });
    } catch {}
    try {
      await page.waitForFunction(
        () => {
          const el = document.getElementById("__NEXT_DATA__");
          if (!el) return false;
          try {
            const d = JSON.parse(el.textContent);
            return (d?.props?.pageProps?.product?.sections?.length ?? 0) > 0;
          } catch {
            return false;
          }
        },
        { timeout: 35000 }
      );
    } catch {}
    await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
    await page.waitForTimeout(1500);
    const html = await page.content();
    const hasNext = html.includes("__NEXT_DATA__");
    let hasProduct = false;
    try {
      const nd = await page.evaluate(() => {
        const el = document.getElementById("__NEXT_DATA__");
        if (!el) return false;
        const d = JSON.parse(el.textContent);
        return (d?.props?.pageProps?.product?.sections?.length ?? 0) > 0;
      });
      hasProduct = nd;
    } catch {}
    if (!hasNext && res && res.status() >= 400) {
      throw new Error(`HTTP ${res.status()}`);
    }
    writeFileSync(dest, html, "utf8");
    manifest.push({
      slug,
      url,
      status: "ok",
      bytes: html.length,
      has_next_data: hasNext,
      has_product: hasProduct,
      file: `data/fwd-pages/${slug}.htm`,
    });
    ok++;
  } catch (e) {
    manifest.push({ slug, url, status: "error", error: String(e.message || e) });
    fail++;
  }
}

await browser.close();
writeFileSync(
  join(outDir, "manifest.json"),
  JSON.stringify({ downloaded_at: new Date().toISOString(), ok, fail, items: manifest }, null, 2),
  "utf8"
);
console.log(`\nDone: ${ok} ok, ${fail} failed → ${outDir}`);
