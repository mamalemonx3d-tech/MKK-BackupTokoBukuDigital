<template>
  <div class="max-w-4xl mx-auto space-y-8">
    <!-- Header Scan / Cari Pesanan untuk User -->
    <div 
      :class="isDark ? 'bg-slate-900 border-slate-800 text-slate-100' : 'bg-[#FFF8EC] border-3 border-[#1A1A1A] shadow-[6px_6px_0px_#1A1A1A] text-slate-900'"
      class="p-5 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-4 transition-colors duration-300"
    >
      <div class="flex items-center gap-2 w-full md:w-auto flex-1">
        <div class="relative w-full max-w-md">
          <input 
            v-model="scanQuery" 
            type="text" 
            placeholder="Scan QR Code / Masukkan Kode (A021)..." 
            :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500 focus:bg-slate-900' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A] focus:shadow-[4px_4px_0px_#1A1A1A]'"
            class="w-full pl-10 pr-4 py-3 rounded-full text-sm font-black outline-none transition-colors"
            @keyup.enter="handleUserScanSubmit"
          />
          <LucideQrCode class="w-5 h-5 text-slate-900 absolute left-3.5 top-3.5" />
        </div>
        <button 
          @click="startCameraScanner" 
          :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[3px_3px_0px_#1A1A1A] active:translate-x-[1px] active:translate-y-[1px]'"
          class="px-4 py-3 rounded-full font-black text-xs flex items-center gap-1.5 whitespace-nowrap transition-transform"
        >
          📷 Scan Kamera HP
        </button>
      </div>

      <div 
        :class="isDark ? 'bg-slate-950 border-slate-800 text-slate-300' : 'bg-[#FFE566] border-2 border-black shadow-[2px_2px_0px_#1A1A1A] text-slate-900'"
        class="text-xs font-black uppercase px-3.5 py-2 rounded-xl"
      >
        Total: {{ orders.length }} Pesanan
      </div>
    </div>

    <!-- Modal Scanner Kamera HP / Web -->
    <div v-if="showCameraModal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center p-4">
      <div 
        :class="isDark ? 'bg-slate-900 border-slate-700 text-white' : 'bg-[#FFF8EC] border-4 border-black text-slate-900 shadow-[8px_8px_0px_#1A1A1A]'"
        class="rounded-3xl p-6 max-w-md w-full space-y-4 text-center relative overflow-hidden"
      >
        <div class="flex justify-between items-center border-b-2 border-black pb-3">
          <h3 class="font-black text-lg flex items-center gap-2">
            📷 Pemindai Kamera QR Code
          </h3>
          <button @click="stopCameraScanner" :class="isDark ? 'bg-slate-800 text-slate-300' : 'bg-[#FFB7B2] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'" class="text-xs font-black px-3 py-1.5 rounded-xl">
            ✕ Tutup Kamera
          </button>
        </div>

        <div class="relative aspect-square w-full bg-slate-950 rounded-2xl overflow-hidden border-2 border-black flex items-center justify-center">
          <video ref="videoRef" class="w-full h-full object-cover"></video>
          <canvas ref="canvasRef" class="hidden"></canvas>
          <div class="absolute inset-0 border-4 border-dashed border-[#C8F53F] m-10 rounded-2xl pointer-events-none animate-pulse flex items-center justify-center">
            <span class="text-black text-xs font-black bg-[#C8F53F] px-3 py-1 rounded-full border-2 border-black shadow-[2px_2px_0px_#1A1A1A]">Arahkan QR ke Sini</span>
          </div>
        </div>

        <p class="text-xs font-black" :class="isDark ? 'text-slate-400' : 'text-slate-700'">Arahkan kamera HP ke QR Code pesanan untuk memindai otomatis.</p>
      </div>
    </div>

    <!-- Banner Notifikasi Scan Berhasil untuk User -->
    <div v-if="scanSuccessBanner" class="bg-[#C8F53F] border-3 border-black text-black p-5 rounded-3xl shadow-[6px_6px_0px_#1A1A1A] flex items-center justify-between gap-4 animate-bounce">
      <div class="flex items-center gap-3">
        <span class="text-3xl">✅</span>
        <div>
          <h3 class="font-black text-lg tracking-wide uppercase text-slate-900">Scan QR Code Berhasil Terdeteksi!</h3>
          <p class="text-xs font-extrabold">
            Kode Pesanan: <span class="font-mono bg-white px-2 py-0.5 rounded border border-black text-black font-black">{{ scanSuccessCode }}</span> ditemukan dalam Riwayat Anda. Status berhasil di-update!
          </p>
        </div>
      </div>
      <button @click="scanSuccessBanner = false" class="bg-black text-white hover:bg-slate-800 font-black text-xs px-3.5 py-2 rounded-xl border-2 border-black">
        ✕ Tutup Notifikasi
      </button>
    </div>

    <div v-if="loading" class="text-center py-16 font-black text-slate-700">
      Memuat riwayat pesanan...
    </div>

    <div 
      v-else-if="orders.length === 0" 
      :class="isDark ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-[#FFF8EC] border-3 border-[#1A1A1A] shadow-[6px_6px_0px_#1A1A1A] text-slate-900'"
      class="text-center py-16 rounded-3xl"
    >
      <div class="text-5xl mb-3">📦</div>
      <h3 class="font-black text-lg">Belum Ada Pesanan</h3>
      <p class="text-xs font-bold text-slate-700 mt-1 mb-6">Anda belum melakukan pesanan buku.</p>
      <NuxtLink to="/user/katalog" :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[3px_3px_0px_#1A1A1A]'" class="px-6 py-3 rounded-xl text-xs font-black inline-block">
        Mulai Belanja &rarr;
      </NuxtLink>
    </div>

    <div v-else class="space-y-8 print:space-y-0">
      <div 
        v-for="order in orders" 
        :key="order.id" 
        :id="'order-card-' + order.kode_pesanan"
        :class="highlightedOrderCode === order.kode_pesanan ? 'ring-4 ring-[#C8F53F] scale-[1.01]' : ''"
        class="order-card space-y-4 transition-all duration-300"
      >
        <!-- Card Container matching Mockup -->
        <div 
          :class="isDark ? 'bg-slate-900 border-slate-700 text-slate-100' : 'bg-[#FFF8EC] border-3 border-[#1A1A1A] shadow-[6px_6px_0px_#1A1A1A] text-slate-900'"
          class="rounded-3xl p-6 relative transition-colors duration-300"
        >
          <!-- Card Title -->
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-black tracking-tight" :class="isDark ? 'text-white' : 'text-slate-900'">
              Kode Pesanan : <span :class="isDark ? 'text-indigo-400' : 'bg-[#FFE566] text-black px-2 py-0.5 rounded-lg border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'" class="font-mono uppercase">{{ order.kode_pesanan }}</span>
            </h2>
            <span :class="getStatusBadgeClass(order.status)" class="text-xs font-black uppercase px-3.5 py-1 rounded-full border-2 border-black shadow-[2px_2px_0px_#1A1A1A]">
              {{ order.status }}
            </span>
          </div>

          <!-- Alert Petunjuk Pembeli -->
          <div 
            :class="isDark ? 'bg-slate-950/80 border-slate-800 text-slate-300' : 'bg-[#D4B8FF] border-2 border-black text-slate-900 shadow-[3px_3px_0px_#1A1A1A]'"
            class="p-3 rounded-2xl mb-4 text-xs font-bold flex items-center gap-2"
          >
            <span class="text-lg">📢</span>
            <span>Tunjukkan QR Code di bawah kepada Kasir/Admin. Setelah di-scan & berhasil dicatat, mohon tunggu Struk / Invoice diserahkan langsung oleh Admin.</span>
          </div>

          <!-- Content Grid: Table + QR Code -->
          <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-center">
            <!-- Table Section -->
            <!-- List Section (retro style, responsive) -->
            <div class="lg:col-span-3">
              <!-- Header - hidden on mobile -->
              <div
                :class="isDark ? 'bg-slate-800 text-slate-300 border-slate-700' : 'bg-[#FFE566] text-black border-2 border-black'"
                class="hidden sm:grid grid-cols-[2fr_1fr_0.6fr_1fr_1fr] gap-2 text-xs font-black uppercase rounded-t-2xl py-3 px-4">
                <span>Judul Buku</span>
                <span class="text-center">Tanggal</span>
                <span class="text-right">Jumlah</span>
                <span class="text-right">Harga Satuan</span>
                <span class="text-right">Total</span>
              </div>

              <div
                :class="isDark ? 'bg-slate-950 border-slate-700' : 'bg-white border-2 border-black shadow-[3px_3px_0px_#1A1A1A]'"
                class="divide-y-2 divide-black rounded-2xl sm:rounded-t-none overflow-hidden">
                <div v-for="detail in (order.details || order.items || [])" :key="detail.id"
                  :class="isDark ? 'hover:bg-slate-900/60' : 'hover:bg-[#FFF8EC]'"
                  class="grid grid-cols-2 sm:grid-cols-[2fr_1fr_0.6fr_1fr_1fr] gap-x-2 gap-y-1.5 p-4 transition-colors text-xs font-bold">

                  <span class="col-span-2 sm:col-span-1 font-black">
                    {{ detail.buku?.judul || detail.nama_buku }}
                  </span>

                  <span class="sm:text-center">
                    <span class="sm:hidden text-slate-500 font-black">Tanggal: </span>{{ formatDate(order.created_at) }}
                  </span>

                  <span class="sm:text-right font-black">
                    <span class="sm:hidden text-slate-500">Jumlah: </span>{{ detail.qty }}
                  </span>

                  <span class="sm:text-right">
                    <span class="sm:hidden text-slate-500 font-black">Harga: </span>Rp {{
                      formatPrice(detail.harga_satuan) }}
                  </span>

                  <span class="sm:text-right font-black">
                    <span class="sm:hidden text-slate-500">Total: </span>Rp {{ formatPrice(detail.subtotal) }}
                  </span>
                </div>

                <!-- Total row -->
                <div
                  :class="isDark ? 'bg-slate-800 text-white border-slate-700' : 'bg-[#C8F53F] text-black border-black'"
                  class="flex justify-between items-center border-t-2 p-4">
                  <span class="text-sm font-black uppercase">Total Harga</span>
                  <span class="text-base font-black">Rp {{ formatPrice(order.total_harga) }}</span>
                </div>
              </div>
            </div>

            <!-- QR Code Section -->
            <div
              :class="isDark ? 'bg-slate-950 border-slate-800' : 'bg-white border-2 border-black shadow-[3px_3px_0px_#1A1A1A]'"
              class="lg:col-span-1 flex flex-col items-center justify-center p-2 rounded-2xl">
              <QrCodeDisplay :value="order.kode_pesanan" :size="150" show-label />
              <p class="text-[10px] text-slate-900 font-black text-center mt-1">
                Tunjukkan QR ke Kasir
              </p>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 print:hidden">
          <button 
            @click="downloadInvoicePdf(order.id)" 
            :disabled="order.status !== 'completed'"
            :class="order.status === 'completed' 
              ? (isDark ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-[#D4B8FF] text-black border-2 border-black shadow-[3px_3px_0px_#1A1A1A] hover:translate-x-[-1px] hover:translate-y-[-1px] active:translate-x-[1px] active:translate-y-[1px]')
              : (isDark ? 'bg-slate-800 text-slate-500 cursor-not-allowed' : 'bg-slate-200 text-slate-500 cursor-not-allowed border-2 border-slate-400')
            "
            class="flex-1 font-black text-sm py-3 rounded-2xl transition-transform flex items-center justify-center gap-2 disabled:opacity-50"
          >
            <LucideFileText class="w-5 h-5" />
            <span>{{ order.status === 'completed' ? 'Unduh Invoice PDF' : 'Tunggu Status Selesai' }}</span>
          </button>

          <button 
            @click="openReviewModal(order)"
            :disabled="order.status !== 'completed'"
            :class="order.status === 'completed' 
              ? (isDark ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-[#FFD4A3] text-black border-2 border-black shadow-[3px_3px_0px_#1A1A1A] hover:translate-x-[-1px] hover:translate-y-[-1px] active:translate-x-[1px] active:translate-y-[1px]')
              : (isDark ? 'bg-slate-800 text-slate-500 cursor-not-allowed' : 'bg-slate-200 text-slate-500 cursor-not-allowed border-2 border-slate-400')
            "
            class="flex-1 font-black text-sm py-3 rounded-2xl transition-transform flex items-center justify-center gap-2 disabled:opacity-50"
          >
            <span>⭐</span>
            <span>{{ order.status === 'completed' ? 'Beri Rating & Ulasan' : 'Tunggu Status Selesai' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Review Modal -->
  <div v-if="showReviewModal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center p-4">
    <div 
      :class="isDark ? 'bg-slate-900 border-slate-700 text-white' : 'bg-[#FFF8EC] border-4 border-black text-slate-900 shadow-[8px_8px_0px_#1A1A1A]'"
      class="rounded-3xl p-6 max-w-md w-full space-y-4 max-h-[90vh] overflow-y-auto"
    >
      <div class="flex justify-between items-center border-b-2 pb-3" :class="isDark ? 'border-slate-700' : 'border-black'">
        <h3 class="font-black text-lg">⭐ Rating & Ulasan</h3>
        <button @click="closeReviewModal()" :class="isDark ? 'bg-slate-800 text-slate-300' : 'bg-[#FFB7B2] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'" class="text-xs font-black px-3 py-1.5 rounded-xl">
          ✕
        </button>
      </div>

      <!-- Order Info -->
      <div :class="isDark ? 'bg-slate-800 text-slate-300' : 'bg-[#FFE566] text-black'" class="p-3 rounded-2xl text-sm font-bold">
        <p>Kode Pesanan: <span class="font-mono">{{ selectedOrder?.kode_pesanan }}</span></p>
      </div>

      <!-- Rating Stars -->
      <div class="space-y-2">
        <label class="block font-black text-sm">Berikan Rating:</label>
        <div class="flex gap-2 text-3xl">
          <button 
            v-for="star in [1, 2, 3, 4, 5]" 
            :key="star"
            @click="reviewForm.rating = star"
            :class="star <= reviewForm.rating ? 'opacity-100' : 'opacity-40'"
            class="transition-opacity cursor-pointer hover:opacity-100"
          >
            ⭐
          </button>
        </div>
        <p class="text-xs font-bold" :class="isDark ? 'text-slate-400' : 'text-slate-600'">
          {{ reviewForm.rating }} dari 5 bintang
        </p>
      </div>

      <!-- Books for Review -->
      <div class="space-y-2">
        <label class="block font-black text-sm">Pilih Buku:</label>
        <div class="space-y-2 max-h-48 overflow-y-auto">
          <button 
            v-for="detail in (selectedOrder?.details || selectedOrder?.items || [])" 
            :key="detail.id"
            @click="reviewForm.book_id = detail.book_id"
            :class="reviewForm.book_id === detail.book_id 
              ? (isDark ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-[#C8F53F] text-black border-black shadow-[2px_2px_0px_#1A1A1A]')
              : (isDark ? 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-700' : 'bg-white text-slate-900 border-2 border-black')
            "
            class="w-full text-left p-3 rounded-xl font-bold text-sm border-2 transition-colors"
          >
            📖 {{ detail.buku?.nama_buku || detail.nama_buku }}
          </button>
        </div>
      </div>

      <!-- Comment -->
      <div class="space-y-2">
        <label class="block font-black text-sm">Komentar (opsional):</label>
        <textarea 
          v-model="reviewForm.komentar"
          placeholder="Tuliskan pengalaman Anda dengan buku ini..."
          :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500' : 'bg-white border-2 border-black text-slate-900 placeholder-slate-500 shadow-[2px_2px_0px_#1A1A1A]'"
          class="w-full p-3 rounded-xl font-medium text-xs outline-none border resize-none h-20 transition-colors"
        ></textarea>
        <p class="text-xs font-bold" :class="isDark ? 'text-slate-500' : 'text-slate-600'">
          {{ reviewForm.komentar?.length || 0 }} / 1000 karakter
        </p>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-3 pt-4 border-t-2" :class="isDark ? 'border-slate-700' : 'border-black'">
        <button 
          @click="closeReviewModal()"
          :class="isDark ? 'bg-slate-800 text-white hover:bg-slate-700' : 'bg-slate-200 text-black border-2 border-black'"
          class="flex-1 py-2.5 rounded-xl font-black text-xs transition-colors"
        >
          Batal
        </button>
        <button 
          @click="submitReview"
          :disabled="!reviewForm.rating || !reviewForm.book_id || submittingReview"
          :class="isDark ? 'bg-indigo-600 hover:bg-indigo-700 text-white disabled:bg-slate-800' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A] disabled:opacity-50'"
          class="flex-1 py-2.5 rounded-xl font-black text-xs transition-colors disabled:cursor-not-allowed"
        >
          {{ submittingReview ? 'Mengirim...' : 'Kirim Ulasan' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { FileText as LucideFileText, QrCode as LucideQrCode } from 'lucide-vue-next'
import jsQR from 'jsqr'

definePageMeta({
  middleware: 'auth'
})

const api = useApi()
const orders = ref<any[]>([])
const loading = ref(true)
let pollTimer: any = null

const scanQuery = ref('')
const scanSuccessBanner = ref(false)
const scanSuccessCode = ref('')
const highlightedOrderCode = ref('')

// Camera scanner state
const showCameraModal = ref(false)
const videoRef = ref<HTMLVideoElement | null>(null)
const canvasRef = ref<HTMLCanvasElement | null>(null)
let cameraStream: MediaStream | null = null
let animationFrameId: number | null = null

// Review modal state
const showReviewModal = ref(false)
const selectedOrder = ref<any>(null)
const submittingReview = ref(false)
const reviewForm = ref({
  rating: 5,
  book_id: null as number | null,
  komentar: ''
})

const formatPrice = (val: number) => {
  return new Intl.NumberFormat('id-ID').format(val || 0)
}

const formatDate = (dateStr: string) => {
  if (!dateStr) return '21/4/2026'
  const d = new Date(dateStr)
  return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`
}

const { isDark } = useTheme()

const getStatusBadgeClass = (status: string) => {
  if (isDark.value) {
    switch (status) {
      case 'pending': return 'bg-amber-950/80 text-amber-300 border-amber-600'
      case 'confirmed': return 'bg-sky-950/80 text-sky-300 border-sky-600'
      case 'completed': return 'bg-emerald-950/80 text-emerald-300 border-emerald-600'
      default: return 'bg-slate-800 text-slate-300 border-slate-700'
    }
  }
  switch (status) {
    case 'pending':
      return 'bg-[#FFE566] text-black border-black shadow-[2px_2px_0px_#1A1A1A]'
    case 'confirmed':
      return 'bg-[#D4B8FF] text-black border-black shadow-[2px_2px_0px_#1A1A1A]'
    case 'completed':
      return 'bg-[#C8F53F] text-black border-black shadow-[2px_2px_0px_#1A1A1A]'
    default:
      return 'bg-[#FFF8EC] text-black border-black shadow-[2px_2px_0px_#1A1A1A]'
  }
}

const downloadInvoicePdf = (orderId: number) => {
  const url = `${api.apiBase}/api/orders/${orderId}/invoice-pdf`
  window.open(url, '_blank')
}

const openReviewModal = (order: any) => {
  selectedOrder.value = order
  // Set first book as default
  if (order.details && order.details.length > 0) {
    reviewForm.value.book_id = order.details[0].book_id
  } else if (order.items && order.items.length > 0) {
    reviewForm.value.book_id = order.items[0].book_id
  }
  reviewForm.value.rating = 5
  reviewForm.value.komentar = ''
  showReviewModal.value = true
}

const closeReviewModal = () => {
  showReviewModal.value = false
  selectedOrder.value = null
  reviewForm.value = {
    rating: 5,
    book_id: null,
    komentar: ''
  }
}

const submitReview = async () => {
  if (!reviewForm.value.rating || !reviewForm.value.book_id) {
    const toast = useToast()
    toast.error('Rating dan pilih buku terlebih dahulu')
    return
  }

  submittingReview.value = true
  try {
    const toast = useToast()
    await api.post('/api/reviews', {
      book_id: reviewForm.value.book_id,
      rating: reviewForm.value.rating,
      komentar: reviewForm.value.komentar || null
    })
    toast.success('Ulasan berhasil ditambahkan! Terima kasih atas penilaian Anda.')
    closeReviewModal()
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal menambahkan ulasan')
  } finally {
    submittingReview.value = false
  }
}

const fetchOrders = async (silent = false) => {
  if (!silent) loading.value = true
  try {
    const res = await api.get('/api/orders')
    orders.value = res.data || []
  } catch (e) {
    console.error(e)
  } finally {
    if (!silent) loading.value = false
  }
}

const handleUserScanSubmit = async () => {
  const query = scanQuery.value.trim().toLowerCase()
  if (!query) return

  try {
    const res = await api.post('/api/orders/scan', { kode_pesanan: query })
    const matched = res.data

    scanSuccessCode.value = matched.kode_pesanan
    scanSuccessBanner.value = true
    highlightedOrderCode.value = matched.kode_pesanan
    scanQuery.value = ''

    // Play audio beep sound
    if (process.client) {
      try {
        const audioCtx = new (window.AudioContext || (window as any).webkitAudioContext)()
        const osc = audioCtx.createOscillator()
        const gain = audioCtx.createGain()
        osc.type = 'sine'
        osc.frequency.setValueAtTime(880, audioCtx.currentTime)
        gain.gain.setValueAtTime(0.3, audioCtx.currentTime)
        osc.connect(gain)
        gain.connect(audioCtx.destination)
        osc.start()
        osc.stop(audioCtx.currentTime + 0.15)
      } catch (e) {
        // Ignore audio errors
      }
    }

    // Scroll to order element
    if (process.client) {
      setTimeout(() => {
        const el = document.getElementById('order-card-' + matched.kode_pesanan)
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' })
      }, 100)
    }

    const toast = useToast()
    toast.success(`✅ Scan Berhasil & Status Diperbarui!\n\nKode Pesanan: ${matched.kode_pesanan}\nStatus Terbaru DB: ${matched.status.toUpperCase()}\nTotal: Rp. ${formatPrice(matched.total_harga)}\n\nStatus pesanan berhasil diubah menjadi 'CONFIRMED'.`)
    await fetchOrders()
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || `Pesanan dengan kode "${scanQuery.value}" tidak ditemukan dalam Riwayat Anda!`)
  }
}

// Camera Scanner Logic for User Page
const startCameraScanner = async () => {
  showCameraModal.value = true
  await nextTick()
  try {
    cameraStream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'environment' }
    })
    if (videoRef.value) {
      videoRef.value.srcObject = cameraStream
      videoRef.value.setAttribute('playsinline', 'true')
      videoRef.value.play()
      requestAnimationFrame(scanVideoFrame)
    }
  } catch (err) {
    const toast = useToast()
    toast.error('Gagal mengakses kamera. Pastikan izin kamera telah diberikan di browser.')
    showCameraModal.value = false
  }
}

const scanVideoFrame = () => {
  if (!showCameraModal.value || !videoRef.value) return

  if (videoRef.value.readyState === videoRef.value.HAVE_ENOUGH_DATA) {
    const video = videoRef.value
    const canvas = canvasRef.value || document.createElement('canvas')
    canvas.width = video.videoWidth
    canvas.height = video.videoHeight
    const ctx = canvas.getContext('2d')
    if (ctx) {
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height)
      const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height)
      const code = jsQR(imageData.data, imageData.width, imageData.height, {
        inversionAttempts: 'dontInvert'
      })

      if (code && code.data) {
        scanQuery.value = code.data
        stopCameraScanner()
        handleUserScanSubmit()
        return
      }
    }
  }
  animationFrameId = requestAnimationFrame(scanVideoFrame)
}

const stopCameraScanner = () => {
  if (animationFrameId) cancelAnimationFrame(animationFrameId)
  if (cameraStream) {
    cameraStream.getTracks().forEach(track => track.stop())
    cameraStream = null
  }
  showCameraModal.value = false
}

// Global listener for USB hardware QR code scanner on User page
let scannerBuffer = ''
let lastKeyTime = Date.now()

const onGlobalKeydown = (e: KeyboardEvent) => {
  const currentTime = Date.now()
  if (currentTime - lastKeyTime > 100) {
    scannerBuffer = ''
  }
  lastKeyTime = currentTime

  if (e.key === 'Enter') {
    if (scannerBuffer.length > 2) {
      scanQuery.value = scannerBuffer
      handleUserScanSubmit()
      scannerBuffer = ''
    }
  } else if (e.key.length === 1) {
    scannerBuffer += e.key
  }
}

onMounted(() => {
  fetchOrders()
  // Poll every 5s so when admin scans & updates status, user sees update live
  pollTimer = setInterval(() => {
    fetchOrders(true)
  }, 5000)

  if (process.client) {
    window.addEventListener('keydown', onGlobalKeydown)
  }
})

onUnmounted(() => {
  if (pollTimer) clearInterval(pollTimer)
  stopCameraScanner()
  if (process.client) {
    window.removeEventListener('keydown', onGlobalKeydown)
  }
})
</script>

<style scoped>
@media print {
  body * {
    visibility: hidden;
  }
  .order-card, .order-card * {
    visibility: visible;
  }
  .order-card {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
}
</style>

