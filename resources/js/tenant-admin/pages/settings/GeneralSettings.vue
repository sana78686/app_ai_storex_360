<template>
  <div class="settings-general-form text-[13px] leading-snug">
    <div class="tenant-settings-form-grid">
      <!-- Primary column -->
      <div class="space-y-3">
        <div>
          <div class="tenant-label-row">
            <label class="tenant-field-label mb-0 cursor-pointer" for="gs-company">Company name</label>
            <span class="tenant-required-mark" aria-hidden="true">*</span>
            <TenantFieldTip
              label="Company name"
              text="The name we use on invoices, emails, and inside your admin. Use the name your customers know."
            />
          </div>
          <input
            id="gs-company"
            v-model="form.company_name"
            type="text"
            class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
            placeholder="My store"
            autocomplete="organization"
          />
        </div>

        <div>
          <div class="tenant-label-row">
            <label class="tenant-field-label mb-0 cursor-pointer" for="gs-email">Default email</label>
            <span class="tenant-required-mark" aria-hidden="true">*</span>
            <TenantFieldTip
              label="Default email"
              text="We send store emails from this address when no other sender is set. Use a real inbox you check."
            />
          </div>
          <input
            id="gs-email"
            v-model="form.default_email"
            type="email"
            class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
            placeholder="hello@yourstore.com"
            autocomplete="email"
          />
        </div>

        <div>
          <div class="tenant-label-row">
            <label class="tenant-field-label mb-0 cursor-pointer" for="gs-country">Country</label>
            <TenantFieldTip label="Country" text="Where your business is based. Used for taxes, invoices, and regional defaults." />
          </div>
          <input
            id="gs-country"
            v-model="form.country"
            type="text"
            class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
            placeholder=" "
            autocomplete="country-name"
          />
        </div>

        <div>
          <div class="tenant-label-row">
            <label class="tenant-field-label mb-0 cursor-pointer" for="gs-state">State / region</label>
            <TenantFieldTip label="State / region" text="Your state, province, or region. Helps with legal and shipping details." />
          </div>
          <input
            id="gs-state"
            v-model="form.state"
            type="text"
            class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
            placeholder=" "
            autocomplete="address-level1"
          />
        </div>

        <div>
          <div class="tenant-label-row">
            <label class="tenant-field-label mb-0 cursor-pointer" for="gs-city">City</label>
            <TenantFieldTip label="City" text="City or town for your business address." />
          </div>
          <input
            id="gs-city"
            v-model="form.city"
            type="text"
            class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
            placeholder=" "
            autocomplete="address-level2"
          />
        </div>
      </div>

      <!-- Secondary column -->
      <div class="space-y-3">
        <div>
          <div class="tenant-label-row">
            <span class="tenant-field-label">Company logo</span>
            <TenantFieldTip label="Logo" text="Shown in emails and some screens. Square or wide PNG or JPG works best." />
          </div>
          <input
            id="gs-logo"
            type="file"
            accept="image/png,image/jpeg,image/webp"
            class="mt-1 block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-[#275a19]/10 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-[#275a19] hover:file:bg-[#275a19]/15"
            @change="handleLogoUpload"
          />
          <div v-if="logoPreview" class="mt-2 rounded-lg border border-gray-100 bg-gray-50 p-2">
            <img :src="logoPreview" alt="" class="max-h-16 max-w-[140px] object-contain" />
          </div>
        </div>

        <div>
          <div class="tenant-label-row">
            <label class="tenant-field-label mb-0 cursor-pointer" for="gs-zip">ZIP / Postal code</label>
            <TenantFieldTip label="Postal code" text="Your mailing or postal code." />
          </div>
          <input
            id="gs-zip"
            v-model="form.zip"
            type="text"
            class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
            placeholder=" "
            autocomplete="postal-code"
          />
        </div>

        <div>
          <div class="tenant-label-row">
            <label class="tenant-field-label mb-0 cursor-pointer" for="gs-currency">Currency</label>
            <TenantFieldTip label="Currency" text="Short code like USD, EUR, or PKR. Used as a default where amounts appear." />
          </div>
          <input
            id="gs-currency"
            v-model="form.currency"
            type="text"
            class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
            placeholder="USD"
          />
        </div>

        <div>
          <div class="tenant-label-row">
            <label class="tenant-field-label mb-0 cursor-pointer" for="gs-calling">Calling code</label>
            <TenantFieldTip label="Calling code" text="Your country phone prefix, for example +1 or +49." />
          </div>
          <input
            id="gs-calling"
            v-model="form.calling_code"
            type="text"
            class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
            placeholder="+1"
          />
        </div>

        <div>
          <div class="tenant-label-row">
            <label class="tenant-field-label mb-0 cursor-pointer" for="gs-tz">Timezone</label>
            <TenantFieldTip label="Timezone" text="Used for order times and scheduled tasks. Use a name like Europe/Berlin." />
          </div>
          <input
            id="gs-tz"
            v-model="form.timezone"
            type="text"
            class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
            placeholder="UTC"
          />
        </div>

        <div>
          <div class="tenant-label-row">
            <span class="tenant-field-label">Date format</span>
            <TenantFieldTip label="Date format" text="How dates look in your admin and documents. Type to search the list." />
          </div>
          <TenantSelectSearch
            v-model="form.date_format"
            input-id="gs-datefmt"
            placeholder="Search date format…"
            :options="dateFormatOptions"
          />
        </div>

        <div>
          <div class="tenant-label-row">
            <span class="tenant-field-label">Datetime format</span>
            <TenantFieldTip label="Datetime format" text="How date and time appear together. Type to search." />
          </div>
          <TenantSelectSearch
            v-model="form.datetime_format"
            input-id="gs-dtfmt"
            placeholder="Search datetime format…"
            :options="datetimeFormatOptions"
          />
        </div>
      </div>

      <!-- Full width -->
      <div class="rounded-lg border border-gray-200 bg-gray-50/70 p-3 lg:col-span-2">
        <div class="tenant-label-row mb-2">
          <span class="tenant-field-label">Maintenance mode</span>
          <TenantFieldTip
            label="Maintenance mode"
            text="When on, visitors may see a maintenance page. Use for short updates."
          />
        </div>
        <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-gray-800">
          <input
            v-model="form.maintenance_mode"
            type="checkbox"
            class="h-4 w-4 rounded border-gray-300 text-[#275a19] focus:ring-[#275a19]"
          />
          <span>Enable maintenance mode</span>
        </label>
        <div class="mt-2">
          <div class="tenant-label-row">
            <label class="tenant-field-label mb-0 cursor-pointer" for="gs-maint-msg">Maintenance message</label>
            <TenantFieldTip label="Message" text="Short text visitors see when maintenance mode is on." />
          </div>
          <input
            id="gs-maint-msg"
            v-model="form.maintenance_message"
            type="text"
            class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
            placeholder="We’ll be back soon."
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'
import { focusFirstValidationField } from '@tenant/helpers/formFocus'
import TenantFieldTip from '@tenant/components/TenantFieldTip.vue'
import TenantSelectSearch from '@tenant/components/TenantSelectSearch.vue'
import { SETTINGS_STICKY_KEY } from '@tenant/settings/settingsStickyContext'

const GENERAL_VALIDATION_FIELD_IDS = {
  company_name: 'gs-company',
  default_email: 'gs-email',
  country: 'gs-country',
  state: 'gs-state',
  city: 'gs-city',
  zip: 'gs-zip',
  currency: 'gs-currency',
  calling_code: 'gs-calling',
  timezone: 'gs-tz',
  date_format: 'gs-datefmt',
  datetime_format: 'gs-dtfmt',
  maintenance_message: 'gs-maint-msg',
}

export default {
  name: 'GeneralSettings',
  components: { TenantFieldTip, TenantSelectSearch },
  inject: {
    settingsStickySave: { from: SETTINGS_STICKY_KEY, default: null },
  },
  data() {
    return {
      form: {
        company_name: '',
        default_email: '',
        country: '',
        country_code: '',
        state: '',
        city: '',
        zip: '',
        ip_address: '',
        latitude: '',
        longitude: '',
        currency: '',
        calling_code: '',
        timezone: '',
        date_format: '',
        datetime_format: '',
        maintenance_mode: false,
        maintenance_message: '',
        logo: null,
      },
      logoPreview: null,
      date_format: [
        'YYYY-MM-DD',
        'DD-MM-YYYY',
        'MM-DD-YYYY',
        'DD/MM/YYYY',
        'MM/DD/YYYY',
        'YYYY/MM/DD',
        'DD MMM YYYY',
        'MMM DD, YYYY',
      ],
      datetime_format: [
        'YYYY-MM-DD HH:mm',
        'YYYY-MM-DD hh:mm A',
        'YYYY/MM/DD HH:mm',
        'YYYY/MM/DD hh:mm A',
        'DD-MM-YYYY HH:mm',
        'DD-MM-YYYY hh:mm A',
        'DD/MM/YYYY HH:mm',
        'DD/MM/YYYY hh:mm A',
        'MM-DD-YYYY HH:mm',
        'MM-DD-YYYY hh:mm A',
        'MM/DD/YYYY HH:mm',
        'MM/DD/YYYY hh:mm A',
        'DD MMM YYYY HH:mm',
        'DD MMM YYYY hh:mm A',
        'MMM DD, YYYY HH:mm',
        'MMM DD, YYYY hh:mm A',
        'MMMM DD, YYYY HH:mm',
        'MMMM DD, YYYY hh:mm A',
        'YYYY-MM-DDTHH:mm',
        'YYYY-MM-DDTHH:mm:ss',
        'YYYY-MM-DD HH:mm:ss',
        'DD MMMM YYYY, HH:mm',
        'DD MMMM YYYY, hh:mm A',
        'dddd, MMMM DD YYYY, HH:mm',
        'dddd, MMMM DD YYYY, hh:mm A',
      ],
    }
  },

  computed: {
    dateFormatOptions() {
      return [{ value: '', label: '— None —' }, ...this.date_format.map((d) => ({ value: d, label: d }))]
    },
    datetimeFormatOptions() {
      return [{ value: '', label: '— None —' }, ...this.datetime_format.map((d) => ({ value: d, label: d }))]
    },
  },

  mounted() {
    this.loadSettings()
    this.settingsStickySave?.setSave(() => this.saveSettings())
    this.$nextTick(() => {
      document.getElementById('gs-company')?.focus({ preventScroll: true })
    })
  },

  beforeUnmount() {
    this.settingsStickySave?.clearSave()
  },

  methods: {
    async loadSettings() {
      try {
        const res = await axiosTenant.get('/settings/general')
        if (res.data) {
          this.form = {
            ...this.form,
            ...res.data,
            logo: null,
            maintenance_mode: res.data.maintenance_mode == 1,
          }
          if (res.data.logo && typeof res.data.logo === 'string' && res.data.logo.length > 0) {
            this.logoPreview = res.data.logo
          } else {
            this.logoPreview = null
          }
        }
      } catch (err) {
        console.error('Error fetching settings', err)
      }
    },

    handleLogoUpload(event) {
      const file = event.target.files[0]
      if (file && file.type.startsWith('image/')) {
        this.form.logo = file
        this.logoPreview = URL.createObjectURL(file)
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Invalid file',
          text: 'Please select a valid image file (png, jpg, jpeg, webp).',
        })
        event.target.value = null
        this.form.logo = null
        this.logoPreview = null
      }
    },

    async saveSettings() {
      const company = (this.form.company_name || '').trim()
      const email = (this.form.default_email || '').trim()
      if (!company || !email) {
        await this.$nextTick()
        const id = !company ? 'gs-company' : 'gs-email'
        const el = document.getElementById(id)
        el?.scrollIntoView({ block: 'center', behavior: 'smooth' })
        el?.focus({ preventScroll: true })
        await Swal.fire({
          icon: 'warning',
          title: 'Required',
          text: !company ? 'Please enter your company name.' : 'Please enter a default email address.',
        })
        return
      }

      try {
        const fd = new FormData()
        Object.keys(this.form).forEach((key) => {
          if (key === 'logo') return
          if (key === 'maintenance_mode') {
            fd.append(key, this.form[key] ? 1 : 0)
          } else {
            let val = this.form[key]
            if (val === 'null' || val === null || val === undefined) val = ''
            if (typeof val === 'object' && Object.keys(val).length === 0) val = ''
            fd.append(key, val)
          }
        })
        if (this.form.logo instanceof File) {
          fd.append('logo', this.form.logo)
        }

        await axiosTenant.post('/settings/general', fd, {
          headers: { 'Content-Type': 'multipart/form-data' },
        })

        await Swal.fire({
          icon: 'success',
          title: 'Saved',
          text: 'Settings updated successfully!',
        })
        this.loadSettings()
      } catch (err) {
        focusFirstValidationField(err, GENERAL_VALIDATION_FIELD_IDS)
        if (err.response && err.response.data && err.response.data.message) {
          await Swal.fire({
            icon: 'error',
            title: 'Error',
            text: err.response.data.message,
          })
        } else {
          await Swal.fire({
            icon: 'error',
            title: 'Save failed',
            text: 'Failed to save settings.',
          })
        }
        console.error('Error saving settings', err)
      }
    },
  },
}
</script>
