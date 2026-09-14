<template>
  <div class="space-y-6 font-['Space_Grotesk']">
    <!-- Header -->
    <div 
      :class="isDark ? 'bg-slate-900 border-slate-800 text-slate-100' : 'bg-[#FFF8EC] border-2 border-black shadow-[4px_4px_0px_#1A1A1A] text-slate-900'"
      class="p-5 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors duration-300"
    >
      <div>
        <h1 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-2xl font-black tracking-tight">
          Manajemen Kategori
        </h1>
        <p :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-bold mt-1">
          Kelola kategori buku. Nama kategori harus unik.
        </p>
      </div>
      <button
        @click="openCreateModal"
        :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[3px_3px_0px_#1A1A1A] active:translate-x-[1px] active:translate-y-[1px]'"
        class="px-4 py-2.5 rounded-xl text-xs font-black transition-all whitespace-nowrap"
      >
        + Tambah Kategori
      </button>
    </div>

    <!-- Table -->
    <div
      :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#FFF8EC] border-2.5 border-black shadow-[4px_4px_0px_#1A1A1A]'"
      class="rounded-2xl overflow-hidden"
    >
      <div v-if="loading" :class="isDark ? 'text-slate-500' : 'text-slate-700'" class="text-center py-14 text-sm font-black">
        Memuat kategori...
      </div>
      <div v-else-if="categories.length === 0" :class="isDark ? 'text-slate-500' : 'text-slate-700'" class="text-center py-14 text-sm font-black">
        <div class="text-4xl mb-2">🏷️</div>
        Belum ada kategori.
      </div>
      <table v-else class="w-full text-left text-xs">
        <thead>
          <tr :class="isDark ? 'bg-slate-800 text-slate-300 border-slate-700' : 'bg-[#FFE566] text-black border-b-2 border-black'" class="font-black uppercase text-[10px] tracking-wider">
            <th class="px-4 py-3">No</th>
            <th class="px-4 py-3">Nama Kategori</th>
            <th class="px-4 py-3">Jumlah Buku</th>
            <th class="px-4 py-3">Tanggal Dibuat</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-black/20 font-bold">
          <tr
            v-for="(cat, idx) in categories"
            :key="cat.id"
            :class="isDark ? 'border-slate-800 hover:bg-slate-800/50' : 'hover:bg-white/80'"
            class="transition-colors"
          >
            <td :class="isDark ? 'text-slate-500' : 'text-slate-700'" class="px-4 py-3 font-mono font-black">{{ idx + 1 }}</td>
            <td :class="isDark ? 'text-white' : 'text-slate-900'" class="px-4 py-3 font-black text-sm">{{ cat.nama_kategori }}</td>
            <td class="px-4 py-3">
              <span :class="isDark ? 'bg-indigo-900/60 text-indigo-300' : 'bg-[#C8F53F] text-black border border-black shadow-[1px_1px_0px_#1A1A1A]'" class="px-2.5 py-1 rounded-full font-black text-[10px]">
                {{ cat.books_count || 0 }} Buku
              </span>
            </td>
            <td :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="px-4 py-3">{{ cat.created_at }}</td>
            <td class="px-4 py-3 text-right">
              <div class="flex items-center justify-end gap-1.5">
                <button
                  @click="openEditModal(cat)"
                  :class="isDark ? 'bg-amber-900/40 text-amber-400 hover:bg-amber-900/70' : 'bg-[#FFE566] text-black border border-black hover:shadow-[2px_2px_0px_#1A1A1A] hover:translate-x-[-1px] hover:translate-y-[-1px]'"
                  class="w-8 h-8 rounded-lg flex items-center justify-center transition-all active:translate-x-[1px] active:translate-y-[1px]"
                  title="Edit Kategori"
                >
                  <LucidePencil class="w-3.5 h-3.5" />
                </button>
                <button
                  @click="deleteCategory(cat)"
                  :class="isDark ? 'bg-rose-900/40 text-rose-400 hover:bg-rose-900/70' : 'bg-[#FFB7B2] text-black border border-black hover:shadow-[2px_2px_0px_#1A1A1A] hover:translate-x-[-1px] hover:translate-y-[-1px]'"
                  class="w-8 h-8 rounded-lg flex items-center justify-center transition-all active:translate-x-[1px] active:translate-y-[1px]"
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

    <!-- Modal Form -->
    <div v-if="showModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div
        :class="isDark ? 'bg-slate-900 border-slate-700 text-slate-100' : 'bg-white border-slate-200 text-slate-900'"
        class="rounded-2xl p-6 max-w-md w-full shadow-2xl border space-y-4"
      >
        <div class="flex items-center justify-between">
          <h3 :class="isDark ? 'text-white' : 'text-slate-900'" class="font-black text-lg">
            {{ isEdit ? '✏️ Edit Kategori' : '➕ Tambah Kategori' }}
          </h3>
          <button
            @click="showModal = false"
            :class="isDark ? 'bg-slate-800 text-slate-400 hover:text-slate-200' : 'bg-slate-100 text-slate-500 hover:text-slate-700'"
            class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-black"
          >
            ✕
          </button>
        </div>

        <div v-if="errorMessage" class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-medium">
          {{ errorMessage }}
        </div>

        <form @submit.prevent="saveCategory" class="space-y-4">
          <div>
            <label :class="isDark ? 'text-slate-300' : 'text-slate-700'" class="block text-xs font-semibold mb-1">
              Nama Kategori <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.nama_kategori"
              type="text"
              required
              :class="isDark ? 'bg-slate-800 border-slate-700 text-white placeholder-slate-500' : 'bg-slate-50 border-slate-300 text-slate-900'"
              class="w-full px-4 py-2.5 text-xs rounded-xl border focus:ring-2 focus:ring-indigo-500 outline-none transition-colors"
              placeholder="Contoh: Novel Fiksi"
            />
          </div>

          <div :class="isDark ? 'border-slate-800' : 'border-slate-100'" class="flex justify-end gap-3 pt-2 border-t">
            <button
              type="button"
              @click="showModal = false"
              :class="isDark ? 'text-slate-400 hover:bg-slate-800' : 'text-slate-600 hover:bg-slate-100'"
              class="px-4 py-2 text-xs font-semibold rounded-xl transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="px-5 py-2 text-xs font-black text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md transition-all disabled:opacity-50 active:scale-95"
            >
              {{ submitting ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Pencil as LucidePencil, Trash2 as LucideTrash2 } from 'lucide-vue-next'

definePageMeta({
  middleware: 'admin'
})

const api = useApi()
const { isDark } = useTheme()
const categories = ref<any[]>([])
const loading = ref(true)
const showModal = ref(false)
const isEdit = ref(false)
const editId = ref<number | null>(null)
const submitting = ref(false)
const errorMessage = ref('')

const form = reactive({
  nama_kategori: ''
})

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
    if (isEdit.value && editId.value) {
      await api.put(`/api/admin/categories/${editId.value}`, form)
    } else {
      await api.post('/api/admin/categories', form)
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
  if (!confirm(`Yakin ingin menghapus kategori '${cat.nama_kategori}'?`)) return
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
