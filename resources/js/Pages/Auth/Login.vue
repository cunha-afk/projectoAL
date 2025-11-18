<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticationCard from '@/Components/AuthenticationCard.vue'
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue'
import Checkbox from '@/Components/Checkbox.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import axios from 'axios'

defineProps({
    canResetPassword: Boolean,
    status: String,
})

// Formulário de login padrão do Jetstream
const form = useForm({
    email: '',
    password: '',
    remember: false,
})

// 🔐 Estado reativo para guardar token local (apenas se usares autenticação via API)
const authToken = ref(null)

// Ler o token do localStorage de forma segura após montar o componente
onMounted(() => {
    if (typeof window !== 'undefined' && window.localStorage) {
        authToken.value = localStorage.getItem('auth_token')
    }
})

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
            console.log('✅ Login bem-sucedido', response)
        },
        onError: (error) => {
            console.error('❌ Erro ao fazer login', error)
            alert('Erro ao realizar login. Verifique suas credenciais e tente novamente.')
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
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <!-- Mensagem de status (ex: senha redefinida com sucesso) -->
        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <!-- Formulário de login -->
        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
