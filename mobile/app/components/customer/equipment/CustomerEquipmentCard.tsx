import React from 'react';
import { View, Text, Pressable, StyleSheet } from 'react-native';
import { router } from 'expo-router';

interface Equipment {
  id: number;
  name: string;
  created_at?: string;
  operating_system?: { name: string };
  brand?: { name: string };
  type_equipment?: { name: string };
}

interface CustomerEquipmentCardProps {
  equipment: Equipment;
}

const CustomerEquipmentCard: React.FC<CustomerEquipmentCardProps> = ({
  equipment,
}) => {
  const createdDate = new Date(
    equipment.created_at || Date.now()
  ).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });

  return (
    <View style={styles.card}>
      <View style={styles.cardHeader}>
        <Text style={styles.name}>{equipment.name}</Text>
      </View>

      {/* OS Information */}
      {equipment.operating_system && (
        <View style={styles.infoRow}>
          <Text style={styles.infoText}>
            OS : {equipment.operating_system.name}
          </Text>
        </View>
      )}

      {/* Model Information */}
      <View style={styles.infoRow}>
        <Text style={styles.infoText}>
          Modèle : {equipment.brand?.name || ''}{' '}
          {equipment.type_equipment?.name || equipment.name}
        </Text>
      </View>

      {/* Dates */}
      <View style={styles.datesContainer}>
        <Text style={styles.dateText}>Déposé : {createdDate}</Text>
      </View>

      {/* Action Button */}
      <View style={styles.actionContainer}>
        <Pressable
          style={styles.detailButton}
          onPress={() => router.push(`/customer/equipment/${equipment.id}`)}
        >
          <Text style={styles.detailButtonText}>Voir détails</Text>
        </Pressable>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#FFF',
    borderRadius: 8,
    padding: 16,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  name: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#344260',
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 4,
  },
  infoText: {
    fontSize: 14,
    color: '#6B7280',
  },
  datesContainer: {
    marginTop: 8,
  },
  dateText: {
    fontSize: 12,
    color: '#78849E',
  },
  actionContainer: {
    marginTop: 12,
    alignItems: 'flex-end',
  },
  detailButton: {
    paddingVertical: 8,
    paddingHorizontal: 12,
    borderRadius: 6,
    backgroundColor: '#FEE2E2',
  },
  detailButtonText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#E53953',
  },
});

export default CustomerEquipmentCard;
