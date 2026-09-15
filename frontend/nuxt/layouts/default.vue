<template>
  <div 
    class="flex flex-col md:flex-row antialiased transition-colors duration-300 font-sans"
    :class="[
      isDark ? 'bg-[#09090b] text-[#f4f4f5]' : 'bg-[#fafafa] text-[#09090b]',
      isChatPage ? 'h-screen overflow-hidden pb-16 md:pb-0' : 'min-h-screen pb-20 md:pb-0'
    ]"
  >
    <!-- Desktop Sidebar -->
    <Sidebar />

    <!-- Right Main Content Wrapper -->
    <div class="flex-grow flex flex-col min-w-0 min-h-0">
      <!-- Header Bar -->
      <Header />

      <!-- Main Content Container -->
      <main 
        class="flex-1 flex flex-col min-h-0 transition-colors duration-300"
        :class="[
          isDark ? 'bg-[#09090b]' : 'bg-[#fafafa]',
          isChatPage ? 'p-0 overflow-hidden' : 'p-3 sm:p-6 lg:p-8 overflow-y-auto'
        ]"
      >
        <div 
          class="transition-colors duration-300"
          :class="[
            isChatPage 
              ? 'flex-1 flex flex-col h-full overflow-hidden' 
              : (isDark 
                  ? 'min-h-full rounded-2xl sm:rounded-3xl p-4 sm:p-6 bg-zinc-900/60 border border-zinc-800 text-zinc-100' 
                  : 'min-h-full rounded-2xl sm:rounded-3xl p-4 sm:p-6 bg-white border-2 border-black shadow-[4px_4px_0px_#000000] text-black')
          ]"
        >
          <slot />
        </div>
      </main>
    </div>

    <!-- Mobile Bottom Navigation Bar (Shown ONLY on Mobile < md) -->
    <nav 
      :class="isDark ? 'bg-[#09090b] border-t border-zinc-800 text-white' : 'bg-white border-t-2 border-black text-black'"
      class="md:hidden fixed bottom-0 left-0 right-0 z-40 transition-colors duration-300"
    >
      <div class="flex items-center justify-around h-16 px-2">
        <template v-for="item in bottomNavItems" :key="item.path">
          <NuxtLink 
            :to="item.path" 
            class="relative flex flex-col items-center justify-center flex-1 h-full transition-all duration-200 cursor-pointer"
          >
            <!-- Active State -->
            <template v-if="isBottomActive(item.path)">
              <span 
                class="flex items-center justify-center w-9 h-9 rounded-xl border-2 transition-all"
                :class="isDark 
                  ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' 
                  : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
              >
                <span class="text-sm">{{ item.icon }}</span>
              </span>
              <span 
                class="text-[9px] font-black tracking-wide mt-0.5"
                :class="isDark ? 'text-white' : 'text-black'"
              >{{ item.label }}</span>
            </template>

            <!-- Inactive State -->
            <template v-else>
              <span class="text-base opacity-40 hover:opacity-100 transition-opacity">{{ item.icon }}</span>
              <span 
                class="text-[9px] font-bold tracking-wide text-zinc-400"
              >{{ item.label }}</span>
            </template>
          </NuxtLink>
        </template>
      </div>
    </nav>

    <!-- Floating Chat Widget (Bottom Right Corner on ALL pages) -->
    <FloatingChat />

    <!-- Toast Notifications (Top Right Corner) -->
    <Toast />
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const authStore = useAuthStore()
const cartStore = useCartStore()
const { isDark, initTheme } = useTheme()

const isChatPage = computed(() => route.path.includes('/chat'))
const api = useApi()
const hasUnreadChat = ref(false)

const checkUnreadChat = async () => {
  if (!authStore.isAuthenticated) {
    hasUnreadChat.value = false
    return
  }
  if (isChatPage.value) {
    hasUnreadChat.value = false
    try {
      await api.post('/api/chats/mark-as-read')
    } catch (e) {}
    return
  }
  try {
    const res = await api.get('/api/chats/unread-count')
    hasUnreadChat.value = (res.data?.unread_count || 0) > 0
  } catch (e) {
    hasUnreadChat.value = false
  }
}

let navPollTimer: any = null

const adminBottomNav = [
  { label: 'Kategori', path: '/admin/kategori', icon: '🏷️' },
  { label: 'Buku', path: '/admin/buku', icon: '📚' },
  { label: 'Kasir', path: '/admin/kasir', icon: '🛒' },
  { label: 'Laporan', path: '/admin/laporan', icon: '📊' },
  { label: 'Chat', path: '/admin/chat', icon: '💬' },
]

const userBottomNav = [
  { label: 'Home', path: '/', icon: '🏠' },
  { label: 'Katalog', path: '/katalog', icon: '📚' },
  { label: 'Keranjang', path: '/keranjang', icon: '🛒' },
  { label: 'Pesanan', path: '/riwayat', icon: '📋' },
]

const bottomNavItems = computed(() => {
  return authStore.isAdmin ? adminBottomNav : userBottomNav
})

const isBottomActive = (path: string) => {
  if (path === '/katalog') return route.path === '/katalog' || route.path === '/user/katalog'
  if (path === '/keranjang') return route.path === '/keranjang' || route.path === '/user/keranjang'
  if (path === '/riwayat') return route.path === '/riwayat' || route.path === '/user/riwayat'
  return route.path === path
}

onMounted(async () => {
  initTheme()
  if (!authStore.initialized) {
    await authStore.fetchUser()
  }
  if (authStore.isAuthenticated) {
    await cartStore.fetchCart()
    checkUnreadChat()
  }
})

onUnmounted(() => {
  if (navPollTimer) clearInterval(navPollTimer)
})
</script>
