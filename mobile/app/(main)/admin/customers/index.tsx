import { useRouter } from "expo-router";
import { useApi } from "@/app/utils/useApi";
import { FC, useEffect, useState } from "react";
import { useSessionContext } from "@/app/context/useSessionContext";
import { ScrollView, StyleSheet, View } from "react-native";
import React from "react";
import { ToolBarCustomer } from "@/app/components/navigation/ToolBarCustomer";
import { AdminCustomerCard } from "@/app/components/admin/customer/AdminCustomerCard";

const AdminCustomersPage: FC = () => {
  const router = useRouter();
  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [customers, setCustomers] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState<boolean>(true);

  useEffect(() => {
    if (!sessionData) return;
    console.log("Session data:");
    (async () => {
      const response = await api.get(
        `/customer?customer_company_id=${sessionData?.company.id}`
      );
      console.log("Customers:", response.data);
      setCustomers(response.data);
    })();
  }, []);

  return (
    <View style={styles.container}>
      <ToolBarCustomer title="Clients" bottomBar onBackPress={() => router.back()} showBack />
        <ScrollView style={{ flex: 1 }}>
          {customers.map((customer) => (
            <AdminCustomerCard key={customer.id} customer={customer} onPress={() => router.push(`/admin/customers/${customer.id}`)} />
          ))}
        </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
});

export default AdminCustomersPage;
