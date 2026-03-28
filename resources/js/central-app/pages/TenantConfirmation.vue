<template>
  <div class="relative flex flex-col items-center justify-center h-screen overflow-hidden px-4 py-12">

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
      <img :src="logoUrl" :alt="`${appName} logo`" class="h-24 w-auto object-contain" />
    </div>

    <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-2xl shadow-indigo-200/60 w-full max-w-[460px] border border-white/50 backdrop-blur-sm relative z-10 transition-transform duration-500 hover:scale-[1.01]">

      <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">Confirm Your Account</h2>
        <p class="text-slate-500 text-sm leading-relaxed">
          Please enter the <span class="font-bold text-slate-700">6-digit confirmation code</span> sent to your email.
        </p>
      </div>

      <form @submit.prevent="confirmAccount" class="space-y-6">
        <div>
          <label for="confirmationCode" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 ml-1">
            Verification Code
          </label>
          <input
            id="confirmationCode"
            type="text"
            v-model="confirmationCode"
            maxlength="6"
            pattern="\d{6}"
            placeholder="0 0 0 0 0 0"
            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-4 text-center text-2xl font-black tracking-[0.5em] text-indigo-600 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required
            autocomplete="off"
          />
        </div>

        <button
          type="submit"
          :disabled="isConfirming"
          class="w-full font-bold py-4 rounded-2xl transition-all duration-300 shadow-lg relative overflow-hidden bg-indigo-600 text-white hover:bg-indigo-700 shadow-indigo-200 hover:shadow-indigo-300 active:scale-[0.98] disabled:bg-slate-100 disabled:text-slate-400 disabled:shadow-none disabled:cursor-not-allowed"
        >
          <span v-if="isConfirming" class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-5 w-5 text-current" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Confirming...
          </span>
          <span v-else>Confirm Account</span>
        </button>
      </form>

      <div class="mt-8 text-center text-sm font-medium text-slate-500">
        Didn't receive the code?
        <button
          @click="resendCode"
          :disabled="isResending"
          class="text-indigo-600 font-bold hover:text-indigo-700 hover:underline transition-all ml-1 disabled:text-slate-400"
        >
          {{ isResending ? 'Resending...' : 'Resend Code' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axiosCentral from '@/api/axiosCentral';
import Swal from 'sweetalert2';
import { APP_NAME, LOGO_URL } from '@central/brand';

export default {
  name: 'TenantConfirmation',
  setup() {
    const route = useRoute();
    const router = useRouter();
    const confirmationCode = ref('');
    const tenantId = ref(null);
    const isConfirming = ref(false);
    const isResending = ref(false);
    const appName = APP_NAME;
    const logoUrl = LOGO_URL;
    onMounted(() => {
      tenantId.value = route.query.tenant_id;
      if (!tenantId.value) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Tenant ID missing. Please register again.',
          confirmButtonColor: '#4f46e5' // Indigo 600
        }).then(() => {
          router.push('/');
        });
      }
    });

    const confirmAccount = async () => {
      isConfirming.value = true;
      try {
        const response = await axiosCentral.post('tenants/confirm', {
          tenant_id: tenantId.value,
          token: confirmationCode.value
        });

        if (response.data.message) {
          await Swal.fire({
            icon: 'success',
            title: 'Confirmed!',
            text: response.data.message,
            confirmButtonColor: '#4f46e5'
          });

          if (response.data.tenant_domain) {
            window.location.href = response.data.tenant_domain;
          } else {
            router.push('/dashboard/login');
          }
        }
      } catch (error) {
        console.error('Error confirming account:', error);
        let errorMessage = 'Failed to confirm account. Please try again later.';
        if (error.response?.data?.message) {
          errorMessage = error.response.data.message;
        } else if (error.response?.data?.errors) {
          errorMessage = Object.values(error.response.data.errors).flat().join('<br>');
        }
        await Swal.fire({
          icon: 'error',
          title: 'Error',
          html: errorMessage,
          confirmButtonColor: '#4f46e5'
        });
      } finally {
        isConfirming.value = false;
      }
    };

    const resendCode = async () => {
      isResending.value = true;
      try {
        const response = await axiosCentral.post('tenants/resend-code', {
          tenant_id: tenantId.value
        });

        await Swal.fire({
          icon: 'success',
          title: 'Code Resent',
          text: response.data.message,
          confirmButtonColor: '#4f46e5'
        });
      } catch (error) {
        console.error('Error resending code:', error);
        let errorMessage = 'Failed to resend code. Please try again later.';
        if (error.response?.data?.message) {
          errorMessage = error.response.data.message;
        }
        await Swal.fire({
          icon: 'error',
          title: 'Error',
          html: errorMessage,
          confirmButtonColor: '#ef4444' // Red 500
        });
      } finally {
        isResending.value = false;
      }
    };

    return {
      appName,
      confirmationCode,
      isConfirming,
      isResending,
      confirmAccount,
      resendCode,
      logoUrl
    };
  }
};
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
