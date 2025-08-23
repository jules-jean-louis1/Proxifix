import axios from 'axios';
import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';

const API_BASE_URL = process.env.EXPO_PUBLIC_API_URL;

const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Helper pour récupérer la session stockée
const getStoredSession = async (): Promise<string | null> => {
  if (Platform.OS === 'web') {
    try {
      return localStorage.getItem('session');
    } catch (e) {
      console.error('Local storage is unavailable:', e);
      return null;
    }
  } else {
    return await SecureStore.getItemAsync('session');
  }
};

// Helper pour supprimer la session stockée
const removeStoredSession = async (): Promise<void> => {
  if (Platform.OS === 'web') {
    try {
      localStorage.removeItem('session');
    } catch (e) {
      console.error('Local storage is unavailable:', e);
    }
  } else {
    await SecureStore.deleteItemAsync('session');
  }
};

// Ajout automatique du token à chaque requête
api.interceptors.request.use(async config => {
  const sessionStr = await getStoredSession();
  if (sessionStr) {
    try {
      const session = JSON.parse(sessionStr);
      if (session.token) {
        config.headers.Authorization = `Bearer ${session.token}`;
      }
    } catch (e) {
      console.error('Error parsing session:', e);
    }
  }
  return config;
});

// Intercepteur de réponse pour gérer les erreurs d'authentification
api.interceptors.response.use(
  response => {
    return response;
  },
  async error => {
    const originalRequest = error.config;

    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;

      try {
        // Récupérer la session pour obtenir le refresh token
        const sessionStr = await getStoredSession();
        if (!sessionStr) throw new Error('No session available');

        const session = JSON.parse(sessionStr);
        const refreshToken = session?.refreshToken;

        if (!refreshToken) throw new Error('No refresh token available');

        // Faire la requête de refresh
        const res = await axios.post(`${API_BASE_URL}/auth/refresh`, {
          refresh_token: refreshToken,
        });

        const { token, refresh_token } = res.data;
        const newSession = {
          token,
          refreshToken: refresh_token,
        };

        // Sauvegarder la nouvelle session
        const newSessionStr = JSON.stringify(newSession);
        if (Platform.OS === 'web') {
          localStorage.setItem('session', newSessionStr);
        } else {
          await SecureStore.setItemAsync('session', newSessionStr);
        }

        // Mettre à jour le header de la requête originale
        originalRequest.headers['Authorization'] = `Bearer ${token}`;

        // Relancer la requête originale
        return axios(originalRequest);
      } catch (refreshError) {
        // Si le refresh échoue, supprimer la session
        await removeStoredSession();
        return Promise.reject(refreshError);
      }
    }

    return Promise.reject(error);
  }
);

export const useApi = () => {
  return api;
};
