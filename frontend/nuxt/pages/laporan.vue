<template>
  <div>
    <slot />
  </div>
</template>

<script setup lang="ts">
navigateTo('/admin/laporan', { replace: true })
</script>
