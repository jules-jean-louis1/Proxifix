import { Stack } from "expo-router";
import { PaperProvider } from "react-native-paper";
import { SessionProvider } from "./context/authContext";
import React from "react";

export default function RootLayout() {
  return (
    <SessionProvider>
      <PaperProvider>
        <Stack screenOptions={{ headerShown: false }}>
          <Stack.Screen name="(main)" />
          <Stack.Screen name="(auth)" />
        </Stack>
      </PaperProvider>
    </SessionProvider>
  );
}
