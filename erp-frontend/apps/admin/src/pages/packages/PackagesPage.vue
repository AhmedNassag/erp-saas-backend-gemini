<script setup lang="ts">
import { ref, onMounted } from "vue"
import { usePackagesStore } from "../../stores/packages"

const store  = usePackagesStore()
const modal  = ref(false)
const editing = ref<any>(null)
const form   = ref({ name: "", slug: "", price: 0, limit_users: 10, features: "{}", is_active: true })

onMounted(() => store.fetchAll())

function openCreate() { editing.value = null; form.value = { name: "", slug: "", price: 0, limit_users: 10, features: "{}", is_active: true }; modal.value = true }
function openEdit(pkg: any) { editing.value = pkg; form.value = { ...pkg }; modal.value = true }

async function save() {
  if (editing.value) await store.update(editing.value.id, form.value)
  else await store.create(form.value)
  modal.value = false
}

async function remove(id: number) {
  if (confirm("Delete this package?")) await store.destroy(id)
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-white">Packages</h1>
        <p class="text-slate-400 text-sm mt-1">Manage subscription plans</p>
      </div>
      <button @click="openCreate" class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm shadow-lg hover:from-blue-600 hover:to-cyan-600 transition-all">
        + New Package
      </button>
    </div>

    <div v-if="store.loading" class="text-center text-slate-500 py-12">Loading...</div>
    <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="pkg in store.packages" :key="pkg.id"
        class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition-colors">
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-white font-bold text-lg">{{ pkg.name }}</h3>
            <p class="text-slate-500 text-xs font-mono">{{ pkg.slug }}</p>
          </div>
          <span :class="['text-xs font-semibold px-2 py-1 rounded-full', pkg.is_active ? 'bg-green-500/15 text-green-400' : 'bg-slate-700 text-slate-400']">
            {{ pkg.is_active ? "Active" : "Inactive" }}
          </span>
        </div>
        <div class="text-3xl font-black text-white mb-1">${{ pkg.price }}<span class="text-slate-500 text-sm font-normal">/mo</span></div>
        <p class="text-slate-400 text-sm mb-4">Up to {{ pkg.limit_users }} users</p>
        <div class="flex gap-2">
          <button @click="openEdit(pkg)" class="flex-1 py-2 bg-white/5 border border-slate-700 rounded-xl text-white text-xs font-semibold hover:bg-white/10 transition-colors">Edit</button>
          <button @click="remove(pkg.id)" class="py-2 px-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs font-semibold hover:bg-red-500/20 transition-colors">Delete</button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-white font-bold text-lg mb-5">{{ editing ? "Edit Package" : "New Package" }}</h3>
        <form @submit.prevent="save" class="space-y-4">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-slate-300 text-xs font-medium mb-1.5">Name</label>
              <input v-model="form.name" required class="w-full px-3 py-2.5 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all" />
            </div>
            <div>
              <label class="block text-slate-300 text-xs font-medium mb-1.5">Slug</label>
              <input v-model="form.slug" required class="w-full px-3 py-2.5 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all" />
            </div>
            <div>
              <label class="block text-slate-300 text-xs font-medium mb-1.5">Price ($/mo)</label>
              <input v-model.number="form.price" type="number" required class="w-full px-3 py-2.5 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all" />
            </div>
            <div>
              <label class="block text-slate-300 text-xs font-medium mb-1.5">Max Users</label>
              <input v-model.number="form.limit_users" type="number" required class="w-full px-3 py-2.5 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all" />
            </div>
          </div>
          <div>
            <label class="block text-slate-300 text-xs font-medium mb-1.5">Features (JSON)</label>
            <textarea v-model="form.features" rows="3" class="w-full px-3 py-2.5 bg-white/4 border border-white/10 rounded-xl text-white text-sm font-mono focus:outline-none focus:border-blue-500 transition-all resize-none"></textarea>
          </div>
          <label class="flex items-center gap-2 cursor-pointer">
            <input v-model="form.is_active" type="checkbox" class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-blue-500" />
            <span class="text-slate-300 text-sm">Active</span>
          </label>
          <div class="flex gap-3 pt-2">
            <button type="button" @click="modal = false" class="flex-1 py-2.5 bg-white/5 border border-slate-700 rounded-xl text-white text-sm font-semibold hover:bg-white/10 transition-colors">Cancel</button>
            <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl text-white text-sm font-bold hover:from-blue-600 hover:to-cyan-600 transition-all">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>