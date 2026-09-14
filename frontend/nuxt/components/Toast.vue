<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-[9999] pointer-events-none space-y-2 md:top-6 md:right-6 max-w-xs md:max-w-sm">
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="getToastClass(toast.type)"
          class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-lg text-sm md:text-base font-medium shadow-lg border animate-in fade-in slide-in-from-top-2 duration-200"
        >
          <!-- Icon -->
          <span class="text-lg md:text-xl flex-shrink-0 mt-0.5">
            {{ getToastIcon(toast.type) }}
          </span>

          <!-- Message -->
          <div class="flex-1 min-w-0">
            <p class="break-words">{{ toast.message }}</p>
          </div>

          <!-- Close Button -->
          <button
            @click="removeToast(toast.id)"
            class="flex-shrink-0 hover:opacity-70 transition-opacity"
            :title="'Tutup'"
          >
            ✕
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { useToast } from '~/composables/useToast'
import { useTheme } from '~/composables/useTheme'

const { toasts, remove } = useToast()
const { isDark } = useTheme()

const removeToast = (id: string) => {
  remove(id)
}

const getToastIcon = (type: string) => {
  const icons: Record<string, string> = {
    success: '✓',
    error: '✕',
    info: 'ℹ',
    warning: '⚠'
  }
  return icons[type] || '•'
}

const getToastClass = (type: string) => {
  const baseClass = isDark.value
    ? 'bg-slate-800 border-slate-700 text-slate-100'
    : 'bg-white border-slate-200 text-slate-900'

  const typeClasses: Record<string, string> = {
    success: isDark.value
      ? 'bg-emerald-900/80 border-emerald-700 text-emerald-100'
      : 'bg-emerald-50 border-emerald-200 text-emerald-900',
    error: isDark.value
      ? 'bg-rose-900/80 border-rose-700 text-rose-100'
      : 'bg-rose-50 border-rose-200 text-rose-900',
    info: isDark.value
      ? 'bg-blue-900/80 border-blue-700 text-blue-100'
      : 'bg-blue-50 border-blue-200 text-blue-900',
    warning: isDark.value
      ? 'bg-amber-900/80 border-amber-700 text-amber-100'
      : 'bg-amber-50 border-amber-200 text-amber-900'
  }

  return typeClasses[type] || baseClass
}
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 200ms ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateY(-8px) translateX(16px);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(16px) translateY(-8px);
}

.toast-move {
  transition: transform 200ms ease;
}
</style>
