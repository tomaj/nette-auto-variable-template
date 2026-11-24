<script setup lang="ts">
import { useRoute, withBase } from 'vitepress'
import { computed } from 'vue'
import { stringsFor } from './i18n'

// The 404 page has no locale of its own: pick the language from the requested path.
const route = useRoute()
const t = computed(() => stringsFor(route.path.startsWith(withBase('/sk/')) ? 'sk' : 'en'))
</script>

<template>
  <div class="navt-404">
    <p class="code">{$404}</p>
    <h1>{{ t.notFoundTitle }}</h1>
    <p>{{ t.notFoundText }}</p>
    <ul><li v-for="[name, url] in t.notFoundLinks" :key="url"><a :href="withBase(url)">{{ name }}</a></li></ul>
  </div>
</template>

<style>
.navt-404 { max-width: 640px; margin: 0 auto; padding: 64px 24px 96px; text-align: center; }
.navt-404 .code { font-family: var(--vp-font-family-mono); font-size: 56px; font-weight: 700; color: var(--vp-c-brand-1); line-height: 1; }
.navt-404 h1 { margin: 24px 0 8px; font-size: 24px; font-weight: 600; }
.navt-404 ul { list-style: none; padding: 0; display: flex; flex-wrap: wrap; gap: 8px 16px; justify-content: center; }
.navt-404 a { color: var(--vp-c-brand-1); font-weight: 500; }
</style>
