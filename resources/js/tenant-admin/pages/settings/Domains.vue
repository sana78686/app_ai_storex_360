<template>
  <div class="domains-settings space-y-6 text-[0.9375rem] leading-relaxed text-gray-700">
    <p v-if="tenantBaseDomain" class="text-[0.9375rem] text-gray-600">
      Add another hostname on
      <span class="font-medium text-gray-800">{{ tenantBaseDomain }}</span>
      (same as your default store URL), or connect a separate domain such as
      <span class="font-medium text-gray-800">www.yourbrand.com</span>
      — we detect which case from what you enter.
    </p>

    <!-- Add domain -->
    <div class="rounded-xl border border-gray-200 bg-gray-50/40 p-4 sm:p-5">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-stretch">
        <input
          id="domains-new-input"
          v-model="newDomain"
          type="text"
          :placeholder="domainPlaceholder"
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
          Add domain
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
        <table class="tenant-data-table tenant-data-table--striped">
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
    <div v-if="domains.length">
      <div class="tenant-data-table-wrap">
        <table class="tenant-data-table tenant-data-table--striped">
          <thead>
            <tr>
              <th>Domain</th>
              <th>Type</th>
              <th>Status</th>
              <th>Primary</th>
              <th class="min-w-[220px]">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="d in paginatedDomains" :key="d.id">
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
              <td class="capitalize text-gray-600">{{ d.type === 'subdomain' ? 'Platform subdomain' : 'Custom domain' }}</td>
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
                    v-if="d.status === 'pending' && d.type === 'custom'"
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
        v-if="domainPageCount > 1"
        class="mt-4 flex flex-wrap items-center justify-center gap-3 text-sm text-gray-600"
      >
        <button
          type="button"
          class="tenant-btn-secondary tenant-btn-sm"
          :disabled="domainPage <= 1"
          @click="domainPage--"
        >
          Previous
        </button>
        <span class="font-medium">Page {{ domainPage }} of {{ domainPageCount }}</span>
        <button
          type="button"
          class="tenant-btn-secondary tenant-btn-sm"
          :disabled="domainPage >= domainPageCount"
          @click="domainPage++"
        >
          Next
        </button>
      </div>
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
import Swal from 'sweetalert2'
import { SETTINGS_STICKY_KEY } from '@tenant/settings/settingsStickyContext'

const PAGE_SIZE = 8

export default {
  inject: {
    settingsStickySave: { from: SETTINGS_STICKY_KEY, default: null },
  },
  data() {
    return {
      domains: [],
      tenantBaseDomain: '',
      newDomain: '',
      dnsInfo: null,
      loading: false,
      domainPage: 1,
      domainPageSize: PAGE_SIZE,
    }
  },

  computed: {
    domainPlaceholder() {
      if (this.tenantBaseDomain) {
        return `e.g. shop or shop.${this.tenantBaseDomain} — or www.other-site.com`
      }
      return 'e.g. www.yourstore.com'
    },
    domainPageCount() {
      return Math.max(1, Math.ceil(this.domains.length / this.domainPageSize))
    },
    paginatedDomains() {
      const start = (this.domainPage - 1) * this.domainPageSize
      return this.domains.slice(start, start + this.domainPageSize)
    },
  },

  watch: {
    domains() {
      if (this.domainPage > this.domainPageCount) {
        this.domainPage = this.domainPageCount
      }
    },
  },

  mounted() {
    this.load()
    this.settingsStickySave?.setSave(() => this.stickySaveDomains())
  },

  beforeUnmount() {
    this.settingsStickySave?.clearSave()
  },

  methods: {
    async stickySaveDomains() {
      const d = (this.newDomain || '').trim()
      if (!d) {
        await Swal.fire({
          icon: 'info',
          title: 'Add a domain',
          text: 'Type a domain in the field at the top, then use Save or Add domain.',
        })
        this.$nextTick(() => document.getElementById('domains-new-input')?.focus({ preventScroll: true }))
        return
      }
      await this.addDomain()
    },

    statusBadgeClass(status) {
      if (status === 'active') return 'tenant-badge--success'
      if (status === 'pending') return 'tenant-badge--pending'
      return 'tenant-badge--error'
    },

    async load() {
      try {
        const res = await axiosTenant.get('/domains')
        const payload = res.data
        if (Array.isArray(payload)) {
          this.domains = payload
          this.tenantBaseDomain = ''
        } else {
          this.domains = payload.domains || []
          this.tenantBaseDomain = (payload.tenant_base_domain || '').trim()
        }
      } catch (err) {
        await Swal.fire({
          icon: 'error',
          title: 'Domains',
          text: err.response?.data?.message || err.message || 'Failed to load domains.',
        })
      }
    },

    async addDomain() {
      if (!this.newDomain?.trim()) return

      this.loading = true

      try {
        const res = await axiosTenant.post('/domains', {
          domain: this.newDomain.trim(),
        })

        this.dnsInfo = res.data.dns || null
        this.newDomain = ''
        await this.load()

        if (!this.dnsInfo) {
          await Swal.fire({
            icon: 'success',
            title: 'Domain added',
            text: res.data.message || 'Your platform subdomain is active.',
            timer: 2200,
            showConfirmButton: false,
          })
        }
      } catch (err) {
        await Swal.fire({
          icon: 'error',
          title: 'Could not add domain',
          text: err.response?.data?.message || err.message || 'Request failed.',
        })
        this.$nextTick(() => document.getElementById('domains-new-input')?.focus({ preventScroll: true }))
      } finally {
        this.loading = false
      }
    },

    async verify(d) {
      try {
        await axiosTenant.post(`/domains/${d.id}/verify`)
        await this.load()
        await Swal.fire({ icon: 'success', title: 'Verified', text: 'Domain verified successfully.' })
      } catch (err) {
        await Swal.fire({
          icon: 'warning',
          title: 'Not verified yet',
          text: err.response?.data?.message || 'CNAME not detected yet.',
        })
      }
    },

    async makePrimary(d) {
      try {
        await axiosTenant.post(`/domains/${d.id}/primary`)
        await this.load()
        await Swal.fire({ icon: 'success', title: 'Updated', text: 'Primary domain updated.' })
      } catch (err) {
        await Swal.fire({
          icon: 'error',
          title: 'Update failed',
          text: err.response?.data?.message || err.message,
        })
      }
    },

    async remove(d) {
      const r = await Swal.fire({
        icon: 'warning',
        title: 'Remove domain?',
        text: `Remove ${d.domain}?`,
        showCancelButton: true,
        confirmButtonText: 'Remove',
        cancelButtonText: 'Cancel',
      })
      if (!r.isConfirmed) return

      try {
        await axiosTenant.delete(`/domains/${d.id}`)
        await this.load()
        await Swal.fire({ icon: 'success', title: 'Removed', text: 'Domain removed.' })
      } catch (err) {
        await Swal.fire({
          icon: 'error',
          title: 'Delete failed',
          text: err.response?.data?.message || err.message,
        })
      }
    },

    copy(text) {
      navigator.clipboard.writeText(text)
    },
  },
}
</script>
