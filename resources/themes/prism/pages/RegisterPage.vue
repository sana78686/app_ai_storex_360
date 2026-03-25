<template>
  <section class="mx-auto max-w-md px-4 py-12">
    <div class="rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
      <h1 class="mb-6 text-2xl font-bold text-slate-900">{{ $t('registerPage.title') }}</h1>

      <div v-if="error" class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
        {{ error }}
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
            {{ $t('registerPage.nameLabel') }}
          </label>
          <input
            id="name"
            v-model="name"
            type="text"
            required
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
            :placeholder="$t('registerPage.namePlaceholder')"
          />
        </div>

        <div>
          <label for="email" class="mb-2 block text-sm font-medium text-slate-700">
            {{ $t('registerPage.emailLabel') }}
          </label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
            :placeholder="$t('registerPage.emailPlaceholder')"
          />
        </div>

        <div>
          <label for="password" class="mb-2 block text-sm font-medium text-slate-700">
            {{ $t('registerPage.passwordLabel') }}
          </label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            minlength="6"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
            :placeholder="$t('registerPage.passwordPlaceholder')"
          />
        </div>

        <div>
          <label for="confirmPassword" class="mb-2 block text-sm font-medium text-slate-700">
            {{ $t('registerPage.confirmPasswordLabel') }}
          </label>
          <input
            id="confirmPassword"
            v-model="confirmPassword"
            type="password"
            required
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
            :placeholder="$t('registerPage.confirmPasswordPlaceholder')"
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
            {{ $t('registerPage.acceptTerms') }}
            <router-link to="#" class="text-brand hover:text-brand-dark">
              {{ $t('registerPage.termsLink') }}
            </router-link>
            {{ $t('registerPage.and') }}
            <router-link to="#" class="text-brand hover:text-brand-dark">
              {{ $t('registerPage.privacyLink') }}
            </router-link>
          </label>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full rounded-full bg-brand py-3 text-sm font-semibold text-white hover:bg-brand-dark disabled:opacity-50"
        >
          {{ loading ? $t('registerPage.submitting') : $t('registerPage.submitButton') }}
        </button>
      </form>

      <div class="mt-6 text-center">
        <p class="text-sm text-slate-600">
          {{ $t('registerPage.hasAccount') }}
          <router-link :to="{ path: '/anmelden', query: $route.query }" class="font-medium text-brand hover:text-brand-dark">
            {{ $t('registerPage.loginLink') }}
          </router-link>
        </p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@theme-prism/stores/authStore'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const { t } = useI18n()

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const acceptTerms = ref(false)
const error = ref('')
const loading = ref(false)

const redirectTo = computed(() => {
  const r = route.query.redirect
  return r && typeof r === 'string' && r.startsWith('/') ? r : '/konto'
})

const handleSubmit = async () => {
  error.value = ''

  if (password.value !== confirmPassword.value) {
    error.value = t('registerPage.passwordsMismatch')
    return
  }

  if (password.value.length < 6) {
    error.value = t('registerPage.passwordMinLength')
    return
  }

  loading.value = true

  const result = await authStore.register(email.value, password.value, name.value)

  if (result.success) {
    router.push(redirectTo.value)
  } else {
    error.value = result.error || t('registerPage.registerFailed')
  }

  loading.value = false
}
</script>






