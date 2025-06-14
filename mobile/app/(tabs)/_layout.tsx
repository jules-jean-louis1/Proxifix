import { Stack, useRouter } from "expo-router";
import { useSession } from "../context/ctx";
import { useEffect, useState } from "react";
import React from "react";
import { useSessionContext } from "../context/useSessionContext";

export default function MainLayout() {
  const router = useRouter();
  const { session, isLoading } = useSession();
  const sessionCtx = useSessionContext();
  const [isAdmin, setIsAdmin] = useState<boolean>(false);

  useEffect(() => {
    if (!isLoading && !session) {
      router.replace("../loginCustomer");
      return;
    }
    setIsAdmin(sessionCtx?.getIsAdmin() ?? false);
  }, [isLoading, session]);

  if (isLoading) {
    // Afficher un écran de chargement pendant que la session est vérifiée
    return null;
  }
  return (
    <Stack>
      <Stack.Protected guard={!isAdmin}>
        <Stack.Screen name="customer" />
      </Stack.Protected>

      <Stack.Protected guard={isAdmin}>
        <Stack.Screen name="admin" />
      </Stack.Protected>
    </Stack>
  );
}
