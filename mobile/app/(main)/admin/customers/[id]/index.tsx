import { useSessionContext } from "@/app/context/useSessionContext";
import { useApi } from "@/app/utils/useApi";
import { useLocalSearchParams, useRouter } from "expo-router";
import { FC, useEffect, useState } from "react";
import { ScrollView, View } from "react-native";
import React from "react";
import { ToolBarCustomer } from "@/app/components/navigation/ToolBarCustomer";
import { Text } from "react-native-paper";
import { AdminCustomerForm } from "@/app/components/admin/customer/AdminCustomerForm";

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

  if (isLoading) {
    return (
      <View style={styles.container}>
        <Text>Loading...</Text>
      </View>
    );
  }

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer
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
            onSubmit={setCustomer}
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
