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
          📧
        </div>
        <h2 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-2xl font-black">Lupa Password (Kode Email)</h2>
        <p :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-bold mt-1">
          <span v-if="step === 1">Langkah 1: Masukkan email terdaftar untuk menerima kode verifikasi 6 digit</span>
          <span v-else-if="step === 2">Langkah 2: Masukkan 6 digit kode yang dikirim ke {{ verifiedEmail }}</span>
          <span v-else>Langkah 3: Buat password baru Anda</span>
        </p>
      </div>

      <div v-if="message" class="mb-4 p-3.5 rounded-xl bg-[#C8F53F] border-2 border-black text-black text-xs font-black shadow-[2px_2px_0px_#1A1A1A]">
        {{ message }}
      </div>

      <div v-if="errorMessage" class="mb-4 p-3.5 rounded-xl bg-[#FFB7B2] border-2 border-black text-slate-900 text-xs font-black shadow-[2px_2px_0px_#1A1A1A]">
        {{ errorMessage }}
      </div>

      <!-- Step 1: Input Email -->
      <form v-if="step === 1" @submit.prevent="sendCode" class="space-y-4">
        <div>
          <label :class="isDark ? 'text-slate-300' : 'text-slate-900'" class="block text-xs font-black mb-1">Email Terdaftar</label>
          <input 
            v-model="emailInput" 
            type="email" 
            required 
            :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A]'"
            class="w-full px-4 py-2.5 text-sm rounded-xl outline-none font-bold transition-all" 
            placeholder="alamat@email.com" 
          />
        </div>

        <button 
          type="submit" 
          :disabled="loading" 
          :class="isDark ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-[#C8F53F] hover:bg-[#b8e82f] text-black border-2 border-black shadow-[4px_4px_0px_#1A1A1A] active:translate-x-[2px] active:translate-y-[2px]'"
          class="w-full py-3 rounded-xl font-black text-sm transition-all disabled:opacity-50 mt-2"
        >
          <span v-if="loading">Mengirim Email...</span>
          <span v-else>📩 Kirim Kode 6 Digit Ke Email</span>
        </button>
      </form>

      <!-- Step 2: Input 6-Digit OTP Code -->
      <form v-else-if="step === 2" @submit.prevent="verifyCode" class="space-y-4">
        <div>
          <label :class="isDark ? 'text-slate-300' : 'text-slate-900'" class="block text-xs font-black mb-1">Kode Verifikasi 6 Digit (Cek Email Kamu)</label>
          <input 
            v-model="otpCode" 
            type="text" 
            maxlength="6" 
            required 
            :class="isDark ? 'bg-slate-950 border-indigo-500/50 text-indigo-300' : 'bg-[#FFE566] border-2 border-black text-black shadow-[3px_3px_0px_#1A1A1A]'"
            class="w-full px-4 py-3 text-center text-2xl font-mono font-black tracking-widest rounded-xl outline-none" 
            placeholder="123456" 
          />
        </div>

        <button 
          type="submit" 
          :disabled="loading || otpCode.length !== 6" 
          :class="isDark ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-[#C8F53F] hover:bg-[#b8e82f] text-black border-2 border-black shadow-[4px_4px_0px_#1A1A1A] active:translate-x-[2px] active:translate-y-[2px]'"
          class="w-full py-3 rounded-xl font-black text-sm transition-all disabled:opacity-50"
        >
          <span v-if="loading">Memverifikasi Kode...</span>
          <span v-else>Verifikasi Kode &rarr;</span>
        </button>

        <div class="text-center pt-2">
          <button type="button" @click="step = 1" :class="isDark ? 'text-indigo-400' : 'text-slate-900 underline font-black'" class="text-xs">
            Kirim Ulang Kode Ke Email
          </button>
        </div>
      </form>

      <!-- Step 3: Input New Password -->
      <form v-else @submit.prevent="handleReset" class="space-y-4">
        <div>
          <label :class="isDark ? 'text-slate-300' : 'text-slate-900'" class="block text-xs font-black mb-1">Password Baru</label>
          <input 
            v-model="newPassword" 
            type="password" 
            required 
            :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A]'"
            class="w-full px-4 py-2.5 text-sm rounded-xl outline-none font-bold transition-all" 
            placeholder="Minimal 6 karakter" 
          />
        </div>

        <div>
          <label :class="isDark ? 'text-slate-300' : 'text-slate-900'" class="block text-xs font-black mb-1">Konfirmasi Password Baru</label>
          <input 
            v-model="confirmPassword" 
            type="password" 
            required 
            :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A]'"
            class="w-full px-4 py-2.5 text-sm rounded-xl outline-none font-bold transition-all" 
            placeholder="Ulangi password baru" 
          />
        </div>

        <button 
          type="submit" 
          :disabled="loading" 
          :class="isDark ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-[#C8F53F] hover:bg-[#b8e82f] text-black border-2 border-black shadow-[4px_4px_0px_#1A1A1A] active:translate-x-[2px] active:translate-y-[2px]'"
          class="w-full py-3 rounded-xl font-black text-sm transition-all disabled:opacity-50"
        >
          <span v-if="loading">Memperbarui Password...</span>
          <span v-else>Simpan Password Baru</span>
        </button>
      </form>

      <div :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="mt-6 text-center text-xs font-bold">
        Kembali ke
        <NuxtLink to="/login" :class="isDark ? 'text-indigo-400' : 'text-black bg-[#FFE566] px-2 py-0.5 rounded border border-black shadow-[1.5px_1.5px_0px_#1A1A1A] ml-1'" class="font-black">Halaman Login</NuxtLink>
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

// Step 1: Send 6-Digit Code to Email
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

// Step 2: Verify 6-Digit Code
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

// Step 3: Reset Password
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
