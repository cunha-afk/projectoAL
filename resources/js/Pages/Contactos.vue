<template>
  <div>
    <!-- Navbar -->
    <Navbar />
    
    <div class="contact-page">
      <h1 class="text-3xl font-bold mb-4">Contactos</h1>
      <p class="mb-4">Se tiver alguma dúvida ou precisar de ajuda, não hesite em entrar em contato conosco!</p>
      
      <div class="contact-form">
        <form @submit.prevent="submitForm">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
            <input v-model="form.name" type="text" id="name" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
          </div>

          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input v-model="form.email" type="email" id="email" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
          </div>

          <div class="mb-4">
            <label for="message" class="block text-sm font-medium text-gray-700">Mensagem</label>
            <textarea v-model="form.message" id="message" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required></textarea>
          </div>

          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Enviar</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import Navbar from '@/Components/NavBar.vue';  // Importa o componente Navbar

const form = ref({
  name: '',
  email: '',
  message: ''
});

const submitForm = () => {
  // Aqui você pode fazer a lógica para enviar o formulário, por exemplo, via axios para um backend Laravel
  console.log(form.value);
};
</script>

<style scoped>
.contact-page {
  padding: 2rem;
}

.contact-form form {
  max-width: 600px;
  margin: 0 auto;
}
</style>
