<template>
  <div 
    class="flex flex-col md:flex-row antialiased transition-colors duration-200 font-['Space_Grotesk']"
    :class="[
      isDark ? 'bg-slate-950 text-slate-200' : 'bg-[#FAF7F0] text-slate-900',
      isChatPage ? 'h-screen overflow-hidden pb-16 md:pb-0' : 'min-h-screen pb-24 md:pb-0'
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
          isDark ? 'bg-slate-950' : 'bg-[#FAF7F0]',
          isChatPage ? 'p-0 overflow-hidden' : 'p-3 sm:p-6 overflow-y-auto'
        ]"
      >
        <div 
          class="transition-colors duration-200"
          :class="[
            isChatPage 
              ? 'flex-1 flex flex-col h-full overflow-hidden' 
              : (isDark 
                  ? 'min-h-full rounded-2xl sm:rounded-3xl p-4 sm:p-6 bg-slate-900 border border-slate-800 text-slate-100' 
                  : 'min-h-full rounded-2xl sm:rounded-3xl p-4 sm:p-6 bg-[#FFF8EC] border-2.5 border-[#1A1A1A] shadow-[5px_5px_0px_#1A1A1A] text-slate-900')
          ]"
        >
          <slot />
        </div>
      </main>
    </div>

    <!-- Mobile Bottom Navigation Bar (Shown ONLY on Mobile < md) -->
    <nav 
      :class="isDark ? 'bg-slate-900 border-t border-slate-800' : 'bg-[#FAF7F0] border-t-0'"
      class="md:hidden fixed bottom-0 left-0 right-0 z-40 transition-colors duration-200"
      :style="!isDark ? 'box-shadow: 0 -1px 0 rgba(0,0,0,0.08)' : 'box-shadow: 0 -1px 0 rgba(0,0,0,0.3)'"
    >
      <div class="flex items-end justify-around h-16 px-2 pb-2">
        <template v-for="item in bottomNavItems" :key="item.path">
          <NuxtLink 
            :to="item.path" 
            class="relative flex flex-col items-center justify-end flex-1 h-full transition-all duration-300"
          >
            <!-- Active: elevated bubble circle -->
            <template v-if="route.path === item.path">
              <span 
                class="absolute flex flex-col items-center justify-center w-14 h-14 rounded-full transition-all duration-200"
                :class="isDark 
                  ? 'bg-indigo-500 border border-indigo-500 shadow-lg shadow-indigo-500/20 -top-5' 
                  : 'bg-[#C8F53F] border-2 border-black shadow-[3px_3px_0px_#1A1A1A] -top-5'"
                style="left: 50%; transform: translateX(-50%)"
              >
                <span class="text-2xl leading-none">{{ item.icon }}</span>
                <span 
                  v-if="item.path.includes('/chat') && hasUnreadChat" 
                  class="absolute -top-1 -right-1 bg-rose-500 text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center border border-black animate-pulse"
                >1</span>
              </span>
              <span 
                class="text-[9px] font-black tracking-wide mb-0.5"
                :class="isDark ? 'text-indigo-400' : 'text-black'"
              >{{ item.label }}</span>
            </template>

            <!-- Inactive: normal icon + label -->
            <template v-else>
              <div class="relative">
                <span class="text-xl leading-none mb-0.5 opacity-50">{{ item.icon }}</span>
                <span 
                  v-if="item.path.includes('/chat') && hasUnreadChat" 
                  class="absolute -top-1 -right-1.5 bg-rose-500 text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center border border-black animate-pulse"
                >1</span>
              </div>
              <span 
                class="text-[9px] font-bold tracking-wide"
                :class="isDark ? 'text-slate-400' : 'text-slate-400'"
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
  { label: 'Home', path: '/', icon: '📋' },
  { label: 'Katalog', path: '/user/katalog', icon: '📚' },
  { label: 'Keranjang', path: '/user/keranjang', icon: '🛒' },
  { label: 'Pesanan', path: '/user/riwayat', icon: '📋' },
]

const bottomNavItems = computed(() => {
  return authStore.isAdmin ? adminBottomNav : userBottomNav
})

onMounted(async () => {
  initTheme()
  if (!authStore.initialized) {
    await authStore.fetchUser()
  }
  if (authStore.isAuthenticated) {
    await cartStore.fetchCart()
    checkUnreadChat()
    navPollTimer = setInterval(checkUnreadChat, 5000)
  }
})

onUnmounted(() => {
  if (navPollTimer) clearInterval(navPollTimer)
})
</script>
