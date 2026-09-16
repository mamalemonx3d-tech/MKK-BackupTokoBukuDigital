<template>
  <aside 
    :class="[
      isDark ? 'bg-[#09090b] border-zinc-800 text-zinc-100' : 'bg-white border-r-2 border-black text-black',
      isCollapsed ? 'w-20 p-3' : 'w-64 p-5'
    ]"
    class="hidden md:flex h-screen sticky top-0 flex-col justify-between select-none shrink-0 transition-all duration-300 ease-in-out border-r z-30"
  >
    <!-- Top Section -->
    <div class="flex flex-col flex-1 min-h-0">
      <!-- Header / Logo & Collapse Toggle -->
      <div class="flex items-center justify-between pb-4 mb-3 border-b" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
        <!-- Expanded Brand Logo -->
        <NuxtLink 
          v-if="!isCollapsed" 
          to="/" 
          class="flex items-center gap-3 group cursor-pointer overflow-hidden"
          title="Ke Halaman Utama"
        >
          <div 
            :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
            class="w-9 h-9 rounded-xl border-2 flex items-center justify-center font-black text-base transition-transform duration-200 group-hover:scale-105 shrink-0"
          >
            B
          </div>
          <div class="flex flex-col min-w-0">
            <span class="font-black text-base tracking-tight leading-tight truncate" :class="isDark ? 'text-white' : 'text-black'">
              TokoBuku<span :class="isDark ? 'text-zinc-400' : 'text-zinc-500'">Digital</span>
            </span>
            <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-zinc-400">
              {{ authStore.isAdmin ? 'Admin Console' : 'Platform Buku' }}
            </span>
          </div>
        </NuxtLink>

        <!-- Collapsed Brand Logo -->
        <NuxtLink 
          v-else 
          to="/" 
          class="mx-auto flex items-center justify-center cursor-pointer group"
          title="TokoBukuDigital - Beranda"
        >
          <div 
            :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
            class="w-10 h-10 rounded-xl border-2 flex items-center justify-center font-black text-base transition-transform duration-200 group-hover:scale-105"
          >
            B
          </div>
        </NuxtLink>

        <!-- Toggle Collapse Button (When Expanded) -->
        <button
          v-if="!isCollapsed"
          type="button"
          @click="toggle"
          :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-300 hover:text-white hover:bg-zinc-800' : 'bg-zinc-100 border-2 border-black text-black shadow-[1.5px_1.5px_0px_#000000] hover:bg-zinc-200'"
          class="w-7 h-7 rounded-lg border flex items-center justify-center text-xs font-black transition-all cursor-pointer hover:scale-105 shrink-0"
          title="Tutup / Kecilkan Sidebar"
        >
          <LucidePanelLeftClose class="w-3.5 h-3.5" />
        </button>
      </div>

      <!-- Toggle Button (When Collapsed) -->
      <div v-if="isCollapsed" class="flex justify-center mb-3">
        <button
          type="button"
          @click="toggle"
          :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-300 hover:text-white hover:bg-zinc-800' : 'bg-zinc-100 border-2 border-black text-black shadow-[1.5px_1.5px_0px_#000000] hover:bg-zinc-200'"
          class="w-8 h-8 rounded-xl border flex items-center justify-center text-xs font-black transition-all cursor-pointer hover:scale-105"
          title="Buka / Lebarkan Sidebar"
        >
          <LucidePanelLeftOpen class="w-4 h-4" />
        </button>
      </div>

      <!-- Sidebar Search Box (When Expanded) -->
      <div v-if="!isCollapsed" class="relative mb-3">
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Cari menu navigasi..." 
          :class="isDark ? 'bg-zinc-950 border-zinc-800 text-white placeholder-zinc-500 focus:border-white' : 'bg-zinc-50 border-2 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[3px_3px_0px_#000000] placeholder-zinc-400'"
          class="w-full pl-8 pr-3 py-2 text-xs rounded-xl outline-none transition-all font-bold border-2"
        />
        <LucideSearch class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-2.5" />
      </div>

      <!-- Navigation Menu List -->
      <nav class="space-y-1 overflow-y-auto flex-1 pr-1 custom-scrollbar">
        <template v-for="item in filteredNavItems" :key="item.path">
          <!-- Expanded Menu Item -->
          <NuxtLink 
            v-if="!isCollapsed"
            :to="item.path" 
            class="group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-black transition-all duration-200 cursor-pointer border-2"
            :class="[
              isRouteActive(item.path) 
                ? (isDark ? 'text-black bg-white border-white shadow-[2px_2px_0px_#ffffff]' : 'text-white bg-black border-black shadow-[2px_2px_0px_#000000]') 
                : (isDark ? 'text-zinc-400 border-transparent hover:text-white hover:bg-zinc-900 hover:border-zinc-800' : 'text-zinc-700 border-transparent hover:text-black hover:bg-zinc-100 hover:border-zinc-300')
            ]"
          >
            <component 
              :is="item.icon" 
              class="w-4 h-4 transition-transform group-hover:scale-110 shrink-0" 
              :class="isRouteActive(item.path) ? (isDark ? 'text-black' : 'text-white') : 'text-zinc-400'" 
            />
            <span class="truncate flex-1">{{ item.label }}</span>
            <span 
              v-if="item.path.includes('/chat') && unreadCount > 0"
              class="ml-auto px-1.5 py-0.5 rounded-full bg-rose-500 text-white text-[9px] font-black shrink-0 animate-pulse"
            >
              {{ unreadCount }}
            </span>
          </NuxtLink>

          <!-- Collapsed Menu Item with Floating Tooltip -->
          <div v-else class="relative group flex justify-center">
            <NuxtLink 
              :to="item.path" 
              class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-200 cursor-pointer border-2 relative"
              :class="[
                isRouteActive(item.path) 
                  ? (isDark ? 'text-black bg-white border-white shadow-[2px_2px_0px_#ffffff]' : 'text-white bg-black border-black shadow-[2px_2px_0px_#000000]') 
                  : (isDark ? 'text-zinc-400 border-transparent hover:text-white hover:bg-zinc-900 hover:border-zinc-800' : 'text-zinc-700 border-transparent hover:text-black hover:bg-zinc-100 hover:border-zinc-300')
              ]"
            >
              <component :is="item.icon" class="w-4 h-4 shrink-0" />
              <span 
                v-if="item.path.includes('/chat') && unreadCount > 0"
                class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-rose-500 text-white text-[8px] font-black flex items-center justify-center"
              >
                {{ unreadCount }}
              </span>
            </NuxtLink>

            <!-- Tooltip on Hover -->
            <div 
              :class="isDark ? 'bg-zinc-900 text-white border-zinc-700 shadow-xl' : 'bg-black text-white border-black shadow-lg'"
              class="absolute left-full ml-3 top-1/2 -translate-y-1/2 px-2.5 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity duration-200 border z-50 shadow-md"
            >
              {{ item.label }}
            </div>
          </div>

          <!-- Divider -->
          <div v-if="item.divider" class="my-2 border-t" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'"></div>
        </template>
      </nav>
    </div>

    <!-- Bottom Section: Logout -->
    <div class="pt-3 mt-2 border-t shrink-0" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
      <!-- Logout Button (Expanded) -->
      <button 
        v-if="!isCollapsed"
        @click="handleLogout" 
        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-black transition-all duration-200 cursor-pointer border-2 hover:-translate-y-0.5"
        :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-300 hover:bg-rose-950/40 hover:text-rose-400 hover:border-rose-800' : 'bg-white border-black text-black shadow-[2px_2px_0px_#000000] hover:bg-rose-50 hover:text-rose-600 hover:border-rose-600'"
      >
        <div class="flex items-center gap-2.5">
          <LucideLogOut class="w-4 h-4" />
          <span>Keluar Akun</span>
        </div>
        <span class="text-[10px] font-mono opacity-50">&rarr;</span>
      </button>

      <!-- Logout Button (Collapsed) -->
      <div v-else class="relative group flex justify-center">
        <button 
          @click="handleLogout" 
          :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-300 hover:bg-rose-950/40 hover:text-rose-400 hover:border-rose-800' : 'bg-white border-2 border-black text-black shadow-[1.5px_1.5px_0px_#000000] hover:bg-rose-50 hover:text-rose-600'"
          class="w-10 h-10 rounded-xl border flex items-center justify-center transition-all cursor-pointer hover:scale-105"
          title="Keluar / Logout"
        >
          <LucideLogOut class="w-4 h-4" />
        </button>

        <!-- Tooltip on Hover -->
        <div 
          :class="isDark ? 'bg-rose-950 text-rose-200 border-rose-800' : 'bg-black text-white border-black'"
          class="absolute left-full ml-3 top-1/2 -translate-y-1/2 px-2.5 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity duration-200 border z-50 shadow-md"
        >
          Keluar / Logout
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { 
  Search as LucideSearch, 
  Tag as LucideTag, 
  Book as LucideBook, 
  Users as LucideUsers, 
  ShoppingBag as LucideShoppingBag, 
  FileText as LucideFileText, 
  ShoppingCart as LucideShoppingCart, 
  History as LucideHistory, 
  MessageSquare as LucideMessageSquare, 
  LogOut as LucideLogOut,
  Store as LucideStore,
  User as LucideUser,
  PanelLeftClose as LucidePanelLeftClose,
  PanelLeftOpen as LucidePanelLeftOpen
} from 'lucide-vue-next'
import { useSidebar } from '~/composables/useSidebar'
import { useConfirmModal } from '~/composables/useConfirmModal'
import { useTheme } from '~/composables/useTheme'

const route = useRoute()
const authStore = useAuthStore()
const { isDark } = useTheme()
const { isCollapsed, toggle, initSidebar } = useSidebar()
const { unreadCount, initUnreadListener } = useUnreadChat()

const searchQuery = ref('')
const api = useApi()

onMounted(() => {
  initSidebar()
  if (authStore.isAuthenticated) {
    initUnreadListener()
  }
})

const adminNavItems = [
  { label: 'Kelola Kategori', path: '/admin/kategori', icon: LucideTag },
  { label: 'Katalog Buku', path: '/admin/buku', icon: LucideBook },
  { label: 'Kelola Pengguna', path: '/admin/pengguna', icon: LucideUsers },
  { label: 'Kasir & Scan QR', path: '/admin/kasir', icon: LucideShoppingBag },
  { label: 'Laporan Penjualan', path: '/admin/laporan', icon: LucideFileText },
  { label: 'Profil Saya', path: '/profile', icon: LucideUser, divider: true },
]

const userNavItems = [
  { label: 'Katalog Buku', path: '/katalog', icon: LucideStore },
  { label: 'Keranjang Belanja', path: '/keranjang', icon: LucideShoppingCart },
  { label: 'Pesanan Anda', path: '/riwayat', icon: LucideHistory },
  { label: 'Live Chat Support', path: '/chat', icon: LucideMessageSquare },
  { label: 'Profil Saya', path: '/profile', icon: LucideUser, divider: true },
]

const navItems = computed(() => {
  return authStore.isAdmin ? adminNavItems : userNavItems
})

const filteredNavItems = computed(() => {
  if (!searchQuery.value.trim()) return navItems.value
  const q = searchQuery.value.toLowerCase()
  return navItems.value.filter(item => item.label.toLowerCase().includes(q))
})

const isRouteActive = (path: string) => {
  if (path === '/katalog') return route.path === '/katalog' || route.path === '/user/katalog'
  if (path === '/keranjang') return route.path === '/keranjang' || route.path === '/user/keranjang'
  if (path === '/riwayat') return route.path === '/riwayat' || route.path === '/user/riwayat'
  if (path === '/profile') return route.path === '/profile' || route.path === '/admin/profile' || route.path === '/user/profile'
  if (path === '/chat') return route.path === '/chat' || route.path === '/user/chat'
  return route.path === path || route.path.startsWith(path + '/')
}

const confirmModal = useConfirmModal()

const handleLogout = async () => {
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
