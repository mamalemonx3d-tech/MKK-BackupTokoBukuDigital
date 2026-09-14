export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()
  
  if (!authStore.initialized) {
    await authStore.fetchUser()
  }

  if (!authStore.isAuthenticated) {
    return navigateTo('/login')
  }
})
