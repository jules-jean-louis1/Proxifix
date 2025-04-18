import { Stack, useRouter } from "expo-router";
import { useSession } from "../context/ctx";
import { useEffect } from "react";
import React from "react";

function ProtectedLayout({ children }: { children: React.ReactNode }) {
  const router = useRouter();
  const { session, isLoading } = useSession();

  useEffect(() => {
    if (!isLoading && !session) {
      router.replace("../loginCustomer");
    }
  }, [isLoading, session]);

  if (isLoading) {
    // Afficher un écran de chargement pendant que la session est vérifiée
    return null;
  }

  return <>{children}</>;
}

export default function MainLayout() {
  return (
    <ProtectedLayout>
      <Stack screenOptions={{headerShown : false}} />
    </ProtectedLayout>
  );
}
