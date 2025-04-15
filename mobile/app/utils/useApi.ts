import axios, { AxiosInstance } from "axios";
import { useSession } from "../context/ctx";

const apiUrl = process.env.EXPO_PUBLIC_API_URL || "https://api.example.com";

export const useApi = () => {
  const { session, setSession, signOut } = useSession();
  const parsedSession = session ? JSON.parse(session) : null;
  console.log("sessionCtx");
  // Create axios instance
  const api: AxiosInstance = axios.create({
    baseURL: apiUrl,
    headers: {
      "Content-Type": "application/json",
      ...(parsedSession?.token && {
        Authorization: `Bearer ${parsedSession.token}`,
      }),
    },
  });

    api.interceptors.response.use(
      (response) => { return response},
      async (error) => {
        const originalRequest = error.config;
        console.log("error");

        // If error is 401 and we haven't tried to refresh yet
        if (error.response.status === 401 && !originalRequest._retry) {
          originalRequest._retry = true;
          // Attempt to refresh token
          try {
            if (!parsedSession?.refreshToken) {
              throw new Error("No refresh token available");
            }

            const response = await axios.post(`${apiUrl}/auth/refresh`, {
              refresh_token: parsedSession.refreshToken,
            });

            const { token, refresh_token } = response.data;
            const newSession = {
              token,
              refreshToken: refresh_token,
            };

            // Update session
            setSession(JSON.stringify(newSession));

            // Retry original request with new token
            originalRequest.headers["Authorization"] = `Bearer ${token}`;
            return axios(originalRequest);
          } catch (refreshError) {
            // Clear session on refresh failure
            signOut();
            return Promise.reject(refreshError);
          }
        }
        return Promise.reject(error);
      }
    );

  return api;
};
