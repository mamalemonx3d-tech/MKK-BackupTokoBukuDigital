<template>
  <div>
    <!-- Hero Section -->
    <section class="relative bg-[#FFF8EC] text-slate-900 py-20 px-4 sm:px-6 lg:px-8 border-2.5 border-[#1A1A1A] rounded-3xl shadow-[6px_6px_0px_#1A1A1A] overflow-hidden mb-12">
      <div class="max-w-7xl mx-auto relative z-10 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div>
          <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#C8F53F] text-slate-900 text-xs font-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A] mb-6">
            ✨ Platform Toko Buku Online Retro
          </span>
          <h1 class="text-4xl sm:text-5xl font-black tracking-tight leading-tight mb-6 text-slate-900">
            Temukan Buku Impianmu & Perluas Wawasanmu
          </h1>
          <p class="text-slate-800 text-base font-bold mb-8 leading-relaxed">
            Jelajahi ribuan koleksi buku fiksi, teknologi, bisnis, hingga sains dengan harga terbaik dan transaksi mudah.
          </p>
          <div class="flex flex-wrap gap-4">
            <NuxtLink to="/user/katalog" class="px-6 py-3.5 rounded-2xl font-black text-black bg-[#C8F53F] border-2.5 border-black shadow-[4px_4px_0px_#1A1A1A] hover:translate-x-[-2px] hover:translate-y-[-2px] active:translate-x-[2px] active:translate-y-[2px] transition-all">
              📚 Jelajahi Katalog Buku
            </NuxtLink>
            <NuxtLink to="/our-story" class="px-6 py-3.5 rounded-2xl font-black text-black bg-[#FFD4A3] border-2.5 border-black shadow-[4px_4px_0px_#1A1A1A] hover:translate-x-[-2px] hover:translate-y-[-2px] active:translate-x-[2px] active:translate-y-[2px] transition-all">
              Tentang Kami
            </NuxtLink>
          </div>
        </div>
        <div class="relative flex justify-center">
          <div class="w-72 h-96 bg-[#D4B8FF] border-3 border-black rounded-3xl shadow-[8px_8px_0px_#1A1A1A] rotate-2 hover:rotate-0 transition-all duration-300 flex flex-col justify-between p-8 text-slate-900">
            <div class="text-6xl">📖</div>
            <div>
              <h3 class="text-2xl font-black mb-2">BookStore Digital</h3>
              <p class="text-xs font-bold text-slate-800">Solusi belanja buku cepat, aman, dan mudah dari mana saja.</p>
            </div>
            <div class="text-xs font-black bg-[#C8F53F] border-2 border-black px-3 py-1.5 rounded-full w-max shadow-[2px_2px_0px_#1A1A1A]">Nuxt 3 + Laravel 12</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="max-w-7xl mx-auto px-2 py-8 mb-8">
      <div class="flex justify-between items-end mb-8">
        <div>
          <h2 class="text-2xl font-black text-slate-900 bg-[#FFE566] px-3 py-1 inline-block rounded-xl border-2 border-black shadow-[3px_3px_0px_#1A1A1A]">Kategori Pilihan</h2>
          <p class="text-xs font-bold text-slate-600 mt-2">Pilih kategori favoritmu untuk menemukan buku yang relevan</p>
        </div>
        <NuxtLink to="/user/katalog" class="text-xs font-black text-black bg-[#C8F53F] px-3 py-1.5 rounded-xl border-2 border-black shadow-[2px_2px_0px_#1A1A1A] hover:translate-x-[-1px] active:translate-x-[1px]">Lihat Semua &rarr;</NuxtLink>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
        <div 
          v-for="(cat, idx) in categories" 
          :key="cat.id" 
          @click="navigateToCatalog(cat.id)" 
          :class="[
            idx % 4 === 0 ? 'bg-[#FFF8EC]' : idx % 4 === 1 ? 'bg-[#D4B8FF]' : idx % 4 === 2 ? 'bg-[#FFD4A3]' : 'bg-[#A8E6CF]'
          ]"
          class="p-6 rounded-2xl border-2.5 border-black shadow-[4px_4px_0px_#1A1A1A] hover:translate-x-[-2px] hover:translate-y-[-2px] active:translate-x-[2px] active:translate-y-[2px] transition-all cursor-pointer group text-center"
        >
          <div class="w-12 h-12 rounded-xl bg-[#C8F53F] border-2 border-black text-slate-900 flex items-center justify-center text-2xl mx-auto mb-3 shadow-[2px_2px_0px_#1A1A1A] group-hover:scale-110 transition-transform">
            📁
          </div>
          <h3 class="font-black text-slate-900 text-sm">{{ cat.nama_kategori }}</h3>
        </div>
      </div>
    </section>

    <!-- Featured Books Section -->
    <section class="max-w-7xl mx-auto px-2 py-6">
      <h2 class="text-2xl font-black text-slate-900 bg-[#FFB7B2] px-3 py-1 inline-block rounded-xl border-2 border-black shadow-[3px_3px_0px_#1A1A1A] mb-8">Buku Terbaru</h2>
      
      <div v-if="loading" class="text-center py-12 font-black text-slate-600">Memuat buku...</div>
      <div v-else-if="books.length === 0" class="text-center py-12 font-black text-slate-600">Belum ada buku tersedia.</div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <div 
          v-for="book in books" 
          :key="book.id"
          @click="goToDetail(book)"
          class="bg-[#FFF8EC] rounded-2xl border-2.5 border-black shadow-[4px_4px_0px_#1A1A1A] hover:translate-x-[-2px] hover:translate-y-[-2px] transition-all overflow-hidden flex flex-col justify-between cursor-pointer group"
        >
          <div>
            <div class="h-48 bg-[#FAF7F0] border-b-2 border-black flex items-center justify-center overflow-hidden relative">
              <img v-if="book.gambar" :src="book.gambar" :alt="book.nama_buku" class="w-full h-full object-cover" />
              <div v-else class="text-5xl">📕</div>
              <span class="absolute top-3 left-3 bg-[#C8F53F] text-black border-2 border-black text-[10px] font-black px-2.5 py-1 rounded-full shadow-[2px_2px_0px_#1A1A1A]">
                {{ book.category_name || 'Buku' }}
              </span>
            </div>
            <div class="p-5">
              <h3 class="font-black text-slate-900 text-base mb-1 line-clamp-1">{{ book.nama_buku }}</h3>
              <p class="text-xs font-bold text-slate-500 mb-2">Terbit: {{ book.tahun_terbit || '-' }}</p>
              <p class="text-xs font-bold text-slate-500 mb-3">Stok: <span :class="book.stok > 0 ? 'text-slate-900 bg-[#C8F53F] px-1 rounded border border-black' : 'text-rose-600 font-black'">{{ book.stok }}</span></p>
              <p class="text-xs font-medium text-slate-700 line-clamp-2 mb-4">{{ book.deskripsi || 'Tidak ada deskripsi' }}</p>
            </div>
          </div>
          <div class="p-5 pt-0 flex items-center justify-between border-t-2 border-black pt-4">
            <div>
              <span class="text-[10px] font-black uppercase text-slate-500 block">Harga</span>
              <span class="text-base font-black text-black">Rp {{ formatNumber(book.harga_jual) }}</span>
            </div>
            <button 
              @click.stop="addToCart(book)" 
              :disabled="book.stok <= 0"
              :class="book.stok > 0 
                ? 'bg-[#C8F53F] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A] hover:translate-x-[-1px] hover:translate-y-[-1px] active:translate-x-[1px] active:translate-y-[1px]'
                : 'bg-slate-200 text-slate-500 border-2 border-slate-400 cursor-not-allowed'
              "
              class="px-4 py-2 rounded-xl font-black text-xs transition-all flex items-center gap-1.5 disabled:opacity-50"
            >
              <span>🛒</span> 
              <span v-if="book.stok > 0">Tambah</span>
              <span v-else>Stok Habis</span>
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
const router = useRouter()
const api = useApi()
const cartStore = useCartStore()

const categories = ref<any[]>([])
const books = ref<any[]>([])
const loading = ref(true)

const formatNumber = (val: number) => {
  return new Intl.NumberFormat('id-ID').format(val)
}

const navigateToCatalog = (catId: number) => {
  navigateTo(`/user/katalog?category_id=${catId}`)
}

const goToDetail = (book: any) => {
  router.push(`/books/${book.id}`)
}

const addToCart = (book: any) => {
  try {
    const toast = useToast()
    cartStore.addToCart(book, 1)
    toast.success(`'${book.nama_buku}' berhasil ditambahkan ke keranjang!`)
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.message || 'Gagal menambahkan ke keranjang')
  }
}

onMounted(async () => {
  try {
    const catRes = await api.get('/api/categories')
    categories.value = catRes.data || []
    
    const bookRes = await api.get('/api/books')
    let data = (bookRes.data || []).slice(0, 8) // Get first 8, then sort
    
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
})
</script>
