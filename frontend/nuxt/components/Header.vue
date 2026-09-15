<template>
  <header 
    :class="isDark ? 'bg-[#09090b]/90 border-zinc-800 text-zinc-100 backdrop-blur-md' : 'bg-white/90 border-b-2 border-black text-black backdrop-blur-md'"
    class="sticky top-0 z-40 h-16 md:h-18 border-b px-4 md:px-8 flex items-center justify-between shrink-0 transition-colors duration-300"
  >
    <!-- Brand Logo on Mobile + Page Title -->
    <div class="flex items-center gap-3">
      <!-- Mobile Logo -->
      <NuxtLink to="/" class="md:hidden flex items-center gap-2 cursor-pointer">
        <div 
          :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
          class="w-8 h-8 rounded-xl border-2 flex items-center justify-center font-black text-sm"
        >
          B
        </div>
        <span class="font-black text-base tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
          TokoBuku<span :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">Digital</span>
        </span>
      </NuxtLink>

      <!-- Page Title -->
      <div class="hidden sm:block">
        <h2 class="text-sm font-black font-mono uppercase tracking-wider text-zinc-500">
          {{ pageTitle }}
        </h2>
      </div>
    </div>

    <!-- Right Controls: Theme Switcher & User Profile -->
    <div class="flex items-center gap-3">
      <!-- Theme Switcher Button -->
      <button 
        @click="toggleTheme()" 
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-amber-300 hover:bg-zinc-800' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-100'"
        class="flex items-center justify-center w-9 h-9 rounded-xl border transition-all duration-200 cursor-pointer hover:scale-105 active:scale-95"
        :title="isDark ? 'Ubah ke Tema Terang' : 'Ubah ke Tema Gelap'"
      >
        <span class="text-sm">{{ isDark ? '☀️' : '🌙' }}</span>
      </button>

      <!-- User Profile Badge Link -->
      <NuxtLink 
        to="/profile"
        :class="isDark ? 'bg-zinc-900 border-zinc-800 text-white hover:border-zinc-600 hover:bg-zinc-800/80' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-50 hover:shadow-[3px_3px_0px_#000000]'"
        class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl border transition-all duration-200 cursor-pointer hover:-translate-y-0.5 active:translate-y-0 group"
        title="Buka Pengaturan Profil Akun"
      >
        <div class="text-right leading-tight hidden sm:block">
          <div class="font-black text-xs group-hover:underline" :class="isDark ? 'text-white' : 'text-black'">
            {{ authStore.user?.name || 'User' }}
          </div>
          <div class="text-[9px] font-mono font-bold uppercase tracking-wider text-zinc-400">
            {{ authStore.isAdmin ? 'Admin' : 'Pembaca' }}
          </div>
        </div>
        <div 
          :class="isDark ? 'border-zinc-700 bg-white text-black' : 'border-2 border-black bg-black text-white'"
          class="w-7 h-7 rounded-lg border overflow-hidden flex items-center justify-center font-black text-xs shrink-0"
        >
          <img 
            v-if="userAvatarUrl && !imageLoadError" 
            :src="userAvatarUrl" 
            :alt="authStore.user?.name || 'User'"
            class="w-full h-full object-cover"
            @error="imageLoadError = true"
          />
          <span v-else>
            {{ (authStore.user?.name || 'U').charAt(0).toUpperCase() }}
          </span>
        </div>
      </NuxtLink>

      <!-- Mobile Nav Menu Drawer Toggle -->
      <button 
        @click="showMobileMenu = !showMobileMenu" 
        :class="isDark ? 'bg-zinc-900 text-white border-zinc-700' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000]'"
        class="md:hidden p-2 rounded-xl border active:scale-95 cursor-pointer"
        aria-label="Toggle Menu"
      >
        <span class="text-base font-bold">☰</span>
      </button>
    </div>

    <!-- Mobile Drawer Overlay Menu -->
    <div v-if="showMobileMenu" class="md:hidden fixed inset-0 z-50 flex flex-col p-6 transition-colors" :class="isDark ? 'bg-zinc-950/95 backdrop-blur-md text-white' : 'bg-white/95 backdrop-blur-md text-black'">
      <div class="flex items-center justify-between border-b-2 pb-4 mb-6" :class="isDark ? 'border-zinc-800' : 'border-black'">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-xl border-2 flex items-center justify-center font-black text-sm" :class="isDark ? 'bg-white text-black border-white' : 'bg-black text-white border-black'">B</div>
          <span class="font-black text-lg">TokoBukuDigital</span>
        </div>
        <button @click="showMobileMenu = false" class="font-black text-sm px-3 py-1.5 rounded-xl border-2 cursor-pointer" :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white' : 'bg-white border-black text-black shadow-[2px_2px_0px_#000000]'">✕</button>
      </div>

      <nav class="space-y-2 flex-1 overflow-y-auto">
        <NuxtLink 
          v-for="item in mobileNavItems" 
          :key="item.path" 
          :to="item.path"
          @click="showMobileMenu = false"
          class="flex items-center justify-between p-3.5 rounded-xl text-xs font-black border-2 transition-colors cursor-pointer"
          :class="[
            route.path === item.path 
              ? (isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]') 
              : (isDark ? 'bg-zinc-900 text-zinc-300 border-zinc-800' : 'bg-white text-black border-zinc-300 shadow-[1px_1px_0px_#000000]')
          ]"
        >
          <span>{{ item.label }}</span>
          <span class="text-xs font-mono">&rarr;</span>
        </NuxtLink>
      </nav>

      <div class="pt-4 border-t-2" :class="isDark ? 'border-zinc-800' : 'border-black'">
        <button 
          @click="handleLogout" 
          :class="isDark ? 'bg-rose-950/40 text-rose-300 border-rose-800' : 'bg-rose-50 text-rose-700 border-2 border-rose-600 shadow-[2px_2px_0px_#e11d48]'"
          class="w-full py-3 font-black text-xs rounded-xl transition-colors border-2 cursor-pointer"
        >
          Keluar / Logout
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
  '/profile': 'Profil Akun',
  '/admin/profile': 'Profil Administrator',
  '/user/profile': 'Profil Pengguna',
  '/admin/kategori': 'Kelola Kategori',
  '/admin/buku': 'Katalog Buku',
  '/admin/pengguna': 'Kelola Pengguna',
  '/admin/kasir': 'Kasir & Scan QR',
  '/admin/laporan': 'Laporan Penjualan',
  '/admin/chat': 'Live Chat Admin',
  '/katalog': 'Katalog Buku',
  '/keranjang': 'Keranjang Belanja',
  '/riwayat': 'Pesanan Anda',
  '/chat': 'Live Chat Bantuan',
  '/user/katalog': 'Katalog Buku',
  '/user/keranjang': 'Keranjang Belanja',
  '/user/riwayat': 'Pesanan Anda',
  '/user/chat': 'Live Chat Bantuan'
}

const pageTitle = computed(() => {
  return pageTitleMap[route.path] || (route.meta.title as string) || 'TokoBukuDigital'
})

const adminMobileNav = [
  { label: 'Kelola Kategori', path: '/admin/kategori' },
  { label: 'Katalog Buku', path: '/admin/buku' },
  { label: 'Kelola Pengguna', path: '/admin/pengguna' },
  { label: 'Kasir & Scan QR Code', path: '/admin/kasir' },
  { label: 'Laporan Penjualan', path: '/admin/laporan' },
  { label: 'Live Chat', path: '/admin/chat' },
  { label: 'Profil Saya', path: '/profile' },
]

const userMobileNav = [
  { label: 'Katalog Buku', path: '/katalog' },
  { label: 'Keranjang Belanja', path: '/keranjang' },
  { label: 'Pesanan Anda', path: '/riwayat' },
  { label: 'Live Chat Bantuan', path: '/chat' },
  { label: 'Profil Saya', path: '/profile' },
]

const mobileNavItems = computed(() => {
  return authStore.isAdmin ? adminMobileNav : userMobileNav
})

const imageLoadError = ref(false)

const userAvatarUrl = computed(() => {
  if (!authStore.user?.foto) return null
  const photo = authStore.user.foto
  if (photo.startsWith('http://localhost/')) {
    return photo.replace('http://localhost/', `${api.apiBase}/`)
  }
  if (photo.startsWith('http') || photo.startsWith('data:')) {
    return photo
  }
  return `${api.apiBase}/storage/${photo.replace(/^\/+/, '')}`
})

watch(() => authStore.user?.foto, () => {
  imageLoadError.value = false
})

const confirmModal = useConfirmModal()

const handleLogout = async () => {
  showMobileMenu.value = false
  const isConfirmed = await confirmModal.open({
    title: 'Konfirmasi Keluar',
    message: 'Apakah Anda yakin ingin keluar dari sesi akun TokoBukuDigital ini?',
    confirmText: 'Ya, Keluar',
    cancelText: 'Batal',
    type: 'info',
    animation: 'info'
  })
  if (isConfirmed) {
    await authStore.logout()
  }
}
</script>
