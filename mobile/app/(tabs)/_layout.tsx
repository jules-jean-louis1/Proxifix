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
  }, [sessionCtx]);

  if (isLoading) {
    return null;
  }

  if (!session) {
    return <Stack.Screen name="loginCustomer" />;
  }

  return (
    <Stack>
      {!isAdmin && <Stack.Screen name="customer" />}

      {isAdmin && <Stack.Screen name="admin" />}
    </Stack>
  );
}