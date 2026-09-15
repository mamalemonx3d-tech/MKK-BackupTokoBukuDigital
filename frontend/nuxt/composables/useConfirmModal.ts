import { ref, computed } from 'vue'

export type ModalType = 'danger' | 'warning' | 'info' | 'success' | 'email'
export type AnimationType = 'error' | 'info' | 'success' | 'email'

export interface ConfirmModalOptions {
  title?: string
  message: string
  confirmText?: string
  cancelText?: string
  type?: ModalType
  animation?: AnimationType
  showCancel?: boolean
}

interface ModalState extends ConfirmModalOptions {
  isOpen: boolean
  resolve?: (value: boolean) => void
}

const state = ref<ModalState>({
  isOpen: false,
  title: '',
  message: '',
  confirmText: 'Ya, Lanjutkan',
  cancelText: 'Batal',
  type: 'info',
  animation: 'info',
  showCancel: true
})

const getAnimationPath = (anim?: AnimationType, type?: ModalType): string => {
  const selected = anim || (type === 'danger' ? 'error' : type === 'success' ? 'success' : type === 'email' ? 'email' : 'info')
  switch (selected) {
    case 'error':
      return '/animations/error.json'
    case 'success':
      return '/animations/success.json'
    case 'email':
      return '/animations/email.json'
    case 'info':
    default:
      return '/animations/info.json'
  }
}

export const useConfirmModal = () => {
  const open = (options: ConfirmModalOptions): Promise<boolean> => {
    return new Promise((resolve) => {
      state.value = {
        isOpen: true,
        title: options.title || (options.type === 'danger' ? 'Konfirmasi Hapus' : 'Konfirmasi'),
        message: options.message,
        confirmText: options.confirmText || (options.type === 'danger' ? 'Ya, Hapus' : 'Ya, Lanjutkan'),
        cancelText: options.cancelText || 'Batal',
        type: options.type || 'info',
        animation: options.animation || (options.type === 'danger' ? 'error' : options.type === 'success' ? 'success' : options.type === 'email' ? 'email' : 'info'),
        showCancel: options.showCancel !== undefined ? options.showCancel : true,
        resolve
      }
    })
  }

  const confirm = (messageOrOptions: string | ConfirmModalOptions, title?: string, type: ModalType = 'info'): Promise<boolean> => {
    if (typeof messageOrOptions === 'string') {
      return open({
        title,
        message: messageOrOptions,
        type
      })
    }
    return open(messageOrOptions)
  }

  const danger = (message: string, title: string = 'Konfirmasi Hapus', options?: Partial<ConfirmModalOptions>): Promise<boolean> => {
    return open({
      title,
      message,
      type: 'danger',
      animation: 'error',
      confirmText: 'Ya, Hapus',
      cancelText: 'Batal',
      ...options
    })
  }

  const alert = (message: string, title: string = 'Pemberitahuan', type: ModalType = 'info', animation?: AnimationType): Promise<boolean> => {
    return open({
      title,
      message,
      type,
      animation,
      confirmText: 'Mengerti',
      showCancel: false
    })
  }

  const onConfirm = () => {
    if (state.value.resolve) {
      state.value.resolve(true)
    }
    state.value.isOpen = false
  }

  const onCancel = () => {
    if (state.value.resolve) {
      state.value.resolve(false)
    }
    state.value.isOpen = false
  }

  return {
    state: computed(() => state.value),
    animationPath: computed(() => getAnimationPath(state.value.animation, state.value.type)),
    open,
    confirm,
    danger,
    alert,
    onConfirm,
    onCancel
  }
}
