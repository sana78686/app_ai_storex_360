// Payment and Shipping logos configuration
// Place your images in: public/images/payments/ and public/images/shipping/

export const paymentMethods = [
  {
    name: 'Visa',
    image: '/images/payments/visa.png',
    alt: 'Visa'
  },
  {
    name: 'Maestro',
    image: '/images/payments/maestro.png',
    alt: 'Maestro'
  },
  {
    name: 'PayPal',
    image: '/images/payments/paypal.png',
    alt: 'PayPal'
  },
  {
    name: 'Klarna',
    image: '/images/payments/klarna.png',
    alt: 'Klarna'
  },
  {
    name: 'Giropay',
    image: '/images/payments/giropay.png',
    alt: 'Giropay'
  },
  {
    name: 'Überweisung',
    image: '/images/payments/ueberweisung.png',
    alt: 'Banküberweisung'
  },
  {
    name: 'Vorkasse',
    image: '/images/payments/vorkasse.png',
    alt: 'Vorkasse'
  }
]

export const shippingCarriers = [
  {
    name: 'DHL',
    image: '/images/shipping/dhl.png',
    alt: 'DHL'
  },
  {
    name: 'Hermes',
    image: '/images/shipping/hermes.png',
    alt: 'Hermes'
  },
  {
    name: 'DPD',
    image: '/images/shipping/dpd.png',
    alt: 'DPD'
  },
  {
    name: 'GLS',
    image: '/images/shipping/gls.png',
    alt: 'GLS'
  },
  {
    name: 'Deutsche Post',
    image: '/images/shipping/deutsche-post.png',
    alt: 'Deutsche Post'
  }
]

// Helper function to get payment method by name
export const getPaymentMethod = (name) => {
  return paymentMethods.find(m => m.name === name) || { name, image: null, alt: name }
}

// Helper function to get shipping carrier by name
export const getShippingCarrier = (name) => {
  return shippingCarriers.find(c => c.name === name) || { name, image: null, alt: name }
}
