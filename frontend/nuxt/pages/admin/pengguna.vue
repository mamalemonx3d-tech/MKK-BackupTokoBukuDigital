<template>
  <div class="space-y-6 font-['Space_Grotesk']">
    <!-- Header -->
    <div
      :class="isDark ? 'bg-slate-900 border-slate-800 text-slate-100' : 'bg-[#FFF8EC] border-2 border-black shadow-[4px_4px_0px_#1A1A1A] text-slate-900'"
      class="p-5 rounded-2xl transition-colors duration-300"
    >
      <h1 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-2xl font-black tracking-tight">
        Manajemen Pengguna
      </h1>
      <p :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-bold mt-1">
        Daftar pengguna terdaftar. Kelola peran (Admin / User).
      </p>
    </div>

    <!-- Table -->
    <div
      :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#FFF8EC] border-2.5 border-black shadow-[4px_4px_0px_#1A1A1A]'"
      class="rounded-2xl overflow-hidden transition-colors"
    >
      <div v-if="loading" :class="isDark ? 'text-slate-500' : 'text-slate-700'" class="text-center py-14 text-sm font-black">
        Memuat data pengguna...
      </div>
      <div v-else class="overflow-x-auto md:overflow-x-visible">
        <table class="w-full text-left text-xs">
          <thead>
            <tr :class="isDark ? 'bg-slate-800 text-slate-300 border-slate-700' : 'bg-[#FFE566] text-black border-b-2 border-black'" class="font-black uppercase text-[10px] tracking-wider">
              <th class="px-4 py-3">Foto</th>
              <th class="px-4 py-3">Nama Lengkap</th>
              <th class="px-4 py-3">Username</th>
              <th class="px-4 py-3">Email</th>
              <th class="px-4 py-3">No. Telp</th>
              <th class="px-4 py-3">Role</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-black/20 font-bold">
            <tr
              v-for="user in users"
              :key="user.id"
              :class="isDark ? 'border-slate-800 hover:bg-slate-800/50' : 'hover:bg-white/80'"
              class="transition-colors"
            >
              <td class="px-4 py-3">
                <div :class="isDark ? 'bg-indigo-600' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A]'" class="w-9 h-9 rounded-full font-black flex items-center justify-center overflow-hidden shrink-0">
                  <img v-if="user.foto" :src="user.foto" :alt="user.name" class="w-full h-full object-cover" />
                  <span v-else class="text-sm">{{ user.name.charAt(0).toUpperCase() }}</span>
                </div>
              </td>
              <td :class="isDark ? 'text-white' : 'text-slate-900'" class="px-4 py-3 font-black text-sm">{{ user.name }}</td>
              <td :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="px-4 py-3 font-mono font-bold">@{{ user.username }}</td>
              <td :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="px-4 py-3">{{ user.email }}</td>
              <td :class="isDark ? 'text-slate-500' : 'text-slate-700'" class="px-4 py-3">{{ user.no_telp || '-' }}</td>
              <td class="px-4 py-3">
                <span
                  :class="user.role === 'admin'
                    ? (isDark ? 'bg-purple-900/50 text-purple-300 border-purple-700' : 'bg-[#D4B8FF] text-black border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A]')
                    : (isDark ? 'bg-slate-800 text-slate-300 border-slate-700' : 'bg-[#FFF8EC] text-black border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A]')"
                  class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wide"
                >
                  {{ user.role }}
                </span>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="openEditModal(user)"
                    :class="isDark ? 'bg-indigo-900/40 text-indigo-400 hover:bg-indigo-900/70' : 'bg-[#D4B8FF] text-black border border-black hover:shadow-[2px_2px_0px_#1A1A1A] hover:translate-x-[-1px] hover:translate-y-[-1px]'"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all active:translate-x-[1px] active:translate-y-[1px]"
                    title="Edit Peran"
                  >
                    <LucidePencil class="w-3.5 h-3.5" />
                  </button>
                  <button
                    @click="deleteUser(user)"
                    :class="isDark ? 'bg-rose-900/40 text-rose-400 hover:bg-rose-900/70' : 'bg-[#FFB7B2] text-black border border-black hover:shadow-[2px_2px_0px_#1A1A1A] hover:translate-x-[-1px] hover:translate-y-[-1px]'"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-all active:translate-x-[1px] active:translate-y-[1px]"
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
    </div>

    <!-- Edit User Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div
        :class="isDark ? 'bg-slate-900 border-slate-700 text-slate-100' : 'bg-white border-slate-200 text-slate-900'"
        class="rounded-2xl p-6 max-w-md w-full shadow-2xl border space-y-4"
      >
        <div class="flex items-center justify-between">
          <h3 :class="isDark ? 'text-white' : 'text-slate-900'" class="font-black text-lg">✏️ Edit Peran Pengguna</h3>
          <button
            @click="showModal = false"
            :class="isDark ? 'bg-slate-800 text-slate-400 hover:text-slate-200' : 'bg-slate-100 text-slate-500 hover:text-slate-700'"
            class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-black"
          >
            ✕
          </button>
        </div>

        <form @submit.prevent="saveUser" class="space-y-4">
          <div>
            <label :class="isDark ? 'text-slate-300' : 'text-slate-700'" class="block text-xs font-semibold mb-1">Nama</label>
            <input
              v-model="form.name"
              type="text"
              required
              :class="isDark ? 'bg-slate-800 border-slate-700 text-white' : 'bg-slate-50 border-slate-300 text-slate-900'"
              class="w-full px-4 py-2.5 text-xs rounded-xl border focus:ring-2 focus:ring-indigo-500 outline-none transition-colors"
            />
          </div>

          <div>
            <label :class="isDark ? 'text-slate-300' : 'text-slate-700'" class="block text-xs font-semibold mb-1">Role Peran</label>
            <select
              v-model="form.role"
              :class="isDark ? 'bg-slate-800 border-slate-700 text-white' : 'bg-slate-50 border-slate-300 text-slate-900'"
              class="w-full px-4 py-2.5 text-xs rounded-xl border focus:ring-2 focus:ring-indigo-500 outline-none transition-colors"
            >
              <option value="user">User / Pembeli</option>
              <option value="admin">Administrator</option>
            </select>
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
              {{ submitting ? 'Menyimpan...' : 'Simpan Perubahan' }}
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
const users = ref<any[]>([])
const loading = ref(true)
const showModal = ref(false)
const editUserObj = ref<any>(null)
const submitting = ref(false)

const form = reactive({
  name: '',
  username: '',
  email: '',
  no_telp: '',
  role: 'user'
})

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
  form.username = u.username
  form.email = u.email
  form.no_telp = u.no_telp || ''
  form.role = u.role
  showModal.value = true
}

const saveUser = async () => {
  if (!editUserObj.value) return
  submitting.value = true

  try {
    const toast = useToast()
    await api.put(`/api/admin/users/${editUserObj.value.id}`, form)
    toast.success('Peran pengguna berhasil diubah!')
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
  if (!confirm(`Yakin menghapus pengguna '${u.name}'?`)) return
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
