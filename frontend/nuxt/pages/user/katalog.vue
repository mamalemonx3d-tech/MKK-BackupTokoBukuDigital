<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
      <div>
        <h1 :class="isDark ? 'text-white' : 'text-slate-900 bg-[#FFE566] px-3 py-1 inline-block rounded-2xl border-2.5 border-black shadow-[4px_4px_0px_#1A1A1A]'" class="text-3xl font-black">Katalog Buku</h1>
        <p :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-bold mt-2">Cari judul buku favoritmu atau filter berdasarkan kategori</p>
      </div>

      <!-- Search & Filter Controls -->
      <div class="flex flex-wrap items-center gap-3">
        <div class="relative w-full sm:w-64">
          <input 
            v-model="searchQuery" 
            @input="fetchBooks" 
            type="text" 
            placeholder="Cari judul buku..." 
            :class="isDark ? 'bg-slate-900 border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A] focus:shadow-[4px_4px_0px_#1A1A1A]'"
            class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl outline-none font-bold transition-all" 
          />
          <span class="absolute left-3.5 top-2.5 text-sm text-slate-400">🔍</span>
        </div>

        <select 
          v-model="selectedCategory" 
          @change="fetchBooks" 
          :class="isDark ? 'bg-slate-900 border-slate-700 text-white' : 'bg-[#FFF8EC] border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A]'"
          class="px-3.5 py-2.5 text-xs rounded-xl outline-none font-bold transition-all"
        >
          <option value="">Semua Kategori</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id" :class="isDark ? 'bg-slate-900 text-white' : 'bg-white text-slate-900'">{{ cat.nama_kategori }}</option>
        </select>
      </div>
    </div>

    <!-- Books Grid -->
    <div v-if="loading" :class="isDark ? 'text-slate-400' : 'text-slate-700 font-black'" class="text-center py-20">Memuat buku...</div>
    <div v-else-if="books.length === 0" :class="isDark ? 'bg-slate-900 border-slate-800' : 'bg-[#FFF8EC] border-3 border-[#1A1A1A] shadow-[6px_6px_0px_#1A1A1A]'" class="text-center py-20 rounded-3xl">
      <div class="text-4xl mb-2">🔍</div>
      <h3 :class="isDark ? 'text-slate-200' : 'text-slate-900'" class="font-black text-lg">Buku tidak ditemukan</h3>
      <p :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-bold mt-1">Coba kata kunci pencarian atau kategori lain.</p>
    </div>
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div 
        v-for="book in books" 
        :key="book.id"
        @click="goToDetail(book)"
        :class="isDark ? 'bg-slate-900 border-slate-800 hover:border-slate-700 shadow-slate-950/50' : 'bg-[#FFF8EC] border-2.5 border-[#1A1A1A] shadow-[4px_4px_0px_#1A1A1A] hover:translate-x-[-2px] hover:translate-y-[-2px]'"
        class="rounded-2xl transition-all overflow-hidden flex flex-col justify-between group cursor-pointer"
      >
        <div>
          <div :class="isDark ? 'bg-slate-950' : 'bg-[#FAF7F0] border-b-2 border-[#1A1A1A]'" class="h-52 flex items-center justify-center overflow-hidden relative">
            <img v-if="book.gambar" :src="book.gambar" :alt="book.nama_buku" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
            <div v-else class="text-5xl">📕</div>
            <span :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'" class="absolute top-3 left-3 text-[10px] font-black px-2.5 py-1 rounded-full">
              {{ book.category_name || 'Buku' }}
            </span>
          </div>

          <div class="p-5">
            <h3 :class="isDark ? 'text-white group-hover:text-indigo-400' : 'text-slate-900'" class="font-black text-base mb-1 line-clamp-1 transition-colors">{{ book.nama_buku }}</h3>
            <p :class="isDark ? 'text-slate-400' : 'text-slate-600'" class="text-[11px] font-bold mb-2">Tahun Terbit: {{ book.tahun_terbit || '-' }} • Stok: <span :class="book.stok > 0 ? (isDark ? 'text-emerald-500' : 'text-slate-900 bg-[#C8F53F] px-1 rounded border border-black') : 'text-rose-600 font-black'">{{ book.stok }}</span></p>
            <p :class="isDark ? 'text-slate-300' : 'text-slate-700'" class="text-xs font-medium line-clamp-2 mb-4">{{ book.deskripsi || 'Tidak ada deskripsi' }}</p>
          </div>
        </div>

        <div :class="isDark ? 'border-slate-800' : 'border-t-2 border-[#1A1A1A]'" class="p-5 pt-0 flex items-center justify-between pt-4">
          <div>
            <span :class="isDark ? 'text-slate-500' : 'text-slate-500'" class="text-[10px] block uppercase font-black">Harga</span>
            <span class="text-base font-black" :class="isDark ? 'text-indigo-400' : 'text-slate-900'">Rp {{ formatNumber(book.harga_jual) }}</span>
          </div>

          <button 
            @click.stop="addToCart(book)" 
            :disabled="book.stok <= 0" 
            :class="isDark ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-[#C8F53F] hover:bg-[#b8e82f] text-black border-2 border-black shadow-[2.5px_2.5px_0px_#1A1A1A] active:translate-x-[1px] active:translate-y-[1px]'"
            class="px-4 py-2 rounded-xl font-black text-xs transition-all flex items-center gap-1.5 disabled:opacity-50"
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
const route = useRoute()
const router = useRouter()
const api = useApi()
const cartStore = useCartStore()
const { isDark } = useTheme()

const searchQuery = ref('')
const selectedCategory = ref<string | number>(route.query.category_id ? String(route.query.category_id) : '')
const categories = ref<any[]>([])
const books = ref<any[]>([])
const loading = ref(true)

const formatNumber = (val: number) => {
  return new Intl.NumberFormat('id-ID').format(val)
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
