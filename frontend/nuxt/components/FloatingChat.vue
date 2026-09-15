<template>
  <div v-if="authStore.isAuthenticated && !isChatPage" class="fixed bottom-20 md:bottom-6 right-4 md:right-6 z-50 select-none">
    <button 
      @click="navigateToChat" 
      class="relative flex items-center gap-2.5 px-4 py-2.5 rounded-full font-black text-xs border-2 transition-all duration-200 hover:scale-105 active:scale-95 cursor-pointer shadow-lg"
      :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[3px_3px_0px_#000000]'"
    >
      <span class="text-base">💬</span>
      <span class="font-mono uppercase tracking-wider text-[11px]">Live Chat</span>

      <!-- Unread Badge Counter -->
      <span 
        v-if="unreadCount > 0" 
        class="absolute -top-1.5 -right-1.5 bg-rose-600 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center border-2 border-black animate-pulse"
      >
        {{ unreadCount }}
      </span>
    </button>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const authStore = useAuthStore()
const api = useApi()
const { isDark } = useTheme()

const unreadCount = ref(0)
let pollTimer: any = null

const isChatPage = computed(() => {
  return route.path.includes('/chat')
})

const navigateToChat = async () => {
  unreadCount.value = 0
  try {
    await api.post('/api/chats/mark-as-read')
  } catch (e) {}
  if (authStore.isAdmin) {
    navigateTo('/admin/chat')
  } else {
    navigateTo('/chat')
  }
}

const checkUnread = async () => {
  if (!authStore.isAuthenticated || isChatPage.value) {
    unreadCount.value = 0
    return
  }
  try {
    const res: any = await api.get('/api/chats/unread-count')
    unreadCount.value = res.unread_count ?? res.data?.unread_count ?? 0
  } catch (e) {
    unreadCount.value = 0
  }
}

onMounted(() => {
  if (authStore.isAuthenticated) {
    checkUnread()
    pollTimer = setInterval(checkUnread, 5000)
  }
})

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer)
})
</script>
