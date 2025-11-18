<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import axios from '@/axiosBackend'

const users = ref([])
const pagination = ref({})
const loading = ref(true)

const fetchUsers = async (page = 1) => {
  loading.value = true

  try {
    const res = await axios.get('/admin/utilizadores-lista', {
      params: { page },
    })

    users.value = res.data.data      // por causa do paginate()
    pagination.value = res.data
  } catch (error) {
    console.error('Erro ao carregar utilizadores:', error)
  } finally {
    loading.value = false
  }
}

const deleteUser = async (id) => {
  if (!confirm('Eliminar utilizador?')) return

  try {
    await axios.delete(`/admin/utilizadores/${id}`)
    await fetchUsers(pagination.value.current_page || 1)
  } catch (error) {
    console.error('Erro ao eliminar utilizador:', error)
  }
}

onMounted(() => fetchUsers())
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
      <th class="p-3 text-center">Papéis</th>
      <th class="p-3 text-center w-100">Ações</th> <!-- largura fixa opcional -->
    </tr>
  </thead>

  <tbody>
    <tr v-for="u in users" :key="u.id" class="border-b">
      <td class="p-3">{{ u.id }}</td>
      <td class="p-3">{{ u.name }}</td>
      <td class="p-3">{{ u.email }}</td>
      <td class="p-3">{{ new Date(u.created_at).toLocaleDateString() }}</td>

      <!-- Papéis centrado -->
      <td class="p-3 text-center">
        {{ u.role }}
      </td>

      <!-- Ações centradas e com espaçamento consistente -->
      <td class="p-3">
        <div class="flex justify-center gap-2">
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
        </div>
      </td>
    </tr>
  </tbody>
</table>

  </AdminLayout>
</template>
