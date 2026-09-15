<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div
      :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-100' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'"
      class="p-5 sm:p-6 rounded-3xl border-2 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors duration-300"
    >
      <div class="space-y-1">
        <div 
          class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider border"
          :class="isDark ? 'bg-zinc-950 text-zinc-300 border-zinc-700' : 'bg-zinc-100 text-black border-black shadow-[1px_1px_0px_#000000]'"
        >
          <span>📊</span>
          <span>Rekapitulasi & Analitik Penjualan</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
          Laporan Penjualan
        </h1>
        <p class="text-xs font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
          Lihat detail transaksi penjualan buku, analitik pendapatan, dan unduh dokumen laporan.
        </p>
      </div>

      <!-- Export Actions in Header -->
      <div class="flex items-center gap-2.5 flex-wrap self-start md:self-auto">
        <a 
          :href="excelExportUrl" 
          target="_blank"
          :class="isDark ? 'bg-zinc-800 hover:bg-zinc-700 text-white border-zinc-700 shadow-[3px_3px_0px_#27272a]' : 'bg-white hover:bg-zinc-50 text-black border-2 border-black shadow-[3px_3px_0px_#000000]'"
          class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer hover:-translate-y-0.5 active:translate-y-0 border"
        >
          <LucideFileSpreadsheet class="w-4 h-4 text-emerald-500" />
          <span>Export Excel</span>
        </a>
        <a 
          :href="pdfExportUrl" 
          target="_blank"
          :class="isDark ? 'bg-white hover:bg-zinc-200 text-black border-white shadow-[3px_3px_0px_#ffffff]' : 'bg-black hover:bg-zinc-800 text-white border-2 border-black shadow-[3px_3px_0px_#000000]'"
          class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-black transition-all cursor-pointer hover:-translate-y-0.5 active:translate-y-0 border-2"
        >
          <LucideFileText class="w-4 h-4" />
          <span>Export PDF</span>
        </a>
      </div>
    </div>

    <!-- Quick Metrics Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Total Omset -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Total Omset</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="isDark ? 'text-white' : 'text-black'">
            Rp {{ formatPrice(summaryMetrics.totalRevenue) }}
          </h3>
        </div>
        <div :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0">
          💰
        </div>
      </div>

      <!-- Total Transaksi -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Total Transaksi</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="isDark ? 'text-white' : 'text-black'">
            {{ orders.length }} <span class="text-xs font-bold text-zinc-400">Pesanan</span>
          </h3>
        </div>
        <div :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0">
          🧾
        </div>
      </div>

      <!-- Total Buku Terjual -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Item Terjual</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="isDark ? 'text-white' : 'text-black'">
            {{ summaryMetrics.totalQty }} <span class="text-xs font-bold text-zinc-400">Buku</span>
          </h3>
        </div>
        <div :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0">
          📚
        </div>
      </div>

      <!-- Rata-rata per Transaksi -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Rata-rata / Pesanan</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="isDark ? 'text-white' : 'text-black'">
            Rp {{ formatPrice(summaryMetrics.averagePerOrder) }}
          </h3>
        </div>
        <div :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0">
          📈
        </div>
      </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div
      :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[3px_3px_0px_#000000]'"
      class="p-4 rounded-2xl border-2 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 transition-colors"
    >
      <!-- Search Input -->
      <div class="relative flex-1 min-w-[240px]">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari kode pesanan atau judul buku..."
          :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-zinc-50 border-2 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[3px_3px_0px_#000000] placeholder-zinc-400'"
          class="w-full pl-9 pr-8 py-2.5 rounded-xl text-xs font-bold outline-none transition-all border-2"
        />
        <LucideSearch class="w-4 h-4 text-zinc-400 absolute left-3 top-3 pointer-events-none" />
        <button 
          v-if="searchQuery" 
          @click="searchQuery = ''" 
          class="absolute right-3 top-3 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
        >
          <LucideX class="w-4 h-4" />
        </button>
      </div>

      <!-- Year Filter & Refresh -->
      <div class="flex items-center gap-2 shrink-0">
        <div 
          :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white' : 'bg-zinc-50 border-2 border-black text-black shadow-[2px_2px_0px_#000000]'" 
          class="flex items-center gap-2 rounded-xl px-3 py-1.5 text-xs font-black border-2"
        >
          <LucideCalendar class="w-4 h-4 text-zinc-400" />
          <span class="text-[10px] font-mono text-zinc-400 uppercase">Tahun:</span>
          <input 
            v-model.number="filterYear" 
            type="number" 
            min="2000"
            max="2099"
            placeholder="2026" 
            :class="isDark ? 'bg-transparent text-white' : 'bg-transparent text-black'"
            class="text-center w-14 outline-none font-black text-xs"
            @change="fetchReport"
          />
        </div>

        <button
          @click="fetchReport"
          :disabled="loading"
          :class="isDark ? 'bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border-zinc-700' : 'bg-zinc-100 hover:bg-zinc-200 text-black border-2 border-black shadow-[2px_2px_0px_#000000]'"
          class="p-2.5 rounded-xl transition-all border disabled:opacity-50 cursor-pointer"
          title="Segarkan Data"
        >
          <LucideRefreshCw class="w-4 h-4" :class="{ 'animate-spin': loading }" />
        </button>
      </div>
    </div>

    <!-- Table Container -->
    <div 
      :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-100' : 'bg-white border-2 border-black shadow-[4px_4px_0px_#000000] text-black'" 
      class="rounded-3xl border-2 overflow-hidden transition-colors"
    >
      <div v-if="loading" class="text-center py-20">
        <LucideRefreshCw class="w-8 h-8 mx-auto animate-spin text-zinc-400 mb-3" />
        <p class="font-black text-sm" :class="isDark ? 'text-zinc-300' : 'text-zinc-700'">Memuat data laporan...</p>
        <p class="text-xs text-zinc-500 mt-1">Mengambil rekapitulasi penjualan tahun {{ filterYear }}</p>
      </div>

      <div v-else-if="filteredRows.length === 0" class="text-center py-20 px-4">
        <div class="w-14 h-14 mx-auto mb-3 rounded-2xl flex items-center justify-center text-2xl border" :class="isDark ? 'bg-zinc-800 border-zinc-700' : 'bg-zinc-100 border-black'">
          📭
        </div>
        <p class="font-black text-base" :class="isDark ? 'text-white' : 'text-black'">Tidak ada data transaksi</p>
        <p class="text-xs text-zinc-500 mt-1 max-w-sm mx-auto">
          {{ searchQuery ? `Tidak ada hasil yang cocok dengan kata kunci "${searchQuery}".` : `Belum ada transaksi tercatat untuk tahun ${filterYear}.` }}
        </p>
        <button
          v-if="searchQuery"
          @click="searchQuery = ''"
          :class="isDark ? 'bg-zinc-800 text-zinc-200 border-zinc-700 hover:bg-zinc-700' : 'bg-black text-white border-black hover:bg-zinc-800'"
          class="mt-4 px-4 py-2 rounded-xl text-xs font-black border transition-all inline-flex items-center gap-1.5"
        >
          <LucideX class="w-3.5 h-3.5" />
          <span>Reset Pencarian</span>
        </button>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr 
              :class="isDark ? 'bg-zinc-950/80 text-zinc-300 border-zinc-800' : 'bg-zinc-100 text-zinc-900 border-b-2 border-black'" 
              class="font-mono font-bold uppercase text-[10px] tracking-wider border-b"
            >
              <th class="px-5 py-3.5">Kode Pesanan</th>
              <th class="px-5 py-3.5">Judul Buku</th>
              <th class="px-5 py-3.5 text-center">Tanggal Transaksi</th>
              <th class="px-5 py-3.5 text-center">Jumlah Item</th>
              <th class="px-5 py-3.5 text-right">Total Harga</th>
            </tr>
          </thead>
          <tbody class="divide-y" :class="isDark ? 'divide-zinc-800' : 'divide-zinc-200'">
            <tr 
              v-for="(item, idx) in paginatedRows" 
              :key="idx" 
              :class="isDark ? 'hover:bg-zinc-800/50' : 'hover:bg-zinc-50'" 
              class="transition-colors"
            >
              <td class="px-5 py-3.5 font-mono">
                <span 
                  class="inline-block px-2 py-0.5 rounded text-[11px] font-bold border"
                  :class="isDark ? 'bg-zinc-950 text-zinc-200 border-zinc-700' : 'bg-white text-black border-black shadow-[1px_1px_0px_#000000]'"
                >
                  {{ item.kode_pesanan }}
                </span>
              </td>
              <td class="px-5 py-3.5 font-bold" :class="isDark ? 'text-zinc-200' : 'text-zinc-900'">
                {{ item.judul_buku }}
              </td>
              <td class="px-5 py-3.5 text-center font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
                {{ item.tanggal }}
              </td>
              <td class="px-5 py-3.5 text-center font-black" :class="isDark ? 'text-zinc-200' : 'text-zinc-900'">
                <span 
                  class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold"
                  :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black/30'"
                >
                  {{ item.jumlah }}
                </span>
              </td>
              <td class="px-5 py-3.5 text-right font-black font-mono" :class="isDark ? 'text-zinc-100' : 'text-black'">
                Rp {{ formatPrice(item.total_harga) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Controls -->
      <div 
        v-if="totalPages > 1" 
        class="p-4 border-t flex flex-col sm:flex-row items-center justify-between gap-3" 
        :class="isDark ? 'border-zinc-800 bg-zinc-950/40' : 'border-zinc-200 bg-zinc-50/50'"
      >
        <span :class="isDark ? 'text-zinc-400' : 'text-zinc-600'" class="text-xs font-bold">
          Menampilkan <span class="font-black" :class="isDark ? 'text-white' : 'text-black'">{{ paginatedRows.length }}</span> dari <span class="font-black" :class="isDark ? 'text-white' : 'text-black'">{{ filteredRows.length }}</span> baris
        </span>

        <div class="flex items-center gap-1.5 flex-wrap">
          <button 
            @click="currentPage = 1"
            :disabled="currentPage === 1"
            :class="isDark ? 'bg-zinc-900 text-zinc-300 border-zinc-800 disabled:opacity-40 hover:bg-zinc-800' : 'bg-white text-black border border-black shadow-[1.5px_1.5px_0px_#000000] disabled:opacity-40 hover:bg-zinc-50'"
            class="px-2.5 py-1.5 rounded-lg font-bold text-xs transition-all cursor-pointer"
            title="Halaman Pertama"
          >
            «
          </button>

          <button 
            @click="currentPage--"
            :disabled="currentPage === 1"
            :class="isDark ? 'bg-zinc-900 text-zinc-300 border-zinc-800 disabled:opacity-40 hover:bg-zinc-800' : 'bg-white text-black border border-black shadow-[1.5px_1.5px_0px_#000000] disabled:opacity-40 hover:bg-zinc-50'"
            class="px-2.5 py-1.5 rounded-lg font-bold text-xs transition-all cursor-pointer"
            title="Sebelumnya"
          >
            ‹
          </button>

          <div class="flex items-center gap-1">
            <button 
              v-for="page in visiblePages"
              :key="page"
              @click="currentPage = page"
              :class="currentPage === page
                ? (isDark ? 'bg-white text-black font-black border-white' : 'bg-black text-white font-black border-black shadow-[2px_2px_0px_#000000]')
                : (isDark ? 'bg-zinc-900 text-zinc-400 border-zinc-800 hover:bg-zinc-800' : 'bg-white text-zinc-700 border border-zinc-300 hover:bg-zinc-100')
              "
              class="w-8 h-8 rounded-lg font-mono text-xs transition-all flex items-center justify-center border cursor-pointer"
            >
              {{ page }}
            </button>
          </div>

          <button 
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            :class="isDark ? 'bg-zinc-900 text-zinc-300 border-zinc-800 disabled:opacity-40 hover:bg-zinc-800' : 'bg-white text-black border border-black shadow-[1.5px_1.5px_0px_#000000] disabled:opacity-40 hover:bg-zinc-50'"
            class="px-2.5 py-1.5 rounded-lg font-bold text-xs transition-all cursor-pointer"
            title="Berikutnya"
          >
            ›
          </button>

          <button 
            @click="currentPage = totalPages"
            :disabled="currentPage === totalPages"
            :class="isDark ? 'bg-zinc-900 text-zinc-300 border-zinc-800 disabled:opacity-40 hover:bg-zinc-800' : 'bg-white text-black border border-black shadow-[1.5px_1.5px_0px_#000000] disabled:opacity-40 hover:bg-zinc-50'"
            class="px-2.5 py-1.5 rounded-lg font-bold text-xs transition-all cursor-pointer"
            title="Halaman Terakhir"
          >
            »
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { 
  Search as LucideSearch, 
  Calendar as LucideCalendar, 
  FileSpreadsheet as LucideFileSpreadsheet, 
  FileText as LucideFileText, 
  RefreshCw as LucideRefreshCw,
  X as LucideX
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'admin'
})

const api = useApi()
const { isDark } = useTheme()
const loading = ref(true)
const searchQuery = ref('')
const filterYear = ref(new Date().getFullYear())
const currentPage = ref(1)
const itemsPerPage = 8

const orders = ref<any[]>([])

const formatPrice = (val: number) => {
  return new Intl.NumberFormat('id-ID').format(val || 0)
}

const pdfExportUrl = computed(() => {
  let url = `${api.apiBase}/api/admin/reports/export-pdf`
  if (filterYear.value) {
    const year = filterYear.value
    const startDate = `${year}-01-01`
    const endDate = `${year}-12-31`
    url += `?start_date=${startDate}&end_date=${endDate}`
  }
  return url
})

const excelExportUrl = computed(() => {
  let url = `${api.apiBase}/api/admin/reports/export-excel`
  if (filterYear.value) {
    const year = filterYear.value
    const startDate = `${year}-01-01`
    const endDate = `${year}-12-31`
    url += `?start_date=${startDate}&end_date=${endDate}`
  }
  return url
})

const fetchReport = async () => {
  loading.value = true
  try {
    const year = filterYear.value
    const startDate = `${year}-01-01`
    const endDate = `${year}-12-31`
    
    const res = await api.get('/api/admin/reports', {
      params: { 
        start_date: startDate,
        end_date: endDate
      }
    })
    orders.value = res.orders || []
    currentPage.value = 1
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

// Flatten order items for table display
const tableRows = computed(() => {
  const rows: any[] = []
  orders.value.forEach(order => {
    if (order.items && Array.isArray(order.items) && order.items.length > 0) {
      order.items.forEach((item: any) => {
        rows.push({
          kode_pesanan: order.kode_pesanan,
          judul_buku: item.buku?.judul || item.judul_buku || 'Buku',
          tanggal: order.created_at ? new Date(order.created_at).toLocaleDateString('id-ID') : '21/4/2026',
          jumlah: item.jumlah,
          total_harga: item.subtotal || (item.jumlah * item.harga_satuan)
        })
      })
    } else if (order.orderDetails && Array.isArray(order.orderDetails) && order.orderDetails.length > 0) {
      order.orderDetails.forEach((detail: any) => {
        rows.push({
          kode_pesanan: order.kode_pesanan,
          judul_buku: detail.book?.nama_buku || detail.judul_buku || 'Buku',
          tanggal: order.created_at ? new Date(order.created_at).toLocaleDateString('id-ID') : '21/4/2026',
          jumlah: detail.qty,
          total_harga: detail.subtotal
        })
      })
    } else {
      rows.push({
        kode_pesanan: order.kode_pesanan,
        judul_buku: order.pelanggan ? `Order oleh ${order.pelanggan}` : 'Detail Buku',
        tanggal: order.created_at ? new Date(order.created_at).toLocaleDateString('id-ID') : '21/4/2026',
        jumlah: order.total_items || 1,
        total_harga: order.total_harga
      })
    }
  })
  return rows
})

// Summary metrics computed from orders & flattened items
const summaryMetrics = computed(() => {
  let totalRevenue = 0
  let totalQty = 0

  orders.value.forEach(order => {
    totalRevenue += Number(order.total_harga) || 0
  })

  tableRows.value.forEach(row => {
    totalQty += Number(row.jumlah) || 0
  })

  const averagePerOrder = orders.value.length > 0 ? Math.round(totalRevenue / orders.value.length) : 0

  return {
    totalRevenue,
    totalQty,
    averagePerOrder
  }
})

const filteredRows = computed(() => {
  if (!searchQuery.value.trim()) return tableRows.value
  const q = searchQuery.value.toLowerCase()
  return tableRows.value.filter(r => 
    r.kode_pesanan.toLowerCase().includes(q) || 
    r.judul_buku.toLowerCase().includes(q)
  )
})

const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredRows.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredRows.value.length / itemsPerPage) || 1
})

const visiblePages = computed(() => {
  const pages: number[] = []
  const maxButtons = 5
  let start = Math.max(1, currentPage.value - 2)
  let end = Math.min(totalPages.value, start + maxButtons - 1)

  if (end - start + 1 < maxButtons) {
    start = Math.max(1, end - maxButtons + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

onMounted(fetchReport)
</script>
