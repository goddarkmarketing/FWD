import { chromium } from "playwright";
const url = "https://www.fwd.co.th/th/health-insurance/precious-care/";
const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();
await page.goto(url, { waitUntil: "networkidle", timeout: 120000 });
try {
  await page.locator("#onetrust-accept-btn-handler").click({ timeout: 3000 });
} catch {}
await page.waitForTimeout(15000);
const has = await page.evaluate(() => {
  const d = JSON.parse(document.getElementById("__NEXT_DATA__").textContent);
  return d?.props?.pageProps?.product?.sections?.length ?? 0;
});
console.log("sections:", has);
await browser.close();
