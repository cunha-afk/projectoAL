<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import axios from '@/axios'

const form = ref({
  name: '',
  email: '',
  password: ''
})

const submit = async () => {
  await axios.post('/admin/utilizadores', form.value)
  router.visit(route('admin.utilizadores'))
}
</script>

<template>
  <AdminLayout title="Criar Utilizador">

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
        <label class="block font-semibold">Password</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full border p-2 rounded"
          required
        >
      </div>

      <button
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
      >
        Criar
      </button>

    </form>

  </AdminLayout>
</template>