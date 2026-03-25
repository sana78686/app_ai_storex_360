<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axiosTenant from '@/api/axiosTenant'

const route = useRoute()
const order = ref(null)
const invoice = ref(null)

const fetchOrder = async () => {
    const { data } = await axiosTenant.get(`/orders/${route.params.id}`)
    order.value = data.order
    invoice.value = data.invoice
}

onMounted(fetchOrder)
</script>

<template>
  <div v-if="order">
    <h1 class="text-xl font-bold mb-4">
      Order {{ order.order_number }}
    </h1>

    <p><strong>Email:</strong> {{ order.customer_email }}</p>
    <p><strong>Status:</strong> {{ order.payment_status }}</p>
    <p><strong>Total:</strong> {{ order.total }} {{ order.currency }}</p>

    <hr class="my-4" />

    <div v-if="invoice">
      <h2 class="font-semibold mb-2">Invoice</h2>

      <p>Invoice #: {{ invoice.number }}</p>

      <a
        :href="invoice.pdf_url"
        target="_blank"
        class="text-blue-600 underline"
      >
        Download Invoice (PDF)
      </a>
    </div>

    <div v-else>
      <p>No invoice generated yet.</p>
    </div>
  </div>
</template>
