<template>
  <div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end sm:justify-between">
      <div class="min-w-0">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
          {{ t('dashboardHome.title') }}
        </h1>
        <p class="mt-2 max-w-2xl text-base leading-relaxed text-gray-600">
          {{ t('dashboardHome.lead') }}
        </p>
        <p class="mt-2 text-sm text-gray-500">
          {{ t('dashboardHome.sub') }}
        </p>
      </div>
      <router-link
        to="/dashboard/order/create"
        class="inline-flex shrink-0 items-center justify-center rounded-xl bg-[#275a19] px-4 py-2.5 text-sm font-bold text-white no-underline shadow-sm transition-colors hover:bg-[#1f4814] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#275a19]/50"
      >
        {{ t('adminLayout.pos') }}
      </router-link>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <div
        v-for="card in statCards"
        :key="card.key"
        class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm"
      >
        <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">
          {{ card.label }}
        </p>
        <p class="mt-2 text-2xl font-extrabold tracking-tight text-gray-900">
          {{ card.value }}
        </p>
        <p class="mt-1 text-xs font-semibold text-[#275a19]">
          {{ card.delta }}
        </p>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
      <div class="rounded-2xl border border-gray-200/80 bg-white p-5 shadow-sm lg:col-span-2">
        <h2 class="text-sm font-bold text-gray-900">
          {{ t('dashboardHome.chartTitle') }}
        </h2>
        <p class="mt-1 text-xs text-gray-500">
          {{ t('dashboardHome.sub') }}
        </p>
        <div class="mt-6 flex h-44 items-end gap-1.5 sm:gap-2">
          <div
            v-for="(pct, i) in chartBars"
            :key="i"
            class="min-w-0 flex-1 rounded-t-md bg-gradient-to-t from-[#1a4012] to-[#275a19]/85 transition-opacity hover:opacity-90"
            :style="{ height: `${Math.max(8, pct)}%` }"
            :title="`Week ${i + 1}`"
          />
        </div>
        <div class="mt-2 flex justify-between text-[10px] font-medium uppercase tracking-wide text-gray-400">
          <span>W1</span>
          <span>W4</span>
          <span>W8</span>
          <span>W12</span>
        </div>
      </div>

      <div class="flex flex-col gap-3">
        <div
          v-for="item in opsCards"
          :key="item.key"
          class="flex flex-1 flex-col justify-center rounded-2xl border border-gray-200/80 bg-white px-4 py-4 shadow-sm"
        >
          <p class="text-xs font-semibold text-gray-500">
            {{ item.label }}
          </p>
          <p class="mt-1 text-2xl font-extrabold text-gray-900">
            {{ item.value }}
          </p>
        </div>
      </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm">
      <div class="border-b border-gray-100 px-5 py-4">
        <h2 class="text-sm font-bold text-gray-900">
          {{ t('dashboardHome.recentOrders') }}
        </h2>
        <p class="mt-0.5 text-xs text-gray-500">
          {{ t('dashboardHome.recentSub') }}
        </p>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
          <thead class="border-b border-gray-100 bg-gray-50/80 text-[11px] font-bold uppercase tracking-wider text-gray-500">
            <tr>
              <th class="px-5 py-3">
                {{ t('dashboardHome.table.order') }}
              </th>
              <th class="px-5 py-3">
                {{ t('dashboardHome.table.customer') }}
              </th>
              <th class="px-5 py-3 text-right">
                {{ t('dashboardHome.table.total') }}
              </th>
              <th class="px-5 py-3">
                {{ t('dashboardHome.table.status') }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="row in recentOrdersDisplay" :key="row.id" class="text-gray-800">
              <td class="px-5 py-3 font-semibold text-[#275a19]">
                {{ row.id }}
              </td>
              <td class="px-5 py-3">
                {{ row.customer }}
              </td>
              <td class="px-5 py-3 text-right font-medium">
                {{ row.total }}
              </td>
              <td class="px-5 py-3">
                <span
                  class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold"
                  :class="statusClass(row.status)"
                >
                  {{ row.statusLabel }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

/** Relative bar heights for a simple sample chart (static). */
const chartBars = [32, 38, 36, 44, 41, 52, 48, 58, 55, 62, 68, 74]

const statCards = computed(() => [
  {
    key: 'revenue',
    label: t('dashboardHome.stats.revenue'),
    value: '€48,290',
    delta: '+12% vs. previous 30 days',
  },
  {
    key: 'orders',
    label: t('dashboardHome.stats.orders'),
    value: '37',
    delta: '+4 vs. yesterday (sample)',
  },
  {
    key: 'aov',
    label: t('dashboardHome.stats.aov'),
    value: '€82.40',
    delta: 'Stable (sample)',
  },
  {
    key: 'visitors',
    label: t('dashboardHome.stats.visitors'),
    value: '2,840',
    delta: '+8% vs. last week (sample)',
  },
])

const opsCards = computed(() => [
  { key: 'fulfill', label: t('dashboardHome.cards.pendingFulfillment'), value: '14' },
  { key: 'stock', label: t('dashboardHome.cards.lowStock'), value: '6' },
  { key: 'abandoned', label: t('dashboardHome.cards.abandoned'), value: '23' },
  { key: 'returns', label: t('dashboardHome.cards.returns'), value: '3' },
])

const recentOrders = [
  { id: '#10482', customer: 'Maria S.', total: '€124.90', status: 'paid' },
  { id: '#10481', customer: 'Jonas K.', total: '€59.00', status: 'processing' },
  { id: '#10480', customer: 'Elena R.', total: '€312.45', status: 'shipped' },
  { id: '#10479', customer: 'Walk-in POS', total: '€18.99', status: 'paid' },
]

function statusClass(status) {
  if (status === 'paid') return 'bg-[#275a19]/12 text-[#275a19]'
  if (status === 'shipped') return 'bg-blue-50 text-blue-800'
  return 'bg-amber-50 text-amber-900'
}

const recentOrdersDisplay = computed(() =>
  recentOrders.map((row) => ({
    ...row,
    statusLabel: t(`dashboardHome.statuses.${row.status}`),
  }))
)
</script>
