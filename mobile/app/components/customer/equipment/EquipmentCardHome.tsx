import React from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import { getStatus, getStatusColor } from '@/app/utils/intervention';
import { router } from 'expo-router';

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
                : 'No data';
            const statusColor = getStatusColor(e.interventions[0]?.status);
            return (
              <View style={styles.card} key={index}>
                <Text style={styles.name}>{e.name}</Text>
                {e.interventions && e.interventions.length > 0 && (
                  <Text style={[styles.status, { color: statusColor }]}>
                    {status}
                  </Text>
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
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: '#ffffff',
    borderRadius: 10,
    padding: 16,
    marginBottom: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
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
    color: '#364A63',
  },
  status: {
    fontSize: 16,
    fontWeight: '600',
  },
  noIntervention: {
    fontSize: 14,
    color: '#999',
  },
});
