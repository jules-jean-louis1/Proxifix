import { useEffect, useState } from "react";
import {
  FlatList,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from "react-native";
import { format } from "date-fns";
import { fr } from "date-fns/locale";
import { useApi } from "@/app/utils/useApi";
import { useSessionContext } from "@/app/context/useSessionContext";
import React from "react";
import { components } from "@/app/types/types";
import { router } from "expo-router";
import { EquipmentModalForm } from "@/app/components/equipment/EquipmentModalForm";
import { FAB } from "react-native-paper";
import { ToolBarCustomer } from "@/app/components/navigation/ToolBarCustomer";

const EquipmentsPage = () => {
  const [equipments, setEquipments] = useState<
    components["schemas"]["Equipment-equipment.read_equipment.details"][]
  >([]);
  const [typeEquipment, setTypeEquipment] = useState<any[]>([]);
  const [brands, setBrands] = useState<any[]>([]);
  const [os, setOs] = useState<any>([]);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;
  const api = useApi();

  useEffect(() => {
    (async () => {
      try {
        const response = await api.get(`/equipment?user_id=${sessionData?.id}`);
        setEquipments(response.data);
        const typeEquipmentResponse = await api.get("/type-equipment");
        setTypeEquipment(typeEquipmentResponse.data);
        const brandsResponse = await api.get("/brand");
        setBrands(brandsResponse.data);
        const osResponse = await api.get("/operating-system");
        setOs(osResponse.data);
      } catch (error) {
        console.error(
          "Error:",
          (error as any).response
            ? (error as any).response.data
            : (error as any).message
        );
        setError(
          (error as any).message || "Impossible to get the equipments of user."
        );
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  if (loading) {
    return (
      <View style={styles.container}>
        <Text style={styles.loadingText}>Loading...</Text>
      </View>
    );
  }

  if (error) {
    return (
      <View style={styles.container}>
        <Text style={styles.errorText}>{error}</Text>
      </View>
    );
  }
  if (equipments.length === 0) {
    return (
      <View style={styles.container}>
        <Text style={styles.loadingText}>Aucun équipement trouvé.</Text>
      </View>
    );
  }

  return (
    <View style={{ flex: 1 }}>
      <ToolBarCustomer
        title={"Mes équipements"}
        bottomBar
        showBack
        onBackPress={() => router.push("/customer")}
      />
      <ScrollView style={styles.container}>
        <FlatList
          data={equipments}
          keyExtractor={(item) => item.id!.toString()}
          renderItem={({ item }) => (
            <Pressable
              style={styles.equipmentItem}
              onPress={() => {
                router.push(`/customer/equipment/${item.id}`);
              }}
            >
              <Text style={styles.equipmentName}>{item.name}</Text>
              <Text style={styles.equipmentBrand}>{item.brand?.name}</Text>
              <Text style={styles.equipmentOS}>
                {item.operating_system?.name}
              </Text>
              <Text style={styles.equipmentType}>
                {item.type_equipment?.name}
              </Text>
              <Text style={styles.equipmentDate}>
                {format(new Date(item.created_at!), "dd MMMM yyyy à HH:mm", {
                  locale: fr,
                })}
              </Text>
            </Pressable>
          )}
        />
      </ScrollView>
      <View pointerEvents="box-none" style={styles.fabContainer}>
        <EquipmentModalForm
          type="create"
          brands={brands}
          typeEquipment={typeEquipment}
          os={os}
          setEquipments={setEquipments}
          button={
            <FAB icon="plus" style={styles.fab} label="Ajouter un équipement" />
          }
        />
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  fabContainer: {
    position: "absolute",
    right: 0,
    bottom: 60,
    width: "100%",
    alignItems: "flex-end",
    zIndex: 100,
  },
  fab: {
    margin: 16,
  },
  container: {
    flex: 1,
    padding: 16,
    backgroundColor: "#F0F3F4",
  },
  title: {
    textAlign: "center",
    fontSize: 24,
    fontWeight: "bold",
    marginBottom: 20,
    color: "#344260",
  },
  loadingText: {
    textAlign: "center",
    fontSize: 18,
    color: "#5B6880",
  },
  errorText: {
    color: "red",
    fontSize: 18,
    textAlign: "center",
  },
  equipmentItem: {
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: "#ccc",
    backgroundColor: "#fff",
    borderRadius: 8,
    marginBottom: 10,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 2,
    elevation: 2,
  },
  equipmentName: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#344260",
    marginBottom: 5,
  },
  equipmentBrand: {
    fontSize: 16,
    color: "#5B6880",
    marginBottom: 5,
  },
  equipmentOS: {
    fontSize: 16,
    color: "#5B6880",
    marginBottom: 5,
  },
  equipmentType: {
    fontSize: 16,
    color: "#5B6880",
    marginBottom: 5,
  },
  equipmentDate: {
    fontSize: 14,
    color: "#78849E",
  },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    padding: 16,
  },
  cancelButton: {
    color: "#FF3B30",
    fontSize: 16,
    fontWeight: "bold",
  },
  confirmButton: {
    color: "#007BFF",
    fontSize: 16,
    fontWeight: "bold",
  },
  modalContainer: {
    flex: 1,
    backgroundColor: "#fff",
  },
  modalContent: {
    flex: 1,
    padding: 16,
  },
});

export default EquipmentsPage;
