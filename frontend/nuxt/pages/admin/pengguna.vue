<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div
      :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-100' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'"
      class="p-5 sm:p-6 rounded-3xl border-2 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors duration-300"
    >
      <div class="space-y-1">
        <div 
          class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider border"
          :class="isDark ? 'bg-zinc-950 text-zinc-300 border-zinc-700' : 'bg-zinc-100 text-black border-black shadow-[1px_1px_0px_#000000]'"
        >
          <span>👥</span>
          <span>Akses & Hak Pengguna</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
          Manajemen Pengguna
        </h1>
        <p class="text-xs font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
          Kelola data akun pengguna, hak akses peran (Administrator atau Pembaca), dan keamanan akun.
        </p>
      </div>

      <div 
        :class="isDark ? 'bg-zinc-950 text-zinc-300 border-zinc-800' : 'bg-zinc-100 text-zinc-800 border-2 border-black shadow-[2px_2px_0px_#000000]'"
        class="px-3.5 py-2 rounded-2xl border text-xs font-mono font-bold self-start md:self-auto flex items-center gap-2"
      >
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span>Akses: Master Admin</span>
      </div>
    </div>

    <!-- Quick Metrics Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
      <!-- Total Pengguna -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Total Akun Terdaftar</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="isDark ? 'text-white' : 'text-black'">
            {{ users.length }}
          </h3>
        </div>
        <div :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0">
          👥
        </div>
      </div>

      <!-- Administrator -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Administrator</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="isDark ? 'text-white' : 'text-black'">
            {{ adminUsersCount }}
          </h3>
        </div>
        <div :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0">
          🛡️
        </div>
      </div>

      <!-- Pembaca / User -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Pembaca / Pelanggan</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="isDark ? 'text-white' : 'text-black'">
            {{ regularUsersCount }}
          </h3>
        </div>
        <div :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0">
          📖
        </div>
      </div>
    </div>

    <!-- Search & Filters Toolbar -->
    <div
      :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[3px_3px_0px_#000000]'"
      class="p-4 rounded-2xl border-2 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 transition-colors"
    >
      <!-- Search Input -->
      <div class="relative flex-1 min-w-[240px]">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari nama, username (@), atau email pengguna..."
          :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-zinc-50 border-2 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[3px_3px_0px_#000000] placeholder-zinc-400'"
          class="w-full pl-9 pr-8 py-2.5 rounded-xl text-xs font-bold outline-none transition-all border-2"
        />
        <LucideSearch class="w-4 h-4 text-zinc-400 absolute left-3 top-3 pointer-events-none" />
        <button 
          v-if="searchQuery" 
          @click="searchQuery = ''" 
          class="absolute right-3 top-2.5 text-xs text-zinc-400 hover:text-zinc-200 cursor-pointer font-bold"
        >
          ✕
        </button>
      </div>

      <!-- Filters & Sorting Group -->
      <div class="flex flex-wrap items-center gap-2.5">
        <!-- Filter Role -->
        <select
          v-model="filterRole"
          :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
          class="px-3 py-2.5 rounded-xl text-xs font-bold outline-none border-2 transition-all cursor-pointer"
        >
          <option value="all">Semua Peran</option>
          <option value="admin" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Administrator (Admin)</option>
          <option value="user" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">User (Pembaca)</option>
        </select>

        <!-- Sort By -->
        <select
          v-model="sortBy"
          :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
          class="px-3 py-2.5 rounded-xl text-xs font-bold outline-none border-2 transition-all cursor-pointer"
        >
          <option value="newest" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Terbaru</option>
          <option value="oldest" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Terlama</option>
          <option value="name_asc" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Nama (A - Z)</option>
          <option value="name_desc" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Nama (Z - A)</option>
          <option value="email_asc" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Email (A - Z)</option>
        </select>

        <!-- Reset Button -->
        <button
          v-if="searchQuery || filterRole !== 'all' || sortBy !== 'newest'"
          @click="resetFilters"
          :class="isDark ? 'bg-zinc-800 text-zinc-300 hover:text-white border-zinc-700' : 'bg-zinc-100 text-black border-2 border-black shadow-[1.5px_1.5px_0px_#000000] hover:bg-zinc-200'"
          class="px-3 py-2.5 rounded-xl text-xs font-black border transition-all cursor-pointer"
          title="Reset Filter"
        >
          Reset
        </button>
      </div>
    </div>

    <!-- Users Modern Table Container -->
    <div
      :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[4px_4px_0px_#000000]'"
      class="rounded-3xl overflow-hidden border-2 transition-colors duration-300"
    >
      <!-- Loading State -->
      <div v-if="loading" :class="isDark ? 'text-zinc-500' : 'text-zinc-600'" class="text-center py-20 text-xs font-black flex flex-col items-center gap-2">
        <span class="text-2xl animate-spin">⏳</span>
        <span>Memuat data pengguna terdaftar...</span>
      </div>

      <!-- Empty State (No Users) -->
      <div v-else-if="users.length === 0" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'" class="text-center py-20 text-xs font-bold space-y-2">
        <div class="text-4xl">👥</div>
        <p class="text-sm font-black" :class="isDark ? 'text-white' : 'text-black'">Belum ada pengguna terdaftar</p>
      </div>

      <!-- Empty State (Filter No Match) -->
      <div v-else-if="filteredUsers.length === 0" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'" class="text-center py-20 text-xs font-bold space-y-2">
        <div class="text-4xl">🔍</div>
        <p class="text-sm font-black" :class="isDark ? 'text-white' : 'text-black'">Pengguna tidak ditemukan</p>
        <p class="text-zinc-400">Tidak ada akun yang sesuai dengan kriteria pencarian.</p>
        <button
          @click="resetFilters"
          :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
          class="mt-2 px-4 py-2 rounded-xl text-xs font-black border-2 cursor-pointer inline-block"
        >
          Hapus Filter Pencarian
        </button>
      </div>

      <!-- Table Content -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr 
              :class="isDark ? 'bg-zinc-950 text-zinc-300 border-zinc-800' : 'bg-zinc-100 text-black border-b-2 border-black'" 
              class="font-black font-mono uppercase text-[10px] tracking-wider border-b"
            >
              <th class="px-4 py-3.5">Profil Pengguna</th>
              <th class="px-4 py-3.5">Username</th>
              <th class="px-4 py-3.5">Alamat Email</th>
              <th class="px-4 py-3.5 text-center">Peran Akun (Role)</th>
              <th class="px-4 py-3.5">Terdaftar</th>
              <th class="px-4 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y font-bold" :class="isDark ? 'divide-zinc-800/80' : 'divide-zinc-200'">
            <tr
              v-for="user in filteredUsers"
              :key="user.id"
              :class="isDark ? 'hover:bg-zinc-800/50' : 'hover:bg-zinc-50/90'"
              class="transition-colors group"
            >
              <!-- Avatar & Name -->
              <td class="px-4 py-3.5">
                <div class="flex items-center gap-3">
                  <div 
                    :class="isDark ? 'bg-zinc-950 text-white border-zinc-700' : 'bg-black text-white border-2 border-black shadow-[1.5px_1.5px_0px_#000000]'" 
                    class="w-10 h-10 rounded-2xl font-black flex items-center justify-center overflow-hidden shrink-0 border text-sm"
                  >
                    <img v-if="user.foto" :src="getUserAvatarUrl(user.foto)" :alt="user.name" class="w-full h-full object-cover" @error="user.foto = null" />
                    <span v-else>{{ user.name.charAt(0).toUpperCase() }}</span>
                  </div>
                  <div class="min-w-0">
                    <div class="font-black text-xs sm:text-sm tracking-tight truncate" :class="isDark ? 'text-white' : 'text-black'">
                      {{ user.name }}
                    </div>
                    <div class="text-[10px] font-mono text-zinc-400">
                      ID #{{ user.id }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Username -->
              <td class="px-4 py-3.5 font-mono text-xs" :class="isDark ? 'text-zinc-300' : 'text-zinc-700'">
                @{{ user.username || '-' }}
              </td>

              <!-- Email -->
              <td class="px-4 py-3.5 font-mono text-xs" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
                {{ user.email }}
              </td>

              <!-- Role Badge -->
              <td class="px-4 py-3.5 text-center whitespace-nowrap">
                <span
                  v-if="user.role === 'admin'"
                  :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
                  class="px-3 py-1 rounded-full text-[10px] font-black uppercase font-mono tracking-wider border inline-flex items-center gap-1.5"
                >
                  <span>🛡️</span>
                  <span>Administrator</span>
                </span>
                <span
                  v-else
                  :class="isDark ? 'bg-zinc-800 text-zinc-300 border-zinc-700' : 'bg-zinc-100 text-zinc-800 border-2 border-black shadow-[1.5px_1.5px_0px_#000000]'"
                  class="px-3 py-1 rounded-full text-[10px] font-black uppercase font-mono tracking-wider border inline-flex items-center gap-1.5"
                >
                  <span>📖</span>
                  <span>Pembaca</span>
                </span>
              </td>

              <!-- Tanggal Bergabung -->
              <td class="px-4 py-3.5 font-mono text-[11px] whitespace-nowrap" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
                {{ user.created_at ? user.created_at.split(' ')[0] : '-' }}
              </td>

              <!-- Actions -->
              <td class="px-4 py-3.5 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="openEditModal(user)"
                    :class="isDark ? 'bg-zinc-800 text-zinc-200 border-zinc-700 hover:bg-zinc-700 hover:text-white' : 'bg-white border-2 border-black text-black shadow-[1.5px_1.5px_0px_#000000] hover:bg-zinc-100'"
                    class="w-8 h-8 rounded-xl border flex items-center justify-center transition-all cursor-pointer hover:scale-105 active:scale-95"
                    title="Ubah Peran Pengguna"
                  >
                    <LucidePencil class="w-3.5 h-3.5" />
                  </button>
                  <button
                    @click="deleteUser(user)"
                    :class="isDark ? 'bg-rose-950/40 text-rose-300 border-rose-800 hover:bg-rose-900/70' : 'bg-rose-50 text-rose-700 border-2 border-rose-600 shadow-[1.5px_1.5px_0px_#e11d48] hover:bg-rose-100'"
                    class="w-8 h-8 rounded-xl border flex items-center justify-center transition-all cursor-pointer hover:scale-105 active:scale-95"
                    title="Hapus Pengguna"
                  >
                    <LucideTrash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Table Footer / Count Info -->
      <div 
        :class="isDark ? 'bg-zinc-950 border-zinc-800 text-zinc-400' : 'bg-zinc-50 border-zinc-200 text-zinc-600'" 
        class="px-5 py-3 border-t flex flex-col sm:flex-row items-center justify-between gap-2 text-xs font-mono font-bold"
      >
        <div>
          Menampilkan <span :class="isDark ? 'text-white' : 'text-black'">{{ filteredUsers.length }}</span> dari total <span :class="isDark ? 'text-white' : 'text-black'">{{ users.length }}</span> pengguna
        </div>
        <div class="text-[11px] text-zinc-500">
          Perubahan peran langsung berlaku pada sesi login pengguna terkait.
        </div>
      </div>
    </div>

    <!-- Edit User Role Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-100 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.8)]' : 'bg-white border-2 border-black text-black shadow-[6px_6px_0px_#000000]'"
        class="rounded-3xl border-2 p-6 sm:p-7 max-w-md w-full space-y-5 transition-all"
        role="dialog"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b pb-4" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
          <div>
            <h3 :class="isDark ? 'text-white' : 'text-black'" class="font-black text-lg sm:text-xl tracking-tight">
              ✏️ Edit Peran Pengguna
            </h3>
            <p class="text-xs text-zinc-400 font-medium mt-0.5">
              Sesuaikan wewenang akses akun untuk pengguna ini.
            </p>
          </div>
          <button
            @click="showModal = false"
            :class="isDark ? 'bg-zinc-800 text-zinc-400 hover:text-white hover:bg-zinc-700' : 'bg-zinc-100 text-zinc-700 hover:text-black border border-zinc-300'"
            class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-black transition-all cursor-pointer"
          >
            ✕
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="saveUser" class="space-y-4">
          <div class="space-y-1">
            <label class="block text-xs font-black font-mono uppercase text-zinc-400">Nama Lengkap</label>
            <input
              v-model="form.name"
              type="text"
              required
              :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
              class="w-full px-4 py-2.5 text-xs sm:text-sm rounded-xl border-2 font-bold outline-none transition-all"
            />
          </div>

          <div class="space-y-1">
            <label class="block text-xs font-black font-mono uppercase text-zinc-400">Username</label>
            <input
              v-model="form.username"
              type="text"
              :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
              class="w-full px-4 py-2.5 text-xs sm:text-sm rounded-xl border-2 font-mono font-bold outline-none transition-all"
            />
          </div>

          <div class="space-y-1">
            <label class="block text-xs font-black font-mono uppercase text-zinc-400">Alamat Email</label>
            <input
              v-model="form.email"
              type="email"
              required
              :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
              class="w-full px-4 py-2.5 text-xs sm:text-sm rounded-xl border-2 font-bold outline-none transition-all"
            />
          </div>

          <div class="space-y-1">
            <label class="block text-xs font-black font-mono uppercase text-zinc-400">Peran Akun (Role)</label>
            <select
              v-model="form.role"
              :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
              class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border-2 font-bold outline-none transition-all cursor-pointer"
            >
              <option value="user" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">User (Pembeli / Pembaca)</option>
              <option value="admin" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Administrator (Hak Akses Lengkap)</option>
            </select>
          </div>

          <div class="flex justify-end gap-2.5 pt-3 border-t" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
            <button
              type="button"
              @click="showModal = false"
              :class="isDark ? 'bg-zinc-800 text-zinc-300 hover:bg-zinc-700' : 'bg-zinc-100 text-black border-2 border-zinc-300 hover:bg-zinc-200'"
              class="px-4 py-2.5 text-xs font-black rounded-xl transition-all cursor-pointer"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="submitting"
              :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff] hover:bg-zinc-200' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
              class="px-5 py-2.5 text-xs font-black rounded-xl border-2 transition-all cursor-pointer hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50"
            >
              {{ submitting ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { 
  Search as LucideSearch, 
  Pencil as LucidePencil, 
  Trash2 as LucideTrash2 
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'admin'
})

useHead({
  title: 'Manajemen Pengguna - Admin TokoBukuDigital'
})

const api = useApi()
const { isDark } = useTheme()
const confirmModal = useConfirmModal()

const users = ref<any[]>([])
const loading = ref(true)
const showModal = ref(false)
const editUserObj = ref<any>(null)
const submitting = ref(false)

// Toolbar Search & Filter States
const searchQuery = ref('')
const filterRole = ref('all')
const sortBy = ref('newest')

const form = reactive({
  name: '',
  username: '',
  email: '',
  role: 'user'
})

const adminUsersCount = computed(() => {
  return users.value.filter(u => u.role === 'admin').length
})

const regularUsersCount = computed(() => {
  return users.value.filter(u => u.role !== 'admin').length
})

const getUserAvatarUrl = (photo: string) => {
  if (!photo) return null
  if (photo.startsWith('http://localhost/')) {
    return photo.replace('http://localhost/', `${api.apiBase}/`)
  }
  if (photo.startsWith('http') || photo.startsWith('data:')) {
    return photo
  }
  return `${api.apiBase}/storage/${photo.replace(/^\/+/, '')}`
}

// Filtered & Sorted Users
const filteredUsers = computed(() => {
  let result = [...users.value]

  // Search by query (Name, Username, Email)
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    result = result.filter(u => 
      (u.name && u.name.toLowerCase().includes(q)) ||
      (u.username && u.username.toLowerCase().includes(q)) ||
      (u.email && u.email.toLowerCase().includes(q))
    )
  }

  // Filter Role
  if (filterRole.value !== 'all') {
    result = result.filter(u => u.role === filterRole.value)
  }

  // Sorting
  switch (sortBy.value) {
    case 'oldest':
      result.sort((a, b) => a.id - b.id)
      break
    case 'name_asc':
      result.sort((a, b) => a.name.localeCompare(b.name))
      break
    case 'name_desc':
      result.sort((a, b) => b.name.localeCompare(a.name))
      break
    case 'email_asc':
      result.sort((a, b) => a.email.localeCompare(b.email))
      break
    case 'newest':
    default:
      result.sort((a, b) => b.id - a.id)
      break
  }

  return result
})

const resetFilters = () => {
  searchQuery.value = ''
  filterRole.value = 'all'
  sortBy.value = 'newest'
}

const fetchUsers = async () => {
  loading.value = true
  try {
    const res = await api.get('/api/admin/users')
    users.value = res.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const openEditModal = (u: any) => {
  editUserObj.value = u
  form.name = u.name
  form.username = u.username || ''
  form.email = u.email
  form.role = u.role
  showModal.value = true
}

const saveUser = async () => {
  if (!editUserObj.value) return
  submitting.value = true

  try {
    const toast = useToast()
    await api.put(`/api/admin/users/${editUserObj.value.id}`, form)
    toast.success('Peran dan data pengguna berhasil diubah!')
    showModal.value = false
    await fetchUsers()
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal mengubah peran pengguna')
  } finally {
    submitting.value = false
  }
}

const deleteUser = async (u: any) => {
  const isConfirmed = await confirmModal.danger(
    `Apakah Anda yakin ingin menghapus pengguna "${u.name}"? Tindakan ini akan menghapus data pengguna secara permanen.`,
    'Hapus Pengguna'
  )
  if (!isConfirmed) return

  try {
    const toast = useToast()
    await api.delete(`/api/admin/users/${u.id}`)
    toast.success('Pengguna berhasil dihapus!')
    await fetchUsers()
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal menghapus pengguna')
  }
}

onMounted(fetchUsers)
</script>
