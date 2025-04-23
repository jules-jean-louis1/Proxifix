import React from "react";
import { View, StyleSheet } from "react-native";
import { Slot, Tabs } from "expo-router";
import { Feather } from "@expo/vector-icons";
import TabBar from "@/app/components/navigation/TabBar";

export default function CustomerLayout() {
  return (
    <Tabs screenOptions={{ tabBarActiveTintColor: "#01358D" }} tabBar={(props) => <TabBar />}>
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