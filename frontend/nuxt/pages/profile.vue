<template>
  <div class="max-w-4xl mx-auto space-y-6 sm:space-y-8">
    <!-- Top Back Navigation -->
    <div class="flex items-center justify-between">
      <NuxtLink
        :to="authStore.isAdmin ? '/admin/kategori' : '/katalog'"
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-300 hover:text-white hover:border-zinc-500' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-50'"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black transition-all cursor-pointer hover:-translate-y-0.5 active:translate-y-0 border-2"
      >
        <span class="text-sm">&larr;</span>
        <span>{{ authStore.isAdmin ? 'Kembali ke Panel Admin' : 'Kembali ke Katalog' }}</span>
      </NuxtLink>

      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-400' : 'bg-zinc-100 border-2 border-black text-black shadow-[1.5px_1.5px_0px_#000000]'"
        class="text-xs font-mono font-bold uppercase px-3.5 py-1.5 rounded-xl border hidden sm:block"
      >
        {{ authStore.isAdmin ? 'Akses: Administrator' : 'Akses: Pembaca' }}
      </div>
    </div>

    <!-- Profile Header Summary Card -->
    <div
      :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-100' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'"
      class="rounded-3xl border-2 p-6 sm:p-8 flex flex-col sm:flex-row items-center sm:items-start gap-6 transition-colors duration-300 relative overflow-hidden"
    >
      <!-- Large Avatar Preview -->
      <div class="relative group shrink-0">
        <div
          :class="isDark ? 'border-zinc-700 bg-zinc-950 text-white' : 'border-2 border-black bg-black text-white shadow-[3px_3px_0px_#000000]'"
          class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl overflow-hidden border-2 flex items-center justify-center font-black text-3xl sm:text-4xl"
        >
          <img
            v-if="(avatarPreview || userAvatarUrl) && !imageLoadError"
            :src="avatarPreview || userAvatarUrl"
            alt="Foto Profil"
            class="w-full h-full object-cover"
            @error="imageLoadError = true"
          />
          <span v-else>
            {{ (authStore.user?.name || 'U').charAt(0).toUpperCase() }}
          </span>
        </div>
      </div>

      <!-- Info Details -->
      <div class="flex-1 text-center sm:text-left space-y-2">
        <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 justify-center sm:justify-start">
          <h1 class="text-2xl sm:text-3xl font-black tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
            {{ authStore.user?.name || 'Pengguna' }}
          </h1>
          <span
            :class="authStore.isAdmin 
              ? (isDark ? 'bg-white text-black border-white' : 'bg-black text-white border-black shadow-[1.5px_1.5px_0px_#000000]') 
              : (isDark ? 'bg-zinc-800 text-zinc-200 border-zinc-700' : 'bg-zinc-100 text-black border-2 border-black shadow-[1.5px_1.5px_0px_#000000]')"
            class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border self-center sm:self-auto"
          >
            {{ authStore.isAdmin ? '🛡️ Administrator' : '📖 Pembaca' }}
          </span>
        </div>

        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 text-xs font-mono text-zinc-400">
          <span v-if="authStore.user?.username" class="font-bold text-zinc-300">
            @{{ authStore.user.username }}
          </span>
          <span>•</span>
          <span>{{ authStore.user?.email || '-' }}</span>
        </div>

        <div class="pt-2 flex flex-wrap items-center justify-center sm:justify-start gap-4 text-xs font-medium">
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg" :class="isDark ? 'bg-emerald-950/60 text-emerald-300 border border-emerald-800' : 'bg-emerald-50 text-emerald-800 border border-emerald-300'">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Akun Aktif & Terverifikasi</span>
          </div>
        </div>
      </div>
    </div>

    <!-- CARD 1: UBAH INFORMASI PROFIL & FOTO -->
    <div
      :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-100' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'"
      class="rounded-3xl border-2 p-6 sm:p-8 space-y-6 transition-colors duration-300"
    >
      <!-- Section Title -->
      <div class="flex items-center justify-between border-b pb-4" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
        <div class="flex items-center gap-2.5">
          <span class="text-xl">👤</span>
          <div>
            <h2 class="text-base sm:text-lg font-black tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
              Informasi Profil & Foto
            </h2>
            <p class="text-xs text-zinc-400 font-medium">Perbarui nama lengkap, email, dan foto profil akun Anda.</p>
          </div>
        </div>
        <span
          :class="isDark ? 'bg-zinc-800 text-zinc-300 border-zinc-700' : 'bg-zinc-100 text-zinc-800 border border-black shadow-[1px_1px_0px_#000000]'"
          class="text-[11px] font-mono font-bold uppercase px-2.5 py-1 rounded-lg border hidden sm:block"
        >
          Data Akun
        </span>
      </div>

      <!-- Alert Error Profil -->
      <div
        v-if="profileError"
        :class="isDark ? 'bg-rose-950/40 border-rose-800 text-rose-300' : 'bg-rose-50 border-2 border-rose-600 text-rose-900 shadow-[2px_2px_0px_#e11d48]'"
        class="p-4 rounded-xl border flex items-center gap-3 text-xs font-bold"
      >
        <span class="text-base">⚠️</span>
        <span>{{ profileError }}</span>
      </div>

      <!-- Alert Success Profil -->
      <div
        v-if="profileSuccess"
        :class="isDark ? 'bg-emerald-950/40 border-emerald-700 text-emerald-300' : 'bg-emerald-50 border-2 border-emerald-600 text-emerald-900 shadow-[2px_2px_0px_#059669]'"
        class="p-4 rounded-xl border flex items-center gap-3 text-xs font-bold"
      >
        <span class="text-base">✓</span>
        <span>{{ profileSuccess }}</span>
      </div>

      <form @submit.prevent="handleSaveProfile" class="space-y-5">
        <!-- Upload Foto Profil -->
        <div class="space-y-2">
          <label class="block text-xs font-black uppercase tracking-wider font-mono text-zinc-400">
            Foto Profil
          </label>

          <div
            :class="isDark ? 'bg-zinc-950 border-zinc-800' : 'bg-zinc-50 border-2 border-black shadow-[2px_2px_0px_#000000]'"
            class="flex flex-col sm:flex-row items-center sm:items-start gap-4 p-4 rounded-2xl border"
          >
            <!-- Avatar Mini Preview -->
            <div
              :class="isDark ? 'border-zinc-700 bg-zinc-900 text-white' : 'border-2 border-black bg-black text-white shadow-[2px_2px_0px_#000000]'"
              class="w-16 h-16 rounded-2xl overflow-hidden border flex items-center justify-center font-black text-2xl shrink-0"
            >
              <img
                v-if="(avatarPreview || userAvatarUrl) && !previewLoadError"
                :src="avatarPreview || userAvatarUrl"
                alt="Preview Avatar"
                class="w-full h-full object-cover"
                @error="previewLoadError = true"
              />
              <span v-else>
                {{ (profileForm.name || 'U').charAt(0).toUpperCase() }}
              </span>
            </div>

            <!-- Upload Buttons & Info -->
            <div class="flex-1 text-center sm:text-left space-y-2">
              <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                <label
                  :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff] hover:bg-zinc-200' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
                  class="inline-flex items-center gap-2 py-2 px-4 rounded-xl text-xs font-black transition-all border-2 cursor-pointer hover:-translate-y-0.5 active:translate-y-0"
                >
                  <span>📷</span>
                  <span>Pilih Foto Baru</span>
                  <input
                    type="file"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                    class="hidden"
                    @change="handleAvatarFileChange"
                  />
                </label>

                <button
                  v-if="avatarPreview || authStore.user?.foto"
                  type="button"
                  @click="handleRemoveAvatar"
                  :class="isDark ? 'bg-rose-950/50 text-rose-300 border-rose-800 hover:bg-rose-900' : 'bg-rose-50 text-rose-700 border-2 border-rose-600 shadow-[1.5px_1.5px_0px_#e11d48] hover:bg-rose-100'"
                  class="py-2 px-3.5 rounded-xl text-xs font-black transition-all border cursor-pointer hover:-translate-y-0.5"
                >
                  Hapus Foto
                </button>
              </div>
              <p class="text-[11px] text-zinc-400 font-medium">
                Format yang didukung: JPG, PNG, GIF, WebP (Maksimal 2 MB).
              </p>
            </div>
          </div>
        </div>

        <!-- Form Inputs Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Nama Lengkap -->
          <div class="space-y-1.5 sm:col-span-2">
            <label class="block text-xs font-black uppercase tracking-wider font-mono text-zinc-400">
              Nama Lengkap <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="profileForm.name"
              type="text"
              required
              placeholder="Masukkan nama lengkap"
              :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[3px_3px_0px_#000000] placeholder-zinc-400'"
              class="w-full px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold outline-none transition-all border-2"
            />
          </div>

          <!-- Username -->
          <div class="space-y-1.5">
            <label class="block text-xs font-black uppercase tracking-wider font-mono text-zinc-400">
              Username
            </label>
            <div class="relative">
              <span class="absolute left-3.5 top-2.5 text-zinc-400 font-mono text-xs sm:text-sm">@</span>
              <input
                v-model="profileForm.username"
                type="text"
                placeholder="username"
                :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[3px_3px_0px_#000000] placeholder-zinc-400'"
                class="w-full pl-8 pr-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold outline-none transition-all border-2 font-mono"
              />
            </div>
          </div>

          <!-- Email -->
          <div class="space-y-1.5">
            <label class="block text-xs font-black uppercase tracking-wider font-mono text-zinc-400">
              Alamat Email <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="profileForm.email"
              type="email"
              required
              placeholder="nama@email.com"
              :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[3px_3px_0px_#000000] placeholder-zinc-400'"
              class="w-full px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold outline-none transition-all border-2"
            />
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-2 border-t" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
          <button
            type="submit"
            :disabled="isProfileSaving"
            :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff] hover:bg-zinc-200' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
            class="px-6 py-3 rounded-xl font-black text-xs sm:text-sm transition-all border-2 flex items-center gap-2 cursor-pointer hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="isProfileSaving" class="animate-spin text-sm">⏳</span>
            <span>{{ isProfileSaving ? 'Menyimpan Perubahan...' : 'Simpan Perubahan Profil' }}</span>
          </button>
        </div>
      </form>
    </div>

    <!-- CARD 2: KEAMANAN AKUN (GANTI PASSWORD) -->
    <div
      :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-100' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'"
      class="rounded-3xl border-2 p-6 sm:p-8 space-y-6 transition-colors duration-300"
    >
      <!-- Section Title -->
      <div class="flex items-center justify-between border-b pb-4" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
        <div class="flex items-center gap-2.5">
          <span class="text-xl">🛡️</span>
          <div>
            <h2 class="text-base sm:text-lg font-black tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
              Keamanan Akun & Ganti Password
            </h2>
            <p class="text-xs text-zinc-400 font-medium">Perbarui password akun Anda secara berkala untuk menjaga keamanan data.</p>
          </div>
        </div>
        <span
          :class="isDark ? 'bg-zinc-800 text-zinc-300 border-zinc-700' : 'bg-zinc-100 text-zinc-800 border border-black shadow-[1px_1px_0px_#000000]'"
          class="text-[11px] font-mono font-bold uppercase px-2.5 py-1 rounded-lg border hidden sm:block"
        >
          Keamanan
        </span>
      </div>

      <!-- Alert Error Password -->
      <div
        v-if="passwordError"
        :class="isDark ? 'bg-rose-950/40 border-rose-800 text-rose-300' : 'bg-rose-50 border-2 border-rose-600 text-rose-900 shadow-[2px_2px_0px_#e11d48]'"
        class="p-4 rounded-xl border flex items-center gap-3 text-xs font-bold"
      >
        <span class="text-base">⚠️</span>
        <span>{{ passwordError }}</span>
      </div>

      <!-- Alert Success Password -->
      <div
        v-if="passwordSuccess"
        :class="isDark ? 'bg-emerald-950/40 border-emerald-700 text-emerald-300' : 'bg-emerald-50 border-2 border-emerald-600 text-emerald-900 shadow-[2px_2px_0px_#059669]'"
        class="p-4 rounded-xl border flex items-center gap-3 text-xs font-bold"
      >
        <span class="text-base">✓</span>
        <span>{{ passwordSuccess }}</span>
      </div>

      <form @submit.prevent="handleSavePassword" class="space-y-4">
        <!-- Password Saat Ini -->
        <div class="space-y-1.5">
          <label class="block text-xs font-black uppercase tracking-wider font-mono text-zinc-400">
            Password Saat Ini <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <input
              v-model="passwordForm.currentPassword"
              :type="showCurrentPassword ? 'text' : 'password'"
              required
              placeholder="Masukkan password saat ini untuk verifikasi"
              :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[3px_3px_0px_#000000] placeholder-zinc-400'"
              class="w-full px-4 pr-10 py-2.5 rounded-xl text-xs sm:text-sm font-bold outline-none transition-all border-2"
            />
            <button
              type="button"
              @click="showCurrentPassword = !showCurrentPassword"
              class="absolute right-3 top-2.5 text-zinc-400 hover:text-zinc-200 cursor-pointer text-xs font-bold"
            >
              {{ showCurrentPassword ? '🙈' : '👁️' }}
            </button>
          </div>
          <p class="text-[11px] text-zinc-400 font-medium">
            Diperlukan untuk memvalidasi bahwa perubahan ini diajukan oleh pemilik akun.
          </p>
        </div>

        <!-- Grid Password Baru + Konfirmasi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
          <!-- Password Baru -->
          <div class="space-y-1.5">
            <label class="block text-xs font-black uppercase tracking-wider font-mono text-zinc-400">
              Password Baru <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
              <input
                v-model="passwordForm.newPassword"
                :type="showNewPassword ? 'text' : 'password'"
                required
                placeholder="Minimal 6 karakter"
                :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[3px_3px_0px_#000000] placeholder-zinc-400'"
                class="w-full px-4 pr-10 py-2.5 rounded-xl text-xs sm:text-sm font-bold outline-none transition-all border-2"
              />
              <button
                type="button"
                @click="showNewPassword = !showNewPassword"
                class="absolute right-3 top-2.5 text-zinc-400 hover:text-zinc-200 cursor-pointer text-xs font-bold"
              >
                {{ showNewPassword ? '🙈' : '👁️' }}
              </button>
            </div>
          </div>

          <!-- Konfirmasi Password Baru -->
          <div class="space-y-1.5">
            <label class="block text-xs font-black uppercase tracking-wider font-mono text-zinc-400">
              Ulangi Password Baru <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
              <input
                v-model="passwordForm.newPasswordConfirmation"
                :type="showNewPassword ? 'text' : 'password'"
                required
                placeholder="Ulangi password baru"
                :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[3px_3px_0px_#000000] placeholder-zinc-400'"
                class="w-full px-4 pr-10 py-2.5 rounded-xl text-xs sm:text-sm font-bold outline-none transition-all border-2"
              />
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-2 border-t" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
          <button
            type="submit"
            :disabled="isPasswordSaving"
            :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff] hover:bg-zinc-200' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
            class="px-6 py-3 rounded-xl font-black text-xs sm:text-sm transition-all border-2 flex items-center gap-2 cursor-pointer hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="isPasswordSaving" class="animate-spin text-sm">⏳</span>
            <span>{{ isPasswordSaving ? 'Memperbarui Password...' : 'Perbarui Password' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'

definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Profil Akun - TokoBukuDigital'
})

const api = useApi()
const toast = useToast()
const authStore = useAuthStore()
const { isDark } = useTheme()

// Profile Edit Form
const profileForm = reactive({
  name: '',
  username: '',
  email: ''
})

const avatarFile = ref<File | null>(null)
const avatarPreview = ref<string | null>(null)
const isAvatarRemoved = ref(false)
const isProfileSaving = ref(false)
const profileError = ref('')
const profileSuccess = ref('')
const imageLoadError = ref(false)
const previewLoadError = ref(false)

// Password Edit Form
const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  newPasswordConfirmation: ''
})

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const isPasswordSaving = ref(false)
const passwordError = ref('')
const passwordSuccess = ref('')

// Avatar URL
const userAvatarUrl = computed(() => {
  if (isAvatarRemoved.value) return null
  if (!authStore.user?.foto) return null
  const photo = authStore.user.foto
  if (photo.startsWith('http://localhost/')) {
    return photo.replace('http://localhost/', `${api.apiBase}/`)
  }
  if (photo.startsWith('http') || photo.startsWith('data:')) {
    return photo
  }
  return `${api.apiBase}/storage/${photo.replace(/^\/+/, '')}`
})

// Populate current data
const syncUserData = () => {
  if (authStore.user) {
    profileForm.name = authStore.user.name || ''
    profileForm.username = authStore.user.username || ''
    profileForm.email = authStore.user.email || ''
  }
}

onMounted(async () => {
  if (!authStore.user) {
    await authStore.fetchUser()
  }
  syncUserData()
})

// Handle Avatar file picker
const handleAvatarFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  if (!file.type.startsWith('image/')) {
    profileError.value = 'File yang dipilih harus berupa gambar (JPG, PNG, GIF, WebP).'
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    profileError.value = 'Ukuran gambar maksimal adalah 2 MB.'
    return
  }

  profileError.value = ''
  isAvatarRemoved.value = false
  imageLoadError.value = false
  previewLoadError.value = false
  avatarFile.value = file

  const reader = new FileReader()
  reader.onload = (e) => {
    avatarPreview.value = e.target?.result as string
  }
  reader.readAsDataURL(file)
}

// Handle remove avatar
const handleRemoveAvatar = () => {
  avatarFile.value = null
  avatarPreview.value = null
  isAvatarRemoved.value = true
}

// 1. Save Profile info & avatar
const handleSaveProfile = async () => {
  profileError.value = ''
  profileSuccess.value = ''

  if (!profileForm.name.trim()) {
    profileError.value = 'Nama lengkap wajib diisi.'
    return
  }

  if (!profileForm.email.trim()) {
    profileError.value = 'Alamat email wajib diisi.'
    return
  }

  isProfileSaving.value = true

  try {
    const formData = new FormData()
    formData.append('name', profileForm.name.trim())
    formData.append('email', profileForm.email.trim().toLowerCase())
    if (profileForm.username.trim()) {
      formData.append('username', profileForm.username.trim())
    }

    if (isAvatarRemoved.value) {
      formData.append('remove_foto', '1')
    } else if (avatarFile.value) {
      formData.append('foto', avatarFile.value)
    }

    const res: any = await api.post('/api/profile', formData)

    if (res && (res.user || res.data)) {
      const updatedUser = res.user || res.data
      authStore.user = updatedUser
      avatarFile.value = null
      avatarPreview.value = null
      isAvatarRemoved.value = false
      imageLoadError.value = false
      previewLoadError.value = false
      syncUserData()
      profileSuccess.value = res.message || 'Profil berhasil diperbarui!'
      toast.success(res.message || 'Profil berhasil diperbarui!')
    }
  } catch (err: any) {
    console.error('Save profile error:', err)
    const serverMsg = err.data?.message || err.message || 'Gagal memperbarui profil.'
    profileError.value = serverMsg
    toast.error(serverMsg)
  } finally {
    isProfileSaving.value = false
  }
}

// 2. Save Password
const handleSavePassword = async () => {
  passwordError.value = ''
  passwordSuccess.value = ''

  if (!passwordForm.currentPassword) {
    passwordError.value = 'Password saat ini wajib diisi untuk verifikasi keamanan.'
    return
  }

  if (passwordForm.newPassword.length < 6) {
    passwordError.value = 'Password baru harus minimal 6 karakter.'
    return
  }

  if (passwordForm.newPassword !== passwordForm.newPasswordConfirmation) {
    passwordError.value = 'Konfirmasi password baru tidak cocok.'
    return
  }

  isPasswordSaving.value = true

  try {
    const res: any = await api.post('/api/profile/password', {
      current_password: passwordForm.currentPassword,
      password: passwordForm.newPassword,
      password_confirmation: passwordForm.newPasswordConfirmation
    })

    passwordSuccess.value = res.message || 'Password berhasil diperbarui!'
    toast.success(res.message || 'Password berhasil diperbarui!')
    passwordForm.currentPassword = ''
    passwordForm.newPassword = ''
    passwordForm.newPasswordConfirmation = ''
  } catch (err: any) {
    console.error('Save password error:', err)
    const serverMsg = err.data?.message || err.message || 'Gagal memperbarui password.'
    passwordError.value = serverMsg
    toast.error(serverMsg)
  } finally {
    isPasswordSaving.value = false
  }
}
</script>
