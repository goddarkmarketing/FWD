import { chromium } from "playwright";
const url = process.argv[2] || "https://www.fwd.co.th/th/health-insurance/precious-care/";
const hits = [];
const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();
page.on("response", async (res) => {
  const u = res.url();
  if (!u.includes("fwd.co.th")) return;
  const ct = res.headers()["content-type"] || "";
  if (!/json|graphql/i.test(ct) && !/product|content|page|cms/i.test(u)) return;
  let size = 0;
  try {
    const b = await res.body();
    size = b.length;
  } catch {}
  if (size > 500) hits.push({ u, status: res.status(), size });
});
await page.goto(url, { waitUntil: "networkidle", timeout: 120000 });
await page.waitForTimeout(8000);
await browser.close();
hits.sort((a, b) => b.size - a.size);
for (const h of hits.slice(0, 25)) console.log(h.size, h.status, h.u);
