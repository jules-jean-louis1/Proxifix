import axios, { AxiosInstance } from "axios";
import { useSession } from "../context/ctx";

const apiUrl = process.env.EXPO_PUBLIC_API_URL || "https://api.example.com";

let currentToken: string | null = null;
let setSessionExternal: ((s: string) => void) | null = null;
let signOutExternal: (() => void) | null = null;

const api: AxiosInstance = axios.create({
  baseURL: apiUrl,
  headers: {
    "Content-Type": "application/json",
  },
});

// REQUEST INTERCEPTOR
api.interceptors.request.use(
  (config) => {
    if (currentToken) {
      config.headers.Authorization = `Bearer ${currentToken}`;
      // console.log("➡️ Using token:", currentToken);
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// RESPONSE INTERCEPTOR
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;

      try {
        // Use the latest session stored in context
        const sessionStr = setSessionExternal ? await getLatestSessionFromContext() : null;
        const parsed = sessionStr ? JSON.parse(sessionStr) : null;
        const refreshToken = parsed?.refreshToken;

        if (!refreshToken) throw new Error("No refresh token available");

        const res = await axios.post(`${apiUrl}/auth/refresh`, {
          refresh_token: refreshToken,
        });

        const { token, refresh_token } = res.data;
        const newSession = {
          token,
          refreshToken: refresh_token,
        };

        // Immediately update current token and axios headers
        currentToken = token;
        api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        originalRequest.headers["Authorization"] = `Bearer ${token}`;

        // Update context session
        if (setSessionExternal) {
          setSessionExternal(JSON.stringify(newSession));
        }

        return axios(originalRequest);
      } catch (refreshError) {
        if (signOutExternal) signOutExternal();
        return Promise.reject(refreshError);
      }
    }

    return Promise.reject(error);
  }
);

// Helper to safely fetch the latest session string from context
const getLatestSessionFromContext = async (): Promise<string | null> => {
  // This can be adapted for AsyncStorage if needed later
  // But right now we're just exposing session from context
  return new Promise((resolve) => {
    // Wait a tick to ensure context updated (just in case)
    setTimeout(() => {
      resolve(currentSessionFromContext);
    }, 0);
  });
};

let currentSessionFromContext: string | null = null;

export const useApi = () => {
  const { session, setSession, signOut } = useSession();

  // Update global access
  setSessionExternal = setSession;
  signOutExternal = signOut;
  currentSessionFromContext = session ?? null;

  const parsedSession = session ? JSON.parse(session) : null;
  if (parsedSession?.token && parsedSession.token !== currentToken) {
    console.log("🔄 Syncing token from useApi:", parsedSession.token);
    currentToken = parsedSession.token;
    api.defaults.headers.common["Authorization"] = `Bearer ${currentToken}`;
  }

  return api;
};
