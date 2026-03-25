<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
      <h1 class="text-xl font-bold">Drafts</h1>
      <button class="bg-[#008060] text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-[#006e52]">
        Create order
      </button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-600 font-medium">
          <tr>
            <th class="px-4 py-3">Draft</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3">Customer</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="i in 3" :key="i" class="hover:bg-gray-50">
            <td class="px-4 py-4 font-bold text-blue-600">#D{{ i + 20 }}</td>
            <td class="px-4 py-4 text-gray-500">Jan 22, 2026</td>
            <td class="px-4 py-4">Anna Schmidt</td>
            <td class="px-4 py-4">
              <span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded-full text-xs font-medium">Open</span>
            </td>
            <td class="px-4 py-4 text-right">€120.99</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
