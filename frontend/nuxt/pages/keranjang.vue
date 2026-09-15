<template>
  <div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border text-[10px] font-mono tracking-wider font-bold uppercase mb-2" :class="isDark ? 'border-zinc-700 bg-zinc-900 text-zinc-300' : 'border-black bg-zinc-100 text-black shadow-[2px_2px_0px_#000000]'">
          KERANJANG BELANJA
        </div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
          Keranjang Buku
        </h1>
        <p class="text-xs font-medium mt-1" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
          Periksa kembali daftar buku yang ingin Anda bawa pulang.
        </p>
      </div>

      <button 
        v-if="cartStore.items.length > 0" 
        @click="cartStore.clearCart()" 
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-rose-400 hover:bg-rose-950/40 hover:border-rose-800' : 'bg-white border-2 border-black text-rose-600 shadow-[2px_2px_0px_#000000] hover:bg-rose-50'" 
        class="text-xs font-black px-3.5 py-2 rounded-xl border transition-all cursor-pointer"
      >
        Kosongkan Keranjang
      </button>
    </div>

    <!-- Empty State -->
    <div 
      v-if="cartStore.items.length === 0" 
      :class="isDark ? 'bg-zinc-900/60 border-zinc-800 text-zinc-300' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'" 
      class="text-center py-16 px-6 rounded-3xl max-w-xl mx-auto border-2 transition-colors"
    >
      <div 
        :class="isDark ? 'bg-zinc-950 border-zinc-800' : 'bg-zinc-100 border-2 border-black shadow-[2px_2px_0px_#000000]'"
        class="w-16 h-16 border rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4"
      >
        🛒
      </div>
      <h3 :class="isDark ? 'text-white' : 'text-black'" class="font-black text-lg mb-1">Keranjang Masih Kosong</h3>
      <p :class="isDark ? 'text-zinc-400' : 'text-zinc-600'" class="text-xs font-medium mb-6 leading-relaxed">
        Jelajahi koleksi literatur kurasi kami dan temukan bacaan terbaik untuk Anda.
      </p>
      <NuxtLink 
        to="/katalog" 
        :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'" 
        class="px-6 py-3 rounded-xl text-xs font-black inline-flex items-center gap-2 border-2 transition-all cursor-pointer hover:-translate-y-0.5"
      >
        <span>📚 Buka Katalog Buku</span>
        <span>&rarr;</span>
      </NuxtLink>
    </div>

    <!-- Cart Grid with Items & Summary -->
    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <!-- Item List -->
      <div class="md:col-span-2 space-y-4">
        <!-- Select All Bar -->
        <div 
          :class="isDark ? 'bg-zinc-900 border-zinc-800 text-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
          class="p-3.5 rounded-xl border flex items-center justify-between"
        >
          <label class="flex items-center gap-2.5 cursor-pointer select-none">
            <input 
              type="checkbox" 
              :checked="isAllSelected" 
              @change="toggleSelectAll"
              class="w-4 h-4 accent-black dark:accent-white rounded cursor-pointer" 
            />
            <span class="text-xs font-black">Pilih Semua ({{ cartStore.items.length }} judul)</span>
          </label>
          <span class="text-xs font-mono font-bold text-zinc-400">{{ selectedIds.length }} dipilih</span>
        </div>

        <!-- Item Cards -->
        <div
          v-for="item in cartStore.items"
          :key="item.book_id"
          :class="[
            isDark ? 'bg-zinc-900/80 border-zinc-800' : 'bg-white border-2 border-black shadow-[3px_3px_0px_#000000]',
            !selectedIds.includes(item.book_id) ? 'opacity-50' : ''
          ]"
          class="p-4 rounded-2xl border flex items-center justify-between gap-4 transition-opacity"
        >
          <div class="flex items-center gap-3.5">
            <input 
              type="checkbox" 
              :checked="selectedIds.includes(item.book_id)"
              @change="toggleSelect(item.book_id)"
              class="w-4 h-4 accent-black dark:accent-white rounded shrink-0 cursor-pointer" 
            />

            <div :class="isDark ? 'bg-zinc-950 border-zinc-800' : 'bg-zinc-100 border-2 border-black'" class="w-14 h-18 rounded-xl overflow-hidden flex items-center justify-center shrink-0 border">
              <img 
                v-if="item.gambar" 
                :src="getImageUrl(item.gambar)" 
                :alt="item.nama_buku" 
                class="w-full h-full object-cover" 
                @error="item.gambar = null"
              />
              <span v-else class="text-2xl">📕</span>
            </div>
            <div>
              <h4 :class="isDark ? 'text-white' : 'text-black'" class="font-black text-xs sm:text-sm line-clamp-1">
                {{ item.nama_buku }}
              </h4>
              <p class="text-xs font-black font-mono mt-1" :class="isDark ? 'text-zinc-300' : 'text-black'">
                Rp {{ formatNumber(item.harga_jual) }}
              </p>
              <p :class="isDark ? 'text-zinc-500' : 'text-zinc-400'" class="text-[10px] font-bold mt-0.5">
                Stok: {{ item.stok }}
              </p>
            </div>
          </div>

          <!-- Quantity Controls -->
          <div class="flex items-center gap-2.5">
            <div :class="isDark ? 'border-zinc-700 bg-zinc-950' : 'border-2 border-black bg-zinc-50 shadow-[1.5px_1.5px_0px_#000000]'" class="flex items-center rounded-lg overflow-hidden border">
              <button 
                @click="updateQty(item.book_id, item.qty - 1)" 
                :class="isDark ? 'text-zinc-300 hover:bg-zinc-800' : 'text-black hover:bg-zinc-200 border-r-2 border-black'" 
                class="px-2.5 py-1 text-xs font-black cursor-pointer"
              >-</button>
              <span :class="isDark ? 'text-white' : 'text-black'" class="px-2.5 py-1 text-xs font-black font-mono">{{ item.qty }}</span>
              <button 
                @click="updateQty(item.book_id, item.qty + 1)" 
                :class="isDark ? 'text-zinc-300 hover:bg-zinc-800' : 'text-black hover:bg-zinc-200 border-l-2 border-black'" 
                class="px-2.5 py-1 text-xs font-black cursor-pointer"
              >+</button>
            </div>

            <button 
              @click="cartStore.removeFromCart(item.book_id)" 
              class="text-zinc-400 hover:text-rose-500 text-xs p-1.5 transition-colors cursor-pointer"
              title="Hapus dari keranjang"
            >
              🗑️
            </button>
          </div>
        </div>
      </div>

      <!-- Order Summary Card -->
      <div 
        :class="isDark ? 'bg-zinc-900 border-zinc-800 text-white' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'" 
        class="p-6 rounded-2xl border h-max space-y-4"
      >
        <h3 :class="isDark ? 'border-zinc-800' : 'border-zinc-200'" class="font-black text-sm pb-3 border-b uppercase font-mono tracking-wider">
          Ringkasan Pesanan
        </h3>

        <div class="flex justify-between text-xs font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
          <span>Item Dipilih</span>
          <span class="font-mono font-black" :class="isDark ? 'text-white' : 'text-black'">{{ selectedTotalItems }} buku</span>
        </div>

        <div :class="isDark ? 'border-zinc-800' : 'border-zinc-200'" class="flex justify-between text-sm font-black pt-3 border-t">
          <span>Total Harga</span>
          <span class="font-mono" :class="isDark ? 'text-white' : 'text-black'">
            Rp {{ formatNumber(selectedTotalPrice) }}
          </span>
        </div>

        <button
          @click="goToPayment"
          :disabled="selectedIds.length === 0"
          :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
          class="w-full py-3 rounded-xl font-black text-xs border-2 transition-all disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer hover:-translate-y-0.5 mt-4"
        >
          <span v-if="selectedIds.length === 0">Pilih Buku Terlebih Dahulu</span>
          <span v-else>Lanjut ke Pembayaran &rarr;</span>
        </button>

        <p :class="isDark ? 'text-zinc-500' : 'text-zinc-400'" class="text-[10px] font-mono text-center leading-relaxed">
          Kode pesanan akan otomatis dibuatkan oleh sistem untuk proses verifikasi transaksi.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Keranjang Belanja - TokoBukuDigital'
})

const api = useApi()
const cartStore = useCartStore()
const { isDark } = useTheme()
const { getImageUrl } = useImageUrl()

const selectedIds = ref<number[]>([])

const formatNumber = (val: number) => {
  return new Intl.NumberFormat('id-ID').format(val || 0)
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
  selectedIds.value = cartStore.items.map(i => i.book_id)
})

const goToPayment = () => {
  if (selectedIds.value.length === 0) return
  cartStore.setCheckoutSelection(selectedItems.value)
  navigateTo('/checkout/pembayaran')
}
</script>
