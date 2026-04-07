import { computed, inject, provide, ref } from 'vue'

export const SETTINGS_STICKY_KEY = Symbol('tenantSettingsSticky')

/**
 * Call from Settingslayout (script setup) once.
 * @returns {{ setSave: (fn: (() => void | Promise<void>) | null) => void, clearSave: () => void, runSave: () => Promise<void>, saving: import('vue').Ref<boolean> }}
 */
export function provideSettingsStickySave() {
  const saveFn = ref(null)
  const saving = ref(false)

  const hasSave = computed(() => !!saveFn.value)

  const api = {
    saving,
    hasSave,
    setSave(fn) {
      saveFn.value = typeof fn === 'function' ? fn : null
    },
    clearSave() {
      saveFn.value = null
    },
    async runSave() {
      if (!saveFn.value || saving.value) return
      saving.value = true
      try {
        await saveFn.value()
      } finally {
        saving.value = false
      }
    },
  }

  provide(SETTINGS_STICKY_KEY, api)
  return api
}

export function injectSettingsStickySave() {
  return inject(SETTINGS_STICKY_KEY, null)
}
