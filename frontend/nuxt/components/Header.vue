<template>
  <header 
    :class="isDark ? 'bg-slate-900/80 border-slate-800 text-slate-200 shadow-sm backdrop-blur-sm' : 'bg-[#FAF7F0] border-b-2.5 border-[#1A1A1A] text-slate-900 shadow-none'"
    class="sticky top-0 z-50 h-16 md:h-20 border-b px-4 md:px-8 flex items-center justify-between shrink-0 transition-colors duration-200"
  >
    <!-- Brand Logo on Mobile + Page Title -->
    <div class="flex items-center gap-3">
      <!-- Mobile Logo -->
      <NuxtLink to="/" class="md:hidden flex items-center gap-2">
        <div 
          :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'"
          class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm"
        >
          📚
        </div>
        <span class="font-black text-lg tracking-tight" :class="isDark ? 'text-white' : 'text-slate-900'">
          Book<span :class="isDark ? 'text-indigo-500' : 'text-black bg-[#C8F53F] px-1 rounded border border-black'">Store</span>
        </span>
      </NuxtLink>

      <!-- Divider on Mobile -->
      <span class="md:hidden font-light" :class="isDark ? 'text-slate-700' : 'text-slate-400'">|</span>

      <!-- Page Title -->
    </div>

    <!-- Right Controls: Theme Switcher & User Profile -->
    <div class="flex items-center gap-3"
      <!-- Theme Switcher Button -->
      <button 
        @click="toggleTheme()" 
        :class="isDark ? 'bg-slate-800 border border-slate-700 text-slate-300 hover:bg-slate-700' : 'bg-[#FFE566] border-2 border-[#1A1A1A] text-black shadow-[2.5px_2.5px_0px_#1A1A1A] hover:translate-x-[-1px] hover:translate-y-[-1px] active:translate-x-[1px] active:translate-y-[1px]'"
        class="flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black transition-all duration-200"
        :title="isDark ? 'Ubah ke Tema Retro Terang' : 'Ubah ke Tema Gelap'"
      >
        <span class="text-sm">{{ isDark ? '🌙' : '☀️' }}</span>
      </button>

      <!-- User Profile Badge -->
      <div 
        :class="isDark ? 'bg-slate-800/90 border-slate-700/80 text-white' : 'bg-[#FFF8EC] border-2 border-[#1A1A1A] shadow-[2.5px_2.5px_0px_#1A1A1A] text-slate-900'"
        class="flex items-center gap-2.5 px-3 py-1.5 rounded-full"
      >
        <div class="text-right leading-tight hidden sm:block">
          <div class="font-extrabold text-xs" :class="isDark ? 'text-white' : 'text-slate-900'">
            {{ authStore.user?.name || 'User' }}
          </div>
          <div class="text-[10px] font-bold uppercase tracking-wider" :class="isDark ? 'text-indigo-400' : 'text-black bg-[#D4B8FF] px-1 rounded border border-black'">
            {{ authStore.isAdmin ? 'Admin Kasir' : (authStore.user?.role || 'Pelanggan') }}
          </div>
        </div>
        <div 
          :class="isDark ? 'ring-2 ring-indigo-500 bg-indigo-900 text-white' : 'border-2 border-black bg-[#FFD4A3] text-black shadow-[1.5px_1.5px_0px_#1A1A1A]'"
          class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center font-black text-xs shrink-0"
        >
          <img 
            v-if="userAvatarUrl" 
            :src="userAvatarUrl" 
            :alt="authStore.user?.name || 'User'"
            class="w-full h-full object-cover"
          />
          <span v-else>
            {{ (authStore.user?.name || 'U').charAt(0).toUpperCase() }}
          </span>
        </div>
      </div>

      <!-- Mobile Nav Menu Drawer Toggle -->
      <button 
        @click="showMobileMenu = !showMobileMenu" 
        :class="isDark ? 'bg-slate-800 text-slate-200 border-slate-700' : 'bg-[#FFF8EC] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'"
        class="md:hidden p-2 rounded-xl active:scale-95"
      >
        <span class="text-lg">☰</span>
      </button>
    </div>

    <!-- Mobile Drawer Overlay Menu -->
    <div v-if="showMobileMenu" class="md:hidden fixed inset-0 z-50 flex flex-col p-6 transition-colors" :class="isDark ? 'bg-slate-950/80 backdrop-blur-md' : 'bg-[#FAF7F0]/95 backdrop-blur-md'">
      <div class="flex items-center justify-between border-b-2 border-black pb-4 mb-6">
        <div class="flex items-center gap-2">
          <div class="w-9 h-9 rounded-xl bg-[#C8F53F] border-2 border-black text-black flex items-center justify-center font-bold shadow-[2px_2px_0px_#1A1A1A]">📚</div>
          <span class="font-black text-xl text-slate-900">Menu BookStore</span>
        </div>
        <button @click="showMobileMenu = false" class="font-black text-lg p-2 rounded-xl border-2 border-black bg-[#FFB7B2] text-black shadow-[2px_2px_0px_#1A1A1A]">✕</button>
      </div>

      <!-- Mobile Theme Toggle -->
      <div class="mb-4">
        <button 
          @click="toggleTheme()" 
          :class="isDark ? 'bg-slate-900 border-slate-700 text-white' : 'bg-[#FFE566] border-2 border-black text-black shadow-[3px_3px_0px_#1A1A1A]'"
          class="w-full py-3 font-black text-xs rounded-2xl flex items-center justify-center gap-2"
        >
          <span>Mode Tema: {{ isDark ? '🌙 Mode Gelap' : '☀️ Mode Retro Terang' }}</span>
        </button>
      </div>

      <nav class="space-y-2 flex-1 overflow-y-auto">
        <NuxtLink 
          v-for="item in mobileNavItems" 
          :key="item.path" 
          :to="item.path"
          @click="showMobileMenu = false"
          class="flex items-center justify-between p-3.5 rounded-2xl text-sm font-extrabold border-2 transition-colors"
          :class="[
            route.path === item.path 
              ? (isDark ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-[#C8F53F] text-black border-black shadow-[3px_3px_0px_#1A1A1A]') 
              : (isDark ? 'bg-slate-900 text-slate-300 border-slate-800' : 'bg-[#FFF8EC] text-slate-900 border-black shadow-[2px_2px_0px_#1A1A1A]')
          ]"
        >
          <span>{{ item.label }}</span>
          <span class="text-xs font-mono">&rarr;</span>
        </NuxtLink>
      </nav>

      <div class="pt-4 border-t-2 border-black">
        <button 
          @click="handleLogout" 
          :class="isDark ? 'bg-rose-600/20 text-rose-300 border-rose-500/30' : 'bg-[#FFB7B2] text-black border-2 border-black shadow-[3px_3px_0px_#1A1A1A]'"
          class="w-full py-3 font-black text-sm rounded-2xl transition-colors"
        >
          🚪 Logout / Keluar
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
const route = useRoute()
const api = useApi()
const authStore = useAuthStore()
const { isDark, toggleTheme, initTheme } = useTheme()

const showMobileMenu = ref(false)

onMounted(() => {
  initTheme()
})

const pageTitleMap: Record<string, string> = {
  '/admin/kategori': 'Kelola Kategori',
  '/admin/buku': 'Katalog Buku',
  '/admin/pengguna': 'Kelola Pengguna',
  '/admin/kasir': 'Kasir & Scan QR',
  '/admin/laporan': 'Laporan Penjualan',
  '/admin/chat': 'Live Chat Admin',
  '/user/katalog': 'Katalog Buku',
  '/user/keranjang': 'Keranjang Belanja',
  '/user/riwayat': 'Riwayat Pesanan',
  '/user/chat': 'Live Chat Bantuan'
}

const pageTitle = computed(() => {
  return pageTitleMap[route.path] || (route.meta.title as string) || 'BookStore'
})

const adminMobileNav = [
  { label: '🏷️ Kelola Kategori', path: '/admin/kategori' },
  { label: '📚 Katalog Buku', path: '/admin/buku' },
  { label: '👥 Kelola Pengguna', path: '/admin/pengguna' },
  { label: '🛒 Kasir & Scan QR Code', path: '/admin/kasir' },
  { label: '📊 Laporan Penjualan', path: '/admin/laporan' },
  { label: '💬 Live Chat', path: '/admin/chat' },
]

const userMobileNav = [
  { label: '📚 Katalog Buku', path: '/user/katalog' },
  { label: '🛒 Keranjang Belanja', path: '/user/keranjang' },
  { label: '📋 Riwayat Pesanan', path: '/user/riwayat' },
  { label: '💬 Live Chat', path: '/user/chat' },
]

const mobileNavItems = computed(() => {
  return authStore.isAdmin ? adminMobileNav : userMobileNav
})

const userAvatarUrl = computed(() => {
  if (!authStore.user?.foto) return null
  if (authStore.user.foto.startsWith('http')) return authStore.user.foto
  return `${api.apiBase}/storage/${authStore.user.foto}`
})

const handleLogout = async () => {
  showMobileMenu.value = false
  if (confirm('Apakah Anda yakin ingin keluar?')) {
    await authStore.logout()
  }
}
</script>
