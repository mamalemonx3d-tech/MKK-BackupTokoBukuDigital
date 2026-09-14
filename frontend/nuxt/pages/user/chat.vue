<template>
  <!-- Full Screen Edge-to-Edge Container -->
  <div class="h-full flex flex-col overflow-hidden transition-colors duration-300">
    <div 
      :class="isDark ? 'bg-slate-950 text-slate-100' : 'bg-white text-slate-800'"
      class="flex-1 flex overflow-hidden relative"
    >
      <!-- ================= SIDEBAR KONTAK ADMIN ================= -->
      <aside 
        :class="[
          isDark ? 'bg-slate-900 border-slate-800' : 'bg-slate-50 border-slate-200',
          isCollapsed ? 'w-20' : 'w-72 sm:w-80'
        ]"
        class="border-r flex flex-col transition-all duration-300 shrink-0 z-10 relative select-none"
      >
        <!-- Expand / Collapse Toggle -->
        <button 
          @click="isCollapsed = !isCollapsed" 
          :class="isDark
            ? 'bg-indigo-600 border-slate-950 text-white hover:bg-indigo-500 shadow-indigo-950/50'
            : 'bg-indigo-600 border-white text-white hover:bg-indigo-700 shadow-indigo-500/30'"
          class="absolute -right-3.5 top-1/2 -translate-y-1/2 z-30 w-7 h-7 rounded-full border-2 shadow-lg flex items-center justify-center text-xs font-black transition-all hover:scale-110 active:scale-95 cursor-pointer"
          :title="isCollapsed ? 'Perbesar Sidebar Admin' : 'Kecilkan Sidebar Admin'"
        >
          <span v-if="isCollapsed">▶</span>
          <span v-else>◀</span>
        </button>

        <!-- Sidebar Header -->
        <div 
          :class="isDark ? 'border-slate-800' : 'border-slate-200'"
          class="p-4 border-b flex items-center justify-between shrink-0"
        >
          <div v-if="!isCollapsed" class="flex items-center gap-2 overflow-hidden">
            <span class="text-xl">💬</span>
            <div>
              <h3 :class="isDark ? 'text-white' : 'text-slate-900'" class="font-extrabold text-sm leading-tight">Daftar Admin</h3>
              <p :class="isDark ? 'text-slate-400' : 'text-slate-500'" class="text-[10px]">Pilih admin customer service</p>
            </div>
          </div>
          <div v-else class="mx-auto text-xl" title="Daftar Admin">💬</div>
        </div>

        <!-- Admin List -->
        <div class="flex-1 overflow-y-auto p-2 space-y-1.5 custom-scrollbar">
          <div v-if="loadingAdmins" class="text-center py-8 text-xs text-slate-400 font-medium">Memuat admin...</div>

          <div 
            v-for="admin in admins" :key="admin.id" 
            @click="selectAdmin(admin)"
            :class="[
              selectedAdmin?.id === admin.id 
                ? (isDark ? 'bg-indigo-900/40 border-indigo-500/50' : 'bg-indigo-50 border-indigo-200')
                : (isDark ? 'hover:bg-slate-900/80 border-transparent' : 'hover:bg-slate-100 border-transparent'),
              isCollapsed ? 'justify-center p-2.5' : 'p-3'
            ]"
            class="w-full flex items-center gap-3 rounded-2xl border transition-all cursor-pointer group relative"
          >
            <div class="relative shrink-0">
              <div 
                :class="selectedAdmin?.id === admin.id ? 'bg-indigo-600 text-white ring-2 ring-indigo-500/30' : 'bg-slate-700 text-white'"
                class="w-10 h-10 rounded-2xl flex items-center justify-center font-extrabold text-sm shadow-sm transition-transform group-hover:scale-105"
              >
                <img v-if="admin.foto" :src="admin.foto" :alt="admin.name" class="w-full h-full object-cover rounded-2xl" />
                <span v-else>{{ admin.name.charAt(0).toUpperCase() }}</span>
              </div>
              <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white shadow-sm"
                :style="isDark ? 'border-color: #0f172a' : 'border-color: #f8fafc'"
              ></span>
            </div>

            <div v-if="!isCollapsed" class="min-w-0 flex-1 text-left">
              <div class="flex items-center justify-between">
                <h4 :class="isDark ? 'text-white' : 'text-slate-900'" class="font-bold text-xs truncate">{{ admin.name }}</h4>
                <span class="text-[9px] text-emerald-400 font-bold">Online</span>
              </div>
              <p :class="isDark ? 'text-indigo-400' : 'text-indigo-600'" class="text-[10px] font-semibold truncate">{{ admin.role_title }}</p>
              <p :class="isDark ? 'text-slate-400' : 'text-slate-500'" class="text-[10px] truncate mt-0.5">{{ admin.last_message }}</p>
            </div>

            <span 
              v-if="isCollapsed" 
              class="absolute left-16 bg-slate-900 text-white text-xs font-bold px-2.5 py-1 rounded-xl shadow-xl border border-slate-700 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-30"
            >
              {{ admin.name }} ({{ admin.role_title }})
            </span>
          </div>
        </div>
      </aside>

      <!-- ================= MAIN CHAT AREA ================= -->
      <main class="flex-1 flex flex-col min-w-0 bg-transparent relative">
        <template v-if="selectedAdmin">

          <!-- Chat Header -->
          <div 
            :class="isDark ? 'bg-slate-950 border-slate-800 text-white' : 'bg-[#FFF8EC] border-[#1A1A1A] text-slate-900'"
            class="p-4 border-b flex items-center justify-between shrink-0 shadow-sm"
          >
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-md">
                <img v-if="selectedAdmin.foto" :src="selectedAdmin.foto" :alt="selectedAdmin.name" class="w-full h-full object-cover rounded-2xl" />
                <span v-else>{{ selectedAdmin.name.charAt(0).toUpperCase() }}</span>
              </div>
              <div>
                <h4 :class="isDark ? 'text-white' : 'text-slate-900'" class="font-extrabold text-sm leading-tight">{{ selectedAdmin.name }}</h4>
                <div class="flex items-center gap-2 text-[10px]" :class="isDark ? 'text-slate-300' : 'text-slate-600'">
                  <span :class="isDark ? 'text-indigo-400' : 'text-indigo-600'" class="font-bold">{{ selectedAdmin.role_title }}</span>
                  <span>•</span>
                  <span class="text-emerald-500 font-bold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Live Online
                  </span>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <!-- Selection Toolbar -->
              <transition name="fade">
                <div v-if="selectedIds.size > 0" class="flex items-center gap-2">
                  <span class="text-xs text-slate-300 font-semibold">{{ selectedIds.size }} dipilih</span>
                  <button @click="cancelSelection" class="text-xs px-3 py-1.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 font-bold transition-colors">
                    Batal
                  </button>
                  <!-- Delete action button — opens WA-style menu -->
                  <button
                    @click.stop="openDeleteMenu"
                    class="text-xs px-3 py-1.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black shadow-md shadow-red-500/20 transition-all flex items-center gap-1"
                  >
                    🗑️ Hapus
                  </button>
                </div>
              </transition>

              <button 
                v-if="isCollapsed" @click="isCollapsed = false"
                :class="isDark ? 'bg-slate-800 hover:bg-slate-700 text-slate-300' : 'bg-[#FFE566] hover:bg-[#ffd93f] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'"
                class="text-xs font-bold px-3 py-1.5 rounded-xl transition-colors"
              >
                👥 Ganti Admin
              </button>
            </div>
          </div>

          <!-- Select Mode Info Banner -->
          <transition name="slide-down">
            <div
              v-if="selectedIds.size > 0"
              :class="isDark ? 'bg-indigo-900/30 border-indigo-800 text-indigo-300' : 'bg-indigo-50 border-indigo-200 text-indigo-700'"
              class="px-4 py-2 border-b text-[11px] font-semibold flex items-center gap-2"
            >
              <span>✅</span>
              <span>Mode seleksi aktif — ketuk bubble untuk pilih/batal. Tekan <strong>Batal</strong> untuk keluar.</span>
            </div>
          </transition>

          <!-- Messages Stream -->
          <div 
            ref="chatContainer" 
            :class="isDark ? 'bg-slate-950' : 'bg-slate-50/60'"
            class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-3.5 custom-scrollbar"
            @click="closeContextMenu"
          >
            <div v-if="loadingChats && chats.length === 0" class="text-center py-12 text-xs text-slate-400 font-bold">
              Memuat percakapan...
            </div>
            <div v-else-if="chats.length === 0" class="text-center py-16 text-slate-400 text-xs font-bold">
              Belum ada obrolan khusus dengan {{ selectedAdmin.name }}. Tulis pesan pertama Anda!
            </div>

            <div 
              v-for="chat in chats" :key="chat.id" 
              :class="chat.sender === 'user' ? 'justify-end' : 'justify-start'" 
              class="flex items-end gap-2.5 group/msg"
            >
              <!-- Admin Avatar -->
              <div v-if="chat.sender === 'admin'" 
                :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A]'"
                class="w-7 h-7 rounded-full font-black text-xs flex items-center justify-center shrink-0 shadow-sm"
              >A</div>

              <!-- Bubble + delete icon wrapper -->
              <div :class="chat.sender === 'user' ? 'flex-row-reverse' : 'flex-row'" class="flex items-end gap-1.5 max-w-xs sm:max-w-md relative">

                <!-- Trash icon on hover (only for own messages, not in select mode) -->
                <button
                  v-if="chat.sender === 'user' && selectedIds.size === 0"
                  @click.stop="openContextMenuSingle(chat, $event)"
                  class="opacity-0 group-hover/msg:opacity-100 w-6 h-6 rounded-full bg-red-500/90 text-white flex items-center justify-center text-[10px] transition-opacity hover:bg-red-600 shrink-0 shadow"
                  title="Pilihan hapus"
                >🗑️</button>

                <!-- Bubble -->
                <!-- Long press 500ms → masuk select mode. Klik biasa hanya toggle jika sudah di select mode -->
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
                      ? 'bg-indigo-600 text-white rounded-t-2xl rounded-l-2xl shadow-md shadow-indigo-600/20' 
                      : (isDark 
                          ? 'bg-slate-800 text-slate-100 border border-slate-700 rounded-t-2xl rounded-r-2xl' 
                          : 'bg-[#FFF8EC] text-slate-900 border-2 border-black shadow-[2px_2px_0px_#1A1A1A] rounded-t-2xl rounded-r-2xl'),
                    selectedIds.has(chat.id) ? 'ring-2 ring-offset-1 ring-red-500 opacity-80 scale-[0.98]' : '',
                    selectedIds.size > 0 ? 'cursor-pointer' : 'cursor-default',
                    'select-none transition-all duration-150'
                  ]"
                  class="p-3.5 text-xs space-y-1 shadow-sm"
                >
                  <div v-if="selectedIds.has(chat.id)" class="flex items-center gap-1 mb-1">
                    <span class="w-4 h-4 rounded bg-red-500 text-white flex items-center justify-center text-[9px] font-black">✓</span>
                    <span class="text-[9px] font-bold opacity-75">Dipilih</span>
                  </div>
                  <p class="font-extrabold text-[9px] opacity-75">
                    {{ chat.sender === 'admin' ? selectedAdmin.name : 'Saya' }}
                  </p>
                  <p class="leading-relaxed whitespace-pre-wrap font-medium">{{ chat.pesan }}</p>
                  <p class="text-[8px] text-right opacity-60 font-mono">{{ chat.created_at }}</p>
                </div>
              </div>

              <!-- User Avatar -->
              <div v-if="chat.sender === 'user'" 
                :class="isDark ? 'bg-slate-700 text-white' : 'bg-[#D4B8FF] text-black border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A]'"
                class="w-7 h-7 rounded-full font-black text-xs flex items-center justify-center shrink-0 shadow-sm"
              >Me</div>
            </div>
          </div>

          <!-- Message Input Form - FIXED at bottom -->
          <form 
            @submit.prevent="sendMessage" 
            :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#FFF8EC] border-[#1A1A1A] border-t-2'"
            class="p-3.5 border-t flex items-center gap-2.5 shrink-0 relative z-10"
          >
            <input 
              v-model="pesanInput" type="text" 
              :placeholder="`Tulis pesan untuk ${selectedAdmin.name}...`" 
              required 
              :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500' : 'bg-white border-2 border-black text-slate-900 placeholder-slate-400 shadow-[2px_2px_0px_#1A1A1A]'"
              class="flex-grow px-4 py-3 text-xs rounded-2xl border focus:outline-none font-medium" 
            />
            <button 
              type="submit" :disabled="sending" 
              :class="isDark ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-500/20' : 'bg-[#C8F53F] hover:bg-[#b8e82f] text-black border-2 border-black shadow-[3px_3px_0px_#1A1A1A] active:translate-x-[1px] active:translate-y-[1px]'"
              class="px-5 py-3 rounded-2xl text-xs font-black shadow-md transition-all shrink-0 disabled:opacity-50 flex items-center gap-1.5"
            >
              <span :class="isDark ? 'text-white' : 'text-black'">Kirim</span><span>🚀</span>
            </button>
          </form>
        </template>

        <!-- No Admin Selected -->
        <template v-else>
          <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
            <div class="w-20 h-20 rounded-3xl bg-indigo-600/10 border border-indigo-500/20 text-indigo-500 flex items-center justify-center text-4xl mb-4 shadow-lg">💬</div>
            <h3 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-xl font-black mb-1">Selamat Datang di Live Chat Bantuan</h3>
            <p :class="isDark ? 'text-slate-400' : 'text-slate-500'" class="text-xs max-w-sm leading-relaxed mb-6">
              Silakan pilih salah satu Admin Support dari daftar di sebelah kiri untuk mulai berkonsultasi.
            </p>
            <button 
              v-if="admins.length > 0" @click="selectAdmin(admins[0])" 
              class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-lg shadow-indigo-500/20 transition-all hover:scale-105 active:scale-95"
            >
              Mulai Obrolan Dengan {{ admins[0].name }} &rarr;
            </button>
          </div>
        </template>
      </main>
    </div>

    <!-- ==================== WA-STYLE DELETE MENU (DROPDOWN) ==================== -->
    <transition name="pop">
      <div
        v-if="deleteMenu.show"
        :style="{ top: deleteMenu.y + 'px', left: deleteMenu.x + 'px' }"
        :class="isDark ? 'bg-slate-800 border-slate-700 text-slate-100' : 'bg-white border-slate-200 text-slate-800'"
        class="fixed z-50 rounded-2xl border shadow-2xl overflow-hidden min-w-[200px]"
        @click.stop
      >
        <!-- Menu Label -->
        <div :class="isDark ? 'border-slate-700 bg-slate-900/50' : 'border-slate-100 bg-slate-50'" class="px-4 py-2.5 border-b">
          <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Pilihan Hapus</p>
          <p class="text-[10px] font-semibold opacity-50 mt-0.5">{{ deleteMenu.count }} pesan dipilih</p>
        </div>

        <!-- "Hapus untuk Saya" — always available -->
        <button
          @click="doDeleteForMe"
          :disabled="processing"
          class="w-full flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-indigo-600 hover:text-white transition-colors group/item disabled:opacity-50"
        >
          <span class="text-lg group-hover/item:scale-110 transition-transform">🙈</span>
          <div class="text-left">
            <p class="font-extrabold">Hapus untuk Saya</p>
            <p class="text-[10px] opacity-60 font-normal">Hanya Anda yang tidak akan melihat pesan ini</p>
          </div>
        </button>

        <!-- "Hapus untuk Semua" — only if ALL selected are own messages -->
        <button
          v-if="deleteMenu.canDeleteForAll"
          @click="doDeleteForAll"
          :disabled="processing"
          class="w-full flex items-center gap-3 px-4 py-3 text-xs font-bold hover:bg-red-600 hover:text-white transition-colors group/item disabled:opacity-50 border-t"
          :class="isDark ? 'border-slate-700' : 'border-slate-100'"
        >
          <span class="text-lg group-hover/item:scale-110 transition-transform">🗑️</span>
          <div class="text-left">
            <p class="font-extrabold">Hapus untuk Semua Orang</p>
            <p class="text-[10px] opacity-60 font-normal">Pesan dihapus dari percakapan semua orang</p>
          </div>
        </button>

        <!-- Cancel -->
        <button
          @click="closeContextMenu"
          :class="isDark ? 'border-slate-700 text-slate-400 hover:bg-slate-700/50' : 'border-slate-100 text-slate-500 hover:bg-slate-50'"
          class="w-full flex items-center gap-3 px-4 py-2.5 text-xs font-bold transition-colors border-t"
        >
          <span class="text-base">✕</span>
          <span>Batal</span>
        </button>
      </div>
    </transition>

    <!-- Backdrop for context menu -->
    <div v-if="deleteMenu.show" class="fixed inset-0 z-40" @click="closeContextMenu"></div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const api = useApi()
const { isDark } = useTheme()

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

// ── Selection & Long Press ────────────────────────────────────────
const selectedIds = ref<Set<number>>(new Set())
let longPressTimer: ReturnType<typeof setTimeout> | null = null
let longPressTriggered = false

/**
 * Start 500ms long press timer — entering select mode on hold
 */
const startLongPress = (chat: any) => {
  longPressTriggered = false
  longPressTimer = setTimeout(() => {
    longPressTriggered = true
    // Vibrate on mobile if supported
    if (navigator.vibrate) navigator.vibrate(60)
    // Enter select mode with this message pre-selected
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

/**
 * On normal click/tap:
 * - If already in select mode → toggle this bubble
 * - If not in select mode → do nothing (long press needed)
 */
const onBubbleClick = (chat: any) => {
  if (longPressTriggered) {
    longPressTriggered = false
    return // already handled in long press
  }
  if (selectedIds.value.size > 0) {
    toggleSelect(chat)
  }
  // else: normal click, do nothing
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

// ── Delete Context Menu ───────────────────────────────────────────
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

/**
 * Open menu from the header "Hapus" button (batch mode)
 */
const openDeleteMenu = () => {
  // Work out if ALL selected are own messages (sender === 'user')
  const selectedChats = chats.value.filter((c: any) => selectedIds.value.has(c.id))
  const allOwn = selectedChats.every((c: any) => c.sender === 'user')

  // Position in center of screen
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

/**
 * Open menu from the trash icon on a single message bubble (hover)
 */
const openContextMenuSingle = (chat: any, event: MouseEvent) => {
  // Pre-select just this message
  selectedIds.value = new Set([chat.id])

  const rect = (event.target as HTMLElement).getBoundingClientRect()
  let x = rect.left - 220 // to the left of the trash icon
  let y = rect.top - 10

  // Clamp to viewport
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

/** Hapus untuk Saya — soft hide via chat_deletions table */
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

/** Hapus untuk Semua Orang — hard delete (only own messages) */
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
    toast.error(err.data?.message || 'Gagal menghapus pesan untuk semua')
  } finally {
    processing.value = false
    closeContextMenu()
  }
}

// ── Core Chat Logic ───────────────────────────────────────────────
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
        name: 'Administrator Toko Buku',
        username: 'admin',
        role_title: 'CS Support Utama',
        is_online: true,
        last_message: 'Ada yang bisa saya bantu?',
        foto: null,
      }]
    }
    loadingAdmins.value = false
  }
}

const selectAdmin = async (admin: any) => {
  selectedAdmin.value = admin
  isCollapsed.value = true
  cancelSelection()
  await fetchChats()
}

const fetchChats = async () => {
  if (!selectedAdmin.value) return
  loadingChats.value = true
  try {
    const res = await api.get('/api/chats', {
      params: { admin_id: selectedAdmin.value.id }
    })
    chats.value = res.data || []
    if (chats.value.length > 0) {
      try {
        await api.post('/api/chats/mark-as-read')
      } catch (err) {}
    }
    scrollToBottom()
  } catch (e: any) {
    if (e.status === 401 || e.statusCode === 401 || e.response?.status === 401) {
      if (pollTimer) clearInterval(pollTimer)
    } else {
      console.error('Failed to fetch chats:', e)
    }
  } finally {
    loadingChats.value = false
  }
}

const sendMessage = async () => {
  if (!pesanInput.value.trim() || !selectedAdmin.value) return
  sending.value = true
  const msg = pesanInput.value
  pesanInput.value = ''
  try {
    await api.post('/api/chats', {
      admin_id: selectedAdmin.value.id,
      pesan: msg,
    })
    await fetchChats()
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal mengirim pesan')
  } finally {
    sending.value = false
  }
}

onMounted(async () => {
  await fetchAdmins()
  if (admins.value.length > 0) selectAdmin(admins.value[0])
  pollTimer = setInterval(() => {
    if (selectedAdmin.value) fetchChats()
  }, 3000)
})

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer)
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-down-enter-active, .slide-down-leave-active { transition: all 0.2s; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-8px); }

.pop-enter-active { transition: all 0.15s cubic-bezier(0.34, 1.56, 0.64, 1); }
.pop-leave-active { transition: all 0.1s ease-in; }
.pop-enter-from, .pop-leave-to { opacity: 0; transform: scale(0.85); }
</style>
