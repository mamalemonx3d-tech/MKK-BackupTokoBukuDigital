<template>
  <div v-if="authStore.isAuthenticated && !isChatPage" class="fixed bottom-20 md:bottom-6 right-4 md:right-6 z-50 select-none">
    <button 
      @click="navigateToChat" 
      class="relative flex items-center gap-2.5 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full font-black text-xs shadow-2xl border-2 border-slate-900 transition-all duration-200 hover:scale-105 active:scale-95 group"
    >
      <span class="text-xl">💬</span>
      <span class="hidden sm:inline font-extrabold">Live Chat</span>

      <!-- Unread Badge Counter -->
      <span 
        v-if="unreadCount > 0" 
        class="absolute -top-2 -right-2 bg-rose-500 text-white text-[11px] font-black w-6 h-6 rounded-full flex items-center justify-center border-2 border-slate-900 shadow-md animate-bounce"
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
    navigateTo('/user/chat')
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
