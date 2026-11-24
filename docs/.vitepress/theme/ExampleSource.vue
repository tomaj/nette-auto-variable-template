<script setup lang="ts">
// The box at the top of every example page: where the files are and how to run them.
defineProps<{ name: string }>()
const repo = 'https://github.com/tomaj/nette-auto-variable-template'
</script>

<template>
  <div class="example-source">
    <p class="example-source-label">Runnable project</p>
    <p>
      Every file below is included from <code>examples/{{ name }}</code>. CI runs <code>php run.php</code> there on
      every pull request and compares the output with <code>expected-output.txt</code>, so what you read here is what the
      code prints.
    </p>
    <a class="example-source-button" :href="`${repo}/tree/master/examples/${name}`">View source on GitHub</a>
  </div>
</template>
