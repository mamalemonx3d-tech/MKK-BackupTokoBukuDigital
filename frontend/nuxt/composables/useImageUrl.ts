export const useImageUrl = () => {
  const api = useApi()

  const getImageUrl = (path?: string | null): string => {
    if (!path) return ''
    
    // If base64 data url
    if (path.startsWith('data:')) return path

    // If starts with http://localhost/ (without port or default laravel APP_URL)
    if (path.startsWith('http://localhost/') || path.startsWith('http://127.0.0.1/')) {
      return path.replace(/^http:\/\/(localhost|127\.0\.0\.1)\//, `${api.apiBase}/`)
    }

    // If starts with http or https
    if (path.startsWith('http://') || path.startsWith('https://')) {
      return path
    }

    // If starts with /storage/ or storage/
    const cleaned = path.replace(/^\/?storage\//, '').replace(/^\/+/, '')
    return `${api.apiBase}/storage/${cleaned}`
  }

  return {
    getImageUrl
  }
}