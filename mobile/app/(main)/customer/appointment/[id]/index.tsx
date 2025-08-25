import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  ActivityIndicator,
} from 'react-native';
import { useLocalSearchParams, router } from 'expo-router';
import { Icon } from 'react-native-paper';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useApi } from '@/app/hooks/useApi';
import {
  getStatusAppointmentCard,
  getStatusColor,
  getStatusColorBackground,
} from '@/app/utils/intervention';
import { AppButton } from '@/app/components/buttons/AppButton';
import { ToolBarCustomer } from '@/app/components/customer/navigation/ToolBarCustomer';

export default function AppointmentDetailScreen() {
  const { id } = useLocalSearchParams();
  const [appointment, setAppointment] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const api = useApi();

  useEffect(() => {
    loadAppointmentDetails();
  }, [id]);

  const loadAppointmentDetails = async () => {
    try {
      setLoading(true);
      const response = await api.get(`/appointment?id=${id}`);
      if (response.data && response.data.length > 0) {
        setAppointment(response.data[0]);
      }
    } catch (error) {
      console.error('Erreur lors du chargement des détails:', error);
    } finally {
      setLoading(false);
    }
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('fr-FR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    });
  };

  const formatDateTime = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('fr-FR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  if (loading) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#E53953" />
          <Text style={styles.loadingText}>Chargement...</Text>
        </View>
      </SafeAreaView>
    );
  }

  if (!appointment) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.errorContainer}>
          <Text style={styles.errorText}>Rendez-vous introuvable</Text>
          <AppButton
            type="primary"
            onPress={() => router.back()}
            children="Retour"
          />
        </View>
      </SafeAreaView>
    );
  }

  const status = getStatusAppointmentCard(appointment.status);
  const statusColor = getStatusColor(appointment.status);
  const statusBgColor = getStatusColorBackground(appointment.status);

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <ToolBarCustomer
        onBackPress={() => router.back()}
        showBack
        bottomBar
        title="Détail de l'intervention"
      />

      <ScrollView style={styles.content} showsVerticalScrollIndicator={false}>
        {/* Title and Status */}
        <View style={styles.titleSection}>
          <Text style={styles.title}>{appointment.title}</Text>
          <View
            style={[styles.statusContainer, { backgroundColor: statusBgColor }]}
          >
            <Text style={[styles.statusText, { color: statusColor }]}>
              {status}
            </Text>
          </View>
        </View>

        {/* Informations Section */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Informations</Text>

          <View style={styles.infoCard}>
            <View style={styles.infoRow}>
              <Icon source="office-building" size={24} color="#6B7280" />
              <View style={styles.infoContent}>
                <Text style={styles.infoLabel}>Entreprise</Text>
                <Text style={styles.infoValue}>
                  {appointment.company?.name || 'it-informatique'}
                </Text>
              </View>
            </View>

            <View style={styles.infoRow}>
              <Icon source="monitor" size={24} color="#6B7280" />
              <View style={styles.infoContent}>
                <Text style={styles.infoLabel}>Équipements</Text>
                <Text style={styles.infoValue}>
                  {appointment.equipment?.brand?.name}{' '}
                  {appointment.equipment?.name}
                </Text>
              </View>
            </View>
          </View>
        </View>

        {/* Timeline Section */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Chronologie</Text>

          <View style={styles.timelineCard}>
            <View style={styles.timelineItem}>
              <View
                style={[styles.timelineDot, { backgroundColor: '#10B981' }]}
              />
              <Text style={styles.timelineText}>
                Déposé le :{' '}
                {formatDateTime(appointment.created_at || appointment.date)}
              </Text>
            </View>

            {appointment.accepted_at && (
              <View style={styles.timelineItem}>
                <View
                  style={[styles.timelineDot, { backgroundColor: '#10B981' }]}
                />
                <Text style={styles.timelineText}>
                  Démarré le : {formatDateTime(appointment.accepted_at)}
                </Text>
              </View>
            )}

            <View style={styles.timelineItem}>
              <View
                style={[styles.timelineDot, { backgroundColor: '#EF4444' }]}
              />
              <Text style={styles.timelineText}>Terminer le : TBD</Text>
            </View>

            <View style={styles.timelineItem}>
              <View
                style={[styles.timelineDot, { backgroundColor: '#EC4899' }]}
              />
              <Text style={styles.timelineText}>Récupérer le : TBD</Text>
            </View>
          </View>
        </View>

        {/* Description Section */}
        {appointment.description && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Description</Text>
            <View style={styles.descriptionCard}>
              <Text style={styles.description}>{appointment.description}</Text>
            </View>
          </View>
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F9FAFB',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 12,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: '#E5E7EB',
  },
  backButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    justifyContent: 'center',
    alignItems: 'center',
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: '#374151',
  },
  content: {
    flex: 1,
    paddingHorizontal: 16,
    paddingTop: 20,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 16,
    fontSize: 16,
    color: '#6B7280',
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 20,
  },
  errorText: {
    fontSize: 18,
    color: '#EF4444',
    marginBottom: 20,
    textAlign: 'center',
  },
  titleSection: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 32,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#E53953',
    flex: 1,
    marginRight: 16,
  },
  statusContainer: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 16,
    alignItems: 'center',
    minWidth: 100,
  },
  statusText: {
    fontSize: 14,
    fontWeight: '600',
  },
  section: {
    marginBottom: 32,
  },
  sectionTitle: {
    fontSize: 20,
    fontWeight: '600',
    color: '#374151',
    marginBottom: 16,
  },
  infoCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    padding: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 4,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 20,
  },
  infoContent: {
    marginLeft: 16,
    flex: 1,
  },
  infoLabel: {
    fontSize: 14,
    color: '#9CA3AF',
    marginBottom: 4,
    fontWeight: '500',
  },
  infoValue: {
    fontSize: 16,
    fontWeight: '600',
    color: '#374151',
  },
  timelineCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    padding: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 4,
  },
  timelineItem: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
  },
  timelineDot: {
    width: 12,
    height: 12,
    borderRadius: 6,
    marginRight: 16,
  },
  timelineText: {
    fontSize: 16,
    color: '#6B7280',
    fontWeight: '500',
  },
  descriptionCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    padding: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 4,
  },
  description: {
    fontSize: 16,
    color: '#6B7280',
    lineHeight: 24,
  },
});
