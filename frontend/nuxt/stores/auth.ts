import { defineStore } from 'pinia'

export interface User {
  id: number
  name: string
  username: string
  email: string
  role: 'admin' | 'user'
  foto: string | null
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const loading = ref(false)
  const initialized = ref(false)

  const isAuthenticated = computed(() => !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  const fetchUser = async () => {
    const api = useApi()
    loading.value = true
    try {
      const res = await api.get<{ user: User }>('/api/me')
      user.value = res.user
    } catch (e) {
      user.value = null
    } finally {
      loading.value = false
      initialized.value = true
    }
  }

  const login = async (credentials: { login: string; password: string }) => {
    const api = useApi()
    const tokenCookie = useCookie<string | null>('auth_token', { maxAge: 60 * 60 * 24 * 7, sameSite: 'lax', path: '/' })
    const res = await api.post<{ message: string; token: string; user: User }>('/api/login', credentials)
    if (res.token) {
      tokenCookie.value = res.token
      if (process.client) {
        localStorage.setItem('auth_token', res.token)
        document.cookie = `auth_token=${res.token}; path=/; max-age=${60 * 60 * 24 * 7}; SameSite=Lax`
      }
    }
    user.value = res.user
    initialized.value = true
    return res
  }

  const register = async (formData: FormData | Record<string, any>) => {
    const api = useApi()
    const tokenCookie = useCookie<string | null>('auth_token', { maxAge: 60 * 60 * 24 * 7, sameSite: 'lax', path: '/' })
    const res = await api.post<{ message: string; token: string; user: User }>('/api/register', formData)
    if (res.token) {
      tokenCookie.value = res.token
      if (process.client) {
        localStorage.setItem('auth_token', res.token)
        document.cookie = `auth_token=${res.token}; path=/; max-age=${60 * 60 * 24 * 7}; SameSite=Lax`
      }
    }
    user.value = res.user
    initialized.value = true
    return res
  }

  const logout = async () => {
    const api = useApi()
    const tokenCookie = useCookie<string | null>('auth_token', { path: '/' })
    try {
      await api.post('/api/logout')
    } catch (e) {
      // ignore error
    } finally {
      tokenCookie.value = null
      if (process.client) {
        localStorage.removeItem('auth_token')
        document.cookie = 'auth_token=; path=/; max-age=0'
      }
      user.value = null
      initialized.value = true
      navigateTo('/login')
    }
  }

  return {
    user,
    loading,
    initialized,
    isAuthenticated,
    isAdmin,
    fetchUser,
    login,
    register,
    logout,
  }
})
