import CustomHomeButton from "@/app/components/Buttons/CustomHomeButton";
import { useSessionContext } from "@/app/context/useSessionContext";
import React, { useEffect, useState } from "react";
import { View, StyleSheet } from "react-native";
import { Text } from "react-native-paper";
import { useApi } from "@/app/utils/useApi";

export const CustomerHome = () => {
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [equipment, setEquipment] = useState<any>();
  const api = useApi();

  useEffect(() => {
    if (!sessionData) return;
    (async () => {
      try {
        const resp = await api.get(`/equipment/customer/${sessionData.id}`);
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
      <Text variant="titleLarge">
        Bonjour {sessionData?.first_name} {sessionData?.last_name}
      </Text>
      <Text variant="bodyMedium">
        Bienvenue sur votre compte client en ligne
      </Text>
      <CustomHomeButton
        title="Intervention"
        subTitle="Consulter mes interventions"
        icon="format-list-text"
      />
      <CustomHomeButton
        title="Adresse"
        subTitle="Mettre à jour mon adresse
postale"
        icon="home"
      />
      <View>
        <Text variant="titleLarge">Vos équipements informatique</Text>
        {equipment &&
          equipment.length > 0 &&
          equipment.map((e: any, index: any) => (
            <View key={index}>
              <Text>{e.name}</Text>
            </View>
          ))}
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    justifyContent: "center",
    alignItems: "center",
  },
});

export default CustomerHome;
