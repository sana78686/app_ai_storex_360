import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

/**
 * Default SweetAlert2 styling for the tenant admin app (rounded card, green gradient CTA).
 */
export function setupTenantSwal() {
  Swal.mixin({
    customClass: {
      popup: 'swal-tenant-popup',
      confirmButton: 'swal-tenant-btn-confirm',
      cancelButton: 'swal-tenant-btn-cancel',
      denyButton: 'swal-tenant-btn-cancel',
      closeButton: 'swal-tenant-close',
    },
    buttonsStyling: false,
    color: '#1a1e27',
    background: '#ffffff',
    showCloseButton: true,
  })
}
