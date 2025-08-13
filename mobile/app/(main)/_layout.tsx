import { Stack } from "expo-router";
import { useSession } from "../context/ctx";
import { useEffect, useState } from "react";
import React from "react";
import { useSessionContext } from "../context/useSessionContext";

export default function MainLayout() {
  const { session, isLoading } = useSession();
  const sessionCtx = useSessionContext();
  const [isAdmin, setIsAdmin] = useState<boolean>(false);

  useEffect(() => {
    setIsAdmin(sessionCtx?.getIsAdmin() ?? false);
    console.log("Session context:", sessionCtx?.session);
  }, [sessionCtx]);

  if (isLoading) {
    return null;
  }

  if (!session) {
    return <Stack.Screen name="loginCustomer" />;
  }

  return (
    <Stack screenOptions={{ headerShown: false }}>
      <Stack.Screen name="customer" options={{ headerShown: false }} />
      <Stack.Screen name="admin" options={{ headerShown: false }} />
    </Stack>
  );
}