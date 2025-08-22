import React, { FC } from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';

interface Customer {
  id: number;
  email: string;
  role: string;
  first_name: string;
  last_name: string;
  created_at: string;
  updated_at: string;
  company: any;
  equipment: any[];
  appointmentRequests: any[];
  zipcode: string;
  city: string;
  phone: string;
  address: string;
}

interface AdminCustomerCardProps {
  customer: Customer;
  onPress: () => void;
}

export const AdminCustomerCard: FC<AdminCustomerCardProps> = ({
  customer,
  onPress,
}) => {
  // Formatter la date d'ajout
  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    });
  };

  return (
    <Pressable style={styles.card} onPress={onPress}>
      <View style={styles.header}>
        <Text style={styles.name}>
          {customer.first_name} {customer.last_name}
        </Text>
        <Text style={styles.date}>
          Ajouté le {formatDate(customer.created_at)}
        </Text>
      </View>

      <View style={styles.content}>
        <Text style={styles.location}>
          {customer.zipcode} - {customer.city}
        </Text>

        <Text style={styles.email}>{customer.email}</Text>

        {customer.phone && <Text style={styles.phone}>{customer.phone}</Text>}
      </View>

      <View style={styles.footer}>
        <Text style={styles.stats}>
          {customer.equipment.length} équipement
          {customer.equipment.length > 1 ? 's' : ''}
        </Text>
        <Text style={styles.stats}>
          {customer.appointmentRequests.length} demande
          {customer.appointmentRequests.length > 1 ? 's' : ''}
        </Text>
      </View>
    </Pressable>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#fff',
    borderRadius: 8,
    padding: 16,
    marginVertical: 8,
    marginHorizontal: 16,
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
    alignItems: 'center',
    marginBottom: 12,
  },
  name: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#333',
  },
  date: {
    fontSize: 12,
    color: '#666',
  },
  content: {
    marginBottom: 12,
  },
  location: {
    fontSize: 14,
    color: '#555',
    marginBottom: 4,
  },
  email: {
    fontSize: 14,
    color: '#007AFF',
    marginBottom: 4,
  },
  phone: {
    fontSize: 14,
    color: '#555',
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: '#eee',
  },
  stats: {
    fontSize: 12,
    color: '#666',
  },
});
