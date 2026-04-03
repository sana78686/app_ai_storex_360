<template>
  <div class="mail-settings-form max-w-xl tenant-settings-stack">
    <div class="tenant-float-field is-always-floated">
      <select id="mail-provider" v-model="form.provider">
        <option value="php">PHP Mail (default)</option>
        <option value="smtp">SMTP</option>
        <option value="mailgun">Mailgun</option>
        <option value="microsoft">Microsoft</option>
        <option value="sendgrid">SendGrid</option>
        <option value="sparkpost">SparkPost</option>
      </select>
      <label for="mail-provider">Mail provider</label>
    </div>

    <template v-if="form.provider === 'smtp'">
      <div class="tenant-float-field">
        <input id="mail-smtp-host" v-model="form.smtp_host" type="text" placeholder=" " />
        <label for="mail-smtp-host">SMTP host</label>
      </div>
      <div class="tenant-float-field">
        <input id="mail-smtp-port" v-model="form.smtp_port" type="text" placeholder=" " />
        <label for="mail-smtp-port">SMTP port</label>
      </div>
      <div class="tenant-float-field">
        <input id="mail-smtp-user" v-model="form.smtp_username" type="text" placeholder=" " autocomplete="username" />
        <label for="mail-smtp-user">SMTP username</label>
      </div>
      <div class="tenant-float-field">
        <input
          id="mail-smtp-pass"
          v-model="form.smtp_password"
          name="smtp_password"
          type="password"
          placeholder=" "
          autocomplete="off"
        />
        <label for="mail-smtp-pass">SMTP password</label>
      </div>
      <div class="tenant-float-field is-always-floated">
        <select id="mail-smtp-enc" v-model="form.smtp_encryption">
          <option value="">None</option>
          <option value="ssl">SSL</option>
          <option value="tls">TLS</option>
        </select>
        <label for="mail-smtp-enc">Encryption</label>
      </div>
    </template>

    <template v-if="form.provider === 'mailgun'">
      <div class="tenant-float-field is-always-floated">
        <select id="mail-mg-region" v-model="form.mailgun_region">
          <option value="us">US</option>
          <option value="eu">EU</option>
        </select>
        <label for="mail-mg-region">Mailgun region</label>
      </div>
      <div class="tenant-float-field">
        <input id="mail-mg-domain" v-model="form.mailgun_domain" type="text" placeholder=" " />
        <label for="mail-mg-domain">Sending domain</label>
      </div>
      <div class="tenant-float-field">
        <input
          id="mail-mg-key"
          v-model="form.mailgun_api_key"
          name="mailgun_api_key"
          type="password"
          placeholder=" "
          autocomplete="off"
        />
        <label for="mail-mg-key">Mailgun private API key</label>
      </div>
    </template>

    <template v-if="form.provider === 'microsoft'">
      <div class="tenant-float-field">
        <input id="mail-ms-client" v-model="form.microsoft_client_id" type="text" placeholder=" " />
        <label for="mail-ms-client">Client ID</label>
      </div>
      <div class="tenant-float-field">
        <input id="mail-ms-tenant" v-model="form.microsoft_tenant_id" type="text" placeholder=" " />
        <label for="mail-ms-tenant">Tenant ID</label>
      </div>
      <div class="tenant-float-field">
        <input
          id="mail-ms-secret"
          v-model="form.microsoft_client_secret"
          name="microsoft_client_secret"
          type="password"
          placeholder=" "
          autocomplete="off"
        />
        <label for="mail-ms-secret">Client secret</label>
      </div>
    </template>

    <template v-if="form.provider === 'sendgrid'">
      <div class="tenant-float-field">
        <input
          id="mail-sg-key"
          v-model="form.sendgrid_api_key"
          name="sendgrid_api_key"
          type="password"
          placeholder=" "
          autocomplete="off"
        />
        <label for="mail-sg-key">SendGrid API key</label>
      </div>
    </template>

    <template v-if="form.provider === 'sparkpost'">
      <div class="tenant-float-field">
        <input
          id="mail-sp-key"
          v-model="form.sparkpost_api_key"
          name="sparkpost_api_key"
          type="password"
          placeholder=" "
          autocomplete="off"
        />
        <label for="mail-sp-key">SparkPost API key</label>
      </div>
    </template>

    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-gray-100 pt-3">
      <button type="button" class="tenant-btn-secondary" @click="testConfig">Test configuration</button>
      <button type="button" class="tenant-btn-submit" @click="save">Save</button>
    </div>
  </div>
</template>

<script>
import axiosTenant from '@/api/axiosTenant';
import Swal from 'sweetalert2';

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
        await Swal.fire({
          icon: 'success',
          title: 'Saved',
          text: 'Mail settings saved!',
        });
      } catch (err) {
        console.error("Error saving mail settings", err);
        await Swal.fire({
          icon: 'error',
          title: 'Save failed',
          text: 'Failed to save mail settings.',
        });
      }
    },

    async testConfig() {
      try {
        const res = await axiosTenant.post("/settings/mail/test");
        await Swal.fire({
          icon: 'info',
          title: 'Mail test',
          text: res.data.message,
        });
      } catch (err) {
        await Swal.fire({
          icon: 'error',
          title: 'Mail test failed',
          text: err.response?.data?.message || 'Mail test failed.',
        });
      }
    }
  }
};
</script>
