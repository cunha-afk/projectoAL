<template>
    <div class="min-h-screen bg-gray-50 text-dark font-sans flex flex-col">
        <!-- Header -->
        <header
            class="flex justify-between items-center p-6 bg-white shadow-sm"
        >
            <div class="flex items-center space-x-2">
                <img src="/images/logo.jpg" alt="Logo" class="h-8" />
                <h1 class="text-lg font-semibold text-dark">
                    Alojamento Local
                </h1>
            </div>

            <div class="flex space-x-4 text-sm text-dark/80">
                <span>EUR - €</span>
                <span>PT</span>
            </div>
        </header>

        <!-- Breadcrumb -->
        <div class="max-w-6xl mx-auto w-full py-6 px-6 md:px-0 text-sm">
            <nav class="flex space-x-2 text-gray-500">
                <span class="text-accent font-medium">Datas</span>
                <span>›</span>
                <span>Add-ons</span>
                <span>›</span>
                <span>Contacto</span>
                <span>›</span>
                <span>Pagamento</span>
            </nav>
        </div>

        <!-- Main content -->
        <main
            class="flex flex-col md:flex-row justify-between max-w-6xl mx-auto w-full px-6 md:px-0 space-y-10 md:space-y-0 md:space-x-10"
        >
            <!-- Formulário -->
            <section class="flex-1 space-y-6">
                <h2 class="text-2xl font-semibold mb-4">Datas</h2>

                <!-- Mensagens de erro -->
                <div
                    v-if="errors.length"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                >
                    <strong class="font-bold">Erro!</strong>
                    <ul class="list-disc list-inside mt-2">
                        <li v-for="(error, index) in errors" :key="index">
                            {{ error }}
                        </li>
                    </ul>
                </div>

                <!-- Mensagem de sucesso -->
                <div
                    v-if="successMessage"
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                >
                    {{ successMessage }}
                </div>

                <!-- Campos -->
                <div
                    class="bg-white p-6 rounded-lg shadow-md flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0"
                >
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-dark mb-1"
                            >Check-in</label
                        >
                        <input
                            type="date"
                            v-model="checkin"
                            :min="minDate"
                            @change="verificarDisponibilidade"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-primary outline-none"
                        />
                    </div>

                    <div class="flex-1">
                        <label class="block text-sm font-medium text-dark mb-1"
                            >Check-out</label
                        >
                        <input
                            type="date"
                            v-model="checkout"
                            :min="checkin"
                            @change="verificarDisponibilidade"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-primary outline-none"
                        />
                    </div>

                    <div class="flex items-center justify-center space-x-2">
                        <button
                            @click="hospedes > 1 ? hospedes-- : hospedes"
                            class="px-3 py-1 bg-secondary rounded-md text-dark font-bold"
                        >
                            −
                        </button>
                        <span class="text-dark font-medium"
                            >{{ hospedes }} hóspede{{
                                hospedes > 1 ? "s" : ""
                            }}</span
                        >
                        <button
                            @click="hospedes++"
                            class="px-3 py-1 bg-secondary rounded-md text-dark font-bold"
                        >
                            +
                        </button>
                    </div>
                </div>

                <!-- Status de disponibilidade -->
                <div v-if="disponibilidadeChecada" class="mt-4">
                    <div
                        v-if="alojamentoDisponivel"
                        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
                    >
                        ✓ Alojamento disponível para estas datas!
                    </div>
                    <div
                        v-else
                        class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded"
                    >
                        ⚠ Alojamento não disponível para estas datas.
                    </div>
                </div>

                <!-- Observações -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <label class="block text-sm font-medium text-dark mb-2"
                        >Observações (opcional)</label
                    >
                    <textarea
                        v-model="observacoes"
                        rows="4"
                        maxlength="1000"
                        placeholder="Alguma informação adicional..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-primary outline-none"
                    ></textarea>
                    <span class="text-xs text-gray-500"
                        >{{ observacoes.length }}/1000</span
                    >
                </div>
            </section>

            <!-- Resumo -->
            <aside
                class="w-full md:w-1/3 bg-white rounded-lg shadow-md p-6 self-start"
            >
                <h3 class="text-lg font-semibold mb-4">Resumo da Reserva</h3>

                <div class="flex items-center mb-4 space-x-3 border-b pb-4">
                    <img
                        :src="alojamento.imagem || '/images/casa1.jpg'"
                        alt="Alojamento"
                        class="w-16 h-16 rounded object-cover"
                    />
                    <div>
                        <h4 class="text-sm font-medium text-dark">
                            {{ alojamento.nome || "Casa do Sol" }}
                        </h4>
                        <button
                            class="text-xs text-accent hover:underline mt-1"
                        >
                            Ver detalhes
                        </button>
                    </div>
                </div>

                <div class="flex justify-between text-sm text-dark/70 mb-2">
                    <span>Estadia</span>
                    <span>{{ noites }} noite{{ noites > 1 ? "s" : "" }}</span>
                </div>
                <div class="flex justify-between text-sm text-dark/70 mb-2">
                    <span>Preço/noite</span>
                    <span>{{ precoNoite }}€</span>
                </div>
                <hr class="my-3" />
                <div class="flex justify-between font-semibold text-dark">
                    <span>Total (EUR)</span>
                    <span>{{ total }}€</span>
                </div>
            </aside>
        </main>

        <!-- Botão Continuar -->
        <div
            class="fixed bottom-0 left-0 w-full bg-white shadow-inner p-4 flex justify-center"
        >
            <button
                class="bg-accent text-dark font-semibold px-8 py-3 rounded-lg hover:bg-yellow-300 transition disabled:opacity-50 disabled:cursor-not-allowed"
                @click="criarReserva"
                :disabled="!podeReservar || loading"
            >
                {{ loading ? "Aguarde..." : "Continuar" }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axiosInstance from "../axios";

const router = useRouter();

// Variáveis reativas
const checkin = ref("");
const checkout = ref("");
const hospedes = ref(1);
const observacoes = ref("");
const alojamentoId = ref(1); // TODO: Buscar da rota ou props
const alojamento = ref({});
const precoNoite = computed(() => alojamento.value.preco_noite || 100);

// Estados
const errors = ref([]);
const successMessage = ref("");
const loading = ref(false);
const disponibilidadeChecada = ref(false);
const alojamentoDisponivel = ref(false);

// Data mínima (hoje)
const minDate = computed(() => {
    const hoje = new Date();
    return hoje.toISOString().split("T")[0];
});

// Calcular noites
const noites = computed(() => {
    if (!checkin.value || !checkout.value) return 0;
    const d1 = new Date(checkin.value);
    const d2 = new Date(checkout.value);
    const diff = (d2 - d1) / (1000 * 60 * 60 * 24);
    return diff > 0 ? diff : 0;
});

// Calcular total
const total = computed(() => noites.value * precoNoite.value);

// Verificar se pode fazer reserva
const podeReservar = computed(() => {
    return (
        checkin.value &&
        checkout.value &&
        noites.value > 0 &&
        alojamentoDisponivel.value
    );
});

// Buscar informações do alojamento
const buscarAlojamento = async () => {
    try {
        const response = await axiosInstance.get(
            `/alojamentos/${alojamentoId.value}`
        );
        alojamento.value = response.data;
    } catch (error) {
        console.error("Erro ao buscar alojamento:", error);
    }
};

// Verificar disponibilidade
const verificarDisponibilidade = async () => {
    if (!checkin.value || !checkout.value || noites.value <= 0) {
        disponibilidadeChecada.value = false;
        return;
    }

    try {
        loading.value = true;
        const response = await axiosInstance.post(
            `/reservas/available/${alojamentoId.value}`,
            {
                checkin: checkin.value,
                checkout: checkout.value,
            }
        );

        alojamentoDisponivel.value = response.data.available;
        disponibilidadeChecada.value = true;
    } catch (error) {
        console.error("Erro ao verificar disponibilidade:", error);
        alojamentoDisponivel.value = false;
        disponibilidadeChecada.value = true;
    } finally {
        loading.value = false;
    }
};

// Criar reserva
const criarReserva = async () => {
    errors.value = [];
    successMessage.value = "";
    loading.value = true;

    try {
        const response = await axiosInstance.post("/reservas", {
            alojamento_id: alojamentoId.value,
            checkin: checkin.value,
            checkout: checkout.value,
            hospedes: hospedes.value,
            observacoes: observacoes.value || null,
        });

        successMessage.value = "Reserva criada com sucesso!";

        // Redirecionar após 2 segundos
        setTimeout(() => {
            router.push({
                name: "minhas-reservas",
            });
        }, 2000);
    } catch (error) {
        if (error.response && error.response.data) {
            // Erros de validação do Laravel
            if (error.response.data.errors) {
                errors.value = Object.values(error.response.data.errors).flat();
            }
            // Erro customizado (conflito de datas)
            else if (error.response.data.error) {
                errors.value = [error.response.data.error];
            } else {
                errors.value = ["Erro ao criar reserva. Tente novamente."];
            }
        } else {
            errors.value = ["Erro de conexão. Verifique sua internet."];
        }
    } finally {
        loading.value = false;
    }
};

// Executar ao montar o componente
onMounted(() => {
    buscarAlojamento();
});
</script>

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
.bg-accent {
    background-color: #e6e019;
}
</style>
