import React, { FC, useEffect, useState } from 'react';
import { TouchableOpacity, View, Text, StyleSheet } from 'react-native';
import { AppointmentModalForm } from './AppointmentModalForm';
import { Card, Icon } from 'react-native-paper';
import {
  APPOINTMENT_STATUS,
  getStatusAppointmentCard,
  getStatusColor,
  getStatusColorBackground,
} from '@/app/utils/intervention';

export const AppointmentCard: FC<{
  appointment: any;
  onPress?: () => void;
  onSuccess?: () => void;
}> = ({ appointment, onPress, onSuccess = () => {} }) => {
  const { company, date, title, status } = appointment;

  const formattedDate = new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
  return (
    <Card onPress={onPress} style={styles.card}>
      <View style={styles.header}>
        <Text style={styles.title}>{title}</Text>
        <View
          style={[
            styles.statusContainer,
            { backgroundColor: getStatusColorBackground(status) },
          ]}
        >
          <Text style={{ fontSize: 14, color: getStatusColor(status) }}>
            {getStatusAppointmentCard(status)}
          </Text>
        </View>
      </View>
      <Card.Content>
        <View style={styles.companyContainer}>
          <Icon source="office-building" size={24} color="#09090B" />
          <Text style={styles.companyName}>{company.name}</Text>
        </View>
        <View style={styles.equipmentContainer}>
          <Icon source="tools" size={24} color="#09090B" />
          <Text style={styles.equipmentName}>{appointment.equipment.name}</Text>
        </View>
        <View style={styles.details}>
          <Text style={styles.date}>{formattedDate}</Text>
        </View>
      </Card.Content>
      <Card.Actions>
        {status === APPOINTMENT_STATUS.PENDING && (
          <AppointmentModalForm
            mode="update"
            id={appointment.id}
            onSuccess={() => onSuccess}
            button={
              <TouchableOpacity>
                <Text style={styles.companyName}>Modifier</Text>
              </TouchableOpacity>
            }
          />
        )}
      </Card.Actions>
    </Card>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#F5F5F8',
    width: '100%',
    borderRadius: 8,
    padding: 16,
    marginVertical: 8,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  companyName: {
    fontSize: 16,
    fontWeight: 'light',
    color: '#637381',
  },
  details: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  date: {
    fontSize: 14,
    color: '#333',
  },
  time: {
    fontSize: 14,
    color: '#333',
  },
  title: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#E53953',
  },
  statusContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 8,
    borderRadius: 8,
  },
  companyContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
  },
  equipmentContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
  },
  equipmentName: {
    fontSize: 16,
    fontWeight: 'light',
    color: '#637381',
    marginLeft: 8,
  },
});
