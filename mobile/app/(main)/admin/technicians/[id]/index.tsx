import { useSessionContext } from '@/app/context/useSessionContext';
import { useApi } from '@/app/hooks/useApi';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { FC, useEffect, useState } from 'react';
import { ScrollView, View } from 'react-native';
import React from 'react';
import { Text } from 'react-native-paper';
import { AdminTechnicianForm } from '@/app/components/admin/technician/AdminTechnicianForm';
import { ToolBarAdmin } from '@/app/components/admin/navigation/ToolBarAdmin';

const AdminTechnicianDetailsPage: FC = () => {
  const { id } = useLocalSearchParams();
  const router = useRouter();
  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [technician, setTechnician] = useState<any>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);

  useEffect(() => {
    if (!sessionData) return;
    (async () => {
      const technicianId = id as string;
      if (!technicianId) return;
      try {
        const response = await api.get(`/user/${technicianId}`);
        setTechnician(response.data);
      } catch (error) {
        console.error('Erreur lors du chargement du technicien:', error);
      } finally {
        setIsLoading(false);
      }
    })();
  }, [id]);

  const handleSubmit = async (data: any) => {
    try {
      const response = await api.patch(`/user/${data.id}`, data);
      setTechnician(data);
      router.back();
    } catch (error) {
      console.error(error);
    }
  };

  if (isLoading) {
    return (
      <View style={styles.container}>
        <Text>Loading...</Text>
      </View>
    );
  }

  return (
    <View style={{ flex: 1 }}>
      <ToolBarAdmin
        title="Détails Technicien"
        bottomBar
        onBackPress={() => router.back()}
        showBack
      />
      <ScrollView style={styles.container}>
        {technician && (
          <AdminTechnicianForm
            mode="edit"
            initialData={technician}
            onSubmit={handleSubmit}
          />
        )}
      </ScrollView>
    </View>
  );
};

const styles = {
  container: {
    flex: 1,
    padding: 16,
  },
};

export default AdminTechnicianDetailsPage;
