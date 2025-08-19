import React from "react";
import { View, StyleSheet } from "react-native";
import { Slot, Tabs, Redirect } from "expo-router";
import { Feather } from "@expo/vector-icons";
import { useSessionContext } from "../../context/useSessionContext";
import TabBarCustomer from "@/app/components/customer/navigation/TabBarCustomer";

export default function CustomerLayout() {
  const sessionCtx = useSessionContext();
  const isAdmin = sessionCtx?.getIsAdmin() ?? false;

  // Rediriger vers admin si c'est un admin
  if (isAdmin) {
    return <Redirect href="/admin" />;
  }

  return (
    <Tabs screenOptions={{ tabBarActiveTintColor: "#01358D", headerShown: false }} tabBar={(props) => <TabBarCustomer />}>
      <Tabs.Screen
        name="index"
        options={{
          headerShown: false,
          title: "Acceuil",
          tabBarIcon: ({ color }) => (
            <Feather name="home" size={16} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="interventions/index"
        options={{
          headerShown: false,
          title: "Interventions",
          tabBarIcon: ({ color }) => (
            <Feather name="list" size={16} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="equipments/index"
        options={{
          title: "Equipements",
          tabBarIcon: ({ color }) => (
            <Feather name="monitor" size={16} color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="profil/index"
        options={{
          title: "Profil",
          tabBarIcon: ({ color }) => (
            <Feather name="settings" size={16} color={color} />
          ),
        }}
      />
    </Tabs>
  );
}