import React from 'react';
import { Alert } from 'react-native';
import { router } from 'expo-router';
import { AdminInterventionStepper } from '@/app/components/admin/intervention/AdminInterventionStepper';
import { useApi } from '@/app/hooks/useApi';

const AdminInterventionsNewPage = () => {
  const api = useApi();

  const handleCreate = async (data: any) => {
    try {
      const response = await api.post('/intervention', data);
      console.log('Intervention created successfully:', response.data);
      Alert.alert(
        'Succès',
        'L\'intervention a été créée avec succès',
        [{ text: 'OK', onPress: () => router.push('/admin/interventions') }]
      );
    } catch (error) {
      console.error('Error creating intervention:', error);
      Alert.alert('Erreur', 'Impossible de créer l\'intervention');
    }
  };

  return (
    <AdminInterventionStepper
      mode="create"
      onSubmit={handleCreate}
      onCancel={() => router.back()}
    />
  );
};

export default AdminInterventionsNewPage;
