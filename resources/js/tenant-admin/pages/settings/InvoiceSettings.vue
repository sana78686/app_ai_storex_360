<template>
  <form class="tenant-settings-stack max-w-2xl" @submit.prevent="saveSettings">
    <div class="tenant-float-field">
      <input id="inv-prefix" v-model="form.prefix" type="text" placeholder=" " />
      <label for="inv-prefix">Invoice prefix</label>
    </div>

    <div class="tenant-float-field">
      <input id="inv-next" v-model="form.next_number" type="number" min="1" placeholder=" " />
      <label for="inv-next">Next invoice number</label>
    </div>

    <div>
      <div class="tenant-label-row">
        <span class="tenant-field-label">Default currency</span>
        <TenantFieldTip label="Currency" text="Currency code used on new invoices when nothing else is set." />
      </div>
      <TenantSelectSearch
        v-model="form.default_currency"
        input-id="inv-currency"
        placeholder="Search currency…"
        :options="currencyOptions"
      />
    </div>

    <div class="tenant-float-field tenant-float-field--compact-textarea">
      <textarea id="inv-intro" v-model="form.intro_text" placeholder=" " rows="2" />
      <label for="inv-intro">Intro text</label>
    </div>

    <div class="tenant-float-field tenant-float-field--compact-textarea">
      <textarea id="inv-footer" v-model="form.footer_text" placeholder=" " rows="2" />
      <label for="inv-footer">Footer text</label>
    </div>

    <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-gray-800">
      <input
        v-model="form.auto_generate_on_paid"
        type="checkbox"
        class="h-4 w-4 rounded border-gray-300 text-[#275a19] focus:ring-[#275a19]"
      />
      <span>Auto-generate invoice when order is paid</span>
    </label>

    <div class="flex flex-wrap gap-2 border-t border-gray-100 pt-3">
      <button type="button" class="tenant-btn-secondary" @click="revertForm">Cancel</button>
    </div>
  </form>
</template>

<script setup>
import { reactive, onMounted, onUnmounted } from 'vue'
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'
import TenantFieldTip from '@tenant/components/TenantFieldTip.vue'
import TenantSelectSearch from '@tenant/components/TenantSelectSearch.vue'
import { injectSettingsStickySave } from '@tenant/settings/settingsStickyContext'
import { focusFirstValidationField } from '@tenant/helpers/formFocus'

const currencyOptions = [
  { value: 'USD', label: 'USD' },
  { value: 'EUR', label: 'EUR' },
  { value: 'PKR', label: 'PKR' },
  { value: 'GBP', label: 'GBP' },
  { value: 'AED', label: 'AED' },
]

const INVOICE_SETTINGS_FIELD_IDS = {
  prefix: 'inv-prefix',
  next_number: 'inv-next',
  default_currency: 'inv-currency',
  intro_text: 'inv-intro',
  footer_text: 'inv-footer',
}

const settingsSticky = injectSettingsStickySave()

const form = reactive({
  prefix: '',
  next_number: 1,
  default_currency: 'USD',
  intro_text: '',
  footer_text: '',
  auto_generate_on_paid: false,
})

async function loadSettings() {
  const res = await axiosTenant.get('/invoice-settings')
  Object.assign(form, res.data)
}

async function saveSettings() {
  const fd = new FormData()

  Object.keys(form).forEach((key) => {
    if (typeof form[key] === 'boolean') {
      fd.append(key, form[key] ? 1 : 0)
    } else {
      fd.append(key, form[key])
    }
  })

  try {
    await axiosTenant.post('/invoice-settings', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    await Swal.fire({
      icon: 'success',
      title: 'Saved',
      text: 'Invoice settings updated!',
    })
  } catch (err) {
    focusFirstValidationField(err, INVOICE_SETTINGS_FIELD_IDS)
    await Swal.fire({
      icon: 'error',
      title: 'Save failed',
      text: err.response?.data?.message || 'Could not save invoice settings.',
    })
  }
}

function revertForm() {
  loadSettings()
}

onMounted(() => {
  loadSettings()
  settingsSticky?.setSave(saveSettings)
})

onUnmounted(() => {
  settingsSticky?.clearSave()
})
</script>
