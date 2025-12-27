<script setup>
import { onMounted, ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import http from '@/http'

const loading = ref(true)
const error = ref(null)

const data = ref({
  kpis: {
    reservas_mes: 0,
    receita_mes: 0,
    ocupacao_percent: 0,
    comentarios_pendentes: 0,
    alojamentos: 0,
  },
  lists: {
    checkins_hoje: [],
    ultimas_reservas: [],
    comentarios_pendentes: [],
  },
  chart: {
    reservas_por_dia: [],
  },
})

const fetchDashboard = async () => {
  loading.value = true
  error.value = null

  try {
    // Isto vai para: /api/admin/dashboard (porque o http tem baseURL '/api')
    const res = await http.get('/admin/dashboard')
    data.value = res.data
  } catch (e) {
    error.value = 'Não foi possível carregar a dashboard.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchDashboard)

const fmtMoney = (v) => {
  const n = Number(v ?? 0)
  return n.toLocaleString('pt-PT', { style: 'currency', currency: 'EUR' })
}
</script>

<template>
  <AdminLayout title="Dashboard">
    <div v-if="loading" class="p-6">A carregar…</div>

    <div v-else class="p-6 space-y-6">
      <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 rounded p-4">
        {{ error }}
      </div>

      <!-- KPIs -->
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded shadow p-4">
          <div class="text-sm text-gray-500">Reservas (mês)</div>
          <div class="text-2xl font-semibold">{{ data.kpis.reservas_mes }}</div>
        </div>

        <div class="bg-white rounded shadow p-4">
          <div class="text-sm text-gray-500">Receita (mês)</div>
          <div class="text-2xl font-semibold">{{ fmtMoney(data.kpis.receita_mes) }}</div>
        </div>

        <div class="bg-white rounded shadow p-4">
          <div class="text-sm text-gray-500">Ocupação</div>
          <div class="text-2xl font-semibold">{{ data.kpis.ocupacao_percent }}%</div>
        </div>

        <div class="bg-white rounded shadow p-4">
          <div class="text-sm text-gray-500">Comentários pendentes</div>
          <div class="text-2xl font-semibold">{{ data.kpis.comentarios_pendentes }}</div>
        </div>

        <div class="bg-white rounded shadow p-4">
          <div class="text-sm text-gray-500">Alojamentos</div>
          <div class="text-2xl font-semibold">{{ data.kpis.alojamentos }}</div>
        </div>
      </div>

      <!-- Ações rápidas -->
      <div class="flex flex-wrap gap-2">
        <a :href="route('admin.reservas.create')" class="bg-indigo-600 text-white px-4 py-2 rounded">
          Criar Reserva
        </a>
        <a :href="route('admin.alojamentos.create')" class="bg-indigo-600 text-white px-4 py-2 rounded">
          Criar Alojamento
        </a>
      </div>

      <!-- Listas -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded shadow p-4">
          <div class="font-semibold mb-3">Check-ins hoje</div>
          <div v-if="!data.lists.checkins_hoje.length" class="text-gray-500">Nada para hoje.</div>
          <ul v-else class="space-y-2">
            <li v-for="r in data.lists.checkins_hoje" :key="r.id" class="border rounded p-2">
              <div class="text-sm text-gray-500">#{{ r.id }}</div>
              <div class="font-medium">{{ r.alojamento }}</div>
              <div class="text-sm">{{ r.user }}</div>
              <div class="text-xs text-gray-500">Estado: {{ r.estado }}</div>
            </li>
          </ul>
        </div>

        <div class="bg-white rounded shadow p-4">
          <div class="font-semibold mb-3">Últimas reservas</div>
          <div v-if="!data.lists.ultimas_reservas.length" class="text-gray-500">Sem dados.</div>
          <ul v-else class="space-y-2">
            <li v-for="r in data.lists.ultimas_reservas" :key="r.id" class="border rounded p-2">
              <div class="text-sm text-gray-500">#{{ r.id }} · {{ r.created_at }}</div>
              <div class="font-medium">{{ r.alojamento }}</div>
              <div class="text-sm">{{ r.user }} · {{ fmtMoney(r.total) }}</div>
              <div class="text-xs text-gray-500">Estado: {{ r.estado }}</div>
            </li>
          </ul>
        </div>

        <div class="bg-white rounded shadow p-4">
          <div class="font-semibold mb-3">Comentários pendentes</div>
          <div v-if="!data.lists.comentarios_pendentes.length" class="text-gray-500">Tudo ok 👍</div>
          <ul v-else class="space-y-2">
            <li v-for="c in data.lists.comentarios_pendentes" :key="c.id" class="border rounded p-2">
              <div class="text-sm text-gray-500">#{{ c.id }} · {{ c.created_at }}</div>
              <div class="font-medium">{{ c.titulo ?? 'Sem título' }}</div>
              <div class="text-sm">{{ c.user }} · Rating: {{ c.rating ?? '-' }}</div>
            </li>
          </ul>
        </div>
      </div>

      <!-- “Gráfico” por enquanto (lista) -->
      <div class="bg-white rounded shadow p-4">
        <div class="font-semibold mb-3">Reservas (últimos 30 dias)</div>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-2 text-sm">
          <div v-for="p in data.chart.reservas_por_dia" :key="p.date" class="border rounded p-2">
            <div class="text-gray-500">{{ p.date }}</div>
            <div class="font-semibold">{{ p.count }}</div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
