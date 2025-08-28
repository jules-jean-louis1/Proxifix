import React, { FC } from 'react';
import { TouchableOpacity, View, Text, StyleSheet } from 'react-native';
import { CustomerAppointmentStepper } from './CustomerAppointmentStepper';
import { Card, Icon } from 'react-native-paper';
import {
  APPOINTMENT_STATUS,
  getStatusAppointmentCard,
  getStatusColor,
  getStatusColorBackground,
} from '@/app/utils/intervention';
import { Feather } from '@expo/vector-icons';

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

  const depositedDate = new Date(
    appointment.created_at || appointment.createdAt || date
  ).toLocaleDateString('fr-FR', {
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
          <Text
            style={{
              fontSize: 12,
              fontWeight: '600',
              color: getStatusColor(status),
            }}
          >
            {getStatusAppointmentCard(status)}
          </Text>
        </View>
      </View>

      <View style={styles.companyContainer}>
        <Feather
          name="package"
          size={25}
          color="#000"
          style={styles.iconLeft}
        ></Feather>
        <Text style={styles.companyName}>{company.name}</Text>
      </View>

      <View style={styles.equipmentContainer}>
        <Feather
          name="monitor"
          size={25}
          color="#000"
          style={styles.iconLeft}
        ></Feather>
        <Text style={styles.equipmentName}>{appointment.equipment.name}</Text>
      </View>

      <View style={styles.details}>
        <View>
          <Text style={styles.date}>{formattedDate}</Text>
        </View>
        <Text style={styles.depositedDate}>Déposé le : {depositedDate}</Text>
      </View>

      {status === APPOINTMENT_STATUS.PENDING && (
        <View style={styles.actionsContainer}>
          <CustomerAppointmentStepper
            mode="update"
            id={appointment.id}
            onSuccess={() => onSuccess()}
            button={
              <TouchableOpacity>
                <Text style={styles.modifyButton}>Modifier</Text>
              </TouchableOpacity>
            }
          />
        </View>
      )}
    </Card>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#FFFFFF',
    width: '100%',
    borderRadius: 16,
    marginVertical: 8,
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 4,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 16,
    paddingHorizontal: 20,
    paddingTop: 20,
  },
  title: {
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
  companyContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
    paddingHorizontal: 20,
  },
  companyName: {
    fontSize: 14,
    fontWeight: '500',
    color: '#6B7280',
    marginLeft: 8,
  },
  equipmentContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
    paddingHorizontal: 20,
  },
  equipmentName: {
    fontSize: 14,
    fontWeight: '500',
    color: '#6B7280',
    marginLeft: 8,
  },
  details: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingBottom: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#F3F4F6',
  },
  date: {
    fontSize: 14,
    fontWeight: '600',
    color: '#10B981',
    backgroundColor: '#ECFDF5',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 6,
  },
  depositedDate: {
    fontSize: 12,
    color: '#9CA3AF',
  },
  time: {
    fontSize: 14,
    color: '#374151',
  },
  actionsContainer: {
    paddingHorizontal: 20,
    paddingVertical: 12,
  },
  modifyButton: {
    color: '#3B82F6',
    fontSize: 14,
    fontWeight: '600',
  },
  iconLeft: {
    marginRight: 10,
  },
});
