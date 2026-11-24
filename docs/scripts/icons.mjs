// Rasterize public/favicon.svg into the PNG icons and favicon.ico that browsers, iOS and the web
// manifest ask for. Run after changing the logo: CHROME_PATH=/path/to/chrome node scripts/icons.mjs
import { readFileSync, writeFileSync } from 'node:fs'
import puppeteer from 'puppeteer-core'

const svg = readFileSync(new URL('../public/favicon.svg', import.meta.url), 'utf8')
const browser = await puppeteer.launch({ executablePath: process.env.CHROME_PATH || '/usr/bin/google-chrome', args: ['--no-sandbox'] })
const page = await browser.newPage()

async function png(size, padding = 0, background = 'transparent') {
  await page.setViewport({ width: size, height: size })
  const inner = size - 2 * padding
  await page.setContent(`<html><body style="margin:0;background:${background}"><div style="padding:${padding}px">${svg.replace('<svg', `<svg width="${inner}" height="${inner}" style="display:block"`)}</div></body></html>`)
  return page.screenshot({ omitBackground: background === 'transparent', type: 'png' })
}

const files = {
  'favicon-16.png': await png(16),
  'favicon-32.png': await png(32),
  // iOS does not round or pad the icon itself: a white square with the logo inside
  'apple-touch-icon.png': await png(180, 14, '#ffffff'),
  'icon-192.png': await png(192),
  'icon-512.png': await png(512),
}
for (const [name, data] of Object.entries(files)) {
  writeFileSync(new URL(`../public/${name}`, import.meta.url), data)
}

// favicon.ico: an ICONDIR header and one entry per size, each image stored as PNG (supported since Windows Vista)
const images = [[16, files['favicon-16.png']], [32, files['favicon-32.png']], [48, await png(48)]]
const header = Buffer.alloc(6 + 16 * images.length)
header.writeUInt16LE(0, 0)
header.writeUInt16LE(1, 2)
header.writeUInt16LE(images.length, 4)
let offset = header.length
images.forEach(([size, data], i) => {
  const entry = 6 + 16 * i
  header.writeUInt8(size, entry)
  header.writeUInt8(size, entry + 1)
  header.writeUInt16LE(1, entry + 4)
  header.writeUInt16LE(32, entry + 6)
  header.writeUInt32LE(data.length, entry + 8)
  header.writeUInt32LE(offset, entry + 12)
  offset += data.length
})
writeFileSync(new URL('../public/favicon.ico', import.meta.url), Buffer.concat([header, ...images.map(([, data]) => Buffer.from(data))]))

await browser.close()
console.log(`public/${Object.keys(files).join(', public/')} and public/favicon.ico written`)
