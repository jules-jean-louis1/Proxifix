import { useSessionContext } from "@/app/context/useSessionContext";
import React, { useEffect, useState } from "react";
import { View, StyleSheet, Image, Pressable } from "react-native";
import { Button, Text } from "react-native-paper";
import { useApi } from "@/app/utils/useApi";
import { Feather } from "@expo/vector-icons";
import { EquipmentCardHome } from "@/app/components/equipment/EquipmentCardHome";
import { router } from "expo-router";
import { ToolBarCustomer } from "@/app/components/navigation/ToolBarCustomer";

export const CustomerHome = () => {
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [equipment, setEquipment] = useState<any>();
  const api = useApi();

  useEffect(() => {
    if (!sessionData) return;
    (async () => {
      try {
        const resp = await api.get(`/equipment?user_id=${sessionData.id}`);
        setEquipment(resp.data);
      } catch (error) {
        console.error(
          "Erreur lors de la récupération des équipements :",
          error
        );
      }
    })();
  }, []);

  return (
    <View style={styles.container}>
      <ToolBarCustomer />
      <View
        style={{
          marginTop: 5,
          justifyContent: "flex-start",
          alignItems: "flex-start",
          width: "90%",
        }}
      >
        <Text variant="titleLarge" style={styles.textTitle}>
          Bonjour {sessionData?.first_name} {sessionData?.last_name}
        </Text>
        <Text variant="bodyMedium" style={styles.textSubTitle}>
          Bienvenue sur votre compte client en ligne
        </Text>
      </View>
      <View style={styles.interventionBlock}>
        <Pressable style={styles.button} onPress={() => router.push("/customer/interventions")}>
          <Image
            source={require("../../assets/images/tool_test 1.png")}
            style={styles.icon}
          />
          <View style={styles.textContainer}>
            <Text style={styles.title}>INTERVENTIONS</Text>
            <View style={styles.row}>
              <Text style={styles.subTitle}>
                Ajouter ou consulter mes interventions
              </Text>
              <Feather name="chevron-right" size={20} color="#fff" />
            </View>
          </View>
        </Pressable>
        <View style={styles.rowButtons}>
          <Button
            mode="outlined"
            icon={() => <Feather name="calendar" size={20} color="#000" />}
            onPress={() => {}}
            style={styles.buttonUnder}
            labelStyle={styles.label}
            contentStyle={styles.contentUnder}
          >
            Calendrier
          </Button>
          <View style={styles.verticalDivider} />
          <Button
            mode="outlined"
            icon={() => <Feather name="clock" size={20} color="#000" />}
            onPress={() => {}}
            style={styles.buttonUnder}
            labelStyle={styles.label}
            contentStyle={styles.contentUnder}
          >
            Historique
          </Button>
        </View>
      </View>
      <View style={styles.rowButtonsSide}>
        <Pressable style={styles.buttonSide} onPress={() => router.push("/customer/equipments")}>
          <Image
            source={require("../../assets/images/equipment.png")}
            style={styles.iconSide}
          />
          <Text variant="titleLarge" style={styles.titleSide}>
            EQUIPEMENTS
          </Text>
        </Pressable>
        <Pressable style={styles.buttonSide} onPress={() => router.push("/customer/profil")}>
          <Image
            source={require("../../assets/images/settings.png")}
            style={styles.iconSide}
          />
          <Text variant="titleLarge" style={styles.titleSide}>
            PROFIL
          </Text>
        </Pressable>
      </View>
      <EquipmentCardHome equipment={equipment} />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    backgroundColor: "#FFFFFF"
  },
  text: {
    color: "#000",
  },
  textTitle: {
    color: "#364A63",
    fontSize: 25,
    fontWeight: "bold",
    marginBottom: 10,
  },
  textSubTitle: {
    color: "#364A63",
    fontSize: 16,
    marginBottom: 20,
    fontWeight: "light",
  },
  button: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#283276",
    borderRadius: 20,
    padding: 16,
    width: "100%",
    alignSelf: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 4,
    elevation: 5,
  },
  buttonSide: {
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#F5F5F8",
    borderRadius: 20,
    padding: 16,
    width: "50%",
  },
  icon: {
    width: 60,
    height: 60,
    resizeMode: "contain",
    marginRight: 12,
  },
  iconSide: {
    width: 80,
    height: 72,
    resizeMode: "contain",
    marginRight: 12,
  },
  textContainer: {
    flex: 1,
  },
  titleSide: {
    color: "#637381",
    fontWeight: "bold",
    fontSize: 18,
    textTransform: "uppercase",
    marginBottom: 4,
  },
  title: {
    color: "#fff",
    fontWeight: "bold",
    fontSize: 18,
    textTransform: "uppercase",
    marginBottom: 4,
  },
  subTitle: {
    color: "#fff",
    fontSize: 14,
    flex: 1,
  },
  row: {
    flexDirection: "row",
    alignItems: "center",
  },
  rowButtonsSide: {
    flexDirection: "row",
    justifyContent: "space-around",
    width: "90%",
    alignSelf: "center",
    alignItems: "center",
    gap: 10,
    marginBottom: 20,
  },
  rowButtons: {
    flexDirection: "row",
    justifyContent: "space-around",
    width: "100%",
    alignSelf: "center",
    alignItems: "center",
  },
  label: {
    color: "#000",
    fontSize: 14,
  },
  contentUnder: {
    flexDirection: "row-reverse",
    height: 48,
  },
  interventionBlock: {
    width: "90%",
    alignSelf: "center",
    borderRadius: 20,
    overflow: "hidden",
    backgroundColor: "#F5F5F8",
    height: "auto",
    marginBottom: 20,
  },
  buttonUnder: {
    flex: 1,
    backgroundColor: "transparent",
    borderWidth: 0,
    elevation: 0,
  },
  noTopBorder: {
    borderTopWidth: 0,
  },
  lastButton: {
    borderBottomLeftRadius: 20,
    borderBottomRightRadius: 20,
  },
  verticalDivider: {
    width: 1,
    height: "80%",
    backgroundColor: "#000",
    marginHorizontal: 8,
  },
});

export default CustomerHome;
