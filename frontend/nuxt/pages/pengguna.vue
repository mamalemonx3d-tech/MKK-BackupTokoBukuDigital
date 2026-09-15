<template>
  <div>
    <slot />
  </div>
</template>

<script setup lang="ts">
navigateTo('/admin/pengguna', { replace: true })
</script>
