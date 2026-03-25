<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4 border-b border-gray-200">
      <h1 class="text-xl font-bold">Abandoned checkouts</h1>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-600 border-b">
          <tr>
            <th class="px-4 py-3">Checkout</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3">Customer</th>
            <th class="px-4 py-3">Email status</th>
            <th class="px-4 py-3">Recovery status</th>
            <th class="px-4 py-3 text-right">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
          <tr v-for="i in 2" :key="i" class="hover:bg-gray-50">
            <td class="px-4 py-4 font-medium">#129384{{ i }}</td>
            <td class="px-4 py-4">3 hours ago</td>
            <td class="px-4 py-4">customer@example.com</td>
            <td class="px-4 py-4 text-gray-500">Not sent</td>
            <td class="px-4 py-4">
              <span class="bg-red-50 text-red-700 px-2 py-0.5 rounded-full text-xs font-medium">Not recovered</span>
            </td>
            <td class="px-4 py-4 text-right font-bold">€29.00</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
