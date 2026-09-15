<template>
  <div class="w-full max-w-4xl mx-auto">
    <div 
      class="w-full rounded-3xl border-2 overflow-hidden transition-all duration-300 grid grid-cols-1 md:grid-cols-12"
      :class="isDark ? 'bg-zinc-900 border-zinc-700 shadow-2xl shadow-black' : 'bg-white border-black shadow-[8px_8px_0px_#000000]'"
    >
      <!-- Left Side: Literary Aesthetic Showcase (Visible on md+) -->
      <div 
        class="hidden md:flex md:col-span-5 flex-col justify-between p-8 relative overflow-hidden border-r-2 transition-colors duration-300"
        :class="isDark ? 'border-zinc-700 bg-white text-black' : 'border-black bg-black text-white'"
      >
        <!-- Modern Geometric Vector Pattern (Dynamic Reversible Grid) -->
        <div class="absolute inset-0 pointer-events-none" :class="isDark ? 'opacity-20 text-black' : 'opacity-20 text-white'">
          <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
            <defs>
              <pattern id="grid-pattern-reg" width="32" height="32" patternUnits="userSpaceOnUse">
                <path d="M0 32V0h32" fill="none" stroke="currentColor" stroke-width="1.2" stroke-dasharray="2 2" />
              </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid-pattern-reg)" />
          </svg>
        </div>

        <!-- Ambient Watermark & Rings -->
        <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full border opacity-20 pointer-events-none" :class="isDark ? 'border-black' : 'border-white'"></div>
        <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full border opacity-10 pointer-events-none" :class="isDark ? 'border-black' : 'border-white'"></div>
        <div class="absolute right-4 bottom-12 font-mono font-black text-8xl pointer-events-none select-none" :class="isDark ? 'text-black/5' : 'text-white/5'">
          B
        </div>

        <!-- Content Overlay -->
        <div class="relative z-10 space-y-3">
          <div 
            class="inline-flex items-center gap-2 px-3 py-1 rounded-full border text-[10px] font-mono tracking-wider font-bold uppercase transition-colors"
            :class="isDark ? 'border-black/20 bg-black/5 text-black' : 'border-white/20 bg-white/10 text-zinc-300'"
          >
            REGISTRASI ANGGOTA
          </div>
          <h3 class="text-2xl font-black leading-tight tracking-tight transition-colors" :class="isDark ? 'text-black' : 'text-white'">
            Bergabung dengan Ribuan Pembaca Setia.
          </h3>
        </div>

        <div class="relative z-10 space-y-4 pt-8">
          <div class="space-y-2.5 text-xs transition-colors" :class="isDark ? 'text-zinc-800 font-bold' : 'text-zinc-300'">
            <div class="flex items-center gap-2">
              <span class="w-4 h-4 rounded-full flex items-center justify-center font-bold text-[10px]" :class="isDark ? 'bg-black text-white' : 'bg-white text-black'">✓</span>
              <span>Koleksi buku terbitan resmi & original</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-4 h-4 rounded-full flex items-center justify-center font-bold text-[10px]" :class="isDark ? 'bg-black text-white' : 'bg-white text-black'">✓</span>
              <span>Akses pratinjau sampel buku gratis</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-4 h-4 rounded-full flex items-center justify-center font-bold text-[10px]" :class="isDark ? 'bg-black text-white' : 'bg-white text-black'">✓</span>
              <span>Pelacakan pesanan & pengiriman real-time</span>
            </div>
          </div>
          
          <div class="pt-2 text-[11px] font-mono transition-colors" :class="isDark ? 'text-zinc-700' : 'text-zinc-400'">
            TokoBukuDigital • MONO Edition
          </div>
        </div>
      </div>

      <!-- Right Side: Register Form -->
      <div class="md:col-span-7 p-6 sm:p-10 flex flex-col justify-center">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center gap-3 mb-2">
            <div 
              class="w-10 h-10 rounded-xl border-2 flex items-center justify-center transition-transform hover:scale-105"
              :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
            >
              <!-- Sleek Vector Register Icon -->
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
            </div>
            <div>
              <h2 class="text-xl sm:text-2xl font-black tracking-tight leading-tight">Buat Akun Baru</h2>
              <p class="text-xs font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
                Daftar hanya dengan nama dan alamat email Anda
              </p>
            </div>
          </div>
        </div>

        <!-- Error Alert -->
        <div 
          v-if="errorMessage" 
          class="mb-4 p-3.5 rounded-xl border-2 text-xs font-bold flex items-start gap-2.5 transition-all"
          :class="isDark ? 'bg-rose-950/40 border-rose-700 text-rose-300' : 'bg-rose-50 border-rose-600 text-rose-800 shadow-[2px_2px_0px_#e11d48]'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <span class="flex-1 leading-relaxed">{{ errorMessage }}</span>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleRegister" class="space-y-3.5">
          <div>
            <label class="block text-xs font-black font-mono uppercase mb-1" :class="isDark ? 'text-zinc-300' : 'text-zinc-800'">
              Nama Lengkap
            </label>
            <input 
              v-model="form.name" 
              type="text" 
              required 
              placeholder="Contoh: Budi Santoso" 
              class="w-full px-3.5 sm:px-4 py-2.5 text-xs sm:text-sm rounded-xl outline-none font-medium border-2 transition-all"
              :class="isDark ? 'bg-black border-zinc-700 text-white focus:border-white placeholder-zinc-600' : 'bg-zinc-50 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[4px_4px_0px_#000000] placeholder-zinc-400'"
            />
          </div>

          <div>
            <label class="block text-xs font-black font-mono uppercase mb-1" :class="isDark ? 'text-zinc-300' : 'text-zinc-800'">
              Alamat Email (Untuk Login)
            </label>
            <input 
              v-model="form.email" 
              type="email" 
              required 
              placeholder="nama@email.com" 
              class="w-full px-3.5 sm:px-4 py-2.5 text-xs sm:text-sm rounded-xl outline-none font-medium border-2 transition-all"
              :class="isDark ? 'bg-black border-zinc-700 text-white focus:border-white placeholder-zinc-600' : 'bg-zinc-50 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[4px_4px_0px_#000000] placeholder-zinc-400'"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-black font-mono uppercase mb-1" :class="isDark ? 'text-zinc-300' : 'text-zinc-800'">
                Password
              </label>
              <input 
                v-model="form.password" 
                type="password" 
                required 
                placeholder="Minimal 6 karakter" 
                class="w-full px-3.5 sm:px-4 py-2.5 text-xs sm:text-sm rounded-xl outline-none font-medium border-2 transition-all"
                :class="isDark ? 'bg-black border-zinc-700 text-white focus:border-white placeholder-zinc-600' : 'bg-zinc-50 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[4px_4px_0px_#000000] placeholder-zinc-400'"
              />
            </div>

            <div>
              <label class="block text-xs font-black font-mono uppercase mb-1" :class="isDark ? 'text-zinc-300' : 'text-zinc-800'">
                Konfirmasi Password
              </label>
              <input 
                v-model="form.password_confirmation" 
                type="password" 
                required 
                placeholder="Ulangi password" 
                class="w-full px-3.5 sm:px-4 py-2.5 text-xs sm:text-sm rounded-xl outline-none font-medium border-2 transition-all"
                :class="isDark ? 'bg-black border-zinc-700 text-white focus:border-white placeholder-zinc-600' : 'bg-zinc-50 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[4px_4px_0px_#000000] placeholder-zinc-400'"
              />
            </div>
          </div>

          <button 
            type="submit" 
            :disabled="loading" 
            class="w-full py-3 sm:py-3.5 rounded-xl font-black text-xs sm:text-sm border-2 transition-all disabled:opacity-50 mt-4 cursor-pointer hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2"
            :class="isDark ? 'bg-white text-black border-white shadow-[3px_3px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[4px_4px_0px_#000000]'"
          >
            <span v-if="loading">Mendaftarkan...</span>
            <span v-else class="flex items-center gap-1.5">
              <span>Daftar Akun Sekarang</span>
              <span>&rarr;</span>
            </span>
          </button>
        </form>

        <!-- Login Link -->
        <div class="mt-5 text-center text-xs font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
          Sudah memiliki akun?
          <NuxtLink 
            to="/login" 
            class="font-black underline ml-1 cursor-pointer hover:opacity-80"
            :class="isDark ? 'text-white' : 'text-black'"
          >
            Masuk di Sini
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'auth'
})

useHead({
  title: 'Daftar Akun - TokoBukuDigital'
})

const authStore = useAuthStore()
const { isDark } = useTheme()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const loading = ref(false)
const errorMessage = ref('')

const handleRegister = async () => {
  if (form.password !== form.password_confirmation) {
    errorMessage.value = 'Konfirmasi password tidak cocok dengan password yang dimasukkan.'
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const formData = new FormData()
    formData.append('name', form.name)
    formData.append('email', form.email)
    formData.append('password', form.password)

    await authStore.register(formData)
    await navigateTo('/katalog')
  } catch (err: any) {
    const errors = err.data?.errors
    if (errors) {
      const firstKey = Object.keys(errors)[0]
      errorMessage.value = errors[firstKey][0]
    } else {
      errorMessage.value = err.data?.message || 'Registrasi gagal. Periksa kembali data formulir Anda.'
    }
  } finally {
    loading.value = false
  }
}
</script>
