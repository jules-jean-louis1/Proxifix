import { AppButton } from "@/app/components/buttons/AppButton";
import { useSessionContext } from "@/app/context/useSessionContext";
import { useApi } from "@/app/utils/useApi";
import { router } from "expo-router";
import React, { useEffect, useState } from "react";
import { ScrollView, StyleSheet, View } from "react-native";
import { Text } from "react-native-paper";
import { AdminInterventionCard } from "@/app/components/admin/intervention/AdminInterventionCard";

export default function AdminHome() {
  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [isLoading, setIsLoading] = useState<boolean>(true); // Commencer à true
  const [interventions, setInterventions] = React.useState<any>([]);

  useEffect(() => {
    if (!sessionData) return;
    console.log("Session data:", sessionData);
    fetchInterventions();
  }, []);

  const fetchInterventions = async () => {
    if (!sessionData) {
      setIsLoading(false); // Pas de session = pas de chargement
      return;
    }

    try {
      let endpoint = "";

      if (sessionCtx?.isTechnician()) {
        // Pour les techniciens : récupérer les interventions assignées à eux
        endpoint = `/intervention?technician_id=${sessionData.id}&status=assigned,in_progress`;
      } else if (sessionCtx?.isAdmin()) {
        // Pour les admins : toutes les interventions de leur entreprise
        endpoint = `/intervention?company_id=${sessionData.company.id}`;
      }

      if (endpoint) {
        const resp = await api.get(endpoint);
        setInterventions(resp.data);
      }
    } catch (error) {
      console.error(
        "Erreur lors de la récupération des interventions :",
        error
      );
      setInterventions([]);
    } finally {
      setIsLoading(false); // Arrêter le chargement dans tous les cas
    }
  };

  // Afficher le chargement tant que les données ne sont pas prêtes
  if (isLoading || !sessionData) {
    return (
      <View style={styles.loadingContainer}>
        <Text style={styles.loadingText}>Chargement...</Text>
      </View>
    );
  }

  // Interface pour les techniciens
  if (sessionCtx?.isTechnician()) {
    return (
      <View style={styles.containerWrapper}>
        <ScrollView style={styles.container}>
          <Text variant="titleLarge" style={styles.title}>
            Bonjour {sessionData?.first_name} {sessionData?.last_name}
          </Text>
          <Text style={styles.subtitle}>
            Vos interventions assignées ({interventions.length})
          </Text>

          {interventions.length > 0 ? (
            interventions.map((intervention: any) => (
              <AdminInterventionCard
                key={intervention.id}
                intervention={intervention}
                showTechnician={false}
                onPress={() => router.push("/admin/interventions")}
              />
            ))
          ) : (
            <Text style={styles.noDataText}>Aucune intervention assignée</Text>
          )}

          <AppButton
            type="primary"
            children="Voir toutes mes interventions"
            onPress={() => router.push("/admin/interventions")}
            style={styles.button}
          />
        </ScrollView>
      </View>
    );
  }

  // Interface pour les admins
  if (sessionCtx?.isAdmin()) {
    return (
      <View style={styles.containerWrapper}>
        <ScrollView style={styles.container}>
          <Text variant="titleLarge" style={styles.title}>
            Bonjour {sessionData?.first_name} {sessionData?.last_name}
          </Text>
          <Text style={styles.subtitle}>
            Dashboard Admin - {sessionData?.company?.name}
          </Text>
          <View style={styles.buttonContainer}>
            <AppButton
              type="primary"
              children="Gérer les clients"
              onPress={() => router.push("/admin/customers")}
            />
            <AppButton
              type="primary"
              children="Gérer les interventions"
              onPress={() => router.push("/admin/interventions")}
            />
            <AppButton
              type="primary"
              children="Ajouter une intervention"
              onPress={() => router.push("/admin/interventions/new")}
            />
            <AppButton
              type="primary"
              children="Ajouter un technicien"
              onPress={() => router.push("/admin/technicians/new")}
            />
          </View>
          <Text variant="titleMedium" style={styles.sectionTitle}>
            Interventions récentes ({interventions.length})
          </Text>
          {interventions.slice(0, 5).map((intervention: any) => (
            <AdminInterventionCard
              key={intervention.id}
              intervention={intervention}
              showTechnician={true}
              onPress={() => router.push("/admin/interventions")}
            />
          ))}
        </ScrollView>
      </View>
    );
  }

  // Fallback (ne devrait pas arriver)
  return (
    <View style={styles.container}>
      <Text>Rôle non reconnu</Text>
    </View>
  );
}
const styles = StyleSheet.create({
  containerWrapper: {
    flex: 1,
    backgroundColor: "#F9F9F9",
    paddingBottom: 40,
  },
  container: {
    flex: 1,
    backgroundColor: "#F9F9F9",
    padding: 16,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    backgroundColor: "#F9F9F9",
  },
  loadingText: {
    fontSize: 16,
    color: "#01358D",
  },
  title: {
    marginBottom: 8,
    color: "#01358D",
  },
  subtitle: {
    fontSize: 16,
    color: "#666",
    marginBottom: 16,
  },
  sectionTitle: {
    marginTop: 24,
    marginBottom: 16,
    color: "#01358D",
  },
  buttonContainer: {
    gap: 4,
  },
  button: {
    marginBottom: 12,
  },
  noDataText: {
    textAlign: "center",
    color: "#666",
    fontSize: 16,
    marginTop: 32,
    fontStyle: "italic",
  },
});
