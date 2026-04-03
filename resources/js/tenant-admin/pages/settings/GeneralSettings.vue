<template>
  <div class="container mt-3">
    <h3>General Settings</h3>
    <div class="card mt-3">
      <div class="card-body">

        <!-- Company Name -->
        <div class="mb-3">
          <label class="form-label fw-bold">Company Name</label>
          <input type="text" class="form-control" v-model="form.company_name">
        </div>

<!-- Logo Upload -->
<div class="mb-3">
  <label class="form-label fw-bold">Company Logo</label>
  <input type="file" class="form-control" @change="handleLogoUpload" >
  <!-- Preview if logo is uploaded or exists -->
  <div v-if="logoPreview" class="mt-2">
    <img :src="logoPreview" alt="Logo" style="max-width: 150px; max-height: 80px;">
  </div>
</div>



        <!-- Default Email -->
        <div class="mb-3">
          <label class="form-label fw-bold">Default Email</label>
          <input type="email" class="form-control" v-model="form.default_email">
        </div>

        <!-- Country -->
        <div class="mb-3">
          <label class="form-label fw-bold">Country</label>
          <input type="text" class="form-control" v-model="form.country" >
        </div>

        <!-- State -->
        <div class="mb-3">
          <label class="form-label fw-bold">State</label>
          <input type="text" class="form-control" v-model="form.state">
        </div>

        <!-- City -->
        <div class="mb-3">
          <label class="form-label fw-bold">City</label>
          <input type="text" class="form-control" v-model="form.city">
        </div>

        <!-- Zip -->
        <div class="mb-3">
          <label class="form-label fw-bold">ZIP / Postal Code</label>
          <input type="text" class="form-control" v-model="form.zip">
        </div>

        <!-- Currency -->
        <div class="mb-3">
          <label class="form-label fw-bold">Currency</label>
          <input type="text" class="form-control" v-model="form.currency" >
        </div>

        <!-- Calling Code -->
        <div class="mb-3">
          <label class="form-label fw-bold">Calling Code</label>
          <input type="text" class="form-control" v-model="form.calling_code" >
        </div>

        <!-- Timezone -->
        <!-- <div class="mb-3">
          <label class="form-label fw-bold">Timezone</label>
          <select class="form-select" v-model="form.timezone">
            <option v-for="tz in timezones" :key="tz">{{ tz }}</option>
          </select>
        </div> -->

        <!-- Date Format -->
        <div class="mb-3">
          <label class="form-label fw-bold">Timezone</label>
          <input type="text" class="form-control" v-model="form.timezone">
        </div>
        <!-- <div class="mb-3">
          <label class="form-label fw-bold">Date Format</label>
          <input type="text" class="form-control" v-model="form.date_format">
        </div> -->
        <div class="mb-3">
          <label class="form-label fw-bold">Date Format</label>
          <select class="form-select" v-model="form.date_format">
            <option v-for="tz in date_format" :key="tz">{{ tz }}</option>
          </select>
        </div>

        <!-- DateTime Format -->
        <!-- <div class="mb-3">
          <label class="form-label fw-bold">Datetime Format</label>
          <input type="text" class="form-control" v-model="form.datetime_format">
        </div> -->

         <div class="mb-3">
          <label class="form-label fw-bold">Datetime Format</label>
          <select class="form-select" v-model="form.datetime_format">
            <option v-for="tz in datetime_format" :key="tz">{{ tz }}</option>
          </select>
        </div>

        <!-- Maintenance Mode -->
        <div class="mb-3">
          <label class="form-label fw-bold">Maintenance Mode</label>
          <input type="checkbox" v-model="form.maintenance_mode"> Enable
          <input class="form-control mt-2" placeholder="Message" v-model="form.maintenance_message" />
        </div>

        <button class="btn btn-primary px-4" @click="saveSettings">Save</button>

      </div>
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


