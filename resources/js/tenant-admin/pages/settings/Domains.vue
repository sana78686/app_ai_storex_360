<template>
  <div class="p-4">

    <h3 class="mb-4">🌍 Custom Domains</h3>

    <!-- ADD DOMAIN -->
    <div class="card p-3 mb-4">
      <div class="d-flex">
        <input
          v-model="newDomain"
          class="form-control me-2"
          placeholder="example.com or www.example.com"
        />

        <button
          class="btn btn-primary"
          @click="addDomain"
          :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          Add Domain
        </button>
      </div>
    </div>

    <!-- DNS SETUP BOX -->
    <div v-if="dnsInfo" class="card border-warning mb-4">

      <div class="card-body">

        <h5 class="text-warning mb-3">⚠ DNS Setup Required</h5>

        <p class="mb-2">
          Add this DNS record in your domain provider (Cloudflare, GoDaddy, etc)
        </p>

        <table class="table table-sm table-bordered text-center align-middle">
          <thead>
            <tr>
              <th>Type</th>
              <th>Host</th>
              <th>Value</th>
              <th></th>
            </tr>
          </thead>

          <tbody>
            <tr>
              <td>{{ dnsInfo.type }}</td>
              <td>{{ dnsInfo.name }}</td>
              <td>{{ dnsInfo.value }}</td>
              <td>
                <button class="btn btn-sm btn-outline-dark"
                        @click="copy(dnsInfo.value)">
                  Copy
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <small class="text-muted">
          DNS propagation may take 5–30 minutes. Click verify after setup.
        </small>
      </div>
    </div>

    <!-- DOMAIN LIST -->
    <div class="card">
      <table class="table mb-0 table-hover align-middle">

        <thead class="table-light">
          <tr>
            <th>Domain</th>
            <th>Status</th>
            <th>Primary</th>
            <th width="260">Actions</th>
          </tr>
        </thead>

        <tbody>

          <tr v-for="d in domains" :key="d.id">

            <!-- DOMAIN -->
            <td>
              <a :href="'https://' + d.domain" target="_blank">
                {{ d.domain }}
              </a>
            </td>

            <!-- STATUS -->
            <td>
              <span
                :class="[
                  'badge',
                  d.status === 'active'
                    ? 'bg-success'
                    : d.status === 'pending'
                      ? 'bg-warning text-dark'
                      : 'bg-danger'
                ]">
                {{ d.status }}
              </span>
            </td>

            <!-- PRIMARY -->
            <td>
              <span v-if="d.is_primary" class="badge bg-primary">
                Primary
              </span>
            </td>

            <!-- ACTIONS -->
            <td>

              <button
                v-if="d.status === 'pending'"
                class="btn btn-sm btn-success me-2"
                @click="verify(d)">
                Verify
              </button>

              <button
                v-if="d.status === 'active' && !d.is_primary"
                class="btn btn-sm btn-secondary me-2"
                @click="makePrimary(d)">
                Make Primary
              </button>

              <button
                class="btn btn-sm btn-danger"
                @click="remove(d)">
                Delete
              </button>

            </td>

          </tr>

        </tbody>

      </table>
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
      loading: false
    }
  },

  mounted() {
    this.load()
  },

  methods: {

    async load() {
      const res = await axiosTenant.get('/domains')
      this.domains = res.data
    },

    async addDomain() {
      if (!this.newDomain) return

      this.loading = true

      try {
        const res = await axiosTenant.post('/domains', {
          domain: this.newDomain
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
    }
  }
}
</script>
