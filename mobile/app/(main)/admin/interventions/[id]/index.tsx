import React from 'react';
import { Alert } from 'react-native';
import { router, useLocalSearchParams } from 'expo-router';
import { AdminInterventionStepper } from '@/app/components/admin/intervention/AdminInterventionStepper';
import { useApi } from '@/app/hooks/useApi';

const AdminInterventionEditPage = () => {
  const { id, step } = useLocalSearchParams<{ id: string; step?: string }>();
  const api = useApi();

  const handleUpdate = async (data: any) => {
    try {
      await api.put(`/intervention/${id}`, data);
      Alert.alert('Succès', "L'intervention a été mise à jour avec succès", [
        { text: 'OK', onPress: () => router.back() },
      ]);
    } catch (error) {
      console.error('Error updating intervention:', error);
      Alert.alert('Erreur', "Impossible de mettre à jour l'intervention");
    }
  };

  return (
    <AdminInterventionStepper
      mode="edit"
      interventionId={id}
      initialStep={step as any}
      onSubmit={handleUpdate}
      onCancel={() => router.back()}
    />
  );
};

export default AdminInterventionEditPage;
