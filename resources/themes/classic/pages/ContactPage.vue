<template>
  <div class="mx-auto max-w-4xl px-4 py-12">
    <h1 class="mb-8 text-3xl font-bold text-slate-900">Kontaktformular</h1>

    <div class="grid gap-8 lg:grid-cols-2">
      <!-- Contact Form -->
      <div>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div v-if="success" class="rounded-lg bg-green-50 border border-green-200 p-4">
            <p class="text-sm text-green-700">
              Vielen Dank für Ihre Nachricht! Wir werden uns schnellstmöglich bei Ihnen melden.
            </p>
          </div>

          <div v-if="error" class="rounded-lg bg-red-50 border border-red-200 p-4">
            <p class="text-sm text-red-700">{{ error }}</p>
          </div>

          <div>
            <label for="name" class="mb-1 block text-sm font-medium text-slate-700">
              Name <span class="text-red-500">*</span>
            </label>
            <input
              id="name"
              v-model="formData.name"
              type="text"
              required
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
            />
          </div>

          <div>
            <label for="email" class="mb-1 block text-sm font-medium text-slate-700">
              E-Mail-Adresse <span class="text-red-500">*</span>
            </label>
            <input
              id="email"
              v-model="formData.email"
              type="email"
              required
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
            />
          </div>

          <div>
            <label for="phone" class="mb-1 block text-sm font-medium text-slate-700">
              Telefonnummer (optional)
            </label>
            <input
              id="phone"
              v-model="formData.phone"
              type="tel"
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
            />
          </div>

          <div>
            <label for="subject" class="mb-1 block text-sm font-medium text-slate-700">
              Betreff <span class="text-red-500">*</span>
            </label>
            <select
              id="subject"
              v-model="formData.subject"
              required
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
            >
              <option value="">Bitte wählen...</option>
              <option value="allgemein">Allgemeine Anfrage</option>
              <option value="bestellung">Frage zu meiner Bestellung</option>
              <option value="reklamation">Reklamation</option>
              <option value="rueckgabe">Rückgabe/Widerruf</option>
              <option value="produkt">Frage zu einem Produkt</option>
              <option value="sonstiges">Sonstiges</option>
            </select>
          </div>

          <div>
            <label for="message" class="mb-1 block text-sm font-medium text-slate-700">
              Nachricht <span class="text-red-500">*</span>
            </label>
            <textarea
              id="message"
              v-model="formData.message"
              rows="6"
              required
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand resize-none"
            ></textarea>
          </div>

          <div class="flex items-start">
            <input
              id="privacy"
              v-model="formData.acceptPrivacy"
              type="checkbox"
              required
              class="mt-1 rounded border-slate-300 text-brand focus:ring-brand"
            />
            <label for="privacy" class="ml-2 text-sm text-slate-600">
              Ich akzeptiere die 
              <router-link to="/datenschutz" class="text-brand hover:underline">Datenschutzerklärung</router-link>
              <span class="text-red-500">*</span>
            </label>
          </div>

          <button
            type="submit"
            :disabled="submitting"
            class="w-full rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white hover:bg-brand-dark disabled:opacity-50 transition-colors"
          >
            {{ submitting ? 'Wird gesendet...' : 'Nachricht senden' }}
          </button>
        </form>
      </div>

      <!-- Contact Information -->
      <div class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6">
          <h2 class="mb-4 text-lg font-semibold text-slate-900">Kontaktinformationen</h2>
          <div class="space-y-4 text-slate-700">
            <div class="flex items-start gap-3">
              <MapPin class="h-5 w-5 text-brand flex-shrink-0 mt-0.5" />
              <div>
                <p class="font-medium">Adresse</p>
                <p class="text-sm">
                  EinfachShop24<br />
                  Am Boscheler Berg 8d<br />
                  52134 Herzogenrath<br />
                  Deutschland
                </p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <Phone class="h-5 w-5 text-brand flex-shrink-0 mt-0.5" />
              <div>
                <p class="font-medium">Telefon</p>
                <a href="tel:+491709014419" class="text-sm text-brand hover:underline">
                  +49 170 901 4419
                </a>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <Mail class="h-5 w-5 text-brand flex-shrink-0 mt-0.5" />
              <div>
                <p class="font-medium">E-Mail</p>
                <a href="mailto:info@einfachshop24.de" class="text-sm text-brand hover:underline">
                  info@einfachshop24.de
                </a>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <Clock class="h-5 w-5 text-brand flex-shrink-0 mt-0.5" />
              <div>
                <p class="font-medium">Öffnungszeiten</p>
                <p class="text-sm">
                  Montag bis Samstag<br />
                  10:00 – 18:00 Uhr
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-6">
          <h2 class="mb-4 text-lg font-semibold text-slate-900">Weitere Kontaktmöglichkeiten</h2>
          <p class="text-sm text-slate-600 mb-4">
            Sie können uns auch über folgende Kanäle erreichen:
          </p>
          <div class="space-y-2">
            <a
              href="mailto:presse@einfachshop24.de"
              class="block text-sm text-brand hover:underline"
            >
              Presseanfragen: presse@einfachshop24.de
            </a>
            <a
              href="mailto:support@einfachshop24.de"
              class="block text-sm text-brand hover:underline"
            >
              Support: support@einfachshop24.de
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { MapPin, Phone, Mail, Clock } from 'lucide-vue-next'

const formData = ref({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: '',
  acceptPrivacy: false
})

const submitting = ref(false)
const success = ref(false)
const error = ref('')

const handleSubmit = async () => {
  error.value = ''
  submitting.value = true

  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500))

    // In production, send to API:
    // await fetch('/api/contact', {
    //   method: 'POST',
    //   headers: { 'Content-Type': 'application/json' },
    //   body: JSON.stringify(formData.value)
    // })

    console.log('Contact form submitted:', formData.value)
    
    success.value = true
    formData.value = {
      name: '',
      email: '',
      phone: '',
      subject: '',
      message: '',
      acceptPrivacy: false
    }

    setTimeout(() => {
      success.value = false
    }, 5000)
  } catch (err) {
    error.value = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.'
  } finally {
    submitting.value = false
  }
}
</script>
