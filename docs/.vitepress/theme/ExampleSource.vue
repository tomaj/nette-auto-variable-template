<script setup lang="ts">
// The box at the top of every example page: where the files are and how to run them.
import { useStrings } from './i18n'

defineProps<{ name: string }>()
const t = useStrings()
const repo = 'https://github.com/tomaj/nette-auto-variable-template'
</script>

<template>
  <div class="example-source">
    <p class="example-source-label">{{ t.runnableProject }}</p>
    <p v-html="t.exampleText(name)" />
    <a class="example-source-button" :href="`${repo}/tree/master/examples/${name}`">{{ t.viewSource }}</a>
  </div>
</template>
