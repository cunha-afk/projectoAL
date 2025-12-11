<script setup>
import { ref } from "vue"
import axios from "axios"
import { router, usePage, Link } from "@inertiajs/vue3"
import Navbar from "@/Components/NavBar.vue"

const page = usePage()
const reserva = page.props.reserva
const pagando = ref(false)
const erro = ref(null)
const sucesso = ref(null)

const pagar = async () => {
  pagando.value = true
  erro.value = null
  sucesso.value = null

  try {
    const res = await axios.post(`/api/pagamentos/checkout/${reserva.id}`)
    
    sucesso.value = "Pedido de pagamento criado! Aguarde confirmação no MBWay."
    console.log(res.data)

    if (res.data.redirect) {
      router.visit(res.data.redirect)
    }

  } catch (e) {
    erro.value = "Erro ao criar pagamento."
    console.error(e)
  } finally {
    pagando.value = false
  }
}
</script>

<template>
  <Navbar />

  <div class="max-w-3xl mx-auto mt-28 px-4">
    <h1 class="text-3xl font-bold text-dark mb-6">Checkout da Reserva</h1>

    <div class="bg-white shadow rounded-lg p-6">
      
      <!-- Dados do alojamento -->
      <h2 class="text-xl font-semibold text-dark">{{ reserva.alojamento.titulo }}</h2>
      <p class="text-gray-600">{{ reserva.data_inicio }} → {{ reserva.data_fim }}</p>

      <p class="mt-4 text-lg font-bold text-dark">
        Total: {{ reserva.preco_total }} €
      </p>

      <div class="mt-6">
        <button
          @click="pagar"
          :disabled="pagando"
          class="bg-accent text-dark px-6 py-2 rounded-md font-semibold hover:bg-yellow-300 disabled:opacity-50"
        >
          Pagar com MBWay
        </button>
      </div>

      <!-- Mensagens -->
      <p v-if="sucesso" class="mt-4 text-green-600 font-medium">{{ sucesso }}</p>
      <p v-if="erro" class="mt-4 text-red-600 font-medium">{{ erro }}</p>
    </div>

  </div>
</template>

<style scoped>
.text-dark {
  color: #616160;
}
</style>
