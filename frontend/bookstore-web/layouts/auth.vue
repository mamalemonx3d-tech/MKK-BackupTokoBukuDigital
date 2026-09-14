<template>
  <div 
    class="min-h-screen font-sans antialiased transition-colors duration-300 flex flex-col justify-center items-center p-4"
    :class="isDark ? 'bg-slate-950 text-slate-100' : 'bg-slate-100 text-slate-800'"
  >
    <main class="w-full">
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
const { isDark, initTheme } = useTheme()

onMounted(() => {
  initTheme()
})
</script>
