import { AppButton } from "@/app/components/buttons/AppButton";
import { useSessionContext } from "@/app/context/useSessionContext";
import { useApi } from "@/app/utils/useApi";
import { router } from "expo-router";
import React, { useEffect, useState } from "react";
import { ScrollView, StyleSheet, View } from "react-native";
import { Text } from "react-native-paper";

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
      <View style={{ flex: 1 }}>
        <ScrollView style={styles.container}>
          <Text variant="titleLarge" style={styles.title}>
            Bonjour {sessionData?.first_name} {sessionData?.last_name}
          </Text>
          <Text style={styles.subtitle}>
            Vos interventions assignées ({interventions.length})
          </Text>

          {interventions.length > 0 ? (
            interventions.map((intervention: any) => (
              <View key={intervention.id} style={styles.interventionCard}>
                <Text variant="titleMedium">{intervention.title}</Text>
                <Text variant="bodyMedium">Status: {intervention.status}</Text>
                <Text variant="bodySmall">
                  Client: {intervention.customer?.first_name}{" "}
                  {intervention.customer?.last_name}
                </Text>
                <Text variant="bodySmall">
                  Date: {new Date(intervention.start_date).toLocaleDateString()}
                </Text>
              </View>
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
      <View style={{ flex: 1 }}>
        <ScrollView style={styles.container}>
          <Text variant="titleLarge" style={styles.title}>
            Bonjour {sessionData?.first_name} {sessionData?.last_name}
          </Text>
          <Text style={styles.subtitle}>
            Dashboard Admin - {sessionData?.company?.name}
          </Text>

          <Text variant="titleMedium" style={styles.sectionTitle}>
            Interventions récentes ({interventions.length})
          </Text>

          {interventions.slice(0, 5).map((intervention: any) => (
            <View key={intervention.id} style={styles.interventionCard}>
              <Text variant="titleMedium">{intervention.title}</Text>
              <Text variant="bodyMedium">Status: {intervention.status}</Text>
              <Text variant="bodySmall">
                Technicien:{" "}
                {intervention.technician?.first_name || "Non assigné"}
              </Text>
              <Text variant="bodySmall">
                Client: {intervention.customer?.first_name}{" "}
                {intervention.customer?.last_name}
              </Text>
            </View>
          ))}

          <View style={styles.buttonContainer}>
            <AppButton
                type="primary"
                children="Gérer les clients"
                onPress={() => router.push("/admin/customers")}
                style={styles.button}
            />
            <AppButton
              type="primary"
              children="Gérer les interventions"
              onPress={() => router.push("/admin/interventions")}
              style={styles.button}
            />
            <AppButton
              type="primary"
              children="Ajouter une intervention"
              onPress={() => router.push("/admin/interventions/new")}
              style={styles.button}
            />
            <AppButton
              type="primary"
              children="Ajouter un technicien"
              onPress={() => router.push("/admin/registerTech")}
              style={styles.button}
            />
          </View>
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
  interventionCard: {
    backgroundColor: "#FFFFFF",
    padding: 16,
    borderRadius: 8,
    marginBottom: 12,
    shadowColor: "#000",
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.1,
    shadowRadius: 3.84,
    elevation: 5,
  },
  buttonContainer: {
    marginTop: 24,
    gap: 12,
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
