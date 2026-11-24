// Strings that the custom theme components render. Pages carry their own text;
// these are the labels inside components shared by the English and Slovak trees.
import { computed } from 'vue'
import { useData } from 'vitepress'

const en = {
  runnableProject: 'Runnable project',
  exampleText: (name: string) => `Every file below is included from <code>examples/${name}</code>. CI runs <code>php run.php</code> there on every pull request and compares the output with <code>expected-output.txt</code>, so what you read here is what the code prints.`,
  viewSource: 'View source on GitHub',
  notFoundTitle: 'Page not found',
  notFoundText: 'This page does not exist or has moved. Try one of these:',
  notFoundLinks: [['Home', '/'], ['Getting started', '/guide/getting-started'], ['Examples', '/examples/'], ['API reference', '/api/reference'], ['FAQ', '/faq']] as [string, string][],
}

export type Strings = typeof en

const sk: Strings = {
  runnableProject: 'Spustiteľný projekt',
  exampleText: (name) => `Každý súbor nižšie sa načítava z <code>examples/${name}</code>. CI tam pri každom pull requeste spustí <code>php run.php</code> a porovná výstup s <code>expected-output.txt</code>, takže to, čo tu čítate, kód naozaj vypíše.`,
  viewSource: 'Zdrojový kód na GitHube',
  notFoundTitle: 'Stránka sa nenašla',
  notFoundText: 'Táto stránka neexistuje alebo sa presunula. Skúste niektorú z týchto:',
  notFoundLinks: [['Úvod', '/sk/'], ['Začíname', '/sk/guide/getting-started'], ['Príklady', '/sk/examples/'], ['Referencia API', '/sk/api/reference'], ['Časté otázky', '/sk/faq']],
}

export function stringsFor(lang: string): Strings {
  return lang.startsWith('sk') ? sk : en
}

export function useStrings() {
  const { lang } = useData()
  return computed(() => stringsFor(lang.value))
}
