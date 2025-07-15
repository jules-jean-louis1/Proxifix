import { AppButton } from "@/app/components/buttons/AppButton";
import { useSessionContext } from "@/app/context/useSessionContext";
import { useApi } from "@/app/utils/useApi";
import { router } from "expo-router";
import React, { useEffect } from "react";
import { View } from "react-native";
import { Text } from "react-native-paper";

export default function AdminHome() {
  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [interventions, setInterventions] = React.useState<any>([]);

  useEffect(() => {
    if (!sessionData) return;
    (async () => {
      try {
        const resp = await api.get(`/intervention?status=pending&company_id=${sessionData.company.id}`);
        setInterventions(resp.data);
      } catch (error) {
        console.error("Erreur lors de la récupération des interventions :", error);
      }
    })();
  },[])

  return <View>
    <View>
        <Text variant="titleLarge">
          Bonjour {sessionData?.first_name} {sessionData?.last_name}
        </Text>
        <Text>Bienvenue sur votre panel.</Text>
        <AppButton
          type="primary"
          children="Ajouter un utilisateur"
          onPress={() => {
            // Logic to add a new user
          }}
        />
        <AppButton
          type="primary"
          children="Ajouter une intervention"
          onPress={() => router.push("/admin/interventions/new")}
        />
        <AppButton
          type="primary"
          children="Ajouter un équipements"
          onPress={() => {
            // Logic to manage equipment
          }}
        /> 
    </View>
    {/* Add more admin functionalities here */}
  </View>;
}
