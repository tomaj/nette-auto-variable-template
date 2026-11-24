import { defineConfig } from 'vitepress'
import { seoHead, siteName, siteUrl } from './seo'

const repo = 'https://github.com/tomaj/nette-auto-variable-template'
const base = process.env.DOCS_BASE || '/nette-auto-variable-template/'
const description = 'Register variables once and use them in every Latte template of your Nette application: presenters, components and templates you create yourself.'

export default defineConfig({
  title: siteName,
  description,
  base,
  lang: 'en',
  cleanUrls: true,
  lastUpdated: true,
  srcExclude: ['public/**', 'scripts/**'],

  sitemap: {
    hostname: siteUrl,
  },

  head: [
    ['link', { rel: 'icon', href: `${base}favicon.ico`, sizes: '48x48' }],
    ['link', { rel: 'icon', type: 'image/svg+xml', href: `${base}favicon.svg` }],
    ['link', { rel: 'icon', type: 'image/png', sizes: '32x32', href: `${base}favicon-32.png` }],
    ['link', { rel: 'icon', type: 'image/png', sizes: '16x16', href: `${base}favicon-16.png` }],
    ['link', { rel: 'apple-touch-icon', sizes: '180x180', href: `${base}apple-touch-icon.png` }],
    ['link', { rel: 'manifest', href: `${base}site.webmanifest` }],
    ['meta', { name: 'theme-color', content: '#0f766e' }],
  ],

  markdown: {
    languageAlias: { latte: 'html', neon: 'yaml' },
  },

  transformHead({ pageData, siteConfig }) {
    return seoHead(pageData, siteConfig.srcDir, description)
  },

  themeConfig: {
    logo: { light: '/logo.svg', dark: '/logo-dark.svg', alt: siteName },

    nav: [
      { text: 'Guide', link: '/guide/getting-started', activeMatch: '^/guide/' },
      { text: 'Examples', link: '/examples/', activeMatch: '^/examples/' },
      { text: 'API', link: '/api/reference' },
      {
        text: '4.x',
        items: [
          { text: '4.x (master, unreleased)', link: '/' },
          { text: '3.x (README)', link: `${repo}/tree/3.0.0#readme` },
          { text: 'Upgrading 3.x → 4.0', link: '/upgrade' },
          { text: 'Changelog', link: '/changelog' },
          { text: 'Releases', link: `${repo}/releases` },
        ],
      },
    ],

    sidebar: [
      {
        text: 'Guide',
        items: [
          { text: 'Getting started', link: '/guide/getting-started' },
          { text: 'How it works', link: '/guide/how-it-works' },
          { text: 'Configuration', link: '/guide/configuration' },
          { text: 'Presenters and components', link: '/guide/templates' },
          { text: 'Running code: onCreate', link: '/guide/on-create' },
          { text: 'Custom template classes', link: '/guide/template-classes' },
          { text: 'Testing', link: '/guide/testing' },
          { text: 'Security', link: '/guide/security' },
        ],
      },
      {
        text: 'Examples',
        items: [
          { text: 'Overview', link: '/examples/' },
          { text: 'Hello world', link: '/examples/hello-world' },
          { text: 'Components', link: '/examples/components' },
          { text: 'onCreate: filters and per-template values', link: '/examples/on-create' },
          { text: 'Which value wins', link: '/examples/precedence' },
          { text: 'Typed templates', link: '/examples/typed-template' },
          { text: 'Without Nette DI', link: '/examples/without-di' },
          { text: 'Testing', link: '/examples/testing' },
        ],
      },
      {
        text: 'Reference',
        items: [
          { text: 'API reference', link: '/api/reference' },
          { text: 'FAQ', link: '/faq' },
          { text: 'Upgrading to 4.0', link: '/upgrade' },
          { text: 'Changelog', link: '/changelog' },
          { text: 'Contributing', link: '/contributing' },
        ],
      },
    ],

    socialLinks: [{ icon: 'github', link: repo }],

    editLink: {
      pattern: `${repo}/edit/master/docs/:path`,
      text: 'Edit this page on GitHub',
    },

    lastUpdated: {
      text: 'Last updated',
    },

    outline: {
      level: [2, 3],
    },

    search: {
      provider: 'local',
    },

    footer: {
      message: 'Released under the LGPL-2.0-or-later license.',
      copyright: 'Copyright © 2015-2026 Tomas Majer',
    },
  },
})
