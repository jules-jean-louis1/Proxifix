import { useRouter } from "expo-router";
import { useApi } from "@/app/utils/useApi";
import { FC, useEffect, useState, useCallback } from "react";
import { useSessionContext } from "@/app/context/useSessionContext";
import { ScrollView, StyleSheet, View } from "react-native";
import React from "react";
import { AdminEquipmentCard } from "@/app/components/admin/equipment/AdminEquipmentCard";
import { FAB } from "react-native-paper";
import { AppTextField } from "@/app/components/inputs/AppTextField";
import { FormProvider, useForm } from "react-hook-form";
import { ToolBarAdmin } from "@/app/components/admin/navigation/ToolBarAdmin";

const AdminEquipmentsIndex: FC = () => {
  const router = useRouter();
  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const [equipments, setEquipments] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState<boolean>(true);
  const [searchQuery, setSearchQuery] = useState<string>("");

  const methods = useForm({
    defaultValues: {
      search: "",
    },
  });

  const loadEquipments = useCallback(
    async (search: string = "") => {
      setIsLoading(true);
      try {
        const url = search
          ? `/equipment?name=${encodeURIComponent(search)}`
          : `/equipment`;
        const response = await api.get(url);
        setEquipments(response.data);
      } catch (error) {
        console.error("Erreur chargement équipements:", error);
      } finally {
        setIsLoading(false);
      }
    },
    [api]
  );

  const searchValue = methods.watch("search");

  // Surveillance de searchValue avec debounce
  useEffect(() => {
    if (searchValue === "") {
      loadEquipments();
      return;
    }

    const timeoutId = setTimeout(() => {
      loadEquipments(searchValue);
    }, 500);

    return () => clearTimeout(timeoutId);
  }, [searchValue, loadEquipments]);

  return (
    <View style={styles.container}>
      <ToolBarAdmin
        title="Équipements"
        bottomBar
      />

      {/* Barre de recherche */}
      <View style={styles.searchContainer}>
        <FormProvider {...methods}>
          <AppTextField
            nameField="search"
            label="Rechercher un équipement..."
            placeholder="Nom, référence ou modèle"
          />
        </FormProvider>
      </View>

      <ScrollView style={styles.scrollView}>
        {isLoading ? (
          <View style={styles.loadingContainer}>
            {/* Ajoutez votre composant de loading ici */}
          </View>
        ) : (
          equipments.map((equipment) => (
            <AdminEquipmentCard
              key={equipment.id}
              equipment={equipment}
              onPress={() => router.push(`/(main)/admin/equipments/${equipment.id}` as any)}
            />
          ))
        )}
      </ScrollView>

      <FAB
        icon="plus"
        style={styles.fab}
        label="Ajouter un équipement"
        onPress={() => router.push("/admin/equipments/new" as any)}
      />
    </View>
  );
};

const styles = StyleSheet.create({
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

export default AdminEquipmentsIndex;