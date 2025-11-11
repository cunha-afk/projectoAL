<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  alojamento: Object
})

const data_inicio = ref('')
const data_fim = ref('')
const mensagem = ref('')
const erro = ref('')

async function reservar() {
  try {
    await axios.post('/api/reservas', {
      alojamento_id: props.alojamento.id,
      data_inicio: data_inicio.value,
      data_fim: data_fim.value
    })

    mensagem.value = 'Reserva efetuada com sucesso!'
    erro.value = ''
  } catch (e) {
    erro.value = e.response?.data?.error || 'Erro ao efetuar reserva.'
  }
}
</script>

<template>
  <div class="p-6 max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">{{ alojamento.titulo }}</h1>
    <p class="mb-4">{{ alojamento.descricao }}</p>
    <p class="font-semibold mb-4">Preço por noite: {{ alojamento.preco_noite }}€</p>

    <div class="border p-4 rounded">
      <h2 class="text-lg font-semibold mb-2">Reservar</h2>
      <div class="flex flex-col gap-2">
        <input v-model="data_inicio" type="date" class="border p-2 rounded" />
        <input v-model="data_fim" type="date" class="border p-2 rounded" />
        <button @click="reservar" class="bg-[#9faea0] text-white px-4 py-2 rounded">Reservar</button>
      </div>

      <p v-if="mensagem" class="text-green-600 mt-3">{{ mensagem }}</p>
      <p v-if="erro" class="text-red-600 mt-3">{{ erro }}</p>
    </div>
  </div>
</template>
