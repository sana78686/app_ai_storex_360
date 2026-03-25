<template>
  <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-xl p-6">
    <h2 class="text-2xl font-bold mb-4">Invoice Settings</h2>

    <form @submit.prevent="saveSettings" class="space-y-6">

      <!-- Prefix -->
      <div>
        <label class="block font-medium mb-1">Invoice Prefix</label>
        <input v-model="form.prefix" class="input" placeholder="INV" />
      </div>

      <!-- Next Number -->
      <div>
        <label class="block font-medium mb-1">Next Invoice Number</label>
        <input type="number" v-model="form.next_number" class="input" />
      </div>

      <!-- Currency -->
      <div>
        <label class="block font-medium mb-1">Default Currency</label>
        <select v-model="form.default_currency" class="input">
          <option value="USD">USD</option>
          <option value="EUR">EUR</option>
          <option value="PKR">PKR</option>
          <option value="GBP">GBP</option>
          <option value="AED">AED</option>
        </select>
      </div>



      <!-- Intro Text -->
      <div>
        <label class="block font-medium mb-1">Intro Text</label>
        <textarea v-model="form.intro_text" rows="2" class="input"></textarea>
      </div>

      <!-- Footer Text -->
      <div>
        <label class="block font-medium mb-1">Footer Text</label>
        <textarea v-model="form.footer_text" rows="2" class="input"></textarea>
      </div>

      <!-- Auto-generate -->
      <div class="flex items-center gap-2">
        <input type="checkbox" v-model="form.auto_generate_on_paid" />
        <label class="font-medium">Auto-generate invoice when order is paid</label>
      </div>

      <!-- Save Button -->
      <div class="flex justify-end">
        <button type="submit" class="btn-primary">Save Settings</button>
      </div>

    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import axiosTenant from "@/api/axiosTenant";

const form = reactive({
  prefix: "",
  next_number: 1,
  default_currency: "USD",
  intro_text: "",
  footer_text: "",
  auto_generate_on_paid: false,

});



// Fetch existing settings
async function loadSettings() {
  const res = await axiosTenant.get("/invoice-settings");
  Object.assign(form, res.data);


}



// Save settings
async function saveSettings() {
  const fd = new FormData();

  Object.keys(form).forEach((key) => {
    if (typeof form[key] === "boolean") {
      fd.append(key, form[key] ? 1 : 0); // ✅ Send boolean properly
    } else {
      fd.append(key, form[key]);
    }
  });

  await axiosTenant.post("/invoice-settings", fd, {
    headers: { "Content-Type": "multipart/form-data" },
  });

  alert("Invoice settings updated!");
}


onMounted(() => loadSettings());
</script>

<style scoped>
.input {
  @apply w-full border px-3 py-2 rounded focus:ring focus:ring-blue-200 focus:border-blue-500;
}
.btn-primary {
  @apply bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700;
}
</style>
