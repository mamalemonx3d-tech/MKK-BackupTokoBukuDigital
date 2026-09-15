<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

definePageMeta({
  layout: false
})

useHead({
  title: 'TokoBukuDigital'
})

const authStore = useAuthStore()
const cartStore = useCartStore()
const toast = useToast()
const api = useApi()
const { isDark, toggleTheme, initTheme } = useTheme()
const { getImageUrl } = useImageUrl()

// --- Navigation & Scroll Tracking ---
const activeSection = ref('hero')
const isScrolled = ref(false)
const isMobileMenuOpen = ref(false)

const scrollToSection = (id: string) => {
  activeSection.value = id
  isMobileMenuOpen.value = false
  const el = document.getElementById(id)
  if (el) {
    const yOffset = -85
    const y = el.getBoundingClientRect().top + window.pageYOffset + yOffset
    window.scrollTo({ top: y, behavior: 'smooth' })
  }
}

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20
  const isBottom = (window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - 60)
  if (isBottom) {
    activeSection.value = 'contact'
    return
  }

  const scrollPosition = window.scrollY + 140
  const sections = ['hero', 'katalog', 'story', 'blog', 'faq', 'contact']
  for (let i = sections.length - 1; i >= 0; i--) {
    const el = document.getElementById(sections[i])
    if (el && scrollPosition >= el.offsetTop) {
      activeSection.value = sections[i]
      break
    }
  }
}

// --- Interactive Book Catalog State ---
const selectedCategory = ref('Semua')
const searchQuery = ref('')
const selectedBookPreview = ref<any | null>(null)
const loadingBooks = ref(false)

const defaultCategories = ['Semua', 'Fiksi', 'Pengembangan Diri', 'Bisnis & Finansial', 'Teknologi']
const categoryList = ref<string[]>(defaultCategories)

const defaultBooks = [
  {
    id: 1,
    nama_buku: 'Filosofi Teras',
    penulis: 'Henry Manampiring',
    category_name: 'Pengembangan Diri',
    rating: 4.9,
    harga_jual: 98000,
    stok: 15,
    gambar: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&auto=format&fit=crop&q=80',
    deskripsi: 'Sebuah buku yang memperkenalkan filsafat Stoisisme klasik untuk membantu kita mengendalikan emosi negatif dan menemukan ketenangan sejati.',
    sampleContent: 'Bab 1: Menentukan Apa yang Bisa Ditolak dan Diterima. Hidup kita terbagi menjadi dua bagian mendasar: hal-hal yang berada di bawah kendali kita, dan hal-hal yang tidak di bawah kendali kita...'
  },
  {
    id: 2,
    nama_buku: 'Laskar Pelangi',
    penulis: 'Andrea Hirata',
    category_name: 'Fiksi',
    rating: 4.8,
    harga_jual: 89000,
    stok: 22,
    gambar: 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=600&auto=format&fit=crop&q=80',
    deskripsi: 'Kisah hangat dan menginspirasi tentang perjuangan sepuluh anak di Pulau Belitung dalam menempuh pendidikan dengan segala keterbatasan.',
    sampleContent: 'Pagi itu, saat sekolah hampir ditutup karena kekurangan murid, sosok Lintang berjalan kaki puluhan kilometer hanya demi sebuah harapan untuk bisa terus belajar...'
  },
  {
    id: 3,
    nama_buku: 'Psychology of Money',
    penulis: 'Morgan Housel',
    category_name: 'Bisnis & Finansial',
    rating: 4.9,
    harga_jual: 115000,
    stok: 18,
    gambar: 'https://images.unsplash.com/photo-1592496431122-2349e0fbc666?w=600&auto=format&fit=crop&q=80',
    deskripsi: '19 cerita pendek yang mengeksplorasi cara berpikir manusia mengenai uang dan mengajari Anda memahami perilaku finansial secara bijak.',
    sampleContent: 'Kunci kesuksesan finansial bukan semata-mata terletak pada seberapa pintarnya Anda menghitung angka, melainkan bagaimana Anda mengontrol emosi dan perilaku saat menghadapi risiko...'
  },
  {
    id: 4,
    nama_buku: 'Bumi Manusia',
    penulis: 'Pramoedya Ananta Toer',
    category_name: 'Fiksi',
    rating: 5.0,
    harga_jual: 135000,
    stok: 9,
    gambar: 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600&auto=format&fit=crop&q=80',
    deskripsi: 'Karya perintis monumental yang mengisahkan perjuangan Minke, seorang pribumi cerdas di era pergerakan nasional awal abad ke-20.',
    sampleContent: 'Orang boleh pandai setinggi langit, tapi selama ia tidak menulis, ia akan hilang di dalam masyarakat dan dari sejarah. Menulis adalah bekerja untuk keabadian...'
  },
  {
    id: 5,
    nama_buku: 'Atomic Habits',
    penulis: 'James Clear',
    category_name: 'Pengembangan Diri',
    rating: 4.9,
    harga_jual: 108000,
    stok: 30,
    gambar: 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=600&auto=format&fit=crop&q=80',
    deskripsi: 'Cara mudah dan terbukti untuk membangun kebiasaan baik dan menghilangkan kebiasaan buruk dengan perubahan kecil 1% setiap hari.',
    sampleContent: 'Perubahan kecil tidak tampak berarti pada awalnya, tetapi akumulasi kebiasaan 1% lebih baik setiap hari dari waktu ke waktu akan menghasilkan dampak luar biasa dalam hidup Anda...'
  },
  {
    id: 6,
    nama_buku: 'Clean Code & Architecture',
    penulis: 'Robert C. Martin',
    category_name: 'Teknologi',
    rating: 4.9,
    harga_jual: 165000,
    stok: 12,
    gambar: 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=600&auto=format&fit=crop&q=80',
    deskripsi: 'Panduan fundamental pengembangan perangkat lunak modern untuk menciptakan kode yang rapi, mudah dirawat, dan berkinerja tinggi.',
    sampleContent: 'Bahkan kode yang buruk pun bisa berfungsi. Namun jika kode tidak bersih, ia dapat membuat tim pengembangan lumpuh perlahan karena hutang teknis yang menumpuk...'
  }
]

const booksList = ref<any[]>(defaultBooks)
const favorites = ref<number[]>([])

const toggleFavorite = (bookId: number) => {
  if (favorites.value.includes(bookId)) {
    favorites.value = favorites.value.filter(id => id !== bookId)
  } else {
    favorites.value.push(bookId)
  }
}

const isBookFavorite = (bookId: number) => favorites.value.includes(bookId)

const filteredBooks = computed(() => {
  return booksList.value.filter(b => {
    const title = (b.nama_buku || b.title || '').toLowerCase()
    const author = (b.penulis || b.author || '').toLowerCase()
    const cat = b.category_name || b.category || ''
    const q = searchQuery.value.toLowerCase()

    const matchesCat = selectedCategory.value === 'Semua' || cat.toLowerCase() === selectedCategory.value.toLowerCase()
    const matchesSearch = title.includes(q) || author.includes(q)
    return matchesCat && matchesSearch
  })
})

const formatPrice = (val: number) => {
  return new Intl.NumberFormat('id-ID').format(val || 0)
}

const handleAddToCart = (book: any) => {
  try {
    cartStore.addToCart(book, 1)
    toast.success(`'${book.nama_buku || book.title}' dimasukkan ke keranjang!`)
  } catch (err: any) {
    toast.error(err.message || 'Gagal menambahkan ke keranjang')
  }
}

// --- Interactive FAQ Accordion ---
const openFaq = ref<number | null>(0)
const faqs = [
  {
    q: 'Apakah semua buku yang dijual di TokoBukuDigital original?',
    a: 'Ya, seluruh koleksi buku di TokoBukuDigital 100% original, bergaransi resmi, dan bersumber langsung dari penerbit terpercaya.'
  },
  {
    q: 'Bagaimana cara membaca sampel gratis?',
    a: 'Anda dapat langsung menekan tombol "Baca Sampel" pada kartu buku mana saja untuk membaca sinopsis lengkap beserta kutipan bab pertama secara gratis.'
  },
  {
    q: 'Bagaimana proses pengiriman dan pembayaran?',
    a: 'Kami mendukung berbagai metode pembayaran modern mulai dari QRIS, Transfer Bank, hingga E-Wallet. Pesanan buku akan dikemas secara aman dan dikirim ke seluruh Indonesia.'
  },
  {
    q: 'Apakah saya perlu mendaftar untuk melihat katalog?',
    a: 'Tidak. Anda bebas menjelajahi katalog dan membaca sampel buku secara gratis tanpa login. Namun untuk menyimpan riwayat pesanan dan checkout, Anda dapat membuat akun dengan cepat.'
  }
]

const toggleFaq = (idx: number) => {
  openFaq.value = openFaq.value === idx ? null : idx
}

// --- Lifecycle ---
onMounted(async () => {
  initTheme()
  window.addEventListener('scroll', handleScroll, { passive: true })
  handleScroll()

  if (!authStore.initialized) {
    await authStore.fetchUser()
  }

  // Fetch live categories and books from API if available
  try {
    loadingBooks.value = true
    const [catRes, bookRes] = await Promise.all([
      api.get('/api/categories').catch(() => null),
      api.get('/api/books').catch(() => null)
    ])

    if (catRes?.data && Array.isArray(catRes.data) && catRes.data.length > 0) {
      const liveCategories = catRes.data.map((c: any) => c.nama_kategori)
      categoryList.value = ['Semua', ...new Set(liveCategories)]
    }

    if (bookRes?.data && Array.isArray(bookRes.data) && bookRes.data.length > 0) {
      booksList.value = bookRes.data.map((b: any) => ({
        ...b,
        rating: b.rating || 4.9,
        sampleContent: b.deskripsi ? `${b.deskripsi.substring(0, 180)}...` : 'Kutipan bab sampel tersedia pada edisi lengkap buku ini.'
      }))
    }
  } catch (e) {
    // Keep fallback curated books
  } finally {
    loadingBooks.value = false
  }
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <div 
    class="min-h-screen transition-colors duration-300 font-sans selection:bg-black selection:text-white dark:selection:bg-white dark:selection:text-black overflow-x-clip"
    :class="isDark ? 'bg-[#09090b] text-[#f4f4f5]' : 'bg-[#fafafa] text-[#09090b]'"
  >
    <!-- TOP STICKY NAVBAR -->
    <header 
      class="sticky top-0 z-50 transition-all duration-300 px-4 sm:px-8 lg:px-12 border-b"
      :class="[
        isScrolled 
          ? (isDark ? 'py-3.5 bg-[#09090b]/90 backdrop-blur-md border-[#27272a] shadow-lg shadow-black/40' : 'py-3.5 bg-[#ffffff]/90 backdrop-blur-md border-[#e4e4e7] shadow-sm') 
          : (isDark ? 'py-5 bg-[#09090b] border-transparent' : 'py-5 bg-[#fafafa] border-transparent')
      ]"
    >
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        
        <!-- Logo (Vortexseries MONO Aesthetic) -->
        <div 
          @click="scrollToSection('hero')" 
          class="flex items-center gap-3 cursor-pointer group select-none"
        >
          <div 
            class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xl border-2 transition-transform duration-300 group-hover:scale-105"
            :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
          >
            B
          </div>
          <div class="flex flex-col">
            <span class="text-lg sm:text-xl font-black tracking-tight leading-none">
              TokoBuku<span :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">Digital</span>
            </span>
            <span class="text-[9px] font-mono tracking-widest uppercase font-bold text-zinc-500 mt-0.5">
              MONO EDITION
            </span>
          </div>
        </div>

        <!-- Center Nav Links (Desktop) -->
        <nav class="hidden lg:flex items-center space-x-8 text-xs sm:text-sm font-bold tracking-wide">
          <button 
            @click="scrollToSection('katalog')" 
            class="py-1 relative transition-colors duration-200 cursor-pointer"
            :class="activeSection === 'katalog' ? (isDark ? 'text-white' : 'text-black') : (isDark ? 'text-zinc-400 hover:text-white' : 'text-zinc-600 hover:text-black')"
          >
            <span>Katalog Buku</span>
            <span 
              class="absolute bottom-0 left-0 h-0.5 w-full transition-all duration-300"
              :class="activeSection === 'katalog' ? (isDark ? 'bg-white scale-x-100' : 'bg-black scale-x-100') : 'scale-x-0'"
            ></span>
          </button>

          <button 
            @click="scrollToSection('story')" 
            class="py-1 relative transition-colors duration-200 cursor-pointer"
            :class="activeSection === 'story' ? (isDark ? 'text-white' : 'text-black') : (isDark ? 'text-zinc-400 hover:text-white' : 'text-zinc-600 hover:text-black')"
          >
            <span>Our Story</span>
            <span 
              class="absolute bottom-0 left-0 h-0.5 w-full transition-all duration-300"
              :class="activeSection === 'story' ? (isDark ? 'bg-white scale-x-100' : 'bg-black scale-x-100') : 'scale-x-0'"
            ></span>
          </button>

          <button 
            @click="scrollToSection('blog')" 
            class="py-1 relative transition-colors duration-200 cursor-pointer"
            :class="activeSection === 'blog' ? (isDark ? 'text-white' : 'text-black') : (isDark ? 'text-zinc-400 hover:text-white' : 'text-zinc-600 hover:text-black')"
          >
            <span>Blog</span>
            <span 
              class="absolute bottom-0 left-0 h-0.5 w-full transition-all duration-300"
              :class="activeSection === 'blog' ? (isDark ? 'bg-white scale-x-100' : 'bg-black scale-x-100') : 'scale-x-0'"
            ></span>
          </button>

          <button 
            @click="scrollToSection('faq')" 
            class="py-1 relative transition-colors duration-200 cursor-pointer"
            :class="activeSection === 'faq' ? (isDark ? 'text-white' : 'text-black') : (isDark ? 'text-zinc-400 hover:text-white' : 'text-zinc-600 hover:text-black')"
          >
            <span>FAQ</span>
            <span 
              class="absolute bottom-0 left-0 h-0.5 w-full transition-all duration-300"
              :class="activeSection === 'faq' ? (isDark ? 'bg-white scale-x-100' : 'bg-black scale-x-100') : 'scale-x-0'"
            ></span>
          </button>

          <button 
            @click="scrollToSection('contact')" 
            class="py-1 relative transition-colors duration-200 cursor-pointer"
            :class="activeSection === 'contact' ? (isDark ? 'text-white' : 'text-black') : (isDark ? 'text-zinc-400 hover:text-white' : 'text-zinc-600 hover:text-black')"
          >
            <span>Kontak</span>
            <span 
              class="absolute bottom-0 left-0 h-0.5 w-full transition-all duration-300"
              :class="activeSection === 'contact' ? (isDark ? 'bg-white scale-x-100' : 'bg-black scale-x-100') : 'scale-x-0'"
            ></span>
          </button>
        </nav>

        <!-- Right Side: Theme Switcher & Auth Buttons -->
        <div class="hidden sm:flex items-center space-x-3">
          <!-- Reversible Theme Switcher (Icon Only) -->
          <button 
            @click="toggleTheme" 
            :title="isDark ? 'Ganti ke Tema Terang (Light Mode)' : 'Ganti ke Tema Gelap (Dark Mode)'"
            class="w-10 h-10 rounded-xl border-2 transition-all flex items-center justify-center cursor-pointer hover:scale-105 active:scale-95 text-base"
            :class="isDark ? 'bg-zinc-900 border-zinc-700 text-amber-300 hover:bg-zinc-800' : 'bg-white border-black text-zinc-900 hover:bg-zinc-100 shadow-[2px_2px_0px_#000000]'"
          >
            <span v-if="isDark" class="select-none">☀️</span>
            <span v-else class="select-none">🌙</span>
          </button>

          <!-- Authenticated State -->
          <template v-if="authStore.isAuthenticated">
            <NuxtLink 
              :to="authStore.isAdmin ? '/admin/buku' : '/katalog'"
              class="px-4 py-2 rounded-xl font-black text-xs sm:text-sm border-2 transition-all flex items-center gap-2 cursor-pointer hover:-translate-y-0.5"
              :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
            >
              <span>👤</span>
              <span class="max-w-[110px] truncate">{{ authStore.user?.name || 'Dasbor' }}</span>
              <span>&rarr;</span>
            </NuxtLink>
            <button 
              @click="authStore.logout()"
              class="px-3.5 py-2 rounded-xl font-bold text-xs border-2 transition-all cursor-pointer hover:bg-rose-600 hover:text-white hover:border-rose-600"
              :class="isDark ? 'border-zinc-700 text-zinc-400' : 'border-zinc-300 text-zinc-600'"
            >
              Logout
            </button>
          </template>

          <!-- Guest State -->
          <template v-else>
            <NuxtLink 
              to="/register" 
              class="px-4 py-2 rounded-xl font-bold text-xs sm:text-sm border-2 transition-all hover:-translate-y-0.5 cursor-pointer"
              :class="isDark ? 'bg-[#18181b] border-zinc-700 text-white hover:border-white' : 'bg-white border-black text-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-50'"
            >
              Daftar
            </NuxtLink>
            <NuxtLink 
              to="/login" 
              class="px-5 py-2 rounded-xl font-black text-xs sm:text-sm border-2 transition-all hover:-translate-y-0.5 cursor-pointer"
              :class="isDark ? 'bg-white border-white text-black shadow-[2px_2px_0px_#ffffff]' : 'bg-black border-black text-white shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
            >
              Masuk
            </NuxtLink>
          </template>
        </div>

        <!-- Mobile Hamburger Button -->
        <div class="flex items-center gap-2 lg:hidden">
          <button 
            @click="toggleTheme" 
            :title="isDark ? 'Tema Terang' : 'Tema Gelap'"
            class="w-10 h-10 rounded-xl border-2 flex items-center justify-center text-sm"
            :class="isDark ? 'border-zinc-700 bg-zinc-900 text-amber-300' : 'border-black bg-white text-zinc-900 shadow-[2px_2px_0px_#000000]'"
          >
            <span>{{ isDark ? '☀️' : '🌙' }}</span>
          </button>
          <button 
            @click="isMobileMenuOpen = !isMobileMenuOpen"
            class="w-10 h-10 rounded-xl border-2 flex flex-col items-center justify-center gap-1 cursor-pointer"
            :class="isDark ? 'border-zinc-700 bg-zinc-900 text-white' : 'border-black bg-white text-black shadow-[2px_2px_0px_#000000]'"
            aria-label="Toggle Navigation"
          >
            <span class="w-5 h-0.5 transition-all" :class="[isDark ? 'bg-white' : 'bg-black', isMobileMenuOpen ? 'rotate-45 translate-y-1.5' : '']"></span>
            <span class="w-5 h-0.5 transition-all" :class="[isDark ? 'bg-white' : 'bg-black', isMobileMenuOpen ? 'opacity-0' : '']"></span>
            <span class="w-5 h-0.5 transition-all" :class="[isDark ? 'bg-white' : 'bg-black', isMobileMenuOpen ? '-rotate-45 -translate-y-1.5' : '']"></span>
          </button>
        </div>
      </div>
    </header>

    <!-- Mobile Drawer Menu -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="-translate-y-4 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="-translate-y-4 opacity-0"
    >
      <div 
        v-if="isMobileMenuOpen" 
        class="fixed top-[70px] left-0 right-0 z-40 lg:hidden border-b-2 p-6 shadow-2xl space-y-4"
        :class="isDark ? 'bg-[#09090b] border-zinc-800 text-white' : 'bg-white border-black text-black'"
      >
        <div class="flex flex-col space-y-2 text-sm font-bold">
          <button @click="scrollToSection('katalog')" class="text-left py-2.5 px-3 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800">📚 Katalog Buku</button>
          <button @click="scrollToSection('story')" class="text-left py-2.5 px-3 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800">📖 Our Story</button>
          <button @click="scrollToSection('blog')" class="text-left py-2.5 px-3 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800">✍️ Blog</button>
          <button @click="scrollToSection('faq')" class="text-left py-2.5 px-3 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800">❓ FAQ</button>
          <button @click="scrollToSection('contact')" class="text-left py-2.5 px-3 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800">📍 Kontak</button>
        </div>

        <div class="pt-4 border-t" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
          <template v-if="authStore.isAuthenticated">
            <NuxtLink 
              :to="authStore.isAdmin ? '/admin/buku' : '/katalog'" 
              class="w-full py-3 rounded-xl font-black text-center block mb-2 border-2"
              :class="isDark ? 'bg-white text-black border-white' : 'bg-black text-white border-black'"
            >
              Masuk ke Dasbor ({{ authStore.user?.name }})
            </NuxtLink>
            <button 
              @click="authStore.logout(); isMobileMenuOpen = false"
              class="w-full py-2.5 rounded-xl font-bold text-rose-500 border border-rose-500/30"
            >
              Logout
            </button>
          </template>
          <template v-else>
            <div class="grid grid-cols-2 gap-3">
              <NuxtLink to="/register" class="py-3 rounded-xl font-bold text-center border-2" :class="isDark ? 'border-zinc-700 bg-zinc-900 text-white' : 'border-black bg-white text-black'">Daftar</NuxtLink>
              <NuxtLink to="/login" class="py-3 rounded-xl font-black text-center border-2" :class="isDark ? 'bg-white border-white text-black' : 'bg-black border-black text-white'">Masuk</NuxtLink>
            </div>
          </template>
        </div>
      </div>
    </Transition>

    <!-- MAIN CONTENT SECTIONS -->
    <main>
      <!-- HERO SECTION -->
      <section id="hero" class="relative px-4 sm:px-8 lg:px-12 py-16 sm:py-24 border-b transition-colors" :class="isDark ? 'border-zinc-800/80 bg-[#09090b]' : 'border-zinc-200 bg-[#ffffff]'">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          
          <div class="lg:col-span-7 space-y-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border text-[11px] font-mono font-black uppercase tracking-widest" :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-300' : 'bg-zinc-100 border-black text-black shadow-[2px_2px_0px_#000000]'">
              TOKO BUKU DIGITAL
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.15]">
              Buka Cakrawala Baru Lewat Lembar Buku Bermakna.
            </h1>

            <p class="text-base sm:text-lg font-medium leading-relaxed max-w-xl" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
              Temukan ribuan karya literatur kurasi terbaik dari berbagai genre. Pengalaman belanja buku cepat, estetika monochrome modern, dan akses sampel instan.
            </p>

            <div class="flex flex-wrap gap-4 pt-2">
              <button 
                @click="scrollToSection('katalog')" 
                class="px-7 py-3.5 rounded-xl font-black text-sm sm:text-base border-2 transition-all hover:-translate-y-1 cursor-pointer"
                :class="isDark ? 'bg-white text-black border-white shadow-[4px_4px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[4px_4px_0px_#000000]'"
              >
                📚 Jelajahi Katalog
              </button>
              <button 
                @click="scrollToSection('story')" 
                class="px-7 py-3.5 rounded-xl font-bold text-sm sm:text-base border-2 transition-all hover:-translate-y-0.5 cursor-pointer"
                :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white hover:border-white' : 'bg-white border-black text-black shadow-[3px_3px_0px_#000000] hover:bg-zinc-100'"
              >
                Tentang Kami
              </button>
            </div>

            <div class="flex items-center gap-6 pt-4 text-xs font-mono" :class="isDark ? 'text-zinc-400' : 'text-zinc-500'">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" :class="isDark ? 'bg-white' : 'bg-black'"></span>
                <span>100% Original Publisher</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" :class="isDark ? 'bg-white' : 'bg-black'"></span>
                <span>Instant Sample Preview</span>
              </div>
            </div>
          </div>

          <!-- Hero Right Preview Card -->
          <div class="lg:col-span-5 flex justify-center">
            <div 
              class="w-full max-w-md rounded-3xl p-6 sm:p-7 border-2 transition-all duration-300 relative group"
              :class="isDark ? 'bg-zinc-900/90 border-zinc-700 shadow-2xl shadow-black' : 'bg-white border-black shadow-[8px_8px_0px_#000000]'"
            >
              <div class="flex items-center justify-between mb-4">
                <span class="text-[11px] font-mono font-black uppercase tracking-wider px-2.5 py-1 rounded-md border" :class="isDark ? 'bg-black border-zinc-700 text-zinc-300' : 'bg-zinc-100 border-black text-black'">
                  ★ FEATURED BOOK
                </span>
                <span class="text-xs font-black font-mono">Rating: 4.9 / 5.0</span>
              </div>

              <!-- Card Image -->
              <div class="h-52 rounded-2xl overflow-hidden border-2 mb-4 relative" :class="isDark ? 'border-zinc-800 bg-zinc-950' : 'border-black bg-zinc-100'">
                <img :src="getImageUrl(booksList[0]?.gambar) || defaultBooks[0].gambar" :alt="booksList[0]?.nama_buku || 'Buku'" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-4 text-white">
                  <span class="text-[10px] font-mono font-black uppercase tracking-widest text-zinc-300">{{ booksList[0]?.category_name || 'Buku Pilihan' }}</span>
                  <h3 class="text-lg font-black leading-tight">{{ booksList[0]?.nama_buku || defaultBooks[0].nama_buku }}</h3>
                  <p class="text-xs text-zinc-300 font-medium">Oleh {{ booksList[0]?.penulis || defaultBooks[0].penulis }}</p>
                </div>
              </div>

              <p class="text-xs leading-relaxed mb-5" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
                {{ booksList[0]?.deskripsi || defaultBooks[0].deskripsi }}
              </p>

              <button 
                @click="selectedBookPreview = booksList[0] || defaultBooks[0]"
                class="w-full py-3 rounded-xl font-black text-xs sm:text-sm border-2 transition-all flex items-center justify-center gap-2 cursor-pointer hover:-translate-y-0.5"
                :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[3px_3px_0px_#000000]'"
              >
                <span>📖</span>
                <span>Baca Sampel Gratis Sekarang</span>
              </button>
            </div>
          </div>

        </div>
      </section>

      <!-- INTERACTIVE CATALOG SECTION -->
      <section id="katalog" class="px-4 sm:px-8 lg:px-12 py-16 sm:py-24 max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
          <div>
            <div class="inline-block px-3 py-1 rounded-lg border font-mono text-[11px] font-black uppercase mb-2" :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-300' : 'bg-black text-white border-black'">
              EXPLORE CATALOG
            </div>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight">Katalog Buku Pilihan</h2>
            <p class="text-xs sm:text-sm font-medium mt-1" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
              Temukan bacaan berkualitas dan nikmati pratinjau sampel setiap buku.
            </p>
          </div>

          <NuxtLink 
            to="/katalog" 
            class="px-5 py-2.5 rounded-xl font-black text-xs border-2 transition-all flex items-center gap-2 self-start md:self-auto cursor-pointer hover:-translate-y-0.5"
            :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white hover:border-white' : 'bg-white border-black text-black shadow-[3px_3px_0px_#000000] hover:bg-zinc-100'"
          >
            <span>Buka Katalog Lengkap</span>
            <span class="text-base">&rarr;</span>
          </NuxtLink>
        </div>

        <!-- Filter Category Tabs & Search Bar -->
        <div class="flex flex-col md:flex-row gap-4 items-stretch md:items-center justify-between mb-8">
          <!-- Categories Scroll -->
          <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 scrollbar-none">
            <button
              v-for="cat in categoryList"
              :key="cat"
              @click="selectedCategory = cat"
              class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all border-2 cursor-pointer"
              :class="selectedCategory === cat 
                ? (isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]') 
                : (isDark ? 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:border-zinc-700' : 'bg-white text-zinc-700 border-zinc-300 hover:border-black')"
            >
              {{ cat }}
            </button>
          </div>

          <!-- Search Input -->
          <div class="relative min-w-[260px]">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Cari judul buku atau penulis..." 
              class="w-full pl-10 pr-4 py-2.5 rounded-xl text-xs font-medium border-2 outline-none transition-all"
              :class="isDark ? 'bg-zinc-900 border-zinc-800 text-white placeholder-zinc-500 focus:border-zinc-600' : 'bg-white border-black text-black placeholder-zinc-400 shadow-[2px_2px_0px_#000000]'"
            />
            <span class="absolute left-3.5 top-2.5 text-sm">🔍</span>
          </div>
        </div>

        <!-- Book Cards Grid -->
        <div v-if="filteredCatalogBooks.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div 
            v-for="book in filteredCatalogBooks" 
            :key="book.id"
            class="rounded-3xl border-2 p-5 transition-all duration-300 flex flex-col justify-between group relative overflow-hidden"
            :class="isDark ? 'bg-zinc-900/60 border-zinc-800 hover:border-zinc-600' : 'bg-white border-black shadow-[4px_4px_0px_#000000] hover:shadow-[6px_6px_0px_#000000] hover:-translate-y-1'"
          >
            <!-- Favorite Badge Button -->
            <button 
              @click="toggleFavorite(book.id)"
              class="absolute top-8 right-8 z-10 w-8 h-8 rounded-full border flex items-center justify-center cursor-pointer transition-transform hover:scale-110 active:scale-95 shadow-sm"
              :class="isDark ? 'bg-zinc-900/90 border-zinc-700 text-white' : 'bg-white border-black text-black shadow-[1.5px_1.5px_0px_#000000]'"
              title="Simpan ke Favorit"
            >
              <span class="text-sm">{{ isBookFavorite(book.id) ? '❤️' : '🤍' }}</span>
            </button>

            <div>
              <!-- Cover Image -->
              <div class="h-48 rounded-2xl overflow-hidden border-2 mb-4 relative" :class="isDark ? 'border-zinc-800 bg-zinc-950' : 'border-black bg-zinc-100'">
                <img 
                  v-if="book.gambar" 
                  :src="getImageUrl(book.gambar)" 
                  :alt="book.nama_buku" 
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                  @error="book.gambar = null"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-4xl">
                  📕
                </div>
                <div class="absolute top-3 left-3">
                  <span class="text-[10px] font-mono font-bold uppercase tracking-wider px-2 py-0.5 rounded border" :class="isDark ? 'bg-black/90 text-white border-zinc-700' : 'bg-black text-white border-black'">
                    {{ book.category_name || 'Buku' }}
                  </span>
                </div>
              </div>

              <!-- Meta & Title -->
              <div class="flex items-center justify-between mb-1.5">
                <h3 class="font-black text-base line-clamp-1 group-hover:underline">
                  {{ book.nama_buku || book.title }}
                </h3>
                <span class="text-xs font-mono font-bold shrink-0 ml-2" :class="isDark ? 'text-zinc-300' : 'text-zinc-700'">
                  ★ {{ book.rating || 4.9 }}
                </span>
              </div>

              <p class="text-xs font-bold mb-2" :class="isDark ? 'text-zinc-400' : 'text-zinc-500'">
                Oleh {{ book.penulis || book.author || 'Penulis' }}
              </p>

              <p class="text-xs line-clamp-2 leading-relaxed mb-5" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
                {{ book.deskripsi || book.synopsis || 'Tidak ada ringkasan deskripsi untuk buku ini.' }}
              </p>
            </div>

            <!-- Price & Actions -->
            <div class="pt-4 border-t" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
              <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-mono font-bold uppercase" :class="isDark ? 'text-zinc-500' : 'text-zinc-400'">Harga</span>
                <span class="text-base font-black font-mono">Rp {{ formatPrice(book.harga_jual || 0) }}</span>
              </div>

              <div class="grid grid-cols-2 gap-2">
                <button 
                  @click="selectedBookPreview = book"
                  class="py-2.5 rounded-xl font-bold text-xs border-2 transition-all flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-90"
                  :class="isDark ? 'border-zinc-700 text-white bg-zinc-900 hover:bg-zinc-800' : 'border-black text-black bg-zinc-100 hover:bg-zinc-200'"
                >
                  <span>👁️</span>
                  <span>Sampel</span>
                </button>
                <button 
                  @click="handleAddToCart(book)"
                  class="py-2.5 rounded-xl font-black text-xs border-2 transition-all flex items-center justify-center gap-1.5 cursor-pointer hover:-translate-y-0.5"
                  :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
                >
                  <span>🛒</span>
                  <span>Beli</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div 
          v-else 
          class="text-center py-16 rounded-3xl border-2"
          :class="isDark ? 'bg-zinc-900/40 border-zinc-800 text-zinc-400' : 'bg-white border-black text-zinc-600 shadow-[4px_4px_0px_#000000]'"
        >
          <div class="text-4xl mb-3">🔍</div>
          <p class="text-base font-bold">Tidak ada buku yang cocok dengan pencarian "{{ searchQuery }}".</p>
          <button 
            @click="searchQuery = ''; selectedCategory = 'Semua'"
            class="mt-3 text-xs font-mono font-bold uppercase underline cursor-pointer hover:opacity-80"
          >
            Reset Filter Pencarian
          </button>
        </div>
      </section>

      <!-- OUR STORY SECTION -->
      <section id="story" class="px-4 sm:px-8 lg:px-12 py-16 sm:py-24 border-t border-b transition-colors" :class="isDark ? 'bg-[#0e0e11] border-zinc-800' : 'bg-[#f4f4f5] border-zinc-200'">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          
          <div class="lg:col-span-7 space-y-5">
            <span class="text-xs font-mono font-black uppercase tracking-widest" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
              TENTANG KAMI • OUR STORY
            </span>
            <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight">
              Membangun Budaya Literasi dengan Sentuhan Modern.
            </h2>
            <p class="text-sm sm:text-base leading-relaxed" :class="isDark ? 'text-zinc-300' : 'text-zinc-700'">
              TokoBukuDigital lahir dari kecintaan terhadap kekuatan kata dan cerita yang mampu mengubah perspektif. Kami berkomitmen menyajikan kurasi buku berkualitas tinggi dari penerbit terpercaya ke tangan pembaca di seluruh nusantara.
            </p>
            <p class="text-sm sm:text-base leading-relaxed" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
              Melalui platform digital yang sederhana, cepat, dan presisi, kami ingin memastikan setiap pembaca dapat menemukan buku impian mereka tanpa hambatan.
            </p>
          </div>

          <div class="lg:col-span-5 grid grid-cols-2 gap-4">
            <div 
              class="p-6 rounded-3xl border-2 text-center transition-all duration-300 hover:-translate-y-1"
              :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white' : 'bg-white border-black text-black shadow-[4px_4px_0px_#000000]'"
            >
              <div class="text-3xl sm:text-4xl font-black font-mono mb-1">10,000+</div>
              <p class="text-xs font-bold" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">Judul Terpilih</p>
            </div>

            <div 
              class="p-6 rounded-3xl border-2 text-center transition-all duration-300 hover:-translate-y-1"
              :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white' : 'bg-white border-black text-black shadow-[4px_4px_0px_#000000]'"
            >
              <div class="text-3xl sm:text-4xl font-black font-mono mb-1">50,000+</div>
              <p class="text-xs font-bold" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">Pembaca Aktif</p>
            </div>

            <div 
              class="p-6 rounded-3xl border-2 text-center transition-all duration-300 hover:-translate-y-1"
              :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white' : 'bg-white border-black text-black shadow-[4px_4px_0px_#000000]'"
            >
              <div class="text-3xl sm:text-4xl font-black font-mono mb-1">100%</div>
              <p class="text-xs font-bold" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">Buku Original</p>
            </div>

            <div 
              class="p-6 rounded-3xl border-2 text-center transition-all duration-300 hover:-translate-y-1"
              :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white' : 'bg-white border-black text-black shadow-[4px_4px_0px_#000000]'"
            >
              <div class="text-3xl sm:text-4xl font-black font-mono mb-1">4.9 ★</div>
              <p class="text-xs font-bold" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">Kepuasan Pembaca</p>
            </div>
          </div>

        </div>
      </section>

      <!-- BLOG SECTION -->
      <section id="blog" class="px-4 sm:px-8 lg:px-12 py-16 sm:py-24 max-w-7xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-12">
          <div class="inline-block px-3 py-1 rounded-lg border font-mono text-[11px] font-black uppercase mb-2" :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-300' : 'bg-black text-white border-black'">
            ARTIKEL & WAWASAN
          </div>
          <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight">Blog & Catatan Literasi</h2>
          <p class="text-xs sm:text-sm font-medium mt-1" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
            Tips membaca produktif, ulasan mendalam, dan inspirasi seputar dunia buku.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
          <article 
            class="rounded-3xl p-6 border-2 transition-all duration-300 flex flex-col justify-between group"
            :class="isDark ? 'bg-zinc-900/60 border-zinc-800 hover:border-zinc-600 hover:-translate-y-1' : 'bg-white border-black shadow-[4px_4px_0px_#000000] hover:shadow-[6px_6px_0px_#000000] hover:-translate-y-1'"
          >
            <div>
              <span class="text-[10px] font-mono font-black uppercase tracking-wider" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">TIPS MEMBACA</span>
              <h3 class="text-lg font-black my-2 group-hover:underline">Membangun Sudut Baca Minimalis di Rumah</h3>
              <p class="text-xs leading-relaxed mb-4" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
                Langkah sederhana merancang suasana membaca yang tenang, fokus, dan bebas distraksi untuk akhir pekan Anda.
              </p>
            </div>
            <span class="text-xs font-black font-mono flex items-center gap-1 group-hover:translate-x-1 transition-transform">
              Baca Artikel &rarr;
            </span>
          </article>

          <article 
            class="rounded-3xl p-6 border-2 transition-all duration-300 flex flex-col justify-between group"
            :class="isDark ? 'bg-zinc-900/60 border-zinc-800 hover:border-zinc-600 hover:-translate-y-1' : 'bg-white border-black shadow-[4px_4px_0px_#000000] hover:shadow-[6px_6px_0px_#000000] hover:-translate-y-1'"
          >
            <div>
              <span class="text-[10px] font-mono font-black uppercase tracking-wider" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">ULASAN BUKU</span>
              <h3 class="text-lg font-black my-2 group-hover:underline">Pelajaran Hidup Berharga dari 'Atomic Habits'</h3>
              <p class="text-xs leading-relaxed mb-4" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
                Bagaimana perbaikan mikro 1% setiap hari dapat mengubah karir, finansial, dan kesehatan mental Anda secara permanen.
              </p>
            </div>
            <span class="text-xs font-black font-mono flex items-center gap-1 group-hover:translate-x-1 transition-transform">
              Baca Artikel &rarr;
            </span>
          </article>

          <article 
            class="rounded-3xl p-6 border-2 transition-all duration-300 flex flex-col justify-between group"
            :class="isDark ? 'bg-zinc-900/60 border-zinc-800 hover:border-zinc-600 hover:-translate-y-1' : 'bg-white border-black shadow-[4px_4px_0px_#000000] hover:shadow-[6px_6px_0px_#000000] hover:-translate-y-1'"
          >
            <div>
              <span class="text-[10px] font-mono font-black uppercase tracking-wider" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">REKOMENDASI</span>
              <h3 class="text-lg font-black my-2 group-hover:underline">5 Buku Finansial Wajib untuk Pemula</h3>
              <p class="text-xs leading-relaxed mb-4" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
                Panduan memilih buku literasi keuangan yang aplikatif dan mudah dipahami tanpa bahasa teknis rumit.
              </p>
            </div>
            <span class="text-xs font-black font-mono flex items-center gap-1 group-hover:translate-x-1 transition-transform">
              Baca Artikel &rarr;
            </span>
          </article>
        </div>
      </section>

      <!-- FAQ SECTION -->
      <section id="faq" class="px-4 sm:px-8 lg:px-12 py-16 sm:py-24 border-t transition-colors" :class="isDark ? 'bg-[#0e0e11] border-zinc-800' : 'bg-[#f4f4f5] border-zinc-200'">
        <div class="max-w-3xl mx-auto">
          <div class="text-center mb-12">
            <div class="inline-block px-3 py-1 rounded-lg border font-mono text-[11px] font-black uppercase mb-2" :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-300' : 'bg-black text-white border-black'">
              PERTANYAAN UMUM
            </div>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight">Frequently Asked Questions</h2>
            <p class="text-xs sm:text-sm font-medium mt-1" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
              Jawaban cepat untuk pertanyaan yang sering diajukan pengunjung.
            </p>
          </div>

          <div class="space-y-4">
            <div 
              v-for="(faq, idx) in faqs" 
              :key="idx"
              @click="toggleFaq(idx)"
              class="rounded-2xl p-5 sm:p-6 border-2 transition-all duration-300 cursor-pointer select-none"
              :class="isDark ? 'bg-zinc-900 border-zinc-800 hover:border-zinc-600' : 'bg-white border-black shadow-[3px_3px_0px_#000000] hover:shadow-[4px_4px_0px_#000000]'"
            >
              <div class="flex items-center justify-between gap-4">
                <h3 class="text-sm sm:text-base font-black">{{ faq.q }}</h3>
                <div 
                  class="w-7 h-7 rounded-lg border-2 flex items-center justify-center font-mono font-bold text-xs shrink-0 transition-transform duration-300"
                  :class="[
                    isDark ? 'bg-black border-zinc-700 text-white' : 'bg-zinc-100 border-black text-black',
                    openFaq === idx ? 'rotate-45' : ''
                  ]"
                >
                  +
                </div>
              </div>

              <p 
                v-if="openFaq === idx" 
                class="mt-3.5 pt-3.5 border-t text-xs sm:text-sm leading-relaxed transition-all"
                :class="isDark ? 'border-zinc-800 text-zinc-400' : 'border-zinc-200 text-zinc-600'"
              >
                {{ faq.a }}
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- CONTACT & FOOTER SECTION -->
      <footer id="contact" class="px-4 sm:px-8 lg:px-12 py-16 border-t" :class="isDark ? 'bg-black border-zinc-800 text-zinc-400' : 'bg-[#111111] border-black text-zinc-400'">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
          
          <div class="md:col-span-2 space-y-4">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-white text-black font-black flex items-center justify-center text-base">
                B
              </div>
              <span class="text-xl font-black text-white tracking-tight">TokoBukuDigital</span>
            </div>
            <p class="text-xs sm:text-sm leading-relaxed max-w-sm">
              Platform belanja buku online modern dengan estetika monochrome dan kurasi karya sastra serta edukasi terbaik.
            </p>
          </div>

          <div>
            <h4 class="text-xs font-mono font-black uppercase text-white tracking-wider mb-4">Kontak Layanan</h4>
            <p class="text-xs mb-2">Email: halo@tokobukudigital.id</p>
            <p class="text-xs mb-2">WhatsApp: +62 812-3456-7890</p>
            <p class="text-xs">Jam Layanan: 08.00 - 21.00 WIB</p>
          </div>

          <div>
            <h4 class="text-xs font-mono font-black uppercase text-white tracking-wider mb-4">Alamat Galeri</h4>
            <p class="text-xs leading-relaxed">
              Jl. Literasi No. 42, Senopati, Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12190
            </p>
          </div>

        </div>

        <div class="max-w-7xl mx-auto pt-8 border-t border-zinc-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-mono">
          <p>© 2026 TokoBukuDigital. All rights reserved.</p>
          <div class="flex items-center gap-4">
            <button @click="scrollToSection('hero')" class="hover:text-white cursor-pointer">&uarr; Kembali ke Atas</button>
          </div>
        </div>
      </footer>
    </main>

    <!-- INTERACTIVE BOOK SAMPLE PREVIEW MODAL -->
    <div 
      v-if="selectedBookPreview" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/75 backdrop-blur-sm"
      @click.self="selectedBookPreview = null"
    >
      <div 
        class="w-full max-w-xl rounded-3xl p-6 sm:p-8 border-2 relative max-h-[90vh] overflow-y-auto shadow-2xl"
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white' : 'bg-white border-black text-black shadow-[8px_8px_0px_#000000]'"
      >
        <button 
          @click="selectedBookPreview = null"
          class="absolute top-5 right-5 w-8 h-8 rounded-full border-2 flex items-center justify-center font-mono font-bold text-xs cursor-pointer hover:scale-110"
          :class="isDark ? 'border-zinc-700 bg-black text-white' : 'border-black bg-zinc-100 text-black'"
        >
          ✕
        </button>

        <div class="h-48 sm:h-56 rounded-2xl overflow-hidden border-2 mb-5" :class="isDark ? 'border-zinc-800' : 'border-black'">
          <img :src="getImageUrl(selectedBookPreview.gambar) || defaultBooks[0].gambar" :alt="selectedBookPreview.nama_buku" class="w-full h-full object-cover" />
        </div>

        <span class="inline-block px-2.5 py-1 rounded-md text-[10px] font-mono font-black uppercase mb-2 border" :class="isDark ? 'bg-black border-zinc-700 text-zinc-300' : 'bg-black text-white border-black'">
          {{ selectedBookPreview.category_name || 'Buku Pilihan' }}
        </span>

        <h3 class="text-xl sm:text-2xl font-black mb-1 leading-tight">{{ selectedBookPreview.nama_buku || selectedBookPreview.title }}</h3>
        <p class="text-xs font-bold mb-4" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
          Oleh {{ selectedBookPreview.penulis || selectedBookPreview.author || 'Penulis' }}
        </p>

        <div class="p-4 rounded-2xl border mb-4" :class="isDark ? 'bg-black/50 border-zinc-800' : 'bg-zinc-50 border-zinc-200'">
          <h4 class="text-[11px] font-mono font-black uppercase tracking-wider mb-1" :class="isDark ? 'text-zinc-400' : 'text-zinc-700'">Sinopsis Buku</h4>
          <p class="text-xs sm:text-sm leading-relaxed" :class="isDark ? 'text-zinc-300' : 'text-zinc-800'">
            {{ selectedBookPreview.deskripsi || selectedBookPreview.synopsis || 'Sinopsis buku ini sedang diperbarui.' }}
          </p>
        </div>

        <div class="p-4 rounded-2xl border mb-6" :class="isDark ? 'bg-black/50 border-zinc-800' : 'bg-zinc-50 border-zinc-200'">
          <h4 class="text-[11px] font-mono font-black uppercase tracking-wider mb-1" :class="isDark ? 'text-zinc-400' : 'text-zinc-700'">Kutipan Sampel Gratis</h4>
          <p class="text-xs sm:text-sm italic leading-relaxed" :class="isDark ? 'text-zinc-300' : 'text-zinc-800'">
            "{{ selectedBookPreview.sampleContent || defaultBooks[0].sampleContent }}"
          </p>
        </div>

        <div class="flex items-center justify-between gap-4 pt-2">
          <div>
            <span class="text-[10px] font-mono uppercase block" :class="isDark ? 'text-zinc-500' : 'text-zinc-400'">Harga Satuan</span>
            <span class="text-lg font-black font-mono">Rp {{ formatPrice(selectedBookPreview.harga_jual || 0) }}</span>
          </div>

          <button 
            @click="handleAddToCart(selectedBookPreview); selectedBookPreview = null"
            class="px-6 py-3 rounded-xl font-black text-xs sm:text-sm border-2 transition-all hover:-translate-y-0.5 cursor-pointer"
            :class="isDark ? 'bg-white text-black border-white shadow-[3px_3px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[3px_3px_0px_#000000]'"
          >
            🛒 Tambah ke Keranjang
          </button>
        </div>
      </div>
    </div>

    <!-- Floating Chat Widget & Toast -->
    <FloatingChat />
    <Toast />
  </div>
</template>
