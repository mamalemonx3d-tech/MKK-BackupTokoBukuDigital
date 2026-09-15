<template>
  <div class="max-w-4xl mx-auto space-y-8">
    <!-- Header Scan & Search Pesanan -->
    <div 
      :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-100' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'"
      class="p-5 rounded-2xl border flex flex-col md:flex-row items-center justify-between gap-4 transition-colors duration-300"
    >
      <div class="flex items-center gap-2 w-full md:w-auto flex-1">
        <div class="relative w-full max-w-md">
          <input 
            v-model="scanQuery" 
            type="text" 
            placeholder="Scan QR Code / Masukkan Kode (A021)..." 
            :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[3px_3px_0px_#000000] placeholder-zinc-400'"
            class="w-full pl-10 pr-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold outline-none transition-all border-2"
            @keyup.enter="handleUserScanSubmit"
          />
          <LucideQrCode class="w-4 h-4 text-zinc-400 absolute left-3.5 top-3" />
        </div>
        <button 
          @click="startCameraScanner" 
          :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
          class="px-4 py-2.5 rounded-xl font-black text-xs flex items-center gap-1.5 whitespace-nowrap transition-transform border-2 cursor-pointer hover:-translate-y-0.5 active:translate-y-0"
        >
          <span>📷</span>
          <span>Scan Kamera HP</span>
        </button>
      </div>

      <div 
        :class="isDark ? 'bg-zinc-950 border-zinc-800 text-zinc-300' : 'bg-zinc-100 border-2 border-black text-black shadow-[1.5px_1.5px_0px_#000000]'"
        class="text-xs font-mono font-bold uppercase px-3.5 py-2 rounded-xl border shrink-0"
      >
        Total: {{ orders.length }} Pesanan
      </div>
    </div>

    <!-- Modal Scanner Kamera HP / Web -->
    <div v-if="showCameraModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div 
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white' : 'bg-white border-2 border-black text-black shadow-[8px_8px_0px_#000000]'"
        class="rounded-3xl border-2 p-6 max-w-md w-full space-y-4 text-center relative overflow-hidden"
      >
        <div class="flex justify-between items-center border-b pb-3" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
          <h3 class="font-black text-sm flex items-center gap-2">
            <span>📷</span>
            <span>Pemindai Kamera QR Code</span>
          </h3>
          <button 
            @click="stopCameraScanner" 
            :class="isDark ? 'bg-zinc-800 text-zinc-300 border-zinc-700' : 'bg-zinc-100 text-black border-2 border-black shadow-[1.5px_1.5px_0px_#000000]'" 
            class="text-xs font-black px-3 py-1 rounded-lg border cursor-pointer"
          >
            ✕ Tutup
          </button>
        </div>

        <div class="relative aspect-square w-full bg-black rounded-2xl overflow-hidden border-2 border-zinc-700 flex items-center justify-center">
          <video ref="videoRef" class="w-full h-full object-cover"></video>
          <canvas ref="canvasRef" class="hidden"></canvas>
          <div class="absolute inset-0 border-2 border-dashed border-white m-10 rounded-2xl pointer-events-none animate-pulse flex items-center justify-center">
            <span class="text-black text-xs font-black bg-white px-3 py-1 rounded-full border border-black shadow-md">Arahkan QR ke Sini</span>
          </div>
        </div>

        <p class="text-xs font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">Arahkan kamera ke QR Code pesanan untuk memindai otomatis.</p>
      </div>
    </div>

    <!-- Banner Notifikasi Scan Berhasil -->
    <div 
      v-if="scanSuccessBanner" 
      class="p-5 rounded-2xl border-2 flex items-center justify-between gap-4 transition-all"
      :class="isDark ? 'bg-emerald-950/40 border-emerald-700 text-emerald-300' : 'bg-emerald-50 border-emerald-600 text-emerald-950 shadow-[3px_3px_0px_#059669]'"
    >
      <div class="flex items-center gap-3">
        <span class="text-2xl">✓</span>
        <div>
          <h3 class="font-black text-xs sm:text-sm uppercase tracking-wide">Scan QR Code Berhasil Ditemukan!</h3>
          <p class="text-xs font-medium mt-0.5">
            Kode Pesanan: <span class="font-mono font-black px-1.5 py-0.5 rounded border" :class="isDark ? 'border-emerald-600 bg-black/40' : 'border-emerald-600 bg-white'">{{ scanSuccessCode }}</span>. Status berhasil diperbarui.
          </p>
        </div>
      </div>
      <button 
        @click="scanSuccessBanner = false" 
        class="text-xs font-black px-3 py-1.5 rounded-lg border cursor-pointer"
        :class="isDark ? 'border-emerald-700 bg-emerald-900/60 text-white' : 'border-emerald-700 bg-emerald-600 text-white shadow-sm'"
      >
        Tutup
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-16 font-bold text-xs" :class="isDark ? 'text-zinc-500' : 'text-zinc-500'">
      Memuat riwayat pesanan...
    </div>

    <!-- Empty State -->
    <div 
      v-else-if="orders.length === 0" 
      :class="isDark ? 'bg-zinc-900/60 border-zinc-800 text-zinc-300' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'" 
      class="text-center py-16 rounded-3xl border-2"
    >
      <div class="text-4xl mb-3">📦</div>
      <h3 class="font-black text-lg">Belum Ada Pesanan</h3>
      <p class="text-xs font-medium text-zinc-500 mt-1 mb-6">Anda belum memiliki transaksi pesanan buku.</p>
      <NuxtLink 
        to="/katalog" 
        :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'" 
        class="px-6 py-3 rounded-xl text-xs font-black inline-flex items-center gap-2 border-2 transition-all cursor-pointer hover:-translate-y-0.5"
      >
        <span>Mulai Belanja</span>
        <span>&rarr;</span>
      </NuxtLink>
    </div>

    <!-- Orders List -->
    <div v-else class="space-y-8 print:space-y-0">
      <div 
        v-for="order in orders" 
        :key="order.id" 
        :id="'order-card-' + order.kode_pesanan"
        :class="highlightedOrderCode === order.kode_pesanan ? (isDark ? 'ring-2 ring-white' : 'ring-2 ring-black') : ''"
        class="order-card space-y-3 transition-all duration-300"
      >
        <!-- Card Container -->
        <div 
          :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-100' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'"
          class="rounded-3xl border-2 p-6 relative transition-colors duration-300"
        >
          <!-- Card Title & Status Badge -->
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg sm:text-xl font-black tracking-tight flex items-center gap-2">
              <span>Kode Pesanan:</span>
              <span class="font-mono uppercase px-2 py-0.5 rounded-lg border text-sm" :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white' : 'bg-zinc-100 border-black text-black'">
                {{ order.kode_pesanan }}
              </span>
            </h2>
            <span :class="getStatusBadgeClass(order.status)" class="text-[10px] font-mono font-bold uppercase px-3 py-1 rounded-full border">
              {{ order.status }}
            </span>
          </div>

          <!-- Alert Petunjuk Pembeli -->
          <div 
            :class="isDark ? 'bg-zinc-950 border-zinc-800 text-zinc-300' : 'bg-zinc-50 border-2 border-black text-zinc-800 shadow-[2px_2px_0px_#000000]'"
            class="p-3.5 rounded-xl border mb-5 text-xs font-medium flex items-center gap-2.5"
          >
            <span class="text-base">📢</span>
            <span>Tunjukkan QR Code berikut kepada Kasir/Admin. Setelah diverifikasi, Anda dapat mengunduh Invoice PDF resmi.</span>
          </div>

          <!-- Content Grid: Table + QR Code -->
          <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-center">
            <!-- Table Section -->
            <div class="lg:col-span-3">
              <!-- Header on Desktop -->
              <div
                :class="isDark ? 'bg-zinc-950 text-zinc-400 border-zinc-800' : 'bg-zinc-100 text-black border-2 border-black'"
                class="hidden sm:grid grid-cols-[2fr_1fr_0.6fr_1fr_1fr] gap-2 text-[10px] font-mono font-bold uppercase rounded-t-xl py-2.5 px-4 border-b"
              >
                <span>Judul Buku</span>
                <span class="text-center">Tanggal</span>
                <span class="text-right">Jumlah</span>
                <span class="text-right">Harga Satuan</span>
                <span class="text-right">Total</span>
              </div>

              <div
                :class="isDark ? 'bg-zinc-950/60 border-zinc-800 divide-zinc-800' : 'bg-white border-2 border-black divide-zinc-200 shadow-[2px_2px_0px_#000000]'"
                class="divide-y rounded-xl sm:rounded-t-none overflow-hidden border"
              >
                <div 
                  v-for="detail in (order.details || order.items || [])" 
                  :key="detail.id"
                  :class="isDark ? 'hover:bg-zinc-900/60' : 'hover:bg-zinc-50'"
                  class="grid grid-cols-2 sm:grid-cols-[2fr_1fr_0.6fr_1fr_1fr] gap-x-2 gap-y-1.5 p-3.5 transition-colors text-xs"
                >
                  <span class="col-span-2 sm:col-span-1 font-black" :class="isDark ? 'text-white' : 'text-black'">
                    {{ detail.buku?.judul || detail.buku?.nama_buku || detail.nama_buku }}
                  </span>

                  <span class="sm:text-center text-zinc-400 font-mono text-[11px]">
                    <span class="sm:hidden text-zinc-500">Tgl: </span>{{ formatDate(order.created_at) }}
                  </span>

                  <span class="sm:text-right font-mono font-bold">
                    <span class="sm:hidden text-zinc-500">Qty: </span>{{ detail.qty }}
                  </span>

                  <span class="sm:text-right font-mono">
                    <span class="sm:hidden text-zinc-500">Harga: </span>Rp {{ formatPrice(detail.harga_satuan) }}
                  </span>

                  <span class="sm:text-right font-mono font-black" :class="isDark ? 'text-white' : 'text-black'">
                    <span class="sm:hidden text-zinc-500">Total: </span>Rp {{ formatPrice(detail.subtotal) }}
                  </span>
                </div>

                <!-- Total Row -->
                <div
                  :class="isDark ? 'bg-zinc-900 text-white border-zinc-800' : 'bg-zinc-100 text-black border-zinc-300'"
                  class="flex justify-between items-center border-t p-3.5 font-black"
                >
                  <span class="text-xs uppercase font-mono">Total Pembayaran</span>
                  <span class="text-sm font-mono font-black">Rp {{ formatPrice(order.total_harga) }}</span>
                </div>
              </div>
            </div>

            <!-- QR Code Section -->
            <div
              :class="isDark ? 'bg-zinc-950 border-zinc-800' : 'bg-white border-2 border-black shadow-[2px_2px_0px_#000000]'"
              class="lg:col-span-1 flex flex-col items-center justify-center p-3 rounded-2xl border"
            >
              <QrCodeDisplay :value="order.kode_pesanan" :size="130" show-label />
              <p class="text-[9px] font-mono font-bold uppercase tracking-wider text-zinc-400 mt-1.5 text-center">
                Scan Kasir
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
              ? (isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800')
              : (isDark ? 'bg-zinc-900 text-zinc-600 border-zinc-800 cursor-not-allowed' : 'bg-zinc-100 text-zinc-400 border-zinc-300 cursor-not-allowed')
            "
            class="flex-1 font-black text-xs py-3 rounded-xl transition-all flex items-center justify-center gap-2 border-2 cursor-pointer disabled:opacity-50"
          >
            <LucideFileText class="w-4 h-4" />
            <span>{{ order.status === 'completed' ? 'Unduh Invoice PDF' : 'Menunggu Status Selesai' }}</span>
          </button>

          <button 
            @click="openReviewModal(order)" 
            :disabled="order.status !== 'completed'"
            :class="order.status === 'completed' 
              ? (isDark ? 'bg-zinc-900 text-white border-zinc-700 hover:border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-white text-black border-2 border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-50')
              : (isDark ? 'bg-zinc-900 text-zinc-600 border-zinc-800 cursor-not-allowed' : 'bg-zinc-100 text-zinc-400 border-zinc-300 cursor-not-allowed')
            "
            class="flex-1 font-black text-xs py-3 rounded-xl transition-all flex items-center justify-center gap-2 border-2 cursor-pointer disabled:opacity-50"
          >
            <span>⭐</span>
            <span>{{ order.status === 'completed' ? 'Beri Rating & Ulasan' : 'Menunggu Status Selesai' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Review Modal -->
  <div v-if="showReviewModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div 
      :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white' : 'bg-white border-2 border-black text-black shadow-[8px_8px_0px_#000000]'"
      class="rounded-3xl border-2 p-6 max-w-md w-full space-y-4 max-h-[90vh] overflow-y-auto"
    >
      <div class="flex justify-between items-center border-b pb-3" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
        <h3 class="font-black text-sm">⭐ Rating & Ulasan Buku</h3>
        <button @click="closeReviewModal()" :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="text-xs font-black px-2.5 py-1 rounded-lg cursor-pointer">
          ✕
        </button>
      </div>

      <!-- Order Info -->
      <div :class="isDark ? 'bg-zinc-950 border-zinc-800 text-zinc-300' : 'bg-zinc-50 border border-zinc-200 text-black'" class="p-3 rounded-xl border text-xs font-medium">
        <p>Kode Pesanan: <span class="font-mono font-bold">{{ selectedOrder?.kode_pesanan }}</span></p>
      </div>

      <!-- Rating Stars -->
      <div class="space-y-1.5">
        <label class="block font-black text-xs font-mono uppercase text-zinc-400">Berikan Rating:</label>
        <div class="flex gap-2 text-2xl">
          <button 
            v-for="star in [1, 2, 3, 4, 5]" 
            :key="star"
            @click="reviewForm.rating = star"
            :class="star <= reviewForm.rating ? 'opacity-100' : 'opacity-30'"
            class="transition-opacity cursor-pointer hover:opacity-100"
          >
            ⭐
          </button>
        </div>
        <p class="text-[11px] font-bold" :class="isDark ? 'text-zinc-400' : 'text-zinc-500'">
          {{ reviewForm.rating }} dari 5 bintang
        </p>
      </div>

      <!-- Books for Review -->
      <div class="space-y-1.5">
        <label class="block font-black text-xs font-mono uppercase text-zinc-400">Pilih Buku:</label>
        <div class="space-y-1.5 max-h-40 overflow-y-auto">
          <button 
            v-for="detail in (selectedOrder?.details || selectedOrder?.items || [])" 
            :key="detail.id"
            @click="reviewForm.book_id = detail.book_id"
            :class="reviewForm.book_id === detail.book_id 
              ? (isDark ? 'bg-white text-black border-white shadow-sm' : 'bg-black text-white border-black shadow-sm')
              : (isDark ? 'bg-zinc-950 text-zinc-300 border-zinc-800 hover:border-zinc-600' : 'bg-zinc-50 text-black border-zinc-300 hover:border-black')
            "
            class="w-full text-left p-2.5 rounded-xl font-bold text-xs border-2 transition-colors cursor-pointer"
          >
            📖 {{ detail.buku?.nama_buku || detail.nama_buku }}
          </button>
        </div>
      </div>

      <!-- Comment -->
      <div class="space-y-1.5">
        <label class="block font-black text-xs font-mono uppercase text-zinc-400">Ulasan (opsional):</label>
        <textarea 
          v-model="reviewForm.komentar"
          placeholder="Tuliskan ulasan Anda mengenai buku ini..."
          :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500' : 'bg-zinc-50 border-2 border-black text-black placeholder-zinc-400 shadow-[2px_2px_0px_#000000]'"
          class="w-full p-3 rounded-xl font-medium text-xs outline-none border resize-none h-20 transition-colors"
        ></textarea>
        <p class="text-[10px] font-mono text-zinc-400">
          {{ reviewForm.komentar?.length || 0 }} / 1000 karakter
        </p>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-2.5 pt-3 border-t" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
        <button 
          @click="closeReviewModal()"
          :class="isDark ? 'bg-zinc-800 text-white hover:bg-zinc-700' : 'bg-zinc-100 text-black border border-zinc-300 hover:bg-zinc-200'"
          class="flex-1 py-2.5 rounded-xl font-black text-xs transition-colors cursor-pointer"
        >
          Batal
        </button>
        <button 
          @click="submitReview"
          :disabled="!reviewForm.rating || !reviewForm.book_id || submittingReview"
          :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
          class="flex-1 py-2.5 rounded-xl font-black text-xs transition-all border-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
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

useHead({
  title: 'Pesanan Anda - TokoBukuDigital'
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
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`
}

const { isDark } = useTheme()

const getStatusBadgeClass = (status: string) => {
  if (isDark.value) {
    switch (status) {
      case 'pending': return 'bg-amber-950/60 text-amber-300 border-amber-700'
      case 'confirmed': return 'bg-sky-950/60 text-sky-300 border-sky-700'
      case 'completed': return 'bg-emerald-950/60 text-emerald-300 border-emerald-700'
      default: return 'bg-zinc-800 text-zinc-300 border-zinc-700'
    }
  }
  switch (status) {
    case 'pending':
      return 'bg-amber-100 text-amber-900 border-amber-500 shadow-sm'
    case 'confirmed':
      return 'bg-sky-100 text-sky-900 border-sky-500 shadow-sm'
    case 'completed':
      return 'bg-emerald-100 text-emerald-900 border-emerald-500 shadow-sm'
    default:
      return 'bg-zinc-100 text-zinc-800 border-zinc-400 shadow-sm'
  }
}

const downloadInvoicePdf = (orderId: number) => {
  const url = `${api.apiBase}/api/orders/${orderId}/invoice-pdf`
  window.open(url, '_blank')
}

const openReviewModal = (order: any) => {
  selectedOrder.value = order
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
    toast.error('Pilih rating dan buku terlebih dahulu.')
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
    toast.success('Ulasan berhasil ditambahkan. Terima kasih!')
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
      } catch (e) {}
    }

    if (process.client) {
      setTimeout(() => {
        const el = document.getElementById('order-card-' + matched.kode_pesanan)
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' })
      }, 100)
    }

    const toast = useToast()
    toast.success(`Scan Berhasil! Kode: ${matched.kode_pesanan}`)
    await fetchOrders()
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || `Pesanan dengan kode "${scanQuery.value}" tidak ditemukan.`)
  }
}

// Camera Scanner
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
    toast.error('Gagal mengakses kamera. Pastikan izin kamera telah diberikan.')
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

// Global listener for USB QR code scanner
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
