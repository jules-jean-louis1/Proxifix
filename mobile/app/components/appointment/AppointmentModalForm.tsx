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
import { ActivityIndicator, MD2Colors } from "react-native-paper";
import { APPOINTMENT_STATUS } from "@/app/utils/intervention";

interface AppointmentModalFormProps {
  type: "create" | "update";
  id?: any;
  externalButton?: boolean;
  title?: string;
  onSuccess?: () => void;
}

export const AppointmentModalForm: FC<AppointmentModalFormProps> = ({
  type,
  id,
  externalButton = false,
  title,
  onSuccess = () => {},
}) => {
  const [modalVisible, setModalVisible] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [equipments, setEquipments] = useState([]);
  const [companies, setCompanies] = useState([]);
  const [freeSlots, setFreeSlots] = useState<any>();
  const [appointment, setAppointment] = useState<any>();
  const methods = useForm();
  const { handleSubmit } = methods;
  const sessionCtx = useSessionContext();
  const api = useApi();
  const sessionData = sessionCtx?.session;

  const companyId = methods.getValues().company_id;
  const date = methods.getValues().date;

  useEffect(() => {
    (async () => {
      if (!modalVisible) return;
      try {
        setIsLoading(true);
        const response = await api.get(`/equipment?user_id=${sessionData?.id}`);
        setEquipments(response.data);
        const companiesResponse = await api.get(`/company`);
        setCompanies(companiesResponse.data);
        if (id) {
          const resp = await api.get(`/appointment?id=${id}`);
          setAppointment(resp.data[0]);
        }
        setIsLoading(false);
      } catch (error) {
        console.error("Error fetching equipments or companies:", error);
      }
    })();
  }, [modalVisible]);

  useEffect(() => {
    (async () => {
      if (!companyId && !date) return;
      try {
        const formatDate = format(new Date(date), "yyyy-MM-dd");
        const response = await api.get(
          `/appointment/free-slots?company_id=${companyId}&date=${formatDate}&interval=60`
        );
        setFreeSlots?.(response.data);
      } catch (error) {
        console.error("Error fetching interventions:", error);
      }
    })();
  }, [companyId]);

  const onSubmit = async (data: any) => {
    try {
      if (type === "create") {
        // console.log("Creating appointment with data:", data);
        await api.post("/appointment", data);
      } else if (type === "update" && appointment.status === APPOINTMENT_STATUS.PENDING) {
        // console.log("Updating appointment with data:", data);
        await api.put(`/appointment/${appointment?.id}`, data);
      }
      setModalVisible(false);
      onSuccess();
      methods.reset();
    } catch (error) {
      console.error("Error saving appointment:", error);
    }
  };

  if (modalVisible && (isLoading || !equipments.length || !companies.length)) {
    return (
      <View style={{ padding: 20 }}>
        <ActivityIndicator animating={true} color={MD2Colors.red800} />
      </View>
    );
  }

  return (
    <View>
      {externalButton ? (
        <AppButton
          type="primary"
          children={title}
          onPress={() => {
            setModalVisible(true);
          }}
        />
      ) : (
        <TouchableOpacity
          onPress={() => {
            setModalVisible(true);
          }}
          style={{ padding: 10, backgroundColor: "#008062", borderRadius: 5 }}
        >
          <Text style={{ color: "#fff" }}>{title}</Text>
        </TouchableOpacity>
      )}

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
            <Text style={styles.title}>
              {type === "create"
                ? "Créer un rendez-vous"
                : "Modifier un rendez-vous"}
            </Text>
            <FormProvider {...methods}>
              <AppSelectInput
                nameField="company_id"
                label="Choisir une entreprise"
                placeholder="Sélectionner une entreprise"
                defaultValue={appointment?.company?.id}
                options={companies!.map((company: any) => ({
                  label: company.name,
                  value: company.id,
                }))}
                rules={{ required: "Ce champ est requis" }}
              />
              <AppDateInputField
                nameField="date"
                label="Date de l'intervention"
                defaultValue={
                  appointment?.date ? new Date(appointment.date) : new Date()
                }
                placeholder="Sélectionner une date"
                rules={{ required: "Ce champ est requis" }}
              />
              <AppTextField
                nameField="title"
                label="Titre"
                defaultValue={appointment?.title}
                placeholder="Entrez le titre de l'intervention"
                rules={{ required: "Le titre est requis" }}
              />
              <AppTextField
                nameField="description"
                label="Description"
                defaultValue={appointment?.description}
                placeholder="Entrez la description de l'intervention"
              />
              <AppSelectInput
                nameField="equipment_id"
                label="Choisir un équipement"
                placeholder="Sélectionner un équipement"
                defaultValue={appointment?.equipment?.id}
                options={equipments!.map((equipment: any) => ({
                  label: equipment.name,
                  value: equipment.id,
                }))}
                rules={{ required: "Ce champ est requis" }}
              />
              {appointment &&
                type === "update" &&
                appointment.status === "pending" && (
                  <AppButton
                    type="secondary"
                    children="Supprimer"
                    onPress={async () => {
                      await api.delete(`/appointment/${id}`);
                      setModalVisible(false);
                      methods.reset();
                    }}
                  />
                )}
              <AppButton
                type="primary"
                children="Enregistrer"
                onPress={handleSubmit(onSubmit)}
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
