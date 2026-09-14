<template>
  <aside 
    :class="isDark ? 'bg-slate-900/95 border-slate-800 text-slate-200 shadow-sm' : 'bg-[#FFF8EC] border-r-2.5 border-[#1A1A1A] text-slate-900 shadow-none'"
    class="hidden md:flex w-64 h-screen sticky top-0 p-5 flex-col justify-between select-none shrink-0 transition-colors duration-200 overflow-y-auto custom-scrollbar"
  >
    <div>
      <!-- Brand Logo -->
      <NuxtLink to="/" class="flex items-center gap-3 px-2 py-3 mb-6 group">
        <div 
          :class="isDark ? 'bg-indigo-600 text-white shadow-sm' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[3px_3px_0px_#1A1A1A]'"
          class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-xl group-hover:scale-105 transition-transform duration-200"
        >
          <LucideBookOpen class="w-5 h-5" :class="isDark ? 'text-white' : 'text-black'" />
        </div>
        <span class="font-black text-2xl tracking-tight" :class="isDark ? 'text-white' : 'text-slate-900'">
          Book<span :class="isDark ? 'text-indigo-500' : 'bg-[#C8F53F] text-black px-1.5 py-0.5 rounded-lg border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'">Store</span>
        </span>
      </NuxtLink>

      <!-- Sidebar Search Box -->
      <div class="relative mb-6">
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Cari menu..." 
          :class="isDark ? 'bg-slate-800/80 border-slate-700 text-white focus:bg-slate-800' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[2.5px_2.5px_0px_#1A1A1A] placeholder-slate-500'"
          class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl outline-none transition-all font-bold"
        />
        <LucideSearch class="w-4 h-4 text-slate-400 absolute left-3 top-3" />
      </div>

      <!-- Navigation Menu -->
      <nav class="space-y-2">
        <template v-for="item in filteredNavItems" :key="item.path">
          <NuxtLink 
            :to="item.path" 
            class="group relative flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-black transition-all duration-200"
            :class="[
              isRouteActive(item.path) 
                ? (isDark ? 'text-white bg-indigo-600 shadow-md shadow-indigo-600/30' : 'text-black bg-[#C8F53F] border-2 border-[#1A1A1A] shadow-[3px_3px_0px_#1A1A1A]') 
                : (isDark ? 'text-slate-400 hover:text-white hover:bg-slate-800/60' : 'text-slate-700 hover:text-black hover:bg-[#FAF7F0] border-2 border-transparent hover:border-[#1A1A1A] hover:shadow-[2px_2px_0px_#1A1A1A]')
            ]"
          >
            <component :is="item.icon" class="w-4 h-4 transition-transform group-hover:scale-110" :class="isRouteActive(item.path) ? (isDark ? 'text-white' : 'text-black') : 'text-slate-400'" />
            <span>{{ item.label }}</span>
          </NuxtLink>

          <!-- Divider -->
          <div v-if="item.divider" class="my-3 border-t" :class="isDark ? 'border-slate-800' : 'border-2 border-[#1A1A1A]'"></div>
        </template>
      </nav>
    </div>

    <!-- Bottom Actions (Chat & Logout) -->
    <div class="space-y-2 pt-4 border-t" :class="isDark ? 'border-slate-800' : 'border-t-2 border-[#1A1A1A]'">

      <button 
        @click="handleLogout" 
        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-black transition-all duration-200"
        :class="isDark ? 'text-slate-400 hover:text-rose-400 hover:bg-rose-500/10' : 'text-slate-800 bg-[#FFB7B2] border-2 border-[#1A1A1A] shadow-[2.5px_2.5px_0px_#1A1A1A] hover:translate-x-[-1px] hover:translate-y-[-1px] active:translate-x-[1px] active:translate-y-[1px]'"
      >
        <LucideLogOut class="w-4 h-4" :class="isDark ? 'text-slate-400 group-hover:text-rose-400' : 'text-black'" />
        <span>Keluar / Logout</span>
      </button>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { 
  BookOpen as LucideBookOpen, 
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
  Store as LucideStore
} from 'lucide-vue-next'

const route = useRoute()
const authStore = useAuthStore()
const { isDark } = useTheme()

const searchQuery = ref('')
const api = useApi()
const unreadCount = ref(0)
let pollTimer: any = null

const isChatPage = computed(() => route.path.includes('/chat'))

const checkUnread = async () => {
  if (!authStore.isAuthenticated) return
  try {
    const res = await api.get('/api/chats/unread-count')
    unreadCount.value = res?.unread_count || 0
  } catch (e) {
    unreadCount.value = 0
  }
}

onMounted(() => {
  if (authStore.isAuthenticated) {
    checkUnread()
    pollTimer = setInterval(checkUnread, 4000)
  }
})

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer)
})

const chatPath = computed(() => {
  return authStore.isAdmin ? '/admin/chat' : '/user/chat'
})

const adminNavItems = [
  { label: 'Kategori', path: '/admin/kategori', icon: LucideTag },
  { label: 'Katalog Buku', path: '/admin/buku', icon: LucideBook },
  { label: 'Pengguna', path: '/admin/pengguna', icon: LucideUsers },
  { label: 'Kasir & Scan', path: '/admin/kasir', icon: LucideShoppingBag },
  { label: 'Laporan', path: '/admin/laporan', icon: LucideFileText, divider: true },
]

const userNavItems = [
  { label: 'Katalog Buku', path: '/user/katalog', icon: LucideStore },
  { label: 'Keranjang', path: '/user/keranjang', icon: LucideShoppingCart },
  { label: 'Pesanan anda', path: '/user/riwayat', icon: LucideHistory, divider: true },
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
  if (path === '/admin/kasir') return route.path === '/admin/kasir'
  return route.path === path || route.path.startsWith(path + '/')
}

const handleLogout = async () => {
  if (confirm('Apakah Anda yakin ingin keluar?')) {
    await authStore.logout()
  }
}
</script>
