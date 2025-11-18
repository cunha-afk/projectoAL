<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import axios from '@/axios'

const users = ref([])
const pagination = ref({})
const loading = ref(true)

const fetchUsers = async (page = 1) => {
  loading.value = true

  const res = await axios.get(`/admin/utilizadores?page=${page}`)
  users.value = res.data.data
  pagination.value = res.data

  loading.value = false
}

const deleteUser = async (id) => {
  if (!confirm("Eliminar utilizador?")) return

  await axios.delete(`/admin/utilizadores/${id}`)
  fetchUsers()
}

onMounted(fetchUsers)
</script>

<template>
  <AdminLayout title="Gestão de Utilizadores">

    <div class="flex justify-end mb-4">
      <Link
        :href="route('admin.utilizadores.create')"
        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
      >
        Criar Utilizador
      </Link>
    </div>

    <div v-if="loading">A carregar...</div>

    <table v-else class="min-w-full bg-white rounded shadow">
      <thead>
        <tr class="bg-gray-200 text-left">
          <th class="p-3">ID</th>
          <th class="p-3">Nome</th>
          <th class="p-3">Email</th>
          <th class="p-3">Criado em</th>
          <th class="p-3">Ações</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="u in users" :key="u.id" class="border-b">
          <td class="p-3">{{ u.id }}</td>
          <td class="p-3">{{ u.name }}</td>
          <td class="p-3">{{ u.email }}</td>
          <td class="p-3">{{ new Date(u.created_at).toLocaleDateString() }}</td>

          <td class="p-3 space-x-2">
            <Link
              :href="route('admin.utilizadores.edit', u.id)"
              class="bg-yellow-500 text-white px-3 py-1 rounded"
            >
              Editar
            </Link>

            <button
              class="bg-red-600 text-white px-3 py-1 rounded"
              @click="deleteUser(u.id)"
            >
              Eliminar
            </button>
          </td>

        </tr>
      </tbody>
    </table>

  </AdminLayout>
</template>
