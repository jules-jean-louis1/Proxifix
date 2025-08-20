import { useSessionContext } from "@/app/context/useSessionContext";
import { useApi } from "@/app/hooks/useApi";
import { useLocalSearchParams, useRouter } from "expo-router";
import { FC, useEffect, useState } from "react";
import { ScrollView, View } from "react-native";
import React from "react";
import { Text } from "react-native-paper";
import { AdminCustomerForm } from "@/app/components/admin/customer/AdminCustomerForm";
import { ToolBarAdmin } from "@/app/components/admin/navigation/ToolBarAdmin";

const AdminCustomerDetailsPage: FC = () => {
  const { id } = useLocalSearchParams();
  const router = useRouter();
  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [customer, setCustomer] = useState<any>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);

  useEffect(() => {
    if (!sessionData) return;
    (async () => {
      const customerId = id as string;
      if (!customerId) return;
      const response = await api.get(`/customer/${customerId}`);
      setCustomer(response.data);
      setIsLoading(false);
    })();
  }, [id]);

    const handleSubmit = async (data: any) => {
        try {
        const response = await api.patch(`/customer/${data.id}`, data);
        setCustomer(data); 
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
        title="Client Details"
        bottomBar
        onBackPress={() => router.back()}
        showBack
      />
      <ScrollView style={styles.container}>
        {customer && (
          <AdminCustomerForm
            mode="edit"
            initialData={customer}
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

export default AdminCustomerDetailsPage;
