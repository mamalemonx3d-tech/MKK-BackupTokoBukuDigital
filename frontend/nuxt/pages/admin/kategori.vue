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
          <span>🏷️</span>
          <span>Struktur Klasifikasi Buku</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
          Manajemen Kategori
        </h1>
        <p class="text-xs font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
          Kelola genre dan kategori literatur buku. Setiap nama kategori harus unik.
        </p>
      </div>

      <button
        @click="openCreateModal"
        :class="isDark ? 'bg-white text-black border-white shadow-[3px_3px_0px_#ffffff] hover:bg-zinc-200' : 'bg-black text-white border-black shadow-[3px_3px_0px_#000000] hover:bg-zinc-800'"
        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl text-xs font-black transition-all cursor-pointer hover:-translate-y-0.5 active:translate-y-0 border-2 shrink-0 self-start md:self-auto"
      >
        <span class="text-base font-bold">+</span>
        <span>Tambah Kategori Baru</span>
      </button>
    </div>

    <!-- Quick Metrics Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
      <!-- Total Kategori -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Total Kategori</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="isDark ? 'text-white' : 'text-black'">
            {{ categories.length }}
          </h3>
        </div>
        <div :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0">
          🏷️
        </div>
      </div>

      <!-- Total Buku Terkategori -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Total Buku Terkategori</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="isDark ? 'text-white' : 'text-black'">
            {{ totalBooksCount }}
          </h3>
        </div>
        <div :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0">
          📚
        </div>
      </div>

      <!-- Rata-rata Buku per Kategori -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Rata-rata per Kategori</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="isDark ? 'text-white' : 'text-black'">
            {{ averageBooksPerCategory }}
          </h3>
        </div>
        <div :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0">
          📊
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
          placeholder="Cari nama kategori..."
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

      <!-- Sorting Group -->
      <div class="flex items-center gap-2.5">
        <select
          v-model="sortBy"
          :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
          class="px-3 py-2.5 rounded-xl text-xs font-bold outline-none border-2 transition-all cursor-pointer"
        >
          <option value="newest" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Terbaru</option>
          <option value="oldest" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Terlama</option>
          <option value="name_asc" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Nama (A - Z)</option>
          <option value="name_desc" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Nama (Z - A)</option>
          <option value="books_high" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Buku Terbanyak</option>
          <option value="books_low" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Buku Tersedikit</option>
        </select>

        <button
          v-if="searchQuery || sortBy !== 'newest'"
          @click="resetFilters"
          :class="isDark ? 'bg-zinc-800 text-zinc-300 hover:text-white border-zinc-700' : 'bg-zinc-100 text-black border-2 border-black shadow-[1.5px_1.5px_0px_#000000] hover:bg-zinc-200'"
          class="px-3 py-2.5 rounded-xl text-xs font-black border transition-all cursor-pointer"
          title="Reset Filter"
        >
          Reset
        </button>
      </div>
    </div>

    <!-- Categories Modern Table Container -->
    <div
      :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[4px_4px_0px_#000000]'"
      class="rounded-3xl overflow-hidden border-2 transition-colors duration-300"
    >
      <!-- Loading State -->
      <div v-if="loading" :class="isDark ? 'text-zinc-500' : 'text-zinc-600'" class="text-center py-20 text-xs font-black flex flex-col items-center gap-2">
        <span class="text-2xl animate-spin">⏳</span>
        <span>Memuat data kategori buku...</span>
      </div>

      <!-- Empty State -->
      <div v-else-if="categories.length === 0" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'" class="text-center py-20 text-xs font-bold space-y-2">
        <div class="text-4xl">🏷️</div>
        <p class="text-sm font-black" :class="isDark ? 'text-white' : 'text-black'">Belum ada kategori terdaftar</p>
        <p class="text-zinc-400">Klik tombol "+ Tambah Kategori Baru" untuk membuat klasifikasi buku pertama Anda.</p>
      </div>

      <!-- Empty State (Filter No Match) -->
      <div v-else-if="filteredCategories.length === 0" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'" class="text-center py-20 text-xs font-bold space-y-2">
        <div class="text-4xl">🔍</div>
        <p class="text-sm font-black" :class="isDark ? 'text-white' : 'text-black'">Kategori tidak ditemukan</p>
        <p class="text-zinc-400">Tidak ada kategori yang cocok dengan kata kunci pencarian Anda.</p>
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
              <th class="px-4 py-3.5 w-16 text-center">No</th>
              <th class="px-4 py-3.5">Nama Kategori</th>
              <th class="px-4 py-3.5 text-center">Jumlah Koleksi Buku</th>
              <th class="px-4 py-3.5">Tanggal Dibuat</th>
              <th class="px-4 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y font-bold" :class="isDark ? 'divide-zinc-800/80' : 'divide-zinc-200'">
            <tr
              v-for="(cat, idx) in filteredCategories"
              :key="cat.id"
              :class="isDark ? 'hover:bg-zinc-800/50' : 'hover:bg-zinc-50/90'"
              class="transition-colors group"
            >
              <!-- Index Number -->
              <td class="px-4 py-3.5 text-center font-mono text-zinc-400">
                {{ idx + 1 }}
              </td>

              <!-- Category Name -->
              <td class="px-4 py-3.5">
                <div class="flex items-center gap-2.5">
                  <div 
                    :class="isDark ? 'bg-zinc-950 text-white border-zinc-700' : 'bg-black text-white border-2 border-black shadow-[1px_1px_0px_#000000]'" 
                    class="w-7 h-7 rounded-lg flex items-center justify-center font-mono font-black text-xs shrink-0 border"
                  >
                    🏷️
                  </div>
                  <span class="font-black text-xs sm:text-sm tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
                    {{ cat.nama_kategori }}
                  </span>
                </div>
              </td>

              <!-- Books Count Badge -->
              <td class="px-4 py-3.5 text-center whitespace-nowrap">
                <span 
                  :class="isDark ? 'bg-zinc-800 text-zinc-200 border-zinc-700' : 'bg-zinc-100 text-black border-2 border-black shadow-[1.5px_1.5px_0px_#000000]'" 
                  class="px-3 py-1 rounded-full font-black text-[10px] font-mono tracking-wide border inline-flex items-center gap-1.5"
                >
                  <span>📚</span>
                  <span>{{ cat.books_count || 0 }} Judul Buku</span>
                </span>
              </td>

              <!-- Created At -->
              <td class="px-4 py-3.5 font-mono text-[11px] whitespace-nowrap" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
                {{ cat.created_at ? cat.created_at.split(' ')[0] : '-' }}
              </td>

              <!-- Actions -->
              <td class="px-4 py-3.5 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="openEditModal(cat)"
                    :class="isDark ? 'bg-zinc-800 text-zinc-200 border-zinc-700 hover:bg-zinc-700 hover:text-white' : 'bg-white border-2 border-black text-black shadow-[1.5px_1.5px_0px_#000000] hover:bg-zinc-100'"
                    class="w-8 h-8 rounded-xl border flex items-center justify-center transition-all cursor-pointer hover:scale-105 active:scale-95"
                    title="Edit Kategori"
                  >
                    <LucidePencil class="w-3.5 h-3.5" />
                  </button>
                  <button
                    @click="deleteCategory(cat)"
                    :class="isDark ? 'bg-rose-950/40 text-rose-300 border-rose-800 hover:bg-rose-900/70' : 'bg-rose-50 text-rose-700 border-2 border-rose-600 shadow-[1.5px_1.5px_0px_#e11d48] hover:bg-rose-100'"
                    class="w-8 h-8 rounded-xl border flex items-center justify-center transition-all cursor-pointer hover:scale-105 active:scale-95"
                    title="Hapus Kategori"
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
          Menampilkan <span :class="isDark ? 'text-white' : 'text-black'">{{ filteredCategories.length }}</span> dari total <span :class="isDark ? 'text-white' : 'text-black'">{{ categories.length }}</span> kategori
        </div>
        <div class="text-[11px] text-zinc-500">
          Kategori digunakan saat menyaring katalog dan pengelompokan buku.
        </div>
      </div>
    </div>

    <!-- Modal Form (Tambah / Edit Kategori) -->
    <div v-if="showModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-100 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.8)]' : 'bg-white border-2 border-black text-black shadow-[6px_6px_0px_#000000]'"
        class="rounded-3xl border-2 p-6 sm:p-7 max-w-md w-full space-y-5 transition-all"
        role="dialog"
      >
        <div class="flex items-center justify-between border-b pb-4" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
          <div>
            <h3 :class="isDark ? 'text-white' : 'text-black'" class="font-black text-lg sm:text-xl tracking-tight">
              {{ isEdit ? '✏️ Edit Kategori' : '➕ Tambah Kategori Baru' }}
            </h3>
            <p class="text-xs text-zinc-400 font-medium mt-0.5">
              {{ isEdit ? 'Perbarui nama kategori yang sudah ada.' : 'Masukkan nama kategori baru untuk koleksi buku.' }}
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

        <!-- Error Message -->
        <div 
          v-if="errorMessage" 
          :class="isDark ? 'bg-rose-950/40 border-rose-800 text-rose-300' : 'bg-rose-50 border-2 border-rose-600 text-rose-900 shadow-[2px_2px_0px_#e11d48]'"
          class="p-3.5 rounded-xl border flex items-center gap-2.5 text-xs font-bold"
        >
          <span>⚠️</span>
          <span class="flex-1">{{ errorMessage }}</span>
        </div>

        <form @submit.prevent="saveCategory" class="space-y-4">
          <div class="space-y-1">
            <label class="block text-xs font-black font-mono uppercase text-zinc-400">
              Nama Kategori <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.nama_kategori"
              type="text"
              required
              placeholder="Contoh: Novel Fiksi & Sastra"
              :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] placeholder-zinc-400'"
              class="w-full px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold outline-none border-2 transition-all"
            />
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
              {{ submitting ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Tambah Kategori') }}
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
  title: 'Manajemen Kategori - Admin TokoBukuDigital'
})

const api = useApi()
const { isDark } = useTheme()
const confirmModal = useConfirmModal()

const categories = ref<any[]>([])
const loading = ref(true)
const showModal = ref(false)
const isEdit = ref(false)
const editId = ref<number | null>(null)
const submitting = ref(false)
const errorMessage = ref('')

// Search & Sort States
const searchQuery = ref('')
const sortBy = ref('newest')

const form = reactive({
  nama_kategori: ''
})

const totalBooksCount = computed(() => {
  return categories.value.reduce((acc, cat) => acc + (Number(cat.books_count) || 0), 0)
})

const averageBooksPerCategory = computed(() => {
  if (categories.value.length === 0) return 0
  return (totalBooksCount.value / categories.value.length).toFixed(1)
})

// Filtered & Sorted Categories
const filteredCategories = computed(() => {
  let result = [...categories.value]

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    result = result.filter(cat => cat.nama_kategori && cat.nama_kategori.toLowerCase().includes(q))
  }

  switch (sortBy.value) {
    case 'oldest':
      result.sort((a, b) => a.id - b.id)
      break
    case 'name_asc':
      result.sort((a, b) => a.nama_kategori.localeCompare(b.nama_kategori))
      break
    case 'name_desc':
      result.sort((a, b) => b.nama_kategori.localeCompare(a.nama_kategori))
      break
    case 'books_high':
      result.sort((a, b) => (Number(b.books_count) || 0) - (Number(a.books_count) || 0))
      break
    case 'books_low':
      result.sort((a, b) => (Number(a.books_count) || 0) - (Number(b.books_count) || 0))
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
  sortBy === ref('newest')
}

const fetchCategories = async () => {
  loading.value = true
  try {
    const res = await api.get('/api/categories')
    categories.value = res.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  isEdit.value = false
  editId.value = null
  form.nama_kategori = ''
  errorMessage.value = ''
  showModal.value = true
}

const openEditModal = (cat: any) => {
  isEdit.value = true
  editId.value = cat.id
  form.nama_kategori = cat.nama_kategori
  errorMessage.value = ''
  showModal.value = true
}

const saveCategory = async () => {
  submitting.value = true
  errorMessage.value = ''

  try {
    const toast = useToast()
    if (isEdit.value && editId.value) {
      await api.put(`/api/admin/categories/${editId.value}`, form)
      toast.success('Kategori berhasil diperbarui!')
    } else {
      await api.post('/api/admin/categories', form)
      toast.success('Kategori baru berhasil ditambahkan!')
    }
    showModal.value = false
    await fetchCategories()
  } catch (err: any) {
    errorMessage.value = err.data?.message || err.data?.errors?.nama_kategori?.[0] || 'Gagal menyimpan kategori'
  } finally {
    submitting.value = false
  }
}

const deleteCategory = async (cat: any) => {
  const isConfirmed = await confirmModal.danger(
    `Apakah Anda yakin ingin menghapus kategori "${cat.nama_kategori}"? Tindakan ini tidak dapat dibatalkan.`,
    'Hapus Kategori'
  )
  if (!isConfirmed) return

  try {
    const toast = useToast()
    await api.delete(`/api/admin/categories/${cat.id}`)
    toast.success('Kategori berhasil dihapus!')
    await fetchCategories()
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal menghapus kategori')
  }
}

onMounted(fetchCategories)
</script>
