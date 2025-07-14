import React, { FC, useEffect, useState } from "react";
import {
  Modal,
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ScrollView,
} from "react-native";
import { FormProvider, useForm } from "react-hook-form";
import { Feather } from "@expo/vector-icons";
import { AppTextField } from "../inputs/AppTextField";
import { AppSelectInput } from "../inputs/AppSelectInput";
import { useApi } from "@/app/utils/useApi";
import { useSessionContext } from "@/app/context/useSessionContext";
import { AppButton } from "../buttons/AppButton";

interface CreateEquipmentModalProps {
  onSuccess?: (newEquipment: any) => void;
  trigger?: React.ReactElement;
}

export const CreateEquipmentModal: FC<CreateEquipmentModalProps> = ({
  onSuccess = () => {},
  trigger,
}) => {
  const [visible, setVisible] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [brands, setBrands] = useState([]);
  const [typeEquipments, setTypeEquipments] = useState<any>([]);
  const methods = useForm();
  const { handleSubmit } = methods;
  const api = useApi();
  const sessionCtx = useSessionContext();
  const sessionData = sessionCtx?.session;

  const showModal = () => setVisible(true);
  const hideModal = () => {
    setVisible(false);
    methods.reset();
  };

  useEffect(() => {
    (async () => {
      try {
        const resp = await api.get("/brand");
        setBrands(resp.data);
        const typeE = await api.get("/type-equipment");
        setTypeEquipments(typeE.data);
      } catch (e) {
        console.log(e);
      }
    })();
  }, []);

  const onSubmit = async (data: any) => {
    try {
      setIsLoading(true);
      const payload = {
        ...data,
        user_id: sessionData?.id,
      };

      const response = await api.post("/equipment", payload);

      // Appeler le callback avec le nouvel équipement
      onSuccess(response.data);

      hideModal();
    } catch (error) {
      console.error("Error creating equipment:", error);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <View>
      {trigger &&
        React.cloneElement(trigger, {
          onPress: () => showModal,
        })}

      <Modal
        animationType="slide"
        visible={visible}
        onRequestClose={hideModal}
        presentationStyle="pageSheet"
      >
        <View style={styles.modalContainer}>
          <View style={styles.modalHeader}>
            <TouchableOpacity onPress={hideModal} style={styles.closeButton}>
              <Feather name="x" size={24} color="#000" />
            </TouchableOpacity>
            <Text style={styles.title}>Créer un équipement</Text>
            <View style={{ width: 24 }} />
          </View>

          <ScrollView style={styles.modalContent}>
            <FormProvider {...methods}>
              <AppTextField
                nameField="name"
                label="Nom de l'équipement"
                placeholder="Ex: Ordinateur portable"
                rules={{ required: "Le nom est requis" }}
                style={styles.input}
              />

              <AppSelectInput
                nameField="brand_id"
                options={brands!.map((brand: any) => ({
                  label: brand.name,
                  value: brand.id,
                }))}
                rules={{ required: "Ce champ est requis" }}
              />
              <AppSelectInput
                nameField="type_equipment_id"
                options={typeEquipments!.map((type: any) => ({
                  label: type.name,
                  value: type.id,
                }))}
                rules={{ required: "Ce champ est requis" }}
              />
              <AppTextField
                nameField="model"
                label="Modèle"
                placeholder="Ex: Latitude 5520"
                style={styles.input}
              />

              <AppTextField
                nameField="reference"
                label="Numéro de série"
                placeholder="Ex: ABC123456"
                style={styles.input}
              />

              <AppTextField
                nameField="description"
                label="Description"
                placeholder="Description de l'équipement..."
                multiline
                numberOfLines={3}
                style={styles.input}
              />
            </FormProvider>
          </ScrollView>

          <View style={styles.buttonContainer}>
            <AppButton
              children={"Annuler"}
              type="secondary"
              onPress={hideModal}
              disabled={isLoading}
            />
            <AppButton
              children={"Créer"}
              type="primary"
              onPress={handleSubmit(onSubmit)}
              loading={isLoading}
              disabled={isLoading}
            />
          </View>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  fab: {
    backgroundColor: "#007AFF",
  },
  iconButton: {
    padding: 8,
    borderRadius: 20,
    backgroundColor: "#F0F8FF",
    alignItems: "center",
    justifyContent: "center",
  },
  modalContainer: {
    flex: 1,
    backgroundColor: "#fff",
  },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: "#e0e0e0",
  },
  closeButton: {
    padding: 8,
    borderRadius: 20,
    backgroundColor: "#F0F3F4",
  },
  title: {
    fontSize: 18,
    fontWeight: "600",
    color: "#344260",
  },
  modalContent: {
    flex: 1,
    padding: 16,
  },
  input: {
    marginBottom: 16,
  },
  buttonContainer: {
    flexDirection: "column",
    padding: 16,
    paddingTop: 8,
    gap: 12,
    borderTopWidth: 1,
    borderTopColor: "#e0e0e0",
  },
  cancelButton: {
    flex: 1,
  },
});
