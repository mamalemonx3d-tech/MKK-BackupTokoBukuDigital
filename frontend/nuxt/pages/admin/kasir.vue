<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header Section with Scan & Camera Controls -->
    <div
      :class="isDark ? 'bg-zinc-900 border-zinc-800 text-zinc-100' : 'bg-white border-2 border-black text-black shadow-[4px_4px_0px_#000000]'"
      class="p-5 sm:p-6 rounded-3xl border-2 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 transition-colors duration-300"
    >
      <div class="space-y-1">
        <div 
          class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider border"
          :class="isDark ? 'bg-zinc-950 text-zinc-300 border-zinc-700' : 'bg-zinc-100 text-black border-black shadow-[1px_1px_0px_#000000]'"
        >
          <span>🛒</span>
          <span>Point of Sale & Kasir</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
          Kasir & Scan QR
        </h1>
        <p class="text-xs font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
          Pindai QR Code pesanan pembeli atau masukkan kode pesanan secara manual untuk konfirmasi dan pembayaran.
        </p>
      </div>

      <!-- Scan Bar & Camera Trigger -->
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
        <div class="relative flex-1 min-w-[240px]">
          <input 
            ref="scannerInput"
            v-model="scanQuery" 
            type="text" 
            placeholder="Scan QR / Ketik Kode (Contoh: A021)..." 
            :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-zinc-50 border-2 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[3px_3px_0px_#000000] placeholder-zinc-400'"
            class="w-full pl-9 pr-4 py-2.5 text-xs font-mono font-bold rounded-xl outline-none transition-all border-2"
            @keyup.enter="handleScanSubmit"
          />
          <LucideQrCode class="w-4 h-4 text-zinc-400 absolute left-3 top-3 pointer-events-none" />
        </div>

        <button 
          @click="startCameraScanner" 
          :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff] hover:bg-zinc-200' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
          class="px-4 py-2.5 rounded-xl font-black text-xs flex items-center justify-center gap-2 whitespace-nowrap transition-all border-2 cursor-pointer hover:-translate-y-0.5 active:translate-y-0"
        >
          <span>📷</span>
          <span>Buka Kamera Scan</span>
        </button>
      </div>
    </div>

    <!-- Quick Status Filter Tabs -->
    <div
      :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[3px_3px_0px_#000000]'"
      class="p-3 sm:p-4 rounded-2xl border-2 flex items-center justify-between gap-3 overflow-x-auto transition-colors"
    >
      <div class="flex items-center gap-2">
        <span class="text-xs font-mono font-bold uppercase text-zinc-400 mr-1 hidden sm:inline">Filter Status:</span>
        <button 
          @click="filterStatus = ''" 
          :class="filterStatus === '' 
            ? (isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]') 
            : (isDark ? 'bg-zinc-950 text-zinc-400 border-zinc-800 hover:text-white' : 'bg-zinc-100 text-zinc-700 border-zinc-300 hover:text-black')"
          class="px-3.5 py-1.5 text-xs font-black rounded-xl border-2 transition-all cursor-pointer whitespace-nowrap"
        >
          Semua Pesanan ({{ orders.length }})
        </button>

        <button 
          @click="filterStatus = 'pending'" 
          :class="filterStatus === 'pending' 
            ? (isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]') 
            : (isDark ? 'bg-zinc-950 text-zinc-400 border-zinc-800 hover:text-white' : 'bg-zinc-100 text-zinc-700 border-zinc-300 hover:text-black')"
          class="px-3.5 py-1.5 text-xs font-black rounded-xl border-2 transition-all cursor-pointer whitespace-nowrap"
        >
          Menunggu Konfirmasi ({{ pendingOrdersCount }})
        </button>

        <button 
          @click="filterStatus = 'confirmed'" 
          :class="filterStatus === 'confirmed' 
            ? (isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]') 
            : (isDark ? 'bg-zinc-950 text-zinc-400 border-zinc-800 hover:text-white' : 'bg-zinc-100 text-zinc-700 border-zinc-300 hover:text-black')"
          class="px-3.5 py-1.5 text-xs font-black rounded-xl border-2 transition-all cursor-pointer whitespace-nowrap"
        >
          Siap Bayar ({{ confirmedOrdersCount }})
        </button>

        <button 
          @click="filterStatus = 'completed'" 
          :class="filterStatus === 'completed' 
            ? (isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]') 
            : (isDark ? 'bg-zinc-950 text-zinc-400 border-zinc-800 hover:text-white' : 'bg-zinc-100 text-zinc-700 border-zinc-300 hover:text-black')"
          class="px-3.5 py-1.5 text-xs font-black rounded-xl border-2 transition-all cursor-pointer whitespace-nowrap"
        >
          Selesai / Lunas ({{ completedOrdersCount }})
        </button>
      </div>

      <button
        @click="fetchOrders"
        :class="isDark ? 'bg-zinc-800 text-zinc-300 hover:text-white border-zinc-700' : 'bg-zinc-100 text-black border-2 border-black shadow-[1.5px_1.5px_0px_#000000] hover:bg-zinc-200'"
        class="px-3 py-1.5 rounded-xl text-xs font-black border transition-all cursor-pointer shrink-0"
        title="Refresh Data Pesanan"
      >
        🔄 Refresh
      </button>
    </div>

    <!-- Modal Scanner Kamera Web / HP -->
    <div v-if="showCameraModal" class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 flex items-center justify-center p-4">
      <div 
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white shadow-[0_25px_50px_-12px_rgba(0,0,0,0.8)]' : 'bg-white border-2 border-black text-black shadow-[6px_6px_0px_#000000]'"
        class="rounded-3xl p-6 max-w-md w-full space-y-4 text-center relative border-2"
      >
        <div class="flex justify-between items-center border-b pb-3" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
          <h3 class="font-black text-base sm:text-lg flex items-center gap-2">
            <span>📷</span>
            <span>Pemindai Kamera QR Code</span>
          </h3>
          <button 
            @click="stopCameraScanner" 
            :class="isDark ? 'bg-zinc-800 text-zinc-300 hover:text-white' : 'bg-zinc-100 text-black border border-zinc-300'"
            class="text-xs font-black px-3 py-1.5 rounded-xl cursor-pointer"
          >
            ✕ Tutup
          </button>
        </div>

        <div class="relative aspect-square w-full bg-black rounded-2xl overflow-hidden border-2 border-zinc-700 flex items-center justify-center">
          <video ref="videoRef" class="w-full h-full object-cover"></video>
          <canvas ref="canvasRef" class="hidden"></canvas>
          <div class="absolute inset-0 border-2 border-dashed border-white m-10 rounded-2xl pointer-events-none animate-pulse flex items-center justify-center">
            <span class="text-black text-xs font-black bg-white px-3 py-1 rounded-full border border-black shadow-md">
              Posisikan QR di dalam Kotak
            </span>
          </div>
        </div>

        <p class="text-xs font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
          Arahkan kamera ke QR Code di layar perangkat pembeli untuk mendeteksi secara otomatis.
        </p>
      </div>
    </div>

    <!-- Banner Notifikasi Scan Berhasil -->
    <div 
      v-if="scanSuccessBanner" 
      :class="isDark ? 'bg-zinc-900 border-emerald-500 text-zinc-100' : 'bg-emerald-50 border-2 border-emerald-600 text-emerald-950 shadow-[4px_4px_0px_#059669]'"
      class="p-5 rounded-3xl border-2 flex items-center justify-between gap-4 transition-all"
    >
      <div class="flex items-center gap-3">
        <span class="text-3xl">✅</span>
        <div>
          <h3 class="font-black text-base tracking-wide uppercase">Scan QR Code Berhasil & Terkonfirmasi!</h3>
          <p class="text-xs font-medium mt-0.5">
            Kode Pesanan: <span class="font-mono font-black px-2 py-0.5 rounded border" :class="isDark ? 'bg-black text-white border-zinc-700' : 'bg-white text-black border-black'">{{ scanSuccessCode }}</span>. Transaksi siap diproses di kasir.
          </p>
        </div>
      </div>
      <button 
        @click="scanSuccessBanner = false" 
        :class="isDark ? 'bg-white text-black border-white' : 'bg-black text-white border-black'"
        class="font-black text-xs px-3.5 py-2 rounded-xl border-2 cursor-pointer shrink-0"
      >
        ✕ Tutup
      </button>
    </div>

    <!-- Scanned / Active Selected Order Detail Card -->
    <div v-if="selectedOrder" id="selected-order-section" class="order-card space-y-4">
      <div 
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-100' : 'bg-white border-2 border-black shadow-[4px_4px_0px_#000000] text-black'"
        class="rounded-3xl p-6 relative border-2 transition-colors duration-300"
      >
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b pb-4 mb-5" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
          <div>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
              Kode Pesanan: <span class="font-mono uppercase font-black">{{ selectedOrder.kode_pesanan }}</span>
            </h2>
            <p class="text-xs font-medium mt-1 text-zinc-400">
              Pelanggan: <span class="font-bold" :class="isDark ? 'text-zinc-200' : 'text-black'">{{ selectedOrder.user_name || selectedOrder.pelanggan || 'User' }}</span> • Tanggal: {{ formatDate(selectedOrder.created_at) }}
            </p>
          </div>
          <div class="flex items-center gap-2">
            <span :class="getStatusBadgeClass(selectedOrder.status)" class="text-xs font-black uppercase font-mono px-3 py-1 rounded-full border">
              {{ selectedOrder.status }}
            </span>
            <button 
              @click="selectedOrder = null" 
              :class="isDark ? 'bg-zinc-800 text-zinc-300 hover:text-white' : 'bg-zinc-100 text-black border border-zinc-300 hover:bg-zinc-200'"
              class="text-xs font-black px-3 py-1 rounded-xl cursor-pointer"
            >
              ✕ Tutup Detail
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-center">
          <!-- Table Items Section -->
          <div class="lg:col-span-3 overflow-x-auto">
            <table 
              :class="isDark ? 'bg-zinc-950 border-zinc-800 text-zinc-200' : 'bg-zinc-50 border-2 border-black text-black'"
              class="w-full text-left text-xs rounded-2xl overflow-hidden border"
            >
              <thead>
                <tr :class="isDark ? 'bg-zinc-900 text-zinc-300 border-zinc-800' : 'bg-zinc-200 text-black border-b border-black'" class="font-black font-mono uppercase text-[10px] tracking-wider border-b">
                  <th class="px-4 py-3">Judul Buku</th>
                  <th class="px-4 py-3 text-center">Qty</th>
                  <th class="px-4 py-3 text-right font-mono">Harga Satuan</th>
                  <th class="px-4 py-3 text-right font-mono">Subtotal</th>
                </tr>
              </thead>
              <tbody class="divide-y font-bold" :class="isDark ? 'divide-zinc-800' : 'divide-zinc-200'">
                <tr v-for="detail in (selectedOrder.details || selectedOrder.items || [])" :key="detail.id" class="transition-colors">
                  <td class="px-4 py-3 font-black">{{ detail.buku?.judul || detail.nama_buku }}</td>
                  <td class="px-4 py-3 text-center font-mono font-black">{{ detail.qty }}</td>
                  <td class="px-4 py-3 text-right font-mono text-zinc-400">Rp {{ formatPrice(detail.harga_satuan) }}</td>
                  <td class="px-4 py-3 text-right font-mono font-black" :class="isDark ? 'text-white' : 'text-black'">Rp {{ formatPrice(detail.subtotal) }}</td>
                </tr>
                <tr :class="isDark ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-black border-t-2 border-black'" class="font-black text-xs">
                  <td colspan="4" class="px-4 py-3 text-right font-mono text-sm">
                    Total Tagihan: <span class="font-black">Rp {{ formatPrice(selectedOrder.total_harga) }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- QR Code Display Box -->
          <div :class="isDark ? 'bg-zinc-950 border-zinc-800' : 'bg-zinc-50 border-2 border-black shadow-[2px_2px_0px_#000000]'" class="lg:col-span-1 flex flex-col items-center justify-center p-4 rounded-2xl border">
            <QrCodeDisplay :value="selectedOrder.kode_pesanan" :size="130" show-label />
            <p class="text-[10px] font-mono font-bold text-center mt-2 text-zinc-400">✔ QR Code Terverifikasi</p>
          </div>
        </div>
      </div>

      <!-- Action Buttons Row for Cashier -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 print:hidden">
        <button 
          v-if="selectedOrder.status === 'pending'"
          @click="confirmOrder(selectedOrder)"
          :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
          class="font-black text-xs py-3 px-4 rounded-2xl border-2 transition-all cursor-pointer hover:-translate-y-0.5 active:translate-y-0"
        >
          ✔ Konfirmasi Pesanan
        </button>

        <button 
          v-if="selectedOrder.status === 'confirmed'"
          @click="openPayModal(selectedOrder)"
          :class="isDark ? 'bg-emerald-600 text-white border-emerald-500 shadow-[2px_2px_0px_#ffffff] hover:bg-emerald-500' : 'bg-emerald-600 text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-emerald-700'"
          class="font-black text-xs py-3 px-4 rounded-2xl border-2 transition-all cursor-pointer hover:-translate-y-0.5 active:translate-y-0"
        >
          💵 Bayar Uang Tunai / Kasir
        </button>

        <button 
          @click="printReceipt" 
          :class="isDark ? 'bg-zinc-800 text-zinc-200 border-zinc-700 hover:bg-zinc-700' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-100'"
          class="font-black text-xs py-3 px-4 rounded-2xl border-2 transition-all cursor-pointer hover:-translate-y-0.5 active:translate-y-0"
        >
          🖨️ Cetak Struk Kasir
        </button>

        <button 
          @click="downloadInvoicePdf(selectedOrder.id)" 
          :class="isDark ? 'bg-zinc-800 text-zinc-200 border-zinc-700 hover:bg-zinc-700' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-100'"
          class="font-black text-xs py-3 px-4 rounded-2xl border-2 transition-all cursor-pointer hover:-translate-y-0.5 active:translate-y-0"
        >
          📄 Unduh Invoice PDF
        </button>
      </div>
    </div>

    <!-- Orders Grid Table Section -->
    <div 
      :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[4px_4px_0px_#000000]'" 
      class="rounded-3xl overflow-hidden border-2 transition-colors duration-300"
    >
      <div v-if="loading" :class="isDark ? 'text-zinc-500' : 'text-zinc-600'" class="text-center py-16 text-xs font-black flex flex-col items-center gap-2">
        <span class="text-2xl animate-spin">⏳</span>
        <span>Memuat data antrean pesanan kasir...</span>
      </div>

      <div v-else-if="filteredOrders.length === 0" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'" class="text-center py-16 text-xs font-bold space-y-2">
        <div class="text-4xl">🛒</div>
        <p class="text-sm font-black" :class="isDark ? 'text-white' : 'text-black'">Tidak ada pesanan ditemukan</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr :class="isDark ? 'bg-zinc-950 text-zinc-300 border-zinc-800' : 'bg-zinc-100 text-black border-b-2 border-black'" class="font-black font-mono uppercase text-[10px] tracking-wider border-b">
              <th class="px-4 py-3.5">Kode Pesanan</th>
              <th class="px-4 py-3.5">Nama Pelanggan</th>
              <th class="px-4 py-3.5">Tanggal</th>
              <th class="px-4 py-3.5 font-mono">Total Tagihan</th>
              <th class="px-4 py-3.5 text-center">Status</th>
              <th class="px-4 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y font-bold" :class="isDark ? 'divide-zinc-800/80' : 'divide-zinc-200'">
            <tr v-for="order in paginatedOrders" :key="order.id" :class="isDark ? 'hover:bg-zinc-800/50' : 'hover:bg-zinc-50/90'" class="transition-colors group">
              <!-- Kode Pesanan -->
              <td class="px-4 py-3.5 font-mono font-black uppercase">
                <span 
                  :class="isDark ? 'bg-zinc-950 border-zinc-700 text-zinc-200' : 'bg-white border border-black text-black shadow-[1px_1px_0px_#000000]'" 
                  class="px-2 py-0.5 rounded-lg border inline-block"
                >
                  {{ order.kode_pesanan }}
                </span>
              </td>

              <!-- Pelanggan -->
              <td class="px-4 py-3.5 font-black" :class="isDark ? 'text-white' : 'text-black'">
                {{ order.user_name || order.pelanggan || 'User' }}
              </td>

              <!-- Tanggal -->
              <td class="px-4 py-3.5 font-mono text-zinc-400">
                {{ formatDate(order.created_at) }}
              </td>

              <!-- Total Tagihan -->
              <td class="px-4 py-3.5 font-mono font-black" :class="isDark ? 'text-white' : 'text-black'">
                Rp {{ formatPrice(order.total_harga) }}
              </td>

              <!-- Status Badge -->
              <td class="px-4 py-3.5 text-center whitespace-nowrap">
                <span :class="getStatusBadgeClass(order.status)" class="text-[10px] font-black uppercase font-mono px-3 py-0.5 rounded-full border inline-block">
                  {{ order.status }}
                </span>
              </td>

              <!-- Aksi -->
              <td class="px-4 py-3.5 text-right">
                <button 
                  @click="selectOrder(order)" 
                  :class="isDark ? 'bg-zinc-800 text-zinc-200 border-zinc-700 hover:bg-zinc-700' : 'bg-black text-white border-black shadow-[1.5px_1.5px_0px_#000000] hover:bg-zinc-800'"
                  class="px-3.5 py-1.5 rounded-xl font-black text-xs transition-all border cursor-pointer hover:-translate-y-0.5 active:translate-y-0"
                >
                  Detail Struk &rarr;
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Controls -->
      <div v-if="totalPages > 1" class="p-4 border-t flex items-center justify-between flex-wrap gap-2 text-xs" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
        <span class="font-mono text-zinc-400">
          Halaman {{ currentPage }} dari {{ totalPages }}
        </span>

        <div class="flex items-center gap-1.5 flex-wrap">
          <button 
            @click="currentPage = 1"
            :disabled="currentPage === 1"
            :class="isDark ? 'bg-zinc-800 text-zinc-300 disabled:opacity-40' : 'bg-white text-black border border-zinc-300 disabled:opacity-40'"
            class="px-2.5 py-1.5 rounded-lg font-mono font-bold transition-all cursor-pointer disabled:cursor-not-allowed"
          >
            « Awal
          </button>

          <button 
            @click="currentPage--"
            :disabled="currentPage === 1"
            :class="isDark ? 'bg-zinc-800 text-zinc-300 disabled:opacity-40' : 'bg-white text-black border border-zinc-300 disabled:opacity-40'"
            class="px-2.5 py-1.5 rounded-lg font-mono font-bold transition-all cursor-pointer disabled:cursor-not-allowed"
          >
            ‹ Sebelumnya
          </button>

          <button 
            v-for="page in totalPages"
            :key="page"
            @click="currentPage = page"
            :class="currentPage === page
              ? (isDark ? 'bg-white text-black border-white shadow-[1px_1px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[1px_1px_0px_#000000]')
              : (isDark ? 'bg-zinc-900 text-zinc-400 border-zinc-800' : 'bg-white text-black border border-zinc-300')
            "
            class="w-7 h-7 rounded-lg font-mono font-black text-xs transition-all flex items-center justify-center cursor-pointer border"
          >
            {{ page }}
          </button>

          <button 
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            :class="isDark ? 'bg-zinc-800 text-zinc-300 disabled:opacity-40' : 'bg-white text-black border border-zinc-300 disabled:opacity-40'"
            class="px-2.5 py-1.5 rounded-lg font-mono font-bold transition-all cursor-pointer disabled:cursor-not-allowed"
          >
            Berikutnya ›
          </button>

          <button 
            @click="currentPage = totalPages"
            :disabled="currentPage === totalPages"
            :class="isDark ? 'bg-zinc-800 text-zinc-300 disabled:opacity-40' : 'bg-white text-black border border-zinc-300 disabled:opacity-40'"
            class="px-2.5 py-1.5 rounded-lg font-mono font-bold transition-all cursor-pointer disabled:cursor-not-allowed"
          >
            Akhir »
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Pembayaran Tunai (Cashier Payment) -->
    <div v-if="showPayModal" class="fixed inset-0 bg-black/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div 
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-100 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.8)]' : 'bg-white border-2 border-black text-black shadow-[6px_6px_0px_#000000]'" 
        class="rounded-3xl p-6 max-w-md w-full space-y-4 border-2 transition-all"
      >
        <div class="border-b pb-3" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
          <h3 class="font-black text-lg tracking-tight">💵 Kasir Pembayaran Tunai</h3>
          <p class="text-xs text-zinc-400 font-medium mt-0.5">
            Kode Pesanan: <span class="font-mono font-bold uppercase">{{ activePayOrder?.kode_pesanan }}</span>
          </p>
        </div>

        <div :class="isDark ? 'bg-zinc-950 border-zinc-800' : 'bg-zinc-50 border-2 border-black shadow-[2px_2px_0px_#000000]'" class="p-4 rounded-2xl text-center border">
          <span class="text-xs text-zinc-400 block font-bold uppercase font-mono">Total Tagihan</span>
          <span class="text-2xl font-black font-mono mt-1 block" :class="isDark ? 'text-white' : 'text-black'">
            Rp {{ formatPrice(activePayOrder?.total_harga || 0) }}
          </span>
        </div>

        <form @submit.prevent="processPayment" class="space-y-4">
          <div>
            <label class="block text-xs font-black font-mono uppercase text-zinc-400 mb-2">
              Pilih Cepat Pecahan Uang Tunai
            </label>

            <!-- Quick-select denomination buttons -->
            <div class="grid grid-cols-4 gap-2 mb-3">
              <button
                v-for="d in denominations"
                :key="d"
                type="button"
                @click="addCash(d)"
                :class="isDark ? 'bg-zinc-950 border-zinc-700 text-zinc-200 hover:border-white' : 'bg-zinc-100 text-black border-2 border-black shadow-[1.5px_1.5px_0px_#000000] hover:bg-zinc-200'"
                class="py-2 rounded-xl font-mono font-black text-xs transition-all border cursor-pointer active:scale-95"
              >
                +{{ formatShort(d) }}
              </button>
            </div>

            <div class="flex gap-2 mb-3">
              <button
                type="button"
                @click="setExactCash"
                :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
                class="flex-1 py-2 rounded-xl font-black text-xs transition-all border-2 cursor-pointer"
              >
                💵 Uang Pas
              </button>
              <button
                type="button"
                @click="resetCash"
                :class="isDark ? 'bg-zinc-800 text-zinc-300 border-zinc-700' : 'bg-zinc-100 text-black border-2 border-zinc-300 hover:bg-zinc-200'"
                class="flex-1 py-2 rounded-xl font-black text-xs transition-all border cursor-pointer"
              >
                ↺ Reset
              </button>
            </div>

            <label class="block text-xs font-black font-mono uppercase text-zinc-400 mb-1">
              Atau Ketik Nominal Tunai (Rp)
            </label>
            <input 
              v-model.number="cashInput" 
              type="number" 
              required 
              :min="activePayOrder?.total_harga" 
              :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
              class="w-full px-4 py-2.5 text-sm font-mono font-black rounded-xl outline-none border-2 transition-all" 
              placeholder="0" 
            />
          </div>

          <div :class="isDark ? 'bg-zinc-950 border-zinc-800' : 'bg-zinc-50 border-2 border-black'" class="p-3.5 rounded-xl flex justify-between items-center text-xs border">
            <span class="font-black font-mono uppercase text-zinc-400">Uang Kembalian:</span>
            <span :class="computedKembalian >= 0 ? (isDark ? 'text-emerald-400' : 'text-emerald-700') : 'text-rose-500'" class="font-black font-mono text-base">
              Rp {{ formatPrice(computedKembalian) }}
            </span>
          </div>

          <div class="flex justify-end gap-2.5 pt-2 border-t" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
            <button 
              type="button" 
              @click="showPayModal = false" 
              :class="isDark ? 'bg-zinc-800 text-zinc-300 hover:bg-zinc-700' : 'bg-zinc-100 text-black border-2 border-zinc-300 hover:bg-zinc-200'" 
              class="px-4 py-2 text-xs font-black rounded-xl cursor-pointer"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="submittingPay || computedKembalian < 0" 
              :class="isDark ? 'bg-emerald-600 text-white border-emerald-500 hover:bg-emerald-500 shadow-[2px_2px_0px_#ffffff]' : 'bg-emerald-600 text-white border-2 border-black rounded-xl shadow-[2px_2px_0px_#000000] hover:bg-emerald-700'"
              class="px-5 py-2.5 text-xs font-black transition-all disabled:opacity-50 cursor-pointer rounded-xl"
            >
              {{ submittingPay ? 'Memproses...' : 'Selesaikan Pembayaran & Lunas' }}
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

useHead({
  title: 'Kasir & Scan QR - Admin TokoBukuDigital'
})

const api = useApi()
const { isDark } = useTheme()

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
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`
}

const pendingOrdersCount = computed(() => orders.value.filter(o => o.status === 'pending').length)
const confirmedOrdersCount = computed(() => orders.value.filter(o => o.status === 'confirmed').length)
const completedOrdersCount = computed(() => orders.value.filter(o => o.status === 'completed').length)

const getStatusBadgeClass = (status: string) => {
  if (isDark.value) {
    switch (status) {
      case 'pending': return 'bg-amber-950/60 text-amber-300 border-amber-800'
      case 'confirmed': return 'bg-sky-950/60 text-sky-300 border-sky-800'
      case 'completed': return 'bg-emerald-950/60 text-emerald-300 border-emerald-800'
      default: return 'bg-zinc-800 text-zinc-300 border-zinc-700'
    }
  }
  switch (status) {
    case 'pending': return 'bg-amber-50 text-amber-900 border-amber-300'
    case 'confirmed': return 'bg-sky-50 text-sky-900 border-sky-300'
    case 'completed': return 'bg-emerald-50 text-emerald-900 border-emerald-300'
    default: return 'bg-zinc-100 text-zinc-800 border-zinc-300'
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
  return Math.ceil(filteredOrders.value.length / itemsPerPage) || 1
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
  if (process.client) {
    const el = document.getElementById('selected-order-section')
    if (el) el.scrollIntoView({ behavior: 'smooth' })
  }
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

    const toast = useToast()
    toast.success(`Scan Berhasil! Kode: ${matched.kode_pesanan}`)
    await fetchOrders()
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || `Pesanan dengan kode "${scanQuery.value}" tidak ditemukan!`)
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
    toast.success(res.message || 'Pembayaran berhasil diselesaikan!')
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
