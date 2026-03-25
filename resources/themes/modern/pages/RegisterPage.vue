<template>
  <section class="mx-auto max-w-md px-4 py-12">
    <div class="rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
      <h1 class="mb-6 text-2xl font-bold text-slate-900">Konto erstellen</h1>

      <div v-if="error" class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
        {{ error }}
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
            Name
          </label>
          <input
            id="name"
            v-model="name"
            type="text"
            required
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
            placeholder="Max Mustermann"
          />
        </div>

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
            minlength="6"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
            placeholder="Mindestens 6 Zeichen"
          />
        </div>

        <div>
          <label for="confirmPassword" class="mb-2 block text-sm font-medium text-slate-700">
            Passwort bestätigen
          </label>
          <input
            id="confirmPassword"
            v-model="confirmPassword"
            type="password"
            required
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
            placeholder="Passwort wiederholen"
          />
        </div>

        <div class="flex items-center">
          <input
            v-model="acceptTerms"
            type="checkbox"
            required
            class="mr-2 rounded border-slate-300 text-brand focus:ring-brand"
          />
          <label class="text-sm text-slate-600">
            Ich akzeptiere die
            <router-link to="#" class="text-brand hover:text-brand-dark">
              AGB
            </router-link>
            und die
            <router-link to="#" class="text-brand hover:text-brand-dark">
              Datenschutzerklärung
            </router-link>
          </label>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full rounded-full bg-brand py-3 text-sm font-semibold text-white hover:bg-brand-dark disabled:opacity-50"
        >
          {{ loading ? 'Wird erstellt...' : 'Konto erstellen' }}
        </button>
      </form>

      <div class="mt-6 text-center">
        <p class="text-sm text-slate-600">
          Bereits ein Konto?
          <router-link to="/anmelden" class="font-medium text-brand hover:text-brand-dark">
            Jetzt anmelden
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

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const acceptTerms = ref(false)
const error = ref('')
const loading = ref(false)

const handleSubmit = async () => {
  error.value = ''

  if (password.value !== confirmPassword.value) {
    error.value = 'Die Passwörter stimmen nicht überein'
    return
  }

  if (password.value.length < 6) {
    error.value = 'Das Passwort muss mindestens 6 Zeichen lang sein'
    return
  }

  loading.value = true

  const result = await authStore.register(email.value, password.value, name.value)

  if (result.success) {
    router.push('/konto')
  } else {
    error.value = result.error || 'Registrierung fehlgeschlagen'
  }

  loading.value = false
}
</script>






