<template>
  <div class="mx-auto max-w-4xl px-4 py-12">
    <h1 class="mb-8 text-3xl font-bold text-slate-900">Häufig gestellte Fragen (FAQ)</h1>

    <div class="space-y-6">
      <div
        v-for="(faq, index) in faqs"
        :key="index"
        class="rounded-lg border border-slate-200 bg-white p-6"
      >
        <button
          @click="toggleFaq(index)"
          class="flex w-full items-center justify-between text-left"
        >
          <h2 class="text-lg font-semibold text-slate-900">{{ faq.question }}</h2>
          <ChevronDown
            :class="[
              'h-5 w-5 text-slate-500 transition-transform',
              openFaqs.includes(index) && 'rotate-180'
            ]"
          />
        </button>
        <Transition name="slide">
          <div v-if="openFaqs.includes(index)" class="mt-4 text-slate-700">
            <div v-html="faq.answer"></div>
          </div>
        </Transition>
      </div>
    </div>

    <div class="mt-12 rounded-lg bg-slate-50 p-6 text-center">
      <p class="text-slate-700 mb-4">
        Ihre Frage wurde nicht beantwortet?
      </p>
      <router-link
        to="/kontakt"
        class="inline-block rounded-full bg-brand px-6 py-2 text-sm font-semibold text-white hover:bg-brand-dark transition-colors"
      >
        Kontaktformular öffnen
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { ChevronDown } from 'lucide-vue-next'

const openFaqs = ref([])

const toggleFaq = (index) => {
  const idx = openFaqs.value.indexOf(index)
  if (idx > -1) {
    openFaqs.value.splice(idx, 1)
  } else {
    openFaqs.value.push(index)
  }
}

const faqs = [
  {
    question: 'Wie kann ich bestellen?',
    answer: 'Sie können Ihre Bestellung ganz einfach über unseren Online-Shop aufgeben. Fügen Sie die gewünschten Artikel zum Warenkorb hinzu und folgen Sie dem Bestellprozess.'
  },
  {
    question: 'Welche Zahlungsarten werden akzeptiert?',
    answer: 'Wir akzeptieren Vorkasse, PayPal, Kreditkarte (Visa, Mastercard), Klarna Sofortüberweisung und Giropay. Alle Zahlungen werden sicher über SSL-verschlüsselte Verbindungen abgewickelt.'
  },
  {
    question: 'Wie hoch sind die Versandkosten?',
    answer: 'Bei Bestellungen ab 50,00 € ist der Versand kostenlos. Bei Bestellungen unter 50,00 € betragen die Versandkosten 4,99 €.'
  },
  {
    question: 'Wie lange dauert die Lieferung?',
    answer: 'Die Lieferzeit beträgt 2-4 Werktage nach Zahlungseingang. Bei Vorkasse beginnt die Lieferzeit nach Zahlungseingang.'
  },
  {
    question: 'Kann ich meine Bestellung stornieren?',
    answer: 'Ja, Sie haben ein 14-tägiges Widerrufsrecht. Bitte kontaktieren Sie uns per E-Mail oder nutzen Sie unser Widerrufsformular.'
  },
  {
    question: 'Wie kann ich eine Reklamation einreichen?',
    answer: 'Bitte kontaktieren Sie uns per E-Mail unter info@einfachshop24.de oder telefonisch unter +49 170 901 4419. Wir werden Ihr Anliegen schnellstmöglich bearbeiten.'
  },
  {
    question: 'Bieten Sie auch Abholung an?',
    answer: 'Ja, Sie können Ihre Bestellung nach vorheriger Absprache in unserem Geschäft in Herzogenrath abholen. Bitte kontaktieren Sie uns vorher.'
  },
  {
    question: 'Wie kann ich den Status meiner Bestellung verfolgen?',
    answer: 'Nach Ihrer Bestellung erhalten Sie eine Bestellbestätigung per E-Mail. Sobald Ihre Bestellung versandt wurde, erhalten Sie eine Versandbestätigung mit Tracking-Informationen.'
  }
]
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
  max-height: 500px;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  max-height: 0;
}
</style>
