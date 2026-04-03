<template>
  <div class="tenant-dashboard-page pb-8 font-sans">
    <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="tenant-dashboard-page__breadcrumb">Catalog · Products</p>
        <h1 class="tenant-dashboard-page__title mt-1">Products</h1>
      </div>
      <div class="flex gap-2">
        <button class="px-3 py-1.5 bg-white border rounded-lg text-sm font-medium hover:bg-gray-50 transition">Export</button>
        <button @click="showImportModal = true" class="px-3 py-1.5 bg-white border rounded-lg text-sm font-medium hover:bg-gray-50 transition">Import</button>
        <router-link to="/dashboard/products/create" class="px-4 py-1.5 bg-[#202223] text-white rounded-lg text-sm font-semibold hover:bg-black transition">
          Add product
        </router-link>
      </div>
    </div>

    <div class="tenant-dashboard-panel overflow-hidden">

      <div class="p-3 border-b border-gray-200 flex flex-col md:flex-row gap-3 items-center bg-white">
        <div class="relative flex-1 w-full">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <i class="fas fa-search text-xs"></i>
          </span>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Filter products"
            class="w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-black outline-none"
          />
        </div>
        <div class="flex items-center gap-2">
          <select v-model="filterStatus" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm outline-none">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
          </select>
        </div>
      </div>

      <transition name="slide-up">
        <div v-if="selectedIds.length > 0" class="fixed bottom-8 left-1/2 -translate-x-1/2 bg-white border border-gray-200 shadow-2xl rounded-xl px-6 py-3 flex items-center gap-6 z-50">
          <span class="text-sm font-bold text-gray-900">{{ selectedIds.length }} selected</span>
          <div class="h-4 w-px bg-gray-300"></div>
          <div class="flex items-center gap-4">
            <button @click="bulkUpdateStatus('active')" class="text-sm font-bold text-gray-600 hover:text-black">Activate</button>
            <button @click="bulkUpdateStatus('archived')" class="text-sm font-bold text-gray-600 hover:text-black">Archive</button>
            <button @click="bulkDelete" class="text-sm font-bold text-red-600 hover:text-red-700">Delete products</button>
          </div>
        </div>
      </transition>

      <div v-if="!loading && filteredProducts.length === 0" class="flex flex-col items-center justify-center py-20 px-4 text-center">
        <h2 class="text-lg font-semibold text-gray-900">No products found</h2>
        <p class="text-gray-500 text-sm mb-6">Try adjusting your filters or add a new product.</p>
      </div>

      <div v-else-if="loading" class="py-20 text-center text-gray-500 text-sm">
        <i class="fas fa-spinner fa-spin mr-2"></i> Loading products...
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-medium">
            <tr>
              <th class="p-4 w-10">
                <input
                  type="checkbox"
                  :checked="isAllSelected"
                  @change="toggleSelectAll"
                  class="rounded border-gray-300 text-black focus:ring-black"
                />
              </th>
              <th class="p-4">Product</th>
              <th class="p-4">Status</th>
              <th class="p-4">Inventory</th>
              <th class="p-4">Category</th>
              <th class="p-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr
              v-for="product in filteredProducts"
              :key="product.id"
              class="group hover:bg-gray-50 transition-colors"
              :class="{'bg-blue-50/50': selectedIds.includes(product.id)}"
            >
              <td class="p-4">
                <input
                  type="checkbox"
                  v-model="selectedIds"
                  :value="product.id"
                  class="rounded border-gray-300 text-black focus:ring-black"
                />
              </td>
              <td class="p-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0">
                <img
                      v-if="product.media && product.media.length"
                       :src="product.media.find(m => m.is_main)?.cdn_url || product.media[0].cdn_url"
                       class="object-cover w-full h-full"
                       alt="Product image"
                     />

                  <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Image</div>
                </div>
                <router-link :to="`/dashboard/products/${product.id}/edit`" class="font-semibold text-gray-900 hover:underline">
                  {{  product.translation?.name  || 'Unnamed Product' }}
                </router-link>
              </td>
              <td class="p-4">
                <span
                  class="px-2 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider border"
                  :class="statusClass(product.status)"
                >
                  {{ product.status }}
                </span>
              </td>
              <td class="p-4">
                <span :class="(product.inventory || 0) <= 0 ? 'text-red-600 font-medium' : 'text-gray-600'">
                  {{ product.inventory ?? product.quantity ?? 0 }} in stock
                </span>
              </td>
              <td class="p-4 text-gray-600">
               {{ getCategoryName(product) }}


              </td>
              <td class="p-4 text-right relative">
  <div class="flex justify-end items-center gap-2">

    <!-- Edit -->
    <router-link
      :to="`/dashboard/products/${product.id}/edit`"
      class="p-1.5 hover:bg-gray-200 rounded-md text-gray-600"
    >
      <i class="fas fa-edit"></i>
    </router-link>

    <!-- Delete -->
    <button
      @click="deleteProduct(product.id)"
      class="p-1.5 hover:bg-red-100 rounded-md text-red-600"
    >
      <i class="fas fa-trash"></i>
    </button>

    <!-- Three Dots -->
    <div class="relative action-menu-wrapper">
      <button
        @click.stop="toggleMenu(product.id)"
        class="p-1.5 hover:bg-gray-200 rounded-md text-gray-600"
      >
        <i class="fas fa-ellipsis-v"></i>
      </button>

      <!-- Dropdown -->
      <div
        v-if="openMenuId === product.id"
        class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-50"
      >
        <button
          @click="bulkUpdateStatus('active'); closeMenu()"
          class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
        >
          Activate
        </button>

        <button
          @click="bulkUpdateStatus('archived'); closeMenu()"
          class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
        >
          Archive
        </button>

        <button
          @click="duplicateProduct(product.id); closeMenu()"
          class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
        >
          Duplicate
        </button>

        <button
          @click="deleteProduct(product.id); closeMenu()"
          class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
        >
          Delete
        </button>
      </div>
    </div>

  </div>
</td>

            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Import Modal -->
    <transition name="modal">
      <div
        v-if="showImportModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="closeImportModal"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40" aria-hidden="true" />

        <!-- Modal Card -->
        <div
          class="relative z-10 w-full max-w-md bg-white rounded-lg shadow-xl border border-gray-200"
          role="dialog"
          aria-modal="true"
          aria-labelledby="import-modal-title"
          @click.stop
        >
          <!-- Header -->
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
            <h2 id="import-modal-title" class="text-sm font-semibold text-gray-900">
              Import products
            </h2>
            <button
              type="button"
              @click="closeImportModal"
              class="p-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
              aria-label="Close"
              :disabled="isUploading || isProcessing"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Body -->
          <div class="px-5 py-4 space-y-4">
            <!-- Upload Area -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
              <p class="text-xs text-gray-700 mb-3">Upload your Excel file (.xlsx or .xls). Import runs in the background.</p>
              <input
                ref="importFileInput"
                type="file"
                accept=".xlsx,.xls"
                class="hidden"
                @change="onImportFileSelected"
                :disabled="isUploading || isProcessing"
              />
              <div
                @click="!isUploading && !isProcessing && $refs.importFileInput?.click()"
                @dragover.prevent="uploadDragOver = true"
                @dragleave.prevent="uploadDragOver = false"
                @drop.prevent="onImportDrop"
                :class="[
                  'flex flex-col items-center justify-center w-full min-h-[100px] border-2 border-dashed rounded-lg cursor-pointer transition-colors text-xs',
                  uploadDragOver
                    ? 'border-blue-500 bg-blue-50 text-blue-700'
                    : 'border-gray-300 hover:border-gray-400 hover:bg-gray-50 text-gray-600',
                  (isUploading || isProcessing) ? 'opacity-50 cursor-not-allowed' : ''
                ]"
              >
                <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                <span
                  :class="[
                    'font-medium',
                    selectedFile && !importError ? 'text-green-600' : ''
                  ]"
                >
                  {{ importFileName || 'Click or drag file here' }}
                </span>
              </div>

              <!-- Upload Progress Bar -->
              <div v-if="isUploading" class="mt-3">
                <div class="flex items-center justify-between mb-1">
                  <span class="text-xs text-gray-700">Uploading file...</span>
                  <span class="text-xs text-gray-600">{{ uploadProgress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div
                    class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                    :style="{ width: uploadProgress + '%' }"
                  ></div>
                </div>
              </div>

              <!-- Processing Progress Bar -->
              <div v-if="isProcessing" class="mt-3">
                <div class="flex items-center justify-between mb-1">
                  <span class="text-xs text-gray-700">Processing import...</span>
                  <span class="text-xs text-gray-600">{{ processingProgress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div
                    class="bg-green-600 h-2 rounded-full transition-all duration-300"
                    :style="{ width: processingProgress + '%' }"
                  ></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">This may take a few moments depending on file size.</p>
              </div>

              <p v-if="importError && !isUploading && !isProcessing" class="mt-2 text-xs text-red-600">{{ importError }}</p>
              <p v-if="importSuccess && !isUploading && !isProcessing" class="mt-2 text-xs text-green-600">{{ importSuccess }}</p>
            </div>
          </div>

          <!-- Footer -->
          <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeImportModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
              :disabled="isUploading || isProcessing"
            >
              Cancel
            </button>
            <button
              type="button"
              @click="handleImport"
              class="px-4 py-2 text-sm font-medium text-white bg-[#202223] rounded-lg hover:bg-black focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
              :disabled="!selectedFile || isUploading || isProcessing"
            >
              {{ isUploading || isProcessing ? 'Processing...' : 'Import' }}
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'
// import axios from 'axios'

// Reactive data
const products = ref([])
const loading = ref(false)
const searchQuery = ref('')
const filterStatus = ref('')
const selectedIds = ref([])
const openMenuId = ref(null)
// Import modal state
const showImportModal = ref(false)
const selectedFile = ref(null)
const importFileName = ref('')
const importFileInput = ref(null)
const uploadDragOver = ref(false)
const isUploading = ref(false)
const isProcessing = ref(false)
const uploadProgress = ref(0)
const processingProgress = ref(0)
const importError = ref('')
const importSuccess = ref('')
let processingInterval = null





const toggleMenu = (id) => {
  openMenuId.value = openMenuId.value === id ? null : id
}

const closeMenu = () => {
  openMenuId.value = null
}

// Close on outside click
const handleClickOutside = (e) => {
  if (!e.target.closest('.action-menu-wrapper')) {
    closeMenu()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Fetch products from API
const fetchProducts = async () => {
  loading.value = true
  try {
    const res = await axiosTenant.get('/products')
    products.value = res.data.products || []
  } catch (e) {
    console.error('Error fetching products:', e)
    products.value = []
  } finally {
    loading.value = false
  }
}

// Computed: Filter products based on search and status
const filteredProducts = computed(() => {
  let filtered = products.value

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(product =>
      product.name?.toLowerCase().includes(query) ||
      product.sku?.toLowerCase().includes(query)
    )
  }

  // Filter by status
  if (filterStatus.value) {
    filtered = filtered.filter(product =>
      product.status?.toLowerCase() === filterStatus.value.toLowerCase()
    )
  }

  return filtered
})

// Computed: Check if all products are selected
const isAllSelected = computed(() => {
  return filteredProducts.value.length > 0 &&
         filteredProducts.value.every(product => selectedIds.value.includes(product.id))
})

// Toggle select all
const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedIds.value = []
  } else {
    selectedIds.value = filteredProducts.value.map(product => product.id)
  }
}

// Status class helper
const statusClass = (status) => {
  const statusLower = status?.toLowerCase() || ''
  if (statusLower === 'active' || statusLower === 'published') {
    return 'bg-green-50 text-green-700 border-green-200'
  } else if (statusLower === 'draft') {
    return 'bg-yellow-50 text-yellow-700 border-yellow-200'
  } else if (statusLower === 'archived') {
    return 'bg-gray-50 text-gray-700 border-gray-200'
  }
  return 'bg-gray-50 text-gray-700 border-gray-200'
}

// Bulk update status
const bulkUpdateStatus = async (status) => {
  if (selectedIds.value.length === 0) return

  try {
    await Promise.all(
      selectedIds.value.map(id =>
         axiosTenant.patch(`/products/${id}/status`, { status })
      )
    )
    await fetchProducts()
    selectedIds.value = []
  } catch (e) {
    console.error('Error updating products:', e)
  }
}

// Bulk delete
const bulkDelete = async () => {
  if (selectedIds.value.length === 0) return

  const bulkConfirm = await Swal.fire({
    icon: 'warning',
    title: 'Delete products?',
    text: `Are you sure you want to delete ${selectedIds.value.length} product(s)?`,
    showCancelButton: true,
    confirmButtonText: 'Yes, delete',
    cancelButtonText: 'Cancel',
  })
  if (!bulkConfirm.isConfirmed) {
    return
  }

  try {
    await Promise.all(
      selectedIds.value.map(id =>
        axiosTenant.delete(`/products/${id}`)
      )
    )
    await fetchProducts()
    selectedIds.value = []
  } catch (e) {
    console.error('Error deleting products:', e)
  }
}

// Delete single product
const deleteProduct = async (id) => {
  const one = await Swal.fire({
    icon: 'warning',
    title: 'Delete product?',
    text: 'Are you sure you want to delete this product?',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete',
    cancelButtonText: 'Cancel',
  })
  if (!one.isConfirmed) {
    return
  }

  try {
    await axiosTenant.delete(`/products/${id}`)
    await fetchProducts()
  } catch (e) {
    console.error('Error deleting product:', e)
  }
}

const getCategoryName = (product) => {
  if (!product.categories || product.categories.length === 0) {
    return 'Uncategorized'
  }

  const category = product.categories[0]

  return (
    category.translation?.name ||
    category.name ||
    'Uncategorized'
  )
}

// Import modal functions
const closeImportModal = () => {
  if (isUploading.value || isProcessing.value) return
  showImportModal.value = false
  resetImportState()
}

const resetImportState = () => {
  selectedFile.value = null
  importFileName.value = ''
  importError.value = ''
  importSuccess.value = ''
  uploadProgress.value = 0
  processingProgress.value = 0
  uploadDragOver.value = false
  if (importFileInput.value) {
    importFileInput.value.value = ''
  }
  if (processingInterval) {
    clearInterval(processingInterval)
    processingInterval = null
  }
}

const onImportFileSelected = (event) => {
  const file = event.target.files?.[0]
  if (file) {
    if (/\.(xlsx|xls)$/i.test(file.name)) {
      selectedFile.value = file
      importFileName.value = file.name
      importError.value = ''
      importSuccess.value = ''
    } else {
      importError.value = 'Please upload an Excel file (.xlsx or .xls only).'
      selectedFile.value = null
      importFileName.value = ''
    }
  }
}

const onImportDrop = (e) => {
  uploadDragOver.value = false
  const file = e.dataTransfer?.files?.[0]
  if (file && /\.(xlsx|xls)$/i.test(file.name)) {
    selectedFile.value = file
    importFileName.value = file.name
    importError.value = ''
    importSuccess.value = ''
  } else {
    importError.value = 'Please upload an Excel file (.xlsx or .xls only).'
    selectedFile.value = null
    importFileName.value = ''
  }
}

const handleImport = () => {
  if (!selectedFile.value || isUploading.value || isProcessing.value) return
  uploadAndImport(selectedFile.value)
}

const uploadAndImport = async (file) => {
  importError.value = ''
  importSuccess.value = ''
  isUploading.value = true
  uploadProgress.value = 0

  if (!file) {
    importError.value = 'Please select a file.'
    isUploading.value = false
    return
  }

  const formData = new FormData()
  formData.append('file', file)

  try {
    // Get token for axios
    // const token = localStorage.getItem('tenant_token')
    const config = {
    //   baseURL: '/api',
    //   headers: {
    //     'Accept': 'application/json',
    //     // ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
    //   },
      onUploadProgress: (progressEvent) => {
        if (progressEvent.total) {
          uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total)
        }
      }
    }

    // Upload file and queue job
    const { data } = await axiosTenant.post('/products/import', formData, config, {
  headers: {
    'Content-Type': 'multipart/form-data'
  }
})

    // File upload complete
    uploadProgress.value = 100
    isUploading.value = false

    // Start processing with job status polling
    isProcessing.value = true
    processingProgress.value = 10

    // Poll job status
    const jobId = data.job_id
    if (jobId) {
      pollJobStatus(jobId)
    } else {
      // Fallback: simulate progress if no job ID
      simulateProgress()
    }

  } catch (err) {
    console.error('Import error:', err)
    isUploading.value = false
    isProcessing.value = false
    uploadProgress.value = 0
    processingProgress.value = 0
    if (processingInterval) {
      clearInterval(processingInterval)
      processingInterval = null
    }
    importError.value = err.response?.data?.message ||
                       err.response?.data?.errors?.file?.[0] ||
                       'Import failed. Please try again.'
  }
}

const pollJobStatus = async (jobId) => {
  const maxAttempts = 60 // Poll for up to 60 seconds
  let attempts = 0
  let currentProgress = 10

  // Gradually increase progress while polling
  const progressInterval = setInterval(() => {
    if (currentProgress < 90) {
      currentProgress += Math.random() * 5
      currentProgress = Math.min(currentProgress, 90)
      processingProgress.value = Math.round(currentProgress)
    }
  }, 1000)

  const checkStatus = async () => {
    try {
      const token = localStorage.getItem('tenant_token')
      const { data } = await axios.get('/products/import/status', {
        baseURL: '/api',
        params: { job_id: jobId },
        headers: {
          'Accept': 'application/json',
          ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
        }
      })

      if (data.status === 'completed') {
        clearInterval(progressInterval)
        if (processingInterval) {
          clearInterval(processingInterval)
          processingInterval = null
        }
        processingProgress.value = 100

        setTimeout(() => {
          isProcessing.value = false
          importSuccess.value = data.message || 'Products imported successfully.'

          // Refresh product list
          fetchProducts()

          // Auto-close modal after 2 seconds on success
          setTimeout(() => {
            if (importSuccess.value) {
              closeImportModal()
            }
          }, 2000)
        }, 500)
      } else if (data.status === 'failed') {
        clearInterval(progressInterval)
        if (processingInterval) {
          clearInterval(processingInterval)
          processingInterval = null
        }
        isProcessing.value = false
        importError.value = data.message || 'Import job failed.'
      } else {
        // Still processing, continue polling
        attempts++
        if (attempts < maxAttempts) {
          setTimeout(checkStatus, 1000)
        } else {
          clearInterval(progressInterval)
          if (processingInterval) {
            clearInterval(processingInterval)
            processingInterval = null
          }
          isProcessing.value = false
          importError.value = 'Import is taking longer than expected. Please check back later.'
        }
      }
    } catch (err) {
      console.error('Error checking job status:', err)
      // Continue polling on error
      attempts++
      if (attempts < maxAttempts) {
        setTimeout(checkStatus, 1000)
      } else {
        clearInterval(progressInterval)
        if (processingInterval) {
          clearInterval(processingInterval)
          processingInterval = null
        }
        isProcessing.value = false
        importError.value = 'Unable to check import status. Please refresh the page to see if import completed.'
      }
    }
  }

  // Start polling after a short delay
  setTimeout(checkStatus, 1000)
}

const simulateProgress = () => {
  let progress = 10
  processingInterval = setInterval(() => {
    progress += Math.random() * 10
    if (progress >= 90) {
      progress = 90
    }
    processingProgress.value = Math.min(Math.round(progress), 90)
  }, 500)

  // Complete after 3 seconds
  setTimeout(() => {
    if (processingInterval) {
      clearInterval(processingInterval)
      processingInterval = null
    }
    processingProgress.value = 100

    setTimeout(() => {
      isProcessing.value = false
      importSuccess.value = 'Products imported successfully.'

      // Refresh product list
      fetchProducts()

      // Auto-close modal after 2 seconds on success
      setTimeout(() => {
        if (importSuccess.value) {
          closeImportModal()
        }
      }, 2000)
    }, 500)
  }, 3000)
}

// Cleanup on unmount
onBeforeUnmount(() => {
  if (processingInterval) {
    clearInterval(processingInterval)
  }
})

// Fetch products on mount
onMounted(() => {
  fetchProducts()
})
</script>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s ease-out; }
.slide-up-enter-from, .slide-up-leave-to { transform: translate(-50%, 20px); opacity: 0; }

.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
  transform: scale(0.95);
  opacity: 0;
}
</style>



