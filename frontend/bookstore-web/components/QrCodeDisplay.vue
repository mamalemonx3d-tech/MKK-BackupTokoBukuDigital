<template>
  <div class="flex flex-col items-center justify-center p-3 bg-white rounded-xl shadow-md border border-slate-200">
    <div class="w-full flex items-center justify-center min-h-[140px]">
      <img 
        v-if="qrDataUrl" 
        :src="qrDataUrl" 
        :alt="value"
        class="w-full h-auto max-w-[160px] aspect-square object-contain border-2 border-slate-900 rounded-lg bg-white p-1" 
      />
      <div v-else class="w-32 h-32 flex items-center justify-center text-xs text-slate-400 font-bold">
        Generating QR...
      </div>
    </div>
    <span v-if="showLabel" class="text-[12px] font-mono font-black text-slate-900 mt-2 tracking-wider bg-slate-100 px-2 py-0.5 rounded border border-slate-300">
      {{ value }}
    </span>
  </div>
</template>

<script setup lang="ts">
import QRCode from 'qrcode'

const props = defineProps({
  value: {
    type: String,
    required: true
  },
  size: {
    type: Number,
    default: 200
  },
  showLabel: {
    type: Boolean,
    default: false
  }
})

const qrDataUrl = ref('')

const generateQR = async () => {
  if (!props.value) return
  try {
    const url = await QRCode.toDataURL(props.value, {
      width: props.size,
      margin: 2,
      color: {
        dark: '#000000',
        light: '#ffffff'
      }
    })
    qrDataUrl.value = url
  } catch (err) {
    console.error('Failed to generate QR code:', err)
  }
}

watch(() => props.value, generateQR, { immediate: true })
onMounted(generateQR)
</script>
