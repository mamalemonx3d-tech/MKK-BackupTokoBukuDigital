<template>
  <div class="space-y-6 font-['Space_Grotesk']">
    <!-- Search / USB Scanner / Camera Scanner Header -->
    <div 
      :class="isDark ? 'bg-slate-900 border-slate-800 text-slate-100' : 'bg-[#FFF8EC] border-2 border-black shadow-[4px_4px_0px_#1A1A1A] text-slate-900'"
      class="p-5 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-4 transition-colors duration-300"
    >
      <div class="flex items-center gap-2 w-full md:w-auto flex-1">
        <div class="relative w-full max-w-md">
          <input 
            ref="scannerInput"
            v-model="scanQuery" 
            type="text" 
            placeholder="Scan QR Code / Masukkan Kode (A021)..." 
            :class="isDark ? 'bg-slate-950 border-slate-700 text-white placeholder-slate-500 focus:bg-slate-900' : 'bg-white border-2 border-black text-slate-900 shadow-[2px_2px_0px_#1A1A1A]'"
            class="w-full pl-10 pr-4 py-2.5 rounded-full text-xs font-black outline-none transition-colors"
            @keyup.enter="handleScanSubmit"
          />
          <LucideQrCode class="w-4 h-4 text-slate-900 absolute left-3.5 top-3" />
        </div>
        <button 
          @click="startCameraScanner" 
          :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A] hover:translate-x-[-1px] active:translate-x-[1px]'"
          class="px-4 py-2.5 rounded-full font-black text-xs flex items-center gap-1.5 whitespace-nowrap transition-all"
        >
          📷 Scan Kamera HP / Web
        </button>
      </div>

      <div class="flex items-center gap-2 flex-wrap">
        <span class="text-xs font-black uppercase" :class="isDark ? 'text-slate-400' : 'text-slate-700'">Status:</span>
        <button @click="filterStatus = ''" :class="filterStatus === '' ? (isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]') : (isDark ? 'bg-slate-800 text-slate-300' : 'bg-white text-slate-800 border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A]')" class="px-3 py-1.5 text-xs font-black rounded-xl transition-all">Semua</button>
        <button @click="filterStatus = 'pending'" :class="filterStatus === 'pending' ? 'bg-[#FFE566] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]' : (isDark ? 'bg-slate-800 text-slate-300' : 'bg-white text-slate-800 border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A]')" class="px-3 py-1.5 text-xs font-black rounded-xl transition-all">Pending</button>
        <button @click="filterStatus = 'confirmed'" :class="filterStatus === 'confirmed' ? 'bg-[#D4B8FF] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]' : (isDark ? 'bg-slate-800 text-slate-300' : 'bg-white text-slate-800 border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A]')" class="px-3 py-1.5 text-xs font-black rounded-xl transition-all">Confirmed</button>
        <button @click="filterStatus = 'completed'" :class="filterStatus === 'completed' ? 'bg-[#A8E6CF] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]' : (isDark ? 'bg-slate-800 text-slate-300' : 'bg-white text-slate-800 border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A]')" class="px-3 py-1.5 text-xs font-black rounded-xl transition-all">Completed</button>
      </div>
    </div>

    <!-- Modal Scanner Kamera Web / HP -->
    <div v-if="showCameraModal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center p-4">
      <div 
        :class="isDark ? 'bg-slate-900 border-slate-700 text-white' : 'bg-[#FFF8EC] border-3 border-black text-slate-900 shadow-[6px_6px_0px_#1A1A1A]'"
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
            <span class="text-black text-xs font-black bg-[#C8F53F] px-3 py-1 rounded-full border-2 border-black shadow-[2px_2px_0px_#1A1A1A]">Posisikan QR di dalam Kotak</span>
          </div>
        </div>

        <p class="text-xs font-black" :class="isDark ? 'text-slate-400' : 'text-slate-700'">Arahkan kamera ke QR Code di layar HP pembeli untuk memindai otomatis.</p>
      </div>
    </div>

    <!-- Banner Notifikasi Scan Berhasil -->
    <div v-if="scanSuccessBanner" class="bg-[#C8F53F] border-3 border-black text-black p-5 rounded-3xl shadow-[6px_6px_0px_#1A1A1A] flex items-center justify-between gap-4 animate-bounce">
      <div class="flex items-center gap-3">
        <span class="text-3xl">✅</span>
        <div>
          <h3 class="font-black text-lg tracking-wide uppercase text-slate-900">Scan QR Code Berhasil & Dicatat di Admin!</h3>
          <p class="text-xs font-bold">
            Kode Pesanan: <span class="font-mono bg-white px-2 py-0.5 rounded border border-black text-black font-black">{{ scanSuccessCode }}</span>. Transaksi terdeteksi! Mohon proses pembayaran / konfirmasi dan berikan Struk atau Invoice langsung kepada pelanggan.
          </p>
        </div>
      </div>
      <button @click="scanSuccessBanner = false" class="bg-black text-white hover:bg-slate-800 font-black text-xs px-3.5 py-2 rounded-xl border-2 border-black">
        ✕ Tutup Notifikasi
      </button>
    </div>

    <!-- Scanned / Active Selected Order View -->
    <div v-if="selectedOrder" id="selected-order-section" class="order-card space-y-4">
      <div 
        :class="isDark ? 'bg-slate-900 border-slate-700 text-slate-100' : 'bg-[#FFF8EC] border-2.5 border-black shadow-[5px_5px_0px_#1A1A1A] text-slate-900'"
        class="rounded-3xl p-6 relative transition-colors duration-300"
      >
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-2xl font-black tracking-tight" :class="isDark ? 'text-white' : 'text-slate-900'">
              Kode Pesanan : <span :class="isDark ? 'text-indigo-400' : 'bg-[#FFE566] text-black px-2 py-0.5 rounded-lg border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'" class="font-mono uppercase">{{ selectedOrder.kode_pesanan }}</span>
            </h2>
            <p class="text-xs font-bold mt-1" :class="isDark ? 'text-slate-400' : 'text-slate-700'">Pelanggan: {{ selectedOrder.user_name || selectedOrder.pelanggan || 'User' }}</p>
          </div>
          <div class="flex items-center gap-2">
            <span :class="getStatusBadgeClass(selectedOrder.status)" class="text-xs font-black uppercase px-3 py-1 rounded-full border-2 border-black shadow-[2px_2px_0px_#1A1A1A]">
              {{ selectedOrder.status }}
            </span>
            <button @click="selectedOrder = null" :class="isDark ? 'bg-slate-800 text-slate-300' : 'bg-[#FFB7B2] text-black border-2 border-black shadow-[1.5px_1.5px_0px_#1A1A1A]'" class="text-xs font-black px-2.5 py-1 rounded-xl">
              ✕ Tutup
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-center">
          <!-- Table Section -->
          <div class="lg:col-span-3 overflow-x-auto">
            <table 
              :class="isDark ? 'bg-slate-950 border-slate-800 text-slate-200' : 'bg-white border-2 border-black shadow-[2px_2px_0px_#1A1A1A] text-slate-900'"
              class="w-full text-left text-xs"
            >
              <thead>
                <tr :class="isDark ? 'bg-slate-800 text-slate-200 border-slate-700' : 'bg-[#FFE566] text-black border-b-2 border-black'" class="font-black uppercase text-[10px] tracking-wider">
                  <th class="px-4 py-2.5">Judul Buku</th>
                  <th class="px-4 py-2.5">Tanggal</th>
                  <th class="px-4 py-2.5 text-center">Qty</th>
                  <th class="px-4 py-2.5 text-right">Harga Satuan</th>
                  <th class="px-4 py-2.5 text-right">Subtotal</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-black/20 font-bold">
                <tr v-for="detail in (selectedOrder.details || selectedOrder.items || [])" :key="detail.id" class="hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-2.5 font-black">{{ detail.buku?.judul || detail.nama_buku }}</td>
                  <td class="px-4 py-2.5 text-slate-600">{{ formatDate(selectedOrder.created_at) }}</td>
                  <td class="px-4 py-2.5 text-center font-black">{{ detail.qty }}</td>
                  <td class="px-4 py-2.5 text-right">Rp.{{ formatPrice(detail.harga_satuan) }}</td>
                  <td class="px-4 py-2.5 text-right font-black">Rp.{{ formatPrice(detail.subtotal) }}</td>
                </tr>
                <tr :class="isDark ? 'bg-slate-800 text-white' : 'bg-[#C8F53F] text-black border-t-2 border-black'" class="font-black text-xs">
                  <td colspan="5" class="px-4 py-2.5 text-right">
                    Total Tagihan : Rp. {{ formatPrice(selectedOrder.total_harga) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- QR Code Display -->
          <div :class="isDark ? 'bg-slate-950 border-slate-800' : 'bg-white border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'" class="lg:col-span-1 flex flex-col items-center justify-center p-2 rounded-2xl">
            <QrCodeDisplay :value="selectedOrder.kode_pesanan" :size="130" show-label />
            <p class="text-[10px] text-slate-900 font-black text-center mt-1">✔ QR Code Terverifikasi</p>
          </div>
        </div>
      </div>

      <!-- Action Row for Cashier -->
      <div class="flex flex-col sm:flex-row gap-3 print:hidden">
        <button 
          v-if="selectedOrder.status === 'pending'"
          @click="confirmOrder(selectedOrder)"
          class="flex-1 bg-[#D4B8FF] hover:bg-[#c39eff] border-2 border-black text-black font-black text-sm py-3 rounded-2xl shadow-[3px_3px_0px_#1A1A1A] transition-all"
        >
          ✔ Konfirmasi Pesanan
        </button>

        <button 
          v-if="selectedOrder.status === 'confirmed'"
          @click="openPayModal(selectedOrder)"
          class="flex-1 bg-[#C8F53F] hover:bg-[#b8e82f] border-2 border-black text-black font-black text-sm py-3 rounded-2xl shadow-[3px_3px_0px_#1A1A1A] transition-all"
        >
          💵 Bayar Uang Tunai
        </button>

        <button 
          @click="printReceipt" 
          :class="isDark ? 'bg-slate-800 text-white border-slate-700' : 'bg-[#FFE566] text-black border-2 border-black shadow-[3px_3px_0px_#1A1A1A]'"
          class="flex-1 font-black text-sm py-3 rounded-2xl transition-all"
        >
          🖨️ Print Struk Kasir
        </button>

        <button 
          @click="downloadInvoicePdf(selectedOrder.id)" 
          class="flex-1 bg-[#FFD4A3] hover:bg-[#ffc68a] border-2 border-black text-black font-black text-sm py-3 rounded-2xl shadow-[3px_3px_0px_#1A1A1A] transition-all"
        >
          📄 Unduh Invoice PDF
        </button>
      </div>
    </div>

    <!-- Orders Grid Table -->
    <div :class="isDark ? 'bg-slate-900 border-slate-800 text-slate-100' : 'bg-[#FFF8EC] border-2.5 border-black shadow-[4px_4px_0px_#1A1A1A] text-slate-900'" class="rounded-3xl overflow-hidden">
      <div v-if="loading" class="text-center py-16 text-slate-700 font-black">Memuat daftar pesanan...</div>
      <div v-else-if="filteredOrders.length === 0" class="text-center py-16 text-slate-700 font-black">
        Tidak ada pesanan ditemukan.
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr :class="isDark ? 'bg-slate-800 text-slate-200 border-slate-700' : 'bg-[#FFE566] text-black border-b-2 border-black'" class="font-black uppercase text-[10px] tracking-wider">
              <th class="px-4 py-3">Kode Pesanan</th>
              <th class="px-4 py-3">Pelanggan</th>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Total Tagihan</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-black/20 font-bold">
            <tr v-for="order in paginatedOrders" :key="order.id" :class="isDark ? 'hover:bg-slate-800/60' : 'hover:bg-white/80'" class="transition-colors">
              <td class="px-4 py-3 font-mono font-black text-slate-900 uppercase">
                <span class="bg-white border border-black px-1.5 py-0.5 rounded shadow-[1px_1px_0px_#1A1A1A]">{{ order.kode_pesanan }}</span>
              </td>
              <td class="px-4 py-3 font-black">{{ order.user_name || order.pelanggan || 'User' }}</td>
              <td class="px-4 py-3 text-slate-700">{{ formatDate(order.created_at) }}</td>
              <td class="px-4 py-3 font-black text-slate-900">Rp. {{ formatPrice(order.total_harga) }}</td>
              <td class="px-4 py-3">
                <span :class="getStatusBadgeClass(order.status)" class="text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full border border-black shadow-[1.5px_1.5px_0px_#1A1A1A]">
                  {{ order.status }}
                </span>
              </td>
              <td class="px-4 py-3 text-right">
                <button @click="selectOrder(order)" :class="isDark ? 'bg-slate-800 text-indigo-300' : 'bg-[#C8F53F] hover:bg-[#b8e82f] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'" class="px-3 py-1.5 rounded-xl font-black text-xs transition-all active:translate-x-[1px] active:translate-y-[1px]">
                  Detail Struk
                </button>
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

    <!-- Modal Pembayaran Tunai -->
    <div v-if="showPayModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div :class="isDark ? 'bg-slate-900 border-slate-700 text-slate-100' : 'bg-[#FFF8EC] border-3 border-black text-slate-900 shadow-[6px_6px_0px_#1A1A1A]'" class="rounded-3xl p-6 max-w-md w-full space-y-4">
        <div class="border-b-2 border-black pb-3">
          <h3 class="font-black text-lg">Kasir Pembayaran Tunai</h3>
          <p class="text-xs font-bold text-slate-700">Kode Pesanan: <span class="font-mono font-black bg-[#FFE566] px-1.5 py-0.5 rounded border border-black uppercase">{{ activePayOrder?.kode_pesanan }}</span></p>
        </div>

        <div :class="isDark ? 'bg-slate-950 border-slate-800' : 'bg-white border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'" class="p-4 rounded-2xl text-center">
          <span class="text-xs text-slate-600 block font-bold">Total Tagihan Harus Dibayar</span>
          <span class="text-2xl font-black text-black">Rp. {{ formatPrice(activePayOrder?.total_harga || 0) }}</span>
        </div>

        <form @submit.prevent="processPayment" class="space-y-4">
  <div>
    <label class="block text-xs font-black mb-2" :class="isDark ? 'text-slate-300' : 'text-slate-900'">
      Pilih Nominal Uang Tunai
    </label>

    <!-- Quick-select denomination buttons -->
    <div class="grid grid-cols-4 gap-2 mb-3">
      <button
        v-for="d in denominations"
        :key="d"
        type="button"
        @click="addCash(d)"
        :class="isDark ? 'bg-slate-800 text-slate-200 hover:bg-slate-700' : 'bg-white text-slate-900 border-2 border-black shadow-[2px_2px_0px_#1A1A1A] hover:bg-[#FFE566] active:translate-x-[1px] active:translate-y-[1px]'"
        class="py-2 rounded-xl font-black text-[11px] transition-all"
      >
        +{{ formatShort(d) }}
      </button>
    </div>

    <div class="flex gap-2 mb-3">
      <button
        type="button"
        @click="setExactCash"
        :class="isDark ? 'bg-indigo-600 text-white' : 'bg-[#C8F53F] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'"
        class="flex-1 py-2 rounded-xl font-black text-[11px] transition-all"
      >
        💵 Uang Pas
      </button>
      <button
        type="button"
        @click="resetCash"
        :class="isDark ? 'bg-slate-800 text-slate-300' : 'bg-[#FFB7B2] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'"
        class="flex-1 py-2 rounded-xl font-black text-[11px] transition-all"
      >
        ↺ Reset
      </button>
    </div>

    <label class="block text-xs font-black mb-1" :class="isDark ? 'text-slate-300' : 'text-slate-900'">
      Atau Ketik Manual (Rp)
    </label>
    <input 
      v-model.number="cashInput" 
      type="number" 
      required 
      :min="activePayOrder?.total_harga" 
      :class="isDark ? 'bg-slate-950 border-slate-700 text-white' : 'bg-white border-2 border-black text-slate-900 shadow-[2px_2px_0px_#1A1A1A]'"
      class="w-full px-4 py-3 text-base font-black rounded-xl outline-none" 
      placeholder="100000" 
    />
  </div>

  <div :class="isDark ? 'bg-slate-950 border-slate-800' : 'bg-[#C8F53F] border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'" class="p-3.5 rounded-xl flex justify-between items-center text-xs">
    <span class="font-black">Uang Kembalian:</span>
    <span :class="computedKembalian >= 0 ? 'text-black font-black text-base' : 'text-rose-600 font-black'">
      Rp. {{ formatPrice(computedKembalian) }}
    </span>
  </div>

  <div class="flex justify-end gap-3 pt-2">
    <button type="button" @click="showPayModal = false" :class="isDark ? 'bg-slate-800 text-slate-300' : 'bg-[#FFB7B2] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]'" class="px-4 py-2 text-xs font-black rounded-xl">
      Batal
    </button>
    <button type="submit" :disabled="submittingPay || computedKembalian < 0" class="px-5 py-2.5 text-xs font-black text-black bg-[#C8F53F] hover:bg-[#b8e82f] border-2 border-black rounded-xl shadow-[3px_3px_0px_#1A1A1A] transition-all disabled:opacity-50">
      Selesaikan Pembayaran & Lunas
    </button>
  </div>
</form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { QrCode as LucideQrCode } from 'lucide-vue-next'
import jsQR from 'jsqr'

definePageMeta({
  middleware: 'admin'
})

const api = useApi()
const orders = ref<any[]>([])
const loading = ref(true)
const filterStatus = ref('')
const currentPage = ref(1)
const itemsPerPage = 6

const scanQuery = ref('')
const selectedOrder = ref<any>(null)
const scannerInput = ref<HTMLInputElement | null>(null)

const scanSuccessBanner = ref(false)
const scanSuccessCode = ref('')

const showPayModal = ref(false)
const activePayOrder = ref<any>(null)
const cashInput = ref<number | ''>('')
const denominations = [1000, 2000, 5000, 10000, 20000, 50000, 100000]

const formatShort = (val: number) => {
  if (val >= 1000) return `${val / 1000}rb`
  return `${val}`
}

const addCash = (amount: number) => {
  cashInput.value = (Number(cashInput.value) || 0) + amount
}

const setExactCash = () => {
  cashInput.value = activePayOrder.value?.total_harga || 0
}

const resetCash = () => {
  cashInput.value = 0
}
const submittingPay = ref(false)

// Camera scanner state
const showCameraModal = ref(false)
const videoRef = ref<HTMLVideoElement | null>(null)
const canvasRef = ref<HTMLCanvasElement | null>(null)
let cameraStream: MediaStream | null = null
let animationFrameId: number | null = null

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
    case 'pending': return 'bg-amber-200 text-amber-950 border-amber-400'
    case 'confirmed': return 'bg-sky-200 text-sky-950 border-sky-400'
    case 'completed': return 'bg-emerald-200 text-emerald-950 border-emerald-400'
    default: return 'bg-slate-200 text-slate-900 border-slate-400'
  }
}

const filteredOrders = computed(() => {
  if (!filterStatus.value) return orders.value
  return orders.value.filter(o => o.status === filterStatus.value)
})

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredOrders.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredOrders.value.length / itemsPerPage)
})

const computedKembalian = computed(() => {
  if (!activePayOrder.value || !cashInput.value) return 0
  return Number(cashInput.value) - Number(activePayOrder.value.total_harga)
})

const fetchOrders = async () => {
  loading.value = true
  try {
    const res = await api.get('/api/orders')
    orders.value = res.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const selectOrder = (order: any) => {
  selectedOrder.value = order
}

const handleScanSubmit = async () => {
  const query = scanQuery.value.trim().toLowerCase()
  if (!query) return

  try {
    const res = await api.post('/api/orders/scan', { kode_pesanan: query })
    const matched = res.data

    selectedOrder.value = matched
    scanSuccessCode.value = matched.kode_pesanan
    scanSuccessBanner.value = true
    scanQuery.value = ''
    
    // Play audio beep sound on scan success
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
        // Ignore audio context errors
      }
    }

    // Toast notification for Admin
    const toast = useToast()
    toast.success(`✅ Scan Berhasil & Tersimpan di Database!\n\nKode Pesanan: ${matched.kode_pesanan}\nPelanggan: ${matched.user_name || matched.pelanggan || 'User'}\nStatus DB: ${matched.status.toUpperCase()}\nTotal: Rp. ${formatPrice(matched.total_harga)}\n\nStatus pesanan otomatis diperbarui ke 'CONFIRMED'.`)
    await fetchOrders()
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || `Pesanan dengan kode "${scanQuery.value}" tidak ditemukan di Database!`)
  }
}

// Camera Scanner Logic
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
        handleScanSubmit()
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

const confirmOrder = async (order: any) => {
  try {
    const toast = useToast()
    const res = await api.put(`/api/admin/orders/${order.id}/confirm`)
    toast.success(res.message || 'Pesanan berhasil dikonfirmasi!')
    await fetchOrders()
    if (selectedOrder.value && selectedOrder.value.id === order.id) {
      selectedOrder.value.status = 'confirmed'
    }
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal mengonfirmasi pesanan.')
  }
}

const openPayModal = (order: any) => {
  activePayOrder.value = order
  cashInput.value = 0
  showPayModal.value = true
}

const processPayment = async () => {
  if (!activePayOrder.value || !cashInput.value) return

  submittingPay.value = true
  try {
    const toast = useToast()
    const res = await api.put(`/api/admin/orders/${activePayOrder.value.id}/pay`, {
      cash: cashInput.value
    })
    showPayModal.value = false
    toast.success(res.message || 'Pembayaran berhasil diselesaikan! Struk / Invoice dapat segera diserahkan kepada pelanggan.')
    await fetchOrders()
    if (selectedOrder.value && selectedOrder.value.id === activePayOrder.value.id) {
      selectedOrder.value.status = 'completed'
      selectedOrder.value.cash = cashInput.value
    }
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal memproses pembayaran.')
  } finally {
    submittingPay.value = false
  }
}

const printReceipt = () => {
  window.print()
}

const downloadInvoicePdf = (orderId: number) => {
  const url = `${api.apiBase}/api/orders/${orderId}/invoice-pdf`
  window.open(url, '_blank')
}

// Global listener for USB hardware QR code scanner
let scannerBuffer = ''
let lastKeyTime = Date.now()

const onGlobalKeydown = (e: KeyboardEvent) => {
  const currentTime = Date.now()
  // Hardware scanners type very rapidly (< 50ms per key)
  if (currentTime - lastKeyTime > 100) {
    scannerBuffer = ''
  }
  lastKeyTime = currentTime

  if (e.key === 'Enter') {
    if (scannerBuffer.length > 2) {
      scanQuery.value = scannerBuffer
      handleScanSubmit()
      scannerBuffer = ''
    }
  } else if (e.key.length === 1) {
    scannerBuffer += e.key
  }
}

onMounted(() => {
  fetchOrders()
  if (process.client) {
    window.addEventListener('keydown', onGlobalKeydown)
  }
})

// Watch filterStatus to reset pagination
watch(filterStatus, () => {
  currentPage.value = 1
})

onUnmounted(() => {
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
