import { Stack, Redirect } from "expo-router";
import { useSessionContext } from "../../context/useSessionContext";
import React from "react";

export default function AdminLayout() {
  const sessionCtx = useSessionContext();
  const isAdmin = sessionCtx?.getIsAdmin() ?? false;

  // Rediriger vers customer si pas admin
  if (!isAdmin) {
    return <Redirect href="/customer" />;
  }

  return (
    <Stack screenOptions={{ headerShown: false }}>
      <Stack.Screen name="index" options={{ headerShown: false }} />
      <Stack.Screen name="interventions" options={{ headerShown: false }} />
      <Stack.Screen name="registerTech" options={{ headerShown: false }} />
    </Stack>
  );
}
