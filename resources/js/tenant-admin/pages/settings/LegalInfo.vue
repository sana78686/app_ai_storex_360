<template>
  <form class="legal-form tenant-settings-stack max-w-2xl" @submit.prevent="save">
    <div class="tenant-settings-field-group">
      <div class="tenant-float-field">
        <input id="legal-company" v-model="form.company_name" type="text" placeholder=" " />
        <label for="legal-company">Full legal company name</label>
      </div>
      <p class="tenant-settings-hint">Official registered name of the company.</p>
    </div>

    <div class="tenant-settings-field-group">
      <div class="tenant-label-row">
        <span class="tenant-field-label">Legal form</span>
        <TenantFieldTip label="Legal form" text="Your company type (for example GmbH or Ltd). Pick the one that matches your registration." />
      </div>
      <TenantSelectSearch
        v-model="form.legal_form"
        input-id="legal-form"
        placeholder="Search legal form…"
        :options="legalFormOptions"
      />
      <p class="tenant-settings-hint">Your company’s legal structure.</p>
    </div>

    <div class="tenant-settings-field-group">
      <div class="legal-autocomplete relative">
        <div class="tenant-float-field">
          <input
            id="legal-addr-search"
            v-model="search"
            type="text"
            placeholder=" "
            autocomplete="off"
            @input="fetchSuggestions"
            @focus="showList = true"
            @blur="hideList"
          />
          <label for="legal-addr-search">Registered address</label>
        </div>
        <p class="tenant-settings-hint">Search and select your official address.</p>
        <ul v-if="showList && suggestions.length" class="legal-autocomplete__list">
          <li
            v-for="item in suggestions"
            :key="item.place_id"
            class="legal-autocomplete__item"
            @mousedown.prevent="selectSuggestion(item)"
          >
            {{ item.description }}
          </li>
        </ul>
      </div>
    </div>

    <div class="tenant-float-field">
      <input id="legal-rep" v-model="form.authorized_representative" type="text" placeholder=" " />
      <label for="legal-rep">Authorized representative</label>
    </div>

    <div class="tenant-float-field">
      <input id="legal-email" v-model="form.email" type="email" placeholder=" " autocomplete="email" />
      <label for="legal-email">Email</label>
    </div>

    <div class="tenant-float-field">
      <input id="legal-phone" v-model="form.phone" type="text" placeholder=" " autocomplete="tel" />
      <label for="legal-phone">Phone</label>
    </div>

    <div class="tenant-float-field">
      <input id="legal-court" v-model="form.registration_court" type="text" placeholder=" " />
      <label for="legal-court">Registration court</label>
    </div>

    <div class="tenant-float-field">
      <input id="legal-regno" v-model="form.commercial_register_number" type="text" placeholder=" " />
      <label for="legal-regno">Commercial register number</label>
    </div>

    <div class="tenant-float-field">
      <input id="legal-vat" v-model="form.vat_id" type="text" placeholder=" " />
      <label for="legal-vat">VAT ID</label>
    </div>

    <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-gray-800">
      <input v-model="form.oss" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-[#275a19] focus:ring-[#275a19]" />
      <span>Part of the EU OSS scheme</span>
    </label>

  </form>
</template>

<script>
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'
import TenantFieldTip from '@tenant/components/TenantFieldTip.vue'
import TenantSelectSearch from '@tenant/components/TenantSelectSearch.vue'
import { SETTINGS_STICKY_KEY } from '@tenant/settings/settingsStickyContext'

export default {
  components: { TenantFieldTip, TenantSelectSearch },
  inject: {
    settingsStickySave: { from: SETTINGS_STICKY_KEY, default: null },
  },
  data() {
    return {
      search: '',
      suggestions: [],
      showList: false,
      autocompleteService: null,
      placesService: null,

      legalFormOptions: [
        { value: '', label: '— Select —' },
        { value: 'UG', label: 'UG' },
        { value: 'GmbH', label: 'GmbH' },
        { value: 'SARL', label: 'SARL' },
        { value: 'Sole Trader', label: 'Sole Trader' },
        { value: 'Ltd', label: 'Ltd' },
        { value: 'BV', label: 'BV' },
        { value: 'OÜ', label: 'OÜ' },
      ],
      form: {
        company_name: '',
        legal_form: '',
        registered_address: '',
        authorized_representative: '',
        email: '',
        phone: '',
        registration_court: '',
        commercial_register_number: '',
        vat_id: '',
        oss: false,
      },
    }
  },

  mounted() {
    this.loadGoogle()
    this.loadLegalInfo()
    this.settingsStickySave?.setSave(() => this.save())
  },

  beforeUnmount() {
    this.settingsStickySave?.clearSave()
  },

  methods: {
    async loadLegalInfo() {
      try {
        const res = await axiosTenant.get('/legal-info')
        if (res.data) {
          this.form = { ...this.form, ...res.data }
          this.search = res.data.registered_address || ''
        }
      } catch (e) {
        console.error(e)
      }
    },

    loadGoogle() {
      if (!window.google?.maps?.places) return
      this.autocompleteService = new google.maps.places.AutocompleteService()
      this.placesService = new google.maps.places.PlacesService(document.createElement('div'))
    },

    fetchSuggestions() {
      if (!this.autocompleteService || !this.search) {
        this.suggestions = []
        return
      }
      this.autocompleteService.getPlacePredictions({ input: this.search }, (preds) => {
        this.suggestions = preds || []
      })
    },

    selectSuggestion(item) {
      if (!this.placesService) return
      this.placesService.getDetails({ placeId: item.place_id }, (place) => {
        if (place?.formatted_address) {
          this.form.registered_address = place.formatted_address
          this.search = place.formatted_address
        }
        this.suggestions = []
        this.showList = false
      })
    },

    hideList() {
      setTimeout(() => {
        this.showList = false
      }, 200)
    },

    async save() {
      try {
        await axiosTenant.post('/legal-info', this.form)
        await Swal.fire({
          icon: 'success',
          title: 'Saved',
          text: 'Saved successfully!',
        })
      } catch (error) {
        await Swal.fire({
          icon: 'error',
          title: 'Save failed',
          text: error.response?.data?.message || error.message || 'Something went wrong',
        })
        console.error(error)
      }
    },
  },
}
</script>

<style scoped>
.legal-autocomplete__list {
  position: absolute;
  left: 0;
  right: 0;
  top: 100%;
  z-index: 50;
  margin: 0.25rem 0 0;
  list-style: none;
  padding: 0.25rem 0;
  border-radius: 0.625rem;
  border: 1px solid #e5e7eb;
  background: #fff;
  box-shadow: 0 10px 25px -10px rgb(15 23 42 / 0.15);
  max-height: 220px;
  overflow-y: auto;
}

.legal-autocomplete__item {
  padding: 0.65rem 0.85rem;
  cursor: pointer;
  font-size: 0.875rem;
  color: #374151;
}

.legal-autocomplete__item:hover {
  background: #f3f4f6;
}
</style>
