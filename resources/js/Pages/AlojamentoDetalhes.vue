<script setup>
import { ref } from "vue";
import axios from "axios";
import Navbar from "@/Components/NavBar.vue";

const props = defineProps({
  alojamento: Object,
});

const checkin = ref("");
const checkout = ref("");
const disponibilidadeMsg = ref(null);
const erroMsg = ref(null);
const reservaId = ref(null);

const verificarDisponibilidade = async () => {
  disponibilidadeMsg.value = null;
  erroMsg.value = null;

  if (!checkin.value || !checkout.value) {
    erroMsg.value = "Selecione as datas.";
    return;
  }

  try {
    const res = await axios.post(`/api/reservas/available/${props.alojamento.id}`, {
      data_inicio: checkin.value,
      data_fim: checkout.value,
    });

    if (res.data.disponivel) {
      disponibilidadeMsg.value = "Quarto disponível!";
    } else {
      erroMsg.value = "Este quarto não está disponível nestas datas.";
    }
  } catch (err) {
    erroMsg.value = "Erro ao verificar disponibilidade.";
  }
};

const reservar = async () => {
  erroMsg.value = null;

  try {
    const res = await axios.post("/api/reservas", {
      alojamento_id: props.alojamento.id,
      data_inicio: checkin.value,
      data_fim: checkout.value,
    });

    reservaId.value = res.data.id;

    window.location.href = `/perfil/reservas`;
  } catch (err) {
    erroMsg.value = "Erro ao criar reserva.";
  }
};
</script>

<template>
  <Navbar />

  <div class="max-w-5xl mx-auto mt-28 px-4">
    <h1 class="text-4xl font-bold mb-4">{{ alojamento.titulo }}</h1>

    <p class="text-gray-600 mb-6">{{ alojamento.descricao }}</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">

      <!-- Date Picker -->
      <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Escolher Datas</h2>

        <label class="block mb-2 font-medium">Check-in</label>
        <input type="date" v-model="checkin" class="border px-3 py-2 w-full rounded">

        <label class="block mt-4 mb-2 font-medium">Check-out</label>
        <input type="date" v-model="checkout" class="border px-3 py-2 w-full rounded">

        <button
          @click="verificarDisponibilidade"
          class="mt-4 bg-primary text-white px-4 py-2 rounded hover:bg-gray-700"
        >
          Verificar disponibilidade
        </button>

        <p v-if="disponibilidadeMsg" class="mt-2 text-green-600 font-semibold">
          {{ disponibilidadeMsg }}
        </p>

        <p v-if="erroMsg" class="mt-2 text-red-600 font-semibold">
          {{ erroMsg }}
        </p>

        <button
          v-if="disponibilidadeMsg"
          @click="reservar"
          class="mt-4 bg-accent text-dark px-5 py-2 rounded font-semibold"
        >
          Reservar Agora
        </button>
      </div>

    </div>
  </div>
</template>
