<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
    <!-- Back Button -->
    <button
      @click="router.back()"
      :class="isDark ? 'text-slate-400 hover:text-white' : 'text-slate-700 hover:text-black'"
      class="text-xs font-bold mb-6 flex items-center gap-1.5 transition-colors"
    >
      ← Kembali ke katalog
    </button>

    <!-- Loading State -->
    <div v-if="loading" :class="isDark ? 'text-slate-400' : 'text-slate-700 font-black'" class="text-center py-20">
      <div class="text-2xl mb-2">📚</div>
      Memuat detail buku...
    </div>

    <!-- Not Found State -->
    <div
      v-else-if="!book"
      :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#FFF8EC] border-3 border-[#1A1A1A] shadow-[6px_6px_0px_#1A1A1A]'"
      class="text-center py-20 rounded-3xl"
    >
      <div class="text-4xl mb-2">📕</div>
      <h3 :class="isDark ? 'text-slate-200' : 'text-slate-900'" class="font-black text-lg">Buku tidak ditemukan</h3>
      <p :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-bold mt-1">
        Buku mungkin sudah dihapus atau link salah.
      </p>
    </div>

    <!-- Book Detail -->
    <template v-else>
      <!-- Detail utama -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 mb-12">
        <!-- Gambar -->
        <div
          :class="isDark ? 'bg-slate-950' : 'bg-[#FAF7F0] border-2.5 border-[#1A1A1A] shadow-[4px_4px_0px_#1A1A1A]'"
          class="rounded-2xl h-80 sm:h-96 flex items-center justify-center overflow-hidden"
        >
          <img v-if="book.gambar" :src="book.gambar" :alt="book.nama_buku" class="w-full h-full object-cover" />
          <div v-else class="text-6xl">📕</div>
        </div>

        <!-- Info -->
        <div class="flex flex-col justify-between">
          <div>
            <!-- Category Badge -->
            <span
              :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'"
              class="inline-block text-[10px] font-black px-2.5 py-1 rounded-full mb-3"
            >
              {{ book.category?.nama_kategori || 'Buku' }}
            </span>

            <!-- Title -->
            <h1 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-xl sm:text-2xl font-black mb-2">
              {{ book.nama_buku }}
            </h1>

            <!-- Stock & Year -->
            <p :class="isDark ? 'text-slate-400' : 'text-slate-600'" class="text-xs font-bold mb-4">
              Tahun Terbit: {{ book.tahun_terbit || '-' }} • Stok:
              <span
                :class="book.stok > 0 ? (isDark ? 'text-emerald-500' : 'text-slate-900 bg-[#C8F53F] px-1 rounded border border-black') : 'text-rose-600 font-black'"
              >
                {{ book.stok }}
              </span>
            </p>

            <!-- Rating ringkas -->
            <div v-if="reviews.length" class="flex items-center gap-2 mb-4">
              <span class="text-sm font-black" :class="isDark ? 'text-amber-400' : 'text-slate-900'">
                ⭐ {{ avgRating.toFixed(1) }}
              </span>
              <span :class="isDark ? 'text-slate-500' : 'text-slate-600'" class="text-[11px] font-bold">
                ({{ reviews.length }} ulasan)
              </span>
            </div>

            <!-- Description -->
            <p :class="isDark ? 'text-slate-300' : 'text-slate-700'" class="text-sm font-medium leading-relaxed mb-6">
              {{ book.deskripsi || 'Tidak ada deskripsi untuk buku ini.' }}
            </p>
          </div>

          <!-- Price & Action Buttons -->
          <div :class="isDark ? 'border-slate-800' : 'border-t-2 border-[#1A1A1A]'" class="pt-5">
            <div class="mb-4">
              <span :class="isDark ? 'text-slate-500' : 'text-slate-500'" class="text-[10px] block uppercase font-black mb-1">
                Harga
              </span>
              <span class="text-2xl font-black" :class="isDark ? 'text-indigo-400' : 'text-slate-900'">
                Rp {{ formatNumber(book.harga_jual) }}
              </span>
            </div>

            <!-- Quantity & Add to Cart -->
            <div class="flex items-center gap-3 flex-wrap">
              <div class="flex items-center gap-2 bg-slate-200/50 dark:bg-slate-800 rounded-lg p-1">
                <button
                  @click="decrementQty"
                  :class="isDark ? 'bg-slate-800 text-white hover:bg-slate-700' : 'bg-white text-black hover:bg-slate-100 border border-black'"
                  class="w-8 h-8 rounded-md font-black text-sm transition-colors"
                >
                  −
                </button>
                <span :class="isDark ? 'text-white' : 'text-slate-900'" class="font-black text-sm w-6 text-center">
                  {{ qty }}
                </span>
                <button
                  @click="incrementQty"
                  :class="isDark ? 'bg-slate-800 text-white hover:bg-slate-700' : 'bg-white text-black hover:bg-slate-100 border border-black'"
                  class="w-8 h-8 rounded-md font-black text-sm transition-colors"
                >
                  +
                </button>
              </div>

              <button
                @click="handleAddToCart"
                :disabled="book.stok <= 0"
                :class="isDark ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-[#C8F53F] hover:bg-[#b8e82f] text-black border-2 border-black shadow-[2.5px_2.5px_0px_#1A1A1A] active:translate-x-[1px] active:translate-y-[1px]'"
                class="px-5 py-2.5 rounded-xl font-black text-xs transition-all flex items-center gap-1.5 disabled:opacity-50"
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
      <div :class="isDark ? 'border-slate-800' : 'border-[#1A1A1A]'" class="border-b-2 mb-8 sm:mb-12"></div>

      <!-- Reviews Section -->
      <div>
        <h2 :class="isDark ? 'text-white' : 'text-slate-900'" class="text-lg font-black mb-4">Ulasan Pembeli</h2>

        <!-- Reviews Loading -->
        <div
          v-if="reviewsLoading"
          :class="isDark ? 'text-slate-400' : 'text-slate-700 font-bold'"
          class="text-xs py-6 text-center"
        >
          Memuat ulasan...
        </div>

        <!-- No Reviews -->
        <div
          v-else-if="reviews.length === 0"
          :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#FFF8EC] border-2 border-[#1A1A1A]'"
          class="rounded-2xl p-6 text-center"
        >
          <p :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-bold">
            Belum ada ulasan untuk buku ini.
          </p>
        </div>

        <!-- Reviews List -->
        <div v-else class="space-y-4 max-w-3xl">
          <div
            v-for="rev in reviews"
            :key="rev.id"
            :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#FFF8EC] border-2 border-[#1A1A1A] shadow-[3px_3px_0px_#1A1A1A]'"
            class="rounded-2xl p-4 sm:p-5"
          >
            <div class="flex items-center justify-between mb-2">
              <span :class="isDark ? 'text-white' : 'text-slate-900'" class="font-black text-sm">
                {{ rev.nama_user }}
              </span>
              <span class="text-xs font-black" :class="isDark ? 'text-amber-400' : 'text-slate-900'">
                {{ '⭐'.repeat(rev.rating) }}
              </span>
            </div>
            <p :class="isDark ? 'text-slate-300' : 'text-slate-700'" class="text-xs sm:text-sm font-medium">
              {{ rev.komentar }}
            </p>
            <p :class="isDark ? 'text-slate-500' : 'text-slate-500'" class="text-[10px] font-bold mt-2">
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
