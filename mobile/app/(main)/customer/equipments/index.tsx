import { useEffect, useState } from 'react';
import {
  FlatList,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';
import { useApi } from '@/app/hooks/useApi';
import { useSessionContext } from '@/app/context/useSessionContext';
import React from 'react';
import { components } from '@/app/types/types';
import { router } from 'expo-router';
import { EquipmentModalForm } from '@/app/components/customer/equipment/EquipmentModalForm';
import { FAB } from 'react-native-paper';
import { ToolBarCustomer } from '@/app/components/customer/navigation/ToolBarCustomer';
import { EquipmentCardHome } from '@/app/components/customer/equipment/EquipmentCardHome';
import CustomerEquipmentCard from '@/app/components/customer/equipment/CustomerEquipmentCard';

const EquipmentsPage = () => {
  const [equipments, setEquipments] = useState<any[]>([]);
  const [typeEquipment, setTypeEquipment] = useState<any[]>([]);
  const [brands, setBrands] = useState<any[]>([]);
  const [os, setOs] = useState<any>([]);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [fetchData, setFetchData] = useState<boolean>(false);
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const api = useApi();

  useEffect(() => {
    (async () => {
      try {
        const response = await api.get(`/equipment?user_id=${sessionData?.id}`);
        setEquipments(response.data);
        const typeEquipmentResponse = await api.get('/type-equipment');
        setTypeEquipment(typeEquipmentResponse.data);
        const brandsResponse = await api.get('/brand');
        setBrands(brandsResponse.data);
        const osResponse = await api.get('/operating-system');
        setOs(osResponse.data);
      } catch (error) {
        console.error(
          'Error:',
          (error as any).response
            ? (error as any).response.data
            : (error as any).message
        );
        setError(
          (error as any).message || 'Impossible to get the equipments of user.'
        );
      } finally {
        setLoading(false);
        setFetchData(false);
      }
    })();
  }, []);

  if (loading) {
    return (
      <View style={{ flex: 1 }}>
        <ToolBarCustomer title={'Mes équipements'} bottomBar />
        <View style={styles.container}>
          <Text style={styles.loadingText}>Loading...</Text>
        </View>
      </View>
    );
  }

  if (error) {
    return (
      <View style={{ flex: 1 }}>
        <ToolBarCustomer title={'Mes équipements'} bottomBar />
        <View style={styles.container}>
          <Text style={styles.errorText}>{error}</Text>
        </View>
      </View>
    );
  }
  if (equipments.length === 0) {
    return (
      <View style={{ flex: 1 }}>
        <ToolBarCustomer title={'Mes équipements'} bottomBar />
        <ScrollView style={styles.container}>
          <Text style={styles.loadingText}>Aucun équipement trouvé.</Text>
        </ScrollView>
        <View pointerEvents="box-none" style={styles.fabContainer}>
          <EquipmentModalForm
            type="create"
            brands={brands}
            typeEquipment={typeEquipment}
            os={os}
            setEquipments={setEquipments}
            onSuccess={() => setFetchData(!fetchData)}
            button={
              <FAB
                icon="plus"
                style={styles.fab}
                label="Ajouter un équipement"
              />
            }
          />
        </View>
      </View>
    );
  }

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer title={'Mes équipements'} bottomBar />
      <ScrollView style={styles.container}>
        {equipments.map(item => (
          <CustomerEquipmentCard key={item.id} equipment={item} />
        ))}
      </ScrollView>
      <View pointerEvents="box-none" style={styles.fabContainer}>
        <EquipmentModalForm
          type="create"
          brands={brands}
          typeEquipment={typeEquipment}
          os={os}
          setEquipments={setEquipments}
          button={
            <FAB
              icon="plus"
              color="white"
              style={styles.fab}
              label="Ajouter un équipement"
            />
          }
        />
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  fabContainer: {
    position: 'absolute',
    right: 0,
    bottom: 60,
    width: '100%',
    alignItems: 'flex-end',
    zIndex: 100,
  },
  fab: {
    margin: 16,
    backgroundColor: '#F9556D',
    color: '#FFF',
  },
  container: {
    flex: 1,
    padding: 16,
    backgroundColor: '#F0F3F4',
  },
  title: {
    textAlign: 'center',
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 20,
    color: '#344260',
  },
  loadingText: {
    textAlign: 'center',
    fontSize: 18,
    color: '#5B6880',
  },
  errorText: {
    color: 'red',
    fontSize: 18,
    textAlign: 'center',
  },
  equipmentItem: {
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#ccc',
    backgroundColor: '#fff',
    borderRadius: 8,
    marginBottom: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 2,
    elevation: 2,
  },
  equipmentName: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#344260',
    marginBottom: 5,
  },
  equipmentBrand: {
    fontSize: 16,
    color: '#5B6880',
    marginBottom: 5,
  },
  equipmentOS: {
    fontSize: 16,
    color: '#5B6880',
    marginBottom: 5,
  },
  equipmentType: {
    fontSize: 16,
    color: '#5B6880',
    marginBottom: 5,
  },
  equipmentDate: {
    fontSize: 14,
    color: '#78849E',
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    padding: 16,
  },
  cancelButton: {
    color: '#FF3B30',
    fontSize: 16,
    fontWeight: 'bold',
  },
  confirmButton: {
    color: '#007BFF',
    fontSize: 16,
    fontWeight: 'bold',
  },
  modalContainer: {
    flex: 1,
    backgroundColor: '#fff',
  },
  modalContent: {
    flex: 1,
    padding: 16,
  },
});

export default EquipmentsPage;
