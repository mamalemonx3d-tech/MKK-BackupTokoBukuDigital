<template>
  <header :class="isDark ? 'bg-slate-900/95 border-slate-800 shadow-sm' : 'bg-[#FFF8EC] border-b-2.5 border-[#1A1A1A] shadow-[0_4px_0px_#1A1A1A]'" class="sticky top-0 z-50 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <NuxtLink to="/" class="flex items-center gap-2 group">
          <div class="w-10 h-10 rounded-xl bg-[#C8F53F] border-2 border-black flex items-center justify-center text-black font-bold text-xl shadow-[2.5px_2.5px_0px_#1A1A1A] group-hover:scale-105 transition-transform">
            📚
          </div>
          <span class="font-black text-xl text-black">
            Book<span class="bg-[#C8F53F] text-black px-1.5 py-0.5 rounded-lg border-2 border-black shadow-[2px_2px_0px_#1A1A1A]">Store</span>
          </span>
        </NuxtLink>

        <!-- Public Navigation Links -->
        <nav class="hidden md:flex items-center gap-6 text-sm font-black text-slate-900">
          <NuxtLink to="/" class="hover:bg-[#C8F53F] px-3 py-1.5 rounded-xl border-2 border-transparent hover:border-black hover:shadow-[2px_2px_0px_#1A1A1A] transition-all" active-class="bg-[#C8F53F] border-black shadow-[2px_2px_0px_#1A1A1A]">Home</NuxtLink>
          <NuxtLink to="/user/katalog" class="hover:bg-[#C8F53F] px-3 py-1.5 rounded-xl border-2 border-transparent hover:border-black hover:shadow-[2px_2px_0px_#1A1A1A] transition-all" active-class="bg-[#C8F53F] border-black shadow-[2px_2px_0px_#1A1A1A]">Katalog Buku</NuxtLink>
          <NuxtLink to="/our-story" class="hover:bg-[#C8F53F] px-3 py-1.5 rounded-xl border-2 border-transparent hover:border-black hover:shadow-[2px_2px_0px_#1A1A1A] transition-all" active-class="bg-[#C8F53F] border-black shadow-[2px_2px_0px_#1A1A1A]">Our Story</NuxtLink>
          <NuxtLink to="/blog" class="hover:bg-[#C8F53F] px-3 py-1.5 rounded-xl border-2 border-transparent hover:border-black hover:shadow-[2px_2px_0px_#1A1A1A] transition-all" active-class="bg-[#C8F53F] border-black shadow-[2px_2px_0px_#1A1A1A]">Blog</NuxtLink>
          <NuxtLink to="/contact" class="hover:bg-[#C8F53F] px-3 py-1.5 rounded-xl border-2 border-transparent hover:border-black hover:shadow-[2px_2px_0px_#1A1A1A] transition-all" active-class="bg-[#C8F53F] border-black shadow-[2px_2px_0px_#1A1A1A]">Contact</NuxtLink>
        </nav>

        <!-- Right User Actions -->
        <div class="flex items-center gap-4">
          <!-- Cart Icon (For User) -->
          <NuxtLink to="/user/keranjang" class="relative p-2 rounded-xl bg-[#FFF8EC] border-2 border-black shadow-[2px_2px_0px_#1A1A1A] text-slate-900 transition-transform active:translate-x-[1px] active:translate-y-[1px]">
            <span class="text-xl">🛒</span>
            <span v-if="cartStore.totalItems > 0" class="absolute -top-1.5 -right-1.5 bg-[#FFB7B2] border-2 border-black text-black text-xs font-black w-5 h-5 rounded-full flex items-center justify-center shadow-[1px_1px_0px_#1A1A1A]">
              {{ cartStore.totalItems }}
            </span>
          </NuxtLink>

          <!-- Logged In User / Admin Menu -->
          <div v-if="authStore.isAuthenticated" class="flex items-center gap-3">
            <NuxtLink v-if="authStore.isAdmin" to="/admin/kategori" class="hidden sm:inline-flex items-center px-3 py-1.5 text-xs font-black rounded-xl bg-[#FFE566] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A] transition-transform active:translate-x-[1px] active:translate-y-[1px]">
              ⚙️ Dashboard Admin
            </NuxtLink>

            <NuxtLink v-else to="/user/riwayat" class="hidden sm:inline-flex items-center px-3 py-1.5 text-xs font-black rounded-xl bg-[#D4B8FF] text-black border-2 border-black shadow-[2px_2px_0px_#1A1A1A] transition-transform active:translate-x-[1px] active:translate-y-[1px]">
              📋 Riwayat Pesanan
            </NuxtLink>

            <NuxtLink :to="authStore.isAdmin ? '/admin/chat' : '/user/chat'" class="p-2 rounded-xl bg-[#FFD4A3] border-2 border-black shadow-[2px_2px_0px_#1A1A1A] text-slate-900 transition-transform active:translate-x-[1px] active:translate-y-[1px]" title="Live Chat">
              💬
            </NuxtLink>

            <div class="flex items-center gap-2 pl-2 border-l-2 border-black">
              <div class="w-8 h-8 rounded-full bg-[#C8F53F] border-2 border-black text-black flex items-center justify-center text-sm font-black shadow-[1.5px_1.5px_0px_#1A1A1A]">
                {{ authStore.user?.name?.charAt(0).toUpperCase() }}
              </div>
              <button @click="authStore.logout()" class="px-2.5 py-1 text-xs font-black text-black bg-[#FFB7B2] border-2 border-black rounded-xl shadow-[1.5px_1.5px_0px_#1A1A1A] transition-transform active:translate-x-[1px] active:translate-y-[1px]">
                Logout
              </button>
            </div>
          </div>

          <!-- Guest Login / Register -->
          <div v-else class="flex items-center gap-2">
            <NuxtLink to="/login" class="px-4 py-2 text-sm font-black text-black hover:bg-[#FFE566] border-2 border-transparent hover:border-black hover:shadow-[2px_2px_0px_#1A1A1A] rounded-xl transition-all">
              Login
            </NuxtLink>
            <NuxtLink to="/register" class="px-4 py-2 text-sm font-black text-black bg-[#C8F53F] border-2 border-black rounded-xl shadow-[3px_3px_0px_#1A1A1A] transition-all hover:translate-x-[-1px] hover:translate-y-[-1px] active:translate-x-[1px] active:translate-y-[1px]">
              Register
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const cartStore = useCartStore()

onMounted(async () => {
  if (!authStore.initialized) {
    await authStore.fetchUser()
  }
})
</script>
