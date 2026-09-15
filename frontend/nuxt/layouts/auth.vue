<template>
  <div 
    class="min-h-screen font-sans antialiased transition-colors duration-300 flex flex-col justify-between p-3 sm:p-6 overflow-x-hidden"
    :class="isDark ? 'bg-[#09090b] text-[#f4f4f5]' : 'bg-[#fafafa] text-[#09090b]'"
  >
    <!-- Top Bar Navigation with Logo, Back Link, and Theme Toggle -->
    <header class="w-full max-w-5xl mx-auto flex items-center justify-between py-2 sm:py-4 px-2">
      <NuxtLink 
        to="/" 
        class="flex items-center gap-2 group text-xs sm:text-sm font-bold transition-transform hover:-translate-x-0.5"
      >
        <span class="w-8 h-8 rounded-lg border-2 flex items-center justify-center font-mono text-xs font-black transition-all"
          :class="isDark ? 'bg-white text-black border-white' : 'bg-black text-white border-black'"
        >
          &larr;
        </span>
        <span class="hidden sm:inline font-mono">Kembali ke Beranda</span>
        <span class="sm:hidden font-mono">Beranda</span>
      </NuxtLink>

      <!-- Theme Switcher Button (☀️ / 🌙) -->
      <button 
        @click="toggleTheme" 
        :title="isDark ? 'Ganti ke Tema Terang (Light Mode)' : 'Ganti ke Tema Gelap (Dark Mode)'"
        class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl border-2 transition-all flex items-center justify-center cursor-pointer hover:scale-105 active:scale-95 text-sm sm:text-base"
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-amber-300 hover:bg-zinc-800' : 'bg-white border-black text-zinc-900 hover:bg-zinc-100 shadow-[2px_2px_0px_#000000]'"
      >
        <span v-if="isDark" class="select-none">☀️</span>
        <span v-else class="select-none">🌙</span>
      </button>
    </header>

    <!-- Main Auth Form Container (Fully responsive from 320px up) -->
    <main class="w-full flex-1 flex items-center justify-center my-4 sm:my-8 px-1 sm:px-4">
      <slot />
    </main>

    <!-- Footer Copyright -->
    <footer class="w-full max-w-5xl mx-auto text-center py-3 text-[11px] font-mono opacity-50">
      &copy; 2026 TokoBukuDigital. All rights reserved.
    </footer>

    <!-- Toast Notifications -->
    <Toast />
  </div>
</template>

<script setup lang="ts">
const { isDark, toggleTheme, initTheme } = useTheme()

onMounted(() => {
  initTheme()
})
</script>
