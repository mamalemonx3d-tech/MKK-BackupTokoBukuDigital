<template>
  <!-- Full Screen Edge-to-Edge Container -->
  <div class="h-full flex flex-col overflow-hidden transition-colors duration-300">
    <div 
      :class="isDark ? 'bg-zinc-950 text-zinc-100' : 'bg-white text-black'"
      class="flex-1 flex overflow-hidden relative"
    >
      <!-- ================= SIDEBAR DAFTAR PELANGGAN ================= -->
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
              <h3 :class="isDark ? 'text-white' : 'text-black'" class="font-black text-xs leading-tight">Obrolan Pelanggan</h3>
              <p class="text-[10px] text-zinc-400">Live Support Admin</p>
            </div>
          </div>
          <div v-else class="mx-auto text-base" title="Obrolan Pelanggan">💬</div>
        </div>

        <!-- User List -->
        <div class="flex-1 overflow-y-auto p-2 space-y-1.5 custom-scrollbar">
          <div v-if="loadingUsers && userList.length === 0" class="text-center py-8 text-xs text-zinc-400 font-medium">Memuat pelanggan...</div>
          <div v-else-if="userList.length === 0" class="text-center py-8 text-xs text-zinc-400 font-medium">Belum ada obrolan masuk.</div>

          <div 
            v-for="u in userList" :key="u.id" 
            @click="selectUser(u)"
            :class="[
              selectedUser?.id === u.id 
                ? (isDark ? 'bg-zinc-800 border-zinc-700 text-white' : 'bg-white border-black text-black shadow-[2px_2px_0px_#000000]')
                : (isDark ? 'hover:bg-zinc-800/60 border-transparent text-zinc-400' : 'hover:bg-zinc-100 border-transparent text-zinc-700'),
              isCollapsed ? 'justify-center p-2' : 'p-3'
            ]"
            class="w-full flex items-center gap-3 rounded-xl border-2 transition-all cursor-pointer group relative"
          >
            <!-- User Avatar in Sidebar -->
            <div class="relative shrink-0">
              <div 
                :class="selectedUser?.id === u.id ? (isDark ? 'bg-white text-black' : 'bg-black text-white') : 'bg-zinc-700 text-white'"
                class="w-9 h-9 rounded-xl border flex items-center justify-center font-black text-xs transition-transform group-hover:scale-105 overflow-hidden"
              >
                <img v-if="u.foto" :src="getImageUrl(u.foto)" :alt="u.name" class="w-full h-full object-cover rounded-xl" @error="u.foto = null" />
                <span v-else>{{ u.name.charAt(0).toUpperCase() }}</span>
              </div>
              <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white shadow-sm"></span>
            </div>

            <!-- User Info in Sidebar -->
            <div v-if="!isCollapsed" class="min-w-0 flex-1 text-left">
              <div class="flex items-center justify-between">
                <h4 class="font-black text-xs truncate" :class="selectedUser?.id === u.id ? (isDark ? 'text-white' : 'text-black') : ''">{{ u.name }}</h4>
                <span class="text-[9px] font-mono text-zinc-400 shrink-0">{{ u.last_time }}</span>
              </div>
              <div class="flex items-center justify-between mt-0.5">
                <p class="text-[10px] text-zinc-400 truncate flex-1">{{ u.last_message || 'Obrolan baru' }}</p>
                <span v-if="u.unread > 0" class="ml-2 px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[9px] font-black shrink-0 animate-pulse">
                  {{ u.unread }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </aside>

      <!-- ================= MAIN CHAT AREA ================= -->
      <main class="flex-1 flex flex-col min-w-0 bg-transparent relative">
        <template v-if="selectedUser">

          <!-- Active User Header -->
          <div 
            :class="isDark ? 'bg-zinc-950 border-zinc-800 text-white' : 'bg-white border-zinc-200 text-black shadow-sm'"
            class="p-3.5 border-b flex items-center justify-between shrink-0"
          >
            <div class="flex items-center gap-3">
              <!-- Active User Avatar -->
              <div class="w-9 h-9 rounded-xl border-2 flex items-center justify-center font-black text-xs overflow-hidden" :class="isDark ? 'bg-white text-black border-white' : 'bg-black text-white border-black'">
                <img v-if="selectedUser.foto" :src="getImageUrl(selectedUser.foto)" :alt="selectedUser.name" class="w-full h-full object-cover rounded-xl" @error="selectedUser.foto = null" />
                <span v-else>{{ selectedUser.name.charAt(0).toUpperCase() }}</span>
              </div>
              <div>
                <h4 class="font-black text-xs sm:text-sm leading-tight">{{ selectedUser.name }}</h4>
                <div class="flex items-center gap-1.5 text-[10px] text-zinc-400">
                  <span class="font-mono">@{{ selectedUser.username || 'pelanggan' }}</span>
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
                Ganti Pelanggan
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
              Belum ada obrolan dengan {{ selectedUser.name }}. Tulis balasan pertama Anda!
            </div>

            <div 
              v-for="chat in chats" :key="chat.id" 
              :class="chat.sender === 'admin' ? 'justify-end' : 'justify-start'" 
              class="flex items-end gap-2.5 group/msg"
            >
              <!-- User Avatar for incoming message (Left) -->
              <div v-if="chat.sender === 'user'" 
                :class="isDark ? 'bg-zinc-800 text-white border-zinc-700' : 'bg-zinc-200 text-black border-black'"
                class="w-7 h-7 rounded-lg font-black text-[10px] flex items-center justify-center shrink-0 border overflow-hidden"
              >
                <img v-if="chat.user_foto || selectedUser.foto" :src="getImageUrl(chat.user_foto || selectedUser.foto)" :alt="selectedUser.name" class="w-full h-full object-cover" @error="chat.user_foto = null" />
                <span v-else>{{ selectedUser.name.charAt(0).toUpperCase() }}</span>
              </div>

              <!-- Bubble + Delete icon wrapper -->
              <div :class="chat.sender === 'admin' ? 'flex-row-reverse' : 'flex-row'" class="flex items-end gap-1.5 max-w-xs sm:max-w-md relative">
                <!-- Trash icon on hover for own admin message -->
                <button
                  v-if="chat.sender === 'admin' && selectedIds.size === 0"
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
                    chat.sender === 'admin' 
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
                    {{ chat.sender === 'admin' ? 'Saya (Admin)' : selectedUser.name }}
                  </p>
                  <p class="leading-relaxed whitespace-pre-wrap font-medium">{{ chat.pesan }}</p>
                  <div class="flex items-center justify-end gap-1 text-[8px] opacity-50 font-mono">
                    <span>{{ chat.created_at }}</span>
                    <span v-if="chat.is_optimistic">⏳</span>
                  </div>
                </div>
              </div>

              <!-- Admin Avatar for outgoing message (Right) -->
              <div v-if="chat.sender === 'admin'" 
                :class="isDark ? 'bg-white text-black border-white' : 'bg-black text-white border-black'"
                class="w-7 h-7 rounded-lg font-black text-[10px] flex items-center justify-center shrink-0 border overflow-hidden"
              >
                <img v-if="chat.admin_foto || authUser?.foto" :src="getImageUrl(chat.admin_foto || authUser?.foto)" alt="Admin" class="w-full h-full object-cover" @error="chat.admin_foto = null" />
                <span v-else>A</span>
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
              :placeholder="`Tulis balasan untuk ${selectedUser.name}...`" 
              required 
              :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black placeholder-zinc-400 shadow-[2px_2px_0px_#000000]'"
              class="flex-grow px-4 py-2.5 text-xs rounded-xl border-2 focus:outline-none font-medium" 
            />
            <button 
              type="submit" :disabled="sending && !pesanInput" 
              :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
              class="px-5 py-2.5 rounded-xl text-xs font-black border-2 transition-all shrink-0 disabled:opacity-50 cursor-pointer"
            >
              <span>Balas</span>
            </button>
          </form>
        </template>

        <!-- No User Selected -->
        <template v-else>
          <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
            <div class="w-16 h-16 rounded-2xl border-2 flex items-center justify-center text-3xl mb-4" :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white' : 'bg-zinc-100 border-black text-black'">💬</div>
            <h3 :class="isDark ? 'text-white' : 'text-black'" class="text-lg font-black mb-1">Live Chat Admin</h3>
            <p class="text-xs text-zinc-400 max-w-sm leading-relaxed mb-6">
              Pilih salah satu pelanggan dari daftar di sebelah kiri untuk mulai membaca & membalas pesan.
            </p>
            <button 
              v-if="userList.length > 0" @click="selectUser(userList[0])" 
              :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
              class="px-5 py-2.5 rounded-xl border-2 text-xs font-black cursor-pointer hover:-translate-y-0.5 transition-all"
            >
              Buka Obrolan Dengan {{ userList[0].name }} &rarr;
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
  middleware: 'admin'
})

useHead({
  title: 'Live Chat Admin - TokoBukuDigital'
})

const api = useApi()
const authStore = useAuthStore()
const { isDark } = useTheme()
const { getImageUrl } = useImageUrl()

const authUser = computed(() => authStore.user)

const userList = ref<any[]>([])
const selectedUser = ref<any | null>(null)
const isCollapsed = ref(false)

const chats = ref<any[]>([])
const pesanInput = ref('')
const loadingUsers = ref(true)
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
  const allOwn = selectedChats.every((c: any) => c.sender === 'admin')

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
    canDeleteForAll: chat.sender === 'admin',
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

const fetchUserList = async (silent = false) => {
  if (isFetchingUsers) return
  isFetchingUsers = true
  if (!silent) loadingUsers.value = true
  try {
    const res = await api.get('/api/chats')
    userList.value = res.data || []
  } catch (e) {
    console.error('Failed to fetch user chat list:', e)
  } finally {
    isFetchingUsers = false
    if (!silent) loadingUsers.value = false
  }
}

const selectUser = async (u: any) => {
  if (selectedUser.value?.id !== u.id) {
    chats.value = []
  }
  selectedUser.value = u
  isCollapsed.value = true
  cancelSelection()
  u.unread = 0
  try {
    await api.post('/api/chats/mark-as-read', { user_id: u.id })
  } catch (e) {}
  await fetchConversation()
}

const fetchConversation = async (silent = false) => {
  if (!selectedUser.value || isFetchingChat) return
  isFetchingChat = true
  if (!silent) loadingChats.value = true
  try {
    const res = await api.get(`/api/chats/user/${selectedUser.value.id}`)
    const incomingChats = res.data || []
    
    // Preserve any optimistic messages not yet returned by backend
    const optimisticMessages = chats.value.filter(c => c.is_optimistic)
    if (optimisticMessages.length > 0) {
      const serverTextSet = new Set(incomingChats.map((c: any) => `${c.sender}:${c.pesan}`))
      const stillPending = optimisticMessages.filter(m => !serverTextSet.has(`${m.sender}:${m.pesan}`))
      chats.value = [...incomingChats, ...stillPending]
    } else {
      chats.value = incomingChats
    }
    
    scrollToBottom()
  } catch (e: any) {
    if (e.status === 401 || e.statusCode === 401) {
      if (pollTimer) clearInterval(pollTimer)
    }
  } finally {
    isFetchingChat = false
    if (!silent) loadingChats.value = false
  }
}

const sendMessage = async () => {
  if (!selectedUser.value || !pesanInput.value.trim()) return

  const msg = pesanInput.value.trim()
  pesanInput.value = ''
  
  // Instant Optimistic Message Push (0ms delay)
  const tempId = Date.now()
  const optimisticMsg = {
    id: tempId,
    sender: 'admin',
    pesan: msg,
    created_at: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
    user_foto: selectedUser.value.foto,
    admin_foto: authUser.value?.foto,
    is_optimistic: true
  }
  chats.value.push(optimisticMsg)
  scrollToBottom()

  // Update user item in sidebar immediately
  if (selectedUser.value) {
    selectedUser.value.last_message = msg
    selectedUser.value.last_time = optimisticMsg.created_at
  }

  sending.value = true
  try {
    await api.post('/api/chats', {
      user_id: selectedUser.value.id,
      pesan: msg
    })
    
    // Broadcast instant sync across browser tabs/windows (0ms delay)
    if (typeof window !== 'undefined' && 'BroadcastChannel' in window) {
      try {
        const bc = new BroadcastChannel('live_chat_sync')
        bc.postMessage({ type: 'new_message', sender: 'admin', user_id: selectedUser.value.id, admin_id: authUser.value?.id })
        bc.close()
      } catch (e) {}
    }

    // Background sync
    await fetchConversation(true)
    fetchUserList(true)
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal membalas pesan')
    // Remove failed optimistic message
    chats.value = chats.value.filter(c => c.id !== tempId)
  } finally {
    sending.value = false
  }
}

let syncChannel: BroadcastChannel | null = null
let isFetchingChat = false
let isFetchingUsers = false

const handleVisibilityOrFocus = () => {
  if (typeof document !== 'undefined' && document.visibilityState === 'visible') {
    fetchUserList(true)
    if (selectedUser.value) {
      fetchConversation(true)
    }
  }
}

onMounted(async () => {
  await fetchUserList()
  if (userList.value.length > 0) {
    selectUser(userList.value[0])
  }

  // Cross-tab 0ms Realtime Channel
  if (typeof window !== 'undefined' && 'BroadcastChannel' in window) {
    try {
      syncChannel = new BroadcastChannel('live_chat_sync')
      syncChannel.onmessage = (event) => {
        if (event.data?.type === 'new_message') {
          fetchUserList(true)
          if (selectedUser.value && (!event.data.user_id || event.data.user_id === selectedUser.value.id)) {
            fetchConversation(true)
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
  
  // Safe 3-second Active Fallback Polling (BroadcastChannel handles 0ms instant sync)
  pollTimer = setInterval(() => {
    if (typeof document !== 'undefined' && document.visibilityState === 'visible') {
      if (selectedUser.value && !isFetchingChat) {
        fetchConversation(true)
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

<style scoped>
.pop-enter-active,
.pop-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.pop-enter-from,
.pop-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
