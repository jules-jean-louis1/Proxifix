import { Redirect, Stack } from "expo-router";
import { useSession } from "../context/authContext";
import { useEffect, useState } from "react";
import React from "react";
import { useSessionContext } from "../context/useSessionContext";

export default function MainLayout() {
  const { session, isLoading } = useSession();
  const sessionCtx = useSessionContext();
  const [isAdmin, setIsAdmin] = useState<boolean>(false);

  useEffect(() => {
    setIsAdmin(sessionCtx?.getIsAdmin() ?? false);
  }, [sessionCtx]);

  if (isLoading) {
    return null;
  }

  if (!session) {
    return <Redirect href="/" />;
  }

  return (
    <Stack screenOptions={{ headerShown: false }}>
      {isAdmin ? (
        <Stack.Screen name="admin" options={{ headerShown: false }} />
      ) : (
        <Stack.Screen name="customer" options={{ headerShown: false }} />
      )}
    </Stack>
  );
}
