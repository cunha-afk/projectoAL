<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();
const user = page.props.auth?.user ?? null;

const menuOpen = ref(false);
const mobileMenuOpen = ref(false);

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value;
};

const toggleMobile = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value;
};

const logout = () => {
  router.post('/logout');
};

// Detectar página atual
const currentUrl = page.url;
</script>

<template>
  <header class="bg-white shadow-md fixed top-0 left-0 w-full z-50">
    <nav class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    
      
      <!-- LOGO -->
      <div class="flex items-center space-x-3">
        <img src="/images/logo.jpg" alt="Logo" class="h-10 w-auto" />
        <span class="text-2xl font-bold text-dark">Marão à Vista</span>
      </div>

      <!-- BOTÃO MOBILE -->
      <button
        class="md:hidden text-dark"
        @click="toggleMobile"
      >
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <!-- LINKS DESKTOP -->
      <ul class="hidden md:flex space-x-8 text-dark font-semibold text-lg">
        
        <li>
          <Link
            href="/"
            :class="currentUrl === '/' ? 'text-accent underline' : 'hover:text-accent'"
          >Início</Link>
        </li>

        <li>
          <Link
            href="/alojamentos"
            :class="currentUrl.startsWith('/quartos') ? 'text-accent underline' : 'hover:text-accent'"
          >Quartos</Link>
        </li>


        <li>
          <Link
            href="/contactos"
            :class="currentUrl.startsWith('/contactos') ? 'text-accent underline' : 'hover:text-accent'"
          >Contactos</Link>
        </li>

        <!-- ADMIN -->
        <li v-if="user?.roles?.some(r => r.name === 'admin')">
          <Link
            href="/admin"
            :class="currentUrl.startsWith('/admin') ? 'text-red-600 underline' : 'text-red-600 hover:text-red-800'"
          >
            Admin
          </Link>
        </li>
      </ul>

      <!-- AUTENTICAÇÃO -->
      <div class="relative hidden md:block">
        
        <!-- LOGIN -->
        <Link
          v-if="!user"
          href="/login"
          class="bg-accent text-dark px-6 py-2 rounded-md font-semibold text-lg hover:bg-yellow-300 transition"
        >
          Login
        </Link>

        <!-- MENU DO UTILIZADOR -->
        <div v-else class="relative">
          <button
            @click="toggleMenu"
            class="flex items-center space-x-2 font-semibold text-lg hover:text-accent transition"
          >
            <span>Olá, {{ user.name }}</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- DROPDOWN -->
          <div
            v-if="menuOpen"
            class="absolute right-0 mt-2 w-40 bg-white shadow-md rounded-md py-2 border z-50"
          >
            <Link
              href="/perfil"
              class="block px-4 py-2 hover:bg-gray-100"
            >
              Perfil
            </Link>
            <Link
              href="/perfil/reservas"
              class="block px-4 py-2 hover:bg-gray-100"
            >
            Minhas Reservas
            </Link>

            <button
              class="block text-left w-full px-4 py-2 hover:bg-gray-100 text-red-600"
              @click="logout"
            >
              Logout
            </button>
          </div>
        </div>

      </div>
    </nav>

    <!-- MENU MOBILE -->
    <div
      v-if="mobileMenuOpen"
      class="md:hidden bg-white shadow-md border-t py-4 px-6 space-y-4 text-dark font-semibold text-lg"
    >
      <Link href="/" class="block hover:text-accent">Início</Link>
      <Link href="/alojamentos" class="block hover:text-accent">Alojamentos</Link>
      <Link href="/reservas" class="block hover:text-accent">Reservas</Link>
      <Link href="/contactos" class="block hover:text-accent">Contactos</Link>

      <Link
        v-if="user?.roles?.some(r => r.name === 'admin')"
        href="/admin"
        class="block text-red-600 hover:text-red-800"
      >Admin</Link>

      <hr />

      <Link
        v-if="!user"
        href="/login"
        class="block bg-accent text-dark text-center py-2 rounded-md"
      >Login</Link>

      <div v-else>
        <p class="font-medium mb-2">Olá, {{ user.name }}</p>

        <Link href="/perfil" class="block py-1 hover:text-accent">Perfil</Link>

        <button
          class="mt-2 w-full text-left text-red-600 py-1 hover:text-red-800"
          @click="logout"
        >
          Logout
        </button>
      </div>
    </div>
  </header>
</template>

<style scoped>
.text-dark {
  color: #616160;
}
.bg-accent {
  background-color: #e6e019;
}
</style>
