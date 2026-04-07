<template>
  <div class="mail-settings-form tenant-settings-stack w-full max-w-xl">
    <div>
      <div class="tenant-label-row">
        <span class="tenant-field-label">Mail provider</span>
        <TenantFieldTip label="Provider" text="How your store sends email. SMTP is common for your own mail server." />
      </div>
      <TenantSelectSearch
        v-model="form.provider"
        input-id="mail-provider"
        placeholder="Search provider…"
        :options="providerOptions"
      />
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
      <div>
        <div class="tenant-label-row">
          <span class="tenant-field-label">Encryption</span>
          <TenantFieldTip label="Encryption" text="TLS or SSL for the SMTP connection. Your host usually tells you which to use." />
        </div>
        <TenantSelectSearch
          v-model="form.smtp_encryption"
          input-id="mail-smtp-enc"
          placeholder="Search…"
          :options="smtpEncOptions"
        />
      </div>
    </template>

    <template v-if="form.provider === 'mailgun'">
      <div>
        <div class="tenant-label-row">
          <span class="tenant-field-label">Mailgun region</span>
          <TenantFieldTip label="Region" text="Pick the region where your Mailgun account lives (US or EU)." />
        </div>
        <TenantSelectSearch
          v-model="form.mailgun_region"
          input-id="mail-mg-region"
          placeholder="Search…"
          :options="mailgunRegionOptions"
        />
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

    <div class="flex flex-wrap items-center gap-2 border-t border-gray-100 pt-3">
      <button type="button" class="tenant-btn-secondary" @click="testConfig">Test configuration</button>
    </div>
  </div>
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
      providerOptions: [
        { value: 'php', label: 'PHP Mail (default)' },
        { value: 'smtp', label: 'SMTP' },
        { value: 'mailgun', label: 'Mailgun' },
        { value: 'microsoft', label: 'Microsoft' },
        { value: 'sendgrid', label: 'SendGrid' },
        { value: 'sparkpost', label: 'SparkPost' },
      ],
      smtpEncOptions: [
        { value: '', label: 'None' },
        { value: 'ssl', label: 'SSL' },
        { value: 'tls', label: 'TLS' },
      ],
      mailgunRegionOptions: [
        { value: 'us', label: 'US' },
        { value: 'eu', label: 'EU' },
      ],
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
      },
    }
  },

  mounted() {
    this.load()
    this.settingsStickySave?.setSave(() => this.save())
  },

  beforeUnmount() {
    this.settingsStickySave?.clearSave()
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
  },
}
</script>
