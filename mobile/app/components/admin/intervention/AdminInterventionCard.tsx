import React, { FC } from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import { Feather } from '@expo/vector-icons';

interface Intervention {
  id: number;
  title: string;
  status: string;
  start_date?: string;
  technician?: {
    id: number;
    first_name: string;
    last_name: string;
  };
  customer?: {
    id: number;
    first_name: string;
    last_name: string;
  };
}

interface AdminInterventionCardProps {
  intervention: Intervention;
  showTechnician?: boolean;
  onPress?: () => void;
  onEditPress?: () => void;
  onAddTaskPress?: () => void;
  showActions?: boolean; // Pour afficher les boutons d'actions
  startStep?: string; // Add this prop to match the usage in index.tsx
}

export const AdminInterventionCard: FC<AdminInterventionCardProps> = ({
  intervention,
  showTechnician = false,
  onPress,
  onEditPress,
  onAddTaskPress,
  showActions = false,
  startStep, // Keep this prop for future use
}) => {
  // Formatter la date
  const formatDate = (dateString?: string) => {
    if (!dateString) return 'Date non définie';
    return new Date(dateString).toLocaleDateString('fr-FR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    });
  };

  // Couleur du statut
  const getStatusColor = (status: string) => {
    switch (status) {
      case 'assigned':
        return '#FFA500';
      case 'in_progress':
        return '#007AFF';
      case 'completed':
        return '#28A745';
      case 'cancelled':
        return '#DC3545';
      default:
        return '#6C757D';
    }
  };

  // Texte du statut en français
  const getStatusText = (status: string) => {
    switch (status) {
      case 'assigned':
        return 'Assignée';
      case 'in_progress':
        return 'En cours';
      case 'completed':
        return 'Terminée';
      case 'cancelled':
        return 'Annulée';
      default:
        return status;
    }
  };

  return (
    <Pressable onPress={onPress} style={styles.card}>
      <View style={styles.header}>
        <Text style={styles.title} numberOfLines={2}>
          {intervention.title}
        </Text>
        <View
          style={[
            styles.statusBadge,
            { backgroundColor: getStatusColor(intervention.status) },
          ]}
        >
          <Text style={styles.statusText}>
            {getStatusText(intervention.status)}
          </Text>
        </View>
      </View>

      <View style={styles.content}>
        {/* Client */}
        <Text style={styles.clientText}>
          Client: {intervention.customer?.first_name}{' '}
          {intervention.customer?.last_name}
        </Text>

        {/* Technicien (seulement pour les admins) */}
        {showTechnician && (
          <Text style={styles.technicianText}>
            Technicien: {intervention.technician?.first_name || 'Non assigné'}
          </Text>
        )}

        {/* Date (seulement pour les techniciens) */}
        {!showTechnician && intervention.start_date && (
          <Text style={styles.dateText}>
            Date: {formatDate(intervention.start_date)}
          </Text>
        )}
      </View>

      {/* Actions (si activées) */}
      {showActions && (
        <View style={styles.actions}>
          {onEditPress && (
            <Pressable onPress={onEditPress} style={styles.actionButton}>
              <Feather name="edit" size={16} color="#007AFF" />
              <Text style={styles.actionText}>Modifier</Text>
            </Pressable>
          )}

          {onAddTaskPress && (
            <Pressable
              onPress={() => {
                if (startStep) {
                  onAddTaskPress();
                }
              }}
              style={styles.actionButton}
            >
              <Feather name="plus" size={16} color="#28A745" />
              <Text style={[styles.actionText, { color: '#28A745' }]}>
                Tâches
              </Text>
            </Pressable>
          )}
        </View>
      )}
    </Pressable>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#FFFFFF',
    borderRadius: 8,
    padding: 16,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.1,
    shadowRadius: 3.84,
    elevation: 5,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  title: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#01358D',
    flex: 1,
    marginRight: 8,
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  content: {
    gap: 4,
  },
  clientText: {
    fontSize: 14,
    color: '#333',
  },
  technicianText: {
    fontSize: 14,
    color: '#666',
  },
  dateText: {
    fontSize: 14,
    color: '#666',
  },
  actions: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: '#eee',
    marginTop: 8,
  },
  actionButton: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 8,
    paddingHorizontal: 12,
    borderRadius: 6,
    backgroundColor: '#f8f9fa',
  },
  actionText: {
    fontSize: 12,
    fontWeight: '500',
    marginLeft: 4,
    color: '#007AFF',
  },
});
