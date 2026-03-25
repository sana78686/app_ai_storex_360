<template>
  <div class="relative flex flex-col items-center justify-center h-screen overflow-hidden px-4">

    <div class="absolute inset-0 bg-slate-50 -z-20"></div>

    <div class="absolute inset-0 -z-10">
      <div class="absolute top-[-15%] left-[-10%] w-[600px] h-[600px] bg-indigo-200/40 rounded-full blur-[120px]"></div>

      <div class="absolute bottom-[-10%] right-[-5%] w-[500px] h-[500px] bg-blue-100/50 rounded-full blur-[100px]"></div>

      <div class="absolute top-[30%] left-[-5%] w-[300px] h-[300px] bg-purple-100/30 rounded-full blur-[80px]"></div>
    </div>

    <div
      class="absolute inset-0 -z-10 opacity-[0.06] mix-blend-multiply"
      style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"
    ></div>

    <div class="mb-2 relative z-10 animate-fade-in">
      <img :src="logoUrl" alt="SaleTodayStore Logo" class="h-24 w-auto object-contain" />
    </div>

    <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-2xl shadow-indigo-200/60 w-full max-w-[440px] border border-white/50 backdrop-blur-sm relative z-10 transition-transform duration-500 hover:scale-[1.01]">

      <div class="text-center mb-8">
        <h3 v-if="!selectedPlanId" class="text-3xl font-extrabold text-slate-900 tracking-tight">
          Start your free trial
        </h3>
        <div v-if="selectedPlanId" class="space-y-1">
          <h3 class="text-2xl font-bold text-slate-900">Complete your signup</h3>
          <p class="text-sm font-semibold text-indigo-600 bg-indigo-50/80 inline-block px-4 py-1.5 rounded-full border border-indigo-100">
            Plan: {{ selectedPlanName }}
          </p>
        </div>
        <!-- <p class="mt-3 text-slate-500 text-sm leading-relaxed">Join thousands of businesses scaling with Megflux.</p> -->
      </div>

      <div class="flex flex-col gap-4">
        <a
          href="#"
          @click.prevent="goToSignup(plan)"
          class="flex items-center justify-center gap-3 bg-indigo-600 text-white py-2 px-4 rounded-2xl hover:bg-indigo-700 transition-all duration-300 font-bold text-base shadow-lg shadow-indigo-200 hover:shadow-indigo-300 active:scale-95"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          {{ plan.cta_text || 'Sign Up with Email' }}
        </a>

        <div class="flex items-center my-2">
          <div class="flex-grow border-t border-slate-100"></div>
          <span class="mx-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">or continue with</span>
          <div class="flex-grow border-t border-slate-100"></div>
        </div>

        <div class="grid grid-cols-1 gap-3">
          <button
            @click="signInWithGoogle"
            class="flex items-center justify-center gap-3 bg-white border border-slate-200 text-slate-700 py-3.5 px-4 rounded-2xl hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 font-bold text-sm"
          >
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-5 h-5" alt="Google">
            Google
          </button>

          <a
            href="/auth/facebook"
            class="flex items-center justify-center gap-3 bg-[#1877F2] text-white py-3.5 px-4 rounded-2xl hover:bg-[#166fe5] transition-all duration-200 font-bold text-sm shadow-md"
          >
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
               <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            Facebook
          </a>
        </div>
      </div>

      <div class="mt-8 text-center text-[11px] text-slate-400 leading-relaxed font-medium">
        By clicking continue, you agree to our
        <a href="#" class="text-slate-600 underline decoration-slate-200 hover:decoration-indigo-500 transition-all">Terms of Service</a>
        and
        <a href="#" class="text-slate-600 underline decoration-slate-200 hover:decoration-indigo-500 transition-all">Privacy Policy</a>.
      </div>
    </div>

    <p class="mt-2 text-sm text-slate-500 relative z-10 font-medium">
      Already have an account?
      <router-link to="/login" class="text-indigo-600 font-bold hover:text-indigo-700 hover:underline">Log in</router-link>
    </p>
  </div>
</template>

<style scoped>
/* Smooth entry animation */
.animate-fade-in {
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<script setup>
import axiosCentral from '@/api/axiosCentral'
import { useRoute, useRouter } from 'vue-router'
import { computed } from 'vue'

const route = useRoute()
const router = useRouter()
const logoUrl = '/assets/logo/saletodaystore-logo.png'

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
  try {
    const response = await axiosCentral.get('/auth/social/redirect/google')
    if (response.data.url) {
      window.location.href = response.data.url
    } else {
      alert('Failed to get Google login URL')
    }
  } catch (error) {
    alert('Failed to get Google login URL')
  }
}
</script>

<style scoped>
/* No custom CSS needed as everything is handled by Tailwind */
</style>
