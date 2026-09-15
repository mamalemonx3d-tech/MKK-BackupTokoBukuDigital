<template>
  <div>
    <slot />
  </div>
</template>

<script setup lang="ts">
navigateTo('/admin/buku', { replace: true })
</script>
