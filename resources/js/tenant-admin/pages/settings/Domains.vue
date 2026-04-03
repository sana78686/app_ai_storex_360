<template>
  <div class="domains-settings space-y-6 text-[0.9375rem] leading-relaxed text-gray-700">
    <!-- Add domain -->
    <div class="rounded-xl border border-gray-200 bg-gray-50/40 p-4 sm:p-5">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-stretch">
        <input
          v-model="newDomain"
          type="text"
          placeholder="example.com or www.example.com"
          class="min-h-[44px] min-w-0 flex-1 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-[#275a19] focus:outline-none focus:ring-2 focus:ring-[#275a19]/20"
        />
        <button
          type="button"
          class="tenant-btn-submit inline-flex shrink-0 items-center justify-center gap-2 sm:px-8"
          :disabled="loading"
          @click="addDomain"
        >
          <span
            v-if="loading"
            class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-solid border-white border-r-transparent"
            aria-hidden="true"
          />
          Add Domain
        </button>
      </div>
    </div>

    <!-- DNS setup -->
    <div
      v-if="dnsInfo"
      class="rounded-xl border border-amber-200/90 bg-amber-50/50 p-4 sm:p-5"
    >
      <h2 class="text-sm font-bold text-amber-900">DNS setup required</h2>
      <p class="mt-2 text-[0.9375rem] leading-relaxed text-amber-900/85">
        Add this DNS record at your domain provider (Cloudflare, GoDaddy, etc.).
      </p>

      <div class="tenant-data-table-wrap mt-4">
        <table class="tenant-data-table">
          <thead>
            <tr>
              <th>Type</th>
              <th>Host</th>
              <th>Value</th>
              <th class="w-28 text-right sm:text-left" />
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="font-mono text-sm">{{ dnsInfo.type }}</td>
              <td class="font-mono text-sm">{{ dnsInfo.name }}</td>
              <td class="max-w-[200px] truncate font-mono text-xs text-gray-600 sm:max-w-none">
                {{ dnsInfo.value }}
              </td>
              <td class="text-right sm:text-left">
                <button type="button" class="tenant-btn-secondary tenant-btn-sm" @click="copy(dnsInfo.value)">
                  Copy
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <p class="mt-3 text-sm text-amber-800/80">
        DNS propagation can take 5–30 minutes. Use Verify after the record is live.
      </p>
    </div>

    <!-- Domain list -->
    <div v-if="domains.length" class="tenant-data-table-wrap">
      <table class="tenant-data-table">
        <thead>
          <tr>
            <th>Domain</th>
            <th>Status</th>
            <th>Primary</th>
            <th class="min-w-[220px]">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="d in domains" :key="d.id">
            <td>
              <a
                :href="'https://' + d.domain"
                target="_blank"
                rel="noopener noreferrer"
                class="font-medium text-[#275a19] underline-offset-2 hover:underline"
              >
                {{ d.domain }}
              </a>
            </td>
            <td>
              <span
                class="tenant-badge"
                :class="statusBadgeClass(d.status)"
              >
                {{ d.status }}
              </span>
            </td>
            <td>
              <span v-if="d.is_primary" class="tenant-badge tenant-badge--primary">Primary</span>
              <span v-else class="text-sm text-gray-400">—</span>
            </td>
            <td>
              <div class="flex flex-wrap items-center gap-2">
                <button
                  v-if="d.status === 'pending'"
                  type="button"
                  class="tenant-btn-outline-accent tenant-btn-sm"
                  @click="verify(d)"
                >
                  Verify
                </button>
                <button
                  v-if="d.status === 'active' && !d.is_primary"
                  type="button"
                  class="tenant-btn-secondary tenant-btn-sm"
                  @click="makePrimary(d)"
                >
                  Make primary
                </button>
                <button type="button" class="tenant-btn-danger tenant-btn-sm" @click="remove(d)">
                  Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div
      v-else
      class="rounded-xl border border-dashed border-gray-200 bg-gray-50/50 px-4 py-12 text-center text-[0.9375rem] text-gray-500"
    >
      No domains yet. Add your first domain above.
    </div>
  </div>
</template>

<script>
import axiosTenant from '@/api/axiosTenant'

export default {
  data() {
    return {
      domains: [],
      newDomain: '',
      dnsInfo: null,
      loading: false,
    }
  },

  mounted() {
    this.load()
  },

  methods: {
    statusBadgeClass(status) {
      if (status === 'active') return 'tenant-badge--success'
      if (status === 'pending') return 'tenant-badge--pending'
      return 'tenant-badge--error'
    },

    async load() {
      const res = await axiosTenant.get('/domains')
      this.domains = res.data
    },

    async addDomain() {
      if (!this.newDomain) return

      this.loading = true

      try {
        const res = await axiosTenant.post('/domains', {
          domain: this.newDomain,
        })

        this.dnsInfo = res.data.dns
        this.newDomain = ''
        this.load()
      } finally {
        this.loading = false
      }
    },

    async verify(d) {
      await axiosTenant.post(`/domains/${d.id}/verify`)
      this.load()
    },

    async makePrimary(d) {
      await axiosTenant.post(`/domains/${d.id}/primary`)
      this.load()
    },

    async remove(d) {
      await axiosTenant.delete(`/domains/${d.id}`)
      this.load()
    },

    copy(text) {
      navigator.clipboard.writeText(text)
    },
  },
}
</script>
