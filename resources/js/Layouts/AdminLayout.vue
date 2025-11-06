<!-- resources/js/Layouts/AdminLayout.vue -->
<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#616160] text-white flex flex-col">
      <div class="p-4 text-xl font-bold border-b border-[#9faea0]">
        Painel Admin
      </div>
      <nav class="flex-1 p-4 space-y-2">
        <RouterLink class="block px-3 py-2 rounded hover:bg-[#9faea0]" to="/admin">
          Dashboard
        </RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-[#9faea0]" to="/admin/reservas">
          Reservas
        </RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-[#9faea0]" to="/admin/utilizadores">
          Utilizadores
        </RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-[#9faea0]" to="/admin/alojamento">
          Alojamento
        </RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-[#9faea0]" to="/admin/comentarios">
          Comentários
        </RouterLink>
      </nav>
    </aside>

    <!-- Conteúdo principal -->
    <main class="flex-1 p-6 overflow-y-auto">
      <header class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-[#616160]">{{ title }}</h1>
        <button
          @click="logout"
          class="px-3 py-1 bg-[#e6e019] text-[#616160] font-bold rounded hover:bg-[#b9bda5]"
        >
          Logout
        </button>
      </header>

      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  title: {
    type: String,
    default: 'Painel Admin',
  },
})

const logout = () => {
  localStorage.removeItem('token')
  window.location.href = '/'
}
</script>