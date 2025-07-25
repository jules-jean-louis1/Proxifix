import { useApi } from "@/app/utils/useApi";
import { useLocalSearchParams, useRouter } from "expo-router";
import React, { useEffect, useState } from "react";
import { View, Text, StyleSheet, ScrollView } from "react-native";
import { Feather } from "@expo/vector-icons";
import { EquipmentModalForm } from "@/app/components/equipment/EquipmentModalForm";
import { ToolBarCustomer } from "@/app/components/navigation/ToolBarCustomer";

export default function EquipmentDetails() {
  const { id } = useLocalSearchParams();
  const [equipment, setEquipment] = useState<any>(null);
  const [typeEquipment, setTypeEquipment] = useState<any[]>([]);
  const [brands, setBrands] = useState<any[]>([]);
  const [os, setOs] = useState<any>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const api = useApi();
  const router = useRouter();

  useEffect(() => {
    (async () => {
      try {
        const response = await api.get(`/equipment/${id}`);
        setEquipment(response.data);
        const typeEquipmentResponse = await api.get("/type-equipment");
        setTypeEquipment(typeEquipmentResponse.data);
        const brandsResponse = await api.get("/brand");
        setBrands(brandsResponse.data);
        const osResponse = await api.get("/operating-system");
        setOs(osResponse.data);
      } catch (error) {
        console.error(
          "Error:",
          (error as any).response
            ? (error as any).response.data
            : (error as any).message
        );
      } finally {
        setLoading(false);
      }
    })();
  }, [id]);

  if (loading) {
    return (
      <View style={styles.centered}>
        <Text style={styles.loadingText}>Chargement...</Text>
      </View>
    );
  }

  if (!equipment) {
    return (
      <View style={styles.centered}>
        <Text style={styles.errorText}>Aucun équipement trouvé</Text>
      </View>
    );
  }

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer
        title="Détails de l'équipement"
        showBack={true}
        onBackPress={() => router.replace("/customer/equipments")}
        bottomBar
      />
      <ScrollView style={styles.container}>
        <View style={styles.section}>
          <Text style={styles.title}>Détails de l'équipement</Text>
          <View style={styles.row}>
            <Feather name="monitor" size={20} color="#01358D" />
            <Text style={styles.infoText}>{equipment.name}</Text>
          </View>
          <View style={styles.row}>
            <Feather name="tag" size={20} color="#01358D" />
            <Text style={styles.infoText}>
              {equipment.type_equipment?.name}
            </Text>
          </View>
          <View style={styles.row}>
            <Feather name="cpu" size={20} color="#01358D" />
            <Text style={styles.infoText}>
              {equipment.operating_system?.name}
            </Text>
          </View>
          <View style={styles.row}>
            <Feather name="box" size={20} color="#01358D" />
            <Text style={styles.infoText}>{equipment.brand?.name}</Text>
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.subtitle}>Interventions</Text>
          {equipment.interventions.length > 0 ? (
            equipment.interventions.map((intervention: any) => (
              <View key={intervention.id} style={styles.card}>
                <Text style={styles.cardTitle}>{intervention.title}</Text>
                <Text style={styles.cardText}>
                  {intervention.typeIntervention?.name}
                </Text>
                <Text style={styles.cardText}>
                  Début : {new Date(intervention.start_date).toLocaleString()}
                </Text>
                <Text style={styles.cardText}>
                  Fin : {new Date(intervention.end_date).toLocaleString()}
                </Text>
                <Text style={styles.cardText}>
                  Statut : {intervention.status?.name}
                </Text>
              </View>
            ))
          ) : (
            <Text style={styles.infoText}>Aucune intervention trouvée.</Text>
          )}
        </View>

        <View style={styles.section}>
          <Text style={styles.subtitle}>Demandes de rendez-vous</Text>
          {equipment.appointmentRequests.length > 0 ? (
            equipment.appointmentRequests.map((request: any) => (
              <View key={request.id} style={styles.card}>
                <Text style={styles.cardTitle}>{request.title}</Text>
                <Text style={styles.cardText}>{request.description}</Text>
                <Text style={styles.cardText}>
                  Statut :{" "}
                  {request.status === "accepted" ? "Accepté" : "En attente"}
                </Text>
              </View>
            ))
          ) : (
            <Text style={styles.infoText}>
              Aucune demande de rendez-vous trouvée.
            </Text>
          )}
        </View>
        {/* Actions */}
        <EquipmentModalForm
          type="update"
          equipment={equipment}
          brands={brands}
          typeEquipment={typeEquipment}
          os={os}
        />
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#F9F9F9",
    padding: 16,
  },
  centered: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  loadingText: {
    fontSize: 16,
    color: "#01358D",
  },
  errorText: {
    fontSize: 16,
    color: "#B00020",
  },
  section: {
    marginBottom: 24,
  },
  title: {
    fontSize: 24,
    fontWeight: "bold",
    color: "#01358D",
    marginBottom: 16,
  },
  subtitle: {
    fontSize: 20,
    fontWeight: "600",
    color: "#01358D",
    marginBottom: 12,
  },
  row: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 8,
  },
  infoText: {
    fontSize: 16,
    marginLeft: 8,
    color: "#364A63",
  },
  card: {
    backgroundColor: "#FFFFFF",
    borderRadius: 8,
    padding: 12,
    marginBottom: 12,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  cardTitle: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#01358D",
    marginBottom: 4,
  },
  cardText: {
    fontSize: 14,
    color: "#364A63",
    marginBottom: 4,
  },
});
