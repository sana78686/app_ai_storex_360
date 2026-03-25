<template>
  <div class="modal-content p-3" style="max-width: 550px;">

    <h4 class="mb-3">Configure Mail Provider</h4>

    <!-- Provider Selection -->
    <div class="mb-3">
      <label class="form-label fw-bold">Mail Provider</label>
      <select class="form-select" v-model="form.provider">
        <option value="php">PHP Mail (Default)</option>
        <option value="smtp">SMTP</option>
        <option value="mailgun">Mailgun</option>
        <option value="microsoft">Microsoft</option>
        <option value="sendgrid">SendGrid</option>
        <option value="sparkpost">SparkPost</option>
      </select>
    </div>

    <!-- SMTP Fields -->
    <template v-if="form.provider === 'smtp'">
      <div class="mb-3">
        <label class="form-label fw-bold">SMTP Host</label>
        <input type="text" class="form-control" v-model="form.smtp_host">
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold">SMTP Port</label>
        <input type="text" class="form-control" v-model="form.smtp_port">
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold">SMTP Username</label>
        <input type="text" class="form-control" v-model="form.smtp_username">
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold">SMTP Password</label>
        <input type="password" class="form-control" v-model="form.smtp_password">
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold">Encryption</label>
        <select class="form-select" v-model="form.smtp_encryption">
          <option value="">None</option>
          <option value="ssl">SSL</option>
          <option value="tls">TLS</option>
        </select>
      </div>
    </template>

    <!-- Mailgun Fields -->
    <template v-if="form.provider === 'mailgun'">
      <div class="mb-3">
        <label class="form-label fw-bold">Mailgun Region</label>
        <select class="form-select" v-model="form.mailgun_region">
          <option value="us">US</option>
          <option value="eu">EU</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">Sending Domain</label>
        <input type="text" class="form-control" v-model="form.mailgun_domain">
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">Mailgun Private API Key</label>
        <input type="password" class="form-control" v-model="form.mailgun_api_key">
      </div>
    </template>

    <!-- Microsoft Fields -->
    <template v-if="form.provider === 'microsoft'">
      <div class="mb-3">
        <label class="form-label fw-bold">Client ID</label>
        <input type="text" class="form-control" v-model="form.microsoft_client_id">
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold">Tenant ID</label>
        <input type="text" class="form-control" v-model="form.microsoft_tenant_id">
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold">Client Secret</label>
        <input type="password" class="form-control" v-model="form.microsoft_client_secret">
      </div>
    </template>

    <!-- SendGrid -->
    <template v-if="form.provider === 'sendgrid'">
      <div class="mb-3">
        <label class="form-label fw-bold">SendGrid API Key</label>
        <input type="password" class="form-control" v-model="form.sendgrid_api_key">
      </div>
    </template>

    <!-- SparkPost -->
    <template v-if="form.provider === 'sparkpost'">
      <div class="mb-3">
        <label class="form-label fw-bold">SparkPost API Key</label>
        <input type="password" class="form-control" v-model="form.sparkpost_api_key">
      </div>
    </template>

    <!-- Footer Buttons -->
    <div class="d-flex justify-content-between mt-4">
      <button class="btn btn-secondary" @click="testConfig">Test Configuration</button>
      <button class="btn btn-primary" @click="save">Save</button>
    </div>

  </div>
</template>

<script>
import axiosTenant from '@/api/axiosTenant';

export default {
  data() {
    return {
      form: {
        provider: "php",

        // SMTP
        smtp_host: "",
        smtp_port: "",
        smtp_username: "",
        smtp_password: "",
        smtp_encryption: "",

        // Mailgun
        mailgun_domain: "",
        mailgun_api_key: "",
        mailgun_region: "us",

        // Microsoft
        microsoft_client_id: "",
        microsoft_tenant_id: "",
        microsoft_client_secret: "",

        // SendGrid
        sendgrid_api_key: "",

        // SparkPost
        sparkpost_api_key: "",
      }
    };
  },

  mounted() {
    this.load();
  },

  methods: {
    async load() {
      try {
        const res = await axiosTenant.get("/settings/mail");
        if (res.data) {
          this.form = { ...this.form, ...res.data };
        }
      } catch (err) {
        console.error("Failed to load mail settings", err);
      }
    },

    async save() {
      try {
        await axiosTenant.post("/settings/mail", this.form);
        alert("Mail settings saved!");
      } catch (err) {
        console.error("Error saving mail settings", err);
        alert("Failed to save mail settings.");
      }
    },

    async testConfig() {
      try {
        const res = await axiosTenant.post("/settings/mail/test");
        alert(res.data.message);
      } catch (err) {
        alert("Mail Test Failed: " + err.response.data.message);
      }
    }
  }
};
</script>

<style scoped>
.modal-content {
  border-radius: 6px;
  background: white;
}
</style>
