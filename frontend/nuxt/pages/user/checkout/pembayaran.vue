<template>
  <div class="max-w-2xl mx-auto px-4 py-10">
    <div class="flex items-center gap-3 mb-8">
      <NuxtLink to="/user/keranjang" :class="isDark ? 'bg-slate-800 text-white' : 'bg-white border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'" class="w-9 h-9 rounded-xl flex items-center justify-center font-black">
        ←
      </NuxtLink>
      <h1 :class="isDark ? 'text-white' : 'text-slate-900 bg-[#FFE566] px-3 py-1 inline-block rounded-2xl border-2 border-black shadow-[4px_4px_0px_#1A1A1A]'" class="text-2xl font-black">
        Pilih Pembayaran
      </h1>
    </div>

    <div v-if="cartStore.checkoutSelection.length === 0" :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#D4B8FF] border-3 border-black shadow-[6px_6px_0px_#1A1A1A]'" class="text-center py-14 rounded-3xl">
      <p class="font-black text-sm mb-4">Belum ada item yang dipilih.</p>
      <NuxtLink to="/user/keranjang" :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] border-2 border-black shadow-[3px_3px_0px_#1A1A1A]'" class="px-5 py-2.5 rounded-xl text-xs font-black inline-block">
        Kembali ke Keranjang
      </NuxtLink>
    </div>

    <div v-else class="space-y-6">
      <!-- Ringkasan Item -->
      <div :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#FFF8EC] border-2.5 border-black shadow-[4px_4px_0px_#1A1A1A]'" class="p-5 rounded-2xl space-y-3">
        <h3 class="font-black text-sm border-b-2 border-black pb-2" :class="isDark ? 'text-white border-slate-800' : ''">Ringkasan Pesanan</h3>
        <div v-for="item in cartStore.checkoutSelection" :key="item.book_id" class="flex justify-between text-xs font-bold">
          <span>{{ item.nama_buku }} <span class="text-slate-500">x{{ item.qty }}</span></span>
          <span class="font-black">Rp {{ formatNumber(item.harga_jual * item.qty) }}</span>
        </div>
        <div class="flex justify-between text-sm font-black pt-3 border-t-2 border-black" :class="isDark ? 'text-white border-slate-800' : ''">
          <span>Total Bayar</span>
          <span :class="isDark ? 'text-indigo-400' : 'bg-[#C8F53F] px-2 py-0.5 rounded border border-black'">Rp {{ formatNumber(totalPrice) }}</span>
        </div>
      </div>

      <!-- Metode Pembayaran -->
      <div :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#FFF8EC] border-2.5 border-black shadow-[4px_4px_0px_#1A1A1A]'" class="p-5 rounded-2xl space-y-3">
        <h3 class="font-black text-sm border-b-2 border-black pb-2" :class="isDark ? 'text-white border-slate-800' : ''">Metode Pembayaran</h3>

        <!-- Cash - aktif -->
        <label :class="isDark ? 'border-slate-700 bg-slate-950' : 'border-2 border-black bg-white'"
          class="flex items-center gap-3 p-4 rounded-xl cursor-pointer transition-all"
          :style="selectedMethod === 'cash' ? 'box-shadow: 3px 3px 0px #1A1A1A;' : ''">
          <input type="radio" v-model="selectedMethod" value="cash" class="w-4 h-4 accent-black" />
          <span class="text-xl">💵</span>
          <div class="flex-1">
            <p class="font-black text-sm">Cash / Bayar di Kasir</p>
            <p class="text-[10px] font-bold text-slate-500">Bayar langsung saat mengambil pesanan</p>
          </div>
        </label>

        <!-- Transfer Bank - disabled -->
        <div class="flex items-center gap-3 p-4 rounded-xl border-2 border-dashed border-slate-300 opacity-50 cursor-not-allowed">
          <input type="radio" disabled class="w-4 h-4" />
          <span class="text-xl">🏦</span>
          <div class="flex-1">
            <p class="font-black text-sm">Transfer Bank</p>
            <p class="text-[10px] font-bold text-slate-500">Belum tersedia</p>
          </div>
          <span class="text-[9px] font-black uppercase bg-slate-200 text-slate-600 px-2 py-1 rounded-full">Segera Hadir</span>
        </div>

        <!-- E-Wallet / QRIS - disabled -->
        <div class="flex items-center gap-3 p-4 rounded-xl border-2 border-dashed border-slate-300 opacity-50 cursor-not-allowed">
          <input type="radio" disabled class="w-4 h-4" />
          <span class="text-xl">📱</span>
          <div class="flex-1">
            <p class="font-black text-sm">QRIS / E-Wallet</p>
            <p class="text-[10px] font-bold text-slate-500">Belum tersedia</p>
          </div>
          <span class="text-[9px] font-black uppercase bg-slate-200 text-slate-600 px-2 py-1 rounded-full">Segera Hadir</span>
        </div>
      </div>

      <button
        @click="confirmOrder"
        :disabled="loading"
        :class="isDark ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-[#C8F53F] hover:bg-[#b8e82f] text-black border-2 border-black shadow-[4px_4px_0px_#1A1A1A] active:translate-x-[2px] active:translate-y-[2px]'"
        class="w-full py-3.5 rounded-2xl font-black text-sm transition-all disabled:opacity-50"
      >
        <span v-if="loading">Memproses Pesanan...</span>
        <span v-else>Buat Pesanan Sekarang &rarr;</span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const api = useApi()
const cartStore = useCartStore()
const { isDark } = useTheme()
const loading = ref(false)
const selectedMethod = ref('cash')

const formatNumber = (val: number) => new Intl.NumberFormat('id-ID').format(val)

const totalPrice = computed(() =>
  cartStore.checkoutSelection.reduce((sum, i) => sum + (i.harga_jual * i.qty), 0)
)

onMounted(() => {
  // guard: kalau user refresh/langsung akses page ini tanpa pilih item
  if (cartStore.checkoutSelection.length === 0) {
    navigateTo('/user/keranjang')
  }
})

const confirmOrder = async () => {
  loading.value = true
  try {
    const payload = {
      items: cartStore.checkoutSelection.map(i => ({
        book_id: i.book_id,
        qty: i.qty
      })),
      payment_method: selectedMethod.value // 'cash' untuk sekarang
    }

    const res = await api.post<{ message: string; data: any }>('/api/orders', payload)

    // hapus cuma item yang di-checkout dari keranjang, sisanya tetap ada
    for (const item of cartStore.checkoutSelection) {
      await cartStore.removeFromCart(item.book_id)
    }
    cartStore.clearCheckoutSelection()

    const toast = useToast()
    toast.success(res.message || 'Pesanan berhasil dibuat!')
    navigateTo('/user/riwayat')
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal memproses checkout.')
  } finally {
    loading.value = false
  }
}
</script>
