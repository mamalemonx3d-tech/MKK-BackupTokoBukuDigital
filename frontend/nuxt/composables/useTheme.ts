export const useTheme = () => {
  // Use cookie so Nuxt SSR can read theme state before rendering HTML
  const themeCookie = useCookie<'dark' | 'light'>('theme_mode', {
    default: () => 'light',
    path: '/'
  })

  const isDark = useState<boolean>('theme_dark', () => themeCookie.value === 'dark')

  const applyTheme = (dark: boolean) => {
    themeCookie.value = dark ? 'dark' : 'light'
    isDark.value = dark
    if (import.meta.client) {
      if (dark) {
        document.documentElement.classList.add('dark')
        document.documentElement.classList.remove('retro')
      } else {
        document.documentElement.classList.remove('dark')
        document.documentElement.classList.add('retro')
      }
    }
  }

  const toggleTheme = () => {
    applyTheme(!isDark.value)
  }

  const initTheme = () => {
    applyTheme(themeCookie.value === 'dark')
  }

  return {
    isDark,
    toggleTheme,
    initTheme,
  }
}
