<template>
  <section class="mx-auto max-w-md px-4 py-12">
    <div class="rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
      <h1 class="mb-6 text-2xl font-bold text-slate-900">Anmelden</h1>

      <div v-if="error" class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
        {{ error }}
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label for="email" class="mb-2 block text-sm font-medium text-slate-700">
            E-Mail-Adresse
          </label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
            placeholder="ihre@email.de"
          />
        </div>

        <div>
          <label for="password" class="mb-2 block text-sm font-medium text-slate-700">
            Passwort
          </label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
            placeholder="••••••••"
          />
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center">
            <input type="checkbox" class="mr-2 rounded border-slate-300 text-brand focus:ring-brand" />
            <span class="text-sm text-slate-600">Angemeldet bleiben</span>
          </label>
          <router-link to="#" class="text-sm text-brand hover:text-brand-dark">
            Passwort vergessen?
          </router-link>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full rounded-full bg-brand py-3 text-sm font-semibold text-white hover:bg-brand-dark disabled:opacity-50"
        >
          {{ loading ? 'Wird angemeldet...' : 'Anmelden' }}
        </button>
      </form>

      <div class="mt-6 text-center">
        <p class="text-sm text-slate-600">
          Noch kein Konto?
          <router-link to="/registrieren" class="font-medium text-brand hover:text-brand-dark">
            Jetzt registrieren
          </router-link>
        </p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@theme-prism/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const handleSubmit = async () => {
  error.value = ''
  loading.value = true

  const result = await authStore.login(email.value, password.value)

  if (result.success) {
    router.push('/konto')
  } else {
    error.value = result.error || 'Anmeldung fehlgeschlagen'
  }

  loading.value = false
}
</script>






