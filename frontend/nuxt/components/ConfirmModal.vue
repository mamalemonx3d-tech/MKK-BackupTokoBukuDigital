<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="state.isOpen"
        class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/65 backdrop-blur-sm overflow-y-auto"
        @click.self="handleBackdropClick"
        @keydown.esc="onCancel"
        tabindex="-1"
      >
        <div
          :class="isDark ? 'bg-zinc-900 border-zinc-700 text-zinc-100 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.8)]' : 'bg-white border-2 border-black text-black shadow-[6px_6px_0px_#000000]'"
          class="relative w-full max-w-sm sm:max-w-md rounded-3xl border-2 p-6 sm:p-7 text-center transition-all transform animate-in fade-in zoom-in-95 duration-200"
          role="dialog"
          aria-modal="true"
        >
          <!-- Close 'X' Button -->
          <button
            @click="onCancel"
            :class="isDark ? 'bg-zinc-800 text-zinc-400 hover:text-white hover:bg-zinc-700' : 'bg-zinc-100 text-zinc-700 hover:text-black hover:bg-zinc-200 border border-zinc-300'"
            class="absolute top-4 right-4 w-8 h-8 rounded-full flex items-center justify-center text-xs font-black transition-all cursor-pointer hover:scale-105 active:scale-95"
            title="Tutup Modal"
          >
            ✕
          </button>

          <!-- Lottie SVG Animation Container -->
          <div class="w-24 h-24 sm:w-28 sm:h-28 mx-auto flex items-center justify-center mb-4 relative">
            <!-- Dedicated Lottie SVG Mount Point -->
            <div 
              ref="lottieContainer"
              class="w-full h-full flex items-center justify-center [&>svg]:w-full [&>svg]:h-full"
            />
            <!-- Fallback placeholder while animation data initializes -->
            <div 
              v-if="!isAnimLoaded" 
              class="absolute inset-0 flex items-center justify-center pointer-events-none"
            >
              <div class="w-16 h-16 rounded-full bg-zinc-500/10 animate-pulse flex items-center justify-center text-2xl">
                {{ state.type === 'danger' ? '⚠️' : 'ℹ️' }}
              </div>
            </div>
          </div>

          <!-- Title -->
          <h3
            :class="isDark ? 'text-white' : 'text-black'"
            class="text-lg sm:text-xl font-black tracking-tight mb-2"
          >
            {{ state.title }}
          </h3>

          <!-- Message -->
          <p
            :class="isDark ? 'text-zinc-300' : 'text-zinc-700'"
            class="text-xs sm:text-sm font-medium leading-relaxed mb-6 px-2 whitespace-pre-line"
          >
            {{ state.message }}
          </p>

          <!-- Action Buttons -->
          <div class="flex items-center gap-3 justify-center">
            <button
              v-if="state.showCancel"
              type="button"
              @click="onCancel"
              :class="isDark ? 'bg-zinc-800 text-zinc-300 border-zinc-700 hover:bg-zinc-700 hover:text-white' : 'bg-zinc-100 text-zinc-800 border-2 border-zinc-300 shadow-[2px_2px_0px_#71717a] hover:bg-zinc-200'"
              class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-black border-2 transition-all cursor-pointer hover:-translate-y-0.5 active:translate-y-0"
            >
              {{ state.cancelText || 'Batal' }}
            </button>

            <button
              type="button"
              @click="onConfirm"
              :class="getConfirmButtonClass()"
              class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-black border-2 transition-all cursor-pointer hover:-translate-y-0.5 active:translate-y-0"
            >
              {{ state.confirmText || 'Ya, Lanjutkan' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch, nextTick, onMounted, onBeforeUnmount } from 'vue'
import lottie from 'lottie-web'
import { useConfirmModal } from '~/composables/useConfirmModal'
import { useTheme } from '~/composables/useTheme'

const { state, animationPath, onConfirm, onCancel } = useConfirmModal()
const { isDark } = useTheme()

const lottieContainer = ref<HTMLElement | null>(null)
const isAnimLoaded = ref(false)
let animInstance: any = null
let currentRenderId = 0

// In-memory cache for instant animation loading
const animationCache = new Map<string, any>()

const preloadAnimations = async () => {
  const paths = [
    '/animations/error.json',
    '/animations/info.json',
    '/animations/success.json',
    '/animations/email.json'
  ]
  for (const p of paths) {
    if (!animationCache.has(p)) {
      try {
        const res = await fetch(p)
        if (res.ok) {
          const data = await res.json()
          animationCache.set(p, data)
        }
      } catch (e) {
        // Ignore preload errors
      }
    }
  }
}

onMounted(() => {
  preloadAnimations()
})

const destroyAnimation = () => {
  if (animInstance) {
    try {
      animInstance.destroy()
    } catch (e) {
      // Ignore destroy errors
    }
    animInstance = null
  }
  if (lottieContainer.value) {
    lottieContainer.value.innerHTML = ''
  }
}

const renderAnimation = async () => {
  const renderId = ++currentRenderId

  destroyAnimation()
  isAnimLoaded.value = false

  if (!state.value.isOpen) return

  await nextTick()

  // Guard against newer render calls or modal close during nextTick
  if (renderId !== currentRenderId || !state.value.isOpen || !lottieContainer.value) {
    return
  }

  // Force clean container DOM before mounting SVG
  lottieContainer.value.innerHTML = ''

  const path = animationPath.value
  const cachedData = animationCache.get(path)

  try {
    if (cachedData) {
      animInstance = lottie.loadAnimation({
        container: lottieContainer.value,
        renderer: 'svg',
        loop: true,
        autoplay: true,
        animationData: cachedData
      })
      isAnimLoaded.value = true
    } else {
      animInstance = lottie.loadAnimation({
        container: lottieContainer.value,
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: path
      })
      animInstance.addEventListener('DOMLoaded', () => {
        if (renderId === currentRenderId) {
          isAnimLoaded.value = true
        }
      })
    }
  } catch (err) {
    console.error('Lottie load error:', err)
  }
}

// Single consolidated watcher with flush: 'post' to eliminate duplicate concurrent executions
watch(
  () => [state.value.isOpen, animationPath.value],
  ([isOpen]) => {
    if (isOpen) {
      renderAnimation()
    } else {
      destroyAnimation()
      isAnimLoaded.value = false
    }
  },
  { flush: 'post' }
)

onBeforeUnmount(() => {
  destroyAnimation()
})

const handleBackdropClick = () => {
  onCancel()
}

const getConfirmButtonClass = () => {
  if (state.value.type === 'danger') {
    return isDark.value
      ? 'bg-rose-600 text-white border-rose-500 shadow-[2px_2px_0px_#ffffff] hover:bg-rose-500'
      : 'bg-rose-600 text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-rose-700'
  }
  return isDark.value
    ? 'bg-white text-black border-white shadow-[2px_2px_0px_#ffffff] hover:bg-zinc-200'
    : 'bg-black text-white border-black shadow-[2px_2px_0px_#000000] hover:bg-zinc-800'
}
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
