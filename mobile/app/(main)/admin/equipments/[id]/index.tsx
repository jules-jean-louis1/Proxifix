import { useSessionContext } from "@/app/context/useSessionContext";
import { useApi } from "@/app/hooks/useApi";
import { useLocalSearchParams, useRouter } from "expo-router";
import { FC, useEffect, useState } from "react";
import { ScrollView, View } from "react-native";
import React from "react";
import { Text } from "react-native-paper";
import { AdminEquipmentForm } from "@/app/components/admin/equipment/AdminEquipmentForm";
import { ToolBarAdmin } from "@/app/components/admin/navigation/ToolBarAdmin";

const AdminEquipmentDetailsPage: FC = () => {
  const { id } = useLocalSearchParams();
  const router = useRouter();
  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [equipment, setEquipment] = useState<any>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);

  useEffect(() => {
    if (!sessionData) return;
    (async () => {
      const equipmentId = id as string;
      if (!equipmentId) return;
      try {
        const response = await api.get(`/equipment/${equipmentId}`);
        setEquipment(response.data);
      } catch (error) {
        console.error("Erreur lors du chargement de l'équipement:", error);
      } finally {
        setIsLoading(false);
      }
    })();
  }, [id]);

  const handleSubmit = async (data: any) => {
    try {
      const response = await api.patch(`/equipment/${data.id}`, data);
      setEquipment(data); 
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
        title="Détails Équipement"
        bottomBar
        onBackPress={() => router.back()}
        showBack
      />
      <ScrollView style={styles.container}>
        {equipment && (
          <AdminEquipmentForm
            mode="edit"
            initialData={equipment}
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

export default AdminEquipmentDetailsPage;
