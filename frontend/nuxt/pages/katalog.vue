<template>
  <div class="max-w-7xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border text-[10px] font-mono tracking-wider font-bold uppercase mb-2" :class="isDark ? 'border-zinc-700 bg-zinc-900 text-zinc-300' : 'border-black bg-zinc-100 text-black shadow-[2px_2px_0px_#000000]'">
          KATALOG LITERATUR
        </div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
          Katalog Buku
        </h1>
        <p class="text-xs font-medium mt-1" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
          Cari judul buku favorit Anda atau saring berdasarkan kategori terdaftar.
        </p>
      </div>

      <!-- Search & Filter Controls -->
      <div class="flex flex-wrap items-center gap-3">
        <div class="relative w-full sm:w-64">
          <input 
            v-model="searchQuery" 
            @input="fetchBooks" 
            type="text" 
            placeholder="Cari judul buku..." 
            :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[3px_3px_0px_#000000] placeholder-zinc-400'"
            class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl outline-none font-bold transition-all border-2" 
          />
          <span class="absolute left-3 top-2.5 text-xs opacity-50">🔍</span>
        </div>

        <select 
          v-model="selectedCategory" 
          @change="fetchBooks" 
          :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
          class="px-3.5 py-2.5 text-xs rounded-xl outline-none font-bold transition-all border-2 cursor-pointer"
        >
          <option value="">Semua Kategori</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">
            {{ cat.nama_kategori }}
          </option>
        </select>
      </div>
    </div>

    <!-- Books Grid -->
    <div v-if="loading" class="text-center py-20 font-bold text-xs" :class="isDark ? 'text-zinc-500' : 'text-zinc-500'">
      Memuat daftar buku...
    </div>

    <div 
      v-else-if="books.length === 0" 
      :class="isDark ? 'bg-zinc-900/60 border-zinc-800 text-zinc-400' : 'bg-white border-2 border-black text-zinc-600 shadow-[4px_4px_0px_#000000]'" 
      class="text-center py-20 rounded-3xl border-2"
    >
      <div class="text-4xl mb-2">🔍</div>
      <h3 :class="isDark ? 'text-white' : 'text-black'" class="font-black text-base">Buku tidak ditemukan</h3>
      <p class="text-xs font-medium mt-1">Coba kata kunci pencarian atau kategori lain.</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div 
        v-for="book in books" 
        :key="book.id"
        @click="goToDetail(book)"
        :class="isDark ? 'bg-zinc-900/80 border-zinc-800 hover:border-zinc-600 shadow-lg shadow-black/40' : 'bg-white border-2 border-black shadow-[4px_4px_0px_#000000] hover:shadow-[6px_6px_0px_#000000] hover:-translate-y-1'"
        class="rounded-2xl border-2 transition-all overflow-hidden flex flex-col justify-between group cursor-pointer"
      >
        <div>
          <!-- Cover Image Container -->
          <div :class="isDark ? 'bg-zinc-950 border-zinc-800' : 'bg-zinc-100 border-black'" class="h-52 flex items-center justify-center overflow-hidden relative border-b-2">
            <img 
              v-if="book.gambar" 
              :src="getImageUrl(book.gambar)" 
              :alt="book.nama_buku" 
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
              @error="book.gambar = null"
            />
            <div v-else class="text-4xl">📕</div>
            
            <span 
              :class="isDark ? 'bg-black/90 text-white border-zinc-700' : 'bg-black text-white border-black'" 
              class="absolute top-3 left-3 text-[10px] font-mono font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-md border"
            >
              {{ book.category_name || 'Buku' }}
            </span>
          </div>

          <div class="p-5">
            <h3 :class="isDark ? 'text-white' : 'text-black'" class="font-black text-sm mb-1 line-clamp-1 group-hover:underline transition-colors">
              {{ book.nama_buku }}
            </h3>
            <p :class="isDark ? 'text-zinc-400' : 'text-zinc-500'" class="text-[11px] font-bold mb-2">
              Tahun: {{ book.tahun_terbit || '-' }} • Stok: 
              <span :class="book.stok > 0 ? (isDark ? 'text-emerald-400' : 'text-black font-black') : 'text-rose-500 font-black'">
                {{ book.stok }}
              </span>
            </p>
            <p :class="isDark ? 'text-zinc-400' : 'text-zinc-600'" class="text-xs font-medium line-clamp-2 mb-2">
              {{ book.deskripsi || 'Tidak ada deskripsi untuk buku ini.' }}
            </p>
          </div>
        </div>

        <div :class="isDark ? 'border-zinc-800' : 'border-zinc-200'" class="p-5 pt-0 flex items-center justify-between border-t mt-2">
          <div>
            <span class="text-[9px] block uppercase font-mono font-bold tracking-wider text-zinc-400">Harga</span>
            <span class="text-sm font-black font-mono" :class="isDark ? 'text-white' : 'text-black'">
              Rp {{ formatNumber(book.harga_jual) }}
            </span>
          </div>

          <button 
            @click.stop="addToCart(book)" 
            :disabled="book.stok <= 0" 
            :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
            class="px-3.5 py-1.5 rounded-xl font-black text-xs transition-all flex items-center gap-1.5 border-2 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer hover:-translate-y-0.5 active:translate-y-0"
          >
            <span>🛒</span>
            <span v-if="book.stok > 0">Beli</span>
            <span v-else>Habis</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Katalog Buku - TokoBukuDigital'
})

const route = useRoute()
const router = useRouter()
const api = useApi()
const cartStore = useCartStore()
const { isDark } = useTheme()
const { getImageUrl } = useImageUrl()

const searchQuery = ref('')
const selectedCategory = ref<string | number>(route.query.category_id ? String(route.query.category_id) : '')
const categories = ref<any[]>([])
const books = ref<any[]>([])
const loading = ref(true)

const formatNumber = (val: number) => {
  return new Intl.NumberFormat('id-ID').format(val || 0)
}

const goToDetail = (book: any) => {
  router.push(`/books/${book.id}`)
}

const fetchBooks = async () => {
  loading.value = true
  try {
    const params: any = {}
    if (searchQuery.value) params.search = searchQuery.value
    if (selectedCategory.value) params.category_id = selectedCategory.value

    const res = await api.get('/api/books', { params })
    let data = res.data || []
    
    // Sort: stok > 0 first, then stok = 0 at the bottom
    data.sort((a: any, b: any) => {
      if (a.stok > 0 && b.stok <= 0) return -1
      if (a.stok <= 0 && b.stok > 0) return 1
      return 0
    })
    
    books.value = data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const addToCart = async (book: any) => {
  try {
    const toast = useToast()
    await cartStore.addToCart(book, 1)
    toast.success(`'${book.nama_buku}' ditambahkan ke keranjang.`)
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.message || 'Gagal menambahkan ke keranjang.')
  }
}

onMounted(async () => {
  try {
    const catRes = await api.get('/api/categories')
    categories.value = catRes.data || []
  } catch (e) {}

  await fetchBooks()
})
</script>
