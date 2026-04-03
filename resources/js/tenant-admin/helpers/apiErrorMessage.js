/**
 * Build a readable message from axios error responses (Laravel validation, JSON errors, etc.)
 */
export function formatApiErrorMessage(error) {
  if (!error?.response) {
    return error?.message || 'Network error. Check your connection and try again.'
  }

  const d = error.response.data
  if (d == null) {
    return `Request failed (${error.response.status})`
  }
  if (typeof d === 'string') {
    return d
  }

  const primary = d.message || d.error || ''
  const errs = d.errors
  if (errs && typeof errs === 'object' && !Array.isArray(errs)) {
    const lines = []
    for (const [field, messages] of Object.entries(errs)) {
      const arr = Array.isArray(messages) ? messages : [messages]
      for (const m of arr) {
        lines.push(`${field}: ${m}`)
      }
    }
    if (lines.length) {
      return [primary, ...lines].filter(Boolean).join('\n')
    }
  }

  return primary || d.error || `Request failed (${error.response.status})`
}

/**
 * HTML for SweetAlert2 (escapes user/backend text)
 */
export function formatApiErrorHtml(error) {
  const text = formatApiErrorMessage(error)
  const esc = (s) =>
    String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')

  const parts = text.split('\n').filter(Boolean)
  if (parts.length <= 1) {
    return `<p class="text-left text-sm">${esc(parts[0] || text)}</p>`
  }
  return `<div class="text-left text-sm"><ul class="list-disc space-y-1 pl-4">${parts.map((p) => `<li>${esc(p)}</li>`).join('')}</ul></div>`
}
