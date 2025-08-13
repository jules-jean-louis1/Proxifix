import { View } from "react-native";
import React from "react";
import { router } from "expo-router";

const AdminCustomerNew = () => {
  const handleSubmit = async (data: any) => {
    try {
      // API call pour créer le client
      // await createCustomer(data);
      router.back(); // Retour à la liste
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <View style={{ flex: 1 }}>
      {/* <CustomerForm 
        mode="create"
        onSubmit={handleSubmit}
        onCancel={() => router.back()}
      /> */}
    </View>
  );
};

export default AdminCustomerNew;
