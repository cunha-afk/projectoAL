<template>
  <div class="min-h-screen flex flex-col font-sans text-dark">
    <!-- Navbar -->
    <header class="bg-white shadow-md fixed top-0 left-0 w-full z-50">
      <nav class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-3">
          <img src="/images/logo.jpg" alt="Logo" class="h-10 w-auto" />
          <span class="text-xl font-bold text-dark">Marão à Vista</span>
        </div>
        <ul class="hidden md:flex space-x-8 text-dark font-medium">
          <li><a href="/" class="hover:text-accent">Início</a></li>
          <li><a href="/alojamentos" class="hover:text-accent">Alojamentos</a></li>
          <li><a href="/reservas" class="hover:text-accent">Reservas</a></li>
          <li><a href="/contactos" class="hover:text-accent">Contactos</a></li>
        </ul>
        <a
          href="/reservas"
          class="bg-accent text-dark px-5 py-2 rounded-md font-semibold hover:bg-yellow-300 transition"
        >
          Login
        </a>
      </nav>
    </header>

    <!-- Hero Section -->
    <section
      class="relative h-[80vh] flex items-center justify-center text-center text-white mt-20"
    >
      <img
        src="/images/hero.jpg"
        alt="Paisagem de alojamento"
        class="absolute inset-0 w-full h-full object-cover brightness-75"
      />
      <div class="relative z-10">
        <h1 class="text-5xl font-bold mb-4">O Teu Refúgio Perfeito</h1>
        <p class="text-lg mb-6 max-w-2xl mx-auto">
          Desfruta de alojamentos únicos e acolhedores, onde conforto e natureza se encontram.
        </p>
        <a
          href="/reservas"
          class="bg-accent text-dark font-semibold px-8 py-3 rounded-lg shadow hover:bg-yellow-300 transition"
        >
          Reservar Agora
        </a>
      </div>
    </section>

    <!-- Sobre nós -->
    <section class="bg-white py-20 px-6 md:px-20 text-center md:text-left">
      <div class="max-w-5xl mx-auto">
        <h2 class="text-4xl font-bold mb-6 text-dark">Alojamentos com Alma</h2>
        <p class="text-lg leading-relaxed text-dark/70">
          Cada espaço foi desenhado para proporcionar experiências inesquecíveis.  
          Quer procures um retiro romântico, férias em família ou uma pausa da rotina, 
          temos o alojamento ideal para ti.
        </p>
      </div>
    </section>

    <!-- Destaques -->
    <section class="bg-secondary text-dark py-20 px-6 md:px-20">
      <h2 class="text-3xl font-bold text-center mb-10">Os Nossos Destaques</h2>
      <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-10">
        <div
          v-for="(item, index) in alojamentos"
          :key="index"
          class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition"
        >
          <img :src="item.imagem" alt="Alojamento" class="h-56 w-full object-cover" />
          <div class="p-6">
            <h3 class="text-xl font-semibold mb-2">{{ item.titulo }}</h3>
            <p class="text-dark/70 mb-4">{{ item.descricao }}</p>
            <p class="text-lg font-bold text-accent">
              {{ item.preco }}€/noite
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Rodapé -->
    <footer class="bg-dark text-white text-center py-6 text-sm">
      <p>© {{ new Date().getFullYear() }} Alojamento Local — Todos os direitos reservados.</p>
    </footer>
  </div>
</template>

<script setup>
const alojamentos = [
  {
    titulo: "Quarto do Sol",
    descricao: "Vista deslumbrante sobre o vale e piscina privativa.",
    preco: 120,
    imagem: "/images/casa1.jpg",
  },
  {
    titulo: "Quarto Verde",
    descricao: "Ambiente natural e tranquilo, ideal para relaxar.",
    preco: 95,
    imagem: "/images/casa2.jpg",
  },
  {
    titulo: "Quarto Horizonte",
    descricao: "Design moderno e conforto com vistas incríveis.",
    preco: 150,
    imagem: "/images/casa3.jpg",
  },
];
function mostrarMensagem() {
  alert('Funcionalidade de reservas em breve ');
}
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





