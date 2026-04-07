<template>
  <p class="text-sm leading-relaxed text-gray-600">
    Configure languages, regional formats, and storefront defaults. More options will appear here as this section is
    expanded.
  </p>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'
import Swal from 'sweetalert2'
import { injectSettingsStickySave } from '@tenant/settings/settingsStickyContext'

const sticky = injectSettingsStickySave()

onMounted(() => {
  sticky?.setSave(async () => {
    await Swal.fire({
      icon: 'info',
      title: 'Coming soon',
      text: 'Localization settings are not editable on this screen yet.',
    })
  })
})

onUnmounted(() => {
  sticky?.clearSave()
})
</script>
