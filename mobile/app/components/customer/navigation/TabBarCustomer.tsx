import { View, TouchableOpacity, StyleSheet, Text } from "react-native";
import { useRouter, usePathname } from "expo-router";
import { Feather } from "@expo/vector-icons";
import React from "react";

const TabBarCustomer: React.FC = () => {
  const router = useRouter();
  const pathname = usePathname();

  const isActive = (route: any) => pathname === route;
  const isEquipmentsRoute = () => {
    return (
      pathname === "/customer/equipments" ||
      pathname.startsWith("/customer/equipment/")
    );
  };
  const isInterventionsRoute = () => {
    return (
      pathname === "/customer/interventions" ||
      pathname.startsWith("/customer/intervention/")
    );
  };

  return (
    <View style={styles.tabBar}>
      <TouchableOpacity
        style={styles.tab}
        onPress={() => router.push("/customer")}
      >
        <Feather
          name="home"
          size={18}
          color={isActive("/customer") ? "#01358D" : "#999"}
        />
        <Text
          style={{
            color: isActive("/customer") ? "#01358D" : "#999",
            fontSize: 12,
            marginTop: 2,
          }}
        >
          Accueil
        </Text>
      </TouchableOpacity>

      <TouchableOpacity
        style={styles.tab}
        onPress={() => router.push("/customer/interventions")}
      >
        <Feather
          name="list"
          size={18}
          color={isInterventionsRoute() ? "#01358D" : "#999"}
        />
        <Text
          style={{
            color: isInterventionsRoute() ? "#01358D" : "#999",
            fontSize: 12,
            marginTop: 2,
          }}
        >
          Interventions
        </Text>
      </TouchableOpacity>

      <TouchableOpacity
        style={styles.tab}
        onPress={() => router.push("/customer/equipments")}
      >
        <Feather
          name="monitor"
          size={18}
          color={isEquipmentsRoute() ? "#01358D" : "#999"}
        />
        <Text
          style={{
            color: isEquipmentsRoute() ? "#01358D" : "#999",
            fontSize: 12,
            marginTop: 2,
          }}
        >
          Equipements
        </Text>
      </TouchableOpacity>

      <TouchableOpacity
        style={styles.tab}
        onPress={() => router.push("/customer/settings")}
      >
        <Feather
          name="settings"
          size={18}
          color={isActive("/customer/settings") ? "#01358D" : "#999"}
        />
        <Text
          style={{
            color: isActive("/customer/settings") ? "#01358D" : "#999",
            fontSize: 12,
            marginTop: 2,
          }}
        >
          Profil
        </Text>
      </TouchableOpacity>
    </View>
  );
};

export default TabBarCustomer;

const styles = StyleSheet.create({
  tabBar: {
    flexDirection: "row",
    borderTopWidth: 1,
    borderTopColor: "#eee",
    backgroundColor: "#fff",
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
});
