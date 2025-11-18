<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
</script>

<template>
  <AdminLayout title="Comentários">

    <h1 class="text-2xl font-bold mb-4">Gestão de Comentários</h1>
    <p class="text-gray-600">Esta é a página de administração dos comentários.</p>

  </AdminLayout>
</template>

<script>
export default {
  name: "ComentariosAdmin",
};
</script>

<style scoped>
h1 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
}
</style>