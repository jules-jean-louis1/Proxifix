import { useLocalSearchParams, useRouter } from "expo-router";
import { View, Text, StyleSheet, ScrollView } from "react-native";
import React, { useEffect, useState } from "react";
import { useApi } from "@/app/utils/useApi";
import { ToolBarCustomer } from "@/app/components/navigation/ToolBarCustomer";

export default function InterventionDetailPage() {
  const { id } = useLocalSearchParams();
  const [intervention, setIntervention] = useState<any>(null);
  const api = useApi();
  const router = useRouter();

  useEffect(() => {
    (async () => {
      try {
        const response = await api.get(`/intervention/${id}`);
        setIntervention(response.data);
      } catch (error) {
        console.error(
          "Error:",
          (error as any).response
            ? (error as any).response.data
            : (error as any).message
        );
      }
    })();
  }, [id]);

  if (!intervention) {
    return <Text style={styles.loading}>Chargement...</Text>;
  }

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer
        title="Détail de l'intervention"
        showBack={true}
        onBackPress={() => router.replace("/customer/interventions")}
        bottomBar
      />
      <ScrollView contentContainerStyle={styles.container}>
        <Text style={styles.title}>{intervention.title}</Text>
        <Text style={styles.label}>Description :</Text>
        <Text style={styles.value}>{intervention.description}</Text>

        <Text style={styles.label}>Entreprise :</Text>
        <Text style={styles.value}>{intervention.company?.name}</Text>

        <Text style={styles.label}>Type d'intervention :</Text>
        <Text style={styles.value}>{intervention.typeIntervention?.name}</Text>

        <Text style={styles.label}>Statut :</Text>
        <Text style={styles.value}>{intervention.status?.name}</Text>

        <Text style={styles.label}>Équipement :</Text>
        <Text style={styles.value}>{intervention.equipment?.name}</Text>

        <Text style={styles.label}>Date de début :</Text>
        <Text style={styles.value}>{intervention.start_date}</Text>

        <Text style={styles.label}>Date de fin :</Text>
        <Text style={styles.value}>{intervention.end_date}</Text>

        <Text style={styles.label}>Créée le :</Text>
        <Text style={styles.value}>{intervention.created_at}</Text>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { padding: 20 },
  title: { fontSize: 22, fontWeight: "bold", marginBottom: 16 },
  label: { fontWeight: "bold", marginTop: 12 },
  value: { marginLeft: 8, marginTop: 2 },
  loading: { marginTop: 40, textAlign: "center" },
});
