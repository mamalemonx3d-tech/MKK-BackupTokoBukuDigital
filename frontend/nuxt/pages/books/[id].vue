<template>
  <div class="max-w-6xl mx-auto space-y-8 py-2">
    <!-- Back Button -->
    <button
      @click="router.back()"
      :class="isDark ? 'text-zinc-400 hover:text-white' : 'text-zinc-600 hover:text-black'"
      class="text-xs font-bold flex items-center gap-1.5 transition-colors cursor-pointer"
    >
      &larr; Kembali ke Katalog
    </button>

    <!-- Loading State -->
    <div v-if="loading" :class="isDark ? 'text-zinc-500' : 'text-zinc-500'" class="text-center py-20 text-xs font-bold">
      Memuat detail buku...
    </div>

    <!-- Not Found State -->
    <div
      v-else-if="!book"
      :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-300' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'"
      class="text-center py-20 rounded-3xl border-2"
    >
      <div class="text-4xl mb-2">📕</div>
      <h3 :class="isDark ? 'text-white' : 'text-black'" class="font-black text-lg">Buku tidak ditemukan</h3>
      <p class="text-xs font-medium text-zinc-500 mt-1">
        Buku mungkin sudah dihapus atau URL salah.
      </p>
    </div>

    <!-- Book Detail -->
    <template v-else>
      <!-- Detail utama -->
      <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        <!-- Gambar -->
        <div
          :class="isDark ? 'bg-zinc-950 border-zinc-800' : 'bg-zinc-100 border-2 border-black shadow-[4px_4px_0px_#000000]'"
          class="md:col-span-5 rounded-3xl h-80 sm:h-96 flex items-center justify-center overflow-hidden border-2 relative"
        >
          <img 
            v-if="book.gambar" 
            :src="getImageUrl(book.gambar)" 
            :alt="book.nama_buku" 
            class="w-full h-full object-cover" 
            @error="book.gambar = null"
          />
          <div v-else class="text-6xl">📕</div>
        </div>

        <!-- Info -->
        <div class="md:col-span-7 flex flex-col justify-between space-y-6">
          <div>
            <!-- Category Badge -->
            <span
              :class="isDark ? 'bg-zinc-900 text-white border-zinc-700' : 'bg-black text-white border-black'"
              class="inline-block text-[10px] font-mono font-bold uppercase tracking-wider px-3 py-1 rounded-md border mb-3"
            >
              {{ book.category?.nama_kategori || 'Buku' }}
            </span>

            <!-- Title -->
            <h1 :class="isDark ? 'text-white' : 'text-black'" class="text-2xl sm:text-3xl font-black mb-2 tracking-tight">
              {{ book.nama_buku }}
            </h1>

            <!-- Stock & Year -->
            <p :class="isDark ? 'text-zinc-400' : 'text-zinc-500'" class="text-xs font-bold mb-4 font-mono">
              Tahun: {{ book.tahun_terbit || '-' }} • Stok:
              <span
                :class="book.stok > 0 ? (isDark ? 'text-emerald-400' : 'text-black font-black') : 'text-rose-500 font-black'"
              >
                {{ book.stok }}
              </span>
            </p>

            <!-- Rating ringkas -->
            <div v-if="reviews.length" class="flex items-center gap-2 mb-4 font-mono">
              <span class="text-xs font-black" :class="isDark ? 'text-white' : 'text-black'">
                ★ {{ avgRating.toFixed(1) }}
              </span>
              <span class="text-[11px] text-zinc-400">
                ({{ reviews.length }} ulasan)
              </span>
            </div>

            <!-- Description -->
            <p :class="isDark ? 'text-zinc-300' : 'text-zinc-700'" class="text-xs sm:text-sm font-medium leading-relaxed">
              {{ book.deskripsi || 'Tidak ada ringkasan deskripsi untuk buku ini.' }}
            </p>
          </div>

          <!-- Price & Action Buttons -->
          <div :class="isDark ? 'border-zinc-800' : 'border-zinc-200'" class="pt-5 border-t space-y-4">
            <div>
              <span class="text-[9px] block uppercase font-mono font-bold tracking-wider text-zinc-400">
                Harga
              </span>
              <span class="text-2xl font-black font-mono" :class="isDark ? 'text-white' : 'text-black'">
                Rp {{ formatNumber(book.harga_jual) }}
              </span>
            </div>

            <!-- Quantity & Add to Cart -->
            <div class="flex items-center gap-3 flex-wrap">
              <div :class="isDark ? 'border-zinc-700 bg-zinc-950' : 'border-2 border-black bg-zinc-50 shadow-[1.5px_1.5px_0px_#000000]'" class="flex items-center rounded-xl overflow-hidden border">
                <button
                  @click="decrementQty"
                  :class="isDark ? 'text-zinc-300 hover:bg-zinc-800' : 'text-black hover:bg-zinc-200 border-r-2 border-black'"
                  class="w-8 h-8 font-black text-xs transition-colors cursor-pointer"
                >
                  -
                </button>
                <span :class="isDark ? 'text-white' : 'text-black'" class="font-black text-xs w-8 text-center font-mono">
                  {{ qty }}
                </span>
                <button
                  @click="incrementQty"
                  :class="isDark ? 'text-zinc-300 hover:bg-zinc-800' : 'text-black hover:bg-zinc-200 border-l-2 border-black'"
                  class="w-8 h-8 font-black text-xs transition-colors cursor-pointer"
                >
                  +
                </button>
              </div>

              <button
                @click="handleAddToCart"
                :disabled="book.stok <= 0"
                :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
                class="px-5 py-2.5 rounded-xl font-black text-xs transition-all flex items-center gap-2 border-2 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer hover:-translate-y-0.5"
              >
                <span>🛒</span>
                <span v-if="book.stok > 0">Tambah ke Keranjang</span>
                <span v-else>Stok Habis</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Divider -->
      <div :class="isDark ? 'border-zinc-800' : 'border-zinc-200'" class="border-b pt-4"></div>

      <!-- Reviews Section -->
      <div class="space-y-4">
        <h2 :class="isDark ? 'text-white' : 'text-black'" class="text-base font-black uppercase font-mono tracking-wider">
          Ulasan Pembeli
        </h2>

        <!-- Reviews Loading -->
        <div
          v-if="reviewsLoading"
          class="text-xs py-6 text-center text-zinc-400 font-medium"
        >
          Memuat ulasan...
        </div>

        <!-- No Reviews -->
        <div
          v-else-if="reviews.length === 0"
          :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-400' : 'bg-white border-2 border-black text-zinc-600 shadow-[2px_2px_0px_#000000]'"
          class="rounded-2xl p-6 text-center border"
        >
          <p class="text-xs font-medium">
            Belum ada ulasan untuk buku ini.
          </p>
        </div>

        <!-- Reviews List -->
        <div v-else class="space-y-3 max-w-3xl">
          <div
            v-for="rev in reviews"
            :key="rev.id"
            :class="isDark ? 'bg-zinc-900/60 border-zinc-800' : 'bg-white border-2 border-black shadow-[2px_2px_0px_#000000]'"
            class="rounded-2xl p-4 border"
          >
            <div class="flex items-center justify-between mb-1.5">
              <span :class="isDark ? 'text-white' : 'text-black'" class="font-black text-xs">
                {{ rev.nama_user }}
              </span>
              <span class="text-xs text-amber-400">
                {{ '⭐'.repeat(rev.rating) }}
              </span>
            </div>
            <p :class="isDark ? 'text-zinc-300' : 'text-zinc-700'" class="text-xs font-medium">
              {{ rev.komentar }}
            </p>
            <p class="text-[10px] text-zinc-400 font-mono mt-2">
              {{ formatDate(rev.created_at) }}
            </p>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const router = useRouter()
const api = useApi()
const cartStore = useCartStore()
const { isDark } = useTheme()
const { getImageUrl } = useImageUrl()
const toast = useToast()

const bookId = route.params.id as string

const book = ref<any>(null)
const reviews = ref<any[]>([])
const loading = ref(true)
const reviewsLoading = ref(true)
const qty = ref(1)

const formatNumber = (val: number) => new Intl.NumberFormat('id-ID').format(val)

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const avgRating = computed(() => {
  if (!reviews.value.length) return 0
  return reviews.value.reduce((sum, r) => sum + r.rating, 0) / reviews.value.length
})

const incrementQty = () => {
  if (book.value && qty.value < book.value.stok) qty.value++
}

const decrementQty = () => {
  if (qty.value > 1) qty.value--
}

const fetchBook = async () => {
  loading.value = true
  try {
    const res = await api.get(`/api/books/${bookId}`)
    book.value = res.data
  } catch (e) {
    console.error('Failed to fetch book:', e)
    book.value = null
  } finally {
    loading.value = false
  }
}

const fetchReviews = async () => {
  reviewsLoading.value = true
  try {
    const res = await api.get(`/api/books/${bookId}/reviews`)
    reviews.value = res.data || []
  } catch (e) {
    console.error('Failed to fetch reviews:', e)
    reviews.value = []
  } finally {
    reviewsLoading.value = false
  }
}

const handleAddToCart = async () => {
  try {
    await cartStore.addToCart(book.value, qty.value)
    toast.success(`'${book.value.nama_buku}' ditambahkan ke keranjang.`)
    qty.value = 1
  } catch (err: any) {
    toast.error(err.message || 'Gagal menambahkan ke keranjang.')
  }
}

onMounted(async () => {
  await fetchBook()
  await fetchReviews()
})
</script>
