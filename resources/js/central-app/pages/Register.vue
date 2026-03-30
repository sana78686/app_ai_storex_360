<template>
  <div class="central-auth-page">
    <div class="central-auth-slate-bg" aria-hidden="true" />
    <div class="central-auth-blobs" aria-hidden="true" />
    <div class="central-auth-texture" aria-hidden="true" />

    <div class="relative z-10 mb-2 central-auth-fade-in">
      <img :src="logoUrl" :alt="`${appName} logo`" class="h-24 w-auto object-contain" />
    </div>

    <div class="central-auth-card central-auth-card--narrow">
      <div class="central-auth-stripe" aria-hidden="true" />
      <div class="central-auth-card-body">
        <div class="mb-8 text-center">
          <h3 v-if="!selectedPlanId" class="text-3xl font-extrabold tracking-tight text-slate-900">
            Start your free trial
          </h3>
          <div v-if="selectedPlanId" class="space-y-1">
            <h3 class="text-2xl font-bold text-slate-900">Complete your signup</h3>
            <p
              class="inline-block rounded-full border border-[#0064D2]/25 bg-[#0064D2]/[0.06] px-4 py-1.5 text-sm font-semibold text-[#0064D2]"
            >
              Plan: {{ selectedPlanName }}
            </p>
          </div>
        </div>

        <div class="flex flex-col gap-4">
          <a
            href="#"
            class="email-signup-cta email-signup-cta--red group flex items-center justify-center gap-3 rounded-2xl px-5 py-3.5 text-base font-bold transition-all duration-300 active:scale-[0.98]"
            aria-label="Sign up with email"
            @click.prevent="goToSignup(plan)"
          >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5 shrink-0 opacity-90 group-hover:opacity-100">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            {{ plan.cta_text || 'Sign Up with Email' }}
          </a>

          <div class="central-auth-divider">
            <div class="central-auth-divider-line" />
            <span class="central-auth-divider-text">or continue with</span>
            <div class="central-auth-divider-line" />
          </div>

          <div class="flex flex-row items-center justify-center gap-4">
            <button
              type="button"
              aria-label="Continue with Google"
              class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition-all duration-200 hover:border-slate-300 hover:shadow-md active:scale-95"
              @click="signInWithGoogle"
            >
              <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="h-5 w-5" alt="" />
            </button>

            <button
              type="button"
              aria-label="Continue with Facebook"
              class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#1877F2] text-white shadow-sm transition-all duration-200 hover:bg-[#166fe5] hover:shadow-md active:scale-95"
              @click="signInWithFacebook"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
              </svg>
            </button>
          </div>
        </div>

        <div class="mt-8 text-center text-sm font-medium leading-relaxed text-slate-400">
          By clicking continue, you agree to our
          <a href="#" class="text-slate-600 underline decoration-slate-200 transition-colors hover:text-[#0064D2] hover:decoration-[#0064D2]">Terms of Service</a>
          and
          <a href="#" class="text-slate-600 underline decoration-slate-200 transition-colors hover:text-[#0064D2] hover:decoration-[#0064D2]">Privacy Policy</a>.
        </div>
      </div>
    </div>

    <p class="central-auth-footer-muted relative z-10 mt-2">
      Already have an account?
      <router-link to="/login" class="ebay-link ml-1 hover:underline">Log in</router-link>
    </p>
    <p class="central-auth-footer-muted relative z-10 mt-1 text-center">
      Already have a tenant?
      <router-link to="/find-tenant" class="ebay-link ml-1 hover:underline">Find my dashboard</router-link>
    </p>
  </div>
</template>

<script setup>
import axiosCentral from '@/api/axiosCentral'
import { useRoute, useRouter } from 'vue-router'
import { computed } from 'vue'
import { APP_NAME, LOGO_URL } from '@central/brand'

const route = useRoute()
const router = useRouter()
const appName = APP_NAME
const logoUrl = LOGO_URL

const selectedPlanId = route.query.plan_id || null
const selectedPlanName = route.query.plan_name || null

const plan = computed(() => ({
  id: route.query.plan_id,
  name: route.query.plan_name,
  cta_text: route.query.cta_text || 'Sign Up with Email'
}))

function goToSignup(plan) {
  if (!plan) return
  router.push({
    name: 'signup',
    query: { plan_id: plan.id, plan_name: plan.name }
  })
}

async function signInWithGoogle() {
  await signInWithProvider('google')
}

async function signInWithFacebook() {
  await signInWithProvider('facebook')
}

async function signInWithProvider(provider) {
  try {
    localStorage.setItem('social_auth_provider', provider)
    const response = await axiosCentral.get(`/auth/social/redirect/${provider}`)
    if (response.data.url) {
      window.location.href = response.data.url
    } else {
      alert(`Failed to get ${provider} login URL`)
    }
  } catch (error) {
    alert(`Failed to get ${provider} login URL`)
  }
}
</script>
