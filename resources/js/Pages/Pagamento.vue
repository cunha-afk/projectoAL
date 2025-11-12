<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'

const pagamento = ref(null)
const reservaId = 1 // exemplo

onMounted(async () => {
  const res = await axios.post(`/api/pagamentos/checkout/${reservaId}`)
  pagamento.value = res.data.pagamento
})
</script>

<template>
  <div class="p-4">
    <h2 class="text-xl font-bold">Pagamento</h2>
    <div v-if="pagamento">
      <p>Entidade: {{ pagamento.method?.entity }}</p>
      <p>Referência: {{ pagamento.method?.reference }}</p>
      <p>Valor: €{{ pagamento.value }}</p>
    </div>
  </div>
</template>
