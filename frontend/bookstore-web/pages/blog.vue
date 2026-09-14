<template>
  <div class="max-w-6xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
      <span class="px-3.5 py-1.5 rounded-full bg-[#C8F53F] text-black text-xs font-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A]">BLOG & ARTIKEL</span>
      <h1 class="text-3xl font-black text-slate-900 mt-4 mb-4">Berita & Informasi Literatur</h1>
      <p class="text-slate-700 font-bold text-sm">Temukan rekomendasi buku, ulasan, dan tips membaca menarik di sini.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div 
        v-for="(post, idx) in posts" 
        :key="post.id" 
        :class="[
          idx % 3 === 0 ? 'bg-[#FFF8EC]' : idx % 3 === 1 ? 'bg-[#D4B8FF]' : 'bg-[#FFD4A3]'
        ]"
        class="rounded-3xl border-3 border-black shadow-[6px_6px_0px_#1A1A1A] overflow-hidden hover:translate-x-[-2px] hover:translate-y-[-2px] transition-all flex flex-col justify-between"
      >
        <div>
          <div class="h-44 bg-[#FAF7F0] border-b-2 border-black flex items-center justify-center text-5xl">
            {{ post.icon }}
          </div>
          <div class="p-6">
            <span class="text-[10px] font-black text-black uppercase bg-[#C8F53F] px-2 py-0.5 rounded border border-black shadow-[1px_1px_0px_#1A1A1A] mb-3 inline-block">{{ post.category }}</span>
            <h3 class="font-black text-slate-900 text-lg mb-2 leading-snug">{{ post.title }}</h3>
            <p class="text-xs font-bold text-slate-600 mb-4">{{ post.date }} • Oleh {{ post.author }}</p>
            <p class="text-xs font-medium text-slate-800 line-clamp-3 mb-4 leading-relaxed">{{ post.excerpt }}</p>
          </div>
        </div>
        <div class="p-6 pt-0">
          <button class="text-xs font-black text-black bg-white px-3 py-2 rounded-xl border-2 border-black shadow-[2px_2px_0px_#1A1A1A] hover:bg-[#FFE566] transition-colors">Baca Selengkapnya &rarr;</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const posts = ref([
  {
    id: 1,
    icon: '📖',
    category: 'Rekomendasi',
    title: '5 Buku Fiksi Indonesia Terbaik yang Wajib Dibaca Tahun Ini',
    date: '10 September 2026',
    author: 'Tim Redaksi',
    excerpt: 'Daftar novel fiksi penuh inspirasi dan nilai budaya tinggi yang siap menemani waktu luang Anda.'
  },
  {
    id: 2,
    icon: '💻',
    category: 'Teknologi',
    title: 'Belajar Fullstack Web Development dengan Laravel 12 & Nuxt 3',
    date: '08 September 2026',
    author: 'Admin Tech',
    excerpt: 'Panduan langkah demi langkah merancang arsitektur API modern dan Single Page Application.'
  },
  {
    id: 3,
    icon: '💡',
    category: 'Tips Membaca',
    title: 'Cara Membangun Kebiasaan Membaca Buku 15 Menit Setiap Hari',
    date: '05 September 2026',
    author: 'Penulis Tamu',
    excerpt: 'Tips sederhana namun efektif untuk konsisten membaca buku di tengah kesibukan harian.'
  }
])
</script>
