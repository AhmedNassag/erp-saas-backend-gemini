import { ref } from 'vue'

const visible  = ref(false)
const message  = ref('')
let resolver: ((v: boolean) => void) | null = null

export function useConfirm() {
  function confirm(msg: string): Promise<boolean> {
    message.value = msg
    visible.value = true
    return new Promise(resolve => { resolver = resolve })
  }

  function accept()  { visible.value = false; resolver?.(true) }
  function decline() { visible.value = false; resolver?.(false) }

  return { visible, message, confirm, accept, decline }
}
