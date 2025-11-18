<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import axios from '@/axiosBackend'

const props = defineProps({
  id: {
    type: [Number, String],
    required: true,
  },
})

const form = ref({
  name: '',
  email: '',
  password: '',
  role: 'cliente',
})

const errors = ref({})
const loading = ref(true)
const saving = ref(false)

const loadUser = async () => {
  loading.value = true
  errors.value = {}

  try {
    const res = await axios.get(`/admin/utilizadores/${props.id}`)
    form.value.name = res.data.name
    form.value.email = res.data.email
    form.value.role = res.data.role || 'cliente'
  } catch (error) {
    console.error('Erro ao carregar utilizador:', error)
  } finally {
    loading.value = false
  }
}

const submit = async () => {
  saving.value = true
  errors.value = {}

  try {
    const payload = {
      name: form.value.name,
      email: form.value.email,
      role: form.value.role,
    }

    if (form.value.password) {
      payload.password = form.value.password
    }

    await axios.put(`/admin/utilizadores/${props.id}`, payload)
    router.visit(route('admin.utilizadores'))
  } catch (error) {
    console.error('Erro ao atualizar utilizador:', error)
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
    }
  } finally {
    saving.value = false
  }
}

onMounted(loadUser)
</script>

<template>
  <AdminLayout title="Editar Utilizador">
    <div v-if="loading">A carregar utilizador...</div>

    <form
      v-else
      @submit.prevent="submit"
      class="max-w-lg mx-auto bg-white p-6 shadow rounded space-y-4"
    >
      <div>
        <label class="block font-semibold">Nome</label>
        <input v-model="form.name" class="w-full border p-2 rounded" required />
        <div v-if="errors.name" class="text-red-600 text-sm mt-1">
          {{ errors.name[0] }}
        </div>
      </div>

      <div>
        <label class="block font-semibold">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full border p-2 rounded"
          required
        />
        <div v-if="errors.email" class="text-red-600 text-sm mt-1">
          {{ errors.email[0] }}
        </div>
      </div>

      <div>
        <label class="block font-semibold">Password (opcional)</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full border p-2 rounded"
        />
        <div v-if="errors.password" class="text-red-600 text-sm mt-1">
          {{ errors.password[0] }}
        </div>
      </div>

      <div>
        <label class="block font-semibold">Role</label>
        <select
          v-model="form.role"
          class="w-full border p-2 rounded"
          required
        >
          <option value="cliente">Cliente</option>
          <option value="admin">Admin</option>
        </select>
        <div v-if="errors.role" class="text-red-600 text-sm mt-1">
          {{ errors.role[0] }}
        </div>
      </div>

      <button
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50"
        :disabled="saving"
      >
        {{ saving ? 'A guardar...' : 'Guardar' }}
      </button>
    </form>
  </AdminLayout>
</template>