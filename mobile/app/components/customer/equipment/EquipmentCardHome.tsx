import React from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import {
  getStatus,
  getStatusColor,
  getStatusColorBackground,
} from '@/app/utils/intervention';
import { router } from 'expo-router';
import { Icon } from 'react-native-paper';

interface EquipmentCardHomeProps {
  equipment: any;
}

export const EquipmentCardHome: React.FC<EquipmentCardHomeProps> = ({
  equipment,
}) => {
  if (!equipment) {
    return null;
  }

  const status =
    equipment.interventions && equipment.interventions.length > 0
      ? getStatus(equipment.interventions[0])
      : null;
  const statusColor = getStatusColor(equipment.interventions[0]?.status);
  const statusBgColor = getStatusColorBackground(
    equipment.interventions[0]?.status
  );

  const createdDate = new Date(
    equipment.created_at || equipment.createdAt || Date.now()
  ).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });

  const recoveryDate =
    equipment.interventions &&
    equipment.interventions.length > 0 &&
    equipment.interventions[0].completed_at
      ? new Date(equipment.interventions[0].completed_at).toLocaleDateString(
          'fr-FR',
          {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
          }
        )
      : null;

  return (
    <View style={styles.card}>
      <View style={styles.cardHeader}>
        <Text style={styles.name}>{equipment.name}</Text>
        {status && (
          <View
            style={[styles.statusContainer, { backgroundColor: statusBgColor }]}
          >
            <Text style={[styles.status, { color: statusColor }]}>
              {status}
            </Text>
          </View>
        )}
      </View>

      {/* OS Information */}
      {equipment.operating_system && (
        <View style={styles.infoRow}>
          <Icon source="monitor" size={16} color="#6B7280" />
          <Text style={styles.infoText}>
            OS : {equipment.operating_system.name}
          </Text>
        </View>
      )}

      {/* Model Information */}
      <View style={styles.infoRow}>
        <Icon source="laptop" size={16} color="#6B7280" />
        <Text style={styles.infoText}>
          Modèle : {equipment.brand?.name || ''}{' '}
          {equipment.type_equipment?.name || equipment.name}
        </Text>
      </View>

      {/* Dates */}
      <View style={styles.datesContainer}>
        <Text style={styles.dateText}>Déposé : {createdDate}</Text>
        {recoveryDate && (
          <Text style={styles.dateText}>Récupéré : {recoveryDate}</Text>
        )}
      </View>

      {/* Action Button */}
      {equipment.interventions && equipment.interventions.length > 0 && (
        <View style={styles.actionContainer}>
          <Pressable
            style={styles.detailButton}
            onPress={() =>
              router.push(
                `/customer/intervention/${equipment.interventions[0].id}`
              )
            }
          >
            <Text style={styles.detailButtonText}>Voir détails</Text>
            <Icon source="chevron-right" size={16} color="#E53953" />
          </Pressable>
        </View>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#ffffff',
    borderRadius: 16,
    padding: 20,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 4,
    width: '90%',
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 16,
  },
  name: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#E53953',
    flex: 1,
    marginRight: 12,
  },
  statusContainer: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 12,
    minWidth: 80,
    alignItems: 'center',
  },
  status: {
    fontSize: 12,
    fontWeight: '600',
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  infoText: {
    fontSize: 14,
    color: '#6B7280',
    marginLeft: 8,
    fontWeight: '500',
  },
  datesContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 8,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: '#F3F4F6',
  },
  dateText: {
    fontSize: 12,
    color: '#9CA3AF',
    fontWeight: '500',
  },
  actionContainer: {
    marginTop: 16,
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: '#F3F4F6',
  },
  detailButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#FEF2F2',
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#FECACA',
  },
  detailButtonText: {
    fontSize: 14,
    fontWeight: '600',
    color: '#E53953',
  },
});
