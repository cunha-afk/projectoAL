<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import Navbar from '../Components/NavBar.vue'; 

const page = usePage();
const user = page.props.auth.user;

// Formulário de edição
const form = useForm({
    name: user.name,
    email: user.email,
    nif: user.nif ?? "",
    profile_photo: null,
});

// Upload pre-visualização
const preview = ref(user.profile_photo_url ?? "/images/default-avatar.png");

const handleImage = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    form.profile_photo = file;
    preview.value = URL.createObjectURL(file);
};

const submit = () => {
    form.post("/perfil/update", {
        preserveScroll: true,
    });
};
</script>

<template>
  <div class="pt-24 max-w-6xl mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
    <Navbar />

    <!-- FOTO PERFIL -->
    <div class="bg-white shadow-md rounded-lg p-6 flex flex-col items-center">
      <img
        :src="preview"
        class="w-40 h-40 object-cover rounded-full border shadow"
      />

      <label class="mt-4 cursor-pointer bg-primary text-white px-4 py-2 rounded-md">
        Alterar imagem
        <input type="file" class="hidden" @change="handleImage" accept="image/*" />
      </label>

      <h2 class="mt-4 text-2xl font-semibold text-dark">{{ form.name }}</h2>
    </div>

    <!-- FORMULÁRIO -->
    <div class="md:col-span-2 bg-white shadow-md rounded-lg p-6">
      <h2 class="text-2xl font-bold text-dark mb-6">Informações Pessoais</h2>

      <form @submit.prevent="submit" class="space-y-4">

        <!-- Nome -->
        <div>
          <label class="block font-medium mb-1">Nome</label>
          <input type="text" v-model="form.name"
            class="w-full border rounded-md px-3 py-2" />
        </div>

        <!-- Email -->
        <div>
          <label class="block font-medium mb-1">Email</label>
          <input type="email" v-model="form.email"
            class="w-full border rounded-md px-3 py-2" />
        </div>

        <!-- NIF -->
        <div>
          <label class="block font-medium mb-1">NIF</label>
          <input type="text" v-model="form.nif"
            class="w-full border rounded-md px-3 py-2" />
        </div>

        <!-- Botão -->
        <div class="pt-4">
          <button
            type="submit"
            class="bg-accent text-dark px-6 py-2 rounded-md font-semibold hover:bg-yellow-300"
          >
            Guardar Alterações
          </button>
        </div>

      </form>
    </div>

  </div>
</template>

<style scoped>
.bg-primary {
  background-color: #9faea0;
}
.text-dark {
  color: #616160;
}
</style>
