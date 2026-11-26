import { defineConfig } from 'vitepress'
import { seoHead, siteName, siteUrl } from './seo'

const repo = 'https://github.com/tomaj/nette-auto-variable-template'
const base = process.env.DOCS_BASE || '/nette-auto-variable-template/'
const description = 'Register variables once and use them in every Latte template of your Nette application: presenters, components and templates you create yourself.'

const descriptionSk = 'Zaregistrujte premenné raz a používajte ich v každej Latte šablóne vašej Nette aplikácie: v presenteroch, komponentoch aj v šablónach, ktoré si vytvoríte sami.'

const navEn = [
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
]

const navSk = [
  { text: 'Príručka', link: '/sk/guide/getting-started', activeMatch: '^/sk/guide/' },
  { text: 'Príklady', link: '/sk/examples/', activeMatch: '^/sk/examples/' },
  { text: 'API', link: '/sk/api/reference' },
  {
    text: '4.x',
    items: [
      { text: '4.x (master, zatiaľ nevydaná)', link: '/sk/' },
      { text: '3.x (README)', link: `${repo}/tree/3.0.0#readme` },
      { text: 'Prechod z 3.x na 4.0', link: '/sk/upgrade' },
      { text: 'Zoznam zmien', link: '/sk/changelog' },
      { text: 'Vydania', link: `${repo}/releases` },
    ],
  },
]

const sidebarEn = [
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
      { text: 'Lazy service', link: '/examples/lazy-service' },
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
]

const sidebarSk = [
  {
    text: 'Príručka',
    items: [
      { text: 'Začíname', link: '/sk/guide/getting-started' },
      { text: 'Ako to funguje', link: '/sk/guide/how-it-works' },
      { text: 'Konfigurácia', link: '/sk/guide/configuration' },
      { text: 'Presentery a komponenty', link: '/sk/guide/templates' },
      { text: 'Spúšťanie kódu: onCreate', link: '/sk/guide/on-create' },
      { text: 'Vlastné triedy šablón', link: '/sk/guide/template-classes' },
      { text: 'Testovanie', link: '/sk/guide/testing' },
      { text: 'Bezpečnosť', link: '/sk/guide/security' },
    ],
  },
  {
    text: 'Príklady',
    items: [
      { text: 'Prehľad', link: '/sk/examples/' },
      { text: 'Hello world', link: '/sk/examples/hello-world' },
      { text: 'Komponenty', link: '/sk/examples/components' },
      { text: 'onCreate: filtre a hodnoty pre každú šablónu', link: '/sk/examples/on-create' },
      { text: 'Ktorá hodnota vyhrá', link: '/sk/examples/precedence' },
      { text: 'Typované šablóny', link: '/sk/examples/typed-template' },
      { text: 'Bez Nette DI', link: '/sk/examples/without-di' },
      { text: 'Testovanie', link: '/sk/examples/testing' },
      { text: 'Lenivá služba', link: '/sk/examples/lazy-service' },
    ],
  },
  {
    text: 'Referencia',
    items: [
      { text: 'Referencia API', link: '/sk/api/reference' },
      { text: 'Časté otázky', link: '/sk/faq' },
      { text: 'Prechod na 4.0', link: '/sk/upgrade' },
      { text: 'Zoznam zmien', link: '/sk/changelog' },
      { text: 'Ako prispieť', link: '/sk/contributing' },
    ],
  },
]

export default defineConfig({
  title: siteName,
  description,
  base,
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
    return seoHead(pageData, siteConfig.srcDir, pageData.relativePath.startsWith('sk/') ? descriptionSk : description)
  },

  // English is the default locale at the site root; Slovak mirrors it under /sk/.
  locales: {
    root: {
      label: 'English',
      lang: 'en-US',
      description,
    },
    sk: {
      label: 'Slovenčina',
      lang: 'sk-SK',
      link: '/sk/',
      description: descriptionSk,
      themeConfig: {
        nav: navSk,
        sidebar: sidebarSk,
        outline: {
          level: [2, 3],
          label: 'Na tejto stránke',
        },
        docFooter: {
          prev: 'Predchádzajúca stránka',
          next: 'Nasledujúca stránka',
        },
        editLink: {
          pattern: `${repo}/edit/master/docs/:path`,
          text: 'Upraviť túto stránku na GitHube',
        },
        lastUpdated: {
          text: 'Naposledy upravené',
        },
        darkModeSwitchLabel: 'Vzhľad',
        lightModeSwitchTitle: 'Prepnúť na svetlý režim',
        darkModeSwitchTitle: 'Prepnúť na tmavý režim',
        sidebarMenuLabel: 'Ponuka',
        returnToTopLabel: 'Návrat na začiatok',
        langMenuLabel: 'Zmeniť jazyk',
        skipToContentLabel: 'Preskočiť na obsah',
        footer: {
          message: 'Vydané pod licenciou MIT.',
          copyright: 'Copyright © 2015-2026 Tomas Majer',
        },
      },
    },
  },

  themeConfig: {
    logo: { light: '/logo.svg', dark: '/logo-dark.svg', alt: siteName },

    nav: navEn,

    sidebar: sidebarEn,

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
      options: {
        locales: {
          sk: {
            translations: {
              button: {
                buttonText: 'Hľadať',
                buttonAriaLabel: 'Hľadať',
              },
              modal: {
                displayDetails: 'Zobraziť podrobný zoznam',
                resetButtonTitle: 'Vymazať hľadanie',
                backButtonTitle: 'Zavrieť hľadanie',
                noResultsText: 'Žiadne výsledky pre',
                footer: {
                  selectText: 'vybrať',
                  selectKeyAriaLabel: 'enter',
                  navigateText: 'prejsť',
                  navigateUpKeyAriaLabel: 'šípka hore',
                  navigateDownKeyAriaLabel: 'šípka dole',
                  closeText: 'zavrieť',
                  closeKeyAriaLabel: 'escape',
                },
              },
            },
          },
        },
      },
    },

    footer: {
      message: 'Released under the MIT license.',
      copyright: 'Copyright © 2015-2026 Tomas Majer',
    },
  },
})
