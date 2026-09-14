<template>
  <div class="w-full max-w-md mx-auto">
    <div 
      class="w-full p-5 sm:p-8 rounded-3xl border-2 transition-all duration-300"
      :class="isDark ? 'bg-zinc-900/90 border-zinc-700 shadow-2xl shadow-black/80' : 'bg-white border-black shadow-[6px_6px_0px_#000000]'"
    >
      <!-- Header -->
      <div class="text-center mb-6 sm:mb-8">
        <div 
          class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl border-2 flex items-center justify-center text-2xl sm:text-3xl mx-auto mb-3.5 transition-transform hover:scale-105"
          :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
        >
          📧
        </div>
        <h2 class="text-xl sm:text-2xl font-black tracking-tight">Reset Password</h2>
        <p class="text-xs font-medium mt-1" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
          <span v-if="step === 1">Masukkan email terdaftar untuk menerima kode verifikasi 6 digit</span>
          <span v-else-if="step === 2">Masukkan 6 digit kode yang dikirim ke {{ verifiedEmail }}</span>
          <span v-else>Buat password baru akun Anda</span>
        </p>
      </div>

      <!-- Success Message -->
      <div 
        v-if="message" 
        class="mb-4 p-3 sm:p-3.5 rounded-xl border-2 text-xs font-bold flex items-start gap-2"
        :class="isDark ? 'bg-emerald-950/50 border-emerald-700 text-emerald-300' : 'bg-emerald-50 border-emerald-700 text-emerald-900 shadow-[2px_2px_0px_#047857]'"
      >
        <span>✅</span>
        <span class="flex-1">{{ message }}</span>
      </div>

      <!-- Error Alert -->
      <div 
        v-if="errorMessage" 
        class="mb-4 p-3 sm:p-3.5 rounded-xl border-2 text-xs font-bold flex items-start gap-2"
        :class="isDark ? 'bg-rose-950/50 border-rose-700 text-rose-300' : 'bg-rose-50 border-rose-600 text-rose-800 shadow-[2px_2px_0px_#e11d48]'"
      >
        <span>⚠️</span>
        <span class="flex-1">{{ errorMessage }}</span>
      </div>

      <!-- Step 1: Input Email -->
      <form v-if="step === 1" @submit.prevent="sendCode" class="space-y-4">
        <div>
          <label class="block text-xs font-black font-mono uppercase mb-1.5" :class="isDark ? 'text-zinc-300' : 'text-zinc-800'">
            Email Terdaftar
          </label>
          <input 
            v-model="emailInput" 
            type="email" 
            required 
            placeholder="alamat@email.com" 
            class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm rounded-xl outline-none font-medium border-2 transition-all"
            :class="isDark ? 'bg-black border-zinc-700 text-white focus:border-white placeholder-zinc-600' : 'bg-zinc-50 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[4px_4px_0px_#000000] placeholder-zinc-400'"
          />
        </div>

        <button 
          type="submit" 
          :disabled="loading" 
          class="w-full py-3 sm:py-3.5 rounded-xl font-black text-xs sm:text-sm border-2 transition-all disabled:opacity-50 mt-3 cursor-pointer hover:-translate-y-0.5"
          :class="isDark ? 'bg-white text-black border-white shadow-[3px_3px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[4px_4px_0px_#000000]'"
        >
          <span v-if="loading">Mengirim Email...</span>
          <span v-else>📩 Kirim Kode 6 Digit Ke Email</span>
        </button>
      </form>

      <!-- Step 2: Input 6-Digit OTP Code -->
      <form v-else-if="step === 2" @submit.prevent="verifyCode" class="space-y-4">
        <div>
          <label class="block text-xs font-black font-mono uppercase mb-1.5 text-center" :class="isDark ? 'text-zinc-300' : 'text-zinc-800'">
            Kode Verifikasi 6 Digit
          </label>
          <input 
            v-model="otpCode" 
            type="text" 
            maxlength="6" 
            required 
            placeholder="123456" 
            class="w-full px-4 py-3 text-center text-2xl font-mono font-black tracking-widest rounded-xl outline-none border-2"
            :class="isDark ? 'bg-black border-white text-white' : 'bg-zinc-100 border-black text-black shadow-[3px_3px_0px_#000000]'"
          />
        </div>

        <button 
          type="submit" 
          :disabled="loading || otpCode.length !== 6" 
          class="w-full py-3 sm:py-3.5 rounded-xl font-black text-xs sm:text-sm border-2 transition-all disabled:opacity-50 cursor-pointer hover:-translate-y-0.5"
          :class="isDark ? 'bg-white text-black border-white shadow-[3px_3px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[4px_4px_0px_#000000]'"
        >
          <span v-if="loading">Memverifikasi Kode...</span>
          <span v-else>Verifikasi Kode &rarr;</span>
        </button>

        <div class="text-center pt-2">
          <button type="button" @click="step = 1" class="text-xs font-bold underline cursor-pointer hover:opacity-80" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
            Kirim Ulang Kode Ke Email
          </button>
        </div>
      </form>

      <!-- Step 3: Input New Password -->
      <form v-else @submit.prevent="handleReset" class="space-y-4">
        <div>
          <label class="block text-xs font-black font-mono uppercase mb-1.5" :class="isDark ? 'text-zinc-300' : 'text-zinc-800'">
            Password Baru
          </label>
          <input 
            v-model="newPassword" 
            type="password" 
            required 
            placeholder="Minimal 6 karakter" 
            class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm rounded-xl outline-none font-medium border-2 transition-all"
            :class="isDark ? 'bg-black border-zinc-700 text-white focus:border-white placeholder-zinc-600' : 'bg-zinc-50 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[4px_4px_0px_#000000] placeholder-zinc-400'"
          />
        </div>

        <div>
          <label class="block text-xs font-black font-mono uppercase mb-1.5" :class="isDark ? 'text-zinc-300' : 'text-zinc-800'">
            Konfirmasi Password Baru
          </label>
          <input 
            v-model="confirmPassword" 
            type="password" 
            required 
            placeholder="Ulangi password baru" 
            class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 text-xs sm:text-sm rounded-xl outline-none font-medium border-2 transition-all"
            :class="isDark ? 'bg-black border-zinc-700 text-white focus:border-white placeholder-zinc-600' : 'bg-zinc-50 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[4px_4px_0px_#000000] placeholder-zinc-400'"
          />
        </div>

        <button 
          type="submit" 
          :disabled="loading" 
          class="w-full py-3 sm:py-3.5 rounded-xl font-black text-xs sm:text-sm border-2 transition-all disabled:opacity-50 cursor-pointer hover:-translate-y-0.5"
          :class="isDark ? 'bg-white text-black border-white shadow-[3px_3px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[4px_4px_0px_#000000]'"
        >
          <span v-if="loading">Memperbarui Password...</span>
          <span v-else>Simpan Password Baru</span>
        </button>
      </form>

      <!-- Back to Login -->
      <div class="mt-6 text-center text-xs font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
        Kembali ke
        <NuxtLink to="/login" class="font-black underline ml-1 cursor-pointer hover:opacity-80" :class="isDark ? 'text-white' : 'text-black'">
          Halaman Login
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'auth'
})

const api = useApi()
const { isDark } = useTheme()

const step = ref(1)
const emailInput = ref('')
const verifiedEmail = ref('')
const otpCode = ref('')
const newPassword = ref('')
const confirmPassword = ref('')

const loading = ref(false)
const message = ref('')
const errorMessage = ref('')

const sendCode = async () => {
  loading.value = true
  message.value = ''
  errorMessage.value = ''

  try {
    const res = await api.post<{ message: string; email: string }>('/api/forgot-password/send-code', {
      email: emailInput.value
    })
    verifiedEmail.value = res.email
    message.value = res.message
    step.value = 2
  } catch (err: any) {
    errorMessage.value = err.data?.message || err.data?.errors?.email?.[0] || 'Gagal mengirim kode email.'
  } finally {
    loading.value = false
  }
}

const verifyCode = async () => {
  loading.value = true
  message.value = ''
  errorMessage.value = ''

  try {
    const res = await api.post<{ message: string }>('/api/forgot-password/verify-code', {
      email: verifiedEmail.value,
      code: otpCode.value
    })
    message.value = res.message
    step.value = 3
  } catch (err: any) {
    errorMessage.value = err.data?.message || 'Kode verifikasi tidak valid.'
  } finally {
    loading.value = false
  }
}

const handleReset = async () => {
  if (newPassword.value !== confirmPassword.value) {
    errorMessage.value = 'Konfirmasi password tidak cocok.'
    return
  }

  loading.value = true
  message.value = ''
  errorMessage.value = ''

  try {
    const res = await api.post<{ message: string }>('/api/forgot-password/reset', {
      email: verifiedEmail.value,
      code: otpCode.value,
      password: newPassword.value,
      password_confirmation: confirmPassword.value
    })
    const toast = useToast()
    toast.success(res.message || 'Password berhasil diperbarui!')
    navigateTo('/login')
  } catch (err: any) {
    errorMessage.value = err.data?.message || 'Gagal memperbarui password.'
  } finally {
    loading.value = false
  }
}
</script>
