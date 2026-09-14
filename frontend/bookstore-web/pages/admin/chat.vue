<template>
  <div class="h-full flex flex-col overflow-hidden transition-colors duration-300">
    <!-- Admin Header Mini Bar -->
    <div class="px-4 py-3 border-b flex items-center justify-between shrink-0" :class="isDark ? 'bg-slate-950 border-slate-800' : 'bg-[#FFF8EC] border-b-2 border-black'">
      <div class="flex items-center gap-2">
        <h1 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-base font-black">Live Chat Admin</h1>
        <span :class="isDark ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-emerald-100 text-emerald-800 border-emerald-400'" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold border">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Live Admin
        </span>
      </div>
    </div>

    <div class="flex-1 flex overflow-hidden relative">
      <!-- User Conversations Sidebar -->
      <aside :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-slate-50 border-slate-200'" class="w-full md:w-72 lg:w-80 border-r flex flex-col shrink-0">
        <div :class="isDark ? 'bg-slate-950 border-slate-800 text-slate-300' : 'bg-[#FFF8EC] border-slate-200 text-slate-900'" class="p-4 border-b font-bold text-xs uppercase tracking-wider">
          Daftar Obrolan Pelanggan
        </div>

        <div :class="isDark ? 'divide-slate-800' : 'divide-slate-200'" class="flex-grow overflow-y-auto divide-y custom-scrollbar">
          <div v-if="loadingUsers" :class="isDark ? 'text-slate-500' : 'text-slate-400'" class="text-center py-10 text-xs">Memuat pengguna...</div>
          <div v-else-if="userList.length === 0" :class="isDark ? 'text-slate-500' : 'text-slate-400'" class="text-center py-10 text-xs">Belum ada obrolan masuk.</div>
          <div 
            v-for="u in userList" 
            :key="u.id" 
            @click="selectUser(u)" 
            :class="selectedUser?.id === u.id 
              ? (isDark ? 'bg-indigo-900/40 border-l-4 border-indigo-500' : 'bg-indigo-50/80 border-l-4 border-indigo-600')
              : (isDark ? 'hover:bg-slate-800' : 'hover:bg-slate-100')" 
            class="p-4 cursor-pointer transition-colors flex items-center gap-3"
          >
            <div class="w-10 h-10 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center shrink-0 text-sm shadow-sm">
              <img v-if="u.foto" :src="u.foto" :alt="u.name" class="w-full h-full object-cover rounded-full" />
              <span v-else>{{ u.name.charAt(0).toUpperCase() }}</span>
            </div>
            <div class="overflow-hidden flex-grow">
              <div class="flex justify-between items-baseline">
                <h4 :class="isDark ? 'text-white' : 'text-slate-900'" class="font-bold text-xs truncate">{{ u.name }}</h4>
                <span :class="isDark ? 'text-slate-500' : 'text-slate-400'" class="text-[9px] ml-2 shrink-0">{{ u.last_time }}</span>
              </div>
              <div class="flex items-center justify-between mt-0.5">
                <p :class="isDark ? 'text-slate-400' : 'text-slate-500'" class="text-[11px] truncate flex-1">{{ u.last_message || 'Obrolan baru' }}</p>
                <span v-if="u.unread > 0" class="ml-2 px-1.5 py-0.5 rounded-full bg-rose-500 text-white text-[9px] font-black shrink-0 animate-pulse">
                  {{ u.unread }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </aside>

      <!-- Chat Box Panel -->
      <main class="flex-1 hidden md:flex flex-col min-w-0 bg-transparent relative">
        <div v-if="!selectedUser" :class="isDark ? 'text-slate-500' : 'text-slate-400'" class="flex-grow flex flex-col items-center justify-center p-8 text-center">
          <div class="text-5xl mb-3">💬</div>
          <h4 :class="isDark ? 'text-slate-300' : 'text-slate-700'" class="font-bold text-sm">Pilih Obrolan Pelanggan</h4>
          <p :class="isDark ? 'text-slate-500' : 'text-slate-400'" class="text-xs mt-1">Pilih pengguna dari daftar di sebelah kiri untuk mulai membaca & membalas pesan.</p>
        </div>

        <template v-else>
          <!-- Active User Header -->
          <div :class="isDark ? 'bg-slate-950 border-slate-800' : 'bg-slate-50 border-slate-100'" class="p-4 border-b flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">
                {{ selectedUser.name.charAt(0).toUpperCase() }}
              </div>
              <div>
                <h4 :class="isDark ? 'text-white' : 'text-slate-900'" class="font-bold text-sm">{{ selectedUser.name }}</h4>
                <p :class="isDark ? 'text-slate-400' : 'text-slate-400'" class="text-[10px]">@{{ selectedUser.username }}</p>
              </div>
            </div>
          </div>

          <div ref="chatContainer" 
            :class="isDark ? 'bg-slate-950' : 'bg-[#FAF7F0]'"
            class="flex-grow p-6 overflow-y-auto space-y-4 custom-scrollbar"
          >
            <div v-for="chat in chats" :key="chat.id" :class="chat.sender === 'admin' ? 'justify-end' : 'justify-start'" class="flex items-end gap-2">
              <div v-if="chat.sender === 'user'" 
                :class="isDark ? 'bg-slate-700 text-white' : 'bg-[#D4B8FF] text-black border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A]'"
                class="w-7 h-7 rounded-full font-bold text-xs flex items-center justify-center shrink-0"
              >
                U
              </div>

              <div 
                :class="chat.sender === 'admin' 
                  ? 'bg-indigo-600 text-white rounded-t-2xl rounded-l-2xl' 
                  : (isDark 
                      ? 'bg-slate-800 text-slate-100 border border-slate-700' 
                      : 'bg-[#FFF8EC] text-slate-800 border-2 border-black shadow-[2px_2px_0px_#1A1A1A]')" 
                class="p-3.5 max-w-sm text-xs space-y-1 rounded-t-2xl rounded-r-2xl shadow-sm"
              >
                <p class="font-semibold text-[10px] opacity-75">{{ chat.sender === 'admin' ? 'Saya (Admin)' : selectedUser.name }}</p>
                <p class="leading-relaxed whitespace-pre-wrap">{{ chat.pesan }}</p>
                <p class="text-[9px] text-right opacity-60">{{ chat.created_at }}</p>
              </div>

              <div v-if="chat.sender === 'admin'" 
                :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A]'"
                class="w-7 h-7 rounded-full font-bold text-xs flex items-center justify-center shrink-0"
              >
                A
              </div>
            </div>
          </div>

          <!-- Reply Input Form - FIXED at bottom -->
          <form @submit.prevent="sendMessage" 
            :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#FFF8EC] border-t-2 border-[#1A1A1A]'"
            class="p-4 border-t flex items-center gap-3 shrink-0 relative z-10"
          >
            <input 
              v-model="pesanInput" 
              type="text" 
              placeholder="Balas pesan ke pelanggan..." 
              required 
              :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500' : 'bg-white border-2 border-black text-slate-900 shadow-[2px_2px_0px_#1A1A1A] placeholder-slate-400'"
              class="flex-grow px-4 py-3 text-xs rounded-xl border focus:outline-none" 
            />
            <button type="submit" :disabled="sending" 
              :class="isDark ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[3px_3px_0px_#1A1A1A] hover:translate-x-[-1px] active:translate-x-[1px]'"
              class="px-5 py-3 rounded-xl text-xs font-black shadow-md transition-all shrink-0 disabled:opacity-50"
            >
              Balas 🚀
            </button>
          </form>
        </template>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'admin'
})

const api = useApi()
const { isDark } = useTheme()

const userList = ref<any[]>([])
const loadingUsers = ref(true)
const selectedUser = ref<any>(null)
const chats = ref<any[]>([])
const pesanInput = ref('')
const sending = ref(false)
const chatContainer = ref<HTMLElement | null>(null)
let pollTimer: any = null

const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight
    }
  })
}

const fetchUserList = async () => {
  try {
    const res = await api.get('/api/chats')
    userList.value = res.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loadingUsers.value = false
  }
}

const fetchConversation = async () => {
  if (!selectedUser.value) return
  try {
    const res = await api.get(`/api/chats/user/${selectedUser.value.id}`)
    chats.value = res.data || []
    scrollToBottom()
  } catch (e) {
    console.error(e)
  }
}

const selectUser = async (u: any) => {
  selectedUser.value = u
  u.unread = 0
  try {
    await api.post('/api/chats/mark-as-read', { user_id: u.id })
  } catch (e) {}
  await fetchConversation()
}

const sendMessage = async () => {
  if (!selectedUser.value || !pesanInput.value.trim()) return

  sending.value = true
  const msg = pesanInput.value
  pesanInput.value = ''

  try {
    const toast = useToast()
    await api.post('/api/chats', {
      user_id: selectedUser.value.id,
      pesan: msg
    })
    toast.success('Pesan berhasil dikirim!')
    await fetchConversation()
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal membalas pesan')
  } finally {
    sending.value = false
  }
}

onMounted(() => {
  fetchUserList()
  pollTimer = setInterval(() => {
    fetchUserList()
    if (selectedUser.value) fetchConversation()
  }, 3000)
})

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer)
})
</script>
