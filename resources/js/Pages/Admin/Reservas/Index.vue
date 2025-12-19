<script setup>
import { ref, onMounted } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import axios from '@/axiosBackend'
import { router } from '@inertiajs/vue3'

// 🔹 Estado reativo
const loading = ref(false)

const reservas = ref([])
const pagination = ref({
  current_page: 1,
  last_page: 1,
  next_page_url: null,
  prev_page_url: null,
})

const filtros = ref({
  search: '',
  estado: 'todas',
  alojamento_id: '',
})

const estados = ref([])
const alojamentos = ref([])

// 🔹 Buscar reservas (com filtros e paginação)
const fetchReservas = async (url = null) => {
  loading.value = true

  try {
    const response = await axios.get(url || '/reservas', {
      params: {
        search: filtros.value.search || null,
        estado: filtros.value.estado || null,
        alojamento_id: filtros.value.alojamento_id || null,
        page: pagination.value.current_page,
      },
    })

    const data = response.data

    reservas.value = Array.isArray(data?.reservas?.data)
      ? data.reservas.data
      : []

    pagination.value = {
      current_page: data.reservas.current_page,
      last_page: data.reservas.last_page,
      next_page_url: data.reservas.next_page_url,
      prev_page_url: data.reservas.prev_page_url,
    }

    estados.value = data.estados || []
    alojamentos.value = data.alojamentos || []
  } catch (error) {
    console.error('Erro ao carregar reservas:', error)
  } finally {
    loading.value = false
  }
}

// 🔹 Ações de UI
const aplicarFiltros = () => {
  pagination.value.current_page = 1
  fetchReservas()
}

const limparFiltros = () => {
  filtros.value = {
    search: '',
    estado: 'todas',
    alojamento_id: '',
  }
  pagination.value.current_page = 1
  fetchReservas()
}

const irParaPagina = (url) => {
  if (!url) return
  fetchReservas(url)
}

// ✅ Navegação (SEM Ziggy)
const verReserva = (id) => {
  router.visit(`/admin/reservas/${id}/editar`)
}

const editarReserva = (id) => {
  router.visit(`/admin/reservas/${id}/editar`)
}

const irParaCriarReserva = () => {
  router.visit('/admin/reservas/criar')
}

// ✅ ÚNICA ação de estado: cancelar
const cancelarReserva = async (reserva) => {
  if (reserva.estado === 'cancelado') return

  if (!confirm(`Cancelar a reserva #${reserva.id}?`)) return

  try {
    await axios.patch(`/reservas/${reserva.id}/cancelar`)
    reserva.estado = 'cancelado'
  } catch (error) {
    alert('Não foi possível cancelar a reserva.')
  }
}

// 👉 Apagar reserva
const apagarReserva = async (reserva) => {
  if (!confirm(`Apagar a reserva #${reserva.id}?`)) return

  try {
    await axios.delete(`/reservas/${reserva.id}`)
    await fetchReservas()
  } catch (error) {
    console.error('Erro ao apagar reserva:', error)
  }
}

// Carregar na montagem
onMounted(() => {
  fetchReservas()
})
</script>

<template>
  <AdminLayout title="Gestão de Reservas">
    <!-- 1️⃣ Header da Página -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <p class="text-sm text-gray-500">
          Central de operações das reservas do sistema.
        </p>
      </div>

      <button
        type="button"
        class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow hover:bg-indigo-700 transition"
        @click="irParaCriarReserva"
      >
        + Criar Reserva
      </button>
    </div>

    <!-- 2️⃣ Zona de Filtros -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
      <div class="flex flex-wrap gap-4 items-end">
        <!-- Pesquisa por ID -->
        <div class="flex-1 min-w-[150px]">
          <label class="block text-xs font-semibold text-gray-600 mb-1">
            ID da Reserva
          </label>
          <input
            v-model="filtros.search"
            type="text"
            placeholder="Ex: 152"
            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <!-- Estado -->
        <div class="w-40">
          <label class="block text-xs font-semibold text-gray-600 mb-1">
            Estado
          </label>
          <select
            v-model="filtros.estado"
            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="todas">Todas</option>
            <option v-for="estado in estados" :key="estado" :value="estado">
              {{ estado.charAt(0).toUpperCase() + estado.slice(1) }}
            </option>
          </select>
        </div>

        <!-- Alojamento -->
        <div class="w-56">
          <label class="block text-xs font-semibold text-gray-600 mb-1">
            Alojamento
          </label>
          <select
            v-model="filtros.alojamento_id"
            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">Todos</option>
            <option v-for="aloj in alojamentos" :key="aloj.id" :value="aloj.id">
              {{ aloj.titulo }}
            </option>
          </select>
        </div>

        <!-- Botões -->
        <div class="flex gap-2">
          <button
            type="button"
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition"
            @click="aplicarFiltros"
          >
            Filtrar
          </button>

          <button
            type="button"
            class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition"
            @click="limparFiltros"
          >
            Limpar filtros
          </button>
        </div>
      </div>
    </div>

    <!-- 3️⃣ Tabela Principal -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div v-if="loading" class="p-4 text-sm text-gray-500">
        A carregar reservas...
      </div>

      <table v-else class="min-w-full text-sm">
        <thead class="bg-gray-100 text-left text-xs font-semibold text-gray-600">
          <tr>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Cliente</th>
            <th class="px-4 py-3">Alojamento</th>
            <th class="px-4 py-3">Check-in</th>
            <th class="px-4 py-3">Check-out</th>
            <th class="px-4 py-3 text-center">Pessoas</th>
            <th class="px-4 py-3">Estado</th>
            <th class="px-4 py-3 text-right">Preço Total (€)</th>
            <th class="px-4 py-3 text-right">Ações</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="reserva in reservas"
            :key="reserva.id"
            class="border-t text-gray-700 hover:bg-gray-50"
          >
            <td class="px-4 py-3 text-xs font-mono">#{{ reserva.id }}</td>

            <td class="px-4 py-3">
              <div class="text-xs font-semibold">
                {{ reserva.user?.name || 'N/D' }}
              </div>
              <div class="text-[11px] text-gray-500">
                {{ reserva.user?.email }}
              </div>
            </td>

            <td class="px-4 py-3 text-xs">
              {{ reserva.alojamento?.titulo || 'N/D' }}
            </td>

            <td class="px-4 py-3 text-xs">{{ reserva.checkin }}</td>
            <td class="px-4 py-3 text-xs">{{ reserva.checkout }}</td>

            <td class="px-4 py-3 text-center text-xs">{{ reserva.hospedes }}</td>

            <!-- Badge de estado -->
            <td class="px-4 py-3">
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                :class="{
                  'bg-yellow-100 text-yellow-800': reserva.estado === 'pendente',
                  'bg-green-100 text-green-800': reserva.estado === 'confirmado',
                  'bg-red-100 text-red-800': reserva.estado === 'cancelado',
                }"
              >
                {{ reserva.estado }}
              </span>
            </td>

            <td class="px-4 py-3 text-right text-xs font-semibold">
              {{ Number(reserva.total).toFixed(2) }} €
            </td>

            <!-- Ações -->
            <td class="px-4 py-3 text-right">
              <div class="flex justify-end gap-2 text-[11px]">
                <button
                  class="px-2 py-1 border rounded hover:bg-gray-100"
                  title="Ver detalhes"
                  @click="verReserva(reserva.id)"
                >
                  Ver
                </button>

                <button
                  class="px-2 py-1 border rounded hover:bg-gray-100"
                  title="Editar"
                  @click="editarReserva(reserva.id)"
                >
                  Editar
                </button>

                <button
                  v-if="reserva.estado !== 'cancelado'"
                  class="px-2 py-1 border rounded hover:bg-red-50 text-red-600"
                  title="Cancelar"
                  @click="cancelarReserva(reserva)"
                >
                  Cancelar
                </button>

                <button
                  class="px-2 py-1 border rounded text-red-600 hover:bg-red-50"
                  title="Apagar"
                  @click="apagarReserva(reserva)"
                >
                  Apagar
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="!reservas.length && !loading">
            <td colspan="9" class="px-4 py-6 text-center text-xs text-gray-500">
              Nenhuma reserva encontrada com os filtros atuais.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- 5️⃣ Paginação -->
    <div class="mt-4 flex items-center justify-between text-xs text-gray-600">
      <div>
        Página {{ pagination.current_page }} de {{ pagination.last_page }}
      </div>

      <div class="flex gap-2">
        <button
          type="button"
          class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="!pagination.prev_page_url"
          @click="irParaPagina(pagination.prev_page_url)"
        >
          Anterior
        </button>

        <button
          type="button"
          class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="!pagination.next_page_url"
          @click="irParaPagina(pagination.next_page_url)"
        >
          Seguinte
        </button>
      </div>
    </div>
  </AdminLayout>
</template>
