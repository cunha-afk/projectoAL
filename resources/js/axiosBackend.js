// resources/js/axiosBackend.js
import axios from 'axios';

// URL do backend Laravel (porta onde o Laravel está a correr)
// Como o teu APP_URL é http://localhost:8080, usamos isso por default
const BACKEND_BASE_URL = import.meta.env.VITE_BACKEND_URL || 'http://localhost:8080';

const backend = axios.create({
    baseURL: BACKEND_BASE_URL, // vamos chamar /admin/... diretamente
    withCredentials: true,     // MUITO importante para enviar cookies da sessão
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

export default backend;