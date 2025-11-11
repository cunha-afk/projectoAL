import { createRouter, createWebHistory } from 'vue-router';

// Importando os componentes de página
import Home from './Pages/Home.vue';
import Alojamentos from './Pages/Alojamentos.vue';
import AlojamentoDetalhes from './Pages/AlojamentoDetalhes.vue';

const routes = [
  { path: '/', component: Home },
  { path: '/alojamentos', component: Alojamentos },
  { path: '/alojamento/:id', component: AlojamentoDetalhes },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;