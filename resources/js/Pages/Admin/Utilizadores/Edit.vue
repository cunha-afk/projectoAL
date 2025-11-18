<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import axios from '@/axios'

const props = defineProps({ id: Number })

const form = ref({
  name: '',
  email: '',
  password: ''
})

const loadUser = async () => {
  const res = await axios.get(`/admin/utilizadores/${props.id}`)
  form.value.name = res.data.name
  form.value.email = res.data.email
}

const submit = async () => {
  await axios.put(`/admin/utilizadores/${props.id}`, form.value)
  router.visit(route('admin.utilizadores'))
}

onMounted(loadUser)
</script>

<template>
  <AdminLayout title="Editar Utilizador">

    <form
      @submit.prevent="submit"
      class="max-w-lg mx-auto bg-white p-6 shadow rounded space-y-4"
    >

      <div>
        <label class="block font-semibold">Nome</label>
        <input v-model="form.name" class="w-full border p-2 rounded" required>
      </div>

      <div>
        <label class="block font-semibold">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full border p-2 rounded"
          required
        >
      </div>

      <div>
        <label class="block font-semibold">Password (opcional)</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full border p-2 rounded"
        >
      </div>

      <button
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Guardar
      </button>

    </form>

  </AdminLayout>
</template>