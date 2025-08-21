import { useFocusEffect, useRouter } from "expo-router";
import { useApi } from "@/app/hooks/useApi";
import { FC, useEffect, useState, useCallback } from "react";
import { useSessionContext } from "@/app/context/useSessionContext";
import { ScrollView, StyleSheet, View } from "react-native";
import React from "react";
import { AdminCustomerCard } from "@/app/components/admin/customer/AdminCustomerCard";
import { FAB } from "react-native-paper";
import { AppTextField } from "@/app/components/inputs/AppTextField";
import { FormProvider, useForm } from "react-hook-form";
import { ToolBarAdmin } from "@/app/components/admin/navigation/ToolBarAdmin";

const AdminCustomersPage: FC = () => {
  const router = useRouter();
  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [customers, setCustomers] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [searchQuery, setSearchQuery] = useState<string>("");

  const methods = useForm({
    defaultValues: {
      search: "",
    },
  });

  const loadCustomers = useCallback(
    async (search: string = "") => {
      if (!sessionData?.company?.id) return;

      setIsLoading(true);
      try {
        const url = search
          ? `/customer?query=${encodeURIComponent(search)}`
          : `/customer?customer_company_id=${sessionData.company.id}`;
        const response = await api.get(url);
        setCustomers(response.data);
      } catch (error) {
        console.error("Erreur chargement clients:", error);
      } finally {
        setIsLoading(false);
      }
    },
    [sessionData?.company?.id, api]
  );

  const searchValue = methods.watch("search");

  // Remplacer le useEffect par surveillance de searchValue
  useEffect(() => {
    if (searchValue === "") {
      loadCustomers();
      return;
    }

    const timeoutId = setTimeout(() => {
      loadCustomers(searchValue);
    }, 500);

    return () => clearTimeout(timeoutId);
  }, [searchValue, loadCustomers]);

  useFocusEffect(
    useCallback(() => {
      loadCustomers(searchValue);
    }, [loadCustomers, searchValue])
  );

  return (
    <View style={styles.containerWrapper}>
      <ToolBarAdmin title="Clients" bottomBar />

      {/* Barre de recherche */}
      <View style={styles.searchContainer}>
        <FormProvider {...methods}>
          <AppTextField
            nameField="search"
            label="Rechercher un client..."
            placeholder="Nom, prénom ou email"
          />
        </FormProvider>
      </View>

      <ScrollView style={styles.scrollView}>
        {isLoading ? (
          <View style={styles.loadingContainer}>
            {/* Ajoutez votre composant de loading ici */}
          </View>
        ) : (
          customers.map((customer) => (
            <AdminCustomerCard
              key={customer.id}
              customer={customer}
              onPress={() => router.push(`/admin/customers/${customer.id}`)}
            />
          ))
        )}
      </ScrollView>

      <FAB
        icon="plus"
        style={styles.fab}
        label="Ajouter un client"
        onPress={() => router.push("/admin/customers/new")}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  containerWrapper: {
    flex: 1,
    backgroundColor: "#F9F9F9",
    paddingBottom: 40,
  },
  container: {
    flex: 1,
  },
  searchContainer: {
    padding: 16,
    backgroundColor: "#f5f5f5",
  },
  scrollView: {
    flex: 1,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    padding: 20,
  },
  fab: {
    margin: 16,
    marginBottom: 70,
    backgroundColor: "#01358D",
  },
});

export default AdminCustomersPage;
