<template>
  <div class="max-w-4xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 :class="isDark ? 'text-white' : 'text-slate-900 bg-[#FFE566] px-3 py-1 inline-block rounded-2xl border-2.5 border-black shadow-[4px_4px_0px_#1A1A1A]'" class="text-3xl font-black">Keranjang Belanja</h1>
        <p :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-bold mt-2">Periksa kembali daftar buku yang akan Anda beli</p>
      </div>

      <button v-if="cartStore.items.length > 0" @click="cartStore.clearCart()" :class="isDark ? 'text-rose-500' : 'bg-[#FFB7B2] text-black border-2 border-black px-3 py-1.5 rounded-xl shadow-[2px_2px_0px_#1A1A1A]'" class="text-xs font-black hover:opacity-90">
        Kosongkan Keranjang
      </button>
    </div>

    <div v-if="cartStore.items.length === 0" :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#D4B8FF] border-3 border-[#1A1A1A] shadow-[8px_8px_0px_#1A1A1A]'" class="text-center py-16 px-6 rounded-3xl max-w-xl mx-auto transition-colors">
      <div class="w-20 h-20 bg-white border-2 border-black rounded-2xl flex items-center justify-center text-4xl mx-auto mb-4 shadow-[3px_3px_0px_#1A1A1A]">🛒</div>
      <h3 :class="isDark ? 'text-slate-200' : 'text-slate-900'" class="font-black text-xl mb-1">Keranjang Belanja Masih Kosong</h3>
      <p :class="isDark ? 'text-slate-400' : 'text-slate-900'" class="text-xs font-bold mb-6 leading-relaxed">Jelajahi katalog kami untuk menemukan buku favoritmu dan mulai tambahkan ke keranjang.</p>
      <NuxtLink to="/user/katalog" :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[4px_4px_0px_#1A1A1A] hover:translate-x-[-2px] hover:translate-y-[-2px] active:translate-x-[2px] active:translate-y-[2px]'" class="px-6 py-3.5 rounded-2xl text-xs font-black inline-block transition-all">
        📚 Ke Katalog Buku Sekarang &rarr;
      </NuxtLink>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <!-- Item List -->
      <div class="md:col-span-2 space-y-4">
        <!-- Select All bar -->
        <div :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#FFE566] border-2 border-black shadow-[3px_3px_0px_#1A1A1A]'"
          class="p-3 rounded-2xl flex items-center justify-between">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll"
              class="w-4 h-4 accent-black rounded border-2 border-black" />
            <span class="text-xs font-black">Pilih Semua ({{ cartStore.items.length }} buku)</span>
          </label>
          <span class="text-xs font-black">{{ selectedIds.length }} dipilih</span>
        </div>

        <div
          v-for="item in cartStore.items"
          :key="item.book_id"
          :class="[
            isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#FFF8EC] border-2.5 border-[#1A1A1A] shadow-[4px_4px_0px_#1A1A1A]',
            !selectedIds.includes(item.book_id) ? 'opacity-50' : ''
          ]"
          class="p-4 rounded-2xl flex items-center justify-between gap-4 transition-opacity"
        >
          <div class="flex items-center gap-4">
            <input type="checkbox" :checked="selectedIds.includes(item.book_id)"
              @change="toggleSelect(item.book_id)"
              class="w-4 h-4 accent-black rounded border-2 border-black shrink-0" />

            <div :class="isDark ? 'bg-slate-950' : 'bg-[#FAF7F0] border-2 border-black'" class="w-16 h-20 rounded-xl overflow-hidden flex items-center justify-center shrink-0">
              <img v-if="item.gambar" :src="item.gambar" :alt="item.nama_buku" class="w-full h-full object-cover" />
              <span v-else class="text-2xl">📕</span>
            </div>
            <div>
              <h4 :class="isDark ? 'text-white' : 'text-slate-900'" class="font-black text-sm line-clamp-1">{{ item.nama_buku }}</h4>
              <p class="text-xs font-black text-slate-900 mt-1" :class="isDark ? 'text-indigo-400' : 'bg-[#C8F53F] px-1.5 rounded border border-black inline-block'">Rp {{ formatNumber(item.harga_jual) }}</p>
              <p :class="isDark ? 'text-slate-400' : 'text-slate-600'" class="text-[10px] font-bold mt-0.5">Sisa stok: {{ item.stok }}</p>
            </div>
          </div>

          <!-- Qty controls -->
          <div class="flex items-center gap-3">
            <div :class="isDark ? 'border-slate-700 bg-slate-950' : 'border-2 border-black bg-white shadow-[2px_2px_0px_#1A1A1A]'" class="flex items-center rounded-xl overflow-hidden">
              <button @click="updateQty(item.book_id, item.qty - 1)" :class="isDark ? 'text-slate-300 hover:bg-slate-800' : 'text-black hover:bg-[#FFE566] border-r-2 border-black'" class="px-3 py-1 text-xs font-black">-</button>
              <span :class="isDark ? 'text-white' : 'text-slate-900'" class="px-3 py-1 text-xs font-black">{{ item.qty }}</span>
              <button @click="updateQty(item.book_id, item.qty + 1)" :class="isDark ? 'text-slate-300 hover:bg-slate-800' : 'text-black hover:bg-[#FFE566] border-l-2 border-black'" class="px-3 py-1 text-xs font-black">+</button>
            </div>

            <button @click="cartStore.removeFromCart(item.book_id)" class="text-rose-500 hover:text-rose-700 text-sm p-1">
              🗑️
            </button>
          </div>
        </div>
      </div>

      <!-- Order Summary Card -->
      <div :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#FFF8EC] border-3 border-[#1A1A1A] shadow-[6px_6px_0px_#1A1A1A]'" class="p-6 rounded-3xl h-max space-y-4">
        <h3 :class="isDark ? 'text-white border-slate-800' : 'text-slate-900 border-b-2 border-black'" class="font-black text-base pb-3">Ringkasan Pesanan</h3>

        <div :class="isDark ? 'text-slate-300' : 'text-slate-800'" class="flex justify-between text-xs font-bold">
          <span>Item Dipilih</span>
          <span class="font-black">{{ selectedTotalItems }} buku</span>
        </div>

        <div :class="isDark ? 'text-white border-slate-800' : 'text-slate-900 border-t-2 border-black'" class="flex justify-between text-sm font-black pt-3">
          <span>Total Harga</span>
          <span :class="isDark ? 'text-indigo-400' : 'bg-[#C8F53F] px-2 py-0.5 rounded border border-black shadow-[1.5px_1.5px_0px_#1A1A1A]'">Rp {{ formatNumber(selectedTotalPrice) }}</span>
        </div>

        <button
          @click="goToPayment"
          :disabled="selectedIds.length === 0"
          :class="isDark ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-[#C8F53F] hover:bg-[#b8e82f] text-black border-2 border-black shadow-[4px_4px_0px_#1A1A1A] active:translate-x-[2px] active:translate-y-[2px]'"
          class="w-full py-3.5 rounded-2xl font-black text-sm transition-all disabled:opacity-40 disabled:cursor-not-allowed mt-4"
        >
          <span v-if="selectedIds.length === 0">Pilih Buku Dulu</span>
          <span v-else>Lanjut ke Pembayaran &rarr;</span>
        </button>

        <p :class="isDark ? 'text-slate-500' : 'text-slate-600'" class="text-[10px] font-bold text-center leading-relaxed">
          Kode pesanan akan otomatis di-generate oleh sistem backend (Format A021).
        </p>
      </div>
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

const selectedIds = ref<number[]>([])

const formatNumber = (val: number) => {
  return new Intl.NumberFormat('id-ID').format(val)
}

const isAllSelected = computed(() =>
  cartStore.items.length > 0 && selectedIds.value.length === cartStore.items.length
)

const toggleSelectAll = () => {
  selectedIds.value = isAllSelected.value
    ? []
    : cartStore.items.map(i => i.book_id)
}

const toggleSelect = (bookId: number) => {
  const idx = selectedIds.value.indexOf(bookId)
  if (idx === -1) {
    selectedIds.value.push(bookId)
  } else {
    selectedIds.value.splice(idx, 1)
  }
}

const selectedItems = computed(() =>
  cartStore.items.filter(i => selectedIds.value.includes(i.book_id))
)

const selectedTotalItems = computed(() =>
  selectedItems.value.reduce((sum, i) => sum + i.qty, 0)
)

const selectedTotalPrice = computed(() =>
  selectedItems.value.reduce((sum, i) => sum + (i.harga_jual * i.qty), 0)
)

const updateQty = async (bookId: number, qty: number) => {
  try {
    const toast = useToast()
    await cartStore.updateQty(bookId, qty)
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.message || 'Stok tidak mencukupi')
  }
}

onMounted(async () => {
  await cartStore.fetchCart()
  // default: semua item ke-select pas pertama load
  selectedIds.value = cartStore.items.map(i => i.book_id)
})

const goToPayment = () => {
  if (selectedIds.value.length === 0) return
  // simpan pilihan ke store biar bisa diakses di halaman pembayaran
  cartStore.setCheckoutSelection(selectedItems.value)
  navigateTo('/user/checkout/pembayaran')
}
</script>
