<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-secondary">

    <!-- Fundo rústico -->
    <div
      class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1529692236671-f1d6f09c09b0?auto=format&fit=crop&w=1350&q=80')] bg-cover bg-center opacity-30">
    </div>

    <!-- Card principal -->
    <div class="relative z-10 w-full max-w-md bg-white shadow-xl rounded-xl p-8 border border-primary">
      
      <h2 class="text-3xl font-semibold text-center mb-4 text-dark">
        Criar Conta
      </h2>

      <p class="text-center text-dark/70 mb-8">
        Junte-se à nossa plataforma de alojamento
      </p>

      <form @submit.prevent="submit" class="space-y-5">

        <div>
          <label class="block text-dark mb-1">Nome</label>
          <input
            v-model="form.name"
            type="text"
            class="w-full border border-primary rounded-md p-2 bg-secondary/50 focus:ring-2 focus:ring-accent focus:outline-none"
            required
          />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div>
          <label class="block text-dark mb-1">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full border border-primary rounded-md p-2 bg-secondary/50 focus:ring-2 focus:ring-accent focus:outline-none"
            required
          />
          <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div>
          <label class="block text-dark mb-1">Password</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full border border-primary rounded-md p-2 bg-secondary/50 focus:ring-2 focus:ring-accent focus:outline-none"
            required
          />
          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div>
          <label class="block text-dark mb-1">Confirmar Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            class="w-full border border-primary rounded-md p-2 bg-secondary/50 focus:ring-2 focus:ring-accent focus:outline-none"
            required
          />
          <InputError class="mt-2" :message="form.errors.password_confirmation" />
        </div>
      
        <button
          type="submit"
          class="w-full py-2 bg-primary hover:bg-dark transition text-white rounded-md shadow-md"
        >
          Criar Conta
        </button>
      </form>

      <p class="text-center mt-6 text-dark">
        Já tem conta?
        <a href="/login" class="text-accent font-semibold hover:underline">
          Entrar
        </a>
      </p>
    </div>

  </div>
</template>
