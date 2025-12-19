<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import backend from '@/axiosBackend'

const props = defineProps({
  id: {
    type: [Number, String],
    required: true,
  },
})

const form = ref({
  user_id: '',
  alojamento_id: '',
  checkin: '',
  checkout: '',
  hospedes: 1,
  estado: '',
  observacoes: '',
})

const users = ref([])
const alojamentos = ref([])

const loading = ref(true)
const saving = ref(false)
const errors = ref({})

// carregar opções
const fetchOptions = async () => {
  const [usersRes, alojRes] = await Promise.all([
    backend.get('/utilizadores', { params: { per_page: 100 } }),
    backend.get('/alojamentos', { params: { per_page: 100 } }),
  ])

  users.value = usersRes.data.data || []
  alojamentos.value = alojRes.data.data || []
}

// carregar reserva
const fetchReserva = async () => {
  const res = await backend.get(`/reservas/${props.id}`)
  const r = res.data

  form.value = {
    user_id: r.user_id,
    alojamento_id: r.alojamento_id,
    checkin: r.checkin,
    checkout: r.checkout,
    hospedes: r.hospedes,
    estado: r.estado,
    observacoes: r.observacoes || '',
  }
}

const submit = async () => {
  saving.value = true
  errors.value = {}

  try {
    await backend.put(`/reservas/${props.id}`, {
      user_id: form.value.user_id,
      alojamento_id: form.value.alojamento_id,
      checkin: form.value.checkin,
      checkout: form.value.checkout,
      hospedes: form.value.hospedes,
      observacoes: form.value.observacoes,
    })

    router.visit('/admin/reservas')
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
    }
  } finally {
    saving.value = false
  }
}

// cancelar (única ação de estado)
const cancelarReserva = async () => {
  if (!confirm('Cancelar esta reserva?')) return

  try {
    await backend.patch(`/reservas/${props.id}/cancelar`)
    form.value.estado = 'cancelado'
  } catch {
    alert('Erro ao cancelar reserva.')
  }
}

onMounted(async () => {
  await fetchOptions()
  await fetchReserva()
  loading.value = false
})
</script>

<template>
  <AdminLayout title="Editar Reserva">
    <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
      <div class="flex justify-between mb-4">
        <h1 class="text-xl font-bold">
          Editar Reserva #{{ props.id }}
        </h1>
        <button class="text-sm text-gray-600" @click="router.visit('/admin/reservas')">
          ← Voltar
        </button>
      </div>

      <div v-if="loading" class="text-sm text-gray-500">
        A carregar reserva...
      </div>

      <form v-else class="space-y-4" @submit.prevent="submit">
        <!-- Cliente -->
        <select v-model="form.user_id" class="w-full border px-3 py-2 rounded">
          <option value="">Selecionar cliente</option>
          <option v-for="u in users" :key="u.id" :value="u.id">
            {{ u.name }} ({{ u.email }})
          </option>
        </select>

        <!-- Alojamento -->
        <select v-model="form.alojamento_id" class="w-full border px-3 py-2 rounded">
          <option value="">Selecionar alojamento</option>
          <option v-for="a in alojamentos" :key="a.id" :value="a.id">
            {{ a.titulo }}
          </option>
        </select>

        <!-- Datas -->
        <div class="grid grid-cols-2 gap-4">
          <input v-model="form.checkin" type="date" class="border px-3 py-2 rounded" />
          <input v-model="form.checkout" type="date" class="border px-3 py-2 rounded" />
        </div>

        <!-- Hóspedes -->
        <input
          v-model.number="form.hospedes"
          type="number"
          min="1"
          class="border px-3 py-2 rounded"
        />

        <!-- Estado (read-only) -->
        <input
          :value="form.estado"
          disabled
          class="w-full bg-gray-100 border px-3 py-2 rounded"
        />

        <!-- Observações -->
        <textarea
          v-model="form.observacoes"
          rows="3"
          class="w-full border px-3 py-2 rounded"
        ></textarea>

        <!-- Botões -->
        <div class="flex justify-between pt-4 border-t">
          <button
            v-if="form.estado !== 'cancelado'"
            type="button"
            class="bg-red-600 text-white px-4 py-2 rounded"
            @click="cancelarReserva"
          >
            Cancelar Reserva
          </button>

          <button
            type="submit"
            class="bg-indigo-600 text-white px-4 py-2 rounded"
          >
            Guardar Alterações
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>