import axios, { AxiosInstance } from "axios";
import { useSession } from "../context/ctx";

const apiUrl = process.env.EXPO_PUBLIC_API_URL || "https://api.example.com";

let currentToken: string | null = null;

export const useApi = () => {
  const { session, setSession, signOut } = useSession();
  const parsedSession = session ? JSON.parse(session) : null;

  if (parsedSession?.token && parsedSession.token !== currentToken) {
    console.log("Updating current token", parsedSession.token);
    currentToken = parsedSession.token;
  }

  const api: AxiosInstance = axios.create({
    baseURL: apiUrl,
    headers: {
      "Content-Type": "application/json",
    },
  });

  api.interceptors.request.use(
    (config) => {
      console.log("Request interceptor", currentToken);
      if (currentToken) {
        config.headers.Authorization = `Bearer ${currentToken}`;
      }
      return config;
    },
    (error) => Promise.reject(error)
  );

  api.interceptors.response.use(
    (response) => response,
    async (error) => {
      const originalRequest = error.config;

      if (error.response?.status === 401 && !originalRequest._retry) {
        originalRequest._retry = true;

        try {
          const parsed = session ? JSON.parse(session) : null;
          const refreshToken = parsed?.refreshToken;
          if (!refreshToken) {
            throw new Error("No refresh token available");
          }

          const response = await axios.post(`${apiUrl}/auth/refresh`, {
            refresh_token: refreshToken,
          });

          const { token, refresh_token } = response.data;

          const newSession = {
            token,
            refreshToken: refresh_token,
          };

          currentToken = token;
          setSession(JSON.stringify(newSession));

          console.log("New token", token);
          originalRequest.headers["Authorization"] = `Bearer ${token}`;
          return axios(originalRequest);
        } catch (refreshError) {
          signOut();
          return Promise.reject(refreshError);
        }
      }

      return Promise.reject(error);
    }
  );

  return api;
};
