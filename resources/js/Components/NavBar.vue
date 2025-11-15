<template>
  <header class="bg-white shadow-md fixed top-0 left-0 w-full z-50">
    <nav class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
      <div class="flex items-center space-x-3">
        <img src="/images/logo.jpg" alt="Logo" class="h-10 w-auto" />
        <span class="text-xl font-bold text-dark">Marão à Vista</span>
      </div>
      <ul class="hidden md:flex space-x-8 text-dark font-medium">
        <li><inertia-link to="/" class="hover:text-accent">Início</inertia-link></li>
        <li><inertia-link to="/alojamentos" class="hover:text-accent">Alojamentos</inertia-link></li>
        <li><inertia-link to="/reservas" class="hover:text-accent">Reservas</inertia-link></li>
        <li><inertia-link to="/contactos" class="hover:text-accent">Contactos</inertia-link></li>
      </ul>
      <a
        href="/login"
        class="bg-accent text-dark px-5 py-2 rounded-md font-semibold hover:bg-yellow-300 transition"
      >
        Login
      </a>
    </nav>
  </header>
</template>

<script>
import { Link } from '@inertiajs/vue3'; // Importa o Link do Inertia.js

export default {
  name: 'Navbar',
  components: {
    'inertia-link': Link,  // Registra o componente Link do Inertia
  }
};
</script>

<style scoped>
.text-dark {
  color: #616160;
}
.bg-dark {
  background-color: #616160;
}
.bg-primary {
  background-color: #9faea0;
}
.bg-secondary {
  background-color: #b9bda5;
}
.bg-accent {
  background-color: #e6e019;
}
</style>
