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

    <div class="tenant-float-field is-always-floated">
      <select id="inv-currency" v-model="form.default_currency">
        <option value="USD">USD</option>
        <option value="EUR">EUR</option>
        <option value="PKR">PKR</option>
        <option value="GBP">GBP</option>
        <option value="AED">AED</option>
      </select>
      <label for="inv-currency">Default currency</label>
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
      <button type="submit" class="tenant-btn-submit">Save settings</button>
      <button type="button" class="tenant-btn-secondary" @click="revertForm">Cancel</button>
    </div>
  </form>
</template>

<script setup>
import { reactive, onMounted } from 'vue'
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'

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

  await axiosTenant.post('/invoice-settings', fd, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })

  await Swal.fire({
    icon: 'success',
    title: 'Saved',
    text: 'Invoice settings updated!',
  })
}

function revertForm() {
  loadSettings()
}

onMounted(() => loadSettings())
</script>
