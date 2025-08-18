import { View, StyleSheet, TouchableOpacity, Text } from "react-native";
import React, { useMemo } from "react";
import { usePathname, useRouter } from "expo-router";
import { Feather } from "@expo/vector-icons";

const TabBarAdmin = () => {
  const router = useRouter();
  const pathname = usePathname();

  const isActive = (route: string) => {
    // Correspondance exacte d'abord
    if (pathname === route) return true;
    
    // Pour les routes avec sous-pages, vérifier startsWith mais pas pour la home
    if (route === '/admin') return false; // Home ne peut être active que si correspondance exacte
    
    // Pour les autres, vérifier si c'est une sous-page
    return pathname?.startsWith(route + '/') || false;
  };

  // Configuration des onglets pour éviter la répétition
  const tabs = useMemo(() => [
    {
      id: 'home',
      route: '/admin',
      icon: 'home' as const,
      label: 'Accueil'
    },
    {
      id: 'interventions',
      route: '/admin/interventions',
      icon: 'list' as const,
      label: 'Interventions'
    },
    {
      id: 'customers',
      route: '/admin/customers',
      icon: 'users' as const,
      label: 'Clients'
    },
    {
      id: 'settings',
      route: '/admin/settings',
      icon: 'settings' as const,
      label: 'Paramètres'
    }
  ], []);

  return (
    <View style={styles.tabBar}>
      {tabs.map((tab) => {
        const active = isActive(tab.route);
        const color = active ? "#01358D" : "#999";
        
        return (
          <TouchableOpacity
            key={tab.id}
            style={styles.tab}
            onPress={() => router.push(tab.route as any)}
            accessibilityLabel={tab.label}
            accessibilityRole="tab"
            accessibilityState={{ selected: active }}
          >
            <Feather name={tab.icon} size={18} color={color} />
            <Text style={[styles.tabText, { color }]}>
              {tab.label}
            </Text>
          </TouchableOpacity>
        );
      })}
    </View>
  );
};

export default TabBarAdmin;

// Constantes pour éviter la répétition
const COLORS = {
  ACTIVE: "#01358D",
  INACTIVE: "#999",
  BORDER: "#eee",
  BACKGROUND: "#fff"
} as const;

const styles = StyleSheet.create({
  tabBar: {
    flexDirection: "row",
    borderTopWidth: 1,
    borderTopColor: COLORS.BORDER,
    backgroundColor: COLORS.BACKGROUND,
    height: 60,
    position: "absolute",
    bottom: 0,
    left: 0,
    right: 0,
  },
  tab: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  tabText: {
    fontSize: 12,
    marginTop: 2,
  },
});
