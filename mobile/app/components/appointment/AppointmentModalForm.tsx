import React, { FC, useEffect, useState } from "react";
import { Modal, View, Text, StyleSheet, TouchableOpacity } from "react-native";
import { AppButton } from "../buttons/AppButton";
import { Form, FormProvider, useForm } from "react-hook-form";
import { useSessionContext } from "@/app/context/useSessionContext";
import { useApi } from "@/app/utils/useApi";
import { AppSelectInput } from "../inputs/AppSelectInput";
import { Feather } from "@expo/vector-icons";
import { AppDateInput } from "../inputs/AppDateInput";
import { AppTextField } from "../inputs/AppTextField";
import { format } from "date-fns";
import { AppDateInputField } from "../inputs/AppDateInputField";

interface AppointmentModalFormProps {
  type: "create" | "update";
  intervention?: any;
  companies?: any[];
  equipments?: any[];
  setInterventions?: (intervention: any) => void;
}

export const AppointmentModalForm: FC<AppointmentModalFormProps> = ({
  type,
  intervention,
  setInterventions,
  equipments,
  companies,
}) => {
  const [modalVisible, setModalVisible] = useState<boolean>(false);
  const [selectedCompany, setSelectedCompany] = useState<any>(null);
  const methods = useForm();
  const { handleSubmit } = methods;
  const sessionCtx = useSessionContext();
  const api = useApi();
  const sessionData = sessionCtx?.session;

  const companyId = methods.getValues().company_id;
  const date = methods.getValues().date;
  console.log("equipements:", equipments);

  useEffect(() => {
    (async () => {
      if (!companyId && !date) return;
      try {
        const formatDate = format(new Date(date), "yyyy-MM-dd");
        const response = await api.get(
          `/appointment/free-slots?company_id=${companyId}&date=${formatDate}&interval=60`
        );
        setInterventions?.(response.data);
      } catch (error) {
        console.error("Error fetching interventions:", error);
      }
    })();
  }, [companyId]);

  return (
    <View>
      <AppButton
        type="primary"
        children="Ajouter un rendez-vous"
        onPress={() => {
          setModalVisible(true);
        }}
      />
      <Modal
        animationType="slide"
        visible={modalVisible}
        onRequestClose={() => {
          setModalVisible(!modalVisible);
        }}
      >
        <View style={styles.modalContainer}>
          <View style={styles.modalHeader}>
            <TouchableOpacity
              onPress={() => setModalVisible(false)}
              style={{
                padding: 10,
                borderRadius: 50,
                backgroundColor: "#F0F3F4",
              }}
            >
              <Feather name="x" size={24} color={"#000"} />
            </TouchableOpacity>
          </View>
          <View style={styles.modalContent}>
            <Text>Ajouter un rendez-vous</Text>
            <FormProvider {...methods}>
              <AppSelectInput
                nameField="company_id"
                label="Choisir une entreprise"
                placeholder="Sélectionner une entreprise"
                options={companies!.map((company) => ({
                  label: company.name,
                  value: company.id,
                }))}
                rules={{ required: "Ce champ est requis" }}
              />
              <AppDateInputField
                nameField="date"
                label="Date de l'intervention"
                defaultValue={
                  intervention?.date ? new Date(intervention.date) : new Date()
                }
                placeholder="Sélectionner une date"
                rules={{ required: "Ce champ est requis" }}
              />
              <AppTextField
                nameField="title"
                label="Titre"
                defaultValue={intervention?.title}
                placeholder="Entrez le titre de l'intervention"
                rules={{ required: "Le titre est requis" }}
              />
              <AppTextField
                nameField="description"
                label="Description"
                defaultValue={intervention?.description}
                placeholder="Entrez la description de l'intervention"
              />
              <AppSelectInput
                nameField="equipment_id"
                label="Choisir un équipement"
                placeholder="Sélectionner un équipement"
                options={equipments!.map((equipment) => ({
                  label: equipment.name,
                  value: equipment.id,
                }))}
                rules={{ required: "Ce champ est requis" }}
              />
              <AppButton
                type="secondary"
                children="Annuler"
                onPress={() => {
                  setModalVisible(false);
                  methods.reset();
                }}
              />
              <AppButton
                type="primary"
                children="Enregistrer"
                onPress={handleSubmit(async (data) => {
                  try {
                    if (type === "create") {
                      console.log("Creating appointment with data:", data);
                      await api.post("/appointment", data);
                    } else {
                      await api.put(`/appointment/${intervention?.id}`, data);
                    }
                    setModalVisible(false);
                  } catch (error) {
                    console.error("Error saving appointment:", error);
                  }
                })}
              />
            </FormProvider>
          </View>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  modalContainer: {
    flex: 1,
    backgroundColor: "#fff",
  },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    padding: 16,
  },
  modalContent: {
    flex: 1,
    padding: 16,
  },
  title: {
    textAlign: "center",
    fontSize: 24,
    marginBottom: 20,
    color: "#344260",
  },
});
