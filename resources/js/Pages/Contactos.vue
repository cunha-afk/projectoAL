<template>
  <div class="min-h-screen bg-gray-50 text-dark font-sans flex flex-col">
    <!-- Header -->
     <navbar />
    <header class="flex justify-between items-center p-6 bg-white shadow-sm">
      <div class="flex items-center space-x-2">
        <img src="/images/logo.jpg" alt="Logo" class="h-8" />
        <h1 class="text-lg font-semibold text-dark">Alojamento Local</h1>
      </div>
    </header>

    <!-- Contact Section -->
    <div class="contact-page py-8">
      <h1 class="text-3xl font-bold mb-4 text-center">sContact Us</h1>
      <p class="mb-4 text-center">Se tiver alguma dúvida ou precisar de ajuda, não hesite em entrar em contato conosco!</p>

      <!-- Contact Info (Email and Phone) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 text-center mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
          <img src="/images/icon-email.png" alt="Email" class="h-10 mx-auto mb-4"/>
          <h3 class="text-xl font-semibold mb-2">Email</h3>
          <p class="text-gray-600">contato@aloja.local</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
          <img src="/images/icon-phone.png" alt="Phone" class="h-10 mx-auto mb-4"/>
          <h3 class="text-xl font-semibold mb-2">Telefone</h3>
          <p class="text-gray-600">(11) 98765-4321</p>
        </div>
      </div>

      <!-- Localização (Google Maps Embed) -->
      <div class="flex justify-center mb-8">
        <div class="w-full md:w-1/2">
          <h3 class="text-xl font-semibold mb-4 text-center">Localização</h3>
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3002.676697415246!2d-7.716855171163932!3d41.185218297444564!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd3b4d52ed736209%3A0xb627b1bfb26c6889!2sAlojamento%20Local%20Mar%C3%A3o%20%C3%A0%20Vista!5e0!3m2!1spt-PT!2spt!4v1763466055241!5m2!1spt-PT!2spt" 
            width="100%" 
            height="450" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>

      <!-- FAQ Section -->
      <div class="faq-section py-8">
        <h2 class="text-3xl font-bold mb-4 text-center">FAQ</h2>
        <div v-for="(item, index) in faq" :key="index" class="faq-item mb-4">
          <div class="flex justify-between items-center cursor-pointer" @click="toggleFAQ(index)">
            <span class="text-lg font-medium">{{ item.question }}</span>
            <span class="text-lg font-semibold text-accent">{{ openFAQ[index] ? "−" : "+" }}</span>
          </div>
          <div v-if="openFAQ[index]" class="mt-2 text-gray-600">
            <p>{{ item.answer }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Importa o ref e computed do Vue
import { ref } from "vue";
import Navbar from "../Components/NavBar.vue";  // Não alterado: Importa a navbar

// FAQ
const faq = [
  { question: "Whaling can cause oceanic pollution?", answer: "Yes, it's one of the major issues..." },
  { question: "Whaling can kill marine species?", answer: "Whaling contributes to the decline of marine species..." },
  { question: "Whaling can hurt tourism?", answer: "Yes, areas known for whale watching can suffer..." },
  { question: "Whaling is harmful to the ecosystem?", answer: "Indeed, the entire marine ecosystem can be disrupted..." },
];

const openFAQ = ref(Array(faq.length).fill(false)); // Controlar os FAQ's abertos

const toggleFAQ = (index) => {
  openFAQ.value[index] = !openFAQ.value[index];
};
</script>

<style scoped>
.contact-page {
  padding: 2rem;
}

.faq-item {
  border-bottom: 1px solid #ddd;
  padding-bottom: 1rem;
  cursor: pointer;
}

.faq-item:hover {
  background-color: #f1f1f1;
}

.faq-item .text-accent {
  color: #e6e019;
}

.bg-primary {
  background-color: #9faea0;
}

.bg-secondary {
  background-color: #b9bda5;
}
</style>
