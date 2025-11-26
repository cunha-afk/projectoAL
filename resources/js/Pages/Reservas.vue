<template>
  <div class="min-h-screen bg-gray-50 text-dark font-sans flex flex-col">
    <!-- Navbar -->
    <Navbar /> <!-- Aqui a Navbar foi importada e colocada no topo da página -->

    <!-- Header -->
    <header class="flex justify-between items-center p-6 bg-white shadow-sm">
      <div class="flex items-center space-x-2">
        <img src="/images/logo.jpg" alt="Logo" class="h-8" />
        <h1 class="text-lg font-semibold text-dark">Alojamento Local</h1>
      </div>

      <div class="flex space-x-4 text-sm text-dark/80">
        <span>EUR - €</span>
        <span>PT</span>
      </div>
    </header>

    <!-- Breadcrumb -->
    <div class="max-w-6xl mx-auto w-full py-6 px-6 md:px-0 text-sm">
      <nav class="flex space-x-2 text-gray-500">
        <span class="text-accent font-medium">Datas</span>
        <span>›</span>
        <span>Add-ons</span>
        <span>›</span>
        <span>Contacto</span>
        <span>›</span>
        <span>Pagamento</span>
      </nav>
    </div>

    <!-- Main content -->
    <main
      class="flex flex-col md:flex-row justify-between max-w-6xl mx-auto w-full px-6 md:px-0 space-y-10 md:space-y-0 md:space-x-10"
    >
      <!-- Formulário -->
      <section class="flex-1 space-y-6">
        <h2 class="text-2xl font-semibold mb-4">Datas</h2>

        <!-- Campos -->
        <div
          class="bg-white p-6 rounded-lg shadow-md flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0"
        >
          <div class="flex-1">
            <label class="block text-sm font-medium text-dark mb-1">Check-in</label>
            <input
              type="date"
              v-model="checkin"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-primary outline-none"
            />
          </div>

          <div class="flex-1">
            <label class="block text-sm font-medium text-dark mb-1">Check-out</label>
            <input
              type="date"
              v-model="checkout"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-primary outline-none"
            />
          </div>

          <div class="flex items-center justify-center space-x-2">
            <button
              @click="hospedes > 1 ? hospedes-- : hospedes"
              class="px-3 py-1 bg-secondary rounded-md text-dark font-bold"
            >
              −
            </button>
            <span class="text-dark font-medium">{{ hospedes }} hóspede{{ hospedes > 1 ? "s" : "" }}</span>
            <button
              @click="hospedes++"
              class="px-3 py-1 bg-secondary rounded-md text-dark font-bold"
            >
              +
            </button>
          </div>
        </div>
      </section>

      <!-- Resumo -->
      <aside class="w-full md:w-1/3 bg-white rounded-lg shadow-md p-6 self-start">
        <h3 class="text-lg font-semibold mb-4">Resumo da Reserva</h3>

        <div class="flex items-center mb-4 space-x-3 border-b pb-4">
          <img
            src="/images/casa1.jpg"
            alt="Alojamento"
            class="w-16 h-16 rounded object-cover"
          />
          <div>
            <h4 class="text-sm font-medium text-dark">Casa do Sol</h4>
            <button class="text-xs text-accent hover:underline mt-1">
              Ver detalhes
            </button>
          </div>
        </div>

        <div class="flex justify-between text-sm text-dark/70 mb-2">
          <span>Estadia</span>
          <span>{{ noites }} noites</span>
        </div>
        <div class="flex justify-between text-sm text-dark/70 mb-2">
          <span>Preço/noite</span>
          <span>{{ precoNoite }}€</span>
        </div>
        <hr class="my-3" />
        <div class="flex justify-between font-semibold text-dark">
          <span>Total (EUR)</span>
          <span>{{ total }}€</span>
        </div>
      </aside>
    </main>

    <!-- Botão Continuar -->
    <div class="fixed bottom-0 left-0 w-full bg-white shadow-inner p-4 flex justify-center">
      <button
        class="bg-accent text-dark font-semibold px-8 py-3 rounded-lg hover:bg-yellow-300 transition"
        @click="continuar"
      >
        Continuar
      </button>
    </div>
  </div>
</template>

<script setup>
// Importa o ref e computed do Vue
import { ref, computed, onMounted } from "vue";
import axiosInstance from "../axios"; // Importa a configuração do Axios
import Navbar from "../Components/NavBar.vue"; // Corrigido: Importando a Navbar corretamente com o @

// Variáveis do Vue
const checkin = ref("");
const checkout = ref("");
const hospedes = ref(1);
const precoNoite = 100;

const noites = computed(() => {
  if (!checkin.value || !checkout.value) return 0;
  const d1 = new Date(checkin.value);
  const d2 = new Date(checkout.value);
  const diff = (d2 - d1) / (1000 * 60 * 60 * 24);
  return diff > 0 ? diff : 0;
});

const total = computed(() => noites.value * precoNoite);

const continuar = () => {
  alert("Reserva guardada temporariamente! A seguir adicionamos o passo de contacto.");
};

// Função para buscar os dados da API (exemplo)
const fetchData = async () => {
  try {
    const response = await axiosInstance.get("/home");
    console.log(response.data); // Verifique a resposta da API
  } catch (error) {
    console.error("Erro ao obter dados:", error); // Exibe erros se houver falhas na requisição
  }
};

// Chama a função fetchData quando o componente for montado
onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.text-dark {
  color: #616160;
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
