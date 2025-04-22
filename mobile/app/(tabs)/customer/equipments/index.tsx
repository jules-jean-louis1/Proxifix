import { useEffect, useState } from "react";
import {
  Alert,
  FlatList,
  Modal,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from "react-native";
import { format } from "date-fns";
import { fr } from "date-fns/locale";
import { useApi } from "@/app/utils/useApi";
import { useSessionContext } from "@/app/context/useSessionContext";
import React from "react";
import { components } from "@/app/types/types";
import { AppButton } from "@/app/components/buttons/AppButton";
import { FormProvider, useForm } from "react-hook-form";
import { AppTextField } from "@/app/components/inputs/AppTextField";
import { AppSelectInput } from "@/app/components/inputs/AppSelectInput";

const EquipmentsPage = () => {
  const [modalVisible, setModalVisible] = useState<boolean>(false);
  const [formDirty, setFormDirty] = useState<boolean>(false);
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
  const methods = useForm();
  const { handleSubmit } = methods;

  const onSubmit = async (data: any) => {
    const values = methods.getValues();
    console.log("values", values);

    try {
      const response = await api.post("/equipment/new", {
        ...data,
        user_id: sessionData?.id,
      });
      setEquipments((prev) => [...prev, response.data]);
      setModalVisible(false);
    } catch (error) {
      console.error(
        "Error:",
        (error as any).response
          ? (error as any).response.data
          : (error as any).message
      );
      setError((error as any).message || "Impossible to add the equipment.");
    }
  };

  const handleCloseModal = () => {
    if (formDirty) {
      Alert.alert(
        "Confirmer",
        "Vous avez des modifications non sauvegardées. Voulez-vous vraiment quitter ?",
        [
          { text: "Annuler", style: "cancel" },
          {
            text: "Quitter",
            style: "destructive",
            onPress: () => setModalVisible(false),
          },
        ]
      );
    } else {
      setModalVisible(false);
    }
  };

  useEffect(() => {
    (async () => {
      try {
        const response = await api.get(
          `/equipment/customer/${sessionData?.id}`
        );
        setEquipments(response.data);
        const typeEquipmentResponse = await api.get("/type_equipment/all");
        setTypeEquipment(typeEquipmentResponse.data);
        const brandsResponse = await api.get("/brand/all");
        setBrands(brandsResponse.data);
        const osResponse = await api.get("/operating_system/all");
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

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Tous mes équipements</Text>
      <AppButton
        type="primary"
        children="Ajouter un equipement"
        onPress={() => setModalVisible(true)}
      />
      {equipments.length === 0 && (
        <Text style={styles.loadingText}>Aucun équipement trouvé.</Text>
      )}
      <FlatList
        data={equipments}
        keyExtractor={(item) => item.id!.toString()}
        renderItem={({ item }) => (
          <View style={styles.equipmentItem}>
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
          </View>
        )}
      />
      <Modal
        animationType="slide"
        visible={modalVisible}
        onRequestClose={() => {
          setModalVisible(!modalVisible);
        }}
      >
        <View style={{ flex: 1, justifyContent: "center" }}>
          <View style={styles.modalContainer}>
            <View style={styles.modalHeader}>
              <TouchableOpacity onPress={handleCloseModal}>
                <Text style={styles.cancelButton}>Annuler</Text>
              </TouchableOpacity>
            </View>
            <View style={styles.modalContent}>
                <Text style={styles.title}>Ajouter un équipement</Text>
              <FormProvider {...methods}>
                <AppTextField
                  nameField="name"
                  label="Nom"
                  placeholder="Entrez le nom de l'équipement"
                  rules={{ required: "Le nom est obligatoire" }}
                />
                <AppSelectInput
                  nameField="type_equipment_id"
                  label="Type"
                  placeholder="Sélectionnez le type d'équipement"
                  options={typeEquipment.map((type) => ({
                    label: type.name,
                    value: type.id,
                  }))}
                  rules={{ required: "Le type est obligatoire" }}
                />
                <AppSelectInput
                  nameField="brand_id"
                  label="Marque"
                  placeholder="Sélectionnez la marque"
                  options={brands.map((brand) => ({
                    label: brand.name,
                    value: brand.id,
                  }))}
                  rules={{ required: "La marque est obligatoire" }}
                />
                <AppSelectInput
                  nameField="operating_system_id"
                  label="Système d'exploitation"
                  placeholder="Sélectionnez le système d'exploitation"
                  options={os.map((os:any) => ({
                    label: os.name,
                    value: os.id,
                  }))}
                />
                <AppButton
                  type="primary"
                  children="Ajouter"
                  onPress={handleSubmit((data) => {
                    onSubmit(data);
                  })}
                />
              </FormProvider>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
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
    borderBottomWidth: 1,
    borderBottomColor: "#ccc",
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
