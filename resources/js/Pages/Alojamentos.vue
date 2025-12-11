<script setup>
import { ref, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import Navbar from "../Components/NavBar.vue";

const page = usePage();
const alojamentos = ref(page.props.alojamentos ?? []);

// índice da foto atual por alojamento
const currentPhotoIndex = ref({});

// devolve a foto atual
const getCurrentPhoto = (alojamento) => {
  const fotos = alojamento.fotos || [];
  if (!fotos.length) return null;

  const index = currentPhotoIndex.value[alojamento.id] ?? 0;
  return fotos[index];
};

// próxima foto
const nextPhoto = (alojamento) => {
  const fotos = alojamento.fotos || [];
  if (!fotos.length) return;

  const actual = currentPhotoIndex.value[alojamento.id] ?? 0;
  const next = (actual + 1) % fotos.length;

  currentPhotoIndex.value = {
    ...currentPhotoIndex.value,
    [alojamento.id]: next,
  };
};

// foto anterior
const prevPhoto = (alojamento) => {
  const fotos = alojamento.fotos || [];
  if (!fotos.length) return;

  const actual = currentPhotoIndex.value[alojamento.id] ?? 0;
  const prev = (actual - 1 + fotos.length) % fotos.length;

  currentPhotoIndex.value = {
    ...currentPhotoIndex.value,
    [alojamento.id]: prev,
  };
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 text-dark">
    <Navbar />

    <main class="max-w-7xl mx-auto px-6 pt-28 pb-16">
      <h1 class="text-5xl font-serif font-bold text-center mb-10">Alojamentos</h1>

      <!-- GRID DE 4 -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div
          v-for="q in alojamentos"
          :key="q.id"
          class="bg-white rounded-xl shadow overflow-hidden hover:scale-[1.02] transition"
        >
          <!-- IMAGEM -->
          <div class="relative h-56 bg-gray-200 overflow-hidden">
            <template v-if="q.fotos?.length">
              <img
                :src="getCurrentPhoto(q)?.url"
                class="w-full h-full object-cover"
              />

              <!-- SETAS -->
              <button
                v-if="q.fotos.length > 1"
                @click.stop="prevPhoto(q)"
                class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 text-white w-8 h-8 rounded-full flex items-center justify-center"
              >
                ‹
              </button>

              <button
                v-if="q.fotos.length > 1"
                @click.stop="nextPhoto(q)"
                class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 text-white w-8 h-8 rounded-full flex items-center justify-center"
              >
                ›
              </button>

              <!-- INDICADOR -->
              <div
                v-if="q.fotos.length > 1"
                class="absolute bottom-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded"
              >
                {{ (currentPhotoIndex[q.id] ?? 0) + 1 }}/{{ q.fotos.length }}
              </div>
            </template>

            <span v-else class="text-gray-500 flex items-center justify-center w-full h-full">
              Sem foto
            </span>
          </div>

          <!-- TEXTO -->
          <div class="p-4 flex flex-col">
            <h2 class="font-bold text-xl mb-2">{{ q.nome }}</h2>

            <p class="text-gray-600 text-sm line-clamp-3 mb-3">
              {{ q.descricao }}
            </p>

            <p class="text-accent font-bold mb-4">
              {{ q.preco }}€
              <span class="text-gray-500 text-sm font-normal">/ noite</span>
            </p>

            <Link
              :href="`/alojamentos/${q.id}`"
              class="mt-auto bg-primary text-white text-center py-2 rounded-md hover:bg-secondary transition"
            >
              Ver Detalhes
            </Link>
          </div>
        </div>
      </div>
    </main>
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
.text-accent {
  color: #e6e019;
}
</style>
