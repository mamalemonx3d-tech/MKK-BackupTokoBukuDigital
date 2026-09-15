export const useApi = () => {
  const config = useRuntimeConfig()
  const tokenCookie = useCookie<string | null>('auth_token', { path: '/' })
  
  let apiBase = config.public.apiBase || 'http://127.0.0.1:8000'
  
  if (process.client && window.location.hostname && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
    apiBase = `${window.location.protocol}//${window.location.hostname}:8000`
  }

  // Get token from cookie (SSR & Client) or localStorage (fallback)
  const getToken = () => {
    if (tokenCookie.value) return tokenCookie.value
    if (process.client) return localStorage.getItem('auth_token') || ''
    return ''
  }

  // Generic request wrapper
  const request = async <T = any>(url: string, options: any = {}): Promise<T> => {
    const headers: Record<string, string> = {
      Accept: 'application/json',
      ...(options.headers || {})
    }

    const token = getToken()
    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }

    return $fetch<T>(url, {
      baseURL: apiBase,
      headers,
      ...options
    })
  }

  return {
    apiBase,
    request,
    get: <T = any>(url: string, opts: any = {}) => request<T>(url, { ...opts, method: 'GET' }),
    post: <T = any>(url: string, body?: any, opts: any = {}) => request<T>(url, { ...opts, method: 'POST', body }),
    put: <T = any>(url: string, body?: any, opts: any = {}) => request<T>(url, { ...opts, method: 'PUT', body }),
    delete: <T = any>(url: string, opts: any = {}) => request<T>(url, { ...opts, method: 'DELETE' }),
  }
}
