export const useApi = () => {
  const config = useRuntimeConfig()
  const tokenCookie = useCookie<string | null>('auth_token', { path: '/' })
  
  let apiBase = config.public.apiBase || 'http://localhost:8000'
  
  if (process.client && window.location.hostname && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
    apiBase = `${window.location.protocol}//${window.location.hostname}:8000`
  }

  // Get token from cookie (SSR & Client) or localStorage (fallback)
  const getToken = () => {
    if (tokenCookie.value) return tokenCookie.value
    if (process.client) return localStorage.getItem('auth_token') || ''
    return ''
  }

  // Fetch CSRF Cookie from Sanctum (for SPA cookie compatibility)
  const fetchCsrfToken = async () => {
    try {
      await $fetch('/sanctum/csrf-cookie', {
        baseURL: apiBase,
        credentials: 'include'
      })
    } catch (e) {
      // Ignore CSRF cookie fetch errors if token is used
    }
  }

  // Get XSRF token from document cookie
  const getXsrfToken = () => {
    if (process.server) return ''
    const match = document.cookie.match(new RegExp('(^|; )XSRF-TOKEN=([^;]+)'))
    return match ? decodeURIComponent(match[2]) : ''
  }

  // Generic request wrapper
  const request = async <T = any>(url: string, options: any = {}): Promise<T> => {
    const isMutation = ['POST', 'PUT', 'PATCH', 'DELETE'].includes((options.method || 'GET').toUpperCase())
    
    // Fetch CSRF cookie if no bearer token is stored yet
    if (isMutation && process.client && !getToken() && !url.includes('csrf-cookie')) {
      await fetchCsrfToken()
    }

    const headers: Record<string, string> = {
      Accept: 'application/json',
      ...(options.headers || {})
    }

    const token = getToken()
    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }

    const xsrf = getXsrfToken()
    if (xsrf) {
      headers['X-XSRF-TOKEN'] = xsrf
    }

    return $fetch<T>(url, {
      baseURL: apiBase,
      credentials: 'include',
      headers,
      ...options
    })
  }

  return {
    apiBase,
    fetchCsrfToken,
    request,
    get: <T = any>(url: string, opts: any = {}) => request<T>(url, { ...opts, method: 'GET' }),
    post: <T = any>(url: string, body?: any, opts: any = {}) => request<T>(url, { ...opts, method: 'POST', body }),
    put: <T = any>(url: string, body?: any, opts: any = {}) => request<T>(url, { ...opts, method: 'PUT', body }),
    delete: <T = any>(url: string, opts: any = {}) => request<T>(url, { ...opts, method: 'DELETE' }),
  }
}
