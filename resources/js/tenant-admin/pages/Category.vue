<template>
  <div class="max-w-6xl mx-auto pb-20 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
      <div>
        <nav class="flex text-[11px] font-bold text-orange-600 uppercase tracking-widest mb-2">
          <span>Catalog</span>
          <span class="mx-2 text-gray-400">/</span>
          <span class="text-gray-500">Categories</span>
        </nav>
        <h1 class="text-2xl font-extrabold text-[#0F1111]">Add Category</h1>
      </div>
      <div class="flex items-center gap-3">
        <button class="px-6 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded shadow-sm hover:bg-gray-50 transition-all">
          Cancel
        </button>
        <button class="px-6 py-2 text-sm font-bold text-white bg-[#232f3e] rounded shadow-md hover:bg-[#131921] transition-all">
          Save Category
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

      <div class="lg:col-span-8 space-y-6">

        <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 bg-[#f8fafc]">
            <h3 class="text-[12px] font-bold text-gray-700 uppercase tracking-wider">General Details</h3>
          </div>
          <div class="p-6 space-y-6">
            <div>
              <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Category Name</label>
              <input v-model="form.name" type="text" placeholder="Enter category name"
                class="w-full border border-gray-300 rounded px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 outline-none transition-all" />
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Description</label>
              <textarea v-model="form.description" rows="5" placeholder="Describe this category for your customers..."
                class="w-full border border-gray-300 rounded px-4 py-2.5 text-sm focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 outline-none transition-all"></textarea>
            </div>
          </div>
        </div>

        <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 bg-[#f8fafc]">
            <h3 class="text-[12px] font-bold text-gray-700 uppercase tracking-wider">Search Engine Optimization</h3>
          </div>
          <div class="p-6 space-y-5">
            <div>
              <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Friendly URL (Slug)</label>
              <div class="flex rounded-md shadow-sm">
                <span class="inline-flex items-center px-3 rounded-l border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-xs font-medium">
                  shop.de/c/
                </span>
                <input v-model="form.slug" type="text" class="flex-1 border border-gray-300 rounded-r px-4 py-2 text-sm focus:border-orange-500 outline-none" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="lg:col-span-4 space-y-6">

        <div class="bg-white border border-gray-200 rounded shadow-sm p-6">
          <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-4">Availability</h3>
          <div class="space-y-3">
            <select v-model="form.status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-orange-500 outline-none bg-white font-medium">
              <option value="active">Active / Visible</option>
              <option value="draft">Draft / Hidden</option>
            </select>
            <div class="flex items-center gap-2 px-1">
              <input type="checkbox" id="nav" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500" />
              <label for="nav" class="text-[11px] text-gray-600 font-medium">Show in Main Navigation</label>
            </div>
          </div>
        </div>

        <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 bg-[#f8fafc]">
            <h3 class="text-[12px] font-bold text-gray-700 uppercase tracking-wider">Placement</h3>
          </div>
          <div class="p-6 space-y-6">
            <div>
              <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Parent Category</label>
              <select v-model="form.parent" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-orange-500 outline-none bg-white">
                <option :value="null">None (Root Level)</option>
                <option v-for="cat in mockCategories" :key="cat.id" :value="cat.name">
                  {{ cat.name }}
                </option>
              </select>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
              <label class="block text-[10px] font-extrabold text-gray-400 uppercase mb-3 tracking-tighter">Live Hierarchy Preview</label>
              <div class="space-y-2">
                <div class="flex items-center text-xs text-gray-400">
                  <i class="fas fa-folder-open mr-2"></i> Root
                </div>
                <div v-if="form.parent" class="flex items-center text-xs text-gray-600 ml-4">
                  <div class="w-px h-4 bg-gray-300 -mt-4 mr-2"></div>
                  <i class="fas fa-chevron-right text-[10px] mr-2 opacity-50"></i>
                  <span class="font-bold">{{ form.parent }}</span>
                </div>
                <div class="flex items-center text-xs text-orange-600 font-extrabold" :class="form.parent ? 'ml-8' : 'ml-4'">
                  <div class="w-px h-4 bg-gray-300 -mt-4 mr-2"></div>
                  <div class="w-2 h-2 rounded-full bg-orange-500 mr-2 animate-pulse"></div>
                  {{ form.name || 'New Category' }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white border border-gray-200 rounded shadow-sm p-6">
          <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-4">Category Image</h3>
          <div @click="$refs.fileInput.click()" class="group relative aspect-square w-full border-2 border-dashed border-gray-200 rounded flex flex-col items-center justify-center hover:bg-orange-50/30 hover:border-orange-400 cursor-pointer transition-all overflow-hidden">
            <div v-if="previewImage" class="absolute inset-0">
               <img :src="previewImage" class="w-full h-full object-cover" />
               <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                 <span class="text-white text-xs font-bold">Change Image</span>
               </div>
            </div>
            <div v-else class="text-center">
              <i class="fas fa-image text-3xl text-gray-200 mb-2 group-hover:text-orange-300"></i>
              <p class="text-[10px] font-bold text-gray-400 uppercase">Drop image here</p>
            </div>
            <input ref="fileInput" type="file" class="hidden" @change="handleFileUpload" />
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'

const previewImage = ref(null)
const form = reactive({
  name: '',
  description: '',
  slug: '',
  status: 'active',
  parent: null
})

// Mock data for the parent selector
const mockCategories = [
  { id: 1, name: 'Electronics' },
  { id: 2, name: 'Fashion' },
  { id: 3, name: 'Home & Kitchen' },
  { id: 4, name: 'Sports' }
]

const handleFileUpload = (e) => {
  const file = e.target.files[0]
  if (file) previewImage.value = URL.createObjectURL(file)
}
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

input, textarea, select {
  font-family: inherit;
}
</style>
