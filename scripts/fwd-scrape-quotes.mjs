/**
 * ดึงเบี้ยจากหน้า FWD จริง (รันเบราว์เซอร์) — ไม่ต้องสกรีนช็อตทีละแผน
 *
 * ติดตั้งครั้งแรก (ในโฟลเดอร์ fwd):
 *   npm init -y
 *   npm install playwright
 *   npx playwright install chromium
 *
 * รัน:
 *   node scripts/fwd-scrape-quotes.mjs
 *
 * ผลลัพธ์: data/fwd-real-pricing.json
 */
import { chromium } from "playwright";
import { readFileSync, writeFileSync, mkdirSync } from "fs";
import { dirname, join } from "path";
import { fileURLToPath } from "url";

const __dir = dirname(fileURLToPath(import.meta.url));
const products = JSON.parse(
  readFileSync(join(__dir, "fwd-products.json"), "utf8")
);

const AGES = [25, 30, 35, 40, 45];
const GENDERS = [
  { key: "male", label: "ผู้ชาย", testId: "male" },
  { key: "female", label: "ผู้หญิง", testId: "female" },
];

function parseBaht(text) {
  const m = text.replace(/,/g, "").match(/(\d+)/);
  return m ? parseInt(m[1], 10) : null;
}

function extractPlans(pageText) {
  const plans = [];
  const re =
    /(Economy|Standard|Premium|แผน\s*[\d.]+[KM]?)[\s\S]{0,120}?([\d,]+)\s*บาท/giu;
  let m;
  while ((m = re.exec(pageText)) !== null) {
    plans.push({
      name: m[1].trim(),
      sum: parseBaht(m[2]),
      raw: m[0].slice(0, 80),
    });
  }
  return plans;
}

async function scrapeProduct(page, product) {
  const result = {
    slug: product.slug,
    url: product.url,
    scraped_at: new Date().toISOString(),
    packages: [],
    quotes: [],
    errors: [],
  };

  try {
    await page.goto(product.url, {
      waitUntil: "networkidle",
      timeout: 90000,
    });
    await page.waitForTimeout(3000);

    const planCards = page.locator('[data-testid="PlanOptionV3"]');
    const count = await planCards.count();
    for (let i = 0; i < count; i++) {
      const card = planCards.nth(i);
      const text = (await card.innerText()).replace(/\s+/g, " ");
      const sums = [...text.matchAll(/([\d,]+)\s*บาท/g)].map((x) =>
        parseBaht(x[1])
      );
      const premiums = [...text.matchAll(/([\d,]+)\s*บาท\/(?:ปี|เดือน)/g)].map(
        (x) => parseBaht(x[1])
      );
      result.packages.push({
        index: i,
        text: text.slice(0, 200),
        sum: sums[0] || null,
        premium_yearly: premiums.find((p) => p) || null,
        premium_monthly: null,
      });
    }

    for (const gender of GENDERS) {
      const btn = page.getByRole("button", { name: gender.label }).first();
      if (await btn.isVisible().catch(() => false)) {
        await btn.click();
        await page.waitForTimeout(800);
      }

      const ageSelect = page.locator("select").first();
      if (!(await ageSelect.isVisible().catch(() => false))) {
        continue;
      }

      for (const age of AGES) {
        try {
          await ageSelect.selectOption(String(age));
          await page.waitForTimeout(1500);

          const calc = page.locator("#plan-calc-price, .plan-calc__result-price");
          let premiumText = "";
          if (await calc.count()) {
            premiumText = await calc.first().innerText();
          } else {
            const widget = page.locator('[class*="QuoteWidget"], [class*="qwPlans"]');
            if (await widget.count()) {
              premiumText = await widget.first().innerText();
            }
          }

          const yearly = parseBaht(premiumText);
          result.quotes.push({
            gender: gender.key,
            age,
            premium_text: premiumText.trim(),
            premium_yearly: yearly,
          });
        } catch (e) {
          result.errors.push(`age ${age} ${gender.key}: ${e.message}`);
        }
      }
    }

    const bodyText = await page.locator("body").innerText();
    const examples = bodyText.match(
      /ตัวอย่างเบี้ย[\s\S]{0,200}?\d[\d,]*\s*บาท\/ปี/g
    );
    if (examples) {
      result.premium_examples = examples;
    }
  } catch (e) {
    result.errors.push(e.message);
  }

  return result;
}

async function main() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    locale: "th-TH",
    userAgent:
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36",
  });
  const page = await context.newPage();

  const all = {};
  for (const product of products) {
    console.log("Scraping:", product.slug);
    all[product.slug] = await scrapeProduct(page, product);
    console.log(
      "  packages:",
      all[product.slug].packages.length,
      "quotes:",
      all[product.slug].quotes.length
    );
  }

  await browser.close();

  const outPath = join(__dir, "..", "data", "fwd-real-pricing.json");
  mkdirSync(dirname(outPath), { recursive: true });
  writeFileSync(outPath, JSON.stringify(all, null, 2), "utf8");
  console.log("Saved:", outPath);
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
