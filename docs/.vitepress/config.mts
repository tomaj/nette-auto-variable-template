import { defineConfig } from 'vitepress'

const repo = 'https://github.com/tomaj/nette-auto-variable-template'
const description = 'Nette template factory that assigns registered variables to every Latte template.'

export default defineConfig({
  title: 'Nette Auto Variable Template',
  description,
  base: '/nette-auto-variable-template/',
  cleanUrls: true,
  lastUpdated: true,

  sitemap: {
    hostname: 'https://tomaj.github.io/nette-auto-variable-template/',
  },

  head: [
    ['link', { rel: 'icon', type: 'image/svg+xml', href: '/nette-auto-variable-template/favicon.svg' }],
    ['meta', { property: 'og:type', content: 'website' }],
    ['meta', { property: 'og:title', content: 'Nette Auto Variable Template' }],
    ['meta', { property: 'og:description', content: description }],
    ['meta', { property: 'og:url', content: 'https://tomaj.github.io/nette-auto-variable-template/' }],
  ],

  themeConfig: {
    logo: '/logo.svg',

    nav: [
      { text: 'Getting started', link: '/guide/getting-started' },
      { text: 'API', link: '/api/reference' },
      {
        text: '4.x',
        items: [
          { text: '4.x (master, unreleased)', link: '/' },
          { text: '3.x (README)', link: `${repo}/tree/3.0.0#readme` },
          { text: 'Upgrading 3.x → 4.0', link: `${repo}/blob/master/UPGRADE-4.0.md` },
        ],
      },
      { text: 'Changelog', link: '/changelog' },
      { text: 'Releases', link: `${repo}/releases` },
    ],

    sidebar: [
      {
        text: 'Documentation',
        items: [
          { text: 'Getting started', link: '/guide/getting-started' },
          { text: 'API reference', link: '/api/reference' },
          { text: 'Changelog', link: '/changelog' },
        ],
      },
    ],

    socialLinks: [{ icon: 'github', link: repo }],

    editLink: {
      pattern: `${repo}/edit/master/docs/:path`,
      text: 'Edit this page on GitHub',
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
