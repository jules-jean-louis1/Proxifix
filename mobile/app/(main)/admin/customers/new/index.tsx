import { View, ScrollView } from "react-native";
import React from "react";
import { router } from "expo-router";
import { AdminCustomerForm } from "@/app/components/admin/customer/AdminCustomerForm";
import { useApi } from "@/app/hooks/useApi";
import { ToolBarAdmin } from "@/app/components/admin/navigation/ToolBarAdmin";

const AdminCustomerNew = () => {
  const api = useApi();

  const handleSubmit = async (data: any) => {
    try {
      const response = await api.post('/customer', data);
      console.log('Client créé:', response.data);
      router.back(); // Retour à la liste
    } catch (error) {
      console.error('Erreur lors de la création:', error);
    }
  };

  return (
    <View style={{ flex: 1 }}>
      <ToolBarAdmin
        title="Nouveau Client"
        bottomBar
        onBackPress={() => router.back()}
        showBack
      />
      <ScrollView style={styles.container}>
        <AdminCustomerForm 
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

export default AdminCustomerNew;
