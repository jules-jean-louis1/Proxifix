import { useContext, createContext, type PropsWithChildren } from "react";
import { setStorageItemAsync, useStorageState } from "./useStorageState";

const AuthContext = createContext<{
  signIn: (email: string, password: string) => Promise<boolean>;
  signOut: () => void;
  session?: string | null;
  isLoading: boolean;
}>({
  signIn: async () => false,
  signOut: () => null,
  session: null,
  isLoading: false,
});

// This hook can be used to access the user info.
export function useSession() {
  const value = useContext(AuthContext);
  if (process.env.NODE_ENV !== "production") {
    if (!value) {
      throw new Error("useSession must be wrapped in a <SessionProvider />");
    }
  }

  return value;
}

export function SessionProvider({ children }: PropsWithChildren) {
  const [[isLoading, session], setSession] = useStorageState("session");

  return (
    <AuthContext.Provider
      value={{
        signIn: async (email: string, password: string) => {
          try {
            const apiUrl = process.env.EXPO_PUBLIC_API_URL;
            const resp = await fetch(`${apiUrl}/auth/login`, {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({ email, password }),
            });
            if (!resp.ok) {
              throw new Error("Login failed");
            }
            const data = await resp.json();
            const token = data.token;

            await setStorageItemAsync("userToken", token);
            setSession(token);
            return true;
          } catch (e) {
            return false;
          }
        },
        signOut: async () => {
          await setStorageItemAsync("userToken", null);
          setSession(null);
        },
        session,
        isLoading,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}
