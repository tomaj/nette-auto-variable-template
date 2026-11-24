import DefaultTheme from 'vitepress/theme'
import type { Theme } from 'vitepress'
import Layout from './Layout.vue'
import LinkCards from './LinkCards.vue'
import ExampleSource from './ExampleSource.vue'
import './custom.css'

export default {
  extends: DefaultTheme,
  Layout,
  enhanceApp({ app }) {
    app.component('LinkCards', LinkCards)
    app.component('ExampleSource', ExampleSource)
  },
} satisfies Theme
