import { categoryProducts } from "./products";

// Helper function to get all products
function getAllProducts() {
  return Object.values(categoryProducts).flat();
}

// Helper function to find product by ID
function findProductById(id) {
  return getAllProducts().find((p) => p.id === id);
}

// Get category name from product ID
function getCategoryFromId(id) {
  if (id.startsWith("auto-")) return "Auto & Motorrad";
  if (id.startsWith("garten-")) return "Garten";
  if (id.startsWith("haus-")) return "Haus & Wohnen";
  if (id.startsWith("werk-")) return "Werkstatt & DIY";
  if (id.startsWith("tier-")) return "Tierbedarf";
  if (id.startsWith("sport-")) return "Sport & Freizeit";
  if (id.startsWith("pool-")) return "Pool & Teich";
  if (id.startsWith("foto-")) return "Foto & Studio";
  return "Unbekannt";
}

// Extended product details
export const productDetails = {
  "auto-1": {
    description: "Hochwertige AGM-Autobatterie mit Start-Stopp-Technologie",
    longDescription: "Diese wartungsfreie AGM-Autobatterie ist speziell für Fahrzeuge mit Start-Stopp-System entwickelt. Sie bietet eine hohe Zyklenfestigkeit und ist besonders vibrationsfest. Ideal für moderne Fahrzeuge mit hohem Energiebedarf.",
    images: [
      "https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=800&q=80"
    ],
    category: "Auto & Motorrad",
    brand: "Premium Auto",
    variants: {
      "Kapazität": {
        type: "size",
        options: [
          {
            name: "74Ah",
            value: "74ah",
            price: 89.99,
            inStock: true
          },
          {
            name: "95Ah",
            value: "95ah",
            price: 119.99,
            inStock: true
          },
          {
            name: "110Ah",
            value: "110ah",
            price: 149.99,
            inStock: false
          }
        ]
      }
    },
    specifications: {
      "Spannung": "12V",
      "Kapazität": "74Ah",
      "Technologie": "AGM",
      "Start-Stopp": "Ja",
      "Wartungsfrei": "Ja",
      "Abmessungen": "278 x 175 x 190 mm",
      "Gewicht": "18.5 kg"
    },
    features: [
      "Wartungsfrei und versiegelt",
      "Start-Stopp-System kompatibel",
      "Hohe Zyklenfestigkeit",
      "Vibrationsfest",
      "Schnelle Ladung möglich"
    ],
    rating: 4.5,
    reviewCount: 127,
    stock: 15,
    shippingInfo: "Kostenloser Versand ab 50€. Lieferung in 1-2 Werktagen.",
    warranty: "24 Monate Garantie"
  },
  "auto-2": {
    description: "Hochwertige Motorrad-Handschuhe aus Leder mit Polsterung",
    longDescription: "Diese robusten Motorrad-Handschuhe aus echtem Leder bieten optimalen Schutz und Komfort. Die gepolsterte Innenausstattung schützt Ihre Hände bei Stürzen, während die wasserabweisende Beschichtung Sie bei jedem Wetter trocken hält.",
    images: [
      "https://images.unsplash.com/photo-1558980664-2506fca6bfc2?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1558980664-2506fca6bfc2?auto=format&fit=crop&w=800&q=80"
    ],
    category: "Auto & Motorrad",
    brand: "Ride Pro",
    variants: {
      "Größe": {
        type: "size",
        options: [
          {
            name: "S",
            value: "s",
            price: 45.50,
            inStock: true
          },
          {
            name: "M",
            value: "m",
            price: 45.50,
            inStock: true
          },
          {
            name: "L",
            value: "l",
            price: 45.50,
            inStock: true
          },
          {
            name: "XL",
            value: "xl",
            price: 47.50,
            inStock: false
          }
        ]
      },
      "Farbe": {
        type: "color",
        options: [
          {
            name: "Schwarz",
            value: "schwarz",
            image: "https://images.unsplash.com/photo-1558980664-2506fca6bfc2?auto=format&fit=crop&w=800&q=80",
            price: 45.50,
            inStock: true
          },
          {
            name: "Braun",
            value: "braun",
            image: "https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=800&q=80",
            price: 45.50,
            inStock: true
          },
          {
            name: "Rot",
            value: "rot",
            image: "https://images.unsplash.com/photo-1558442074-3d1982a56c24?auto=format&fit=crop&w=800&q=80",
            price: 48.50,
            inStock: true
          }
        ]
      }
    },
    specifications: {
      "Material": "Echtleder",
      "Polsterung": "Ja",
      "Wasserabweisend": "Ja",
      "CE-zertifiziert": "Ja"
    },
    features: [
      "Robustes Leder",
      "Gepolsterte Innenausstattung",
      "Wasserabweisend",
      "CE-zertifiziert",
      "Verschiedene Größen verfügbar"
    ],
    rating: 4.4,
    reviewCount: 203,
    stock: 18,
    shippingInfo: "Kostenloser Versand. Lieferung in 1-2 Werktagen.",
    warranty: "12 Monate Garantie"
  },
  "auto-3": {
    description: "Hochwertige Autoreifen für Sommer, 4er Set",
    longDescription: "Diese Sommerreifen bieten ausgezeichnete Haftung auf trockenen und nassen Straßen. Das innovative Profil sorgt für optimale Traktion und reduziert den Rollwiderstand für besseren Kraftstoffverbrauch.",
    images: [
      "https://images.unsplash.com/photo-1558442074-3d1982a56c24?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1558442074-3d1982a56c24?auto=format&fit=crop&w=800&q=80"
    ],
    category: "Auto & Motorrad",
    brand: "Tire Pro",
    variants: {
      "Reifengröße": {
        type: "size",
        options: [
          {
            name: "205/55 R16",
            value: "205-55-r16",
            price: 299.99,
            inStock: true
          },
          {
            name: "215/55 R16",
            value: "215-55-r16",
            price: 329.99,
            inStock: true
          },
          {
            name: "225/50 R17",
            value: "225-50-r17",
            price: 379.99,
            inStock: true
          },
          {
            name: "235/45 R18",
            value: "235-45-r18",
            price: 449.99,
            inStock: false
          }
        ]
      }
    },
    specifications: {
      "Typ": "Sommerreifen",
      "Set": "4 Stück",
      "Geschwindigkeitsindex": "V (bis 240 km/h)",
      "Lastindex": "91"
    },
    features: [
      "Optimale Haftung",
      "Geringer Rollwiderstand",
      "Langlebig",
      "4er Set",
      "Verschiedene Größen verfügbar"
    ],
    rating: 4.6,
    reviewCount: 312,
    stock: 12,
    shippingInfo: "Kostenloser Versand. Lieferung in 2-3 Werktagen.",
    warranty: "36 Monate Garantie"
  },
  "rec-1": {
    description: "Professioneller Green Screen Hintergrund für Foto- und Videoaufnahmen",
    longDescription: "Dieser faltbare Green Screen Hintergrund ist ideal für professionelle Foto- und Videoaufnahmen. Die hochwertige, reflektionsarme Oberfläche sorgt für perfekte Ergebnisse bei der Bildbearbeitung. Einfach aufzubauen und zu transportieren.",
    images: [
      "https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?auto=format&fit=crop&w=800&q=80"
    ],
    category: "Foto & Studio",
    brand: "Studio Pro",
    specifications: {
      "Größe": "2x3m",
      "Material": "Polyester",
      "Faltbar": "Ja",
      "Reflektionsarm": "Ja",
      "Gewicht": "1.2 kg",
      "Farbe": "Chroma Green"
    },
    features: [
      "Faltbar und leicht zu transportieren",
      "Reflektionsarme Oberfläche",
      "Wiederverwendbar",
      "Einfacher Aufbau",
      "Professionelle Qualität"
    ],
    rating: 4.7,
    reviewCount: 89,
    stock: 23,
    shippingInfo: "Kostenloser Versand. Lieferung in 1-2 Werktagen.",
    warranty: "12 Monate Garantie"
  },
  "rec-2": {
    description: "Robuster Gartenpavillon mit wasserabweisender Beschichtung",
    longDescription: "Dieser hochwertige Gartenpavillon bietet Schutz vor Sonne und Regen. Die wasserabweisende Beschichtung und die stabilen Seitenwände machen ihn zu einem idealen Begleiter für Gartenpartys und Outdoor-Events.",
    images: [
      "https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?auto=format&fit=crop&w=800&q=80"
    ],
    category: "Garten",
    brand: "Garden Pro",
    variants: {
      "Größe": {
        type: "size",
        options: [
          {
            name: "3x3m",
            value: "3x3",
            price: 199.99,
            inStock: true
          },
          {
            name: "4x4m",
            value: "4x4",
            price: 279.99,
            inStock: true
          },
          {
            name: "5x5m",
            value: "5x5",
            price: 349.99,
            inStock: false
          }
        ]
      },
      "Farbe": {
        type: "color",
        options: [
          {
            name: "Schwarz",
            value: "schwarz",
            image: "https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?auto=format&fit=crop&w=800&q=80",
            price: 199.99,
            inStock: true
          },
          {
            name: "Beige",
            value: "beige",
            image: "https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=800&q=80",
            price: 199.99,
            inStock: true
          },
          {
            name: "Grau",
            value: "grau",
            image: "https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?auto=format&fit=crop&w=800&q=80",
            price: 209.99,
            inStock: true
          }
        ]
      }
    },
    specifications: {
      "Größe": "3x3m",
      "Farbe": "Schwarz",
      "Material": "Polyester mit PU-Beschichtung",
      "Wasserabweisend": "Ja",
      "Seitenwände": "Inklusive",
      "Gewicht": "12.5 kg"
    },
    features: [
      "Wasserabweisende Beschichtung",
      "Stabile Konstruktion",
      "Einfacher Aufbau",
      "UV-beständig",
      "Mit Seitenwänden"
    ],
    rating: 4.3,
    reviewCount: 156,
    stock: 8,
    shippingInfo: "Kostenloser Versand. Lieferung in 2-3 Werktagen.",
    warranty: "24 Monate Garantie"
  },
  "rec-3": {
    description: "Stabile Autorampen für einfache Wartungsarbeiten",
    longDescription: "Diese robusten Autorampen ermöglichen sichere Wartungsarbeiten an Ihrem Fahrzeug. Die hohe Tragfähigkeit von 2 Tonnen pro Rampe macht sie ideal für PKW und leichte Transporter.",
    images: [
      "https://images.unsplash.com/photo-1558442074-3d1982a56c24?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1558442074-3d1982a56c24?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1558442074-3d1982a56c24?auto=format&fit=crop&w=800&q=80"
    ],
    category: "Auto & Motorrad",
    brand: "Auto Tools",
    specifications: {
      "Tragfähigkeit": "2 Tonnen pro Rampe",
      "Material": "Stahl",
      "Set": "2 Stück",
      "Gewicht": "18 kg",
      "Oberfläche": "Rutschfest"
    },
    features: [
      "Hohe Tragfähigkeit",
      "Rutschfeste Oberfläche",
      "Robuste Stahlkonstruktion",
      "Einfache Handhabung",
      "Langlebig"
    ],
    rating: 4.6,
    reviewCount: 203,
    stock: 12,
    shippingInfo: "Kostenloser Versand. Lieferung in 1-2 Werktagen.",
    warranty: "36 Monate Garantie"
  },
  "1": {
    description: "Professioneller Motorrad-Montageständer für sichere Wartungsarbeiten",
    longDescription: "Dieser hochwertige Motorrad-Montageständer ermöglicht sichere und stabile Wartungsarbeiten an Ihrem Motorrad. Die rote Lackierung macht ihn leicht erkennbar und die hohe Tragfähigkeit von 300 kg sorgt für maximale Sicherheit.",
    images: [
      "https://images.unsplash.com/photo-1558980664-2506fca6bfc2?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1558980664-2506fca6bfc2?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1558980664-2506fca6bfc2?auto=format&fit=crop&w=800&q=80"
    ],
    category: "Auto & Motorrad",
    brand: "Motorcycle Pro",
    variants: {
      "Farbe": {
        type: "color",
        options: [
          {
            name: "Rot",
            value: "rot",
            image: "https://images.unsplash.com/photo-1558980664-2506fca6bfc2?auto=format&fit=crop&w=800&q=80",
            price: 89.99,
            inStock: true
          },
          {
            name: "Schwarz",
            value: "schwarz",
            image: "https://images.unsplash.com/photo-1558442074-3d1982a56c24?auto=format&fit=crop&w=800&q=80",
            price: 89.99,
            inStock: true
          },
          {
            name: "Blau",
            value: "blau",
            image: "https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=800&q=80",
            price: 94.99,
            inStock: false
          }
        ]
      },
      "Größe": {
        type: "size",
        options: [
          {
            name: "Standard (300 kg)",
            value: "standard",
            price: 89.99,
            inStock: true
          },
          {
            name: "XL (500 kg)",
            value: "xl",
            price: 129.99,
            inStock: true
          },
          {
            name: "XXL (750 kg)",
            value: "xxl",
            price: 159.99,
            inStock: false
          }
        ]
      }
    },
    specifications: {
      "Tragfähigkeit": "300 kg",
      "Farbe": "Rot",
      "Material": "Stahl",
      "Montage": "Vorne/Hinten",
      "Gewicht": "8.5 kg"
    },
    features: [
      "Hohe Tragfähigkeit",
      "Stabile Konstruktion",
      "Einfache Montage",
      "Robust und langlebig",
      "Für Vorder- und Hinterrad"
    ],
    rating: 4.8,
    reviewCount: 312,
    stock: 25,
    shippingInfo: "Kostenloser Versand. Lieferung in 1-2 Werktagen.",
    warranty: "24 Monate Garantie"
  },
  "2": {
    description: "Hochwertige Umkehrosmoseanlage für reines Trinkwasser",
    longDescription: "Diese 5-stufige Umkehrosmoseanlage filtert bis zu 190 Liter Trinkwasser pro Tag. Sie entfernt bis zu 99% aller Schadstoffe, Schwermetalle und Bakterien aus dem Leitungswasser und sorgt für kristallklares, gesundes Trinkwasser.",
    images: [
      "https://images.unsplash.com/photo-1514996937319-344454492b37?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1514996937319-344454492b37?auto=format&fit=crop&w=800&q=80"
    ],
    category: "Haus & Wohnen",
    brand: "Naturewater",
    variants: {
      "Kapazität": {
        type: "size",
        options: [
          {
            name: "190 L/Tag",
            value: "190l",
            price: 299.99,
            inStock: true
          },
          {
            name: "300 L/Tag",
            value: "300l",
            price: 399.99,
            inStock: true
          },
          {
            name: "500 L/Tag",
            value: "500l",
            price: 549.99,
            inStock: false
          }
        ]
      },
      "Farbe": {
        type: "color",
        options: [
          {
            name: "Weiß",
            value: "weiss",
            image: "https://images.unsplash.com/photo-1514996937319-344454492b37?auto=format&fit=crop&w=800&q=80",
            price: 299.99,
            inStock: true
          },
          {
            name: "Schwarz",
            value: "schwarz",
            image: "https://images.unsplash.com/photo-1586105251261-72a756497a11?auto=format&fit=crop&w=800&q=80",
            price: 309.99,
            inStock: true
          }
        ]
      }
    },
    specifications: {
      "Leistung": "190 L/Tag",
      "Filterstufen": "5",
      "Entfernung": "99% aller Schadstoffe",
      "Anschluss": "Standard Wasserhahn",
      "Abmessungen": "45 x 20 x 40 cm"
    },
    features: [
      "5-stufiges Filtersystem",
      "Hohe Filterleistung",
      "Einfache Installation",
      "Wartungsfreundlich",
      "Energiesparend"
    ],
    rating: 4.6,
    reviewCount: 234,
    stock: 18,
    shippingInfo: "Kostenloser Versand. Lieferung in 1-2 Werktagen.",
    warranty: "24 Monate Garantie"
  },
  "3": {
    description: "Robustes Werkstattregal für professionelle Organisation",
    longDescription: "Dieses 5-stufige Werkstattregal bietet viel Stauraum für Werkzeuge, Materialien und Zubehör. Die stabile Stahlkonstruktion trägt bis zu 500 kg und ist ideal für Werkstatt, Garage oder Hobbyraum.",
    images: [
      "https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=800&q=80"
    ],
    category: "Werkstatt & DIY",
    brand: "Workshop Pro",
    specifications: {
      "Abmessungen": "200x50x180 cm",
      "Ebenen": "5",
      "Tragfähigkeit": "500 kg",
      "Material": "Stahl",
      "Farbe": "Schwarz",
      "Gewicht": "28 kg"
    },
    features: [
      "Hohe Tragfähigkeit",
      "5 Ebenen für optimalen Stauraum",
      "Stabile Stahlkonstruktion",
      "Einfacher Aufbau",
      "Robust und langlebig"
    ],
    rating: 4.7,
    reviewCount: 189,
    stock: 12,
    shippingInfo: "Kostenloser Versand. Lieferung in 2-3 Werktagen.",
    warranty: "36 Monate Garantie"
  },
  "4": {
    description: "Wetterfester Hühnerstall mit großzügigem Auslauf",
    longDescription: "Dieser hochwertige Hühnerstall bietet Platz für 4-6 Hühner und verfügt über einen großzügigen Auslauf. Die wetterfeste Konstruktion schützt Ihre Tiere vor Wind und Wetter, während die Sitzstangen für Komfort sorgen.",
    images: [
      "https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=800&q=80"
    ],
    category: "Tierbedarf",
    brand: "Farm Pro",
    specifications: {
      "Kapazität": "4-6 Hühner",
      "Material": "Holz mit Schutzlasur",
      "Wetterfest": "Ja",
      "Auslauf": "Inklusive",
      "Sitzstangen": "Ja"
    },
    features: [
      "Wetterfeste Konstruktion",
      "Großzügiger Auslauf",
      "Sitzstangen inklusive",
      "Einfache Reinigung",
      "Robust und langlebig"
    ],
    rating: 4.5,
    reviewCount: 145,
    stock: 6,
    shippingInfo: "Kostenloser Versand. Lieferung in 3-5 Werktagen.",
    warranty: "12 Monate Garantie"
  },
  "garten-2": {
    description: "Hochwertiges Gartenstühle Set aus 4 Stühlen, wetterfest und langlebig",
    longDescription: "Dieses praktische Gartenstühle Set besteht aus 4 robusten Kunststoffstühlen in verschiedenen Farben. Die Stühle sind wetterfest, UV-beständig und ideal für Garten, Terrasse oder Balkon. Einfach zu reinigen und stapelbar für platzsparende Aufbewahrung.",
    images: [
      "https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=800&q=80",
      "https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?auto=format&fit=crop&w=800&q=80"
    ],
    category: "Garten",
    brand: "Garden Pro",
    variants: {
      "Farbe": {
        type: "color",
        options: [
          {
            name: "Weiß",
            value: "weiss",
            image: "https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?auto=format&fit=crop&w=800&q=80",
            price: 79.99,
            inStock: true
          },
          {
            name: "Schwarz",
            value: "schwarz",
            image: "https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=800&q=80",
            price: 79.99,
            inStock: true
          },
          {
            name: "Braun",
            value: "braun",
            image: "https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?auto=format&fit=crop&w=800&q=80",
            price: 84.99,
            inStock: true
          },
          {
            name: "Grau",
            value: "grau",
            image: "https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=800&q=80",
            price: 79.99,
            inStock: false
          }
        ]
      },
      "Anzahl": {
        type: "size",
        options: [
          {
            name: "4 Stühle",
            value: "4",
            price: 79.99,
            inStock: true
          },
          {
            name: "6 Stühle",
            value: "6",
            price: 109.99,
            inStock: true
          },
          {
            name: "8 Stühle",
            value: "8",
            price: 139.99,
            inStock: false
          }
        ]
      }
    },
    specifications: {
      "Anzahl": "4 Stühle",
      "Material": "Kunststoff",
      "Wetterfest": "Ja",
      "UV-beständig": "Ja",
      "Stapelbar": "Ja",
      "Gewicht pro Stuhl": "2.5 kg",
      "Max. Belastung": "120 kg"
    },
    features: [
      "Wetterfest und UV-beständig",
      "Einfach zu reinigen",
      "Stapelbar für platzsparende Aufbewahrung",
      "Robust und langlebig",
      "Verschiedene Farben verfügbar"
    ],
    rating: 4.3,
    reviewCount: 89,
    stock: 12,
    shippingInfo: "Kostenloser Versand. Lieferung in 2-3 Werktagen.",
    warranty: "24 Monate Garantie"
  }
};

// Get product detail by ID
export function getProductDetail(id) {
  const product = findProductById(id);
  if (!product) return null;

  const details = productDetails[id] || {
    description: product.title,
    longDescription: product.title,
    images: [product.imageUrl],
    category: getCategoryFromId(id),
    specifications: {},
    features: [],
    stock: product.available ? 10 : 0,
    shippingInfo: "Kostenloser Versand ab 50€. Lieferung in 1-2 Werktagen."
  };

  return {
    ...product,
    ...details
  };
}

// Get related products (same category, excluding current product)
export function getRelatedProducts(productId, limit = 4) {
  const product = findProductById(productId);
  if (!product) return [];

  const category = getCategoryFromId(productId);
  const categorySlug = Object.entries(categoryProducts).find(([_, products]) =>
    products.some((p) => p.id === productId)
  )?.[0];

  if (!categorySlug) return [];

  const related = categoryProducts[categorySlug]
    .filter((p) => p.id !== productId)
    .slice(0, limit);

  return related;
}
