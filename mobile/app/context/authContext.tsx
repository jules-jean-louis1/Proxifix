import React, {
  useContext,
  createContext,
  type PropsWithChildren,
} from 'react';
import { setStorageItemAsync, useStorageState } from './useStorageState';
import { router } from 'expo-router';

const AuthContext = createContext<{
  signIn: (email: string, password: string) => Promise<boolean>;
  signOut: () => void;
  session?: string | null;
  isLoading: boolean;
  setSession: (session: string | null) => void;
}>({
  signIn: async () => false,
  signOut: () => null,
  session: null,
  isLoading: false,
  setSession: () => null,
});

// This hook can be used to access the user info.
export function useSession() {
  return useContext(AuthContext);
}

export function SessionProvider({ children }: PropsWithChildren) {
  const [[isLoading, session], setSession] = useStorageState('session');

  return (
    <AuthContext.Provider
      value={{
        signIn: async (email: string, password: string) => {
          try {
            const apiUrl = process.env.EXPO_PUBLIC_API_URL;
            const resp = await fetch(`${apiUrl}/auth/login`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({ email, password }),
            });
            console.log(resp);
            if (!resp.ok) {
              throw new Error('Login failed');
            }
            const data = await resp.json();
            const sessionData = JSON.stringify({
              token: data.token,
              refreshToken: data.refresh_token,
            });

            setSession(sessionData);
            return true;
          } catch (e) {
            // Handle the error appropriately (e.g., log to an external service)
            // console.error('Error during signIn:', e);
            return false;
          }
        },
        signOut: async () => {
          setSession(null);
          router.replace('/');
        },
        setSession: async (session: string | null) => {
          if (session) {
            await setStorageItemAsync('session', session);
          } else {
            await setStorageItemAsync('session', null);
          }
        },
        session,
        isLoading,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}
