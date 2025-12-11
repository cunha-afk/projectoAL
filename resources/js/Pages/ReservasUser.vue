<script setup>
import { ref } from "vue";
import axios from "axios";
import Navbar from "@/Components/NavBar.vue";

const props = defineProps({
  id: Number,
  quarto: Object,
});

// Datas escolhidas pelo utilizador
const checkin = ref("");
const checkout = ref("");

// Resposta do backend
const disponibilidade = ref(null);
const erro = ref("");
const loading = ref(false);

// Passo 1 — Verificar disponibilidade
const verificarDisponibilidade = async () => {
  erro.value = "";
  disponibilidade.value = null;

  if (!checkin.value || !checkout.value) {
    erro.value = "Selecione ambas as datas.";
    return;
  }

  loading.value = true;

  try {
    const res = await axios.post(`/api/reservas/available/${props.id}`, {
      data_inicio: checkin.value,
      data_fim: checkout.value,
    });

    disponibilidade.value = res.data;
  } catch (e) {
    erro.value = "Datas indisponíveis.";
  } finally {
    loading.value = false;
  }
};

// Passo 2 — Criar a reserva
const criarReserva = async () => {
  try {
    const res = await axios.post("/api/reservas", {
      alojamento_id: props.id,
      data_inicio: checkin.value,
      data_fim: checkout.value,
    });

    // Ir para o checkout com o ID da reserva
    window.location.href = `/pagamento/${res.data.reserva.id}`;

  } catch (e) {
    erro.value = "Erro ao criar reserva.";
  }
};
</script>

<template>
  <Navbar />

  <div class="max-w-5xl mx-auto mt-28 px-4">
    <h1 class="text-3xl font-bold text-dark mb-4">
      {{ quarto.titulo }}
    </h1>

    <img
      v-if="quarto.foto_principal"
      :src="quarto.foto_principal"
      class="w-full h-80 object-cover rounded mb-6"
    />

    <p class="text-gray-700 mb-6">{{ quarto.descricao }}</p>

    <!-- FORMULÁRIO DATAS -->
    <div class="bg-white shadow p-6 rounded-lg">
      <h2 class="text-xl font-semibold mb-4">Reserva</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- Check-in -->
        <div>
          <label class="font-medium">Check-in</label>
          <input
            type="date"
            v-model="checkin"
            class="w-full border rounded px-3 py-2"
          />
        </div>

        <!-- Check-out -->
        <div>
          <label class="font-medium">Check-out</label>
          <input
            type="date"
            v-model="checkout"
            class="w-full border rounded px-3 py-2"
          />
        </div>

      </div>

      <!-- Botão verificar -->
      <button
        @click="verificarDisponibilidade"
        class="mt-4 bg-primary text-white px-5 py-2 rounded hover:bg-secondary"
      >
        Verificar Disponibilidade
      </button>

      <!-- Erro -->
      <p v-if="erro" class="text-red-600 mt-3">{{ erro }}</p>

      <!-- Resultado -->
      <div v-if="disponibilidade" class="mt-4">
        <p class="text-green-700 font-semibold">
          ✔ O quarto está disponível!
        </p>

        <p class="text-lg font-bold mt-2">
          Total: {{ disponibilidade.preco_total }} €
        </p>

        <button
          @click="criarReserva"
          class="mt-4 bg-accent text-dark px-6 py-2 rounded font-semibold hover:bg-yellow-300"
        >
          Reservar agora
        </button>
      </div>

    </div>
  </div>
</template>

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
</style>
