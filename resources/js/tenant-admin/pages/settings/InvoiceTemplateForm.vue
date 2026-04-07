<template>
  <div class="tenant-invoice-template pb-24 text-[13px] leading-snug">
    <header
      class="sticky top-16 z-[45] -mx-1 mb-4 flex flex-wrap items-center justify-between gap-2 rounded-xl border border-gray-200/90 bg-white/95 px-3 py-2.5 shadow-sm backdrop-blur-sm sm:-mx-0 sm:px-4"
    >
      <h1 class="text-base font-bold text-gray-900 sm:text-lg">Invoice template</h1>
      <div class="flex flex-wrap gap-2">
        <button type="button" class="tenant-btn-secondary tenant-btn-sm" @click="preview">Preview</button>
        <button type="button" class="tenant-btn-submit tenant-btn-sm" @click="save">Save template</button>
      </div>
    </header>

    <div class="mx-auto max-w-4xl space-y-4 px-1 sm:px-0">
      <div>
        <div class="tenant-label-row">
          <label class="tenant-field-label mb-0" for="inv-tpl-name">Template name</label>
          <span class="tenant-required-mark" aria-hidden="true">*</span>
          <TenantFieldTip label="Name" text="A short name so you can pick this design later." />
        </div>
        <input
          id="inv-tpl-name"
          v-model="form.name"
          type="text"
          placeholder="e.g. Modern Clean"
          class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
        />
      </div>

      <div>
        <div class="tenant-label-row">
          <label class="tenant-field-label mb-0" for="inv-tpl-html">Invoice HTML</label>
          <TenantFieldTip label="HTML" text="Full HTML for the printed or PDF invoice. Use placeholders below." />
        </div>
        <textarea
          id="inv-tpl-html"
          v-model="form.template_html"
          rows="16"
          class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2 font-mono text-[12px]"
        />
      </div>

      <div>
        <div class="tenant-label-row">
          <label class="tenant-field-label mb-0" for="inv-tpl-css">Custom CSS</label>
          <TenantFieldTip label="CSS" text="Optional styles applied when the invoice is rendered." />
        </div>
        <textarea
          id="inv-tpl-css"
          v-model="form.template_css"
          rows="10"
          class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2 font-mono text-[12px]"
        />
      </div>

      <div class="rounded-xl border border-[#e3e3e3] bg-[#fafafa] p-4">
        <h3 class="mb-2 text-sm font-bold text-[#303030]">Placeholders</h3>
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
          <button
            v-for="item in placeholders"
            :key="item.key"
            type="button"
            class="rounded-lg border border-[#e3e3e3] bg-white px-2 py-2 text-left text-[11px] text-[#303030] transition hover:bg-[#f3f4f6]"
            @click="insertPlaceholder(item.key)"
          >
            {{ item.label || item.key }}
          </button>
        </div>
        <p class="mt-2 text-[11px] text-[#616161]">Click to append to the HTML field.</p>
      </div>

      <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-gray-800">
        <input v-model="form.is_default" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-[#275a19]" />
        <span>Set as default template</span>
      </label>
    </div>
  </div>

  <div
    v-if="showPreview"
    class="fixed inset-0 z-[10060] flex items-center justify-center bg-black/50 p-4"
    @click.self="showPreview = false"
  >
    <div class="relative flex h-[min(85vh,720px)] w-full max-w-4xl flex-col overflow-hidden rounded-xl bg-white shadow-xl">
      <button
        type="button"
        class="absolute right-3 top-3 z-10 rounded-lg px-2 py-1 text-sm text-gray-600 hover:bg-gray-100"
        @click="showPreview = false"
      >
        Close
      </button>
      <iframe :srcdoc="previewHtml" class="h-full w-full flex-1 border-0" title="Preview" />
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, nextTick } from 'vue'
import Swal from 'sweetalert2'
import TenantFieldTip from '@tenant/components/TenantFieldTip.vue'
import { focusFirstValidationField } from '@tenant/helpers/formFocus'

const INV_TPL_IDS = {
  name: 'inv-tpl-name',
  template_html: 'inv-tpl-html',
  template_css: 'inv-tpl-css',
}

const showPreview = ref(false)
const previewHtml = ref('')

const form = reactive({
  name: 'Modern Clean',
  template_html: '',
  template_css: '',
  is_default: false,

  settings: {
    company_name: 'Demo Company GmbH',
    street: 'Alexanderplatz 1',
    zip: '10178',
    city: 'Berlin',
    country: 'Germany',
    default_email: 'info@demo.com',
    calling_code: '+49',
    website: 'www.demo.com',
    bank_name: 'Demo Bank',
    iban: 'DE89370400440532013000',
    swift: 'DEUTDEDBBER',
    logo: null,
  },

  invoice: {
    invoice_number: 'INV-0001',
    created_at: new Date().toISOString(),
    customer_name: 'John Doe',
    customer_email: 'customer@example.com',
    intro_text: 'Thank you for your business!',
    footer_text: 'Please contact us if you have any questions.',
    subtotal: 120.0,
    total: 120.0,
    currency: 'EUR',
    items: [
      { name: 'Test Product 1', description: 'This is a test product', quantity: 2, price: 30.0, total: 60.0 },
      { name: 'Test Product 2', description: 'Another test product', quantity: 1, price: 60.0, total: 60.0 },
    ],
  },
})

const placeholders = [
  { key: '{{ $settings->company_name }}', label: 'Company Name' },
  { key: '{{ $settings->street }}', label: 'Street' },
  { key: '{{ $settings->zip }}', label: 'ZIP / Postal Code' },
  { key: '{{ $settings->city }}', label: 'City' },
  { key: '{{ $settings->country }}', label: 'Country' },
  { key: '{{ $settings->default_email }}', label: 'Company Email' },
  { key: '{{ $settings->calling_code }}', label: 'Phone Calling Code' },
  { key: '{{ $settings->website }}', label: 'Website' },
  { key: '{{ $settings->bank_name }}', label: 'Bank Name' },
  { key: '{{ $settings->iban }}', label: 'IBAN' },
  { key: '{{ $settings->swift }}', label: 'BIC / SWIFT' },
  { key: '{{ $settings->logo }}', label: 'Company Logo' },
  { key: '{{ $invoice->invoice_number }}', label: 'Invoice Number' },
  { key: '{{ $invoice_date }}', label: 'Invoice Date' },
  { key: '{{ $due_date }}', label: 'Due Date' },
  { key: '{{ $invoice->customer_name }}', label: 'Customer Name' },
  { key: '{{ $invoice->customer_email }}', label: 'Customer Email' },
  { key: '{{ $invoice->intro_text }}', label: 'Intro Text' },
  { key: '{{ $invoice->footer_text }}', label: 'Footer Text' },
  { key: '{{ $invoice->subtotal }}', label: 'Subtotal' },
  { key: '{{ $invoice->total }}', label: 'Total' },
  { key: '{{ $invoice->currency }}', label: 'Currency' },
  {
    key: `@foreach($invoice->items as $item)
<tr>
  <td>{{ $item->name }}</td>
  <td>{{ $item->description }}</td>
  <td>{{ $item->quantity }}</td>
  <td>{{ $item->price }}</td>
  <td>{{ $item->total }}</td>
</tr>
@endforeach`,
    label: 'Invoice Items Table Rows',
  },
  { key: '{{ number_format($invoice->subtotal, 2) }}', label: 'Formatted Subtotal' },
  { key: '{{ number_format($invoice->total, 2) }}', label: 'Formatted Total' },
  { key: '{{ number_format($item->quantity * $item->price, 2) }}', label: 'Formatted Item Total' },
]

const insertPlaceholder = (key) => {
  form.template_html += key
}

const preview = () => {
  const html = `
    <h2>Invoice Preview</h2>
    <p>Company: ${form.settings.company_name}</p>
    <p>Invoice No: ${form.invoice.invoice_number}</p>
    <p>Customer: ${form.invoice.customer_name}</p>
    <p>Subtotal: ${form.invoice.subtotal} ${form.invoice.currency}</p>
    <p>Total: ${form.invoice.total} ${form.invoice.currency}</p>
    <table border="1" cellpadding="4">
      <tr>
        <th>Name</th><th>Description</th><th>Qty</th><th>Price</th><th>Total</th>
      </tr>
      ${form.invoice.items.map((i) => `<tr><td>${i.name}</td><td>${i.description}</td><td>${i.quantity}</td><td>${i.price}</td><td>${i.total}</td></tr>`).join('')}
    </table>
  `
  previewHtml.value = html
  showPreview.value = true
}

const save = async () => {
  const name = (form.name || '').trim()
  if (!name) {
    await nextTick()
    const el = document.getElementById('inv-tpl-name')
    el?.scrollIntoView({ block: 'center', behavior: 'smooth' })
    el?.focus({ preventScroll: true })
    await Swal.fire({ icon: 'warning', title: 'Name required', text: 'Please enter a template name.' })
    return
  }
  try {
    await Swal.fire({
      icon: 'success',
      title: 'Saved',
      text: 'Template saved successfully!',
    })
  } catch (e) {
    focusFirstValidationField(e, INV_TPL_IDS)
    await Swal.fire({
      icon: 'error',
      title: 'Save failed',
      text: e.response?.data?.message || 'Could not save.',
    })
  }
}

onMounted(async () => {
  await nextTick()
  document.getElementById('inv-tpl-name')?.focus({ preventScroll: true })
})
</script>

<style scoped>
.tenant-invoice-template :deep(textarea:focus),
.tenant-invoice-template :deep(input:focus) {
  outline: none;
}
</style>
