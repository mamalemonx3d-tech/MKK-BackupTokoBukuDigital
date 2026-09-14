<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 :class="isDark ? 'text-white' : 'text-slate-900 bg-[#FFE566] px-3 py-1 inline-block rounded-2xl border-2.5 border-black shadow-[4px_4px_0px_#1A1A1A]'" class="text-3xl font-black">Laporan Penjualan</h1>
      <p :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-bold mt-2">Lihat detail transaksi penjualan buku berdasarkan tahun</p>
    </div>

    <!-- Top Filter Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
      <!-- Search Box -->
      <div class="relative w-full sm:w-96">
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Cari kode pesanan atau judul buku..." 
          :class="isDark ? 'bg-slate-900 border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500' : 'bg-white border-2 border-[#1A1A1A] text-slate-900 shadow-[3px_3px_0px_#1A1A1A] focus:shadow-[4px_4px_0px_#1A1A1A]'"
          class="w-full pl-10 pr-4 py-2.5 rounded-full text-xs font-bold outline-none border transition-all" 
        />
        <LucideSearch class="w-5 h-5 absolute left-3.5 top-2.5" :class="isDark ? 'text-slate-500' : 'text-slate-900'" />
      </div>

      <!-- Year Picker -->
      <div :class="isDark ? 'bg-slate-900 border-slate-700 text-white' : 'bg-[#FFE566] text-black border-2 border-black shadow-[3px_3px_0px_#1A1A1A]'" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-xs font-black">
        <input 
          v-model.number="filterYear" 
          type="number" 
          placeholder="2026" 
          :class="isDark ? 'bg-slate-950 text-white' : 'bg-white text-black'"
          class="bg-transparent text-center w-16 outline-none font-black"
          @change="fetchReport"
        />
        <LucideCalendar class="w-5 h-5" />
      </div>
    </div>

    <!-- Table Container -->
    <div :class="isDark ? 'bg-slate-900 border-slate-800 text-slate-100' : 'bg-[#FFF8EC] border-2.5 border-black shadow-[4px_4px_0px_#1A1A1A] text-slate-900'" class="rounded-3xl overflow-hidden">
      <div v-if="loading" class="text-center py-16 text-slate-700 font-black">
        Memuat laporan...
      </div>
      <div v-else-if="filteredRows.length === 0" class="text-center py-16 text-slate-700 font-black">
        Tidak ada data transaksi.
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr :class="isDark ? 'bg-slate-800 text-slate-200 border-slate-700' : 'bg-[#FFE566] text-black border-b-2 border-black'" class="font-black uppercase text-[10px] tracking-wider">
              <th class="px-4 py-3">Kode Pesanan</th>
              <th class="px-4 py-3">Judul Buku</th>
              <th class="px-4 py-3 text-center">Tanggal</th>
              <th class="px-4 py-3 text-center">Jumlah</th>
              <th class="px-4 py-3 text-right">Total Harga</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-black/20 font-bold">
            <tr 
              v-for="(item, idx) in paginatedRows" 
              :key="idx" 
              :class="isDark ? 'hover:bg-slate-800/60' : 'hover:bg-white/80'" 
              class="transition-colors"
            >
              <td class="px-4 py-3 font-mono font-black text-slate-900 uppercase">
                <span class="bg-white border border-black px-1.5 py-0.5 rounded shadow-[1px_1px_0px_#1A1A1A]">{{ item.kode_pesanan }}</span>
              </td>
              <td class="px-4 py-3 font-black" :class="isDark ? 'text-slate-200' : 'text-slate-900'">
                {{ item.judul_buku }}
              </td>
              <td class="px-4 py-3 text-center" :class="isDark ? 'text-slate-400' : 'text-slate-700'">
                {{ item.tanggal }}
              </td>
              <td class="px-4 py-3 text-center font-black" :class="isDark ? 'text-slate-200' : 'text-slate-900'">
                {{ item.jumlah }}
              </td>
              <td class="px-4 py-3 text-right font-black" :class="isDark ? 'text-indigo-400' : 'text-slate-900'">
                Rp {{ formatPrice(item.total_harga) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Controls -->
      <div v-if="totalPages > 1" class="p-4 border-t" :class="isDark ? 'border-slate-800' : 'border-black'">
        <div class="flex items-center justify-center gap-2 flex-wrap">
          <button 
            @click="currentPage = 1"
            :disabled="currentPage === 1"
            :class="isDark ? 'bg-slate-800 text-slate-300 disabled:opacity-50' : 'bg-[#FFE566] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A] disabled:opacity-50'"
            class="px-3 py-2 rounded-lg font-black text-xs transition-all"
          >
            « Awal
          </button>

          <button 
            @click="currentPage--"
            :disabled="currentPage === 1"
            :class="isDark ? 'bg-slate-800 text-slate-300 disabled:opacity-50' : 'bg-[#FFE566] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A] disabled:opacity-50'"
            class="px-3 py-2 rounded-lg font-black text-xs transition-all"
          >
            ‹ Sebelumnya
          </button>

          <div class="flex items-center gap-1">
            <button 
              v-for="page in totalPages"
              :key="page"
              @click="currentPage = page"
              :class="currentPage === page
                ? (isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]')
                : (isDark ? 'bg-slate-800 text-slate-300' : 'bg-white text-black border border-black')
              "
              class="w-8 h-8 rounded-lg font-black text-xs transition-all flex items-center justify-center"
            >
              {{ page }}
            </button>
          </div>

          <button 
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            :class="isDark ? 'bg-slate-800 text-slate-300 disabled:opacity-50' : 'bg-[#FFE566] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A] disabled:opacity-50'"
            class="px-3 py-2 rounded-lg font-black text-xs transition-all"
          >
            Berikutnya ›
          </button>

          <button 
            @click="currentPage = totalPages"
            :disabled="currentPage === totalPages"
            :class="isDark ? 'bg-slate-800 text-slate-300 disabled:opacity-50' : 'bg-[#FFE566] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A] disabled:opacity-50'"
            class="px-3 py-2 rounded-lg font-black text-xs transition-all"
          >
            Akhir »
          </button>

          <span :class="isDark ? 'text-slate-400' : 'text-slate-700'" class="text-xs font-bold ml-2">
            Hal {{ currentPage }} dari {{ totalPages }}
          </span>
        </div>
      </div>
    </div>

    <!-- Download Button -->
    <div class="flex justify-end gap-3">
      <a 
        :href="excelExportUrl" 
        target="_blank"
        :class="isDark ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-[#10b981] text-white border-2 border-black shadow-[4px_4px_0px_#1A1A1A] hover:translate-x-[-2px] hover:translate-y-[-2px] active:translate-x-[2px] active:translate-y-[2px]'"
        class="px-8 py-3 rounded-2xl font-black text-sm flex items-center gap-2 transition-transform"
      >
        📊 Download Excel
      </a>
      <a 
        :href="pdfExportUrl" 
        target="_blank"
        :class="isDark ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[4px_4px_0px_#1A1A1A] hover:translate-x-[-2px] hover:translate-y-[-2px] active:translate-x-[2px] active:translate-y-[2px]'"
        class="px-8 py-3 rounded-2xl font-black text-sm flex items-center gap-2 transition-transform"
      >
        📥 Download PDF
      </a>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Search as LucideSearch, Calendar as LucideCalendar } from 'lucide-vue-next'

definePageMeta({
  middleware: 'admin'
})

const api = useApi()
const { isDark } = useTheme()
const loading = ref(true)
const searchQuery = ref('')
const filterYear = ref(new Date().getFullYear())
const currentPage = ref(1)
const itemsPerPage = 6

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
    // Build date range for the selected year
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
    currentPage.value = 1 // Reset ke page 1 saat fetch
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

// Flatten order items for table display matching mockup columns
const tableRows = computed(() => {
  const rows: any[] = []
  orders.value.forEach(order => {
    // Check if this is the old format (with items array) or new format (with orderDetails)
    if (order.items && Array.isArray(order.items) && order.items.length > 0) {
      // Old format with items
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
      // New format with orderDetails
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
      // Fallback format
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
  return Math.ceil(filteredRows.value.length / itemsPerPage)
})

onMounted(fetchReport)
</script>
