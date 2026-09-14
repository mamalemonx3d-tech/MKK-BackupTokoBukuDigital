<template>
  <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div 
      :class="isDark ? 'bg-slate-900 border-slate-800 shadow-2xl text-slate-100' : 'bg-[#FFF8EC] border-3 border-[#1A1A1A] shadow-[8px_8px_0px_#1A1A1A] text-slate-900'"
      class="max-w-md w-full p-8 rounded-3xl transition-colors duration-300"
    >
      <div class="text-center mb-8">
        <div 
          :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[3px_3px_0px_#1A1A1A]'"
          class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-3"
        >
          🔑
        </div>
        <h2 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-2xl font-black">Selamat Datang Kembali</h2>
        <p :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-bold mt-1">Masuk ke akun BookStore Anda</p>
      </div>

      <div v-if="errorMessage" class="mb-4 p-3.5 rounded-xl bg-[#FFB7B2] border-2 border-black text-slate-900 text-xs font-black shadow-[2px_2px_0px_#1A1A1A]">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label :class="isDark ? 'text-slate-300' : 'text-slate-900'" class="block text-xs font-black mb-1">Username atau Email</label>
          <input 
            v-model="form.login" 
            type="text" 
            required 
            :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A] focus:shadow-[4px_4px_0px_#1A1A1A]'"
            class="w-full px-4 py-2.5 text-sm rounded-xl outline-none font-bold transition-all" 
            placeholder="Masukkan username/email" 
          />
        </div>

        <div>
          <div class="flex justify-between items-center mb-1">
            <label :class="isDark ? 'text-slate-300' : 'text-slate-900'" class="text-xs font-black">Password</label>
            <NuxtLink to="/forget-password" :class="isDark ? 'text-indigo-400' : 'text-slate-900 underline font-black hover:text-indigo-600'" class="text-xs font-bold">Lupa Password?</NuxtLink>
          </div>
          <input 
            v-model="form.password" 
            type="password" 
            required 
            :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A] focus:shadow-[4px_4px_0px_#1A1A1A]'"
            class="w-full px-4 py-2.5 text-sm rounded-xl outline-none font-bold transition-all" 
            placeholder="••••••••" 
          />
        </div>

        <button 
          type="submit" 
          :disabled="loading" 
          :class="isDark ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-[#C8F53F] hover:bg-[#b8e82f] text-black border-2 border-black shadow-[4px_4px_0px_#1A1A1A] active:translate-x-[2px] active:translate-y-[2px] active:shadow-[2px_2px_0px_#1A1A1A]'"
          class="w-full py-3 rounded-xl font-black text-sm transition-all disabled:opacity-50 mt-2"
        >
          <span v-if="loading">Memproses...</span>
          <span v-else>Masuk Sekarang &rarr;</span>
        </button>
      </form>

      <div :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="mt-6 text-center text-xs font-bold">
        Belum punya akun?
        <NuxtLink to="/register" :class="isDark ? 'text-indigo-400' : 'text-black bg-[#FFE566] px-2 py-0.5 rounded border border-black shadow-[1.5px_1.5px_0px_#1A1A1A] ml-1'" class="font-black">Daftar Akun Baru</NuxtLink>
      </div>

      <!-- Quick Demo Account Hint -->
      <div :class="isDark ? 'border-slate-800 text-slate-400' : 'border-t-2 border-black text-slate-800'" class="mt-8 pt-6 text-xs font-bold">
        <p :class="isDark ? 'text-slate-300' : 'text-slate-900'" class="font-black mb-1">Akun Demo Testing:</p>
        <p>• Admin: <code :class="isDark ? 'bg-slate-800 text-indigo-300' : 'bg-[#FFE566] text-black border border-black shadow-[1px_1px_0px_#1A1A1A]'" class="px-1.5 py-0.5 rounded font-mono font-black">admin</code> / <code :class="isDark ? 'bg-slate-800 text-indigo-300' : 'bg-[#FFE566] text-black border border-black shadow-[1px_1px_0px_#1A1A1A]'" class="px-1.5 py-0.5 rounded font-mono font-black">password</code></p>
        <p class="mt-1">• User: <code :class="isDark ? 'bg-slate-800 text-indigo-300' : 'bg-[#D4B8FF] text-black border border-black shadow-[1px_1px_0px_#1A1A1A]'" class="px-1.5 py-0.5 rounded font-mono font-black">userdemo</code> / <code :class="isDark ? 'bg-slate-800 text-indigo-300' : 'bg-[#D4B8FF] text-black border border-black shadow-[1px_1px_0px_#1A1A1A]'" class="px-1.5 py-0.5 rounded font-mono font-black">password</code></p>
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
  login: '',
  password: ''
})

const loading = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    await authStore.login(form)
    if (authStore.isAdmin) {
      navigateTo('/admin/kategori')
    } else {
      navigateTo('/user/katalog')
    }
  } catch (err: any) {
    errorMessage.value = err.data?.message || err.data?.errors?.login?.[0] || 'Login gagal. Periksa kembali data Anda.'
  } finally {
    loading.value = false
  }
}
</script>
