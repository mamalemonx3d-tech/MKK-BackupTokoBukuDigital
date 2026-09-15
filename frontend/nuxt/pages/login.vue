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
              <pattern id="grid-pattern-login" width="32" height="32" patternUnits="userSpaceOnUse">
                <path d="M0 32V0h32" fill="none" stroke="currentColor" stroke-width="1.2" stroke-dasharray="2 2" />
              </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid-pattern-login)" />
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
            TokoBukuDigital
          </div>
          <h3 class="text-2xl font-black leading-tight tracking-tight transition-colors" :class="isDark ? 'text-black' : 'text-white'">
            Ruang Baca & Literatur Kurasi Terbaik.
          </h3>
        </div>

        <div class="relative z-10 space-y-4 pt-8">
          <blockquote 
            class="text-xs italic border-l-2 pl-3 leading-relaxed transition-colors"
            :class="isDark ? 'border-black/40 text-zinc-700' : 'border-white/40 text-zinc-300'"
          >
            "Membaca adalah jembatan imajinasi dan pengetahuan yang menghubungkan masa lalu dengan masa depan."
          </blockquote>
          
          <div class="flex items-center gap-3 pt-2 text-[11px] font-mono transition-colors" :class="isDark ? 'text-zinc-700' : 'text-zinc-400'">
            <div class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full" :class="isDark ? 'bg-black' : 'bg-white'"></span>
              <span class="font-bold">100% Original</span>
            </div>
            <span>•</span>
            <div class="font-bold">Akses Instan</div>
          </div>
        </div>
      </div>

      <!-- Right Side: Login Form -->
      <div class="md:col-span-7 p-6 sm:p-10 flex flex-col justify-center">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
          <div class="flex items-center gap-3 mb-3">
            <div 
              class="w-10 h-10 rounded-xl border-2 flex items-center justify-center transition-transform hover:scale-105"
              :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
            >
              <!-- Sleek Modern Key SVG Icon -->
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
              </svg>
            </div>
            <div>
              <h2 class="text-xl sm:text-2xl font-black tracking-tight leading-tight">Selamat Datang</h2>
              <p class="text-xs font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
                Masuk menggunakan akun email terdaftar Anda
              </p>
            </div>
          </div>
        </div>

        <!-- Error Alert -->
        <div 
          v-if="errorMessage" 
          class="mb-5 p-3.5 rounded-xl border-2 text-xs font-bold flex items-start gap-2.5 transition-all"
          :class="isDark ? 'bg-rose-950/40 border-rose-700 text-rose-300' : 'bg-rose-50 border-rose-600 text-rose-800 shadow-[2px_2px_0px_#e11d48]'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <span class="flex-1 leading-relaxed">{{ errorMessage }}</span>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label class="block text-xs font-black font-mono uppercase mb-1.5" :class="isDark ? 'text-zinc-300' : 'text-zinc-800'">
              Alamat Email / Username
            </label>
            <div class="relative">
              <input 
                v-model="form.email" 
                type="text" 
                required 
                placeholder="nama@email.com atau username" 
                class="w-full px-4 py-2.5 sm:py-3 pl-10 text-xs sm:text-sm rounded-xl outline-none font-medium border-2 transition-all"
                :class="isDark ? 'bg-black border-zinc-700 text-white focus:border-white placeholder-zinc-600' : 'bg-zinc-50 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[4px_4px_0px_#000000] placeholder-zinc-400'"
              />
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3.5 top-3.5 sm:top-4 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
              </svg>
            </div>
          </div>

          <div>
            <div class="flex justify-between items-center mb-1.5">
              <label class="text-xs font-black font-mono uppercase" :class="isDark ? 'text-zinc-300' : 'text-zinc-800'">
                Password
              </label>
              <NuxtLink 
                to="/forget-password" 
                class="text-[11px] font-bold underline hover:opacity-80 transition-opacity"
                :class="isDark ? 'text-zinc-400' : 'text-zinc-600'"
              >
                Lupa Password?
              </NuxtLink>
            </div>
            <div class="relative">
              <input 
                v-model="form.password" 
                :type="showPassword ? 'text' : 'password'" 
                required 
                placeholder="••••••••" 
                class="w-full px-4 py-2.5 sm:py-3 pl-10 pr-10 text-xs sm:text-sm rounded-xl outline-none font-medium border-2 transition-all"
                :class="isDark ? 'bg-black border-zinc-700 text-white focus:border-white placeholder-zinc-600' : 'bg-zinc-50 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[4px_4px_0px_#000000] placeholder-zinc-400'"
              />
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3.5 top-3.5 sm:top-4 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <button 
                type="button" 
                @click="showPassword = !showPassword" 
                class="absolute right-3.5 top-3 sm:top-3.5 text-xs font-mono font-bold opacity-60 hover:opacity-100"
              >
                {{ showPassword ? 'Sembunyikan' : 'Lihat' }}
              </button>
            </div>
          </div>

          <button 
            type="submit" 
            :disabled="loading" 
            class="w-full py-3 sm:py-3.5 rounded-xl font-black text-xs sm:text-sm border-2 transition-all disabled:opacity-50 mt-3 cursor-pointer hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2"
            :class="isDark ? 'bg-white text-black border-white shadow-[3px_3px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[4px_4px_0px_#000000]'"
          >
            <span v-if="loading">Memproses...</span>
            <span v-else class="flex items-center gap-1.5">
              <span>Masuk ke Akun</span>
              <span>&rarr;</span>
            </span>
          </button>
        </form>

        <!-- Register Link -->
        <div class="mt-6 text-center text-xs font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
          Belum memiliki akun?
          <NuxtLink 
            to="/register" 
            class="font-black underline ml-1 cursor-pointer hover:opacity-80"
            :class="isDark ? 'text-white' : 'text-black'"
          >
            Daftar Akun Baru
          </NuxtLink>
        </div>

        <!-- Demo Account Shortcut -->
        <div 
          class="mt-6 sm:mt-8 pt-4 sm:pt-5 border-t text-[11px] font-medium"
          :class="isDark ? 'border-zinc-800 text-zinc-400' : 'border-zinc-200 text-zinc-600'"
        >
          <div class="flex items-center justify-between gap-2 mb-2">
            <span class="font-mono font-black uppercase text-[10px]" :class="isDark ? 'text-zinc-300' : 'text-zinc-800'">
              Akun Cepat Testing:
            </span>
            <span class="text-[10px] text-zinc-500 font-mono">Klik untuk autofill</span>
          </div>
          <div class="grid grid-cols-2 gap-2 font-mono">
            <button 
              type="button" 
              @click="fillDemo('admin@bookstore.com', 'password')"
              class="px-2.5 py-1.5 rounded-lg border text-left transition-all hover:scale-102 cursor-pointer"
              :class="isDark ? 'bg-zinc-950 border-zinc-700 text-zinc-300 hover:border-white' : 'bg-zinc-100 border-zinc-300 text-black hover:border-black'"
            >
              <div class="text-[10px] font-black">👑 Akun Admin</div>
              <div class="text-[9px] opacity-70 truncate">admin@bookstore.com</div>
            </button>
            <button 
              type="button" 
              @click="fillDemo('user@bookstore.com', 'password')"
              class="px-2.5 py-1.5 rounded-lg border text-left transition-all hover:scale-102 cursor-pointer"
              :class="isDark ? 'bg-zinc-950 border-zinc-700 text-zinc-300 hover:border-white' : 'bg-zinc-100 border-zinc-300 text-black hover:border-black'"
            >
              <div class="text-[10px] font-black">👤 Akun User</div>
              <div class="text-[9px] opacity-70 truncate">user@bookstore.com</div>
            </button>
          </div>
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
  title: 'Masuk - TokoBukuDigital'
})

const authStore = useAuthStore()
const { isDark } = useTheme()

const form = reactive({
  email: '',
  password: ''
})

const showPassword = ref(false)
const loading = ref(false)
const errorMessage = ref('')

const fillDemo = (email: string, pass: string) => {
  form.email = email
  form.password = pass
}

const handleLogin = async () => {
  if (!form.email.trim() || !form.password) {
    errorMessage.value = 'Silakan isi email/username dan password.'
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    await authStore.login({
      login: form.email.trim(),
      password: form.password
    })
    
    const toast = useToast()
    toast.success('Login berhasil! Selamat datang.')

    if (authStore.isAdmin) {
      await navigateTo('/admin/buku')
    } else {
      await navigateTo('/katalog')
    }
  } catch (err: any) {
    console.error('Login error:', err)
    errorMessage.value = err.data?.message || err.data?.errors?.login?.[0] || err.message || 'Login gagal. Periksa kembali email/username dan password Anda.'
  } finally {
    loading.value = false
  }
}
</script>
