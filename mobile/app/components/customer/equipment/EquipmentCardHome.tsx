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
  equipment: any[];
}

export const EquipmentCardHome: React.FC<EquipmentCardHomeProps> = ({
  equipment,
}) => {
  if (!equipment || equipment.length === 0) {
    return null;
  }

  return (
    <View style={{ width: '100%', alignItems: 'center', marginTop: 20 }}>
      <View style={styles.header}>
        <Text style={styles.titleSide}>Vos équipements</Text>
        <Pressable onPress={() => router.push('/customer/equipments')}>
          <Text>Voir plus</Text>
        </Pressable>
      </View>
      <View style={styles.equipmentCard}>
        <View style={styles.equipmentCardContainer}>
          {equipment.slice(0, 4).map((e: any, index: number) => {
            const status =
              e.interventions && e.interventions.length > 0
                ? getStatus(e.interventions[0])
                : null;
            const statusColor = getStatusColor(e.interventions[0]?.status);
            const statusBgColor = getStatusColorBackground(
              e.interventions[0]?.status
            );

            const createdDate = new Date(
              e.created_at || e.createdAt || Date.now()
            ).toLocaleDateString('fr-FR', {
              day: '2-digit',
              month: '2-digit',
              year: 'numeric',
            });

            const recoveryDate =
              e.interventions &&
              e.interventions.length > 0 &&
              e.interventions[0].completed_at
                ? new Date(e.interventions[0].completed_at).toLocaleDateString(
                    'fr-FR',
                    {
                      day: '2-digit',
                      month: '2-digit',
                      year: 'numeric',
                    }
                  )
                : null;

            return (
              <View style={styles.card} key={index}>
                <View style={styles.cardHeader}>
                  <Text style={styles.name}>{e.name}</Text>
                  {status && (
                    <View
                      style={[
                        styles.statusContainer,
                        { backgroundColor: statusBgColor },
                      ]}
                    >
                      <Text style={[styles.status, { color: statusColor }]}>
                        {status}
                      </Text>
                    </View>
                  )}
                </View>

                {/* OS Information */}
                {e.operating_system && (
                  <View style={styles.infoRow}>
                    <Icon source="monitor" size={16} color="#6B7280" />
                    <Text style={styles.infoText}>
                      OS : {e.operating_system.name}
                    </Text>
                  </View>
                )}

                {/* Model Information */}
                <View style={styles.infoRow}>
                  <Icon source="laptop" size={16} color="#6B7280" />
                  <Text style={styles.infoText}>
                    Modèle : {e.brand?.name || ''}{' '}
                    {e.type_equipment?.name || e.name}
                  </Text>
                </View>

                {/* Dates */}
                <View style={styles.datesContainer}>
                  <Text style={styles.dateText}>Déposé : {createdDate}</Text>
                  {recoveryDate && (
                    <Text style={styles.dateText}>
                      Récupéré : {recoveryDate}
                    </Text>
                  )}
                </View>

                {/* Action Button */}
                {e.interventions && e.interventions.length > 0 && (
                  <View style={styles.actionContainer}>
                    <Pressable
                      style={styles.detailButton}
                      onPress={() =>
                        router.push(
                          `/customer/intervention/${e.interventions[0].id}`
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
          })}
        </View>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  header: {
    width: '90%',
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  equipmentCard: {
    borderRadius: 20,
    marginBottom: 20,
    width: '90%',
    alignSelf: 'center',
  },
  equipmentCardContainer: {
    width: '100%',
  },
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
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 16,
  },
  titleSide: {
    color: '#637381',
    fontWeight: 'bold',
    fontSize: 12,
    textTransform: 'uppercase',
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
  noIntervention: {
    fontSize: 14,
    color: '#999',
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
