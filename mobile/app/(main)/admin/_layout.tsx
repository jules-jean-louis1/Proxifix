import { Stack, Redirect, Tabs } from 'expo-router';
import { useSessionContext } from '../../context/useSessionContext';
import React from 'react';
import TabBarAdmin from '@/app/components/admin/navigation/TabBarAdmin';

export default function AdminLayout() {
  const sessionCtx = useSessionContext();
  const isAdmin = sessionCtx?.getIsAdmin() ?? false;

  // Rediriger vers customer si pas admin
  if (!isAdmin) {
    return <Redirect href="/customer" />;
  }

  return (
    <Tabs
      screenOptions={{ tabBarActiveTintColor: '#01358D', headerShown: false }}
      tabBar={props => <TabBarAdmin />}
    >
      <Stack.Screen name="index" options={{ headerShown: false }} />
      <Stack.Screen name="interventions" options={{ headerShown: false }} />
      <Stack.Screen name="registerTech" options={{ headerShown: false }} />
      <Stack.Screen name="settings" options={{ headerShown: false }} />
    </Tabs>
  );
}
