import { AdminInterventionCard } from '@/app/components/admin/intervention/AdminInterventionCard';
import { AdminAppointmentCard } from '@/app/components/admin/appointment/AdminAppointmentCard';
import { useSessionContext } from '@/app/context/useSessionContext';
import { useApi } from '@/app/hooks/useApi';
import { Feather } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useEffect } from 'react';
import { Pressable, ScrollView, Text, View } from 'react-native';
import { StyleSheet } from 'react-native';
import { FAB } from 'react-native-paper';

const AdminInterventionsPage = () => {
  const router = useRouter();
  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [interventions, setInterventions] = React.useState<any[]>([]);
  const [appointments, setAppointments] = React.useState<any[]>([]);
  const [isLoading, setIsLoading] = React.useState<boolean>(true);
  const [mode, setMode] = React.useState<'interventions' | 'appointments'>(
    'interventions'
  );

  useEffect(() => {
    (async () => {
      if (!sessionData?.company?.id) return;

      try {
        let endpoint = '';

        if (sessionCtx?.isTechnician()) {
          // Pour les techniciens : récupérer les interventions assignées à eux
          endpoint = `/intervention?technician_id=${sessionData.id}&status=assigned,in_progress`;
        } else if (sessionCtx?.isAdmin()) {
          // Pour les admins : toutes les interventions de leur entreprise
          endpoint = `/intervention?company_id=${sessionData.company.id}`;
        }

        const response = await api.get(endpoint);
        const responseAppointments = await api.get(
          `/appointment?company_id=${sessionData.company.id}&status=pending,scheduled`
        );
        setInterventions(response.data);
        setAppointments(responseAppointments.data);
      } catch (error) {
        console.error('Erreur lors du chargement des interventions:', error);
      }
    })();
  }, []);

  return (
    <View style={{ flex: 1, padding: 16 }}>
      <Pressable
        style={{
          flexDirection: 'row',
          alignItems: 'center',
          marginBottom: 16,
        }}
        onPress={() =>
          setMode(prev =>
            prev === 'interventions' ? 'appointments' : 'interventions'
          )
        }
      >
        <Text style={{ fontSize: 18, fontWeight: 'bold' }}>
          {mode === 'appointments' ? 'Rendez-vous' : 'Interventions'}
        </Text>
        <Feather
          name={'chevron-down'}
          size={24}
          color="black"
          style={{ marginLeft: 8 }}
        />
      </Pressable>
      <ScrollView style={styles.container}>
        {mode === 'appointments'
          ? appointments.map(appointment => (
              <AdminAppointmentCard
                key={appointment.id}
                appointment={appointment}
                showActions={sessionCtx?.isAdmin()}
                onPress={() => {
                  router.push(`/admin/appointments/${appointment.id}`);
                }}
                onEditPress={() => {
                  // Navigation vers l'édition du rendez-vous
                  console.log('Navigate to appointment edit:', appointment.id);
                }}
              />
            ))
          : interventions.map(intervention => (
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
                  router.push(
                    `/admin/interventions/${intervention.id}?step=tasks`
                  )
                }
              />
            ))}
      </ScrollView>
      <FAB
        icon="plus"
        style={styles.fab}
        label="Ajouter une intervention"
        onPress={() => router.push('/admin/interventions/new')}
      />
    </View>
  );
};

export default AdminInterventionsPage;

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  fab: {
    margin: 16,
    marginBottom: 70,
    backgroundColor: '#01358D',
  },
});
