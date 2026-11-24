// Per-page SEO: title, canonical URL, Open Graph and Twitter tags, and JSON-LD.
// Used by transformHead in config.mts. Adapted from the nette-api documentation.
import { readFileSync } from 'node:fs'
import { resolve } from 'node:path'
import type { HeadConfig, PageData } from 'vitepress'

const base = process.env.DOCS_BASE || '/nette-auto-variable-template/'
export const isPreview = base !== '/nette-auto-variable-template/'
export const siteUrl = `https://tomaj.github.io${base}`
export const ogImage = `${siteUrl}og.png`
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

const sections: Record<string, [string, string]> = {
  guide: ['Guide', 'guide/getting-started'],
  examples: ['Examples', 'examples/'],
  api: ['Reference', 'api/reference'],
}

function breadcrumb(page: PageData, url: string) {
  const items: { name: string, item: string }[] = [{ name: siteName, item: siteUrl }]
  const section = sections[page.relativePath.split('/')[0]]
  if (section && siteUrl + section[1] !== url) items.push({ name: section[0], item: siteUrl + section[1] })
  if (url !== siteUrl) items.push({ name: page.title, item: url })
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
    .split(/(`[^`]*`)/)
    .map((part, i) => i % 2 ? part.slice(1, -1) : part.replace(/<[^>]+>/g, '').replace(/\*\*([^*]+)\*\*/g, '$1'))
    .join('')
    .replace(/\s+/g, ' ')
    .trim()
}

// Questions are the `##` headings of faq.md.
export function faqEntries(source: string, relativePath: string): { q: string, a: string }[] {
  if (relativePath !== 'faq.md') return []
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
  const isHome = page.frontmatter.layout === 'home'
  const title = isHome ? `${siteName} – variables in every Nette template` : `${page.title} | ${siteName}`
  const description = page.description || page.frontmatter.description || siteDescription
  const source = readFileSync(resolve(srcDir, page.relativePath), 'utf8')
  const modified = page.lastUpdated ? new Date(page.lastUpdated).toISOString() : undefined

  const head: HeadConfig[] = [
    ['link', { rel: 'canonical', href: url }],
    ['meta', { property: 'og:type', content: isHome ? 'website' : 'article' }],
    ['meta', { property: 'og:site_name', content: siteName }],
    ['meta', { property: 'og:locale', content: 'en_US' }],
    ['meta', { property: 'og:title', content: title }],
    ['meta', { property: 'og:description', content: description }],
    ['meta', { property: 'og:url', content: url }],
    ['meta', { property: 'og:image', content: ogImage }],
    ['meta', { property: 'og:image:width', content: '1200' }],
    ['meta', { property: 'og:image:height', content: '630' }],
    ['meta', { property: 'og:image:alt', content: 'Nette Auto Variable Template: one registerVariable() line, the app name in every template' }],
    ['meta', { name: 'twitter:card', content: 'summary_large_image' }],
    ['meta', { name: 'twitter:title', content: title }],
    ['meta', { name: 'twitter:description', content: description }],
    ['meta', { name: 'twitter:image', content: ogImage }],
    ['meta', { name: 'author', content: person.name }],
  ]
  if (isPreview) head.push(['meta', { name: 'robots', content: 'noindex, nofollow' }])
  if (modified && !isHome) head.push(['meta', { property: 'article:modified_time', content: modified }])

  const logo = { '@type': 'ImageObject', '@id': `${siteUrl}#logo`, url: `${siteUrl}icon-512.png`, contentUrl: `${siteUrl}icon-512.png`, width: 512, height: 512, caption: siteName }
  const website = { '@type': 'WebSite', '@id': `${siteUrl}#website`, name: siteName, url: siteUrl, image: { '@id': logo['@id'] }, inLanguage: 'en', publisher: { '@id': person['@id'] } }
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
      license: 'https://spdx.org/licenses/LGPL-2.0-or-later.html',
      softwareVersion: '4.x',
      author: { '@id': person['@id'] },
      maintainer: { '@id': person['@id'] },
      image: [{ '@id': logo['@id'] }, ogImage],
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
      inLanguage: 'en',
      image: ogImage,
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
