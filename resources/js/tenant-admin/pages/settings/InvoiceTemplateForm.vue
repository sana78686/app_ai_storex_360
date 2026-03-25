<template>
  <div class="max-w-4xl mx-auto p-6 space-y-6">

    <!-- Page Title -->
    <h1 class="text-2xl font-bold tracking-tight">Create Invoice Template</h1>

    <!-- Template Name -->
    <div>
      <label class="font-medium block mb-1">Template Name</label>
      <input
        v-model="form.name"
        placeholder="e.g. Modern Clean, Classic, Minimalist"
        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"
      />
    </div>

    <!-- HTML Editor -->
    <div>
      <label class="font-medium block mb-1">Full Invoice HTML</label>
      <textarea
        v-model="form.template_html"
        rows="18"
        class="w-full border rounded-lg px-3 py-2 font-mono text-sm bg-gray-50 focus:ring focus:ring-blue-300"
      ></textarea>
    </div>

    <!-- CSS Editor -->
    <div>
      <label class="font-medium block mb-1">Custom CSS</label>
      <textarea
        v-model="form.template_css"
        rows="10"
        class="w-full border rounded-lg px-3 py-2 font-mono text-sm bg-gray-50 focus:ring focus:ring-blue-300"
      ></textarea>
    </div>

    <!-- Placeholder Tools -->
    <div class="bg-gray-100 border rounded-lg p-4">
      <h3 class="font-semibold mb-2">Available Placeholders</h3>

      <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
        <button
          v-for="item in placeholders"
          :key="item.key"
          @click="insertPlaceholder(item.key)"
          class="px-3 py-2 text-sm bg-white shadow rounded-lg border hover:bg-blue-50 hover:text-blue-700 transition"
        >
          {{ item.key }}
        </button>
      </div>

      <p class="text-xs text-gray-600 mt-2">
        Click any placeholder to insert it into the HTML editor.
      </p>
    </div>

    <!-- Set as default -->
    <label class="flex items-center gap-2 mt-4">
      <input type="checkbox" v-model="form.is_default" />
      <span>Set as default template</span>
    </label>
<button
  @click="preview"
  class="w-full bg-gray-800 text-white py-3 rounded-lg font-semibold hover:bg-gray-900 transition"
>
  Preview Template
</button>

    <!-- Sticky Save Bar -->
    <div class="sticky bottom-0 bg-white py-3 border-t">
      <button
        @click="save"
        class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition"
      >
        Save Template
      </button>
    </div>
  </div>
  <div v-if="showPreview" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white w-[80%] h-[80%] rounded-lg overflow-hidden relative">

    <button
      @click="showPreview = false"
      class="absolute top-3 right-3 text-gray-600 hover:text-black"
    >
      ✕
    </button>

    <iframe
      :srcdoc="previewHtml"
      class="w-full h-full border-none"
    ></iframe>

  </div>
</div>

</template>

<script setup>
import { reactive, ref } from 'vue'

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

// Full list of placeholders
const placeholders = [
  // Company / Tenant
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

  // Invoice-level
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

  // Items (loop wrapper)
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
    label: 'Invoice Items Table Rows'
  },

  // Formatted
  { key: '{{ number_format($invoice->subtotal, 2) }}', label: 'Formatted Subtotal' },
  { key: '{{ number_format($invoice->total, 2) }}', label: 'Formatted Total' },
  { key: '{{ number_format($item->quantity * $item->price, 2) }}', label: 'Formatted Item Total' },
]

// Insert placeholder into editor
const insertPlaceholder = (key) => {
  form.template_html += key
}

// Preview locally
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
      ${form.invoice.items.map(i => `<tr><td>${i.name}</td><td>${i.description}</td><td>${i.quantity}</td><td>${i.price}</td><td>${i.total}</td></tr>`).join('')}
    </table>
  `
  previewHtml.value = html
  showPreview.value = true
}

const save = async () => {
  alert('Template Saved Successfully!')
}
</script>



<style>
/* Optional subtle shadow for editors */
textarea {
  transition: box-shadow 0.2s ease;
}
textarea:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
}
</style>
