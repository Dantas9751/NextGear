import axios from 'axios';

// 1. Define o endereço base da sua API Laravel
// Por exemplo, se seu Laravel está rodando em http://127.0.0.1:8000
const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api', // Lembre-se do '/api' padrão do Laravel
  headers: {
    // Configura o tipo de conteúdo para JSON
    'Content-Type': 'application/json',
  },
  // 2. Importante para sessões/cookies (se estiver usando Laravel Sanctum)
  withCredentials: true, 
});

export default api;