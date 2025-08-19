import { View, ScrollView } from "react-native";
import React from "react";
import { router } from "expo-router";
import { AdminTechnicianForm } from "@/app/components/admin/technician/AdminTechnicianForm";
import { useApi } from "@/app/utils/useApi";
import { ToolBarCustomer } from "@/app/components/navigation/ToolBarCustomer";

const AdminTechnicianNew = () => {
  const api = useApi();

  const handleSubmit = async (data: any) => {
    try {
      const response = await api.post('/user', data);
      console.log('Technicien créé:', response.data);
      router.back(); // Retour à la liste
    } catch (error) {
      console.error('Erreur lors de la création:', error);
    }
  };

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer
        title="Nouveau Technicien"
        bottomBar
        onBackPress={() => router.back()}
        showBack
      />
      <ScrollView style={styles.container}>
        <AdminTechnicianForm 
          mode="create"
          onSubmit={handleSubmit}
          onCancel={() => router.back()}
        />
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

export default AdminTechnicianNew;