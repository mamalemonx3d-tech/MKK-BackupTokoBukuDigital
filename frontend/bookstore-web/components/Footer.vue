<template>
  <footer class="bg-[#FFF8EC] text-slate-900 py-12 border-t-2.5 border-[#1A1A1A] mt-20 font-['Space_Grotesk']">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <div class="flex items-center gap-2 mb-4">
            <span class="text-2xl">📚</span>
            <span class="text-xl font-black text-slate-900 bg-[#C8F53F] px-2 py-0.5 rounded-lg border-2 border-black shadow-[2px_2px_0px_#1A1A1A]">BookStore</span>
          </div>
          <p class="text-xs font-bold text-slate-700 leading-relaxed">
            Platform toko buku online terlengkap dengan koleksi buku berkualitas untuk mencerdaskan kehidupan bangsa.
          </p>
        </div>
        <div>
          <h4 class="text-slate-900 font-black mb-4 uppercase tracking-wider text-xs bg-[#D4B8FF] px-2 py-1 inline-block rounded border-2 border-black shadow-[2px_2px_0px_#1A1A1A]">Navigasi Utama</h4>
          <ul class="space-y-2 text-xs font-bold">
            <li><NuxtLink to="/" class="hover:underline hover:text-indigo-600 transition-colors">Home</NuxtLink></li>
            <li><NuxtLink to="/user/katalog" class="hover:underline hover:text-indigo-600 transition-colors">Katalog Buku</NuxtLink></li>
            <li><NuxtLink to="/our-story" class="hover:underline hover:text-indigo-600 transition-colors">Our Story</NuxtLink></li>
            <li><NuxtLink to="/blog" class="hover:underline hover:text-indigo-600 transition-colors">Blog & Berita</NuxtLink></li>
          </ul>
        </div>
        <div>
          <h4 class="text-slate-900 font-black mb-4 uppercase tracking-wider text-xs bg-[#FFD4A3] px-2 py-1 inline-block rounded border-2 border-black shadow-[2px_2px_0px_#1A1A1A]">Bantuan & Kontak</h4>
          <ul class="space-y-2 text-xs font-bold">
            <li><NuxtLink to="/contact" class="hover:underline hover:text-indigo-600 transition-colors">Hubungi Kami</NuxtLink></li>
            <li><NuxtLink to="/forget-password" class="hover:underline hover:text-indigo-600 transition-colors">Lupa Password</NuxtLink></li>
            <li><NuxtLink to="/user/chat" class="hover:underline hover:text-indigo-600 transition-colors">Live Chat Support</NuxtLink></li>
          </ul>
        </div>
        <div>
          <h4 class="text-slate-900 font-black mb-4 uppercase tracking-wider text-xs bg-[#FFE566] px-2 py-1 inline-block rounded border-2 border-black shadow-[2px_2px_0px_#1A1A1A]">Jam Operasional</h4>
          <p class="text-xs font-bold text-slate-700">Senin - Sabtu: 08.00 - 20.00 WIB</p>
          <p class="text-xs font-bold text-slate-500 mt-2">Dukungan Sistem: Laravel 12 API + Nuxt 3 SPA</p>
        </div>
      </div>
      <div class="border-t-2 border-black mt-10 pt-6 text-center text-xs font-black text-slate-700">
        &copy; {{ new Date().getFullYear() }} BookStore Project. All rights reserved.
      </div>
    </div>
  </footer>
</template>
