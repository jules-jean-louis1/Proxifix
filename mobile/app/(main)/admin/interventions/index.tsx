import { AdminInterventionCard } from "@/app/components/admin/intervention/AdminInterventionCard";
import { ToolBarAdmin } from "@/app/components/admin/navigation/ToolBarAdmin";
import { useSessionContext } from "@/app/context/useSessionContext";
import { useApi } from "@/app/hooks/useApi";
import { useRouter } from "expo-router";
import React, { useEffect } from "react";
import { ScrollView, View } from "react-native";
import { StyleSheet } from "react-native";
import { FAB } from "react-native-paper";

const AdminInterventionsPage = () => {
  const router = useRouter();
  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [interventions, setInterventions] = React.useState<any[]>([]);

  useEffect(() => {
    (async () => {
      if (!sessionData?.company?.id) return;

      try {
        let endpoint = "";

        if (sessionCtx?.isTechnician()) {
          // Pour les techniciens : récupérer les interventions assignées à eux
          endpoint = `/intervention?technician_id=${sessionData.id}&status=assigned,in_progress`;
        } else if (sessionCtx?.isAdmin()) {
          // Pour les admins : toutes les interventions de leur entreprise
          endpoint = `/intervention?company_id=${sessionData.company.id}`;
        }

        const response = await api.get(endpoint);
        setInterventions(response.data);
      } catch (error) {
        console.error("Erreur lors du chargement des interventions:", error);
      }
    })();
  }, []);

  return (
    <View style={{ flex: 1 }}>
      <ToolBarAdmin title="Interventions" bottomBar />
      <ScrollView style={styles.container}>
        {interventions.map((intervention) => (
          <AdminInterventionCard
            key={intervention.id}
            intervention={intervention}
            showTechnician={sessionCtx?.isAdmin()}
            showActions={sessionCtx?.isAdmin()} // Afficher les actions pour les admins
            onPress={() =>
              router.push(`/admin/interventions/${intervention.id}`)
            }
            onEditPress={() =>
              router.push(`/admin/interventions/${intervention.id}`)
            }
            onAddTaskPress={() =>
              router.push(`/admin/interventions/${intervention.id}?step=tasks`)
            }
          />
        ))}
      </ScrollView>
      <FAB
        icon="plus"
        style={styles.fab}
        label="Ajouter une intervention"
        onPress={() => router.push("/admin/interventions/new")}
      />
    </View>
  );
};

export default AdminInterventionsPage;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 16,
  },
  fab: {
    margin: 16,
    marginBottom: 70,
    backgroundColor: "#01358D",
  },
});
