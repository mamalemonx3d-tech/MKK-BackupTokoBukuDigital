import { ref, watch, onMounted } from 'vue'

const isCollapsed = ref(false)

export const useSidebar = () => {
  const toggle = () => {
    isCollapsed.value = !isCollapsed.value
    if (process.client) {
      localStorage.setItem('sidebar_collapsed', isCollapsed.value ? '1' : '0')
    }
  }

  const setCollapsed = (val: boolean) => {
    isCollapsed.value = val
    if (process.client) {
      localStorage.setItem('sidebar_collapsed', val ? '1' : '0')
    }
  }

  const initSidebar = () => {
    if (process.client) {
      const saved = localStorage.getItem('sidebar_collapsed')
      if (saved !== null) {
        isCollapsed.value = saved === '1'
      }
    }
  }

  return {
    isCollapsed,
    toggle,
    setCollapsed,
    initSidebar
  }
}
