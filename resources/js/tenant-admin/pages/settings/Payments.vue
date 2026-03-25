<template>
  <div class="flex flex-col items-start space-y-3">

    <!-- Status -->
    <div v-if="loadingStatus">Checking Stripe status...</div>
    <div v-else>
      <span v-if="isConnected" class="text-green-600 font-semibold">
        ✔ Stripe Connected
      </span>
      <span v-else class="text-red-600 font-semibold">
        ✖ Stripe Not Connected
      </span>
    </div>

    <!-- Button -->
    <button
      v-if="!isConnected"
      class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      @click="connectStripe"
      :disabled="processing"
    >
      {{ processing ? "Redirecting..." : "Connect Stripe" }}
    </button>

    <button
      v-if="isConnected"
      class="px-4 py-2 bg-gray-600 text-white rounded cursor-not-allowed"
      disabled
    >
      Stripe Connected
    </button>

    <!-- Error -->
    <p v-if="errorMessage" class="text-red-600">{{ errorMessage }}</p>

  </div>
</template>

<script>
import axiosTenant from '@/api/axiosTenant'

export default {
  name: "StripeConnectButton",

  data() {
    return {
      isConnected: false,
      loadingStatus: true,
      processing: false,
      errorMessage: "",
    };
  },

  mounted() {
    this.fetchConnectionStatus();
  },

  methods: {
    async fetchConnectionStatus() {
      try {
        // This API should return: { connected: true/false }
        const res = await axiosTenant.get("/stripe/status");
        this.isConnected = res.data.connected;
      } catch (e) {
        console.error("Status error:", e);
        this.errorMessage = "Unable to check Stripe connection status.";
      } finally {
        this.loadingStatus = false;
      }
    },

    async connectStripe() {
      this.processing = true;
      this.errorMessage = "";

      try {
        // Call backend which returns the Stripe OAuth redirect URL
        const res = await axiosTenant.get("/stripe/connect");

        // Backend should return something like: { redirect_url: "https://connect.stripe.com/oauth/..." }
        if (res.data.redirect_url) {
          window.location.href = res.data.redirect_url; // Redirect to Stripe
        } else {
          this.errorMessage = "Invalid redirect response.";
          this.processing = false;
        }
      } catch (e) {
        console.error("Connect error:", e);
        this.errorMessage = "Failed to start Stripe connection.";
        this.processing = false;
      }
    },
  },
};
</script>
