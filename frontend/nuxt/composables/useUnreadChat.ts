let isPollingStarted = false
let pollInterval: any = null
let broadcastChannel: BroadcastChannel | null = null

export const useUnreadChat = () => {
  const unreadCount = useState<number>('unread_chat_count', () => 0)
  const isFetching = ref(false)
  const authStore = useAuthStore()
  const api = useApi()

  const fetchUnread = async () => {
    if (!authStore.isAuthenticated) {
      unreadCount.value = 0
      return
    }
    if (isFetching.value) return
    isFetching.value = true
    try {
      const res = await api.get('/api/chats/unread-count')
      unreadCount.value = res?.data?.unread_count ?? res?.unread_count ?? 0
    } catch (e) {
      // Keep existing count on transient error
    } finally {
      isFetching.value = false
    }
  }

  const initUnreadListener = () => {
    if (!process.client || isPollingStarted) return
    isPollingStarted = true

    fetchUnread()

    // Gentle 15s fallback poll only when tab is visible
    pollInterval = setInterval(() => {
      if (document.visibilityState === 'visible' && authStore.isAuthenticated) {
        fetchUnread()
      }
    }, 15000)

    // 0ms instant sync across tabs via BroadcastChannel
    try {
      broadcastChannel = new BroadcastChannel('live_chat_sync')
      broadcastChannel.onmessage = () => {
        if (authStore.isAuthenticated) {
          fetchUnread()
        }
      }
    } catch (e) {}

    // Refetch when tab regains focus
    window.addEventListener('focus', () => {
      if (authStore.isAuthenticated) fetchUnread()
    })
  }

  return {
    unreadCount,
    fetchUnread,
    initUnreadListener
  }
}