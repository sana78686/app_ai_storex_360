/**
 * Focus and scroll to the first matching field from Laravel-style validation errors.
 * @param {import('axios').AxiosError} error
 * @param {Record<string, string>} idByField - Laravel field key -> HTML id
 * @returns {boolean} true if an element was focused
 */
export function focusFirstValidationField(error, idByField) {
  const errs = error?.response?.data?.errors
  if (!errs || typeof errs !== 'object') return false

  for (const field of Object.keys(errs)) {
    const tail = field.includes('.') ? field.split('.').pop() : field
    const id = idByField[field] || idByField[tail]
    if (!id) continue
    const el = document.getElementById(id)
    if (el && typeof el.focus === 'function') {
      el.scrollIntoView({ block: 'center', behavior: 'smooth' })
      el.focus({ preventScroll: true })
      return true
    }
  }
  return false
}
