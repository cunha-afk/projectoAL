<script setup>
import { useForm } from '@inertiajs/vue3'
import AuthenticationCard from '@/Components/AuthenticationCard.vue'
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
  email: String,
  postUrl: String,
})

const form = useForm({
  phone: '',
  nif: '',
})

const submit = () => {
  form.post(props.postUrl, {
    preserveScroll: true,
  })
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-secondary">

    <!-- Fundo rústico (igual ao Register) -->
    <div
      class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1529692236671-f1d6f09c09b0?auto=format&fit=crop&w=1350&q=80')] bg-cover bg-center opacity-30">
    </div>

    <!-- Card principal (igual ao Register) -->
    <div class="relative z-10 w-full max-w-md bg-white shadow-xl rounded-xl p-8 border border-primary">

      <h2 class="text-3xl font-semibold text-center mb-4 text-dark">
        Concluir Registo
      </h2>

      <p class="text-center text-dark/70 mb-8">
        Confirmação feita para:
        <span class="font-semibold">{{ email }}</span>
      </p>

      <form @submit.prevent="submit" class="space-y-5">

        <div>
          <label class="block text-dark mb-1">Telemóvel</label>
          <input
            v-model="form.phone"
            type="text"
            class="w-full border border-primary rounded-md p-2 bg-secondary/50 focus:ring-2 focus:ring-accent focus:outline-none"
            placeholder="Ex: 912345678"
            required
          />
          <InputError class="mt-2" :message="form.errors.phone" />
        </div>

        <div>
          <label class="block text-dark mb-1">NIF</label>
          <input
            v-model="form.nif"
            type="text"
            class="w-full border border-primary rounded-md p-2 bg-secondary/50 focus:ring-2 focus:ring-accent focus:outline-none"
            placeholder="9 dígitos"
            required
          />
          <InputError class="mt-2" :message="form.errors.nif" />
        </div>

        <button
          type="submit"
          class="w-full py-2 bg-primary hover:bg-dark transition text-white rounded-md shadow-md"
          :disabled="form.processing"
          :class="{ 'opacity-60 cursor-not-allowed': form.processing }"
        >
          Concluir Registo
        </button>
      </form>
    </div>

  </div>
</template>