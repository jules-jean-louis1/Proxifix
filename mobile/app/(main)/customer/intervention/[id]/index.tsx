import { useLocalSearchParams, useRouter } from 'expo-router';
import { View, Text, StyleSheet, ScrollView } from 'react-native';
import React, { useEffect, useState } from 'react';
import { useApi } from '@/app/hooks/useApi';
import { ToolBarCustomer } from '@/app/components/customer/navigation/ToolBarCustomer';
import { getStatus } from '@/app/utils/intervention';

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
          'Error:',
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
  console.log(intervention);

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer
        title="Détail de l'intervention"
        showBack={true}
        onBackPress={() => router.replace('/customer/interventions')}
        bottomBar
      />
      <ScrollView contentContainerStyle={styles.container}>
        <Text style={styles.title}>{intervention.title}</Text>
        <Text style={styles.status}>{getStatus(intervention)}</Text>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Informations</Text>
          <Text style={styles.info}>
            Entreprise: {intervention.company?.name}
          </Text>
          <Text style={styles.info}>
            Equipements: {intervention.equipment?.name}
          </Text>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Chronologie</Text>
          <Text style={styles.info}>Déposé le: {intervention.created_at}</Text>
          <Text style={styles.info}>
            Démarrer le: {intervention.start_date}
          </Text>
          <Text style={styles.info}>
            Terminer le: {intervention.end_date || 'TBD'}
          </Text>
          <Text style={styles.info}>Récupérer le: TBD</Text>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Tâches</Text>
          {intervention.taskInterventions.map(
            (taskIntervention: any, index: number) => (
              <Text key={index} style={styles.info}>
                {taskIntervention.task.name}
              </Text>
            )
          )}
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    padding: 20,
    backgroundColor: '#fff',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#d32f2f',
    marginBottom: 10,
  },
  status: {
    fontSize: 16,
    color: '#90caf9',
    marginBottom: 20,
  },
  section: {
    marginBottom: 20,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 10,
  },
  info: {
    fontSize: 16,
    marginBottom: 5,
  },
  loading: { marginTop: 40, textAlign: 'center' },
});
