<template>
  <div class="min-h-screen bg-white">
    <!-- Header -->
    <header class="bg-primary text-white py-4">
      <div class="max-w-7xl mx-auto px-6">
        <h1 class="text-3xl font-semibold">{{ alojamento.titulo }}</h1>
      </div>
    </header>

    <!-- Detalhes do Alojamento -->
    <section class="py-8 px-6 md:px-20">
      <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-10">
        <div>
          <img :src="alojamento.imagem" alt="Alojamento" class="w-full h-96 object-cover rounded-lg shadow-md" />
        </div>
        <div class="space-y-4">
          <p class="text-lg">{{ alojamento.descricao }}</p>
          <p class="text-xl font-semibold text-accent">{{ alojamento.preco }}€/noite</p>
          
          <!-- Formulário de Reserva -->
          <div class="mt-8">
            <h2 class="text-2xl font-semibold">Fazer Reserva</h2>
            <form @submit.prevent="reservar">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="dataInicio" class="block text-sm">Data de Início</label>
                  <input type="date" id="dataInicio" v-model="reserva.dataInicio" class="w-full mt-1 p-2 border rounded" required />
                </div>
                <div>
                  <label for="dataFim" class="block text-sm">Data de Fim</label>
                  <input type="date" id="dataFim" v-model="reserva.dataFim" class="w-full mt-1 p-2 border rounded" required />
                </div>
              </div>

              <!-- Verificação de erro no formato das datas -->
              <p v-if="erroDatas" class="text-red-500 text-sm mt-2">A data de fim não pode ser anterior à data de início.</p>

              <button type="submit" :disabled="isLoading" class="mt-4 bg-accent text-dark font-semibold px-6 py-3 rounded-lg shadow hover:bg-yellow-300 transition">
                {{ isLoading ? 'Aguarde...' : 'Reservar Agora' }}
              </button>
            </form>
            <p v-if="erro" class="text-red-500 mt-4">{{ erro }}</p>
            <p v-if="mensagem" class="text-green-600 mt-4">{{ mensagem }}</p>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axiosInstance from '../axios'; // Importa a configuração do Axios
import { useRoute, useRouter } from 'vue-router'; // Para obter parâmetros de rota

// Armazenar dados do alojamento e do formulário de reserva
const alojamento = ref({});
const reserva = ref({
  dataInicio: '',
  dataFim: '',
});

const isLoading = ref(false);
const erro = ref('');
const mensagem = ref('');
const erroDatas = ref(false);

// Obter o ID do alojamento da URL
const route = useRoute();
const router = useRouter();
const alojamentoId = route.params.id; // O parâmetro 'id' que vem da URL

// Função para buscar os dados do alojamento
const fetchAlojamento = async () => {
  try {
    const response = await axiosInstance.get(`/alojamentos/${alojamentoId}`);
    alojamento.value = response.data; // Salva os dados do alojamento
  } catch (error) {
    console.error('Erro ao buscar alojamento', error);
  }
};

// Verifica se as datas são válidas
watch(() => reserva.dataFim, (newDataFim) => {
  if (newDataFim && reserva.dataInicio && new Date(newDataFim) < new Date(reserva.dataInicio)) {
    erroDatas.value = true;
  } else {
    erroDatas.value = false;
  }
});

// Função para fazer a reserva
const reservar = async () => {
  if (erroDatas.value) return;

  isLoading.value = true;
  erro.value = ''; // Resetando erro antes de tentar novamente

  try {
    const response = await axiosInstance.post('/reservas', {
      alojamento_id: alojamentoId,
      data_inicio: reserva.value.dataInicio,
      data_fim: reserva.value.dataFim,
    });
    mensagem.value = 'Reserva feita com sucesso!';
    erro.value = '';
    router.push('/reservas'); // Redireciona para a página de reservas
  } catch (error) {
    console.error('Erro ao fazer reserva', error);
    erro.value = 'Erro ao fazer a reserva. Tente novamente.';
    mensagem.value = '';
  } finally {
    isLoading.value = false;
  }
};

// Buscar dados do alojamento quando o componente for montado
onMounted(() => {
  fetchAlojamento();
});
</script>

<style scoped>
.bg-primary {
  background-color: #9faea0;
}

.text-accent {
  color: #e6e019;
}

.text-dark {
  color: #616160;
}
</style>
