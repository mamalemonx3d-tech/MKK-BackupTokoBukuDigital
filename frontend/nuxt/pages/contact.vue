<template>
  <div class="max-w-4xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
      <span class="px-3.5 py-1.5 rounded-full bg-[#C8F53F] text-black text-xs font-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]">KONTAK</span>
      <h1 class="text-3xl font-black text-slate-900 mt-4 mb-4">Hubungi Kami</h1>
      <p class="text-slate-700 font-bold text-sm">Ada pertanyaan atau kendala? Kami siap membantu Anda kapan saja.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <div class="bg-[#FFF8EC] p-8 rounded-3xl border-3 border-black shadow-[6px_6px_0px_#1A1A1A] space-y-6">
        <h3 class="font-black text-lg text-slate-900 bg-[#FFE566] px-2 py-0.5 rounded-lg border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A] inline-block">Informasi Kontak</h3>

        <div class="flex items-start gap-4">
          <div class="w-10 h-10 rounded-xl bg-[#C8F53F] border-2 border-black text-black flex items-center justify-center text-xl shrink-0 shadow-[2px_2px_0px_#1A1A1A]">📍</div>
          <div>
            <h5 class="font-black text-slate-900 text-sm">Alamat</h5>
            <p class="text-xs font-bold text-slate-700 mt-1">Jl. Pendidikan No. 123, Kota Sekolahan, Indonesia</p>
          </div>
        </div>

        <div class="flex items-start gap-4">
          <div class="w-10 h-10 rounded-xl bg-[#D4B8FF] border-2 border-black text-black flex items-center justify-center text-xl shrink-0 shadow-[2px_2px_0px_#1A1A1A]">📞</div>
          <div>
            <h5 class="font-black text-slate-900 text-sm">Telepon / WhatsApp</h5>
            <p class="text-xs font-bold text-slate-700 mt-1">+62 812-3456-7890</p>
          </div>
        </div>

        <div class="flex items-start gap-4">
          <div class="w-10 h-10 rounded-xl bg-[#FFD4A3] border-2 border-black text-black flex items-center justify-center text-xl shrink-0 shadow-[2px_2px_0px_#1A1A1A]">✉️</div>
          <div>
            <h5 class="font-black text-slate-900 text-sm">Email</h5>
            <p class="text-xs font-bold text-slate-700 mt-1">support@bookstore.com</p>
          </div>
        </div>

        <div class="pt-4 border-t-2 border-black">
          <NuxtLink to="/user/chat" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#C8F53F] text-black border-2 border-black text-xs font-black shadow-[3px_3px_0px_#1A1A1A] hover:translate-x-[-1px] hover:translate-y-[-1px] active:translate-x-[1px] active:translate-y-[1px] transition-all">
            💬 Gunakan Live Chat Langsung
          </NuxtLink>
        </div>
      </div>

      <div class="bg-[#FFF8EC] p-8 rounded-3xl border-3 border-black shadow-[6px_6px_0px_#1A1A1A]">
        <h3 class="font-black text-lg text-slate-900 mb-4 bg-[#FFD4A3] px-2 py-0.5 rounded-lg border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A] inline-block">Kirim Pesan</h3>
        <form @submit.prevent="sendMessage" class="space-y-4">
          <div>
            <label class="block text-xs font-black text-slate-900 mb-1">Nama Lengkap</label>
            <input v-model="form.name" type="text" required class="w-full px-3.5 py-2 text-sm rounded-xl border-2 border-black bg-white shadow-[2.5px_2.5px_0px_#1A1A1A] outline-none font-bold" placeholder="Masukkan nama" />
          </div>
          <div>
            <label class="block text-xs font-black text-slate-900 mb-1">Email</label>
            <input v-model="form.email" type="email" required class="w-full px-3.5 py-2 text-sm rounded-xl border-2 border-black bg-white shadow-[2.5px_2.5px_0px_#1A1A1A] outline-none font-bold" placeholder="alamat@email.com" />
          </div>
          <div>
            <label class="block text-xs font-black text-slate-900 mb-1">Pesan</label>
            <textarea v-model="form.message" rows="4" required class="w-full px-3.5 py-2 text-sm rounded-xl border-2 border-black bg-white shadow-[2.5px_2.5px_0px_#1A1A1A] outline-none font-bold" placeholder="Tuliskan pesan Anda..."></textarea>
          </div>
          <button type="submit" class="w-full py-3 rounded-xl bg-[#C8F53F] hover:bg-[#b8e82f] text-black border-2 border-black font-black text-sm shadow-[3.5px_3.5px_0px_#1A1A1A] active:translate-x-[1px] active:translate-y-[1px] transition-all">
            Kirim Pesan Sekarang &rarr;
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const form = reactive({
  name: '',
  email: '',
  message: ''
})

const sendMessage = () => {
  const toast = useToast()
  toast.success('Terima kasih! Pesan Anda telah terkirim.')
  form.name = ''
  form.email = ''
  form.message = ''
}
</script>
