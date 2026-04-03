<template>
  <div class="legal-form">

    <h2 class="title">Legal Information</h2>

    <form @submit.prevent="save" class="form-container">

      <!-- COMPANY NAME -->
      <label class="form-label">Full Legal Company Name</label>
      <p class="desc">Official registered name of the company.</p>
      <input class="form-input" v-model="form.company_name" placeholder="Ex: Example GmbH">

      <!-- LEGAL FORM -->
      <label class="form-label">Legal Form</label>
      <p class="desc">Select your company’s legal structure.</p>
      <select class="form-input" v-model="form.legal_form">
        <option>UG</option>
        <option>GmbH</option>
        <option>SARL</option>
        <option>Sole Trader</option>
        <option>Ltd</option>
        <option>BV</option>
        <option>OÜ</option>
      </select>

      <!-- ADDRESS AUTOCOMPLETE -->
      <label class="form-label">Registered Address</label>
      <p class="desc">Enter your official address.</p>

      <div class="autocomplete-wrapper">
        <input
          type="text"
          class="form-input"
          v-model="search"
          placeholder="Search address"
          @input="fetchSuggestions"
          @focus="showList = true"
          @blur="hideList"
        />

        <ul v-if="showList && suggestions.length" class="suggestion-list">
          <li
            v-for="item in suggestions"
            :key="item.place_id"
            class="suggestion-item"
            @mousedown.prevent="selectSuggestion(item)"
          >
            {{ item.description }}
          </li>
        </ul>
      </div>

      <!-- REPRESENTATIVE -->
      <label class="form-label">Authorized Representative</label>
      <input class="form-input" v-model="form.authorized_representative">

      <!-- EMAIL -->
      <label class="form-label">Email</label>
      <input class="form-input" v-model="form.email">

      <!-- PHONE -->
      <label class="form-label">Phone</label>
      <input class="form-input" v-model="form.phone">

      <!-- REG COURT -->
      <label class="form-label">Registration Court</label>
      <input class="form-input" v-model="form.registration_court">

      <!-- COMMERCIAL REGISTER NUMBER -->
      <label class="form-label">Commercial Register Number</label>
      <input class="form-input" v-model="form.commercial_register_number">

      <!-- VAT -->
      <label class="form-label">VAT ID</label>
      <input class="form-input" v-model="form.vat_id">

      <!-- OSS -->
      <label class="mt-3">
        <input type="checkbox" v-model="form.oss">
        <span>Part of the EU OSS Scheme</span>
      </label>

      <button type="submit" class="btn-save">Save Data</button>
    </form>
  </div>
</template>

<script>
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'

export default {
  data() {
    return {
      search: "",
      suggestions: [],
      showList: false,
      autocompleteService: null,
      placesService: null,

      form: {
        company_name: "",
        legal_form: "",
        registered_address: "",
        authorized_representative: "",
        email: "",
        phone: "",
        registration_court: "",
        commercial_register_number: "",
        vat_id: "",
        oss: false,
      }
    };
  },

  mounted() {
    this.loadGoogle();
    this.loadLegalInfo();
  },

  methods: {

async loadLegalInfo() {
      try {
        const { data } = await axiosTenant.get("/legal-info");

        if (!data || data.length === 0) return;

        const info = data[0];

        this.form = {
          company_name: info.company_name || "",
          legal_form: info.legal_form || "",
          registered_address: info.registered_address || "",
          authorized_representative: info.authorized_representative || "",
          email: info.email || "",
          phone: info.phone || "",
          registration_court: info.registration_court || "",
          commercial_register_number: info.commercial_register_number || "",
          vat_id: info.vat_id || "",
          oss: !!info.oss,
        };

        this.search = info.registered_address;

      } catch (error) {
        console.error("Failed to load legal info", error);
      }
    },


    /** Load Google Places API */
    loadGoogle() {
      if (window.google && window.google.maps) {
        this.initServices();
        return;
      }

      const script = document.createElement("script");
      script.src =
        `https://maps.google.com/maps/api/js?key=AIzaSyCZDgTTb7vm0co-2yHGinkgSs_yDTNtbSo&libraries=places`;
      script.async = true;
      script.defer = true;
      script.onload = this.initServices;

      document.head.appendChild(script);
    },

    initServices() {
      this.autocompleteService = new google.maps.places.AutocompleteService();
      this.placesService = new google.maps.places.PlacesService(document.createElement("div"));
    },

    fetchSuggestions() {
      if (!this.search) {
        this.suggestions = [];
        return;
      }

      this.autocompleteService.getPlacePredictions(
        {
          input: this.search,
          types: ["address"],
        },
        (predictions) => {
          this.suggestions = predictions || [];
        }
      );
    },

    selectSuggestion(item) {
      this.search = item.description;
      this.showList = false;

      this.placesService.getDetails(
        { placeId: item.place_id },
        (place) => {
          this.form.registered_address = place.formatted_address; // FIXED
        }
      );
    },

    hideList() {
      setTimeout(() => {
        this.showList = false;
      }, 200);
    },

   async save() {
  try {
    console.log("SAVING:", this.form);
    await axiosTenant.post("/legal-info", this.form);
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
    console.error(error);
  }
}


  }
};
</script>






<style>
.legal-form { max-width: 600px; margin: auto; }
.form-label { font-weight: bold; margin-top: 15px; }
.desc { margin: 2px 0 10px; font-size: 0.85rem; color: #666; }
.form-input { width: 100%; padding: 10px; margin-bottom: 10px; }
.btn-save {
  margin-top: 20px; padding: 10px 15px; background: #3b82f6;
  color: white; border: none; cursor: pointer; border-radius: 4px;
}




.autocomplete-wrapper {
  position: relative;
}

.suggestion-list {
  position: absolute;
  background: white;
  list-style: none;
  padding: 0;
  margin: 5px 0 0;
  width: 100%;
  border-radius: 8px;
  border: 1px solid #ddd;
  box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
  z-index: 50;
}

.suggestion-item {
  padding: 12px;
  cursor: pointer;
}

.suggestion-item:hover {
  background: #f2f2f2;
}

</style>
