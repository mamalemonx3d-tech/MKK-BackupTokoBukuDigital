<template>
  <!-- Full Screen Edge-to-Edge Container -->
  <div class="h-full flex flex-col overflow-hidden transition-colors duration-300">
    <div 
      :class="isDark ? 'bg-zinc-950 text-zinc-100' : 'bg-white text-black'"
      class="flex-1 flex overflow-hidden relative"
    >
      <!-- ================= SIDEBAR KONTAK ADMIN ================= -->
      <aside 
        :class="[
          isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-zinc-50 border-zinc-300',
          isCollapsed ? 'w-20' : 'w-72 sm:w-80'
        ]"
        class="border-r flex flex-col transition-all duration-300 shrink-0 z-10 relative select-none"
      >
        <!-- Expand / Collapse Toggle -->
        <button 
          @click="isCollapsed = !isCollapsed" 
          :class="isDark
            ? 'bg-white text-black border-white shadow-md'
            : 'bg-black text-white border-black shadow-md'"
          class="absolute -right-3 top-1/2 -translate-y-1/2 z-30 w-6 h-6 rounded-full border flex items-center justify-center text-[10px] font-black transition-all hover:scale-110 active:scale-95 cursor-pointer"
          :title="isCollapsed ? 'Perbesar Sidebar' : 'Kecilkan Sidebar'"
        >
          <span v-if="isCollapsed">&rsaquo;</span>
          <span v-else>&lsaquo;</span>
        </button>

        <!-- Sidebar Header -->
        <div 
          :class="isDark ? 'border-zinc-800' : 'border-zinc-200'"
          class="p-4 border-b flex items-center justify-between shrink-0"
        >
          <div v-if="!isCollapsed" class="flex items-center gap-2 overflow-hidden">
            <span class="text-lg">💬</span>
            <div>
              <h3 :class="isDark ? 'text-white' : 'text-black'" class="font-black text-xs leading-tight">Daftar Admin</h3>
              <p class="text-[10px] text-zinc-400">Customer Support</p>
            </div>
          </div>
          <div v-else class="mx-auto text-base" title="Daftar Admin">💬</div>
        </div>

        <!-- Admin List -->
        <div class="flex-1 overflow-y-auto p-2 space-y-1.5 custom-scrollbar">
          <div v-if="loadingAdmins" class="text-center py-8 text-xs text-zinc-400 font-medium">Memuat admin...</div>

          <div 
            v-for="admin in admins" :key="admin.id" 
            @click="selectAdmin(admin)"
            :class="[
              selectedAdmin?.id === admin.id 
                ? (isDark ? 'bg-zinc-800 border-zinc-700 text-white' : 'bg-white border-black text-black shadow-[2px_2px_0px_#000000]')
                : (isDark ? 'hover:bg-zinc-800/60 border-transparent text-zinc-400' : 'hover:bg-zinc-100 border-transparent text-zinc-700'),
              isCollapsed ? 'justify-center p-2' : 'p-3'
            ]"
            class="w-full flex items-center gap-3 rounded-xl border-2 transition-all cursor-pointer group relative"
          >
            <div class="relative shrink-0">
              <div 
                :class="selectedAdmin?.id === admin.id ? (isDark ? 'bg-white text-black' : 'bg-black text-white') : 'bg-zinc-700 text-white'"
                class="w-9 h-9 rounded-xl border flex items-center justify-center font-black text-xs transition-transform group-hover:scale-105"
              >
                <img v-if="admin.foto" :src="getImageUrl(admin.foto)" :alt="admin.name" class="w-full h-full object-cover rounded-xl" @error="admin.foto = null" />
                <span v-else>{{ admin.name.charAt(0).toUpperCase() }}</span>
              </div>
              <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white shadow-sm"></span>
            </div>

            <div v-if="!isCollapsed" class="min-w-0 flex-1 text-left">
              <div class="flex items-center justify-between">
                <h4 class="font-black text-xs truncate" :class="selectedAdmin?.id === admin.id ? (isDark ? 'text-white' : 'text-black') : ''">{{ admin.name }}</h4>
                <span class="text-[8px] font-mono text-emerald-500 font-bold">Online</span>
              </div>
              <p class="text-[10px] font-mono text-zinc-400 truncate">{{ admin.role_title }}</p>
              <p class="text-[10px] text-zinc-500 truncate mt-0.5">{{ admin.last_message }}</p>
            </div>
          </div>
        </div>
      </aside>

      <!-- ================= MAIN CHAT AREA ================= -->
      <main class="flex-1 flex flex-col min-w-0 bg-transparent relative">
        <template v-if="selectedAdmin">

          <!-- Chat Header -->
          <div 
            :class="isDark ? 'bg-zinc-950 border-zinc-800 text-white' : 'bg-white border-zinc-200 text-black shadow-sm'"
            class="p-3.5 border-b flex items-center justify-between shrink-0"
          >
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl border-2 flex items-center justify-center font-black text-xs" :class="isDark ? 'bg-white text-black border-white' : 'bg-black text-white border-black'">
                <img v-if="selectedAdmin.foto" :src="getImageUrl(selectedAdmin.foto)" :alt="selectedAdmin.name" class="w-full h-full object-cover rounded-xl" @error="selectedAdmin.foto = null" />
                <span v-else>{{ selectedAdmin.name.charAt(0).toUpperCase() }}</span>
              </div>
              <div>
                <h4 class="font-black text-xs sm:text-sm leading-tight">{{ selectedAdmin.name }}</h4>
                <div class="flex items-center gap-1.5 text-[10px] text-zinc-400">
                  <span class="font-mono">{{ selectedAdmin.role_title }}</span>
                  <span>•</span>
                  <span class="text-emerald-500 font-bold">Online</span>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <!-- Selection Toolbar -->
              <transition name="fade">
                <div v-if="selectedIds.size > 0" class="flex items-center gap-2">
                  <span class="text-xs font-mono font-bold">{{ selectedIds.size }} dipilih</span>
                  <button @click="cancelSelection" class="text-xs px-2.5 py-1 rounded-lg border font-bold cursor-pointer" :class="isDark ? 'border-zinc-700 bg-zinc-800 text-white' : 'border-zinc-300 bg-zinc-100 text-black'">
                    Batal
                  </button>
                  <button
                    @click.stop="openDeleteMenu"
                    class="text-xs px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-black cursor-pointer shadow-sm"
                  >
                    Hapus
                  </button>
                </div>
              </transition>

              <button 
                v-if="isCollapsed" @click="isCollapsed = false"
                :class="isDark ? 'bg-zinc-800 text-zinc-300 border-zinc-700' : 'bg-zinc-100 text-black border-zinc-300'"
                class="text-xs font-bold px-3 py-1.5 rounded-lg border transition-colors cursor-pointer"
              >
                Ganti Admin
              </button>
            </div>
          </div>

          <!-- Messages Stream -->
          <div 
            ref="chatContainer" 
            :class="isDark ? 'bg-zinc-950' : 'bg-zinc-50/50'"
            class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-3.5 custom-scrollbar"
            @click="closeContextMenu"
          >
            <div v-if="loadingChats && chats.length === 0" class="text-center py-12 text-xs text-zinc-400 font-bold">
              Memuat percakapan...
            </div>
            <div v-else-if="chats.length === 0" class="text-center py-16 text-zinc-400 text-xs font-bold">
              Belum ada obrolan dengan {{ selectedAdmin.name }}. Tulis pesan pertama Anda!
            </div>

            <div 
              v-for="chat in chats" :key="chat.id" 
              :class="chat.sender === 'user' ? 'justify-end' : 'justify-start'" 
              class="flex items-end gap-2.5 group/msg"
            >
              <!-- Admin Avatar -->
              <div v-if="chat.sender === 'admin'" 
                :class="isDark ? 'bg-zinc-800 text-white border-zinc-700' : 'bg-black text-white border-black'"
                class="w-7 h-7 rounded-lg font-black text-[10px] flex items-center justify-center shrink-0 border overflow-hidden"
              >
                <img v-if="chat.admin_foto || selectedAdmin?.foto" :src="getImageUrl(chat.admin_foto || selectedAdmin?.foto)" :alt="selectedAdmin?.name" class="w-full h-full object-cover" @error="chat.admin_foto = null" />
                <span v-else>A</span>
              </div>

              <!-- Bubble + Delete icon wrapper -->
              <div :class="chat.sender === 'user' ? 'flex-row-reverse' : 'flex-row'" class="flex items-end gap-1.5 max-w-xs sm:max-w-md relative">
                <!-- Trash icon on hover for own message -->
                <button
                  v-if="chat.sender === 'user' && selectedIds.size === 0"
                  @click.stop="openContextMenuSingle(chat, $event)"
                  class="opacity-0 group-hover/msg:opacity-100 w-5 h-5 rounded-md bg-rose-600 text-white flex items-center justify-center text-[9px] transition-opacity shrink-0 cursor-pointer"
                  title="Pilihan hapus"
                >🗑️</button>

                <!-- Message Bubble -->
                <div 
                  @mousedown="startLongPress(chat)"
                  @mouseup="cancelLongPress"
                  @mouseleave="cancelLongPress"
                  @touchstart.prevent="startLongPress(chat)"
                  @touchend="cancelLongPress"
                  @touchcancel="cancelLongPress"
                  @click.stop="onBubbleClick(chat)"
                  :class="[
                    chat.sender === 'user' 
                      ? (isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]') 
                      : (isDark 
                          ? 'bg-zinc-900 text-zinc-100 border-zinc-800' 
                          : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000]'),
                    selectedIds.has(chat.id) ? 'ring-2 ring-rose-500 opacity-80 scale-[0.98]' : '',
                    selectedIds.size > 0 ? 'cursor-pointer' : 'cursor-default',
                    'select-none transition-all duration-150 border-2 rounded-2xl p-3 text-xs space-y-1'
                  ]"
                >
                  <p class="font-bold text-[9px] opacity-60 uppercase font-mono">
                    {{ chat.sender === 'admin' ? selectedAdmin.name : 'Saya' }}
                  </p>
                  <p class="leading-relaxed whitespace-pre-wrap font-medium">{{ chat.pesan }}</p>
                  <div class="flex items-center justify-end gap-1 text-[8px] opacity-50 font-mono">
                    <span>{{ chat.created_at }}</span>
                    <span v-if="chat.is_optimistic">⏳</span>
                  </div>
                </div>
              </div>

              <!-- User Avatar -->
              <div v-if="chat.sender === 'user'" 
                :class="isDark ? 'bg-zinc-800 text-white border-zinc-700' : 'bg-zinc-200 text-black border-black'"
                class="w-7 h-7 rounded-lg font-black text-[10px] flex items-center justify-center shrink-0 border overflow-hidden"
              >
                <img v-if="chat.user_foto || authUser?.foto" :src="getImageUrl(chat.user_foto || authUser?.foto)" alt="Saya" class="w-full h-full object-cover" @error="chat.user_foto = null" />
                <span v-else>{{ authUser?.name?.charAt(0)?.toUpperCase() || 'U' }}</span>
              </div>
            </div>
          </div>

          <!-- Message Input Form -->
          <form 
            @submit.prevent="sendMessage" 
            :class="isDark ? 'bg-zinc-950 border-zinc-800' : 'bg-white border-zinc-200 border-t-2'"
            class="p-3.5 border-t flex items-center gap-2.5 shrink-0 relative z-10"
          >
            <input 
              v-model="pesanInput" type="text" 
              :placeholder="`Tulis pesan untuk ${selectedAdmin.name}...`" 
              required 
              :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black placeholder-zinc-400 shadow-[2px_2px_0px_#000000]'"
              class="flex-grow px-4 py-2.5 text-xs rounded-xl border-2 focus:outline-none font-medium" 
            />
            <button 
              type="submit" :disabled="sending" 
              :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
              class="px-5 py-2.5 rounded-xl text-xs font-black border-2 transition-all shrink-0 disabled:opacity-50 cursor-pointer"
            >
              <span>Kirim</span>
            </button>
          </form>
        </template>

        <!-- No Admin Selected -->
        <template v-else>
          <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
            <div class="w-16 h-16 rounded-2xl border-2 flex items-center justify-center text-3xl mb-4" :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white' : 'bg-zinc-100 border-black text-black'">💬</div>
            <h3 :class="isDark ? 'text-white' : 'text-black'" class="text-lg font-black mb-1">Live Chat Customer Support</h3>
            <p class="text-xs text-zinc-400 max-w-sm leading-relaxed mb-6">
              Pilih salah satu admin support dari daftar di sebelah kiri untuk memulai obrolan.
            </p>
            <button 
              v-if="admins.length > 0" @click="selectAdmin(admins[0])" 
              :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
              class="px-5 py-2.5 rounded-xl border-2 text-xs font-black cursor-pointer hover:-translate-y-0.5 transition-all"
            >
              Mulai Obrolan Dengan {{ admins[0].name }} &rarr;
            </button>
          </div>
        </template>
      </main>
    </div>

    <!-- Delete Menu Dropdown Modal -->
    <transition name="pop">
      <div
        v-if="deleteMenu.show"
        :style="{ top: deleteMenu.y + 'px', left: deleteMenu.x + 'px' }"
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-100' : 'bg-white border-2 border-black text-black shadow-xl'"
        class="fixed z-50 rounded-2xl border-2 overflow-hidden min-w-[200px]"
        @click.stop
      >
        <div :class="isDark ? 'border-zinc-800 bg-zinc-950' : 'border-zinc-200 bg-zinc-50'" class="px-4 py-2 border-b">
          <p class="text-[10px] font-mono font-bold uppercase opacity-60">Pilihan Hapus</p>
          <p class="text-[10px] opacity-50 mt-0.5">{{ deleteMenu.count }} pesan dipilih</p>
        </div>

        <button
          @click="doDeleteForMe"
          :disabled="processing"
          class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-left disabled:opacity-50 cursor-pointer"
        >
          <span>🙈</span>
          <div>
            <p class="font-black text-xs">Hapus untuk Saya</p>
          </div>
        </button>

        <button
          v-if="deleteMenu.canDeleteForAll"
          @click="doDeleteForAll"
          :disabled="processing"
          class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 transition-colors text-left border-t disabled:opacity-50 cursor-pointer"
          :class="isDark ? 'border-zinc-800' : 'border-zinc-200'"
        >
          <span>🗑️</span>
          <div>
            <p class="font-black text-xs">Hapus untuk Semua Orang</p>
          </div>
        </button>

        <button
          @click="closeContextMenu"
          class="w-full px-4 py-2 text-xs font-bold text-center border-t opacity-60 hover:opacity-100 cursor-pointer"
          :class="isDark ? 'border-zinc-800' : 'border-zinc-200'"
        >
          Batal
        </button>
      </div>
    </transition>

    <div v-if="deleteMenu.show" class="fixed inset-0 z-40" @click="closeContextMenu"></div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Live Chat - TokoBukuDigital'
})

const api = useApi()
const authStore = useAuthStore()
const { isDark } = useTheme()
const { getImageUrl } = useImageUrl()

const authUser = computed(() => authStore.user)

const admins = ref<any[]>([])
const selectedAdmin = ref<any | null>(null)
const isCollapsed = ref(false)

const chats = ref<any[]>([])
const pesanInput = ref('')
const loadingAdmins = ref(true)
const loadingChats = ref(false)
const sending = ref(false)
const chatContainer = ref<HTMLElement | null>(null)
let pollTimer: any = null

const selectedIds = ref<Set<number>>(new Set())
let longPressTimer: ReturnType<typeof setTimeout> | null = null
let longPressTriggered = false

const startLongPress = (chat: any) => {
  longPressTriggered = false
  longPressTimer = setTimeout(() => {
    longPressTriggered = true
    if (navigator.vibrate) navigator.vibrate(60)
    const next = new Set(selectedIds.value)
    next.add(chat.id)
    selectedIds.value = next
  }, 500)
}

const cancelLongPress = () => {
  if (longPressTimer) {
    clearTimeout(longPressTimer)
    longPressTimer = null
  }
}

const onBubbleClick = (chat: any) => {
  if (longPressTriggered) {
    longPressTriggered = false
    return
  }
  if (selectedIds.value.size > 0) {
    toggleSelect(chat)
  }
}

const toggleSelect = (chat: any) => {
  const next = new Set(selectedIds.value)
  if (next.has(chat.id)) {
    next.delete(chat.id)
  } else {
    next.add(chat.id)
  }
  selectedIds.value = next
}

const cancelSelection = () => {
  selectedIds.value = new Set()
  closeContextMenu()
}

interface DeleteMenu {
  show: boolean
  x: number
  y: number
  count: number
  canDeleteForAll: boolean
  singleChat: any | null
}

const deleteMenu = ref<DeleteMenu>({
  show: false,
  x: 0,
  y: 0,
  count: 0,
  canDeleteForAll: false,
  singleChat: null,
})

const processing = ref(false)

const openDeleteMenu = () => {
  const selectedChats = chats.value.filter((c: any) => selectedIds.value.has(c.id))
  const allOwn = selectedChats.every((c: any) => c.sender === 'user')

  const x = Math.max(window.innerWidth / 2 - 110, 16)
  const y = Math.max(window.innerHeight / 2 - 120, 80)

  deleteMenu.value = {
    show: true,
    x,
    y,
    count: selectedIds.value.size,
    canDeleteForAll: allOwn,
    singleChat: null,
  }
}

const openContextMenuSingle = (chat: any, event: MouseEvent) => {
  selectedIds.value = new Set([chat.id])

  const rect = (event.target as HTMLElement).getBoundingClientRect()
  let x = rect.left - 200
  let y = rect.top - 10

  if (x < 8) x = 8
  if (y + 160 > window.innerHeight) y = window.innerHeight - 170

  deleteMenu.value = {
    show: true,
    x,
    y,
    count: 1,
    canDeleteForAll: chat.sender === 'user',
    singleChat: chat,
  }
}

const closeContextMenu = () => {
  deleteMenu.value.show = false
}

const doDeleteForMe = async () => {
  if (processing.value) return
  processing.value = true
  try {
    await api.post('/api/chats/delete-for-me', {
      ids: Array.from(selectedIds.value),
    })
    const removed = new Set(selectedIds.value)
    chats.value = chats.value.filter((c: any) => !removed.has(c.id))
    cancelSelection()
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal menyembunyikan pesan')
  } finally {
    processing.value = false
    closeContextMenu()
  }
}

const doDeleteForAll = async () => {
  if (processing.value) return
  processing.value = true
  try {
    await api.post('/api/chats/delete-for-all', {
      ids: Array.from(selectedIds.value),
    })
    const removed = new Set(selectedIds.value)
    chats.value = chats.value.filter((c: any) => !removed.has(c.id))
    cancelSelection()
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal menghapus pesan')
  } finally {
    processing.value = false
    closeContextMenu()
  }
}

const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight
    }
  })
}

const fetchAdmins = async () => {
  loadingAdmins.value = true
  try {
    const res = await api.get('/api/admins')
    admins.value = res.data || []
  } catch (e) {
    console.error('Failed to fetch admins:', e)
  } finally {
    if (!admins.value || admins.value.length === 0) {
      admins.value = [{
        id: 1,
        name: 'Admin Support',
        username: 'admin',
        role_title: 'Customer Service',
        is_online: true,
        last_message: 'Ada yang bisa kami bantu?',
        foto: null,
      }]
    }
    loadingAdmins.value = false
  }
}

const selectAdmin = async (admin: any) => {
  if (selectedAdmin.value?.id !== admin.id) {
    chats.value = []
  }
  selectedAdmin.value = admin
  isCollapsed.value = true
  cancelSelection()
  await fetchChats()
}

let syncChannel: BroadcastChannel | null = null
let isFetchingChat = false

const fetchChats = async (silent = false) => {
  if (!selectedAdmin.value || isFetchingChat) return
  isFetchingChat = true
  if (!silent) loadingChats.value = true
  try {
    const res = await api.get('/api/chats', {
      params: { admin_id: selectedAdmin.value.id }
    })
    const incomingChats = res.data || []
    
    // Preserve optimistic messages not yet acknowledged
    const optimisticMessages = chats.value.filter(c => c.is_optimistic)
    if (optimisticMessages.length > 0) {
      const serverTextSet = new Set(incomingChats.map((c: any) => `${c.sender}:${c.pesan}`))
      const stillPending = optimisticMessages.filter(m => !serverTextSet.has(`${m.sender}:${m.pesan}`))
      chats.value = [...incomingChats, ...stillPending]
    } else {
      chats.value = incomingChats
    }

    if (incomingChats.length > 0) {
      try {
        await api.post('/api/chats/mark-as-read')
      } catch (err) {}
    }
    scrollToBottom()
  } catch (e: any) {
    if (e.status === 401 || e.statusCode === 401 || e.response?.status === 401) {
      if (pollTimer) clearInterval(pollTimer)
    }
  } finally {
    isFetchingChat = false
    if (!silent) loadingChats.value = false
  }
}

const sendMessage = async () => {
  if (!pesanInput.value.trim() || !selectedAdmin.value) return

  const msg = pesanInput.value.trim()
  pesanInput.value = ''

  // Instant Optimistic message push (0ms delay)
  const tempId = Date.now()
  const optimisticMsg = {
    id: tempId,
    sender: 'user',
    pesan: msg,
    created_at: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
    user_foto: authUser.value?.foto,
    admin_foto: selectedAdmin.value?.foto,
    is_optimistic: true
  }
  chats.value.push(optimisticMsg)
  scrollToBottom()

  // Update last message preview in admin list
  if (selectedAdmin.value) {
    selectedAdmin.value.last_message = msg
  }

  sending.value = true
  try {
    await api.post('/api/chats', {
      admin_id: selectedAdmin.value.id,
      pesan: msg,
    })

    // Broadcast instant sync across browser tabs/windows (0ms delay)
    if (typeof window !== 'undefined' && 'BroadcastChannel' in window) {
      try {
        const bc = new BroadcastChannel('live_chat_sync')
        bc.postMessage({ type: 'new_message', sender: 'user', user_id: authUser.value?.id, admin_id: selectedAdmin.value.id })
        bc.close()
      } catch (e) {}
    }

    // Background sync
    await fetchChats(true)
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal mengirim pesan')
    // Remove failed message
    chats.value = chats.value.filter(c => c.id !== tempId)
  } finally {
    sending.value = false
  }
}

const handleVisibilityOrFocus = () => {
  if (typeof document !== 'undefined' && document.visibilityState === 'visible') {
    if (selectedAdmin.value) {
      fetchChats(true)
    }
  }
}

onMounted(async () => {
  await fetchAdmins()
  if (admins.value.length > 0) selectAdmin(admins.value[0])

  // Cross-tab 0ms Realtime Channel
  if (typeof window !== 'undefined' && 'BroadcastChannel' in window) {
    try {
      syncChannel = new BroadcastChannel('live_chat_sync')
      syncChannel.onmessage = (event) => {
        if (event.data?.type === 'new_message') {
          if (selectedAdmin.value && (!event.data.admin_id || event.data.admin_id === selectedAdmin.value.id)) {
            fetchChats(true)
          }
        }
      }
    } catch (e) {}
  }

  // Window Focus Trigger
  if (typeof window !== 'undefined') {
    window.addEventListener('focus', handleVisibilityOrFocus)
    document.addEventListener('visibilitychange', handleVisibilityOrFocus)
  }
  
  // Safe 3-second Active Fallback Polling
  pollTimer = setInterval(() => {
    if (typeof document !== 'undefined' && document.visibilityState === 'visible') {
      if (selectedAdmin.value && !isFetchingChat) {
        fetchChats(true)
      }
    }
  }, 3000)
})

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer)
  if (syncChannel) {
    syncChannel.close()
    syncChannel = null
  }
  if (typeof window !== 'undefined') {
    window.removeEventListener('focus', handleVisibilityOrFocus)
    document.removeEventListener('visibilitychange', handleVisibilityOrFocus)
  }
  cancelLongPress()
})
</script>
