import React from 'react';
import { View, Text, Pressable, StyleSheet } from 'react-native';
import { Feather } from '@expo/vector-icons';

interface AppointmentData {
  id: number;
  date: string;
  title: string;
  description: string;
  type_intervention: string | null;
  equipment: {
    name: string;
    id: number;
  } | null;
  created_at: string;
  updated_at: string;
  status: string;
  company: {
    name: string;
    id: number;
  } | null;
  user: [number, string, string, string] | null; // [id, firstName, lastName, email]
}

interface AdminAppointmentCardProps {
  appointment: AppointmentData;
  onPress?: () => void;
  onEditPress?: () => void;
  onDeletePress?: () => void;
  showActions?: boolean;
}

export const AdminAppointmentCard: React.FC<AdminAppointmentCardProps> = ({
  appointment,
  onPress,
  onEditPress,
  onDeletePress,
  showActions = false,
}) => {
  const getStatusColor = (status: string) => {
    switch (status) {
      case 'pending':
        return '#FF9800';
      case 'scheduled':
        return '#2196F3';
      case 'completed':
        return '#4CAF50';
      case 'cancelled':
        return '#F44336';
      default:
        return '#757575';
    }
  };

  const getStatusText = (status: string) => {
    switch (status) {
      case 'pending':
        return 'En attente';
      case 'scheduled':
        return 'Planifié';
      case 'completed':
        return 'Terminé';
      case 'cancelled':
        return 'Annulé';
      default:
        return status;
    }
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  const getUserName = () => {
    if (!appointment.user) return 'Client non défini';
    const [, firstName, lastName] = appointment.user;
    return `${firstName} ${lastName}`;
  };

  return (
    <Pressable style={styles.card} onPress={onPress}>
      <View style={styles.header}>
        <View style={styles.titleRow}>
          <Text style={styles.title} numberOfLines={2}>
            {appointment.title}
          </Text>
          <View
            style={[
              styles.statusBadge,
              { backgroundColor: getStatusColor(appointment.status) },
            ]}
          >
            <Text style={styles.statusText}>
              {getStatusText(appointment.status)}
            </Text>
          </View>
        </View>

        {showActions && (
          <View style={styles.actions}>
            {onEditPress && (
              <Pressable style={styles.actionButton} onPress={onEditPress}>
                <Feather name="edit-2" size={16} color="#01358D" />
              </Pressable>
            )}
            {onDeletePress && (
              <Pressable style={styles.actionButton} onPress={onDeletePress}>
                <Feather name="trash-2" size={16} color="#F44336" />
              </Pressable>
            )}
          </View>
        )}
      </View>

      <View style={styles.content}>
        <View style={styles.infoRow}>
          <Feather name="calendar" size={16} color="#666" />
          <Text style={styles.infoText}>{formatDate(appointment.date)}</Text>
        </View>

        <View style={styles.infoRow}>
          <Feather name="user" size={16} color="#666" />
          <Text style={styles.infoText}>{getUserName()}</Text>
        </View>

        {appointment.equipment && (
          <View style={styles.infoRow}>
            <Feather name="monitor" size={16} color="#666" />
            <Text style={styles.infoText}>{appointment.equipment.name}</Text>
          </View>
        )}

        {appointment.type_intervention && (
          <View style={styles.infoRow}>
            <Feather name="tool" size={16} color="#666" />
            <Text style={styles.infoText}>{appointment.type_intervention}</Text>
          </View>
        )}

        {appointment.description && (
          <View style={styles.descriptionContainer}>
            <Text style={styles.description} numberOfLines={2}>
              {appointment.description}
            </Text>
          </View>
        )}

        <View style={styles.footer}>
          <Text style={styles.createdAt}>
            Créé le {formatDate(appointment.created_at)}
          </Text>
          {appointment.company && (
            <Text style={styles.company}>{appointment.company.name}</Text>
          )}
        </View>
      </View>
    </Pressable>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  titleRow: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'flex-start',
    justifyContent: 'space-between',
    marginRight: 8,
  },
  title: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#333',
    flex: 1,
    marginRight: 8,
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: '600',
  },
  actions: {
    flexDirection: 'row',
    gap: 8,
  },
  actionButton: {
    padding: 8,
    borderRadius: 8,
    backgroundColor: '#f5f5f5',
  },
  content: {
    gap: 8,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  infoText: {
    fontSize: 14,
    color: '#666',
    flex: 1,
  },
  descriptionContainer: {
    marginTop: 4,
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: '#f0f0f0',
  },
  description: {
    fontSize: 14,
    color: '#666',
    fontStyle: 'italic',
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 8,
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: '#f0f0f0',
  },
  createdAt: {
    fontSize: 12,
    color: '#999',
  },
  company: {
    fontSize: 12,
    color: '#01358D',
    fontWeight: '600',
  },
});
