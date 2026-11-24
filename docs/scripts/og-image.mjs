// Build the social card public/og.png, 1200 × 630: the logo, the name and the tagline on the left,
// the home page illustration on the right. Run after changing either:
// CHROME_PATH=/path/to/chrome node scripts/og-image.mjs
import { readFileSync } from 'node:fs'
import puppeteer from 'puppeteer-core'

const read = (path) => readFileSync(new URL(path, import.meta.url), 'utf8')
const logo = read('../public/logo.svg').replace('<svg', '<svg width="112" height="112"')
const hero = read('../public/illustrations/hero-light.svg').replace('<svg', '<svg style="width:560px;height:auto;display:block"')

const browser = await puppeteer.launch({ executablePath: process.env.CHROME_PATH || '/usr/bin/google-chrome', args: ['--no-sandbox'] })
const page = await browser.newPage()
await page.setViewport({ width: 1200, height: 630 })
await page.setContent(`<html><body style="margin:0;width:1200px;height:630px;overflow:hidden;background:linear-gradient(135deg,#ffffff 0%,#f0fdfa 60%,#ccfbf1 100%);font-family:Inter,'Helvetica Neue',Helvetica,Arial,sans-serif;display:flex;align-items:center;gap:24px;padding:0 40px 0 64px;box-sizing:border-box">
  <div style="flex:1">
    ${logo}
    <div style="font-size:60px;font-weight:800;color:#134e4a;line-height:1.05;margin-top:28px">Nette Auto<br>Variable Template</div>
    <div style="font-size:28px;color:#475569;margin-top:20px;line-height:1.35">Register a variable once,<br>use it in every Latte template.</div>
    <div style="font-size:17px;white-space:nowrap;color:#0f766e;margin-top:28px;font-family:Menlo,Consolas,monospace">composer require tomaj/nette-auto-variable-template</div>
  </div>
  ${hero}</body></html>`)
await page.screenshot({ path: new URL('../public/og.png', import.meta.url).pathname })
await browser.close()
console.log('public/og.png written')
