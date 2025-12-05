<script setup>
import { ref, onMounted } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import axios from '@/axiosBackend' // assumindo baseURL = '/admin/api'

const comentarios = ref([])
const pagination = ref(null)
const loading = ref(false)
const statusFilter = ref('todos') // tdos | pendentes | aprovados

const setStatusFilter = (value) => {
  if (statusFilter.value === value) return // se já estiver nesse filtro, não faz nada
  statusFilter.value = value
  fetchComentarios() // volta a buscar comentários (primeira página)
}

const respostaTexto = ref('')
const comentarioSelecionado = ref(null)
const erroResposta = ref('')

const fetchComentarios = async (url = null) => {
  loading.value = true
  try {
    const response = await axios.get(url || '/comentarios', {
      params: { status: statusFilter.value },
    })

    // Garante que ficas SEMPRE com um array
    comentarios.value = Array.isArray(response.data?.data)
      ? response.data.data
      : []

    if (response.data) {
      pagination.value = {
        current_page: response.data.current_page ?? 1,
        last_page: response.data.last_page ?? 1,
        next_page_url: response.data.next_page_url ?? null,
        prev_page_url: response.data.prev_page_url ?? null,
      }
    } else {
      pagination.value = null
    }
  } catch (e) {
    console.error('Erro ao carregar comentários', e)
    comentarios.value = []   // mesmo em erro, mantém array
    pagination.value = null
  } finally {
    loading.value = false
  }
}



const aprovar = async (comentario) => {
  try {
    await axios.post(`/comentarios/${comentario.id}/aprovar`)
    fetchComentarios()
  } catch (e) {
    console.error('Erro ao aprovar', e)
  }
}

const apagar = async (comentario) => {
  if (!confirm('Tens a certeza que queres apagar este comentário?')) return
  try {
    await axios.delete(`/comentarios/${comentario.id}`)
    fetchComentarios()
  } catch (e) {
    console.error('Erro ao apagar', e)
  }
}

const abrirResposta = (comentario) => {
  comentarioSelecionado.value = comentario
  respostaTexto.value = comentario.resposta_admin || ''
  erroResposta.value = ''
}

const enviarResposta = async () => {
  if (!comentarioSelecionado.value) return

  try {
    await axios.post(`/comentarios/${comentarioSelecionado.value.id}/responder`, {
      resposta_admin: respostaTexto.value,
    })
    comentarioSelecionado.value = null
    respostaTexto.value = ''
    fetchComentarios()
  } catch (e) {
    console.error('Erro ao responder', e)
    erroResposta.value = 'Erro ao guardar a resposta.'
  }
}

const goToPage = (url) => {
  if (!url) return
  fetchComentarios(url.replace('/admin/api', '')) // se o backend devolver URL absoluto, ajusta conforme o teu axiosBackend
}

onMounted(() => fetchComentarios())
</script>

<template>
  <AdminLayout title="Gestão de Comentários">
    <div class="flex justify-between items-center mb-4">
  <h1 class="text-2xl font-bold">Comentários</h1>

  <!-- Filtros tipo "tabs" -->
  <div class="inline-flex border rounded-lg overflow-hidden text-sm bg-white">
    
    <button
      type="button"
      class="px-4 py-2"
      :class="statusFilter === 'todos'
        ? 'bg-indigo-600 text-white'
        : 'bg-white text-gray-700 hover:bg-gray-100'"
      @click="setStatusFilter('todos')"
    >
      Todos
    </button>
    
    
    <button
      type="button"
      class="px-4 py-2 border-r last:border-r-0"
      :class="statusFilter === 'pendentes'
        ? 'bg-indigo-600 text-white'
        : 'bg-white text-gray-700 hover:bg-gray-100'"
      @click="setStatusFilter('pendentes')"
    >
      Pendentes
    </button>

    <button
      type="button"
      class="px-4 py-2 border-r last:border-r-0"
      :class="statusFilter === 'aprovados'
        ? 'bg-indigo-600 text-white'
        : 'bg-white text-gray-700 hover:bg-gray-100'"
      @click="setStatusFilter('aprovados')"
    >
      Aprovados
    </button>

    
  </div>
</div>


    <div v-if="loading">A carregar...</div>

    <div v-else>
    <div v-if="!comentarios || comentarios.length === 0" class="text-gray-500">
        Nenhum comentário encontrado.
    </div>

      <div
        v-for="comentario in comentarios"
        :key="comentario.id"
        class="bg-white shadow rounded p-4 mb-3"
      >
        <div class="flex justify-between mb-2">
          <div>
            <p class="font-semibold">
              {{ comentario.user?.name || 'Utilizador' }}
              <span class="text-xs text-gray-500">
                ({{ comentario.user?.email }})
              </span>
            </p>
            <p class="text-sm text-gray-500">
              Alojamento:
              <span v-if="comentario.alojamento">
                {{ comentario.alojamento.titulo }}
              </span>
              <span v-else>-</span>
            </p>
          </div>

          <span
  class="inline-flex items-center justify-center rounded-lg text-xs font-medium min-w-[90px] h-8 px-3"
  :class="comentario.aprovado
    ? 'bg-green-100 text-green-700'
    : 'bg-yellow-100 text-yellow-700'"
>
  {{ comentario.aprovado ? 'Aprovado' : 'Pendente' }}
</span>

        </div>

        <p class="font-semibold mb-1">{{ comentario.titulo }}</p>
        <p class="text-sm text-gray-600 mb-1">
          Rating: {{ comentario.rating }}/5
        </p>
        <p class="mb-3">{{ comentario.texto }}</p>

        <div
          v-if="comentario.resposta_admin"
          class="bg-gray-50 border-l-4 border-blue-400 p-3 mb-3 text-sm"
        >
          <p class="font-semibold mb-1">Resposta do admin:</p>
          <p>{{ comentario.resposta_admin }}</p>
        </div>

        <div class="flex gap-2">
          <button
            v-if="!comentario.aprovado"
            @click="aprovar(comentario)"
            class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700"
          >
            Aprovar
          </button>

          <button
            @click="abrirResposta(comentario)"
            class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700"
          >
            Responder / Editar resposta
          </button>

          <button
            @click="apagar(comentario)"
            class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700"
          >
            Apagar
          </button>
        </div>
      </div>

      <!-- Paginação -->
      <div
        v-if="pagination && (pagination.prev_page_url || pagination.next_page_url)"
        class="flex justify-between items-center mt-4"
      >
        <button
          :disabled="!pagination.prev_page_url"
          @click="goToPage(pagination.prev_page_url)"
          class="px-3 py-1 border rounded disabled:opacity-50"
        >
          Anterior
        </button>

        <span class="text-sm text-gray-600">
          Página {{ pagination.current_page }} de {{ pagination.last_page }}
        </span>

        <button
          :disabled="!pagination.next_page_url"
          @click="goToPage(pagination.next_page_url)"
          class="px-3 py-1 border rounded disabled:opacity-50"
        >
          Seguinte
        </button>
      </div>
    </div>

    <!-- Modal de resposta -->
    <div
      v-if="comentarioSelecionado"
      class="fixed inset-0 bg-black/40 flex items-center justify-center"
    >
      <div class="bg-white rounded shadow p-4 w-full max-w-lg">
        <h2 class="text-lg font-semibold mb-2">
          Responder ao comentário: {{ comentarioSelecionado.titulo }}
        </h2>

        <textarea
          v-model="respostaTexto"
          rows="4"
          class="w-full border rounded px-3 py-2 mb-2"
          placeholder="Escreve aqui a resposta do admin..."
        ></textarea>

        <p v-if="erroResposta" class="text-sm text-red-600 mb-2">
          {{ erroResposta }}
        </p>

        <div class="flex justify-end gap-2">
          <button
            @click="comentarioSelecionado = null"
            class="px-3 py-1 border rounded"
          >
            Cancelar
          </button>
          <button
            @click="enviarResposta"
            class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            Guardar resposta
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
