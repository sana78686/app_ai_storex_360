<template>
  <div class="tenant-settings-stack settings-form">
    <div class="tenant-float-field">
      <input id="gs-company" v-model="form.company_name" type="text" placeholder=" " autocomplete="organization" />
      <label for="gs-company">Company name</label>
    </div>

    <div class="tenant-settings-file">
      <span class="tenant-settings-file__label">Company logo</span>
      <input
        type="file"
        accept="image/png,image/jpeg,image/webp"
        class="block w-full cursor-pointer text-sm text-gray-600 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-[#275a19]/10 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-[#275a19] hover:file:bg-[#275a19]/15"
        @change="handleLogoUpload"
      />
      <div v-if="logoPreview" class="mt-2 rounded-lg border border-gray-100 bg-gray-50 p-2">
        <img :src="logoPreview" alt="" class="max-h-16 max-w-[140px] object-contain" />
      </div>
    </div>

    <div class="tenant-float-field">
      <input id="gs-email" v-model="form.default_email" type="email" placeholder=" " autocomplete="email" />
      <label for="gs-email">Default email</label>
    </div>

    <div class="tenant-float-field">
      <input id="gs-country" v-model="form.country" type="text" placeholder=" " autocomplete="country-name" />
      <label for="gs-country">Country</label>
    </div>

    <div class="tenant-float-field">
      <input id="gs-state" v-model="form.state" type="text" placeholder=" " autocomplete="address-level1" />
      <label for="gs-state">State</label>
    </div>

    <div class="tenant-float-field">
      <input id="gs-city" v-model="form.city" type="text" placeholder=" " autocomplete="address-level2" />
      <label for="gs-city">City</label>
    </div>

    <div class="tenant-float-field">
      <input id="gs-zip" v-model="form.zip" type="text" placeholder=" " autocomplete="postal-code" />
      <label for="gs-zip">ZIP / Postal code</label>
    </div>

    <div class="tenant-float-field">
      <input id="gs-currency" v-model="form.currency" type="text" placeholder=" " />
      <label for="gs-currency">Currency</label>
    </div>

    <div class="tenant-float-field">
      <input id="gs-calling" v-model="form.calling_code" type="text" placeholder=" " />
      <label for="gs-calling">Calling code</label>
    </div>

    <div class="tenant-float-field">
      <input id="gs-tz" v-model="form.timezone" type="text" placeholder=" " />
      <label for="gs-tz">Timezone</label>
    </div>

    <div class="tenant-float-field is-always-floated">
      <select id="gs-datefmt" v-model="form.date_format">
        <option v-for="tz in date_format" :key="tz" :value="tz">{{ tz }}</option>
      </select>
      <label for="gs-datefmt">Date format</label>
    </div>

    <div class="tenant-float-field is-always-floated">
      <select id="gs-dtfmt" v-model="form.datetime_format">
        <option v-for="tz in datetime_format" :key="tz" :value="tz">{{ tz }}</option>
      </select>
      <label for="gs-dtfmt">Datetime format</label>
    </div>

    <div class="rounded-lg border border-gray-200 bg-gray-50/70 p-3">
      <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-gray-800">
        <input
          v-model="form.maintenance_mode"
          type="checkbox"
          class="h-4 w-4 rounded border-gray-300 text-[#275a19] focus:ring-[#275a19]"
        />
        <span>Maintenance mode — enable</span>
      </label>
      <div class="tenant-float-field mt-2">
        <input id="gs-maint-msg" v-model="form.maintenance_message" type="text" placeholder=" " />
        <label for="gs-maint-msg">Maintenance message</label>
      </div>
    </div>

    <div class="flex flex-wrap gap-2 pt-1">
      <button type="button" class="tenant-btn-submit" @click="saveSettings">Save</button>
    </div>
  </div>
</template>

<script>
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'

export default {
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
        logo: null, // File object for upload (or null)
      },
      logoPreview: null, // URL for previewing logo image
      date_format: [
        "YYYY-MM-DD",
        "DD-MM-YYYY",
        "MM-DD-YYYY",
        "DD/MM/YYYY",
        "MM/DD/YYYY",
        "YYYY/MM/DD",
        "DD MMM YYYY",
        "MMM DD, YYYY"
      ],
      datetime_format: [
        "YYYY-MM-DD HH:mm",
        "YYYY-MM-DD hh:mm A",
        "YYYY/MM/DD HH:mm",
        "YYYY/MM/DD hh:mm A",
        "DD-MM-YYYY HH:mm",
        "DD-MM-YYYY hh:mm A",
        "DD/MM/YYYY HH:mm",
        "DD/MM/YYYY hh:mm A",
        "MM-DD-YYYY HH:mm",
        "MM-DD-YYYY hh:mm A",
        "MM/DD/YYYY HH:mm",
        "MM/DD/YYYY hh:mm A",
        "DD MMM YYYY HH:mm",
        "DD MMM YYYY hh:mm A",
        "MMM DD, YYYY HH:mm",
        "MMM DD, YYYY hh:mm A",
        "MMMM DD, YYYY HH:mm",
        "MMMM DD, YYYY hh:mm A",
        "YYYY-MM-DDTHH:mm",
        "YYYY-MM-DDTHH:mm:ss",
        "YYYY-MM-DD HH:mm:ss",
        "DD MMMM YYYY, HH:mm",
        "DD MMMM YYYY, hh:mm A",
        "dddd, MMMM DD YYYY, HH:mm",
        "dddd, MMMM DD YYYY, hh:mm A"
      ],
    };
  },

  mounted() {
    this.loadSettings();
  },

 methods: {
  // Load general settings
  async loadSettings() {
    try {
      const res = await axiosTenant.get('/settings/general');
      if (res.data) {
        // Merge backend data but do NOT overwrite logo file object
        this.form = {
          ...this.form,
          ...res.data,
          logo: null, // ensure no object/string is assigned
          maintenance_mode: res.data.maintenance_mode == 1,
        };

        // If backend provides a logo URL, use it for preview
        if (res.data.logo && typeof res.data.logo === 'string' && res.data.logo.length > 0) {
          this.logoPreview = res.data.logo;
        } else {
          this.logoPreview = null;
        }
      }
    } catch (err) {
      console.error("Error fetching settings", err);
    }
  },

  // Handle file input for logo upload
  handleLogoUpload(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith("image/")) {
      this.form.logo = file; // Assign File object for upload
      this.logoPreview = URL.createObjectURL(file); // Generate preview URL
    } else {
      Swal.fire({
        icon: 'warning',
        title: 'Invalid file',
        text: 'Please select a valid image file (png, jpg, jpeg, webp).',
      })
      event.target.value = null; // Reset the input
      this.form.logo = null;
      this.logoPreview = null;
    }
  },

  // Optionally allow removing the current logo
  removeLogo() {
    this.form.logo = null;
    this.logoPreview = null;
  },

  // Save general settings
  async saveSettings() {
  try {
    const fd = new FormData();

    // Append all keys except logo
    Object.keys(this.form).forEach(key => {
      if (key === 'logo') return; // Skip logo

      // Convert maintenance_mode boolean to integer (0 or 1)
      if (key === 'maintenance_mode') {
        fd.append(key, this.form[key] ? 1 : 0);
      } else {
        let val = this.form[key];
        // Convert null, "null", undefined, or empty object to empty string
        if (val === 'null' || val === null || val === undefined) val = '';
        if (typeof val === 'object' && Object.keys(val).length === 0) val = '';
        fd.append(key, val);
      }
    });

    // Append logo ONLY if a real File object was selected
    if (this.form.logo instanceof File) {
      fd.append('logo', this.form.logo);
    }

    // POST FormData
    await axiosTenant.post('/settings/general', fd, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    await Swal.fire({
      icon: 'success',
      title: 'Saved',
      text: 'Settings updated successfully!',
    })

    // Reload settings
    this.loadSettings();

  } catch (err) {
    // Show backend error message if available
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
    console.error("Error saving settings", err);
  }
}

}

};
</script>


