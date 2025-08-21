import React, { useEffect, useState } from "react";
import {
  View,
  StyleSheet,
  Text,
  ScrollView,
  ActivityIndicator,
} from "react-native";
import { useApi } from "@/app/hooks/useApi";
import { useSessionContext } from "@/app/context/useSessionContext";
import { AppointmentModalForm } from "@/app/components/customer/appointment/AppointmentModalForm";
import { AppointmentCard } from "@/app/components/customer/appointment/AppointmentCard";
import { InterventionCard } from "@/app/components/customer/intervention/InterventionCard";
import { FAB, MD2Colors } from "react-native-paper";
import { useRouter } from "expo-router";
import { ToolBarCustomer } from "@/app/components/customer/navigation/ToolBarCustomer";

export default function InterventionsPage() {
  const api = useApi();
  const router = useRouter();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [interventions, setInterventions] = useState<any>([]);
  const [appointments, setAppointments] = useState<any>([]);
  const [fetchData, setFetchData] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    (async () => {
      try {
        setLoading(true);
        setError(null);
        const response = await api.get(
          `/intervention?user_id=${sessionData?.id}`,
        );
        const appointmentsResponse = await api.get(
          `/appointment?user_id=${sessionData?.id}`,
        );
        setInterventions(response.data);
        setAppointments(appointmentsResponse.data);
        setFetchData(false);
      } catch (error) {
        console.error("Error fetching interventions:", error);
        setError("Impossible de récupérer les interventions.");
      } finally {
        setLoading(false);
      }
    })();
  }, [fetchData]);

  if (loading) {
    return (
      <View style={{ padding: 20 }}>
        <ActivityIndicator animating={true} color={MD2Colors.red800} />
      </View>
    );
  }

  if (error) {
    return (
      <View style={styles.container}>
        <Text style={styles.errorText}>{error}</Text>
      </View>
    );
  }

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer title="Mes interventions" bottomBar />
      <ScrollView style={styles.container}>
        <View>
          <Text style={styles.dateText}>Vos interventions</Text>
        </View>
        <View style={styles.listContainer}>
          {interventions && interventions.length !== 0 ? (
            interventions.map((intervention: any) => (
              <InterventionCard
                key={intervention.id}
                intervention={intervention}
                onPress={() => {
                  router.push(`/customer/intervention/${intervention.id}`);
                }}
              />
            ))
          ) : (
            <Text style={styles.textEmptyData}>
              Aucune intervention trouvée.
            </Text>
          )}
        </View>
        {appointments && appointments.length !== 0 && (
          <View style={styles.listContainerAppointments}>
            <Text style={styles.dateText}>Vos demandes</Text>
            {appointments.map((appointment: any) => (
              <AppointmentCard
                key={appointment.id}
                appointment={appointment}
                onSuccess={() => setFetchData(!fetchData)}
              />
            ))}
          </View>
        )}
      </ScrollView>
      <View pointerEvents="box-none" style={styles.fabContainer}>
        <AppointmentModalForm
          mode="create"
          onSuccess={() => setFetchData(!fetchData)}
          button={
            <FAB
              icon="plus"
              style={styles.fab}
              label="Ajouter un rendez-vous"
            />
          }
        />
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#FFF",
    padding: 16,
  },
  title: {
    fontSize: 24,
    fontWeight: "bold",
    color: "#344260",
    marginBottom: 10,
  },
  description: {
    fontSize: 16,
    color: "#5B6880",
  },
  listContainer: {
    marginBottom: 20,
  },
  listContainerAppointments: {
    marginBottom: 150,
  },
  dateText: {
    fontSize: 15,
    color: "#344260",
    fontWeight: "bold",
    marginBottom: 10,
  },
  icon: {
    paddingLeft: 5,
  },
  iconLeft: {
    marginRight: 10,
  },
  status: {
    fontSize: 13,
    fontWeight: "bold",
    backgroundColor: "rgba(0, 119, 193, 0.2)",
    color: "#48A3D7",
    padding: 8,
    borderRadius: 8,
    marginRight: 8,
    textAlign: "center",
  },
  footerDate: {
    fontSize: 14,
    color: "#5B6880",
    flex: 1,
    textAlign: "right",
  },
  buttonContainer: {
    flexDirection: "column",
    alignItems: "center",
    padding: 16,
    justifyContent: "space-evenly",
  },
  errorText: {
    color: "red",
    fontSize: 18,
    textAlign: "center",
  },
  textEmptyData: {
    fontSize: 16,
    color: "#000000ff",
    textAlign: "center",
    fontWeight: "semibold",
    marginTop: 20,
  },
  fabContainer: {
    position: "absolute",
    right: 0,
    bottom: 60,
    width: "100%",
    alignItems: "flex-end",
    zIndex: 100,
    backgroundColor: "#F9556D",
  },
  fab: {
    margin: 16,
  },
});
