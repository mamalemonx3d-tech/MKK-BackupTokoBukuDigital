import { defineStore } from 'pinia'

export interface CartItem {
  id?: number
  book_id: number
  nama_buku: string
  harga_jual: number
  gambar: string | null
  stok: number
  qty: number
}

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>([])
  const checkoutSelection = ref<CartItem[]>([])
  const loading = ref(false)
  const authStore = useAuthStore()
  const api = useApi()

  // Persistent cart in localStorage fallback
  if (process.client) {
    const saved = localStorage.getItem('bookstore_cart')
    if (saved) {
      try {
        items.value = JSON.parse(saved)
      } catch (e) {
        items.value = []
      }
    }
  }

  const saveCart = () => {
    if (process.client) {
      localStorage.setItem('bookstore_cart', JSON.stringify(items.value))
    }
  }

  const fetchCart = async () => {
    if (!authStore.isAuthenticated) return
    loading.value = true
    try {
      const res = await api.get<{ data: CartItem[] }>('/api/cart')
      if (res.data) {
        items.value = res.data
        saveCart()
      }
    } catch (err) {
      console.error('Gagal mengambil data keranjang dari server:', err)
    } finally {
      loading.value = false
    }
  }

  const totalItems = computed(() => items.value.reduce((acc, item) => acc + item.qty, 0))

  const totalPrice = computed(() => items.value.reduce((acc, item) => acc + (item.harga_jual * item.qty), 0))

  const addToCart = async (book: { id: number; nama_buku: string; harga_jual: number; gambar: string | null; stok: number }, qty: number = 1) => {
    const existing = items.value.find(i => i.book_id === book.id)
    const newQty = (existing ? existing.qty : 0) + qty
    if (newQty > book.stok) {
      throw new Error(`Stok buku tidak mencukupi (Sisa stok: ${book.stok})`)
    }

    if (authStore.isAuthenticated) {
      try {
        await api.post('/api/cart', { book_id: book.id, qty })
        await fetchCart()
        return
      } catch (err: any) {
        throw new Error(err.data?.message || 'Gagal menambahkan ke keranjang')
      }
    }

    if (existing) {
      existing.qty = newQty
    } else {
      items.value.push({
        book_id: book.id,
        nama_buku: book.nama_buku,
        harga_jual: book.harga_jual,
        gambar: book.gambar,
        stok: book.stok,
        qty
      })
    }
    saveCart()
  }

  const updateQty = async (bookId: number, qty: number) => {
    const item = items.value.find(i => i.book_id === bookId)
    if (!item) return

    if (qty <= 0) {
      await removeFromCart(bookId)
      return
    }

    if (qty > item.stok) {
      throw new Error(`Stok maksimal: ${item.stok}`)
    }

    if (authStore.isAuthenticated && item.id) {
      try {
        await api.put(`/api/cart/${item.id}`, { qty })
        item.qty = qty
        saveCart()
        return
      } catch (err: any) {
        throw new Error(err.data?.message || 'Gagal mengupdate keranjang')
      }
    }

    item.qty = qty
    saveCart()
  }

  const removeFromCart = async (bookId: number) => {
    const item = items.value.find(i => i.book_id === bookId)
    if (authStore.isAuthenticated && item && item.id) {
      try {
        await api.delete(`/api/cart/${item.id}`)
      } catch (err) {
        console.error('Gagal menghapus item keranjang dari DB:', err)
      }
    }
    items.value = items.value.filter(i => i.book_id !== bookId)
    saveCart()
  }

  const clearCart = async () => {
    if (authStore.isAuthenticated) {
      try {
        await api.delete('/api/cart/clear')
      } catch (err) {
        console.error('Gagal mengosongkan keranjang di DB:', err)
      }
    }
    items.value = []
    saveCart()
  }

  const setCheckoutSelection = (selectedItems: CartItem[]) => {
    checkoutSelection.value = selectedItems
  }

  const clearCheckoutSelection = () => {
    checkoutSelection.value = []
  }

  return {
    items,
    loading,
    checkoutSelection,
    totalItems,
    totalPrice,
    fetchCart,
    addToCart,
    updateQty,
    removeFromCart,
    clearCart,
    setCheckoutSelection,
    clearCheckoutSelection,
  }
})
