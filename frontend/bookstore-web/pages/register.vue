<template>
  <div class="min-h-[85vh] flex items-center justify-center px-4 py-12">
    <div 
      :class="isDark ? 'bg-slate-900 border-slate-800 shadow-2xl text-slate-100' : 'bg-[#FFF8EC] border-3 border-[#1A1A1A] shadow-[8px_8px_0px_#1A1A1A] text-slate-900'"
      class="max-w-lg w-full p-8 rounded-3xl transition-colors duration-300"
    >
      <div class="text-center mb-8">
        <div 
          :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[3px_3px_0px_#1A1A1A]'"
          class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-3"
        >
          📝
        </div>
        <h2 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-2xl font-black">Buat Akun Baru</h2>
        <p :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-bold mt-1">Isi formulir pendaftaran untuk bergabung di BookStore</p>
      </div>

      <div v-if="errorMessage" class="mb-4 p-3.5 rounded-xl bg-[#FFB7B2] border-2 border-black text-slate-900 text-xs font-black shadow-[2px_2px_0px_#1A1A1A]">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label :class="isDark ? 'text-slate-300' : 'text-slate-900'" class="block text-xs font-black mb-1">Nama Lengkap</label>
          <input 
            v-model="form.name" 
            type="text" 
            required 
            :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A]'"
            class="w-full px-4 py-2.5 text-sm rounded-xl outline-none font-bold transition-all" 
            placeholder="Contoh: Budi Santoso" 
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label :class="isDark ? 'text-slate-300' : 'text-slate-900'" class="block text-xs font-black mb-1">Username (Unik)</label>
            <input 
              v-model="form.username" 
              type="text" 
              required 
              :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A]'"
              class="w-full px-4 py-2.5 text-sm rounded-xl outline-none font-bold transition-all" 
              placeholder="budi123" 
            />
          </div>
          <div>
            <label :class="isDark ? 'text-slate-300' : 'text-slate-900'" class="block text-xs font-black mb-1">No. Telepon</label>
            <input 
              v-model="form.no_telp" 
              type="text" 
              :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A]'"
              class="w-full px-4 py-2.5 text-sm rounded-xl outline-none font-bold transition-all" 
              placeholder="08123456789" 
            />
          </div>
        </div>

        <div>
          <label :class="isDark ? 'text-slate-300' : 'text-slate-900'" class="block text-xs font-black mb-1">Email (Unik)</label>
          <input 
            v-model="form.email" 
            type="email" 
            required 
            :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A]'"
            class="w-full px-4 py-2.5 text-sm rounded-xl outline-none font-bold transition-all" 
            placeholder="budi@example.com" 
          />
        </div>

        <div>
          <label :class="isDark ? 'text-slate-300' : 'text-slate-900'" class="block text-xs font-black mb-1">Password</label>
          <input 
            v-model="form.password" 
            type="password" 
            required 
            :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A]'"
            class="w-full px-4 py-2.5 text-sm rounded-xl outline-none font-bold transition-all" 
            placeholder="Minimal 6 karakter" 
          />
        </div>

        <div>
          <label :class="isDark ? 'text-slate-300' : 'text-slate-900'" class="block text-xs font-black mb-1">Foto Profil (Opsional)</label>
          <input 
            @change="handleFileChange" 
            type="file" 
            accept="image/*" 
            :class="isDark ? 'text-slate-400 file:bg-slate-800 file:text-indigo-300' : 'text-slate-700 file:bg-[#FFE566] file:text-black file:border-2 file:border-black file:shadow-[1.5px_1.5px_0px_#1A1A1A] file:font-black'"
            class="w-full text-xs file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 hover:file:opacity-80 transition-opacity" 
          />
        </div>

        <button 
          type="submit" 
          :disabled="loading" 
          :class="isDark ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-[#C8F53F] hover:bg-[#b8e82f] text-black border-2 border-black shadow-[4px_4px_0px_#1A1A1A] active:translate-x-[2px] active:translate-y-[2px]'"
          class="w-full py-3 rounded-xl font-black text-sm transition-all disabled:opacity-50 mt-2"
        >
          <span v-if="loading">Mendaftarkan...</span>
          <span v-else>Daftar Akun Sekarang &rarr;</span>
        </button>
      </form>

      <div :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="mt-6 text-center text-xs font-bold">
        Sudah memiliki akun?
        <NuxtLink to="/login" :class="isDark ? 'text-indigo-400' : 'text-black bg-[#FFE566] px-2 py-0.5 rounded border border-black shadow-[1.5px_1.5px_0px_#1A1A1A] ml-1'" class="font-black">Masuk di Sini</NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'auth'
})

const authStore = useAuthStore()
const { isDark } = useTheme()

const form = reactive({
  name: '',
  username: '',
  email: '',
  no_telp: '',
  password: ''
})

const selectedFoto = ref<File | null>(null)
const loading = ref(false)
const errorMessage = ref('')

const handleFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    selectedFoto.value = target.files[0]
  }
}

const handleRegister = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('username', form.username)
    formData.append('email', form.email)
    formData.append('no_telp', form.no_telp)
    formData.append('password', form.password)
    if (selectedFoto.value) {
      formData.append('foto', selectedFoto.value)
    }

    await authStore.register(formData)
    navigateTo('/user/katalog')
  } catch (err: any) {
    const errors = err.data?.errors
    if (errors) {
      const firstKey = Object.keys(errors)[0]
      errorMessage.value = errors[firstKey][0]
    } else {
      errorMessage.value = err.data?.message || 'Registrasi gagal. Periksa kembali data Anda.'
    }
  } finally {
    loading.value = false
  }
}
</script>
