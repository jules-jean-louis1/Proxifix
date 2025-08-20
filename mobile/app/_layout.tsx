import { Stack } from "expo-router";
import { PaperProvider } from "react-native-paper";
import { SessionProvider } from "./context/authContext";
import React from "react";

export default function Layout() {
  return (
    <SessionProvider>
      <PaperProvider>
        <Stack screenOptions={{ headerShown: false }}>
          {/* PUBLIC_ACCESS */}
          <Stack.Screen name="home" options={{ headerShown : false}} />
          <Stack.Screen name="login" options={{ headerShown: false}} />
          {/* LOGGED_ACCESS */}
          <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
        </Stack>
      </PaperProvider>
    </SessionProvider>
  );
}
