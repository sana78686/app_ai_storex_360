<template>
  <div class="mx-auto max-w-4xl px-4 py-12">
    <h1 class="mb-8 text-3xl font-bold text-slate-900">Versand & Zahlung</h1>

    <div class="prose prose-slate max-w-none space-y-8">
      <section>
        <h2 class="mb-4 text-xl font-semibold text-slate-900">Versandkosten</h2>
        <div class="bg-slate-50 p-6 rounded-lg">
          <p class="text-slate-700 mb-2">
            <strong>Innerhalb Deutschlands:</strong>
          </p>
          <ul class="list-disc pl-6 text-slate-700 space-y-1">
            <li>Bei Bestellungen ab 50,00 €: <strong>Kostenlos</strong></li>
            <li>Bei Bestellungen unter 50,00 €: <strong>4,99 €</strong></li>
          </ul>
        </div>
      </section>

      <section>
        <h2 class="mb-4 text-xl font-semibold text-slate-900">Lieferzeiten</h2>
        <p class="text-slate-700 mb-2">
          Die Lieferzeit beträgt 2-4 Werktage nach Zahlungseingang. Bei Vorkasse beginnt die Lieferzeit nach Zahlungseingang.
        </p>
        <p class="text-slate-700">
          Sollte ein Artikel nicht verfügbar sein, werden Sie unverzüglich per E-Mail informiert.
        </p>
      </section>

      <section>
        <h2 class="mb-4 text-xl font-semibold text-slate-900">Versandpartner</h2>
        <p class="text-slate-700 mb-6">
          Wir versenden mit folgenden Partnern:
        </p>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="carrier in shippingCarriers"
            :key="carrier.name"
            class="group bg-white p-5 rounded-lg border-2 border-slate-200 hover:border-brand/60 hover:shadow-md transition-all duration-200"
          >
            <div class="flex items-center justify-center mb-3 h-12">
              <img
                v-if="carrier.image"
                :src="carrier.image"
                :alt="carrier.alt"
                class="h-10 w-auto max-w-[120px] object-contain transition-transform duration-200 group-hover:scale-105"
                @error="handleImageError($event, carrier)"
                loading="lazy"
              />
              <p v-else class="font-semibold text-slate-900 text-center">{{ carrier.name }}</p>
            </div>
            <p class="text-sm text-slate-600 text-center">Paketversand innerhalb Deutschlands</p>
          </div>
        </div>
      </section>

      <section>
        <h2 class="mb-4 text-xl font-semibold text-slate-900">Zahlungsarten</h2>
        <div class="grid gap-4 sm:grid-cols-2">
          <div
            v-for="method in paymentMethods"
            :key="method.name"
            class="group bg-white p-5 rounded-lg border-2 border-slate-200 hover:border-brand/60 hover:shadow-md transition-all duration-200"
          >
            <div class="flex items-center gap-4 mb-3">
              <div class="flex-shrink-0 h-12 w-20 flex items-center justify-center bg-slate-50 rounded-lg p-2">
                <img
                  v-if="method.image"
                  :src="method.image"
                  :alt="method.alt"
                  class="h-8 w-auto max-w-[70px] object-contain transition-transform duration-200 group-hover:scale-105"
                  @error="handleImageError($event, method)"
                  loading="lazy"
                />
                <span v-else class="text-xs font-semibold text-slate-600 text-center">{{ method.name }}</span>
              </div>
              <p class="font-semibold text-slate-900 flex-1">{{ method.name }}</p>
            </div>
            <p class="text-sm text-slate-600 leading-relaxed">{{ getPaymentDescription(method.name) }}</p>
          </div>
        </div>
      </section>

      <section>
        <h2 class="mb-4 text-xl font-semibold text-slate-900">Sicherheit</h2>
        <p class="text-slate-700">
          Alle Zahlungsvorgänge werden über gesicherte SSL-Verbindungen abgewickelt. Ihre Zahlungsdaten werden nicht bei uns gespeichert, sondern direkt an den jeweiligen Zahlungsdienstleister übermittelt.
        </p>
      </section>
    </div>
  </div>
</template>

<script setup>
import { shippingCarriers, paymentMethods } from '@theme-classic/config/paymentShipping'

const handleImageError = (event, item) => {
  event.target.style.display = 'none'
}

const getPaymentDescription = (name) => {
  const descriptions = {
    'Vorkasse': 'Sie überweisen den Betrag im Voraus auf unser Bankkonto. Die Ware wird nach Zahlungseingang versendet.',
    'PayPal': 'Bezahlen Sie sicher und schnell mit Ihrem PayPal-Konto oder per Kreditkarte über PayPal.',
    'Visa': 'Sichere Zahlung per Kreditkarte. Ihre Daten werden verschlüsselt übertragen.',
    'Maestro': 'Sichere Zahlung per Maestro-Karte. Ihre Daten werden verschlüsselt übertragen.',
    'Klarna': 'Bezahlen Sie direkt per Online-Banking. Die Zahlung wird sofort bestätigt.',
    'Giropay': 'Bezahlen Sie sicher per Online-Banking mit Ihrer Girocard.',
    'Überweisung': 'Bezahlen Sie per Banküberweisung. Die Ware wird nach Zahlungseingang versendet.'
  }
  return descriptions[name] || 'Sichere Zahlungsmethode.'
}
</script>
