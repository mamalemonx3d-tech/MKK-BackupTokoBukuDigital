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
          <span>📚</span>
          <span>Inventaris & Katalog Buku</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight" :class="isDark ? 'text-white' : 'text-black'">
          Katalog Buku
        </h1>
        <p class="text-xs font-medium" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
          Kelola inventaris, stok, harga modal, harga jual, dan margin keuntungan buku secara terpusat.
        </p>
      </div>

      <button
        @click="openCreateModal"
        :class="isDark ? 'bg-white text-black border-white shadow-[3px_3px_0px_#ffffff] hover:bg-zinc-200' : 'bg-black text-white border-black shadow-[3px_3px_0px_#000000] hover:bg-zinc-800'"
        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl text-xs font-black transition-all cursor-pointer hover:-translate-y-0.5 active:translate-y-0 border-2 shrink-0 self-start md:self-auto"
      >
        <span class="text-base font-bold">+</span>
        <span>Tambah Buku Baru</span>
      </button>
    </div>

    <!-- Quick Metrics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Card 1: Total Judul -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Total Judul</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="isDark ? 'text-white' : 'text-black'">
            {{ books.length }}
          </h3>
        </div>
        <div :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0">
          📖
        </div>
      </div>

      <!-- Card 2: Total Unit Stok -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Total Stok Fisik</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="isDark ? 'text-white' : 'text-black'">
            {{ totalStockCount }}
          </h3>
        </div>
        <div :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0">
          📦
        </div>
      </div>

      <!-- Card 3: Stok Menipis/Habis -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Stok Kritis / Habis</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="lowStockCount > 0 ? 'text-rose-500' : (isDark ? 'text-white' : 'text-black')">
            {{ lowStockCount }}
          </h3>
        </div>
        <div :class="isDark ? 'bg-rose-950/50 text-rose-300 border-rose-800' : 'bg-rose-50 text-rose-700 border border-rose-300'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 border">
          ⚠️
        </div>
      </div>

      <!-- Card 4: Kategori Terdaftar -->
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[2.5px_2.5px_0px_#000000]'"
        class="p-4 rounded-2xl border transition-colors flex items-center justify-between gap-3"
      >
        <div>
          <p class="text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400">Total Kategori</p>
          <h3 class="text-xl sm:text-2xl font-black mt-0.5" :class="isDark ? 'text-white' : 'text-black'">
            {{ categories.length }}
          </h3>
        </div>
        <div :class="isDark ? 'bg-zinc-800 text-zinc-300' : 'bg-zinc-100 text-black border border-black'" class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0">
          🏷️
        </div>
      </div>
    </div>

    <!-- Search & Filters Toolbar -->
    <div
      :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[3px_3px_0px_#000000]'"
      class="p-4 rounded-2xl border-2 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 transition-colors"
    >
      <!-- Search Input -->
      <div class="relative flex-1 min-w-[240px]">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari nama buku atau deskripsi..."
          :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-zinc-50 border-2 border-black text-black shadow-[2px_2px_0px_#000000] focus:shadow-[3px_3px_0px_#000000] placeholder-zinc-400'"
          class="w-full pl-9 pr-8 py-2.5 rounded-xl text-xs font-bold outline-none transition-all border-2"
        />
        <LucideSearch class="w-4 h-4 text-zinc-400 absolute left-3 top-3 pointer-events-none" />
        <button 
          v-if="searchQuery" 
          @click="searchQuery = ''" 
          class="absolute right-3 top-2.5 text-xs text-zinc-400 hover:text-zinc-200 cursor-pointer font-bold"
        >
          ✕
        </button>
      </div>

      <!-- Filters & Sorting Group -->
      <div class="flex flex-wrap items-center gap-2.5">
        <!-- Filter Kategori -->
        <select
          v-model="filterCategory"
          :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
          class="px-3 py-2.5 rounded-xl text-xs font-bold outline-none border-2 transition-all cursor-pointer"
        >
          <option value="">Semua Kategori</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">
            {{ cat.nama_kategori }}
          </option>
        </select>

        <!-- Filter Status Stok -->
        <select
          v-model="filterStock"
          :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
          class="px-3 py-2.5 rounded-xl text-xs font-bold outline-none border-2 transition-all cursor-pointer"
        >
          <option value="all">Semua Status Stok</option>
          <option value="in_stock" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Tersedia (> 5)</option>
          <option value="low_stock" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Stok Menipis (1 - 5)</option>
          <option value="out_of_stock" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Stok Habis (0)</option>
        </select>

        <!-- Sort By -->
        <select
          v-model="sortBy"
          :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
          class="px-3 py-2.5 rounded-xl text-xs font-bold outline-none border-2 transition-all cursor-pointer"
        >
          <option value="newest" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Terbaru</option>
          <option value="oldest" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Terlama</option>
          <option value="name_asc" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Nama (A - Z)</option>
          <option value="name_desc" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Nama (Z - A)</option>
          <option value="price_high" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Harga Jual Tertinggi</option>
          <option value="price_low" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Harga Jual Terendah</option>
          <option value="stock_high" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Stok Terbanyak</option>
          <option value="stock_low" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">Stok Tersedikit</option>
        </select>

        <!-- Reset Button -->
        <button
          v-if="searchQuery || filterCategory || filterStock !== 'all' || sortBy !== 'newest'"
          @click="resetFilters"
          :class="isDark ? 'bg-zinc-800 text-zinc-300 hover:text-white border-zinc-700' : 'bg-zinc-100 text-black border-2 border-black shadow-[1.5px_1.5px_0px_#000000] hover:bg-zinc-200'"
          class="px-3 py-2.5 rounded-xl text-xs font-black border transition-all cursor-pointer"
          title="Reset Semua Filter"
        >
          Reset
        </button>
      </div>
    </div>

    <!-- Books Modern Table Container -->
    <div
      :class="isDark ? 'bg-zinc-900 border-zinc-800' : 'bg-white border-2 border-black shadow-[4px_4px_0px_#000000]'"
      class="rounded-3xl overflow-hidden border-2 transition-colors duration-300"
    >
      <!-- Loading State -->
      <div v-if="loading" :class="isDark ? 'text-zinc-500' : 'text-zinc-600'" class="text-center py-20 text-xs font-black flex flex-col items-center gap-2">
        <span class="text-2xl animate-spin">⏳</span>
        <span>Memuat data katalog buku...</span>
      </div>

      <!-- Empty State (No Books in DB) -->
      <div v-else-if="books.length === 0" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'" class="text-center py-20 text-xs font-bold space-y-2">
        <div class="text-4xl">📚</div>
        <p class="text-sm font-black" :class="isDark ? 'text-white' : 'text-black'">Belum ada buku terdaftar</p>
        <p class="text-zinc-400">Klik tombol "+ Tambah Buku Baru" untuk memulai menambahkan koleksi buku.</p>
      </div>

      <!-- Empty State (Filter No Match) -->
      <div v-else-if="filteredBooks.length === 0" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'" class="text-center py-20 text-xs font-bold space-y-2">
        <div class="text-4xl">🔍</div>
        <p class="text-sm font-black" :class="isDark ? 'text-white' : 'text-black'">Tidak ada buku yang cocok</p>
        <p class="text-zinc-400">Hasil pencarian atau kombinasi filter tidak menemukan buku yang sesuai.</p>
        <button
          @click="resetFilters"
          :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff]' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000]'"
          class="mt-2 px-4 py-2 rounded-xl text-xs font-black border-2 cursor-pointer inline-block"
        >
          Hapus Filter Pencarian
        </button>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr 
              :class="isDark ? 'bg-zinc-950 text-zinc-300 border-zinc-800' : 'bg-zinc-100 text-black border-b-2 border-black'" 
              class="font-black font-mono uppercase text-[10px] tracking-wider border-b"
            >
              <th class="px-4 py-3.5 w-16">Cover</th>
              <th class="px-4 py-3.5 min-w-[200px]">Informasi Buku</th>
              <th class="px-4 py-3.5">Kategori</th>
              <th class="px-4 py-3.5">Tgl Terbit</th>
              <th class="px-4 py-3.5 text-center">Stok</th>
              <th class="px-4 py-3.5 font-mono">Harga Modal</th>
              <th class="px-4 py-3.5 font-mono">Harga Jual</th>
              <th class="px-4 py-3.5 font-mono">Margin Keuntungan</th>
              <th class="px-4 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y font-bold" :class="isDark ? 'divide-zinc-800/80' : 'divide-zinc-200'">
            <tr
              v-for="book in filteredBooks"
              :key="book.id"
              :class="isDark ? 'hover:bg-zinc-800/50' : 'hover:bg-zinc-50/90'"
              class="transition-colors group"
            >
              <!-- Cover Thumbnail -->
              <td class="px-4 py-3">
                <div 
                  :class="isDark ? 'bg-zinc-950 border-zinc-700' : 'bg-zinc-100 border-2 border-black shadow-[1.5px_1.5px_0px_#000000]'" 
                  class="w-11 h-14 rounded-xl overflow-hidden flex items-center justify-center shrink-0 border transition-transform group-hover:scale-105"
                >
                  <img 
                    v-if="book.gambar" 
                    :src="getImageUrl(book.gambar)" 
                    :alt="book.nama_buku" 
                    class="w-full h-full object-cover" 
                    @error="book.gambar = null"
                  />
                  <span v-else class="text-xl">📕</span>
                </div>
              </td>

              <!-- Nama Buku & Deskripsi Ringkas -->
              <td class="px-4 py-3">
                <div class="font-black text-xs sm:text-sm tracking-tight line-clamp-1" :class="isDark ? 'text-white' : 'text-black'">
                  {{ book.nama_buku }}
                </div>
                <div class="text-[11px] font-medium text-zinc-400 line-clamp-1 mt-0.5">
                  {{ book.deskripsi || 'Tidak ada catatan deskripsi.' }}
                </div>
              </td>

              <!-- Kategori Badge -->
              <td class="px-4 py-3 whitespace-nowrap">
                <span 
                  :class="isDark ? 'bg-zinc-800 text-zinc-200 border-zinc-700' : 'bg-zinc-100 text-black border border-black shadow-[1px_1px_0px_#000000]'" 
                  class="px-2.5 py-1 rounded-lg font-black text-[10px] font-mono uppercase tracking-wide border inline-block"
                >
                  {{ book.category_name || 'Buku' }}
                </span>
              </td>

              <!-- Tanggal & Tahun Terbit -->
              <td class="px-4 py-3 whitespace-nowrap font-mono text-[11px]" :class="isDark ? 'text-zinc-400' : 'text-zinc-600'">
                <div>{{ book.tanggal_terbit || '-' }}</div>
                <div class="text-[9px] font-bold text-zinc-500">Tahun {{ book.tahun_terbit }}</div>
              </td>

              <!-- Stok Badge -->
              <td class="px-4 py-3 text-center whitespace-nowrap">
                <span
                  v-if="book.stok > 5"
                  :class="isDark ? 'bg-emerald-950/60 text-emerald-300 border-emerald-800' : 'bg-emerald-50 text-emerald-800 border border-emerald-400'"
                  class="px-2.5 py-1 rounded-full text-[10px] font-black border font-mono inline-flex items-center gap-1"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  <span>{{ book.stok }} pcs</span>
                </span>
                <span
                  v-else-if="book.stok > 0"
                  :class="isDark ? 'bg-amber-950/60 text-amber-300 border-amber-800' : 'bg-amber-50 text-amber-900 border border-amber-400'"
                  class="px-2.5 py-1 rounded-full text-[10px] font-black border font-mono inline-flex items-center gap-1"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                  <span>{{ book.stok }} pcs</span>
                </span>
                <span
                  v-else
                  :class="isDark ? 'bg-rose-950/60 text-rose-300 border-rose-800' : 'bg-rose-50 text-rose-800 border border-rose-400'"
                  class="px-2.5 py-1 rounded-full text-[10px] font-black border font-mono inline-flex items-center gap-1"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                  <span>Habis</span>
                </span>
              </td>

              <!-- Harga Modal -->
              <td class="px-4 py-3 whitespace-nowrap font-mono text-zinc-400">
                Rp {{ formatNumber(book.harga_modal) }}
              </td>

              <!-- Harga Jual -->
              <td class="px-4 py-3 font-mono font-black whitespace-nowrap" :class="isDark ? 'text-white' : 'text-black'">
                Rp {{ formatNumber(book.harga_jual) }}
              </td>

              <!-- Keuntungan -->
              <td class="px-4 py-3 font-mono font-black whitespace-nowrap" :class="book.keuntungan >= 0 ? (isDark ? 'text-emerald-400' : 'text-emerald-700') : 'text-rose-500'">
                +Rp {{ formatNumber(book.keuntungan) }}
              </td>

              <!-- Aksi -->
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="openEditModal(book)"
                    :class="isDark ? 'bg-zinc-800 text-zinc-200 border-zinc-700 hover:bg-zinc-700 hover:text-white' : 'bg-white border-2 border-black text-black shadow-[1.5px_1.5px_0px_#000000] hover:bg-zinc-100'"
                    class="w-8 h-8 rounded-xl border flex items-center justify-center transition-all cursor-pointer hover:scale-105 active:scale-95"
                    title="Edit Buku"
                  >
                    <LucidePencil class="w-3.5 h-3.5" />
                  </button>
                  <button
                    @click="deleteBook(book)"
                    :class="isDark ? 'bg-rose-950/40 text-rose-300 border-rose-800 hover:bg-rose-900/70' : 'bg-rose-50 text-rose-700 border-2 border-rose-600 shadow-[1.5px_1.5px_0px_#e11d48] hover:bg-rose-100'"
                    class="w-8 h-8 rounded-xl border flex items-center justify-center transition-all cursor-pointer hover:scale-105 active:scale-95"
                    title="Hapus Buku"
                  >
                    <LucideTrash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Table Footer / Count Info -->
      <div 
        :class="isDark ? 'bg-zinc-950 border-zinc-800 text-zinc-400' : 'bg-zinc-50 border-zinc-200 text-zinc-600'" 
        class="px-5 py-3 border-t flex flex-col sm:flex-row items-center justify-between gap-2 text-xs font-mono font-bold"
      >
        <div>
          Menampilkan <span :class="isDark ? 'text-white' : 'text-black'">{{ filteredBooks.length }}</span> dari total <span :class="isDark ? 'text-white' : 'text-black'">{{ books.length }}</span> buku terdaftar
        </div>
        <div class="text-[11px] text-zinc-500">
          Data tersinkronisasi otomatis dengan server backend.
        </div>
      </div>
    </div>

    <!-- Modal Form (Tambah / Edit Buku) -->
    <div v-if="showModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
      <div
        :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-100 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.8)]' : 'bg-white border-2 border-black text-black shadow-[6px_6px_0px_#000000]'"
        class="rounded-3xl border-2 p-6 sm:p-7 max-w-xl w-full my-8 space-y-5 transition-all"
        role="dialog"
      >
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b pb-4" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
          <div>
            <h3 :class="isDark ? 'text-white' : 'text-black'" class="font-black text-lg sm:text-xl tracking-tight">
              {{ isEdit ? '✏️ Edit Data Buku' : '➕ Tambah Koleksi Buku Baru' }}
            </h3>
            <p class="text-xs text-zinc-400 font-medium mt-0.5">
              {{ isEdit ? 'Perbarui informasi detail buku dan stok inventaris.' : 'Isi formulir berikut untuk mendaftarkan buku baru ke katalog.' }}
            </p>
          </div>
          <button
            @click="showModal = false"
            :class="isDark ? 'bg-zinc-800 text-zinc-400 hover:text-white hover:bg-zinc-700' : 'bg-zinc-100 text-zinc-700 hover:text-black border border-zinc-300'"
            class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-black transition-all cursor-pointer"
          >
            ✕
          </button>
        </div>

        <!-- Alert Error -->
        <div 
          v-if="errorMessage" 
          :class="isDark ? 'bg-rose-950/40 border-rose-800 text-rose-300' : 'bg-rose-50 border-2 border-rose-600 text-rose-900 shadow-[2px_2px_0px_#e11d48]'"
          class="p-3.5 rounded-xl border flex items-center gap-2.5 text-xs font-bold"
        >
          <span>⚠️</span>
          <span class="flex-1">{{ errorMessage }}</span>
        </div>

        <!-- Form Content -->
        <form @submit.prevent="saveBook" class="space-y-4">
          <!-- Nama Buku -->
          <div class="space-y-1">
            <label class="block text-xs font-black font-mono uppercase text-zinc-400">
              Judul / Nama Buku <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.nama_buku"
              type="text"
              required
              placeholder="Contoh: Laskar Pelangi"
              :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] placeholder-zinc-400'"
              class="w-full px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold outline-none border-2 transition-all"
            />
          </div>

          <!-- Kategori & Stok Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="space-y-1">
              <label class="block text-xs font-black font-mono uppercase text-zinc-400">
                Kategori Buku <span class="text-rose-500">*</span>
              </label>
              <select
                v-model="form.category_id"
                required
                :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
                class="w-full px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-bold outline-none border-2 transition-all cursor-pointer"
              >
                <option value="" disabled>Pilih Kategori</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id" :class="isDark ? 'bg-zinc-900 text-white' : 'bg-white text-black'">
                  {{ cat.nama_kategori }}
                </option>
              </select>
            </div>

            <div class="space-y-1">
              <label class="block text-xs font-black font-mono uppercase text-zinc-400">
                Stok Fisik <span class="text-rose-500">*</span>
              </label>
              <input
                v-model.number="form.stok"
                type="number"
                min="0"
                required
                placeholder="Jumlah unit"
                :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] placeholder-zinc-400'"
                class="w-full px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold outline-none border-2 transition-all font-mono"
              />
            </div>
          </div>

          <!-- Tanggal & Tahun Terbit -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="space-y-1">
              <label class="block text-xs font-black font-mono uppercase text-zinc-400">
                Tanggal Terbit
              </label>
              <input
                v-model="form.tanggal_terbit"
                type="date"
                :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000]'"
                class="w-full px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold outline-none border-2 transition-all font-mono"
              />
            </div>

            <div class="space-y-1">
              <label class="block text-xs font-black font-mono uppercase text-zinc-400">
                Tahun Terbit (Otomatis)
              </label>
              <input
                :value="computedYear"
                type="text"
                readonly
                :class="isDark ? 'bg-zinc-800 border-zinc-700 text-zinc-400' : 'bg-zinc-100 border-2 border-zinc-300 text-zinc-600'"
                class="w-full px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold outline-none border-2 font-mono cursor-not-allowed"
              />
            </div>
          </div>

          <!-- Finansial (Harga Modal & Harga Jual & Margin Otomatis) -->
          <div class="p-4 rounded-2xl border space-y-3" :class="isDark ? 'bg-zinc-950 border-zinc-800' : 'bg-zinc-50 border-2 border-black'">
            <div class="flex items-center justify-between border-b pb-2" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
              <span class="text-xs font-black font-mono uppercase text-zinc-400">Kalkulasi Keuntungan</span>
              <span class="text-xs font-mono font-bold" :class="computedProfit >= 0 ? 'text-emerald-500' : 'text-rose-500'">
                Margin: Rp {{ formatNumber(computedProfit) }}
              </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div class="space-y-1">
                <label class="block text-[11px] font-black font-mono uppercase text-zinc-400">
                  Harga Modal (Rp) <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model.number="form.harga_modal"
                  type="number"
                  min="0"
                  required
                  placeholder="0"
                  :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white focus:border-white' : 'bg-white border-2 border-black text-black shadow-[1.5px_1.5px_0px_#000000]'"
                  class="w-full px-3 py-2 rounded-xl text-xs font-bold outline-none border-2 font-mono"
                />
              </div>

              <div class="space-y-1">
                <label class="block text-[11px] font-black font-mono uppercase text-zinc-400">
                  Harga Jual (Rp) <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model.number="form.harga_jual"
                  type="number"
                  min="0"
                  required
                  placeholder="0"
                  :class="isDark ? 'bg-zinc-900 border-zinc-700 text-white focus:border-white' : 'bg-white border-2 border-black text-black shadow-[1.5px_1.5px_0px_#000000]'"
                  class="w-full px-3 py-2 rounded-xl text-xs font-bold outline-none border-2 font-mono"
                />
              </div>
            </div>
          </div>

          <!-- Cover Gambar -->
          <div class="space-y-1">
            <label class="block text-xs font-black font-mono uppercase text-zinc-400">
              Cover Gambar Buku
            </label>
            <div class="flex items-center gap-3">
              <div 
                v-if="imagePreview || form.existing_gambar" 
                class="w-14 h-16 rounded-xl overflow-hidden border-2 shrink-0"
                :class="isDark ? 'border-zinc-700 bg-zinc-950' : 'border-black bg-zinc-100'"
              >
                <img 
                  :src="imagePreview || getImageUrl(form.existing_gambar)" 
                  alt="Cover Preview" 
                  class="w-full h-full object-cover" 
                  @error="form.existing_gambar = ''"
                />
              </div>
              <input
                type="file"
                accept="image/*"
                @change="handleImageChange"
                :class="isDark ? 'bg-zinc-950 border-zinc-700 text-zinc-300' : 'bg-white border-2 border-black text-black'"
                class="w-full text-xs py-2 px-3 rounded-xl border file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-black file:bg-zinc-200 file:text-black hover:file:bg-zinc-300 cursor-pointer"
              />
            </div>
          </div>

          <!-- Deskripsi -->
          <div class="space-y-1">
            <label class="block text-xs font-black font-mono uppercase text-zinc-400">
              Deskripsi / Sinopsis Buku
            </label>
            <textarea
              v-model="form.deskripsi"
              rows="3"
              placeholder="Tuliskan sinopsis singkat mengenai buku ini..."
              :class="isDark ? 'bg-zinc-950 border-zinc-700 text-white placeholder-zinc-500 focus:border-white' : 'bg-white border-2 border-black text-black shadow-[2px_2px_0px_#000000] placeholder-zinc-400'"
              class="w-full px-4 py-2.5 rounded-xl text-xs font-medium outline-none border-2 transition-all resize-none"
            ></textarea>
          </div>

          <!-- Buttons Action -->
          <div class="flex justify-end gap-2.5 pt-3 border-t" :class="isDark ? 'border-zinc-800' : 'border-zinc-200'">
            <button
              type="button"
              @click="showModal = false"
              :class="isDark ? 'bg-zinc-800 text-zinc-300 hover:bg-zinc-700' : 'bg-zinc-100 text-black border-2 border-zinc-300 hover:bg-zinc-200'"
              class="px-4 py-2.5 rounded-xl text-xs font-black transition-all cursor-pointer"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="submitting"
              :class="isDark ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff] hover:bg-zinc-200' : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'"
              class="px-6 py-2.5 rounded-xl text-xs font-black border-2 transition-all cursor-pointer hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ submitting ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Tambah Buku') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { 
  Search as LucideSearch, 
  Pencil as LucidePencil, 
  Trash2 as LucideTrash2 
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'admin'
})

useHead({
  title: 'Katalog Buku - Admin TokoBukuDigital'
})

const api = useApi()
const { isDark } = useTheme()
const { getImageUrl } = useImageUrl()
const confirmModal = useConfirmModal()

const books = ref<any[]>([])
const categories = ref<any[]>([])
const loading = ref(true)
const showModal = ref(false)
const isEdit = ref(false)
const editBookObj = ref<any>(null)
const submitting = ref(false)
const errorMessage = ref('')
const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)

// Toolbar Search & Filter States
const searchQuery = ref('')
const filterCategory = ref('')
const filterStock = ref('all')
const sortBy = ref('newest')

const form = reactive({
  nama_buku: '',
  category_id: '',
  tanggal_terbit: '',
  stok: 0,
  harga_modal: 0,
  harga_jual: 0,
  deskripsi: '',
  existing_gambar: ''
})

const computedYear = computed(() => {
  if (!form.tanggal_terbit) return new Date().getFullYear().toString()
  return new Date(form.tanggal_terbit).getFullYear().toString()
})

const computedProfit = computed(() => {
  return (Number(form.harga_jual) || 0) - (Number(form.harga_modal) || 0)
})

const totalStockCount = computed(() => {
  return books.value.reduce((acc, b) => acc + (Number(b.stok) || 0), 0)
})

const lowStockCount = computed(() => {
  return books.value.filter(b => (Number(b.stok) || 0) <= 5).length
})

// Filtered & Sorted Books
const filteredBooks = computed(() => {
  let result = [...books.value]

  // Search by query (nama buku or deskripsi)
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    result = result.filter(b => 
      (b.nama_buku && b.nama_buku.toLowerCase().includes(q)) ||
      (b.deskripsi && b.deskripsi.toLowerCase().includes(q)) ||
      (b.category_name && b.category_name.toLowerCase().includes(q))
    )
  }

  // Filter Category
  if (filterCategory.value) {
    result = result.filter(b => String(b.category_id) === String(filterCategory.value))
  }

  // Filter Stock
  if (filterStock.value === 'in_stock') {
    result = result.filter(b => Number(b.stok) > 5)
  } else if (filterStock.value === 'low_stock') {
    result = result.filter(b => Number(b.stok) > 0 && Number(b.stok) <= 5)
  } else if (filterStock.value === 'out_of_stock') {
    result = result.filter(b => Number(b.stok) === 0)
  }

  // Sorting
  switch (sortBy.value) {
    case 'oldest':
      result.sort((a, b) => a.id - b.id)
      break
    case 'name_asc':
      result.sort((a, b) => a.nama_buku.localeCompare(b.nama_buku))
      break
    case 'name_desc':
      result.sort((a, b) => b.nama_buku.localeCompare(a.nama_buku))
      break
    case 'price_high':
      result.sort((a, b) => (Number(b.harga_jual) || 0) - (Number(a.harga_jual) || 0))
      break
    case 'price_low':
      result.sort((a, b) => (Number(a.harga_jual) || 0) - (Number(b.harga_jual) || 0))
      break
    case 'stock_high':
      result.sort((a, b) => (Number(b.stok) || 0) - (Number(a.stok) || 0))
      break
    case 'stock_low':
      result.sort((a, b) => (Number(a.stok) || 0) - (Number(b.stok) || 0))
      break
    case 'newest':
    default:
      result.sort((a, b) => b.id - a.id)
      break
  }

  return result
})

const resetFilters = () => {
  searchQuery.value = ''
  filterCategory.value = ''
  filterStock.value = 'all'
  sortBy.value = 'newest'
}

const formatNumber = (val: number) => {
  return new Intl.NumberFormat('id-ID').format(val || 0)
}

const fetchBooks = async () => {
  loading.value = true
  try {
    const res = await api.get('/api/books')
    books.value = res.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const res = await api.get('/api/categories')
    categories.value = res.data || []
  } catch (e) {
    console.error(e)
  }
}

const openCreateModal = () => {
  isEdit.value = false
  editBookObj.value = null
  form.nama_buku = ''
  form.category_id = ''
  form.tanggal_terbit = new Date().toISOString().split('T')[0]
  form.stok = 10
  form.harga_modal = 50000
  form.harga_jual = 75000
  form.deskripsi = ''
  form.existing_gambar = ''
  imageFile.value = null
  imagePreview.value = null
  errorMessage.value = ''
  showModal.value = true
}

const openEditModal = (b: any) => {
  isEdit.value = true
  editBookObj.value = b
  form.nama_buku = b.nama_buku
  form.category_id = b.category_id
  form.tanggal_terbit = b.tanggal_terbit || ''
  form.stok = b.stok
  form.harga_modal = b.harga_modal
  form.harga_jual = b.harga_jual
  form.deskripsi = b.deskripsi || ''
  form.existing_gambar = b.gambar || ''
  imageFile.value = null
  imagePreview.value = null
  errorMessage.value = ''
  showModal.value = true
}

const handleImageChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) {
    imageFile.value = target.files[0]
    imagePreview.value = URL.createObjectURL(target.files[0])
  }
}

const saveBook = async () => {
  submitting.value = true
  errorMessage.value = ''

  try {
    const formData = new FormData()
    formData.append('nama_buku', form.nama_buku.trim())
    formData.append('category_id', String(form.category_id))
    if (form.tanggal_terbit) formData.append('tanggal_terbit', form.tanggal_terbit)
    formData.append('stok', String(form.stok))
    formData.append('harga_modal', String(form.harga_modal))
    formData.append('harga_jual', String(form.harga_jual))
    if (form.deskripsi) formData.append('deskripsi', form.deskripsi)

    if (imageFile.value) {
      formData.append('gambar', imageFile.value)
    }

    const toast = useToast()

    if (isEdit.value && editBookObj.value) {
      formData.append('_method', 'PUT')
      await api.post(`/api/admin/books/${editBookObj.value.id}`, formData)
      toast.success('Data buku berhasil diperbarui!')
    } else {
      await api.post('/api/admin/books', formData)
      toast.success('Buku baru berhasil ditambahkan!')
    }

    showModal.value = false
    await fetchBooks()
  } catch (err: any) {
    errorMessage.value = err.data?.message || err.data?.errors?.nama_buku?.[0] || 'Gagal menyimpan data buku.'
  } finally {
    submitting.value = false
  }
}

const deleteBook = async (b: any) => {
  const isConfirmed = await confirmModal.danger(
    `Apakah Anda yakin ingin menghapus buku "${b.nama_buku}"? Tindakan ini tidak dapat dibatalkan.`,
    'Hapus Buku'
  )
  if (!isConfirmed) return

  try {
    const toast = useToast()
    await api.delete(`/api/admin/books/${b.id}`)
    toast.success('Buku berhasil dihapus!')
    await fetchBooks()
  } catch (err: any) {
    const toast = useToast()
    toast.error(err.data?.message || 'Gagal menghapus buku.')
  }
}

onMounted(async () => {
  await Promise.all([
    fetchCategories(),
    fetchBooks()
  ])
})
</script>
