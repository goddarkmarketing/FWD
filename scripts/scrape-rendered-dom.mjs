import { chromium } from "playwright";
const url = process.argv[2] || "https://www.fwd.co.th/th/health-insurance/precious-care/";
const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();
await page.goto(url, { waitUntil: "networkidle", timeout: 120000 });
try {
  await page.locator("#onetrust-accept-btn-handler").click({ timeout: 3000 });
} catch {}
await page.waitForTimeout(12000);
const text = await page.evaluate(() => {
  const main = document.querySelector("main") || document.body;
  const headings = [...main.querySelectorAll("h1,h2,h3")].slice(0, 15).map((el) => el.innerText.trim());
  const hasUsp = main.innerText.includes("จุดเด่น");
  return { headings, hasUsp, len: main.innerText.length };
});
console.log(JSON.stringify(text, null, 2));
await browser.close();
