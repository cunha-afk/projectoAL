// resources/js/axios.js
import axios from 'axios';

// Configuração do Axios
const axiosInstance = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8080/api', // A URL base da API (ajuste conforme necessário)
    headers: {
        'Content-Type': 'application/json',
    },
});

// Interceptador para adicionar o token de autenticação (caso haja)
axiosInstance.interceptors.request.use((config) => {
    const token = localStorage.getItem('token'); // Acessa o token armazenado no localStorage
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`; // Adiciona o token ao cabeçalho
    }
    return config;
}, (error) => {
    return Promise.reject(error); // Lida com erros
});

export default axiosInstance;
