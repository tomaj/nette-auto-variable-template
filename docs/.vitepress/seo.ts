// Per-page SEO: title, canonical URL, Open Graph and Twitter tags, and JSON-LD.
// Used by transformHead in config.mts. Adapted from the nette-api documentation.
import { existsSync, readFileSync } from 'node:fs'
import { resolve } from 'node:path'
import type { HeadConfig, PageData } from 'vitepress'

const base = process.env.DOCS_BASE || '/nette-auto-variable-template/'
export const isPreview = base !== '/nette-auto-variable-template/'
export const siteUrl = `https://tomaj.github.io${base}`
export const ogImage = `${siteUrl}og.png`
// Slovak pages get the social card with the Slovak tagline.
export const ogImageSk = `${siteUrl}og-sk.png`
export const siteName = 'Nette Auto Variable Template'
const repoUrl = 'https://github.com/tomaj/nette-auto-variable-template'

const person = {
  '@type': 'Person',
  '@id': 'https://github.com/tomaj#person',
  name: 'Tomas Majer',
  url: 'https://github.com/tomaj',
}

export function pageUrl(relativePath: string): string {
  const path = relativePath.replace(/(^|\/)index\.md$/, '$1').replace(/\.md$/, '')
  return siteUrl + path
}

// English pages live at the site root, Slovak pages mirror them under sk/.
export function localeOf(relativePath: string): { prefix: string, lang: string, ogLocale: string } {
  return relativePath.startsWith('sk/')
    ? { prefix: 'sk/', lang: 'sk-SK', ogLocale: 'sk_SK' }
    : { prefix: '', lang: 'en-US', ogLocale: 'en_US' }
}

// The same page in the other locale, when it exists, for hreflang links.
export function alternates(relativePath: string, srcDir: string): { lang: string, href: string }[] {
  const base = relativePath.slice(localeOf(relativePath).prefix.length)
  const pairs: [string, string][] = [['en', base], ['sk', `sk/${base}`]]
  const found = pairs.filter(([, path]) => existsSync(resolve(srcDir, path)))
  if (found.length < 2) return []
  return [
    ...found.map(([lang, path]) => ({ lang, href: pageUrl(path) })),
    { lang: 'x-default', href: pageUrl(base) },
  ]
}

// Section names for the breadcrumb, from the first path segment after the locale.
const sections: Record<string, Record<string, [string, string]>> = {
  '': {
    guide: ['Guide', 'guide/getting-started'],
    examples: ['Examples', 'examples/'],
    api: ['Reference', 'api/reference'],
  },
  'sk/': {
    guide: ['Príručka', 'guide/getting-started'],
    examples: ['Príklady', 'examples/'],
    api: ['Referencia', 'api/reference'],
  },
}

function breadcrumb(page: PageData, url: string) {
  const { prefix } = localeOf(page.relativePath)
  const home = siteUrl + prefix
  const items: { name: string, item: string }[] = [{ name: siteName, item: home }]
  const section = sections[prefix][page.relativePath.slice(prefix.length).split('/')[0]]
  if (section && home + section[1] !== url) items.push({ name: section[0], item: home + section[1] })
  if (url !== home) items.push({ name: page.title, item: url })
  return {
    '@type': 'BreadcrumbList',
    itemListElement: items.map((entry, i) => ({ '@type': 'ListItem', position: i + 1, name: entry.name, item: entry.item })),
  }
}

// Markdown to plain text, enough for JSON-LD answers.
export function plain(markdown: string): string {
  return markdown
    .replace(/```[\s\S]*?```/g, '')
    .replace(/^<<< .*$/gm, '')
    .replace(/!\[[^\]]*\]\([^)]*\)(\{[^}]*\})?/g, '')
    .replace(/\[([^\]]+)\]\([^)]*\)/g, '$1')
    .replace(/\s*\{#[^}]+\}/g, '')
    .split(/(`[^`]*`)/)
    .map((part, i) => i % 2 ? part.slice(1, -1) : part.replace(/<[^>]+>/g, '').replace(/\*\*([^*]+)\*\*/g, '$1'))
    .join('')
    .replace(/\s+/g, ' ')
    .trim()
}

// Questions are the `##` headings of faq.md and sk/faq.md.
export function faqEntries(source: string, relativePath: string): { q: string, a: string }[] {
  if (relativePath !== 'faq.md' && relativePath !== 'sk/faq.md') return []
  return ('\n' + source).split('\n## ').slice(1)
    .map((part) => {
      const [q, ...rest] = part.split('\n')
      return { q: plain(q), a: plain(rest.join('\n')) }
    })
    .filter((e) => e.q && e.a)
}

export function seoHead(page: PageData, srcDir: string, siteDescription: string): HeadConfig[] {
  if (page.isNotFound || page.relativePath === '404.md') {
    return [
      ['meta', { name: 'robots', content: 'noindex' }],
      ['meta', { name: 'description', content: 'This Nette Auto Variable Template documentation page does not exist. Go to Getting started, the examples, the API reference or the FAQ.' }],
    ]
  }
  if (!page.relativePath) return []
  const url = pageUrl(page.relativePath)
  const locale = localeOf(page.relativePath)
  const sk = locale.prefix === 'sk/'
  const image = sk ? ogImageSk : ogImage
  const isHome = page.frontmatter.layout === 'home'
  const homeTitle = sk ? `${siteName} – premenné v každej Nette šablóne` : `${siteName} – variables in every Nette template`
  const title = isHome ? homeTitle : `${page.title} | ${siteName}`
  const description = page.description || page.frontmatter.description || siteDescription
  const source = readFileSync(resolve(srcDir, page.relativePath), 'utf8')
  const modified = page.lastUpdated ? new Date(page.lastUpdated).toISOString() : undefined

  const head: HeadConfig[] = [
    ['link', { rel: 'canonical', href: url }],
    ['meta', { property: 'og:type', content: isHome ? 'website' : 'article' }],
    ['meta', { property: 'og:site_name', content: siteName }],
    ['meta', { property: 'og:locale', content: locale.ogLocale }],
    ['meta', { property: 'og:locale:alternate', content: sk ? 'en_US' : 'sk_SK' }],
    ['meta', { property: 'og:title', content: title }],
    ['meta', { property: 'og:description', content: description }],
    ['meta', { property: 'og:url', content: url }],
    ['meta', { property: 'og:image', content: image }],
    ['meta', { property: 'og:image:width', content: '1200' }],
    ['meta', { property: 'og:image:height', content: '630' }],
    ['meta', { property: 'og:image:alt', content: sk ? 'Nette Auto Variable Template: jeden riadok registerVariable(), názov aplikácie v každej šablóne' : 'Nette Auto Variable Template: one registerVariable() line, the app name in every template' }],
    ['meta', { name: 'twitter:card', content: 'summary_large_image' }],
    ['meta', { name: 'twitter:title', content: title }],
    ['meta', { name: 'twitter:description', content: description }],
    ['meta', { name: 'twitter:image', content: image }],
    ['meta', { name: 'author', content: person.name }],
  ]
  if (isPreview) head.push(['meta', { name: 'robots', content: 'noindex, nofollow' }])
  for (const alt of alternates(page.relativePath, srcDir)) {
    head.push(['link', { rel: 'alternate', hreflang: alt.lang, href: alt.href }])
  }
  if (modified && !isHome) head.push(['meta', { property: 'article:modified_time', content: modified }])

  const logo = { '@type': 'ImageObject', '@id': `${siteUrl}#logo`, url: `${siteUrl}icon-512.png`, contentUrl: `${siteUrl}icon-512.png`, width: 512, height: 512, caption: siteName }
  const website = { '@type': 'WebSite', '@id': `${siteUrl}#website`, name: siteName, url: siteUrl, image: { '@id': logo['@id'] }, inLanguage: ['en-US', 'sk-SK'], publisher: { '@id': person['@id'] } }
  const graph: Record<string, unknown>[] = [person, logo, website]

  if (isHome) {
    graph.push({
      '@type': ['SoftwareSourceCode', 'SoftwareApplication'],
      '@id': `${siteUrl}#software`,
      name: 'tomaj/nette-auto-variable-template',
      description: siteDescription,
      url: siteUrl,
      codeRepository: repoUrl,
      programmingLanguage: { '@type': 'ComputerLanguage', name: 'PHP' },
      runtimePlatform: 'PHP 8.4+',
      applicationCategory: 'DeveloperApplication',
      operatingSystem: 'Linux, macOS, Windows',
      license: 'https://spdx.org/licenses/MIT.html',
      softwareVersion: '4.x',
      author: { '@id': person['@id'] },
      maintainer: { '@id': person['@id'] },
      image: [{ '@id': logo['@id'] }, image],
      offers: { '@type': 'Offer', price: '0', priceCurrency: 'USD' },
    })
  } else {
    graph.push({
      '@type': 'TechArticle',
      '@id': `${url}#article`,
      headline: page.title,
      description,
      url,
      mainEntityOfPage: url,
      inLanguage: locale.lang,
      image,
      author: { '@id': person['@id'] },
      publisher: { '@id': person['@id'] },
      isPartOf: { '@id': website['@id'] },
      about: { '@type': 'SoftwareSourceCode', name: 'tomaj/nette-auto-variable-template', codeRepository: repoUrl },
      ...(modified ? { dateModified: modified } : {}),
    })
    graph.push(breadcrumb(page, url))
  }

  const faq = faqEntries(source, page.relativePath)
  if (faq.length > 0) {
    graph.push({
      '@type': 'FAQPage',
      '@id': `${url}#faq`,
      url,
      mainEntity: faq.map((e) => ({ '@type': 'Question', name: e.q, acceptedAnswer: { '@type': 'Answer', text: e.a } })),
    })
  }

  head.push(['script', { type: 'application/ld+json' }, JSON.stringify({ '@context': 'https://schema.org', '@graph': graph })])
  return head
}
