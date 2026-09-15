<template>
  <div class="max-w-2xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center gap-3">
      <NuxtLink 
        to="/keranjang" 
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'" 
        class="w-9 h-9 rounded-xl border flex items-center justify-center font-black cursor-pointer hover:scale-105"
      >
        &larr;
      </NuxtLink>
      <div>
        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-md border text-[9px] font-mono tracking-wider font-bold uppercase mb-1" :class="isDark ? 'border-zinc-700 bg-zinc-900 text-zinc-300' : 'border-black bg-zinc-100 text-black'">
          CHECKOUT
        </div>
        <h1 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
          Metode Pembayaran
        </h1>
      </div>
    </div>

    <!-- Empty Checkout State -->
    <div 
      v-if="cartStore.checkoutSelection.length === 0" 
      :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-300' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'" 
      class="text-center py-14 rounded-3xl border-2"
    >
      <p class="font-black text-xs sm:text-sm mb-4">Belum ada item buku yang dipilih untuk checkout.</p>
      <NuxtLink 
        to="/keranjang" 
        :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'" 
        class="px-5 py-2.5 rounded-xl text-xs font-black inline-block border-2 cursor-pointer"
      >
        Kembali ke Keranjang
      </NuxtLink>
    </div>

    <div v-else class="space-y-6">
      <!-- Ringkasan Item -->
      <div 
        :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-100' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'" 
        class="p-5 rounded-2xl border space-y-3"
      >
        <h3 class="font-black text-xs uppercase font-mono tracking-wider pb-2 border-b" :class="isDark ? 'border-zinc-800 text-zinc-400' : 'border-zinc-200 text-zinc-600'">
          Ringkasan Pesanan
        </h3>
        
        <div v-for="item in cartStore.checkoutSelection" :key="item.book_id" class="flex justify-between text-xs font-medium">
          <span>{{ item.nama_buku }} <span class="text-zinc-400 font-mono">x{{ item.qty }}</span></span>
          <span class="font-mono font-bold">Rp {{ formatNumber(item.harga_jual * item.qty) }}</span>
        </div>
        
        <div class="flex justify-between text-sm font-black pt-3 border-t font-mono" :class="isDark ? 'border-zinc-800 text-white' : 'border-zinc-200 text-black'">
          <span>Total Pembayaran</span>
          <span>Rp {{ formatNumber(totalPrice) }}</span>
        </div>
      </div>

      <!-- Metode Pembayaran Selection -->
      <div 
        :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-100' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'" 
        class="p-5 rounded-2xl border space-y-3"
      >
        <h3 class="font-black text-xs uppercase font-mono tracking-wider pb-2 border-b" :class="isDark ? 'border-zinc-800 text-zinc-400' : 'border-zinc-200 text-zinc-600'">
          Pilih Metode Bayar
        </h3>

        <!-- Cash / Bayar di Kasir (Active) -->
        <label 
          :class="isDark ? 'border-zinc-700 bg-zinc-950' : 'border-2 border-black bg-zinc-50 shadow-[2px_2px_0px_#000000]'"
          class="flex items-center gap-3 p-4 rounded-xl cursor-pointer transition-all border-2"
        >
          <input type="radio" v-model="selectedMethod" value="cash" class="w-4 h-4 accent-black dark:accent-white cursor-pointer" />
          <span class="text-xl">💵</span>
          <div class="flex-1">
            <p class="font-black text-xs sm:text-sm">Cash / Bayar di Kasir</p>
            <p class="text-[10px] font-medium text-zinc-400">Bayar langsung saat mengambil pesanan fisik di toko</p>
          </div>
        </label>

        <!-- Transfer Bank (Disabled) -->
        <div class="flex items-center gap-3 p-3.5 rounded-xl border border-dashed border-zinc-400 opacity-40 cursor-not-allowed">
          <input type="radio" disabled class="w-4 h-4" />
          <span class="text-lg">🏦</span>
          <div class="flex-1">
            <p class="font-black text-xs">Transfer Bank Virtual Account</p>
            <p class="text-[10px] text-zinc-400">Segera hadir</p>
          </div>
          <span class="text-[9px] font-mono font-bold uppercase px-2 py-0.5 rounded border border-zinc-400">Soon</span>
        </div>

        <!-- QRIS / E-Wallet (Disabled) -->
        <div class="flex items-center gap-3 p-3.5 rounded-xl border border-dashed border-zinc-400 opacity-40 cursor-not-allowed">
          <input type="radio" disabled class="w-4 h-4" />
          <span class="text-lg">📱</span>
          <div class="flex-1">
            <p class="font-black text-xs">QRIS / E-Wallet Instan</p>
            <p class="text-[10px] text-zinc-400">Segera hadir</p>
          </div>
          <span class="text-[9px] font-mono font-bold uppercase px-2 py-0.5 rounded border border-zinc-400">Soon</span>
        </div>
      </div>

      <!-- Submit Order Button -->
      <button
        @click="confirmOrder"
        :disabled="loading"
        :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
        class="w-full py-3.5 rounded-xl font-black text-xs sm:text-sm border-2 transition-all disabled:opacity-50 cursor-pointer hover:-translate-y-0.5"
      >
        <span v-if="loading">Memproses Pesanan...</span>
        <span v-else class="flex items-center justify-center gap-2">
          <span>Konfirmasi & Buat Pesanan</span>
          <span>&rarr;</span>
        </span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Konfirmasi Pembayaran - TokoBukuDigital'
})

const api = useApi()
const cartStore = useCartStore()
const { isDark } = useTheme()
const loading = ref(false)
const selectedMethod = ref('cash')

const formatNumber = (val: number) => new Intl.NumberFormat('id-ID').format(val || 0)

const totalPrice = computed(() =>
  cartStore.checkoutSelection.reduce((sum, i) => sum + (i.harga_jual * i.qty), 0)
)

onMounted(() => {
  if (cartStore.checkoutSelection.length === 0) {
    navigateTo('/keranjang')
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
      payment_method: selectedMethod.value
    }

    const res = await api.post<{ message: string; data: any }>('/api/orders', payload)

    for (const item of cartStore.checkoutSelection) {
      await cartStore.removeFromCart(item.book_id)
    }
    cartStore.clearCheckoutSelection()

    const toast = useToast()
    toast.success(res.message || 'Pesanan berhasil dibuat!')
    navigateTo('/riwayat')
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal memproses checkout.')
  } finally {
    loading.value = false
  }
}
</script>
