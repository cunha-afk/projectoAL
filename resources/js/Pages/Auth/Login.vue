<script setup>
import { useForm } from '@inertiajs/vue3'

defineProps({
  canResetPassword: Boolean,
  status: String,
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

// Estado reativo para guardar token local (apenas se usares autenticação via API)
const authToken = ref(null)

// Ler o token do localStorage de forma segura após montar o componente
onMounted(() => {
    if (typeof window !== 'undefined' && window.localStorage) {
        authToken.value = localStorage.getItem('auth_token')
    }
})
const loginError = ref('')
// Função para login
const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
        onSuccess: (response) => {
            // Garante que o window existe antes de usar localStorage
            if (typeof window !== 'undefined' && window.localStorage) {
                // Guardar token (caso estejas a usar API personalizada)
                localStorage.setItem('auth_token', response?.token ?? 'logged_in')
                authToken.value = response?.token ?? 'logged_in'
            }
            console.log('Login bem-sucedido', response)
        },
        onError: (errors) => {
        loginError.value = errors?.email || ''
      }
    })
}

// Função para logout
const logout = () => {
    if (typeof window !== 'undefined' && window.localStorage) {
        localStorage.removeItem('auth_token')
        authToken.value = null
    }

    console.log('✅ Logout local efetuado')

    // Logout via Laravel API (opcional)
    axios.post('/logout').then(() => {
        console.log('✅ Logout API efetuado')
    }).catch((error) => {
        console.error('❌ Erro ao fazer logout na API', error)
    })
}
</script>


<template>
  <div class="min-h-screen flex items-center justify-center bg-secondary">

    <!-- Fundo rústico em imagem + cor base -->
    <div
      class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1350&q=80')] bg-cover bg-center opacity-30">
    </div>

    <!-- Card -->
    <div class="relative z-10 w-full max-w-md bg-white shadow-xl rounded-xl p-8 border border-primary">
      
      <h2 class="text-3xl font-semibold text-center mb-4 text-dark">
        Bem-vindo(a)
      </h2>

      <p class="text-center text-dark/70 mb-8">
        Entre para gerir ou reservar o seu alojamento
      </p>
      <div
        v-if="loginError"
        class="mb-4 rounded-md border border-red-300 bg-red-50 p-3 text-sm text-red-700"
      >
        {{ loginError }}
      </div>
      <form @submit.prevent="submit" class="space-y-5">

        <div>
          <label class="block text-dark mb-1">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full border border-primary rounded-md p-2 bg-secondary/50 focus:ring-2 focus:ring-accent focus:outline-none"
          />
        </div>

        <div>
          <label class="block text-dark mb-1">Password</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full border border-primary rounded-md p-2 bg-secondary/50 focus:ring-2 focus:ring-accent focus:outline-none"
          />
        </div>

        <div class="flex items-center justify-between text-dark">
          <label class="flex items-center space-x-2">
            <input type="checkbox" v-model="form.remember" />
            <span>Lembrar-me</span>
          </label>
          <a href="/forgot-password" class="hover:underline text-dark">
            Esqueceu a password?
          </a>
        </div>

        <button
          type="submit"
          class="w-full py-2 bg-primary hover:bg-dark transition text-white rounded-md shadow-md"
        >
          Entrar
        </button>
      </form>

      <p class="text-center mt-6 text-dark">
        Ainda não tem conta?
        <a href="/register" class="text-accent font-semibold hover:underline">
          Criar conta
        </a>
      </p>
    </div>

  </div>
</template>
